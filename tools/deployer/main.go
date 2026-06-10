package main

// ═══════════════════════════════════════════════════════════════════
// ParkolóABC Telepítő / Deployer v2.0
// ═══════════════════════════════════════════════════════════════════
// Feltölti a weboldal fájljait FTP-n keresztül bármely szerverre.
//
// Konfiguráció:
//   1. deploy.conf fájl (a program mellett) — ha létezik, onnan olvas
//   2. Ha nincs deploy.conf, interaktívan kéri be az adatokat
//
// A program automatikusan bejárja a mappát és feltölti az összes
// releváns fájlt (kihagyja: .git, docs, tools, a saját exe-jét stb.)
// ═══════════════════════════════════════════════════════════════════

import (
	"bufio"
	"crypto/tls"
	"fmt"
	"io"
	"net"
	"net/http"
	"net/textproto"
	"os"
	"path/filepath"
	"runtime"
	"strings"
	"time"
)

// ─── Konfiguráció ───

type Config struct {
	FTPHost    string
	FTPPort    string
	FTPUser    string
	FTPPass    string
	SiteURL    string
	RemoteBase string // pl. "/" vagy "/public_html/" — távoli gyökér
}

// Kihagyandó mappák (ezek nem kerülnek fel a szerverre)
var skipDirs = map[string]bool{
	".git":         true,
	".github":      true,
	"docs":         true,
	"tools":        true,
	"node_modules": true,
	".vscode":      true,
	"legacy":       true, // régi WordPress fájlok
	"uploads":      true, // felhasználó által feltöltött média (már a szerveren van)
}

// Kihagyandó fájlok
var skipFiles = map[string]bool{
	".env":                true, // titkos beállítások — soha ne töltsd fel!
	"deploy.sh":           true,
	"deploy.conf":         true,
	"deploy.conf.example": true,
	"OLVASS_EL.txt":       true,
	"README.md":           true,
	".gitignore":          true,
	"go.mod":              true,
	"go.sum":              true,
	"smtp_diag.php":       true, // ideiglenes diagnosztikai fájl
}

// ─── Konfiguráció betöltése / bekérése ───

func loadConfig(baseDir string) Config {
	cfg := Config{
		FTPPort:    "21",
		RemoteBase: "/",
	}

	confPath := filepath.Join(baseDir, "deploy.conf")
	if data, err := os.ReadFile(confPath); err == nil {
		fmt.Println("  📄 deploy.conf megtalálva, beállítások betöltése...")
		for _, line := range strings.Split(string(data), "\n") {
			line = strings.TrimSpace(line)
			if line == "" || strings.HasPrefix(line, "#") || strings.HasPrefix(line, ";") {
				continue
			}
			if idx := strings.Index(line, "="); idx > 0 {
				key := strings.TrimSpace(line[:idx])
				val := strings.TrimSpace(line[idx+1:])
				switch strings.ToLower(key) {
				case "ftp_host":
					cfg.FTPHost = val
				case "ftp_port":
					cfg.FTPPort = val
				case "ftp_user":
					cfg.FTPUser = val
				case "ftp_pass":
					cfg.FTPPass = val
				case "site_url":
					cfg.SiteURL = val
				case "remote_base":
					cfg.RemoteBase = val
				}
			}
		}
		fmt.Println("")
	}

	reader := bufio.NewReader(os.Stdin)

	// Ha bármely kötelező mező hiányzik, interaktívan bekérjük
	if cfg.FTPHost == "" {
		cfg.FTPHost = prompt(reader, "FTP szerver címe (pl. ftp.example.hu)", "")
	}
	if cfg.FTPPort == "" || cfg.FTPPort == "0" {
		cfg.FTPPort = prompt(reader, "FTP port", "21")
	}
	if cfg.FTPUser == "" {
		cfg.FTPUser = prompt(reader, "FTP felhasználónév", "")
	}
	if cfg.FTPPass == "" {
		cfg.FTPPass = prompt(reader, "FTP jelszó", "")
	}
	if cfg.SiteURL == "" {
		cfg.SiteURL = prompt(reader, "Weboldal URL (pl. https://www.example.hu)", "")
	}
	if cfg.RemoteBase == "" {
		cfg.RemoteBase = prompt(reader, "Távoli mappa (FTP gyökérhez képest)", "/")
	}

	// Felajánljuk a mentést ha nem volt config fájl
	if _, err := os.Stat(confPath); os.IsNotExist(err) {
		fmt.Print("  💾 Mentsem a beállításokat deploy.conf fájlba? (i/n): ")
		answer, _ := reader.ReadString('\n')
		answer = strings.TrimSpace(strings.ToLower(answer))
		if answer == "i" || answer == "y" || answer == "igen" {
			saveConfig(confPath, cfg)
			fmt.Println("  ✅ Mentve!")
		}
		fmt.Println("")
	}

	return cfg
}

