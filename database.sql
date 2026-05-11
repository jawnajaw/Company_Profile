-- Database Structure for PTJSMP
-- Generated for MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS ptjsmp_db;
USE ptjsmp_db;

-- 1. Tabel User (Admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin', 'admin') DEFAULT 'admin',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Settings (SEO & Global Info)
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabel Hero Settings
CREATE TABLE IF NOT EXISTS `hero_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `headline` TEXT,
  `subheadline` TEXT,
  `badge_1` VARCHAR(50),
  `badge_2` VARCHAR(50),
  `cta_text` VARCHAR(50),
  `cta_link` VARCHAR(100),
  `image` VARCHAR(255),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabel About Section
CREATE TABLE IF NOT EXISTS `about_section` (
  `id` INT PRIMARY KEY,
  `title` VARCHAR(255),
  `description` TEXT,
  `stat1_value` VARCHAR(50),
  `stat1_label` VARCHAR(100),
  `stat2_value` VARCHAR(50),
  `stat2_label` VARCHAR(100)
) ENGINE=InnoDB;

-- 5. Tabel Galeri Foto About
CREATE TABLE IF NOT EXISTS `about_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `image_path` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB;

-- 6. Tabel Layanan (Services)
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 7. Tabel Keunggulan (Advantages)
CREATE TABLE IF NOT EXISTS `advantages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100),
  `description` TEXT,
  `icon` VARCHAR(50)
) ENGINE=InnoDB;

-- 8. Tabel Statistik (Counters)
CREATE TABLE IF NOT EXISTS `stats` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(100),
  `count_value` VARCHAR(20)
) ENGINE=InnoDB;

-- 9. Tabel Langkah Kerja (Work Steps)
CREATE TABLE IF NOT EXISTS `work_steps` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `step_number` INT,
  `title` VARCHAR(100),
  `description` TEXT
) ENGINE=InnoDB;

-- 10. Tabel Tim (Struktur Komando)
CREATE TABLE IF NOT EXISTS `team` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `rank` VARCHAR(100),
  `role` VARCHAR(100),
  `bio` TEXT,
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 11. Tabel Proyek / Case Study
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50),
  `image` VARCHAR(255),
  `incident_rate` VARCHAR(20),
  `patrol_status` VARCHAR(100),
  `problem` TEXT,
  `solution` TEXT,
  `result` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 12. Tabel Paket Layanan (Service Packages)
CREATE TABLE IF NOT EXISTS `service_packages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `subtitle` VARCHAR(255),
  `features` TEXT,
  `badge_label` VARCHAR(50),
  `cta_text` VARCHAR(50),
  `is_featured` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- 13. Tabel Opsi Simulasi Biaya
CREATE TABLE IF NOT EXISTS `simulation_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `location_name` VARCHAR(100) NOT NULL,
  `multiplier` DECIMAL(10,2) DEFAULT 1.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 14. Tabel Pesan Masuk (Contacts)
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `service` VARCHAR(100),
  `message` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 15. Tabel Calon Klien (Leads CRM)
CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `service_requested` VARCHAR(100),
  `message` TEXT,
  `status` ENUM('New', 'Contacted', 'Deal', 'Spam') DEFAULT 'New',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- DATA INITIALIZATION
-- --------------------------------------------------------

-- 1. Default Admin User (Password: admin123)
INSERT INTO `users` (`full_name`, `email`, `username`, `password`, `role`, `status`) 
VALUES ('Super Admin', 'admin@ptjsmp.com', 'admin', '$2y$10$T80S6Xm.X5eY9f1b7Q8YueO/A.P5D5t8n5L9f5B5r5v5x5z5v5B5y', 'super_admin', 'active');

-- 2. Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES 
('site_title', 'PT Jaka Satria Mandala Putra'),
('contact_email', 'info@ptjsmp.com'),
('contact_phone', '08123456789'),
('contact_address', 'Bogor, Jawa Barat, Indonesia'),
('contact_hours', 'Senin - Jumat: 08:00 - 17:00'),
('meta_description', 'Penyedia layanan keamanan profesional untuk korporat dan industri.'),
('meta_keywords', 'jasa satpam, security bogor, pengamanan industri');

-- 3. Default Hero Settings
INSERT INTO `hero_settings` (`headline`, `subheadline`, `badge_1`, `badge_2`, `cta_text`, `cta_link`, `image`) 
VALUES (
  'PROFESIONALISME & KEAMANAN TERJAMIN', 
  'Penyedia layanan keamanan profesional untuk korporat dan industri dengan standar sertifikasi internasional.', 
  '24/7 Monitoring', 
  'Sertifikat ISO', 
  'Pelajari Lebih Lanjut', 
  '#about', 
  'assets/img/hero-bg.jpg'
);

-- 4. Default About Section
INSERT INTO `about_section` (`id`, `title`, `description`, `stat1_value`, `stat1_label`, `stat2_value`, `stat2_label`)
VALUES (1, 'Berpengalaman Lebih dari 10 Tahun', 'PT Jaka Satria Mandala Putra hadir sebagai mitra strategis dalam pengelolaan risiko keamanan aset Anda.', '180+', 'Personel Aktif', '50+', 'Klien Korporat');
