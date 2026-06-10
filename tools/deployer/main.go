package main

// ═══════════════════════════════════════════════════════════════════
// Section CMS Deployer v2.0
// ═══════════════════════════════════════════════════════════════════
// Uploads the website files to any server over FTP.
//
// Configuration:
//   1. deploy.conf file (next to the program) — read from here if it exists
//   2. If there is no deploy.conf, the data is requested interactively
//
// The program automatically walks the directory and uploads all
// relevant files (skipping: .git, docs, tools, its own executable, etc.)
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

// ─── Configuration ───

type Config struct {
	FTPHost    string
	FTPPort    string
	FTPUser    string
	FTPPass    string
	SiteURL    string
	RemoteBase string // e.g. "/" or "/public_html/" — remote root
}

// Directories to skip (these are not uploaded to the server)
var skipDirs = map[string]bool{
	".git":         true,
	".github":      true,
	"docs":         true,
	"tools":        true,
	"node_modules": true,
	".vscode":      true,
	"legacy":       true, // legacy files
	"uploads":      true, // user-uploaded media (already on the server)
}

// Files to skip
var skipFiles = map[string]bool{
	".env":                true, // secret settings — never upload!
	"deploy.sh":           true,
	"deploy.conf":         true,
	"deploy.conf.example": true,
	"README.md":           true,
	".gitignore":          true,
	"go.mod":              true,
	"go.sum":              true,
	"smtp_diag.php":       true, // temporary diagnostic file
}

// ─── Loading / requesting configuration ───

func loadConfig(baseDir string) Config {
	cfg := Config{
		FTPPort:    "21",
		RemoteBase: "/",
	}

	confPath := filepath.Join(baseDir, "deploy.conf")
	if data, err := os.ReadFile(confPath); err == nil {
		fmt.Println("  📄 deploy.conf found, loading settings...")
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

	// If any required field is missing, request it interactively
	if cfg.FTPHost == "" {
		cfg.FTPHost = prompt(reader, "FTP server address (e.g. ftp.example.com)", "")
	}
	if cfg.FTPPort == "" || cfg.FTPPort == "0" {
		cfg.FTPPort = prompt(reader, "FTP port", "21")
	}
	if cfg.FTPUser == "" {
		cfg.FTPUser = prompt(reader, "FTP username", "")
	}
	if cfg.FTPPass == "" {
		cfg.FTPPass = prompt(reader, "FTP password", "")
	}
	if cfg.SiteURL == "" {
		cfg.SiteURL = prompt(reader, "Website URL (e.g. https://www.example.com)", "")
	}
	if cfg.RemoteBase == "" {
		cfg.RemoteBase = prompt(reader, "Remote directory (relative to FTP root)", "/")
	}

	// Offer to save if there was no config file
	if _, err := os.Stat(confPath); os.IsNotExist(err) {
		fmt.Print("  💾 Save the settings to a deploy.conf file? (y/n): ")
		answer, _ := reader.ReadString('\n')
		answer = strings.TrimSpace(strings.ToLower(answer))
		if answer == "y" || answer == "yes" {
			saveConfig(confPath, cfg)
			fmt.Println("  ✅ Saved!")
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
		"# Section CMS Deployer — FTP settings",
		"# This file was generated automatically. It can also be edited by hand.",
		"#",
		"# WARNING: This file contains a password!",
		"# Do not share it, do not commit it to Git!",
		"",
		"ftp_host = " + cfg.FTPHost,
		"ftp_port = " + cfg.FTPPort,
		"ftp_user = " + cfg.FTPUser,
		"ftp_pass = " + cfg.FTPPass,
		"site_url = " + cfg.SiteURL,
		"",
		"# Remote directory (most hosting: / or /public_html/)",
		"remote_base = " + cfg.RemoteBase,
		"",
	}
	os.WriteFile(path, []byte(strings.Join(lines, "\n")), 0600)
}

// ─── File discovery (automatic) ───

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

		// Directories to skip
		if info.IsDir() {
			base := filepath.Base(rel)
			if skipDirs[base] {
				return filepath.SkipDir
			}
			return nil
		}

		// Files to skip
		baseName := filepath.Base(rel)
		if skipFiles[baseName] {
			return nil
		}

		// Skip our own executable
		if baseName == exeName {
			return nil
		}

		// Skip DOCX and other non-web files
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
			return "deployer.exe"
		}
		return "deployer"
	}
	return filepath.Base(exe)
}

