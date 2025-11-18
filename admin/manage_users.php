<?php
    // /admin/manage_users.php (Final, Corrected Version)
    $page_title = "Manage Users";
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db_connect.php';
    require_once __DIR__ . '/../includes/session_handler.php';
    require_admin();

    // --- Search and Filter Logic ---
    $search = trim($_GET['search'] ?? '');
    $filter_role = $_GET['role'] ?? 'all';

    // --- DEFINITIVE, CORRECTED SQL QUERY ---
    $sql = "SELECT 
                u.id, u.name, u.email, u.user_type, u.created_at, u.profile_image, 
                u.location, u.phone, u.is_active,
                (CASE 
                    WHEN u.user_type = 'recruitee' THEN (SELECT COUNT(*) FROM cvs WHERE user_id = u.id)
                    WHEN u.user_type = 'recruiter' THEN (SELECT COUNT(*) FROM jobs WHERE recruiter_user_id = u.id)
                    ELSE 0 
                END) as content_count,
                (CASE 
                    WHEN u.user_type = 'recruitee' THEN (SELECT AVG(rating) FROM recruitee_ratings WHERE recruitee_user_id = u.id)
                    WHEN u.user_type = 'recruiter' THEN (SELECT AVG(rating) FROM recruiter_ratings WHERE recruiter_user_id = u.id)
                    ELSE NULL 
                END) as average_rating,
                (CASE 
                    WHEN u.user_type = 'recruitee' THEN (SELECT COUNT(*) FROM recruitee_ratings WHERE recruitee_user_id = u.id)
                    WHEN u.user_type = 'recruiter' THEN (SELECT COUNT(*) FROM recruiter_ratings WHERE recruiter_user_id = u.id)
                    ELSE 0 
                END) as rating_count
            FROM users u
            WHERE u.user_type != 'admin'";

    $params = [];
    if (!empty($search)) {
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.location LIKE ?)";
        $like_term = '%' . $search . '%';
        $params[] = $like_term;
        $params[] = $like_term;
        $params[] = $like_term;
    }
    if ($filter_role === 'recruitee' || $filter_role === 'recruiter') {
        $sql .= " AND u.user_type = ?";
        $params[] = $filter_role;
    }
    $sql .= " ORDER BY u.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .admin-container { max-width: 1200px;    margin: 0px auto;}
    .quick-actions{margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;}
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 2rem; background: var(--secondary-bg); padding: 1rem; border-radius: 12px;}
    .admin-table { width: 100%; border-collapse: collapse; background: var(--secondary-bg); border-radius: 12px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: var(--primary-bg); }
    .action-links a, .action-links button { margin-right: 1rem; text-decoration: none; background: none; border: none; cursor: pointer; font-size: 1em; }
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
    .user-info-cell { display: flex; align-items: center; gap: 1rem; }
    .user-avatar-small { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .user-details { font-size: 0.9em; color: var(--text-secondary); }
    .status-badge { padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8em; font-weight: 500; }
    .status-active { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
    .status-suspended { background: rgba(239, 68, 68, 0.1); color: var(--error-color); }
    .fa-user-slash:before{
        content: "\f506";
        color: var(--success-color);
    }
    .fa-user-check:before {
        content: "\f4fc";
        color: var(--error-color);
    }
    .admin-content{overflow-x:auto;}
</style>

<div class="admin-container">
    <!-- Quick Actions (Moved up for better UX) -->
    <div class="quick-actions">
        <a href="<?php echo BASE_URL; ?>admin/manage_jobs.php" class="action-btn">
            <i class="fas fa-briefcase"></i>Manage Jobs
        </a>
        <a href="<?php echo BASE_URL; ?>admin/manage_blogs.php" class="action-btn">
            <i class="fas fa-newspaper"></i>Manage Blog
        </a>
    </div>
    
    <h1>Manage Users</h1>
    
    <!-- Filter/Search Bar -->
    <div class="filter-bar">
        <form style="display: flex; gap: 1rem; width: 100%;">
            <input type="search" name="search" class="form-input" placeholder="Search by name, email, or location..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="role" class="form-input">
                <option value="all" <?php if($filter_role == 'all') echo 'selected'; ?>>All Roles</option>
                <option value="recruitee" <?php if($filter_role == 'recruitee') echo 'selected'; ?>>Recruitee</option>
                <option value="recruiter" <?php if($filter_role == 'recruiter') echo 'selected'; ?>>Recruiter</option>
            </select>
            <button type="submit" class="btn-submit" style="width: auto;">Filter</button>
        </form>
    </div>

    <div class="admin-content">
        <table class="admin-table">
            <thead>
                <tr>
                    <tr>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Content</th>
                    <th>Rating</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
                </tr>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="8" style="text-align:center; padding: 2rem;">No users found matching your criteria.</td></tr>
                <?php else: ?>
                    <?php foreach($users as $user): ?>
                        <tr id="user-row-<?php echo $user['id']; ?>">
                            <td>
                                <div class="user-info-cell">
                                    <img src="<?php echo BASE_URL . ($user['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" class="user-avatar-small">
                                    <div>
                                        <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                        <div class="user-details"><?php echo htmlspecialchars($user['location'] ?? 'No location'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($user['email']); ?></div>
                                <div class="user-details"><?php echo htmlspecialchars($user['phone'] ?? 'No phone'); ?></div>
                            </td>
                            <td><?php echo ucfirst($user['user_type']); ?></td>
                            <td><span class="status-badge <?php echo $user['is_active'] ? 'status-active' : 'status-suspended'; ?>"><?php echo $user['is_active'] ? 'Active' : 'Suspended'; ?></span></td>
                            <td>
                                <?php if($user['user_type'] === 'recruitee'): echo $user['content_count'] . ' CVs'; endif; ?>
                                <?php if($user['user_type'] === 'recruiter'): echo $user['content_count'] . ' Jobs'; endif; ?>
                            </td>
                            <td>
                                <?php if ($user['rating_count'] > 0): ?>
                                    <div class="rating-cell">
                                        <i class="fas fa-star" style="color:#f59e0b;"></i>
                                        <strong><?php echo number_format($user['average_rating'], 1); ?></strong>
                                        <span class="user-details">(<?php echo $user['rating_count']; ?>)</span>
                                    </div>
                                <?php else: ?>
                                    <span class="user-details">Not Rated</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td class="action-links">
                                <a href="<?php echo BASE_URL; ?>profile.php?id=<?php echo $user['id']; ?>" target="_blank" title="View Profile"><i class="fas fa-eye"></i></a>
                                <button class="suspend-btn" data-user-id="<?php echo $user['id']; ?>" title="<?php echo $user['is_active'] ? 'Suspend User' : 'Unsuspend User'; ?>">
                                    <i class="fas <?php echo $user['is_active'] ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adminTableBody = document.querySelector('.admin-table tbody');
        if (!adminTableBody) return;

        // --- Modal Element References ---
        const confirmModal = document.getElementById('confirm-modal');
        const confirmTitle = document.getElementById('confirm-title');
        const confirmText = document.getElementById('confirm-text');
        const confirmBtn = document.getElementById('btn-confirm-action'); // Corrected ID
        const cancelBtn = document.getElementById('btn-cancel-action');   // Corrected ID

        let currentAction = null; // To store the function to run on confirm

        // --- Event Listener for Suspend actions ---
        adminTableBody.addEventListener('click', function(e) {
            if (e.target.closest('.suspend-btn')) {
                e.preventDefault();
                const button = e.target.closest('.suspend-btn');
                const userId = button.dataset.userId;
                const isCurrentlyActive = !button.querySelector('i').classList.contains('fa-user-check');

                confirmTitle.textContent = isCurrentlyActive ? 'Suspend User?' : 'Unsuspend User?';
                confirmText.textContent = isCurrentlyActive 
                    ? 'Suspending this user will prevent them from logging in.' 
                    : 'Unsuspending will allow this user to log in again.';
                
                // Set the action to be performed
                currentAction = () => {
                    const formData = new FormData();
                    formData.append('user_id', userId);

                    fetch('<?php echo BASE_URL; ?>api.php?action=admin_toggle_suspension', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('User status updated successfully.', 'success');
                            location.reload();
                        } else {
                            showToast(data.message || 'Failed to update status.', 'error');
                        }
                    })
                    .finally(() => confirmModal.classList.remove('active'));
                };

                confirmModal.classList.add('active');
            }
        });

        // --- Modal Button Listeners ---
        confirmBtn.addEventListener('click', () => {
            if (typeof currentAction === 'function') {
                currentAction();
                currentAction = null; // Reset for next use
            }
        });
        
        cancelBtn.addEventListener('click', () => {
            confirmModal.classList.remove('active');
            currentAction = null; // Reset
        });
    });
</script>