<?php
/**
 * Dashboard - overview of the doctor's scan history.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_GET['lang'])) { setLang($_GET['lang']); header('Location: dashboard.php'); exit; }
requireLogin();

$user = currentUser();
$db   = getDB();

// Stats
$statStmt = $db->prepare('
    SELECT
        COUNT(r.id) AS total,
        SUM(CASE WHEN r.result = "Positive" THEN 1 ELSE 0 END) AS positive,
        SUM(CASE WHEN r.result = "Negative" THEN 1 ELSE 0 END) AS negative,
        AVG(r.confidence_score) AS avg_conf
    FROM results r
    JOIN images i ON r.image_id = i.id
    WHERE i.user_id = ?
');
$statStmt->execute([$user['id']]);
$stats = $statStmt->fetch() ?: ['total' => 0, 'positive' => 0, 'negative' => 0, 'avg_conf' => 0];

// Recent scans
$listStmt = $db->prepare('
    SELECT i.id, i.image_path, i.upload_date, r.result, r.confidence_score
    FROM images i
    LEFT JOIN results r ON r.image_id = i.id
    WHERE i.user_id = ?
    ORDER BY i.upload_date DESC
    LIMIT 10
');
$listStmt->execute([$user['id']]);
$scans = $listStmt->fetchAll();

$pageTitle = t('nav_dashboard');
$activeNav = 'dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= e(t('welcome')) ?>, <?= e($user['name']) ?> 👋</h1>
    <p><?= e(t('dashboard_intro')) ?></p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-medical"></i></div>
        <div>
            <div class="stat-label"><?= e(t('total_scans')) ?></div>
            <div class="stat-value"><?= (int)$stats['total'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-triangle-exclamation"></i></div>
        <div>
            <div class="stat-label"><?= e(t('positive_cases')) ?></div>
            <div class="stat-value"><?= (int)$stats['positive'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
        <div>
            <div class="stat-label"><?= e(t('negative_cases')) ?></div>
            <div class="stat-value"><?= (int)$stats['negative'] ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon teal"><i class="fas fa-percent"></i></div>
        <div>
            <div class="stat-label"><?= e(t('avg_confidence')) ?></div>
            <div class="stat-value"><?= $stats['avg_conf'] ? number_format($stats['avg_conf'], 1) . '%' : '—' ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="section-title">
        <h2><?= e(t('recent_scans')) ?></h2>
        <a href="upload.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?= e(t('new_analysis')) ?>
        </a>
    </div>

    <?php if (empty($scans)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p><?= e(t('no_scans')) ?></p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= e(t('date')) ?></th>
                        <th><?= e(t('result')) ?></th>
                        <th><?= e(t('confidence')) ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($scans as $s): ?>
                    <tr>
                        <td>#<?= (int)$s['id'] ?></td>
                        <td><?= e(date('M d, Y H:i', strtotime($s['upload_date']))) ?></td>
                        <td>
                            <?php if ($s['result'] === 'Positive'): ?>
                                <span class="badge badge-pos"><?= e(t('ms_positive')) ?></span>
                            <?php elseif ($s['result'] === 'Negative'): ?>
                                <span class="badge badge-neg"><?= e(t('ms_negative')) ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?= $s['confidence_score'] ? number_format($s['confidence_score'], 2) . '%' : '—' ?></td>
                        <td>
                            <a href="result.php?id=<?= (int)$s['id'] ?>" class="btn btn-ghost" style="padding:6px 12px;font-size:13px">
                                <i class="fas fa-eye"></i> <?= e(t('view')) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
