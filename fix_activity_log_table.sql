-- Fix activity_log table structure
-- This script will create or modify the activity_log table to have the correct structure

-- Drop the table if it exists and recreate it with proper structure
DROP TABLE IF EXISTS `activity_log`;

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a sample log entry to test
INSERT INTO `activity_log` (`user_id`, `username`, `action`, `details`, `ip_address`, `created_at`) 
VALUES (1, 'admin', 'Table Fix', 'Activity log table structure fixed', '127.0.0.1', NOW()); 