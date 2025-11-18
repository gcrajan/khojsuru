<?php
// /generate_pdf.php (Updated)

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/vendor/autoload.php';

$cv_id = (int)($_GET['id'] ?? 0);
if ($cv_id === 0) { exit('Invalid CV ID.'); }

$stmt = $pdo->prepare("SELECT * FROM cvs WHERE id = ?");
$stmt->execute([$cv_id]);
$cv = $stmt->fetch();
if (!$cv) { exit('CV not found.'); }

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $cv['user_id'];
if (!$cv['is_public'] && !$is_owner) {
    exit('Access Denied: This CV is private.');
}

// --- UPDATED: Fetch ALL data components ---
$experiences = $pdo->prepare("SELECT * FROM cv_experience WHERE cv_id = ? ORDER BY id DESC");
$experiences->execute([$cv_id]);

$educations = $pdo->prepare("SELECT * FROM cv_education WHERE cv_id = ? ORDER BY id DESC");
$educations->execute([$cv_id]);

$projects = $pdo->prepare("SELECT * FROM cv_projects WHERE cv_id = ? ORDER BY id ASC");
$projects->execute([$cv_id]);

$certificates = $pdo->prepare("SELECT * FROM cv_certificates WHERE cv_id = ? ORDER BY id ASC");
$certificates->execute([$cv_id]);

$skills = $pdo->prepare("SELECT skill_name FROM cv_skills WHERE cv_id = ?");
$skills->execute([$cv_id]);
// --- END OF UPDATE ---

$template_name = basename($cv['template_name']);
$template_file = __DIR__ . '/templates/' . $template_name . '.php';

if (!file_exists($template_file)) {
    exit('Error: Template file could not be found.');
}

ob_start();
$is_pdf = true; // Flag for the template to use PDF-friendly styles
include $template_file;
$html_content = ob_get_clean();

try {
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_header' => 5,
        'margin_footer' => 5,
        'default_font_size' => 11,
        'default_font' => 'arial'
    ]);
    $mpdf->WriteHTML($html_content);
    $filename = 'CV-' . preg_replace('/[^A-Za-z0-9-]+/', '-', $cv['full_name']) . '.pdf';
    $mpdf->Output($filename, 'D'); // 'D' for download
} catch (\Mpdf\MpdfException $e) {
    echo 'PDF Generation Error: ' . $e->getMessage();
}
?>