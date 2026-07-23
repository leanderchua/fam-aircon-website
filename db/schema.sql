-- FAM Airconditioning Supply CMS schema
-- Run identically on local (XAMPP) and Hostinger MySQL — see plan doc for full context.

CREATE DATABASE IF NOT EXISTS fam_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fam_cms;

CREATE TABLE site_settings (
  id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  company_name VARCHAR(120) NOT NULL,
  logo_path VARCHAR(255),
  hero_eyebrow VARCHAR(120), hero_heading_line1 VARCHAR(120), hero_heading_line2 VARCHAR(120),
  hero_subtitle VARCHAR(255), hero_bg_image_path VARCHAR(255),
  hero_cta1_text VARCHAR(60), hero_cta1_href VARCHAR(120),
  hero_cta2_text VARCHAR(60), hero_cta2_href VARCHAR(120),
  about_eyebrow VARCHAR(120), about_heading VARCHAR(150),
  about_paragraph1 TEXT, about_paragraph2 TEXT, about_image_path VARCHAR(255),
  services_eyebrow VARCHAR(120), services_heading VARCHAR(150), services_intro TEXT,
  brands_eyebrow VARCHAR(120), brands_heading VARCHAR(150),
  projects_eyebrow VARCHAR(120), projects_heading VARCHAR(150),
  contact_eyebrow VARCHAR(120), contact_heading VARCHAR(150), contact_intro TEXT,
  contact_recipient_email VARCHAR(190) NOT NULL,
  footer_blurb TEXT, copyright_text VARCHAR(150),
  social_facebook_url VARCHAR(255), social_linkedin_url VARCHAR(255), social_email VARCHAR(190),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE stats (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  value_display VARCHAR(20) NOT NULL, count_target INT UNSIGNED NULL, suffix VARCHAR(10) NULL,
  label VARCHAR(80) NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE about_checklist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon_name VARCHAR(60) NOT NULL DEFAULT 'check_circle', label VARCHAR(120) NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon_name VARCHAR(60) NOT NULL, title VARCHAR(120) NOT NULL, description TEXT NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE brands (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL, logo_path VARCHAR(255) NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL, subtitle VARCHAR(200) NOT NULL,
  category ENUM('Commercial','Residential') NOT NULL DEFAULT 'Commercial',
  photo_path VARCHAR(255) NOT NULL, photo_alt VARCHAR(200) NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE contact_info_blocks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon_name VARCHAR(60) NOT NULL, label VARCHAR(80) NOT NULL, value_text TEXT NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE nav_links (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(60) NOT NULL, href VARCHAR(120) NOT NULL, sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL,
  failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0, locked_until DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contact_submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL, phone VARCHAR(40), email VARCHAR(190) NOT NULL,
  service_needed VARCHAR(120), project_details TEXT,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ip_address VARCHAR(45)
) ENGINE=InnoDB;
