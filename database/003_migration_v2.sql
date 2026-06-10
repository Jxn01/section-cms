-- =============================================
-- Parkoló ABC — v2 Migration
-- Adds: page_type, featured_image, contact_messages
-- =============================================

-- Add page_type to distinguish pages from articles
ALTER TABLE pages ADD COLUMN page_type ENUM('page', 'article') DEFAULT 'page' AFTER template;

-- Add featured_image for article cards / og fallback
ALTER TABLE pages ADD COLUMN featured_image VARCHAR(500) DEFAULT '' AFTER og_image;

-- Contact form messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_name VARCHAR(255) NOT NULL,
    sender_email VARCHAR(255) NOT NULL,
    sender_phone VARCHAR(100) DEFAULT '',
    message TEXT NOT NULL,
    page_slug VARCHAR(255) DEFAULT '',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
