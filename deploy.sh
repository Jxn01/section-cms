#!/bin/bash
# ─── Section CMS — FTP/FTPS deploy script ───
# Uploads the project files to a remote host over FTP(S) using curl.
#
# Configuration is read from ./deploy.conf (NOT committed — see
# deploy.conf.example). For a richer, interactive deployer with a
# nicer UI, build the Go tool in tools/deployer instead.
#
# Usage:
#   cp deploy.conf.example deploy.conf   # then fill in your details
#   ./deploy.sh

set -euo pipefail

PROJECT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONF="${PROJECT}/deploy.conf"

if [[ ! -f "$CONF" ]]; then
    echo "Error: deploy.conf not found. Copy deploy.conf.example to deploy.conf and fill it in."
    exit 1
fi

# ─── Load config (ftp_host, ftp_port, ftp_user, ftp_pass, site_url, remote_base) ───
ftp_port=21
remote_base=/
while IFS='=' read -r key value; do
    key="$(echo "$key" | xargs)"
    value="$(echo "${value:-}" | xargs)"
    [[ -z "$key" || "${key:0:1}" == "#" ]] && continue
    case "$key" in
        ftp_host)    ftp_host="$value" ;;
        ftp_port)    ftp_port="$value" ;;
        ftp_user)    ftp_user="$value" ;;
        ftp_pass)    ftp_pass="$value" ;;
        site_url)    site_url="$value" ;;
        remote_base) remote_base="$value" ;;
    esac
done < "$CONF"

: "${ftp_host:?ftp_host missing from deploy.conf}"
: "${ftp_user:?ftp_user missing from deploy.conf}"
: "${ftp_pass:?ftp_pass missing from deploy.conf}"
remote_base="${remote_base%/}/"

# ─── Paths that must never be uploaded ───
# (server-specific config, VCS data, local tooling and docs)
EXCLUDES=(
    "./.git/*" "./.git"
    "./.env" "./.env.example"
    "./deploy.conf" "./deploy.sh"
    "./tools/*" "./docs/*"
    "./.vscode/*" "./.github/*"
    "./legacy/*" "*/.DS_Store"
    "./README.md" "./LICENSE"
)

skip() {
    local f="$1"
    for pat in "${EXCLUDES[@]}"; do
        # shellcheck disable=SC2053
        [[ "$f" == $pat ]] && return 0
    done
    return 1
}

upload() {
    local local_file="$1"
    local remote_path="${remote_base}${2}"
    curl -fsS --ftp-ssl --ftp-create-dirs \
        --user "${ftp_user}:${ftp_pass}" \
        -T "$local_file" \
        "ftp://${ftp_host}:${ftp_port}/${remote_path}"
    echo "  ✓ ${2}"
}

echo "═══ Deploying Section CMS → ${ftp_host} ═══"
echo ""

cd "$PROJECT"
count=0
while IFS= read -r -d '' file; do
    rel="${file#./}"
    if skip "$file"; then continue; fi
    upload "$file" "$rel"
    count=$((count + 1))
done < <(find . -type f -print0)

echo ""
echo "═══ Deploy complete — ${count} files uploaded ═══"
echo ""
echo "Next steps:"
echo "  1. Create/upload your .env on the server (DB + SITE_URL + SMTP)."
echo "  2. Visit ${site_url:-https://your-domain}/database/setup.php once to set up the database."
echo "  3. DELETE the /database/ folder from the server afterwards."
echo "  4. Log in at ${site_url:-https://your-domain}/admin/ and change the admin password."
