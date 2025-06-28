<?php
/*
 * config/production.php
 *
 * Production configuration for LCN Management System.
 * - Optimized for security and performance
 * - Error reporting disabled for production
 * - Session security enhanced
 * - Database connection optimized
 */

// Production environment flag
define('ENVIRONMENT', 'production');

// Error reporting - Disabled for production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Session security settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.cookie_lifetime', 3600); // 1 hour

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Database connection settings for production
define('DB_HOST', 'localhost');
define('DB_NAME', 'meghbela_lcn_db_kol'); // Update with your production DB name
define('DB_USER', 'your_production_user'); // Update with production credentials
define('DB_PASS', 'your_production_password'); // Update with production credentials
define('DB_CHARSET', 'utf8mb4');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['application/pdf']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Logging settings
define('LOG_LEVEL', 'ERROR'); // ERROR, WARNING, INFO, DEBUG
define('LOG_FILE', __DIR__ . '/../logs/app.log');

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_DURATION', 3600); // 1 hour

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 100); // requests per minute
define('RATE_LIMIT_WINDOW', 60); // seconds

// Backup settings
define('BACKUP_ENABLED', true);
define('BACKUP_RETENTION_DAYS', 30);
define('BACKUP_PATH', __DIR__ . '/../backups/'); 