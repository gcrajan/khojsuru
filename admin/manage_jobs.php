<?php
    $page_title = "Manage Jobs";
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db_connect.php';
    require_once __DIR__ . '/../includes/session_handler.php';
    require_admin();

    // --- Filter Logic ---
    $filter_status = $_GET['status'] ?? 'all';

    $sql = "SELECT 
                j.id, j.title, j.is_active, j.is_featured, j.posted_at, 
                c.name as company_name, 
                u.name as recruiter_name,
                (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as application_count
            FROM jobs j
            JOIN companies c ON j.company_id = c.id
            JOIN users u ON j.recruiter_user_id = u.id";

    $params = [];
    if ($filter_status === 'active') {
        $sql .= " WHERE j.is_active = 1";
    } elseif ($filter_status === 'inactive') {
        $sql .= " WHERE j.is_active = 0";
    }
    $sql .= " ORDER BY j.posted_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .admin-container { max-width: 1200px;    margin: 0px auto;}
    .quick-actions{margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;}
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .filter-bar { display: flex; gap: 1rem; margin-bottom: 2rem; background: var(--secondary-bg); padding: 1rem; border-radius: 12px;}
    .admin-table { width: 100%; border-collapse: collapse; background: var(--secondary-bg); border-radius: 12px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
    .admin-table tbody tr:last-child td { border-bottom: none; }
    .admin-table tbody tr:hover { background: var(--primary-bg); }
    .action-links a, .action-links button { margin-right: 1rem; text-decoration: none; background: none; border: none; cursor: pointer; font-size: 1em; }
    .status-badge { padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8em; font-weight: 500; }
    .status-active { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
    .status-inactive { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    .featured-badge {
        background: rgba(217, 119, 6, 0.1); color: #f59e0b;
        padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8em; font-weight: 500;
        margin-left: 0.5rem;
    }
    .toggle-switch-small { /* A smaller version of the dashboard toggle */
        position: relative; display: inline-block; width: 34px; height: 20px;
    }
    .toggle-switch-small input { opacity: 0; width: 0; height: 0; }
    .slider-small { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 20px; }
    .slider-small:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider-small { background-color: var(--accent-color); }
    input:checked + .slider-small:before { transform: translateX(14px); }
    .admin-content{overflow-x:auto;}
    .status-toggle-btn>i:before{font-size:1.25rem;}
</style>

<div class="admin-container">
    
    <div class="quick-actions">
        <a href="<?php echo BASE_URL; ?>admin/manage_users.php" class="action-btn">
            <i class="fas fa-users"></i>Manage Users
        </a>
        <a href="<?php echo BASE_URL; ?>admin/manage_blogs.php" class="action-btn">
            <i class="fas fa-newspaper"></i>Manage Blog
        </a>
    </div>

    <h1>Manage Job Postings</h1>

    <div class="filter-bar">
        <form style="display: flex; gap: 1rem; width: 100%;">
            <select name="status" class="form-input">
                <option value="all" <?php if($filter_status == 'all') echo 'selected'; ?>>All Statuses</option>
                <option value="active" <?php if($filter_status == 'active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($filter_status == 'inactive') echo 'selected'; ?>>Inactive</option>
            </select>
            <button type="submit" class="btn-submit" style="width: auto;">Filter</button>
        </form>
    </div>

    <div class="admin-content">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Posted By</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="jobs-table-body">
                <?php foreach($jobs as $job): ?>
                    <tr id="job-row-<?php echo $job['id']; ?>">
                        <td>
                            <?php echo htmlspecialchars($job['title']); ?>
                            <?php if ($job['is_featured']): ?>
                                <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($job['recruiter_name']); ?></td>
                        <td><?php echo $job['application_count']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $job['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $job['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <label class="toggle-switch-small">
                                <input type="checkbox" class="featured-toggle" data-job-id="<?php echo $job['id']; ?>" <?php echo $job['is_featured'] ? 'checked' : ''; ?>>
                                <span class="slider-small"></span>
                            </label>
                        </td>
                        <td class="action-links">
                            <a href="<?php echo BASE_URL; ?>view_job.php?id=<?php echo $job['id']; ?>" target="_blank" title="View Job Post"><i class="fas fa-eye"></i></a>
                            <button class="status-toggle-btn" data-job-id="<?php echo $job['id']; ?>" title="<?php echo $job['is_active'] ? 'Deactivate' : 'Activate'; ?> Job">
                                <i class="fas <?php echo $job['is_active'] ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('jobs-table-body');
        if (!tableBody) return;

        tableBody.addEventListener('change', function(e) {
            // --- Handle Featured Toggle Switch ---
            if (e.target.classList.contains('featured-toggle')) {
                const checkbox = e.target;
                const jobId = checkbox.dataset.jobId;

                const formData = new FormData();
                formData.append('job_id', jobId);

                fetch('<?php echo BASE_URL; ?>api.php?action=admin_toggle_job_featured', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Featured status updated.', 'success');
                        location.reload(); // Reload to update the badge
                    } else {
                        showToast(data.message || 'Failed to update.', 'error');
                        checkbox.checked = !checkbox.checked; // Revert on failure
                    }
                });
            }
        });

        tableBody.addEventListener('click', function(e) {
            // --- Handle Deactivate/Reactivate Button ---
            if (e.target.closest('.status-toggle-btn')) {
                const button = e.target.closest('.status-toggle-btn');
                const jobId = button.dataset.jobId;

                const formData = new FormData();
                formData.append('job_id', jobId);
                
                fetch('<?php echo BASE_URL; ?>api.php?action=admin_toggle_job_status', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('Job status updated.', 'success');
                        location.reload();
                    } else {
                        showToast(data.message || 'Failed to update status.', 'error');
                    }
                });
            }
        });
    });
</script>