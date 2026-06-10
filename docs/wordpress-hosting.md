# Running on WordPress hosting

Much of the cheap shared hosting on the market is sold as **"WordPress hosting"**: for a few dollars a month you get PHP, a MySQL/MariaDB database, FTP/SFTP access, and a web root pre-loaded with a WordPress installation. The hosting itself is just a generic LAMP stack — WordPress is only what happens to be installed in it.

Because Section CMS is **plain PHP + MySQL with no special requirements**, you can repurpose that same hosting you already pay for: clear out the WordPress install and run Section CMS in its place. For a small content/marketing site this avoids renting a separate VPS, which is typically far more expensive.

> ⚠️ **Authorization:** Only do this on hosting **you own or are explicitly authorized to administer.** This is about reusing your own paid hosting plan — not touching anyone else's server. Always take a full backup first.

## What you're actually doing

1. **Back up** the existing WordPress files and database (download the files, export the DB). Keep the backup somewhere safe.
2. **Clear the web root** of the WordPress files (or move them into a backup subfolder). You can reuse the **same MySQL database** that came with the plan — just drop the old `wp_*` tables, or create a fresh database if your plan allows more than one.
3. **Upload Section CMS** into the web root so that `index.php` sits at the top level (where `wp-load.php` used to be).
4. **Point `.env`** at the existing database credentials your host gave you (often visible in the hosting control panel / phpMyAdmin).
5. **Run `database/setup.php` once**, then delete the `database/` folder.

That's it — the domain that used to serve WordPress now serves Section CMS.

## Step by step

```bash
# locally: configure the deployer
cp deploy.conf.example deploy.conf
#   ftp_host    = the host from your hosting panel
#   ftp_user    = your FTP username
#   ftp_pass    = your FTP password
#   site_url    = https://your-domain
#   remote_base = /            (often / or /public_html/)

# push the files
./deploy.sh
#   — or — build the interactive Go tool:
#   cd tools/deployer && go build -o deployer . && ./deployer
```

On the server:

1. In the hosting DB tool (phpMyAdmin or similar), **drop the old WordPress tables** (everything prefixed `wp_`), or empty the database.
2. Create `.env` in the project root with the DB credentials and `SITE_URL`.
3. Visit `https://your-domain/database/setup.php`, note the admin password, then **delete `/database/`**.
4. Log in at `/admin/`, change the password, and set up your site in **Settings**.

## Things to watch for

- **PHP version** — make sure the panel is set to PHP 8.1+. Many WordPress plans default to an older PHP; switch it in the control panel.
- **`.htaccess`** — WordPress ships its own; Section CMS replaces it with its own rewrite + security headers. Make sure the upload overwrote it.
- **Email** — shared hosts often block the bare `mail()` function. Section CMS sends contact-form notifications via SMTP (PHPMailer); configure SMTP in **Settings** or `.env`.
- **`.env` location** — if the panel lets you put files above the web root, place `.env` there. Otherwise it stays in the root, where the rewrite rules and `.gitignore` keep it out of the way; it is never served because requests for real files are passed through, so confirm your host denies dotfiles (most do).
- **File permissions** — the `assets/uploads/` directory (created by the media library) must be writable by PHP.

## Reverting

Because you backed everything up first, reverting is just: clear the web root, re-upload your WordPress backup, and re-import the database export.
