<?php
// /applicants.php
$page_title = "View Applicants: Khojsuru";
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

// --- Role-Based Access Control ---
if ($_SESSION['user_type'] !== 'recruiter') {
    header('Location: ' . BASE_URL);
    exit();
}

$job_id = (int)($_GET['job_id'] ?? 0);
if ($job_id === 0) { die("Invalid job specified."); }

// --- Security Check: Verify this job belongs to the logged-in recruiter ---
$job_stmt = $pdo->prepare("SELECT title FROM jobs WHERE id = ? AND recruiter_user_id = ?");
$job_stmt->execute([$job_id, $_SESSION['user_id']]);
$job = $job_stmt->fetch();
if (!$job) {
    die("Job not found or you do not have permission to view its applicants.");
}

// --- Fetch all applicants for this job ---
$app_stmt = $pdo->prepare(
    "SELECT 
        a.id as application_id, a.status, a.application_date,
        a.cv_id, a.uploaded_cv_path, a.recruitee_user_id,
        u.id as user_id, u.name, u.phone, u.location, u.profile_image,
        c.title as cv_title
     FROM applications a
     JOIN users u ON a.recruitee_user_id = u.id
     LEFT JOIN cvs c ON a.cv_id = c.id
     WHERE a.job_id = ?
     ORDER BY a.application_date DESC"
);
$app_stmt->execute([$job_id]);
$applicants = $app_stmt->fetchAll();
?>

