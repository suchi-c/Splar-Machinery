-- =============================================================
-- Splar Machinery – Careers Module Database Setup
-- Run this once in phpMyAdmin against the `splar_machinery` DB
-- =============================================================

USE `splar_machinery`;

-- -----------------------------------------------------------
-- Table: jobs
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `jobs` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(255) NOT NULL,
  `type`        ENUM('Full-time','Part-time','Contract','Internship') DEFAULT 'Full-time',
  `location`    VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `status`      ENUM('Active','Closed') DEFAULT 'Active',
  `posted_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Table: applications
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `applications` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `job_id`      INT NOT NULL,
  `name`        VARCHAR(255) NOT NULL,
  `email`       VARCHAR(255) NOT NULL,
  `phone`       VARCHAR(50) DEFAULT NULL,
  `resume_path` VARCHAR(500) NOT NULL,
  `applied_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_applications_job`
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------
-- Sample seed job (optional – delete after testing)
-- -----------------------------------------------------------
INSERT IGNORE INTO `jobs` (`title`, `type`, `location`, `description`, `status`) VALUES
('Mechanical Design Engineer', 'Full-time', 'Pune, India',
 'We are seeking an experienced Mechanical Design Engineer to join our R&D team. The candidate will be responsible for designing and developing machinery components, conducting stress analysis, preparing technical drawings, and collaborating with the manufacturing team. Requirements: B.E./B.Tech in Mechanical Engineering, 3+ years experience, proficiency in SolidWorks or AutoCAD.',
 'Active'),
('Automation Technician', 'Full-time', 'Mumbai, India',
 'Looking for an Automation Technician to work on our PLC/SCADA-based production lines. Responsibilities include programming PLCs, troubleshooting automation equipment, and performing preventive maintenance. Requirements: Diploma/B.Tech in Electrical/Electronics, 2+ years in industrial automation.',
 'Active');
