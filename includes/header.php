<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? t('app_name');
$activeNav = $activeNav ?? '';
$user      = currentUser();
?>
<!DOCTYPE html>
<html lang="<?= currentLang() ?>" dir="<?= dirAttr() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · <?= e(t('app_name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= currentLang() === 'ar' ? 'lang-ar' : 'lang-en' ?>">

<?php if ($user): ?>
<header class="topbar">
    <div class="topbar-inner">
        <a href="dashboard.php" class="brand">
            <span class="brand-icon"><i class="fas fa-brain"></i></span>
            <span class="brand-text"><?= e(t('app_name')) ?></span>
        </a>

        <nav class="main-nav">
            <a href="dashboard.php" class="<?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> <?= e(t('nav_dashboard')) ?>
            </a>
            <a href="upload.php" class="<?= $activeNav === 'upload' ? 'active' : '' ?>">
                <i class="fas fa-cloud-upload-alt"></i> <?= e(t('nav_upload')) ?>
            </a>
            <a href="team.php" class="<?= $activeNav === 'team' ? 'active' : '' ?>">
                <i class="fas fa-user-md"></i> <?= e(t('nav_team')) ?>
            </a>
        </nav>

        <div class="top-actions">
            <a href="?lang=<?= currentLang() === 'ar' ? 'en' : 'ar' ?>" class="lang-btn">
                <i class="fas fa-globe"></i> <?= e(t('lang_switch')) ?>
            </a>
            <span class="user-chip"><i class="fas fa-user-doctor"></i> <?= e($user['name']) ?></span>
            <a href="logout.php" class="logout-btn" title="<?= e(t('nav_logout')) ?>">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </div>
</header>
<?php endif; ?>

<main class="page">
