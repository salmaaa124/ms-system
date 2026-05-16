<?php
/**
 * Result Page - displays analysis outcome.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_GET['lang'])) {
    setLang($_GET['lang']);
    header('Location: result.php?id=' . (int)($_GET['id'] ?? 0));
    exit;
}
requireLogin();

$user    = currentUser();
$imageId = (int)($_GET['id'] ?? 0);

$stmt = getDB()->prepare('
    SELECT i.id, i.image_path, i.upload_date, i.original_name,
           r.result, r.confidence_score, r.notes
    FROM images i
    LEFT JOIN results r ON r.image_id = i.id
    WHERE i.id = ? AND i.user_id = ?
    LIMIT 1
');
$stmt->execute([$imageId, $user['id']]);
$scan = $stmt->fetch();

if (!$scan) {
    header('Location: dashboard.php');
    exit;
}

$isPositive = $scan['result'] === 'Positive';

$pageTitle = t('result_title');
$activeNav = '';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= e(t('result_title')) ?></h1>
    <p>#<?= (int)$scan['id'] ?> · <?= e(date('F d, Y - H:i', strtotime($scan['upload_date']))) ?></p>
</div>

<div class="result-grid">
    <div class="result-image">
        <img src="<?= e($scan['image_path']) ?>" alt="MRI Scan">
    </div>

    <div class="result-info">
        <div class="verdict-card <?= $isPositive ? 'pos' : 'neg' ?>">
            <i class="fas fa-<?= $isPositive ? 'triangle-exclamation' : 'circle-check' ?>"></i>
            <div class="label"><?= e(t('result')) ?></div>
            <div class="value"><?= $isPositive ? e(t('ms_positive')) : e(t('ms_negative')) ?></div>
        </div>

        <div class="confidence-card">
            <div class="label"><?= e(t('confidence_score')) ?></div>
            <div class="value"><?= number_format((float)$scan['confidence_score'], 2) ?>%</div>
            <div class="progress">
                <div class="progress-bar" data-value="<?= (float)$scan['confidence_score'] ?>" style="width:0"></div>
            </div>
        </div>

        <?php if (!empty($scan['notes'])): ?>
        <div class="notes-card">
            <h3><i class="fas fa-notes-medical"></i> <?= e(t('clinical_notes')) ?></h3>
            <p><?= e($scan['notes']) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="disclaimer">
    <i class="fas fa-shield-halved"></i> <?= e(t('disclaimer')) ?>
</div>

<div class="result-actions">
    <a href="upload.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> <?= e(t('scan_again')) ?>
    </a>
    <a href="dashboard.php" class="btn btn-ghost">
        <i class="fas fa-arrow-<?= currentLang() === 'ar' ? 'right' : 'left' ?>"></i>
        <?= e(t('back_dashboard')) ?>
    </a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
