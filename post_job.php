<?php
$page_title = "Post Job: Khojsuru";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';

// --- Role-Based Access Control ---
if ($_SESSION['user_type'] !== 'recruiter') {
    header('Location: ' . BASE_URL);
    exit();
}

// --- Check for Company Profile ---
$company_id = null;
$company_stmt = $pdo->prepare("SELECT id FROM companies WHERE created_by_user_id = ? LIMIT 1");
$company_stmt->execute([$_SESSION['user_id']]);
$company = $company_stmt->fetch();

if ($company) {
    $company_id = $company['id'];
}

require_once __DIR__ . '/includes/header.php';
?>

<style>
    .form-container { max-width: 800px; margin: 2rem auto; }
    .form-card { background: var(--secondary-bg); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); }
    .form-card-title{font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; background: linear-gradient(135deg, var(--text-primary), var(--accent-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;} 
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; }
    .ck-editor__editable_inline {
        min-height: 250px;
        background: var(--primary-bg) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }
    @media (min-width: 768px) {
        .form-card { padding: 2.5rem; }
        .form-card-title{font-size: 2.5rem;}
        .form-grid { grid-template-columns: 1fr 1fr; }
    }
</style>

<div class="form-container">
    <div class="form-card">
        <?php if ($company_id): ?>
            <!-- VIEW 1: Show Job Posting Form -->
            <h1 class="form-card-title">Post a New Job</h1>
            <p style="color: var(--text-secondary);">Fill out the details below to find your next great hire.</p>
            <form id="post-job-form">
                <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">

                <div class="form-group">
                    <label for="title">Job Title</label>
                    <input type="text" id="title" name="title" class="form-input" required placeholder="e.g., Senior Frontend Developer">
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-input" required placeholder="e.g., San Francisco, CA or Remote">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="job_type">Job Type</label>
                        <select id="job_type" name="job_type" class="form-input">
                            <option>Full-time</option>
                            <option>Part-time</option>
                            <option>Contract</option>
                            <option>Internship</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Application Deadline</label>
                        <input type="datetime-local" id="deadline" name="deadline" class="form-input" required>
                    </div>
                     <div class="form-group">
                         <label>Work Style</label>
                         <div class="checkbox-group">
                            <input type="checkbox" id="is_remote" name="is_remote" value="1">
                            <label for="is_remote">This is a remote position</label>
                         </div>
                    </div>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="description">Job Description</label>
                    <!-- This textarea will be replaced by CKEditor -->
                    <textarea id="description" name="description" class="form-input"></textarea>
                </div>
                <button type="submit" id="submit-btn" class="btn-submit">Publish Job Posting</button>
            </form>

        <?php else: ?>
            <!-- VIEW 2: Show Company Creation Form -->
            <h1>Set Up Your Company Profile</h1>
            <p style="color: var(--text-secondary);">Before you can post jobs, please tell us about your company. This only needs to be done once.</p>
            <form id="create-company-form">
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="website">Company Website (Optional)</label>
                    <input type="url" id="website" name="website" class="form-input" placeholder="https://www.example.com">
                </div>
                <button type="submit" id="submit-btn" class="btn-submit">Create Company & Continue</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editor;
        ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'imageUpload', '|', 'undo', 'redo' ],
            ckfinder: {
                uploadUrl: '<?php echo BASE_URL; ?>api.php?action=ckeditor_image_upload'
            }
        })
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
        
        const postJobForm = document.getElementById('post-job-form');
        if (postJobForm) {
            const deadlineInput = document.getElementById('deadline');
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Adjust for local timezone
            deadlineInput.min = now.toISOString().slice(0, 16);
            postJobForm.addEventListener('submit', function(event) {
                event.preventDefault();
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Publishing...';
                
                const editorData = editor.getData();
                const formData = new FormData(postJobForm);
                formData.set('description', editorData); 

                fetch('<?php echo BASE_URL; ?>api.php?action=post_new_job', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Job posted successfully!', 'success');
                        setTimeout(() => { window.location.href = '<?php echo BASE_URL; ?>'; }, 1500);
                    } else {
                        showToast(data.message || 'An error occurred.', 'error');
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Publish Job Posting';
                    }
                });
            });
        }
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>