func prompt(reader *bufio.Reader, label, defaultVal string) string {
	if defaultVal != "" {
		fmt.Printf("  %s [%s]: ", label, defaultVal)
	} else {
		fmt.Printf("  %s: ", label)
	}
	input, _ := reader.ReadString('\n')
	input = strings.TrimSpace(input)
	if input == "" {
		return defaultVal
	}
	return input
}

func saveConfig(path string, cfg Config) {
	lines := []string{
		"# ParkolóABC Telepítő — FTP beállítások",
		"# Ez a fájl automatikusan jött létre. Szerkeszthető kézzel is.",
		"#",
		"# FIGYELEM: Ez a fájl jelszót tartalmaz!",
		"# Ne ossza meg, ne töltse fel Git-be!",
		"",
		"ftp_host = " + cfg.FTPHost,
		"ftp_port = " + cfg.FTPPort,
		"ftp_user = " + cfg.FTPUser,
		"ftp_pass = " + cfg.FTPPass,
		"site_url = " + cfg.SiteURL,
		"",
		"# Távoli mappa (legtöbb hosting: / vagy /public_html/)",
		"remote_base = " + cfg.RemoteBase,
		"",
	}
	os.WriteFile(path, []byte(strings.Join(lines, "\n")), 0600)
}

// ─── Fájl felderítés (automatikus) ───

func discoverFiles(baseDir string) []struct{ local, remote string } {
	var files []struct{ local, remote string }
	exeName := executableName()

	filepath.Walk(baseDir, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return nil
		}

		rel, _ := filepath.Rel(baseDir, path)
		rel = filepath.ToSlash(rel)

		if rel == "." {
			return nil
		}

		// Kihagyandó mappák
		if info.IsDir() {
			base := filepath.Base(rel)
			if skipDirs[base] {
				return filepath.SkipDir
			}
			return nil
		}

		// Kihagyandó fájlok
		baseName := filepath.Base(rel)
		if skipFiles[baseName] {
			return nil
		}

		// Saját exe kihagyása
		if baseName == exeName {
			return nil
		}

		// DOCX és egyéb nem-webes fájlok kihagyása
		lower := strings.ToLower(baseName)
		for _, ext := range []string{".docx", ".xlsx", ".pptx", ".exe", ".zip", ".tar", ".gz"} {
			if strings.HasSuffix(lower, ext) {
				return nil
			}
		}

		files = append(files, struct{ local, remote string }{rel, rel})
		return nil
	})

	return files
}

func executableName() string {
	exe, err := os.Executable()
	if err != nil {
		if runtime.GOOS == "windows" {
			return "ParkoloABC_Telepito.exe"
		}
		return "deployer"
	}
	return filepath.Base(exe)
}

// ─── Egyszerű FTP kliens (FTPS támogatással) ───

type FTPClient struct {
	conn   net.Conn
	reader *textproto.Reader
	host   string
}

