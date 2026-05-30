<?php echo extension_loaded('pdo_mysql') ? 'PDO OK' : 'PDO MISSING'; exit;
?>
<?php
/**
 * Entry point - redirect based on auth state.
 */
require_once __DIR__ . '/includes/auth.php';
header('Location: ' . (isLoggedIn() ? 'dashboard.php' : 'login.php'));
exit;