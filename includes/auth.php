<?php
/**
 * Authentication helpers.
 * Include this file at the top of any page that requires login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

/** Is the current visitor logged in? */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/** Force-redirect guests to the login page. */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/** Return the logged-in user record, or null. */
function currentUser() {
    if (!isLoggedIn()) return null;
    $stmt = getDB()->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/** Attempt to log a user in. Returns [bool, message]. */
function attemptLogin($email, $password) {
    $stmt = getDB()->prepare('SELECT id, name, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        return [true, 'OK'];
    }
    return [false, 'invalid_credentials'];
}

/** Destroy the session and send the user home. */
function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