func ftpConnect(cfg Config) (*FTPClient, error) {
	addr := cfg.FTPHost + ":" + cfg.FTPPort
	fmt.Printf("  → Csatlakozás: %s ...\n", addr)

	conn, err := net.DialTimeout("tcp", addr, 15*time.Second)
	if err != nil {
		return nil, fmt.Errorf("nem sikerült csatlakozni: %s — %v", addr, err)
	}

	c := &FTPClient{
		conn:   conn,
		reader: textproto.NewReader(bufio.NewReader(conn)),
		host:   cfg.FTPHost,
	}

	// Üdvözlő üzenet
	if _, err := c.readResponse(220); err != nil {
		conn.Close()
		return nil, fmt.Errorf("a szerver nem válaszolt megfelelően: %v", err)
	}

	// AUTH TLS (titkosított kapcsolat, ha a szerver támogatja)
	if err := c.sendCmd("AUTH TLS"); err == nil {
		if _, err := c.readResponse(234); err == nil {
			tlsConn := tls.Client(conn, &tls.Config{
				InsecureSkipVerify: true,
				ServerName:        cfg.FTPHost,
			})
			if err := tlsConn.Handshake(); err == nil {
				c.conn = tlsConn
				c.reader = textproto.NewReader(bufio.NewReader(tlsConn))
				// Titkosítás bekapcsolása az adatcsatornán is
				c.sendCmd("PBSZ 0")
				c.readResponseAny()
				c.sendCmd("PROT P")
				c.readResponseAny()
			}
		}
	}

	// Bejelentkezés
	c.sendCmd("USER " + cfg.FTPUser)
	if _, err := c.readResponse(331); err != nil {
		conn.Close()
		return nil, fmt.Errorf("felhasználónév elutasítva: %v", err)
	}
	c.sendCmd("PASS " + cfg.FTPPass)
	if _, err := c.readResponse(230); err != nil {
		conn.Close()
		return nil, fmt.Errorf("jelszó elutasítva (hibás jelszó?): %v", err)
	}

	// Bináris átviteli mód
	c.sendCmd("TYPE I")
	c.readResponseAny()

	return c, nil
}

func (c *FTPClient) sendCmd(cmd string) error {
	_, err := fmt.Fprintf(c.conn, "%s\r\n", cmd)
	return err
}

func (c *FTPClient) readResponse(expected int) (string, error) {
	line, err := c.reader.ReadLine()
	if err != nil {
		return "", fmt.Errorf("kapcsolat megszakadt: %v", err)
	}
	if len(line) < 3 {
		return line, fmt.Errorf("váratlan válasz: %s", line)
	}
	// Többsoros válasz kezelése
	if len(line) > 3 && line[3] == '-' {
		prefix := line[:3]
		for {
			next, err := c.reader.ReadLine()
			if err != nil {
				break
			}
			if strings.HasPrefix(next, prefix+" ") {
				break
			}
		}
	}
	code := 0
	fmt.Sscanf(line[:3], "%d", &code)
	if expected > 0 && code != expected {
		return line, fmt.Errorf("FTP hiba (kód: %d): %s", code, line)
	}
	return line, nil
}

func (c *FTPClient) readResponseAny() string {
	line, _ := c.readResponse(0)
	return line
}

func (c *FTPClient) enterPassive() (string, error) {
	// Először EPSV (egyszerűbb, modernebb)
	c.sendCmd("EPSV")
	resp, err := c.readResponse(229)
	if err == nil {
		start := strings.Index(resp, "|||")
		end := strings.LastIndex(resp, "|")
		if start >= 0 && end > start+3 {
			port := resp[start+3 : end]
			return c.host + ":" + port, nil
		}
	}

	// Fallback: PASV
	c.sendCmd("PASV")
	resp, err = c.readResponse(227)
	if err != nil {
		return "", fmt.Errorf("passzív mód nem sikerült: %v", err)
	}
	start := strings.Index(resp, "(")
	end2 := strings.Index(resp, ")")
	if start < 0 || end2 < 0 {
		return "", fmt.Errorf("nem sikerült a passzív mód értelmezése")
	}
	parts := strings.Split(resp[start+1:end2], ",")
	if len(parts) != 6 {
		return "", fmt.Errorf("hibás PASV válasz: %s", resp)
	}
	p1, p2 := 0, 0
	fmt.Sscanf(parts[4], "%d", &p1)
	fmt.Sscanf(parts[5], "%d", &p2)
	port := p1*256 + p2
	host := strings.Join(parts[:4], ".")
	return fmt.Sprintf("%s:%d", host, port), nil
}

