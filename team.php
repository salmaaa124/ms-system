<?php
/**
 * Team Page
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isset($_GET['lang'])) { setLang($_GET['lang']); header('Location: team.php'); exit; }
requireLogin();

/**
 * Team member definitions.
 * Edit these names/roles to match your real team for the graduation project.
 */
$supervisor = [
    'name_en' => 'Dr. Rasha Almarshdi',
    'name_ar' => 'د.رشا المرشدي',
    'role_en' => 'Project Supervisor',
    'role_ar' => 'المشرف على المشروع',
];
$members = [
    ['name_en' => 'Salma Alazmi',    'name_ar' => 'سلمى العازمي',     'role_en' => 'Full Stack Developer',       'role_ar' => '  مطور فل ستاك'],
    ['name_en' => 'Remas Alshammri',   'name_ar' => 'ريماس الشمري', 'role_en' => 'UI/UX Designer',      'role_ar' => ' مصممة الواجهة '],
    ['name_en' => 'Renad Aloufi ',     'name_ar' => 'ريناد العوفي ',     'role_en' => 'AI Engineer',        'role_ar' => 'مهندسة الذكاء الاصطناعي '],
    ['name_en' => 'Afrah Alharbi',   'name_ar' => 'أفراح العوفي ',    'role_en' => 'Frontend Developer',          'role_ar' => 'مطورة الواجهات '],
    ['name_en' => 'Wejdan Alharbi ',   'name_ar' => 'وجدان الحربي ',   'role_en' => 'Database & QA',             'role_ar' => 'قواعد البيانات والاختبار'],
    ['name_en' => 'Khalda Alshammari  ',   'name_ar' => ' خلدا الشمري  ',   'role_en' => 'QA Engineer', 'role_ar' => '  مهندسة اختبار الجودة'],

];

$lang = currentLang();
$pageTitle = t('team_title');
$activeNav = 'team';
include __DIR__ . '/includes/header.php';

function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);
    return mb_strtoupper($first . $last);
}
?>

<div class="team-hero">
    <h1><?= e(t('team_title')) ?></h1>
</div>

<!-- Supervisor -->
<div style="max-width:320px;margin:0 auto 28px;">
    <div class="team-card supervisor">
        <div class="team-avatar"><?= e(initials($supervisor['name_' . $lang])) ?></div>
        <div class="team-name"><?= e($supervisor['name_' . $lang]) ?></div>
        <div class="team-role"><i class="fas fa-star"></i> <?= e(t('supervisor')) ?></div>
    </div>
</div>

<!-- Members -->
<div class="team-grid">
    <?php foreach ($members as $m): ?>
        <div class="team-card">
            <div class="team-avatar"><?= e(initials($m['name_' . $lang])) ?></div>
            <div class="team-name"><?= e($m['name_' . $lang]) ?></div>
            <div class="team-role"><?= e($m['role_' . $lang]) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
