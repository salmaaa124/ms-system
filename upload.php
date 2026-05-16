<?php
/**
 * Upload MRI Page
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_GET['lang'])) { setLang($_GET['lang']); header('Location: upload.php'); exit; }
requireLogin();

$user  = currentUser();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$ok, $pathOrErr, $origName] = handleUpload('mri');
    if (!$ok) {
        $error = t($pathOrErr);
    } else {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO images (user_id, image_path, original_name) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $pathOrErr, $origName]);
        $imageId = (int)$db->lastInsertId();

        // Run AI / simulated analysis
        $absolute = __DIR__ . '/' . $pathOrErr;
        $analysis = analyseMRI($absolute);

        $rStmt = $db->prepare('INSERT INTO results (image_id, result, confidence_score, notes) VALUES (?, ?, ?, ?)');
        $rStmt->execute([$imageId, $analysis['result'], $analysis['confidence'], $analysis['notes']]);

        header('Location: result.php?id=' . $imageId);
        exit;
    }
}

$pageTitle = t('nav_upload');
$activeNav = 'upload';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><?= e(t('upload_title')) ?></h1>
    <p><?= e(t('upload_subtitle')) ?></p>
</div>

<div class="card upload-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <?= e($error) ?></div>
    <?php endif; ?>

    <form id="uploadForm" method="POST" enctype="multipart/form-data">
        <div id="dropzone" class="dropzone">
            <div class="icon-big"><i class="fas fa-cloud-arrow-up"></i></div>
            <h3><?= e(t('select_file')) ?></h3>
            <p><?= e(t('supported_formats')) ?></p>
            <input type="file" id="fileInput" name="mri" accept="image/jpeg,image/png,image/webp" required>
        </div>

        <div class="preview-wrap" id="previewWrap">
            <img id="previewImg" src="" alt="Preview">
            <div class="file-name" id="fileName"></div>
        </div>

        <div class="upload-actions">
            <a href="dashboard.php" class="btn btn-ghost">
                <i class="fas fa-arrow-<?= currentLang() === 'ar' ? 'right' : 'left' ?>"></i>
                <?= e(t('back_dashboard')) ?>
            </a>
            <button type="submit" id="submitBtn" class="btn btn-primary"
                    data-analysing="<?= e(t('analysing')) ?>" disabled>
                <i class="fas fa-wand-magic-sparkles"></i>
                <span class="label"><?= e(t('analyse')) ?></span>
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
