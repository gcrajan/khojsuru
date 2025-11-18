<?php
// /create_ai.php
$page_title = "Create CV With AI: Khojsuru";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

// Role-Based Access Control
if ($_SESSION['user_type'] !== 'recruitee') {
    header('Location: ' . BASE_URL);
    exit();
}

// Fetch current user's details to pre-fill the form
$user_stmt = $pdo->prepare("SELECT name, email, phone, location FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch();
?>

<style>
    .ai-create-container { display: flex; gap: 2rem; max-width: 1600px; margin: 2rem auto; padding: 0 1rem; }
    .ai-create-card { background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2.5rem; flex:1; }
    .ai-create-header { text-align: center; margin-bottom: 2rem; }
    .ai-create-header h1 { font-size: 2.25rem; font-weight: 700; }
    .ai-create-header p { color: #94a3b8; font-size: 1.1rem; }
    .form-step { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .form-step:last-of-type { border-bottom: none; }
    .form-step h3 { font-size: 1.5rem; margin-bottom: 1.5rem; color: #3b82f6; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group.full-width { grid-column: span 2; }
    .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
    .form-input, .form-textarea, .form-select, .detail-form {
        width: -webkit-fill-available; padding: 0.8rem 1rem; background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: white;
        font-size: 1rem; font-family: 'Inter', sans-serif;
    }
    .form-select>option{background-color: #232b3c;}
    .form-textarea { resize: vertical; }
    .education-entry { background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border-left: 3px solid #3b82f6; }
    .entry-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .entry-header h4 { margin: 0; font-size: 1rem; color: #cbd5e1; }
    .btn-remove-entry { background: #ef4444; color: white; border: none; padding: 0.3rem 0.6rem; border-radius: 5px; cursor: pointer; }
    .btn-add { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #cbd5e1; padding: 0.6rem 1rem; border-radius: 8px; cursor: pointer; }
    .btn-submit-ai { width: 100%; padding: 1rem; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; }
    .btn-submit-ai.loading { cursor: not-allowed; opacity: 0.7; }
    .btn-submit-ai .btn-text { display: inline; }
    .btn-submit-ai.loading .btn-text { display: none; }
    .loader { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-left-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; }
    .btn-submit-ai.loading .loader { display: block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .ad-space-left, .ad-space-right { display:none;}

    @media (min-width: 1200px) {.ad-space-left, .ad-space-right { width:200px; background: rgba(30, 41, 59, 0.5); border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-weight: 500; min-height: 500px; }}
</style>

<div class="ai-create-container">
    <div class="ai-create-card">
        <h1>AI-Powered CV Generation</h1>
        <p style="color: var(--text-secondary);">Provide your details and paste a job description. Our AI will craft a tailored CV draft for you to review and edit.</p>
        
        <form id="ai-cv-form" action="<?php echo BASE_URL; ?>api.php?action=generate_ai_cv" method="POST">
            
            <div class="form-step">
                <h3><i class="fas fa-user-circle"></i> Step 1: Your Personal Details</h3>
                <div class="form-grid">
                    <div class="form-group"><label>Full Name</label><input type="text" class="form-input" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required></div>
                    <div class="form-group"><label>Email</label><input type="email" class="form-input" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required></div>
                    <div class="form-group"><label>Phone Number</label><input type="tel" class="form-input" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Location</label><input type="text" class="form-input" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"></div>
                </div>
            </div>

            <div class="form-step">
                <h3><i class="fas fa-graduation-cap"></i> Step 2: Your Education</h3>
                <div id="education-container">
                    <!-- JS will add education entries here -->
                </div>
                <button type="button" id="add-education-btn" class="btn-add"><i class="fas fa-plus"></i> Add Education</button>
            </div>

            <div class="form-step">
                <h3><i class="fas fa-bullseye"></i> Step 3: The Job You're Targeting</h3>
                <div class="form-group"><label for="job_title">Job Title</label><input type="text" id="job_title" name="job_title" class="form-input" placeholder="e.g., Senior Frontend Developer" required></div>
                <div class="form-group"><label for="job_description">Paste the Full Job Description Here</label><textarea id="job_description" name="job_description" class="form-input" rows="12" required placeholder="Paste the responsibilities, requirements, and qualifications..."></textarea></div>
                <div class="form-group"><label for="about_company">About the Company (Optional)</label><textarea id="about_company" name="about_company" class="form-input" rows="4"></textarea></div>
            </div>
            
            <button type="submit" id="ai-submit-btn" class="btn-submit" style="width:100%;">
                <span class="btn-text"><i class="fas fa-magic"></i> Generate CV and Continue to Editor</span>
                <div class="loader"></div>
            </button>
        </form>
    </div>
</div>

<!-- Template for dynamic education entries -->
<template id="education-template">
    <div class="education-entry">
        <div class="entry-header">
            <h4>Education Entry</h4>
            <button type="button" class="btn-remove-entry" title="Remove this entry">&times;</button>
        </div>
        <div class="form-group"><label>Level</label><select name="edu_degree[]" class="form-input" required><option value="">-- Select Level --</option><option value="School">School</option><option value="+2 / High School">High School (+2)</option><option value="Bachelor's Degree">Bachelor's Degree</option><option value="Master's Degree">Master's Degree</option><option value="PhD">PhD</option></select></div>
        <div class="form-group"><label>Institution / University</label><input type="text" name="edu_institution[]" class="form-input" required></div>
        <div class="form-grid">
            <div class="form-group"><label>Start Date</label><input type="text" name="edu_start_date[]" class="form-input" placeholder="e.g., Aug 2018"></div>
            <div class="form-group"><label>End Date</label><input type="text" name="edu_end_date[]" class="form-input" placeholder="e.g., May 2022 (or leave blank)"></div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ai-cv-form');
    const submitBtn = document.getElementById('ai-submit-btn');

    form.addEventListener('submit', function() {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });

    // --- Dynamic Education Fields ---
    const educationContainer = document.getElementById('education-container');
    const addEducationBtn = document.getElementById('add-education-btn');
    const educationTemplate = document.getElementById('education-template');

    function addEducationEntry() {
        educationContainer.append(educationTemplate.content.cloneNode(true));
    }
    
    addEducationEntry(); // Add one entry by default
    addEducationBtn.addEventListener('click', addEducationEntry);

    educationContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-entry')) {
            e.target.closest('.education-entry').remove();
        }
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>