<?php
/**
 * Database Configuration
 * Zimnat Policy Renewal Reminder System
 */

define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_NAME',    getenv('DB_NAME')    ?: 'zimnat_prs');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME',    'Zimnat PRS');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    '/zimnat_prs');          // Adjust if deployed to web root
define('UPLOAD_DIR',  __DIR__ . '/../uploads/documents/');
define('UPLOAD_URL',  BASE_URL . '/uploads/documents/');

define('MAX_FILE_SIZE',    5 * 1024 * 1024);   // 5 MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

define('RENEWAL_WARN_DAYS', 30);               // Days before renewal to flag as "Pending Renewal"
