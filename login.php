<?php
/**
 * Login Page
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Language switcher
if (isset($_GET['lang'])) {
    setLang($_GET['lang']);
    header('Location: login.php');
    exit;
}

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    [$ok, $msg] = attemptLogin($email, $password);
    if ($ok) {
        header('Location: dashboard.php');
        exit;
    }
    $error = t('invalid_credentials');
}
?>
<!DOCTYPE html>
<html lang="<?= currentLang() ?>" dir="<?= dirAttr() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(t('login_title')) ?> · <?= e(t('app_name')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= currentLang() === 'ar' ? 'lang-ar' : 'lang-en' ?>">

<div class="auth-wrap">
    <div class="auth-card">
        <div style="display:flex;justify-content:flex-end;margin-bottom:-10px;">
            <a href="?lang=<?= currentLang() === 'ar' ? 'en' : 'ar' ?>" class="lang-btn">
                <i class="fas fa-globe"></i> <?= e(t('lang_switch')) ?>
            </a>
        </div>

        <div class="auth-logo"><i class="fas fa-brain"></i></div>
        <h1><?= e(t('login_title')) ?></h1>
        <p class="subtitle"><?= e(t('login_subtitle')) ?></p>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> <?= e(t('email')) ?></label>
                <input type="email" id="email" name="email" class="form-control" required
                       value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> <?= e(t('password')) ?></label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-right-to-bracket"></i> <?= e(t('sign_in')) ?>
            </button>
        </form>

        <div class="demo-note">
            <i class="fas fa-circle-info"></i> <?= e(t('demo_account')) ?>
        </div>
    </div>
</div>

</body>
</html>
