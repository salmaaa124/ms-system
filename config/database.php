<?php
/**
 * Database Configuration
 * -----------------------
 * Edit these values to match your local MySQL setup (e.g. XAMPP).
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ms_detection_db');

/**
 * Returns a PDO connection.
 * Throws a clean error page if the DB is unreachable.
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;max-width:640px;margin:40px auto;border:1px solid #ddd;border-radius:8px;">
                <h2 style="color:#c0392b">Database Connection Failed</h2>
                <p>Please check <code>config/database.php</code> and make sure MySQL is running and the database <b>ms_detection_db</b> exists.</p>
                <p><a href="install.php">Run Installer</a></p>
                </div>');
        }
    }
    return $pdo;
}
