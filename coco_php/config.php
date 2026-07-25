<?php
/**
 * Database configuration
 * Update these four values to match your local MySQL / XAMPP / WAMP setup.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'coco_db');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Every page needs a session (login state + cart identity)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
