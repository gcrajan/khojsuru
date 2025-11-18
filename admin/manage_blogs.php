<?php
    $page_title = "Manage Blog";
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db_connect.php';
    require_once __DIR__ . '/../includes/session_handler.php';
    require_admin();

    // Editing existing post?
    $edit_post = null;
    if (isset($_GET['edit_id'])) {
        $edit_id = (int)$_GET['edit_id'];
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_post = $stmt->fetch();

        // Safely load HTML for the editor. Some rows may have content_json, others content_html.
        if ($edit_post) {
            if (isset($edit_post['content_json']) && !empty($edit_post['content_json'])) {
                $json = json_decode($edit_post['content_json'], true);
                $edit_post['content_html'] = $json['html'] ?? '';
            } else {
                // If content_html exists in the row, use it; otherwise default to empty string
                $edit_post['content_html'] = $edit_post['content_html'] ?? '';
            }
        }
    }

    // Fetch all posts
    $all_posts_stmt = $pdo->prepare(
        "SELECT p.*, u.name as author_name FROM blog_posts p JOIN users u ON p.author_user_id = u.id ORDER BY p.created_at DESC"
    );
    $all_posts_stmt->execute();
    $all_posts = $all_posts_stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .admin-container { max-width: 1200px; margin: 0px auto;}
    .quick-actions{margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;}
    .editor-card { background: var(--secondary-bg); padding: 2rem; border-radius: 12px; margin-bottom: 2rem; }
    .ck-editor__editable_inline {
        min-height: 250px;
        background: var(--primary-bg) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }
    .admin-table { width: 100%; border-collapse: collapse; background: var(--secondary-bg); border-radius: 12px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: var(--primary-bg); }
    .action-links a, .action-links button { margin-right: 1rem; text-decoration: none; background: none; border: none; cursor: pointer; font-size: 1em; }
    .status-badge { padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8em; font-weight: 500; }
    .status-publish { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
    .status-draft { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .delete-btn { color: var(--error-color); }
    /* Confirmation Modal */
    .confirm-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7); backdrop-filter: blur(5px);
        z-index: 5000; display: none; align-items: center; justify-content: center;
    }
    .confirm-modal-overlay.active { display: flex; }
    .confirm-modal {
        background: var(--secondary-bg); border-radius: 16px; padding: 2rem;
        width: 90%; max-width: 400px; text-align: center;
    }
    .confirm-modal h3 { margin-top: 0; }
    .confirm-actions { display: flex; gap: 1rem; margin-top: 2rem; }
    .confirm-actions button { flex: 1; padding: 0.75rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    #btn-confirm-action { background: var(--error-color); color: white; }
    .cancel-edit-btn{margin-top:2rem; font-size: 1.25rem; text-align: center;}
    .cancel-edit-btn a{color: var(--ck-color-base-error); text-decoration: none; background: none;}
    .cancel-edit-btn a:hover{color: #f59595;}
    .admin-content{overflow-x:auto;}
</style>

<div class="admin-container">

    <div class="quick-actions">
        <a href="<?php echo BASE_URL; ?>admin/manage_users.php" class="action-btn">
            <i class="fas fa-users"></i>Manage Users
        </a>
        <a href="<?php echo BASE_URL; ?>admin/manage_jobs.php" class="action-btn">
            <i class="fas fa-briefcase"></i>Manage Jobs
        </a>
    </div>

    <h1>Manage Job Bloggings</h1>

    <div class="editor-card">
        <h1><?php echo $edit_post ? 'Edit Blog Post' : 'Create New Blog Post'; ?></h1>
        <form id="blog-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="post_id" value="<?php echo $edit_post['id'] ?? 0; ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_post['featured_image'] ?? ''); ?>">

            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" name="title" id="title" class="form-input" 
                       value="<?php echo htmlspecialchars($edit_post['title'] ?? ''); ?>" required>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="featured_image">Featured Image</label>
                    <input type="file" name="featured_image" id="featured_image" class="form-input" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-input">
                        <option value="draft" <?php if(isset($edit_post) && ($edit_post['status'] ?? '') == 'draft') echo 'selected'; ?>>Draft</option>
                        <option value="published" <?php if(isset($edit_post) && ($edit_post['status'] ?? '') == 'published') echo 'selected'; ?>>Published</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="editor">Content</label>
                <textarea name="content_html" id="editor"><?php echo $edit_post['content_html'] ?? ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit"><?php echo $edit_post ? 'Update Post' : 'Create Post'; ?></button>
            <?php if ($edit_post): ?>
                <div class="cancel-edit-btn">
                    <a href="manage_blogs.php" class="btn-submit btn-manual">Cancel Edit</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <h2>Existing Blog Posts</h2>
    <div class="admin-content">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($all_posts as $post): ?>
                    <tr id="post-row-<?php echo $post['id']; ?>">
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                        <td><span class="status-badge <?php echo ($post['status'] ?? '') == 'published' ? 'status-publish' : 'status-draft'; ?>"><?php echo ucfirst($post['status'] ?? 'draft'); ?></span></td>
                        <td><?php echo isset($post['created_at']) ? date('M d, Y', strtotime($post['created_at'])) : ''; ?></td>
                        <td class="action-links">
                            <a href="manage_blogs.php?edit_id=<?php echo $post['id']; ?>" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                            <?php if(($post['status'] ?? '') == 'published'): ?>
                                <a href="<?php echo BASE_URL; ?>article.php?slug=<?php echo $post['slug']; ?>" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                            <?php endif; ?>
                            <button class="delete-btn" data-post-id="<?php echo $post['id']; ?>" title="Delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="confirm-modal-overlay" id="confirm-modal">
    <div class="confirm-modal">
        <h3 id="confirm-title">Are you sure?</h3>
        <p id="confirm-text">This action cannot be undone.</p>
        <div class="confirm-actions">
            <button type="button" id="btn-cancel-action" class="btn-submit btn-manual">Cancel</button>
            <button type="button" id="btn-confirm-action" class="btn-submit">Yes, Confirm</button>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editor;
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'heading', '|', 
                    'bold', 'italic', 'link', 
                    'bulletedList', 'numberedList', '|', 
                    'imageUpload', '|', 
                    'undo', 'redo'
                ],
                ckfinder: {
                    uploadUrl: '<?php echo BASE_URL; ?>api.php?action=blogs_ckeditor_image'
                }
            })
            .then(newEditor => { editor = newEditor; })
            .catch(error => { console.error('CKEditor error:', error); });

        // Save (Create/Update)
        const blogForm = document.getElementById('blog-form');
        blogForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            const contentData = editor.getData();
            const formData = new FormData(blogForm);
            formData.set('content_html', contentData);

            fetch('<?php echo BASE_URL; ?>api.php?action=admin_save_blog_post', {
                method: 'POST', body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => { window.location.href = 'manage_blogs.php'; }, 1500);
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Network error.', 'error');
                submitBtn.disabled = false;
            });
        });

        // Delete with Modal
        const confirmModal = document.getElementById('confirm-modal');
        let currentAction = null;

        document.querySelector('.admin-table').addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                const button = e.target.closest('.delete-btn');
                const postId = button.dataset.postId;

                document.getElementById('confirm-title').textContent = 'Delete Blog Post?';
                document.getElementById('confirm-text').textContent = 'This action is irreversible.';
                confirmModal.classList.add('active');

                currentAction = () => {
                    const formData = new FormData();
                    formData.append('post_id', postId);
                    fetch('<?php echo BASE_URL; ?>api.php?action=admin_delete_blog_post', {
                        method: 'POST', body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Post deleted successfully.', 'success');
                            const row = document.getElementById(`post-row-${postId}`);
                            if (row) row.remove();
                        } else {
                            showToast(data.message || 'Failed to delete post.', 'error');
                        }
                    })
                    .finally(() => confirmModal.classList.remove('active'));
                };
            }
        });

        document.getElementById('btn-confirm-action').addEventListener('click', () => {
            if (typeof currentAction === 'function') { currentAction(); }
        });

        document.getElementById('btn-cancel-action').addEventListener('click', () => {
            confirmModal.classList.remove('active');
            currentAction = null;
        });
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>