#!/bin/bash
# ─── Deploy script for parkoloabc ───
# Uploads all project files to the FTP server.

set -e

PROJECT="/home/jxn/Projects/parkoloabc"
FTP_HOST="ftp.example.com"
FTP_USER="__REDACTED_DBUSER__"
FTP_PASS="__REDACTED_PASSWORD__"

upload() {
    local local_file="$1"
    local remote_path="$2"
    curl -s --ftp-ssl --ftp-create-dirs \
        --user "${FTP_USER}:${FTP_PASS}" \
        -T "$local_file" \
        "ftp://${FTP_HOST}/${remote_path}" 2>/dev/null
    echo "  ✓ $remote_path"
}

echo "═══ Deploying parkoloabc v2 ═══"
echo ""

# Root files
echo "→ Root files..."
upload "$PROJECT/index.php"   "index.php"
upload "$PROJECT/.htaccess"   ".htaccess"
upload "/tmp/server.env"      ".env"

# Config
echo "→ Config..."
upload "$PROJECT/config/db.php"        "config/db.php"
upload "$PROJECT/config/templates.php" "config/templates.php"

# Core
echo "→ Core..."
upload "$PROJECT/core/router.php"   "core/router.php"
upload "$PROJECT/core/renderer.php" "core/renderer.php"
upload "$PROJECT/core/seo.php"      "core/seo.php"
upload "$PROJECT/core/sanitize.php" "core/sanitize.php"

# Templates
echo "→ Templates..."
upload "$PROJECT/templates/base.php"             "templates/base.php"
upload "$PROJECT/templates/404.php"              "templates/404.php"
upload "$PROJECT/templates/keyword_results.php"  "templates/keyword_results.php"

# Section templates
echo "→ Section templates..."
for tpl in hero text image_text gallery cta cards ticker keywords_cloud accordion video divider two_columns testimonials stats page_list map contact_form hero_slideshow product_grid seo_hidden link_banner reference_gallery sitemap tudasmorzsak; do
    upload "$PROJECT/templates/sections/${tpl}.php" "templates/sections/${tpl}.php"
done

# Admin
echo "→ Admin..."
upload "$PROJECT/admin/index.php"    "admin/index.php"
upload "$PROJECT/admin/auth.php"     "admin/auth.php"
upload "$PROJECT/admin/pages.php"    "admin/pages.php"
upload "$PROJECT/admin/page-edit.php" "admin/page-edit.php"
upload "$PROJECT/admin/settings.php" "admin/settings.php"
upload "$PROJECT/admin/api.php"      "admin/api.php"
upload "$PROJECT/admin/media.php"    "admin/media.php"
upload "$PROJECT/admin/menus.php"    "admin/menus.php"
upload "$PROJECT/admin/messages.php" "admin/messages.php"
upload "$PROJECT/admin/password.php" "admin/password.php"
upload "$PROJECT/admin/components.php" "admin/components.php"
upload "$PROJECT/admin/includes/header.php" "admin/includes/header.php"
upload "$PROJECT/admin/includes/footer.php" "admin/includes/footer.php"
upload "$PROJECT/admin/includes/csrf.php"   "admin/includes/csrf.php"
upload "$PROJECT/admin/assets/admin.css"    "admin/assets/admin.css"
upload "$PROJECT/admin/assets/admin.js"     "admin/assets/admin.js"

# Assets
echo "→ Assets..."
upload "$PROJECT/assets/css/style.css" "assets/css/style.css"

# PHPMailer library
echo "→ PHPMailer..."
upload "$PROJECT/lib/PHPMailer/PHPMailer.php" "lib/PHPMailer/PHPMailer.php"
upload "$PROJECT/lib/PHPMailer/SMTP.php"      "lib/PHPMailer/SMTP.php"
upload "$PROJECT/lib/PHPMailer/Exception.php" "lib/PHPMailer/Exception.php"

# Database
echo "→ Database..."
upload "$PROJECT/database/001_schema.sql"       "database/001_schema.sql"
upload "$PROJECT/database/002_seed.sql"         "database/002_seed.sql"
upload "$PROJECT/database/003_migration_v2.sql" "database/003_migration_v2.sql"
upload "$PROJECT/database/setup.php"            "database/setup.php"
upload "$PROJECT/database/migrate-v2.php"       "database/migrate-v2.php"
upload "$PROJECT/database/004_migration_v3.sql" "database/004_migration_v3.sql"
upload "$PROJECT/database/migrate-v3.php"       "database/migrate-v3.php"

echo ""
echo "═══ Deploy complete! ═══"
echo ""
echo "Next steps:"
echo "  1. Run V3 migration: curl https://www.parkoloabc.hu/database/migrate-v3.php"
echo "  2. Visit https://www.parkoloabc.hu/admin/ to log in"
echo "  3. DELETE database/migrate-v3.php from the server after migration!"