// ─── Simple FTP client (with FTPS support) ───

type FTPClient struct {
	conn   net.Conn
	reader *textproto.Reader
	host   string
}

func ftpConnect(cfg Config) (*FTPClient, error) {
	addr := cfg.FTPHost + ":" + cfg.FTPPort
	fmt.Printf("  → Connecting: %s ...\n", addr)

	conn, err := net.DialTimeout("tcp", addr, 15*time.Second)
	if err != nil {
		return nil, fmt.Errorf("could not connect: %s — %v", addr, err)
	}

	c := &FTPClient{
		conn:   conn,
		reader: textproto.NewReader(bufio.NewReader(conn)),
		host:   cfg.FTPHost,
	}

	// Welcome message
	if _, err := c.readResponse(220); err != nil {
		conn.Close()
		return nil, fmt.Errorf("the server did not respond properly: %v", err)
	}

	// AUTH TLS (encrypted connection, if the server supports it)
	if err := c.sendCmd("AUTH TLS"); err == nil {
		if _, err := c.readResponse(234); err == nil {
			tlsConn := tls.Client(conn, &tls.Config{
				InsecureSkipVerify: true,
				ServerName:         cfg.FTPHost,
			})
			if err := tlsConn.Handshake(); err == nil {
				c.conn = tlsConn
				c.reader = textproto.NewReader(bufio.NewReader(tlsConn))
				// Enable encryption on the data channel too
				c.sendCmd("PBSZ 0")
				c.readResponseAny()
				c.sendCmd("PROT P")
				c.readResponseAny()
			}
		}
	}

	// Log in
	c.sendCmd("USER " + cfg.FTPUser)
	if _, err := c.readResponse(331); err != nil {
		conn.Close()
		return nil, fmt.Errorf("username rejected: %v", err)
	}
	c.sendCmd("PASS " + cfg.FTPPass)
	if _, err := c.readResponse(230); err != nil {
		conn.Close()
		return nil, fmt.Errorf("password rejected (wrong password?): %v", err)
	}

	// Binary transfer mode
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
		return "", fmt.Errorf("connection lost: %v", err)
	}
	if len(line) < 3 {
		return line, fmt.Errorf("unexpected response: %s", line)
	}
	// Handle multi-line response
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
		return line, fmt.Errorf("FTP error (code: %d): %s", code, line)
	}
	return line, nil
}

func (c *FTPClient) readResponseAny() string {
	line, _ := c.readResponse(0)
	return line
}

