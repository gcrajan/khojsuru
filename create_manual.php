<?php
    $page_title = "Create CV Manually: Khojsuru"; 
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/includes/header.php';

    if ($_SESSION['user_type'] !== 'recruitee') {
        header('Location: ' . BASE_URL);
        exit();
    }
?>

<style>
    .create-container { max-width: 800px; margin: 2rem auto; }
    .create-card { background: var(--secondary-bg); padding: 2.5rem; border-radius: 16px; }
    .templates-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .template-card {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        overflow: hidden; /* Ensures border-radius is respected on image */
    }
    .template-card img {
        width: 100%;
        display: block;
        aspect-ratio: 3/4;
        object-fit: cover;
        object-position: top; /* Show the top part of the template */
        background: #334155;
    }
    .template-card .name { font-weight: 500; padding: 0.75rem 0.5rem; }
    .template-card input[type="radio"] { display: none; }
    .template-card.selected { border-color: var(--accent-color); box-shadow: 0 0 10px rgba(59, 130, 246, 0.3); }
    .btn-submit:hover{background: #206eed;}
</style>

<div class="create-container">
    <div class="create-card">
        <h1>Create a New CV</h1>
        <p style="color: var(--text-secondary);">Start by giving your CV a title and choosing a design. You'll add experience, skills, and more on the next step.</p>
        
        <form id="create-cv-form" method="POST">
            <div class="form-group">
                <label for="cv_title">CV Title</label>
                <input type="text" id="cv_title" name="cv_title" class="form-input" placeholder="e.g., My Resume for Tech Roles" required>
            </div>
            <div class="form-group">
                <label for="target_role">Target Job Role</label>
                <input type="text" id="target_role" name="target_role" class="form-input" placeholder="e.g., Senior Software Engineer" required>
            </div>

            <div class="form-group">
                <label>Choose a Template</label>
                <div class="templates-grid">
                    <label class="template-card">
                        <input type="radio" name="template_name" value="classic">
                        <img src="<?php echo BASE_URL; ?>assets/images/classic-template.png" alt="Classic Template">
                        <div class="name">Classic</div>
                    </label>
                    <label class="template-card">
                        <input type="radio" name="template_name" value="modern" checked>
                        <img src="<?php echo BASE_URL; ?>assets/images/modern-template.png" alt="Modern Template">
                        <div class="name">Modern</div>
                    </label>
                    <label class="template-card">
                        <input type="radio" name="template_name" value="creative">
                        <img src="<?php echo BASE_URL; ?>assets/images/creative-template.png" alt="Creative Template">
                        <div class="name">Creative</div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 1rem;">Create & Continue to Editor</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Template Selection Visual Feedback ---
        const templateCards = document.querySelectorAll('.template-card');
        templateCards.forEach(card => {
            card.addEventListener('click', function() {
                templateCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
        // Set initial selection based on the 'checked' attribute
        document.querySelector('.template-card input:checked').closest('.template-card').classList.add('selected');

        // --- AJAX Form Submission ---
        const createForm = document.getElementById('create-cv-form');
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';

            const formData = new FormData(this);

            fetch('<?php echo BASE_URL; ?>api.php?action=create_manual_cv', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                // Check if response is valid JSON
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (data.success && data.cv_id) {
                    // Redirect to the editor page for the new CV
                    window.location.href = `<?php echo BASE_URL; ?>edit.php?id=${data.cv_id}`;
                } else {
                    // Use the global showToast function from footer.php
                    showToast(data.message || 'An error occurred. Please check the form.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Create & Continue to Editor';
                }
            })
            .catch(err => {
                console.error('Fetch Error:', err);
                showToast('A network or server error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create & Continue to Editor';
            });
        });
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>