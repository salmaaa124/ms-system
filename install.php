<?php
/**
 * One-time installer.
 * Run this once (http://localhost/ms-detection-system/install.php)
 * after creating the database using schema.sql, OR let it create
 * the database for you automatically.
 */
require_once __DIR__ . '/config/database.php';

$messages = [];
$done     = false;

try {
    // 1. Connect without DB to create it if missing
    $pdoRoot = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdoRoot->exec('CREATE DATABASE IF NOT EXISTS ' . DB_NAME . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $messages[] = 'Database "' . DB_NAME . '" ready.';

    // 2. Run the schema
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    // Remove the CREATE DATABASE/USE lines (already handled)
    $sql = preg_replace('/^\s*(CREATE DATABASE|USE)[^;]*;/im', '', $sql);

    $pdo = getDB();
    $pdo->exec($sql);
    $messages[] = 'Tables created (users, images, results).';

    // 3. Seed / reset default doctor with a properly hashed password
    $hash = password_hash('doctor1234', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('
        INSERT INTO users (name, email, password)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE password = VALUES(password), name = VALUES(name)
    ');
    $stmt->execute(['Dr. Ahmed Hassan', 'doctor@ms-detect.com', $hash]);
    $messages[] = 'Default doctor account seeded.';

    // 4. Ensure uploads dir is writable
    $uploads = __DIR__ . '/uploads';
    if (!is_dir($uploads)) @mkdir($uploads, 0775, true);
    @chmod($uploads, 0775);
    $messages[] = 'Uploads directory ready.';

    $done = true;
} catch (Throwable $e) {
    $messages[] = 'ERROR: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Install ·MS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card" style="max-width:560px;">
        <div class="auth-logo">⚕️</div>
        <h1>NeuroScan MS · Installer</h1>
        <p class="subtitle">Run once to set up the database.</p>

        <?php foreach ($messages as $m): ?>
            <div class="alert <?= str_starts_with($m, 'ERROR') ? 'alert-error' : 'alert-info' ?>">
                <?= htmlspecialchars($m) ?>
            </div>
        <?php endforeach; ?>

        <?php if ($done): ?>
            <div class="demo-note">
                <b>Demo account</b><br>
                doctor@ms-detect.com / doctor123
            </div>
            <a href="login.php" class="btn btn-primary btn-block mt-3">Go to Login →</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