func (c *FTPClient) enterPassive() (string, error) {
	// EPSV first (simpler, more modern)
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
		return "", fmt.Errorf("passive mode failed: %v", err)
	}
	start := strings.Index(resp, "(")
	end2 := strings.Index(resp, ")")
	if start < 0 || end2 < 0 {
		return "", fmt.Errorf("could not parse passive mode response")
	}
	parts := strings.Split(resp[start+1:end2], ",")
	if len(parts) != 6 {
		return "", fmt.Errorf("invalid PASV response: %s", resp)
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
	// Create the directory if needed
	dir := filepath.ToSlash(filepath.Dir(remotePath))
	if dir != "." && dir != "" {
		c.mkdirAll(dir)
	}

	// Passive mode
	dataAddr, err := c.enterPassive()
	if err != nil {
		return err
	}

	// Data connection (TLS if the control channel is also TLS)
	var dataConn net.Conn
	if _, ok := c.conn.(*tls.Conn); ok {
		rawConn, err := net.DialTimeout("tcp", dataAddr, 10*time.Second)
		if err != nil {
			return fmt.Errorf("data connection error: %v", err)
		}
		dataConn = tls.Client(rawConn, &tls.Config{
			InsecureSkipVerify: true,
			ServerName:         c.host,
		})
	} else {
		dataConn, err = net.DialTimeout("tcp", dataAddr, 10*time.Second)
		if err != nil {
			return fmt.Errorf("data connection error: %v", err)
		}
	}

	// Send STOR command
	remotePath = filepath.ToSlash(remotePath)
	c.sendCmd("STOR " + remotePath)
	if _, err := c.readResponse(150); err != nil {
		dataConn.Close()
		return err
	}

	// Upload the file
	f, err := os.Open(localPath)
	if err != nil {
		dataConn.Close()
		return fmt.Errorf("could not open: %s", localPath)
	}
	_, err = io.Copy(dataConn, f)
	f.Close()
	dataConn.Close()

	if err != nil {
		return fmt.Errorf("upload error: %v", err)
	}

	// Finish transfer
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

// ─── Helper: human-readable file size ───

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

// ─── Main program ───

func main() {
	fmt.Println("")
	fmt.Println("  ╔══════════════════════════════════════════════════╗")
	fmt.Println("  ║       Section CMS — FTP Deployer  v2.0           ║")
	fmt.Println("  ╠══════════════════════════════════════════════════╣")
	fmt.Println("  ║  Uploads the website files to the server over    ║")
	fmt.Println("  ║  FTP. Do not close the window during the         ║")
	fmt.Println("  ║  upload!                                         ║")
	fmt.Println("  ╚══════════════════════════════════════════════════╝")
	fmt.Println("")

	reader := bufio.NewReader(os.Stdin)

	// Determine the current directory
	baseDir, _ := os.Getwd()
	if exe, err := os.Executable(); err == nil {
		candidate := filepath.Dir(exe)
		// If there is an index.php in the executable's directory, use that
		if _, err := os.Stat(filepath.Join(candidate, "index.php")); err == nil {
			baseDir = candidate
		}
	}

	// Check: does index.php exist?
	if _, err := os.Stat(filepath.Join(baseDir, "index.php")); os.IsNotExist(err) {
		fmt.Println("  ❌ ERROR: Cannot find the index.php file!")
		fmt.Println("")
		fmt.Println("  The program must be located in the website directory,")
		fmt.Println("  where index.php and the other directories are.")
		fmt.Println("")
		waitExit(reader)
		return
	}
	fmt.Printf("  📂 Project directory: %s\n", baseDir)
	fmt.Println("")

	// Load configuration (from file or interactively)
	cfg := loadConfig(baseDir)

	// Automatic file discovery
	fmt.Println("  🔍 Searching for files...")
	files := discoverFiles(baseDir)
	fmt.Printf("  📦 %d files found for upload\n", len(files))
	fmt.Println("")

	if len(files) == 0 {
		fmt.Println("  ❌ No uploadable files found!")
		waitExit(reader)
		return
	}

	// Confirmation
	fmt.Printf("  Server:   %s:%s\n", cfg.FTPHost, cfg.FTPPort)
	fmt.Printf("  Account:  %s\n", cfg.FTPUser)
	if cfg.RemoteBase != "/" && cfg.RemoteBase != "" {
		fmt.Printf("  Folder:   %s\n", cfg.RemoteBase)
	}
	fmt.Printf("  Files:    %d\n", len(files))
	fmt.Println("")
	fmt.Print("  Start the upload? (y/n): ")
	answer, _ := reader.ReadString('\n')
	answer = strings.TrimSpace(strings.ToLower(answer))
	if answer == "n" || answer == "no" {
		fmt.Println("  Cancelled.")
		waitExit(reader)
		return
	}
	fmt.Println("")

	// FTP connection
	fmt.Println("  🔗 Connecting to the server...")
	client, err := ftpConnect(cfg)
	if err != nil {
		fmt.Printf("  ❌ ERROR: %v\n", err)
		fmt.Println("")
		fmt.Println("  Possible causes:")
		fmt.Println("    - No internet connection")
		fmt.Println("    - Wrong FTP server address / port")
		fmt.Println("    - Wrong username or password")
		fmt.Println("    - A firewall is blocking the FTP port")
		fmt.Println("")
		waitExit(reader)
		return
	}
	defer client.quit()
	fmt.Println("  ✅ Connected!")

	// Set the remote root (e.g. /public_html/)
	if cfg.RemoteBase != "/" && cfg.RemoteBase != "" {
		client.mkdirAll(strings.Trim(cfg.RemoteBase, "/"))
		if err := client.cwd(cfg.RemoteBase); err != nil {
			fmt.Printf("  ⚠️  Could not enter the remote directory: %s\n", cfg.RemoteBase)
		}
	}
	fmt.Println("")

	// Upload
	fmt.Println("  📤 Uploading files...")
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

	// Summary
	fmt.Println("  ╔══════════════════════════════════════════════════╗")
	if failCount == 0 {
		fmt.Println("  ║          ✅ UPLOAD SUCCESSFUL!                   ║")
	} else {
		fmt.Println("  ║      ⚠️  UPLOAD PARTIALLY SUCCESSFUL              ║")
	}
	fmt.Println("  ╠══════════════════════════════════════════════════╣")
	fmt.Printf("  ║  Uploaded:   %-4d files                          ║\n", successCount)
	if failCount > 0 {
		fmt.Printf("  ║  Failed:     %-4d files                          ║\n", failCount)
	}
	fmt.Printf("  ║  Size:       %-12s                       ║\n", humanSize(totalBytes))
	fmt.Printf("  ║  Time:       %-12s                       ║\n", elapsed.Round(time.Second))
	fmt.Println("  ╚══════════════════════════════════════════════════╝")

	if failCount > 0 {
		fmt.Println("")
		fmt.Println("  Failed files:")
		for _, f := range failedFiles {
			fmt.Printf("    ✗ %s\n", f)
		}
	}

	// Website URL
	if cfg.SiteURL != "" {
		fmt.Println("")
		siteBase := strings.TrimRight(cfg.SiteURL, "/")
		fmt.Printf("  🌐 Website: %s\n", siteBase)
		fmt.Printf("  🔒 Admin:   %s/admin/\n", siteBase)
	}

	// Database setup (HTTP)
	if cfg.SiteURL != "" {
		siteBase := strings.TrimRight(cfg.SiteURL, "/")
		fmt.Println("")
		fmt.Println("  ─── Database setup ───")
		fmt.Println("")
		fmt.Print("  Run the database setup scripts? (y/n): ")
		dbAnswer, _ := reader.ReadString('\n')
		dbAnswer = strings.TrimSpace(strings.ToLower(dbAnswer))
		if dbAnswer == "n" || dbAnswer == "no" {
			fmt.Println("  Skipped. You can also run them manually in a browser:")
			fmt.Printf("    %s/database/setup.php\n", siteBase)
			fmt.Printf("    %s/database/migrate-v2.php\n", siteBase)
			fmt.Printf("    %s/database/migrate-v3.php\n", siteBase)
		} else {
			dbScripts := []struct{ name, path string }{
				{"Base schema + seed", "/database/setup.php"},
				{"V2 migration", "/database/migrate-v2.php"},
				{"V3 migration", "/database/migrate-v3.php"},
			}
			for _, s := range dbScripts {
				url := siteBase + s.path
				fmt.Printf("  🗄️  %s ... ", s.name)
				body, err := httpGet(url)
				if err != nil {
					fmt.Printf("❌ %v\n", err)
				} else if strings.Contains(body, "❌") {
					fmt.Println("⚠️  (partially failed, check in a browser)")
				} else {
					fmt.Println("✅")
				}
			}
			fmt.Println("")
			fmt.Println("  ⚠️  SECURITY: Delete the /database/ directory from the server!")
		}
	} else {
		fmt.Println("")
		fmt.Println("  ⚠️  Database: no site_url → could not run the scripts.")
		fmt.Println("  Open in a browser: <website>/database/setup.php")
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
		return "", fmt.Errorf("HTTP error: %v", err)
	}
	defer resp.Body.Close()
	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return "", fmt.Errorf("read error: %v", err)
	}
	if resp.StatusCode >= 400 {
		return string(body), fmt.Errorf("HTTP %d", resp.StatusCode)
	}
	return string(body), nil
}

func waitExit(reader *bufio.Reader) {
	fmt.Println("  Press ENTER to close...")
	reader.ReadBytes('\n')
}
