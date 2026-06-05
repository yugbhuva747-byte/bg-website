-- ============================================================
--  setup.sql — Run this ONCE to create all tables
--  Usage: mysql -u root -p < setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS bg_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bg_website;

-- ── ADMIN USERS ──────────────────────────────────────────────
-- Stores admin login credentials
-- password_hash() is used in PHP so plain text is NEVER stored
CREATE TABLE IF NOT EXISTS admin_users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- bcrypt hash, never plain text
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=Admin@123
-- Generate hash via PHP: echo password_hash('Admin@123', PASSWORD_BCRYPT);
INSERT IGNORE INTO admin_users (username, password) VALUES
('admin', '$2y$10$8OAOpIOCuuI0VDMdFssHHuFTAmGIUR7qGWQz0NucRyLoOSdbeDnsC');

-- ── BLOG POSTS ────────────────────────────────────────────────
-- Full CRUD managed via Admin Panel
CREATE TABLE IF NOT EXISTS blog_posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    slug       VARCHAR(255) NOT NULL UNIQUE,   -- URL-friendly: "my-first-post"
    content    LONGTEXT NOT NULL,
    image_path VARCHAR(500) DEFAULT NULL,      -- relative path to uploaded image
    status     ENUM('draft','published') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ── SITE SETTINGS ─────────────────────────────────────────────
-- Key-value store for dynamic site config (no hardcoding!)
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key   VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default settings
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('site_title',       'Bhavana Goparaju'),
('tagline',          'Narrative & Systems Strategist | Filmmaker'),
('contact_email',    'hello@bhavanagoparaju.com'),
('contact_phone',    '+1 (555) 000-0000'),
('dark_mode_default','0'),
('meta_description', 'Bridging lived experience, collective memory, and future possibility.');

-- ── CONTACT INQUIRIES ────────────────────────────────────────
-- Stores messages sent via the Contact page form
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,   -- 0=unread, 1=read
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