<style>
    .applicants-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .page-header {
        background: linear-gradient(135deg, var(--accent-color), #6366f1);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-header h1 {
        color: white;
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .applicants-count {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }

    .applicants-grid {
        display: grid;
        gap: 1.5rem;
    }

    .applicant-card {
        background: var(--secondary-bg);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .applicant-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--accent-color);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .applicant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        border-color: var(--accent-color);
    }

    .applicant-card:hover::before {
        opacity: 1;
    }

    .applicant-header {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1.5rem;
        align-items: start;
        margin-bottom: 1.5rem;
    }

    .applicant-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent-color);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .applicant-basic-info h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .status-badge {
        width: fit-content;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-submitted {
        background: rgba(156, 163, 175, 0.2);
        color: var(--text-secondary);
    }

    .status-viewed {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-color);
    }

    .status-interviewing {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .status-rejected {
        background: rgba(239, 68, 68, 0.2);
        color: var(--error-color);
    }

    .status-hired {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
    }

    .applicant-details {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 2rem;
        align-items: start;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--primary-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .info-item:hover {
        border-color: var(--accent-color);
        transform: translateY(-1px);
    }

    .info-item i {
        color: var(--accent-color);
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }

    .info-item a {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .info-item a:hover {
        color: var(--accent-color);
    }

    .info-item span {
        color: var(--text-secondary);
        font-weight: 500;
    }

    .cv-section {
        margin-top: 1.5rem;
    }

    .cv-link {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, var(--accent-color), #6366f1);
        color: white;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .cv-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .cv-link i {
        font-size: 1.1rem;
    }

    .actions-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .status-selector {
        background: var(--secondary-bg);
        border: 2px solid var(--border-color);
        color: var(--text-primary);
        padding: 1rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 180px;
    }

    .status-selector:hover {
        border-color: var(--accent-color);
    }

    .status-selector:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .application-date {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--secondary-bg);
        border-radius: 20px;
        border: 2px dashed var(--border-color);
        color: var(--text-secondary);
        font-size: 1.2rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 1rem;
        display: block;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .applicants-container {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .applicant-card {
            padding: 1.5rem;
        }

        .applicant-header {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .applicant-details {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .status-selector {
            min-width: auto;
        }
    }

    /* Animation for status updates */
    @keyframes statusUpdate {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .status-updated {
        animation: statusUpdate 0.5s ease;
    }
</style>

<div class="applicants-container">
    <div class="page-header">
        <div class="page-header-content">
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <div class="applicants-count">
                <i class="fas fa-users"></i>
                <?php echo count($applicants); ?> Applicant<?php echo count($applicants) !== 1 ? 's' : ''; ?>
            </div>
        </div>
    </div>
    
    <?php if (empty($applicants)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <div>No applicants yet</div>
            <div style="font-size: 1rem; margin-top: 0.5rem;">Check back later for new applications</div>
        </div>
    <?php else: ?>
        <div class="applicants-grid">
            <?php foreach ($applicants as $applicant): ?>
                <div class="applicant-card" id="application-<?php echo $applicant['application_id']; ?>">
                    <div class="applicant-header">
                        <img src="<?php echo BASE_URL . ($applicant['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                             alt="<?php echo htmlspecialchars($applicant['name']); ?>" 
                             class="applicant-avatar">
                        
                        <div class="applicant-basic-info">
                            <h4><?php echo htmlspecialchars($applicant['name']); ?></h4>
                            <div class="application-date">
                                <i class="fas fa-calendar-alt"></i>
                                Applied on <?php echo date('M j, Y', strtotime($applicant['application_date'])); ?>
                            </div>
                        </div>

                        <div class="status-badge status-<?php echo $applicant['status']; ?>">
                            <?php 
                            $status_icons = [
                                'submitted' => 'fas fa-paper-plane',
                                'viewed' => 'fas fa-eye',
                                'interviewing' => 'fas fa-comments',
                                'rejected' => 'fas fa-times-circle',
                                'hired' => 'fas fa-check-circle'
                            ];
                            ?>
                            <i class="<?php echo $status_icons[$applicant['status']] ?? 'fas fa-question-circle'; ?>"></i>
                            <?php echo ucfirst($applicant['status']); ?>
                        </div>
                    </div>

                    <div class="applicant-details">
                        <div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <a href="profile.php?id=<?php echo $applicant['recruitee_user_id']; ?>">View Full Profile</a>
                                </div>
                                
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($applicant['location']); ?></span>
                                </div>
                                
                                <div class="info-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($applicant['phone']); ?></span>
                                </div>
                            </div>

                            <?php if ($applicant['cv_id'] || $applicant['uploaded_cv_path']): ?>
                                <div class="cv-section">
                                    <?php if ($applicant['cv_id']): ?>
                                        <a href="view_cv.php?id=<?php echo $applicant['cv_id']; ?>" class="cv-link" target="_blank">
                                            <i class="fas fa-file-alt"></i>
                                            View CV: <?php echo htmlspecialchars($applicant['cv_title']); ?>
                                        </a>
                                    <?php elseif ($applicant['uploaded_cv_path']): ?>
                                        <a href="<?php echo BASE_URL . htmlspecialchars($applicant['uploaded_cv_path']); ?>" class="cv-link" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                            View Uploaded Resume
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="actions-section">
                            <select class="status-selector" data-application-id="<?php echo $applicant['application_id']; ?>">
                                <option value="submitted" <?php if($applicant['status'] == 'submitted') echo 'selected'; ?>>📋 Submitted</option>
                                <option value="viewed" <?php if($applicant['status'] == 'viewed') echo 'selected'; ?>>👀 Viewed</option>
                                <option value="interviewing" <?php if($applicant['status'] == 'interviewing') echo 'selected'; ?>>💬 Interviewing</option>
                                <option value="rejected" <?php if($applicant['status'] == 'rejected') echo 'selected'; ?>>❌ Rejected</option>
                                <option value="hired" <?php if($applicant['status'] == 'hired') echo 'selected'; ?>>✅ Hired</option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelectors = document.querySelectorAll('.status-selector');
    
    statusSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            const applicationId = this.dataset.applicationId;
            const newStatus = this.value;
            const card = document.getElementById(`application-${applicationId}`);

            // Visual feedback during update
            card.style.opacity = '0.7';
            this.disabled = true;

            // Prepare the data to send
            const formData = new FormData();
            formData.append('application_id', applicationId);
            formData.append('status', newStatus);
            
            // Send the update to the API
            fetch('<?php echo BASE_URL; ?>api.php?action=update_application_status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Status updated successfully!', 'success');
                    
                    // Update the status badge
                    const statusBadge = card.querySelector('.status-badge');
                    statusBadge.className = `status-badge status-${newStatus}`;
                    statusBadge.innerHTML = `<i class="${getStatusIcon(newStatus)}"></i>${capitalize(newStatus)}`;
                    
                    // Add success animation
                    card.classList.add('status-updated');
                    setTimeout(() => {
                        card.classList.remove('status-updated');
                    }, 500);
                    
                } else {
                    showToast(data.message || 'Failed to update status.', 'error');
                    // Revert selector to previous value
                    selector.value = selector.dataset.previousValue || 'submitted';
                }
            })
            .catch(err => {
                console.error(err);
                showToast('A network error occurred.', 'error');
                selector.value = selector.dataset.previousValue || 'submitted';
            })
            .finally(() => {
                // Restore card state
                card.style.opacity = '1';
                this.disabled = false;
            });
        });

        // Store previous value for error handling
        selector.addEventListener('focus', function() {
            this.dataset.previousValue = this.value;
        });
    });

    function getStatusIcon(status) {
        const icons = {
            'submitted': 'fas fa-paper-plane',
            'viewed': 'fas fa-eye',
            'interviewing': 'fas fa-comments',
            'rejected': 'fas fa-times-circle',
            'hired': 'fas fa-check-circle'
        };
        return icons[status] || 'fas fa-question-circle';
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>