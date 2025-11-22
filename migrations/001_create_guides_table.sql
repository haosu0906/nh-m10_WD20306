-- Migration: Create guides table
-- Run this in your MySQL database (e.g., via phpMyAdmin or mysql CLI)

CREATE TABLE IF NOT EXISTS `guides` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(191) DEFAULT NULL,
  `identity_no` VARCHAR(100) DEFAULT NULL,
  `certificate_no` VARCHAR(100) DEFAULT NULL,
  `guide_type` ENUM('domestic','international') NOT NULL DEFAULT 'domestic',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_full_name` (`full_name`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_email` (`email`),
  INDEX `idx_guide_type` (`guide_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