func (c *FTPClient) mkdirAll(dir string) {
	if dir == "" || dir == "." || dir == "/" {
		return
	}
	parts := strings.Split(strings.Trim(dir, "/"), "/")
	for i := range parts {
		path := strings.Join(parts[:i+1], "/")
		c.sendCmd("MKD " + path)
		c.readResponseAny()
	}
}

func (c *FTPClient) cwd(dir string) error {
	c.sendCmd("CWD " + dir)
	_, err := c.readResponse(250)
	return err
}

func (c *FTPClient) uploadFile(localPath, remotePath string) error {
	// Mappa létrehozása ha szükséges
	dir := filepath.ToSlash(filepath.Dir(remotePath))
	if dir != "." && dir != "" {
		c.mkdirAll(dir)
	}

	// Passzív mód
	dataAddr, err := c.enterPassive()
	if err != nil {
		return err
	}

	// Adat kapcsolat (TLS ha a vezérlő csatorna is TLS)
	var dataConn net.Conn
	if _, ok := c.conn.(*tls.Conn); ok {
		rawConn, err := net.DialTimeout("tcp", dataAddr, 10*time.Second)
		if err != nil {
			return fmt.Errorf("adat kapcsolat hiba: %v", err)
		}
		dataConn = tls.Client(rawConn, &tls.Config{
			InsecureSkipVerify: true,
			ServerName:        c.host,
		})
	} else {
		dataConn, err = net.DialTimeout("tcp", dataAddr, 10*time.Second)
		if err != nil {
			return fmt.Errorf("adat kapcsolat hiba: %v", err)
		}
	}

	// STOR parancs küldése
	remotePath = filepath.ToSlash(remotePath)
	c.sendCmd("STOR " + remotePath)
	if _, err := c.readResponse(150); err != nil {
		dataConn.Close()
		return err
	}

	// Fájl feltöltése
	f, err := os.Open(localPath)
	if err != nil {
		dataConn.Close()
		return fmt.Errorf("nem sikerült megnyitni: %s", localPath)
	}
	_, err = io.Copy(dataConn, f)
	f.Close()
	dataConn.Close()

	if err != nil {
		return fmt.Errorf("feltöltés hiba: %v", err)
	}

	// Átvitel befejezése
	if _, err := c.readResponse(226); err != nil {
		return err
	}
	return nil
}

func (c *FTPClient) quit() {
	c.sendCmd("QUIT")
	c.readResponseAny()
	c.conn.Close()
}

// ─── Segéd: emberi olvashatóságú fájlméret ───

func humanSize(b int64) string {
	switch {
	case b >= 1024*1024:
		return fmt.Sprintf("%.1f MB", float64(b)/(1024*1024))
	case b >= 1024:
		return fmt.Sprintf("%.1f KB", float64(b)/1024)
	default:
		return fmt.Sprintf("%d B", b)
	}
}

// ─── Fő program ───

