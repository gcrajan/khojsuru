<?php
// /create_post.php
$page_title = "Create Post: Khojsuru";
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';

// --- Role-Based Access Control ---
// Only 'recruitees' (job seekers) can create these posts.
if ($_SESSION['user_type'] !== 'recruitee') {
    // Redirect recruiters away from this page.
    header('Location: ' . BASE_URL);
    exit();
}
?>

<style>
    .form-container { max-width: 700px; margin: 2rem auto; }
    .form-card { background: var(--secondary-bg); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); }
    .form-card h1 { margin-top: 0; }
    .form-card textarea { min-height: 150px; resize: vertical; }

    @media (min-width: 768px) {
        .form-card { padding: 2.5rem; }
    }
</style>

<div class="form-container">
    <div class="form-card">
        <h1>Create a Post</h1>
        <p style="color: var(--text-secondary);">Share your skills, career goals, or let recruiters know you're open to opportunities.</p>
        
        <form id="create-post-form">
            <div class="form-group">
                <label for="content">Your Post</label>
                <textarea id="content" name="content" class="form-input" required placeholder="e.g., I'm a junior full-stack developer with experience in React and Node.js, looking for my first role in a fast-paced startup..."></textarea>
            </div>
            <button type="submit" id="submit-btn" class="btn-submit">
                <span class="btn-text">Publish Post</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-post-form');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Stop the default form submission

        // Provide user feedback
        submitBtn.disabled = true;
        submitBtn.textContent = 'Publishing...';

        const formData = new FormData(form);

        fetch('<?php echo BASE_URL; ?>api.php?action=create_user_post', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Use the global toast function from footer.php
                showToast('Post published successfully!', 'success');
                // Redirect back to the feed after a short delay
                setTimeout(() => {
                    window.location.href = '<?php echo BASE_URL; ?>';
                }, 1500);
            } else {
                showToast(data.message || 'An error occurred.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Publish Post';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('A network error occurred. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Publish Post';
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>