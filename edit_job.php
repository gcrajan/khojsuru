<?php
// /edit_job.php
$page_title = "Edit Job: Khojsuru";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

// Security: User must be a recruiter
if ($_SESSION['user_type'] !== 'recruiter') {
    header('Location: ' . BASE_URL);
    exit();
}

$job_id = (int)($_GET['id'] ?? 0);
if ($job_id === 0) {
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}

// Fetch the job data and verify ownership
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND recruiter_user_id = ?");
$stmt->execute([$job_id, $_SESSION['user_id']]);
$job = $stmt->fetch();

if (!$job) {
    // If job doesn't exist or doesn't belong to this recruiter, redirect them.
    header('Location: ' . BASE_URL . 'dashboard.php');
    exit();
}
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
        <h1 class="form-card-title">Edit Job Posting</h1>
        <p style="color: var(--text-secondary);">Update the details for your job opening below.</p>
        <form id="edit-job-form">
            <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">

            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" class="form-input" required value="<?php echo htmlspecialchars($job['title']); ?>">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-input" required value="<?php echo htmlspecialchars($job['location']); ?>">
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="job_type">Job Type</label>
                    <select id="job_type" name="job_type" class="form-input">
                        <option <?php if($job['job_type'] == 'Full-time') echo 'selected'; ?>>Full-time</option>
                        <option <?php if($job['job_type'] == 'Part-time') echo 'selected'; ?>>Part-time</option>
                        <option <?php if($job['job_type'] == 'Contract') echo 'selected'; ?>>Contract</option>
                        <option <?php if($job['job_type'] == 'Internship') echo 'selected'; ?>>Internship</option>
                    </select>
                </div>
                 <div class="form-group">
                     <label>Work Style</label>
                     <div class="checkbox-group">
                        <input type="checkbox" id="is_remote" name="is_remote" value="1" <?php if($job['is_remote']) echo 'checked'; ?>>
                        <label for="is_remote">This is a remote position</label>
                     </div>
                </div>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="description">Job Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($job['description']); ?></textarea>
            </div>
            <button type="submit" id="submit-btn" class="btn-submit">Save Changes</button>
        </form>
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
        .catch(error => { console.error('CKEditor initialization error:', error); });
    
    const editJobForm = document.getElementById('edit-job-form');
    if (editJobForm) {
        editJobForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            const editorData = editor.getData();
            const formData = new FormData(editJobForm);
            formData.set('description', editorData); 

            fetch('<?php echo BASE_URL; ?>api.php?action=update_job', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Job updated successfully!', 'success');
                    // Redirect back to dashboard after a short delay
                    setTimeout(() => { window.location.href = '<?php echo BASE_URL; ?>dashboard.php'; }, 1500);
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Save Changes';
                }
            });
        });
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>