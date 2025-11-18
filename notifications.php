<?php
    $page_title = "Notifications: Khojsuru";

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/includes/header.php';

    $user_id = $_SESSION['user_id'];

    // Pagination setup
    $per_page = 15; // Number of notifications per page
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;

    // Total notifications count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
    $count_stmt->execute([$user_id]);
    $total_notifications = $count_stmt->fetchColumn();
    $total_pages = ceil($total_notifications / $per_page);

    // Fetch notifications for current page
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$user_id, $per_page, $offset]);
    $notifications = $stmt->fetchAll();

    // Mark all as read
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0")->execute([$user_id]);

    // Helper function to get an icon based on notification type
    function get_notification_icon($type) {
        switch ($type) {
            case 'new_applicant': return 'fa-user-plus';
            case 'status_change': return 'fa-briefcase';
            case 'new_comment': return 'fa-comment';
            case 'new_reply': return 'fa-reply';
            case 'new_rating': return 'fa-star';
            default: return 'fa-bell';
        }
    }
?>

<style>
    .notifications-container {
        max-width: 800px;
        margin: 2rem auto;
        overflow: hidden;
    }
    .notifications-header {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .notifications-header h1 {
        background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
        font-size: 1.75rem;
    }
    .notification-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .delete-notification-btn{
        font-size: 1rem;}
    .delete-notification{
        background: none;
        border: none;
        color: var(--error-color);
    }
    .delete-notification:hover{
        color: var(--text-primary);
        cursor:pointer;
    }
    .notification-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    .notification-item:last-child { border-bottom: none; }
    .notification-item:hover { 
        background: var(--secondary-bg); }
    
    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-color);
        font-size: 1.1rem;
    }
    .notification-content {
        flex-grow: 1;
        color: var(--text-secondary);
    }
    .notification-content strong {
        color: var(--text-primary);
        font-weight: 500;
    }
    .notification-time {
        font-size: 0.8em;
        margin-top: 0.25rem;
        color: var(--text-secondary);
        opacity: 0.7;
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 2rem 0;
    }

    .pagination-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 0.5rem;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-secondary);
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination-link:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
        transform: translateY(-2px);
    }

    .pagination-link.active {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }

    .pagination-link.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    .confirm-modal-overlay {
        position: fixed;
        top:0; left:0; right:0; bottom:0;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .confirm-modal-overlay.active { display: flex; }

    .confirm-modal {
        background: var(--primary-bg);
        padding: 2rem;
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    }

    .confirm-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
        gap: 1rem;
    }
    .btn-confirm-delete{background: var(--error-color);
    color: white;}
    .btn-manual {
        background: #535e71;
    }
</style>

<div class="notifications-container">
    <div class="notifications-header">
        <h1>Notifications</h1>
        <?php if (!empty($notifications)): ?>
            <div>
                <button id="delete-all-btn" class="delete-notification">Delete All</button>
            </div>
        <?php endif; ?>

    </div>
    <div class="notification-list">
        <?php if (empty($notifications)): ?>
            <p class="empty-state">You have no notifications yet.</p>
        <?php else: ?>
            <?php foreach($notifications as $n): ?>
                <a href="<?php echo BASE_URL . htmlspecialchars($n['link']); ?>" class="notification-item" data-id="<?php echo $n['id']; ?>">
                    <div class="notification-icon">
                        <i class="fas <?php echo get_notification_icon($n['type']); ?>"></i>
                    </div>
                    <div class="notification-content">
                        <p style="margin:0;"><?php echo $n['message']; ?></p>
                        <div class="notification-time"><?php echo date('F d, Y \a\t h:i A', strtotime($n['created_at'])); ?></div>
                    </div>
                    <div class="notification-actions">
                        <button class="delete-notification delete-notification-btn" title="Delete Notification"><i class="fas fa-trash"></i></button>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-container">
        <a href="?page=<?php echo max(1, $page - 1); ?>" class="pagination-link <?php echo $page == 1 ? 'disabled' : ''; ?>">&laquo; Prev</a>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="pagination-link <?php echo $page == $total_pages ? 'disabled' : ''; ?>">Next &raquo;</a>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="confirm-modal-overlay" id="notification-confirm-modal">
    <div class="confirm-modal">
        <h3 id="notification-confirm-title">Are you sure?</h3>
        <p id="notification-confirm-text">This action cannot be undone.</p>
        <div class="confirm-actions">
            <button id="btn-cancel-notification-delete" class="btn-submit btn-manual">Cancel</button>
            <button id="btn-confirm-notification-delete" class="btn-submit btn-confirm-delete">Yes, Delete</button>
        </div>
    </div>
</div>


<?php include_once __DIR__ . '/includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmModal = document.getElementById('notification-confirm-modal');
        const confirmTitle = document.getElementById('notification-confirm-title');
        const confirmText = document.getElementById('notification-confirm-text');
        const btnCancel = document.getElementById('btn-cancel-notification-delete');
        const btnConfirm = document.getElementById('btn-confirm-notification-delete');

        let deleteEndpoint = '';
        let elementToRemove = null;

        // Single notification delete
        document.querySelectorAll('.delete-notification-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                elementToRemove = this.closest('.notification-item');
                const notificationId = elementToRemove.dataset.id;

                deleteEndpoint = 'api.php?action=delete_notification';
                confirmText.textContent = 'This notification will be permanently deleted.';

                confirmModal.classList.add('active');

                btnConfirm.onclick = () => {
                    const formData = new URLSearchParams({ notification_id: notificationId });
                    fetch(deleteEndpoint, { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (elementToRemove) elementToRemove.remove();
                                showToast(data.message, 'success');
                            } else {
                                showToast(data.message || 'Failed to delete.', 'error');
                            }
                        })
                        .finally(() => confirmModal.classList.remove('active'));
                };
            });
        });

        // Delete all notifications
        const deleteAllBtn = document.getElementById('delete-all-btn');
        if (deleteAllBtn) {
            deleteAllBtn.addEventListener('click', function() {
                deleteEndpoint = 'api.php?action=delete_all_notifications';
                elementToRemove = null; // all
                confirmText.textContent = 'All notifications will be permanently deleted.';
                confirmModal.classList.add('active');

                btnConfirm.onclick = () => {
                    fetch(deleteEndpoint, { method: 'POST' })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelectorAll('.notification-item').forEach(n => n.remove());
                                showToast(data.message, 'success');
                            } else {
                                showToast(data.message || 'Failed to delete.', 'error');
                            }
                        })
                        .finally(() => confirmModal.classList.remove('active'));
                };
            });
        }

        // Cancel button
        btnCancel.addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });
    });


</script>