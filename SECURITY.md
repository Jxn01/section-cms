# Security

## Reporting a vulnerability

This is a portfolio/reference project. If you spot a security issue, please
open an issue (or contact the maintainer privately for anything sensitive)
rather than filing a public exploit.

## Built-in protections

- **SQL injection** — all queries use PDO prepared statements with
  `PDO::ATTR_EMULATE_PREPARES = false`.
- **XSS** — output is escaped (`htmlspecialchars` / the `$h` helper / `te()`).
  Rich-text content goes through a whitelist sanitizer (`core/sanitize.php`)
  that strips disallowed tags/attributes, `javascript:`/`data:` URLs and
  `on*` event handlers.
- **CSRF** — admin forms carry a per-session token verified on POST.
- **Sessions** — cookies are `HttpOnly` + `SameSite=Strict`; the session id is
  regenerated on login.
- **Headers** — `.htaccess` forces HTTPS and sets a strict
  Content-Security-Policy plus `X-Content-Type-Options`, `X-Frame-Options`,
  `Referrer-Policy` and `Permissions-Policy`.
- **Path traversal** — section type names are validated against
  `^[a-z0-9_]+$` before the template file is included.

## Operational checklist

- Keep secrets in `.env` / `deploy.conf` — both are gitignored. **Never commit
  them.**
- After install, **delete the `database/` folder** from the server and change
  the default admin password.
- Restrict the `assets/uploads/` directory to images; the media uploader
  validates MIME type and size.
- If a credential is ever committed by accident, **rotate it** — removing it
  from history is not enough, since old commits can persist in clones, forks
  and provider caches.
