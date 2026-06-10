#!/bin/sh
# Section CMS — container entrypoint.
# Writes .env from the compose environment (if missing), waits for the
# database, runs the one-time setup, then hands off to the CMD (Apache).
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cat > .env <<EOF
DB_HOST=${DB_HOST:-db}
DB_NAME=${DB_NAME:-section_cms}
DB_USER=${DB_USER:-section_cms}
DB_PASS=${DB_PASS:-section_cms}
SITE_URL=${SITE_URL:-http://localhost:8080}
ADMIN_USER=${ADMIN_USER:-admin}
ADMIN_PASS=${ADMIN_PASS:-admin}
EOF
    echo "[entrypoint] wrote .env"
fi

echo "[entrypoint] waiting for database at ${DB_HOST:-db}..."
until php -r '
    $h=getenv("DB_HOST")?:"db"; $n=getenv("DB_NAME")?:"section_cms";
    $u=getenv("DB_USER")?:"section_cms"; $p=getenv("DB_PASS")?:"section_cms";
    try { new PDO("mysql:host=$h;dbname=$n", $u, $p); exit(0); }
    catch (Exception $e) { exit(1); }
' 2>/dev/null; do
    sleep 2
done

# Initialize the schema + demo seed + admin user once (idempotent enough
# for local dev: schema uses IF NOT EXISTS; seed may warn if already present).
if [ ! -f .docker-initialized ]; then
    echo "[entrypoint] initializing database..."
    php database/setup.php > /tmp/setup.log 2>&1 || true
    touch .docker-initialized
    echo "[entrypoint] done. Admin: ${ADMIN_USER:-admin} / ${ADMIN_PASS:-admin}"
fi

exec "$@"
