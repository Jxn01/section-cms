# Deployment

Section CMS is plain PHP + MySQL, so deployment is "copy the files, point them at a database." This guide covers a generic shared host. For the WordPress-hosting case specifically, see [`wordpress-hosting.md`](wordpress-hosting.md).

## Requirements

- PHP **8.1+** with the `pdo_mysql` extension
- MySQL **5.7+** / MariaDB **10.3+**
- Apache with `mod_rewrite` (the project ships an `.htaccess`). On nginx, replicate the rewrite (everything except `/admin`, `/assets` and real files → `index.php`).

## 1. Get the files onto the server

Pick one:

- **Plain FTP/SFTP** — upload the whole project to your web root.
- **`deploy.sh`** — `cp deploy.conf.example deploy.conf`, fill it in, then `./deploy.sh`. It walks the tree and uploads over FTPS, skipping secrets, `.git`, `docs/`, `tools/`, etc.
- **Go deployer** — `cd tools/deployer && go build -o deployer . && ./deployer`. Interactive; reads/writes `deploy.conf` and can trigger the DB setup over HTTP.

> The web root should contain `index.php` at its top level. `.env` lives **one level above** the web root if your host allows it; otherwise keep it in the root — it is gitignored and denied by the rewrite rules, but above the web root is safest.

## 2. Configure the environment

Copy `.env.example` to `.env` and fill in at least the database block:

```ini
DB_HOST=localhost
DB_NAME=your_db
DB_USER=your_db_user
DB_PASS=your_db_password
SITE_URL=https://www.your-domain.com
```

`SITE_URL` is used for canonical URLs, the sitemap and Open Graph tags. If you omit it, the app derives the URL from the incoming request (handy for local dev, but set it explicitly in production).

## 3. Initialize the database

Open `https://your-domain/database/setup.php` **once** in a browser. It:

1. creates the schema (`001_schema.sql`),
2. inserts the demo seed (`002_seed.sql`),
3. creates the first admin user — password from `ADMIN_PASS` in `.env`, or a random one shown once on the page.

**Then delete the entire `/database/` folder from the server.** It is only needed for setup and should not be publicly reachable afterwards.

## 4. Lock it down

- Log in at `/admin/`, open **Password**, and change the admin password.
- In **Settings**, set the site name, language, contact details and (optionally) SMTP for contact-form notifications.
- Confirm HTTPS works — `.htaccess` redirects HTTP → HTTPS automatically.

## Updating an existing install

Re-upload the changed PHP/CSS/JS files. The schema is consolidated in `001_schema.sql`; if you add columns in a future version, apply them with a one-off SQL migration. Do **not** re-run `setup.php` on a live site unless you intend to re-seed (the `INSERT`s assume a fresh schema).

## nginx (alternative to .htaccess)

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ ^/(admin|assets)/ { try_files $uri $uri/ =404; }
```

Reproduce the security headers and gzip from `.htaccess` in your server config, and block direct access to `.env` and `/database/`.
