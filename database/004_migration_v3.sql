-- =============================================
-- V3 Migration: New features
-- Adds featured flag to media (for hero slideshow)
-- =============================================

ALTER TABLE media ADD COLUMN is_featured TINYINT(1) DEFAULT 0;

INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES ('logo_url', '');