func main() {
	fmt.Println("")
	fmt.Println("  ╔══════════════════════════════════════════════════╗")
	fmt.Println("  ║     Weboldal Telepítő — FTP Deployer  v2.0      ║")
	fmt.Println("  ╠══════════════════════════════════════════════════╣")
	fmt.Println("  ║  Feltölti a weboldal fájljait a szerverre FTP-n  ║")
	fmt.Println("  ║  keresztül. Ne zárja be az ablakot feltöltés     ║")
	fmt.Println("  ║  közben!                                         ║")
	fmt.Println("  ╚══════════════════════════════════════════════════╝")
	fmt.Println("")

	reader := bufio.NewReader(os.Stdin)

	// Aktuális mappa meghatározása
	baseDir, _ := os.Getwd()
	if exe, err := os.Executable(); err == nil {
		candidate := filepath.Dir(exe)
		// Ha az exe mappájában van index.php, azt használjuk
		if _, err := os.Stat(filepath.Join(candidate, "index.php")); err == nil {
			baseDir = candidate
		}
	}

	// Ellenőrzés: létezik-e az index.php?
	if _, err := os.Stat(filepath.Join(baseDir, "index.php")); os.IsNotExist(err) {
		fmt.Println("  ❌ HIBA: Nem találom az index.php fájlt!")
		fmt.Println("")
		fmt.Println("  A programnak a weboldal mappájában kell lennie,")
		fmt.Println("  ahol az index.php és a többi mappa van.")
		fmt.Println("")
		waitExit(reader)
		return
	}
	fmt.Printf("  📂 Projekt mappa: %s\n", baseDir)
	fmt.Println("")

	// Konfiguráció betöltése (fájlból vagy interaktívan)
	cfg := loadConfig(baseDir)

	// Fájlok automatikus felderítése
	fmt.Println("  🔍 Fájlok keresése...")
	files := discoverFiles(baseDir)
	fmt.Printf("  📦 %d fájl található a feltöltéshez\n", len(files))
	fmt.Println("")

	if len(files) == 0 {
		fmt.Println("  ❌ Nem találtam feltölthető fájlokat!")
		waitExit(reader)
		return
	}

	// Megerősítés
	fmt.Printf("  Szerver:  %s:%s\n", cfg.FTPHost, cfg.FTPPort)
	fmt.Printf("  Fiók:     %s\n", cfg.FTPUser)
	if cfg.RemoteBase != "/" && cfg.RemoteBase != "" {
		fmt.Printf("  Mappa:    %s\n", cfg.RemoteBase)
	}
	fmt.Printf("  Fájlok:   %d db\n", len(files))
	fmt.Println("")
	fmt.Print("  Indulhat a feltöltés? (i/n): ")
	answer, _ := reader.ReadString('\n')
	answer = strings.TrimSpace(strings.ToLower(answer))
	if answer == "n" || answer == "nem" || answer == "no" {
		fmt.Println("  Megszakítva.")
		waitExit(reader)
		return
	}
	fmt.Println("")

	// FTP csatlakozás
	fmt.Println("  🔗 Csatlakozás a szerverhez...")
	client, err := ftpConnect(cfg)
	if err != nil {
		fmt.Printf("  ❌ HIBA: %v\n", err)
		fmt.Println("")
		fmt.Println("  Lehetséges okok:")
		fmt.Println("    - Nincs internetkapcsolat")
		fmt.Println("    - Hibás FTP szerver cím / port")
		fmt.Println("    - Hibás felhasználónév vagy jelszó")
		fmt.Println("    - Tűzfal blokkolja az FTP portot")
		fmt.Println("")
		waitExit(reader)
		return
	}
	defer client.quit()
	fmt.Println("  ✅ Csatlakozva!")

	// Távoli gyökér beállítása (pl. /public_html/)
	if cfg.RemoteBase != "/" && cfg.RemoteBase != "" {
		client.mkdirAll(strings.Trim(cfg.RemoteBase, "/"))
		if err := client.cwd(cfg.RemoteBase); err != nil {
			fmt.Printf("  ⚠️  Nem sikerült a távoli mappába lépni: %s\n", cfg.RemoteBase)
		}
	}
	fmt.Println("")

	// Feltöltés
	fmt.Println("  📤 Fájlok feltöltése...")
	fmt.Println("  ───────────────────────────────────────────────────────────")
	successCount := 0
	failCount := 0
	var failedFiles []string
	var totalBytes int64
	startTime := time.Now()

	for i, f := range files {
		localPath := filepath.Join(baseDir, filepath.FromSlash(f.local))
		info, err := os.Stat(localPath)
		if err != nil {
			continue
		}

		progress := float64(i+1) / float64(len(files)) * 100
		size := humanSize(info.Size())
		fmt.Printf("  [%3.0f%%] %-45s %8s ", progress, f.remote, size)

		err = client.uploadFile(localPath, f.remote)
		if err != nil {
			fmt.Println("❌")
			failCount++
			failedFiles = append(failedFiles, fmt.Sprintf("%s (%v)", f.remote, err))
		} else {
			fmt.Println("✅")
			successCount++
			totalBytes += info.Size()
		}
	}

	elapsed := time.Since(startTime)
	fmt.Println("  ───────────────────────────────────────────────────────────")
	fmt.Println("")

	// Összegzés
	fmt.Println("  ╔══════════════════════════════════════════════════╗")
	if failCount == 0 {
		fmt.Println("  ║          ✅ FELTÖLTÉS SIKERES!                   ║")
	} else {
		fmt.Println("  ║      ⚠️  FELTÖLTÉS RÉSZBEN SIKERES                ║")
	}
	fmt.Println("  ╠══════════════════════════════════════════════════╣")
	fmt.Printf("  ║  Feltöltött: %-4d fájl                           ║\n", successCount)
	if failCount > 0 {
		fmt.Printf("  ║  Sikertelen: %-4d fájl                           ║\n", failCount)
	}
	fmt.Printf("  ║  Méret:      %-12s                       ║\n", humanSize(totalBytes))
	fmt.Printf("  ║  Idő:        %-12s                       ║\n", elapsed.Round(time.Second))
	fmt.Println("  ╚══════════════════════════════════════════════════╝")

	if failCount > 0 {
		fmt.Println("")
		fmt.Println("  Sikertelen fájlok:")
		for _, f := range failedFiles {
			fmt.Printf("    ✗ %s\n", f)
		}
	}

	// Weboldal URL
	if cfg.SiteURL != "" {
		fmt.Println("")
		siteBase := strings.TrimRight(cfg.SiteURL, "/")
		fmt.Printf("  🌐 Weboldal: %s\n", siteBase)
		fmt.Printf("  🔒 Admin:    %s/admin/\n", siteBase)
	}

	// Adatbázis telepítés (HTTP)
	if cfg.SiteURL != "" {
		siteBase := strings.TrimRight(cfg.SiteURL, "/")
		fmt.Println("")
		fmt.Println("  ─── Adatbázis beállítás ───")
		fmt.Println("")
		fmt.Print("  Futtassam az adatbázis telepítő szkripteket? (i/n): ")
		dbAnswer, _ := reader.ReadString('\n')
		dbAnswer = strings.TrimSpace(strings.ToLower(dbAnswer))
		if dbAnswer == "n" || dbAnswer == "nem" || dbAnswer == "no" {
			fmt.Println("  Kihagyva. Kézzel is futtathatja böngészőben:")
			fmt.Printf("    %s/database/setup.php\n", siteBase)
			fmt.Printf("    %s/database/migrate-v2.php\n", siteBase)
			fmt.Printf("    %s/database/migrate-v3.php\n", siteBase)
		} else {
			dbScripts := []struct{ name, path string }{
				{"Alap séma + seed", "/database/setup.php"},
				{"V2 migráció", "/database/migrate-v2.php"},
				{"V3 migráció", "/database/migrate-v3.php"},
			}
			for _, s := range dbScripts {
				url := siteBase + s.path
				fmt.Printf("  🗄️  %s ... ", s.name)
				body, err := httpGet(url)
				if err != nil {
					fmt.Printf("❌ %v\n", err)
				} else if strings.Contains(body, "❌") {
					fmt.Println("⚠️  (részben hibás, ellenőrizze böngészőben)")
				} else {
					fmt.Println("✅")
				}
			}
			fmt.Println("")
			fmt.Println("  ⚠️  BIZTONSÁG: Törölje a /database/ mappát a szerverről!")
		}
	} else {
		fmt.Println("")
		fmt.Println("  ⚠️  Adatbázis: nincs site_url → nem tudtam futtatni a szkripteket.")
		fmt.Println("  Nyissa meg böngészőben: <weboldal>/database/setup.php")
	}
	fmt.Println("")

	waitExit(reader)
}

func httpGet(url string) (string, error) {
	client := &http.Client{
		Timeout: 30 * time.Second,
		Transport: &http.Transport{
			TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
		},
	}
	resp, err := client.Get(url)
	if err != nil {
		return "", fmt.Errorf("HTTP hiba: %v", err)
	}
	defer resp.Body.Close()
	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return "", fmt.Errorf("olvasási hiba: %v", err)
	}
	if resp.StatusCode >= 400 {
		return string(body), fmt.Errorf("HTTP %d", resp.StatusCode)
	}
	return string(body), nil
}

func waitExit(reader *bufio.Reader) {
	fmt.Println("  Nyomjon ENTER-t a bezáráshoz...")
	reader.ReadBytes('\n')
}
