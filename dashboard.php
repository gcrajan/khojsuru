<?php
$page_title = "My Dashboard: Khojsuru";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Fetch user profile
$user_stmt = $pdo->prepare("SELECT name, headline, location, phone, email, profile_image, skills_cache FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();
$profile_avatar_url = BASE_URL . ($user['profile_image'] ?? 'assets/images/default-avatar.png');

// Pagination setup
$per_page = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

$my_cvs = [];
$total_cvs = 0;
$my_jobs = [];
$total_jobs = 0;
$company = null;

if ($user_type === 'recruitee') {
    // Total CVs count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM cvs WHERE user_id = ?");
    $count_stmt->execute([$user_id]);
    $total_cvs = $count_stmt->fetchColumn();

    // Fetch CVs for current page
    $cvs_stmt = $pdo->prepare("SELECT id, title, updated_at, is_public FROM cvs WHERE user_id = ? ORDER BY updated_at DESC LIMIT ? OFFSET ?");
    $cvs_stmt->execute([$user_id, $per_page, $offset]);
    $my_cvs = $cvs_stmt->fetchAll();

    $total_pages = ceil($total_cvs / $per_page);

} else { // recruiter
    $company_stmt = $pdo->prepare("SELECT * FROM companies WHERE created_by_user_id = ? LIMIT 1");
    $company_stmt->execute([$user_id]);
    $company = $company_stmt->fetch();

    // Fetch ACTIVE jobs (deadline is in the future AND is_active = 1)
    $active_jobs_stmt = $pdo->prepare("
        SELECT j.id, j.title, j.deadline, COUNT(a.id) as applicant_count
        FROM jobs j LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.recruiter_user_id = ? AND j.is_active = 1 AND j.deadline > UTC_TIMESTAMP()
        GROUP BY j.id ORDER BY j.deadline ASC
    ");
    $active_jobs_stmt->execute([$user_id]);
    $active_jobs = $active_jobs_stmt->fetchAll();

    // Fetch EXPIRED & INACTIVE jobs (deadline has passed OR is_active = 0)
    $expired_jobs_stmt = $pdo->prepare("
        SELECT j.id, j.title, j.is_active, j.deadline, COUNT(a.id) as applicant_count
        FROM jobs j LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.recruiter_user_id = ? AND (j.is_active = 0 OR j.deadline <= UTC_TIMESTAMP())
        GROUP BY j.id ORDER BY j.posted_at DESC
    ");
    $expired_jobs_stmt->execute([$user_id]);
    $expired_jobs = $expired_jobs_stmt->fetchAll();
}
?>


<style>
    .profile-layout {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .profile-card, .content-card {
        background: var(--secondary-bg);
        padding: 1.5rem;
        border-radius: 12px;
        height: fit-content;
    }
    .profile-card h2, .content-card h2 { margin-top: 0; }
    
    .avatar-upload-area { text-align: center; margin-bottom: 1.5rem; }
    #avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-color); cursor: pointer; background: white; }
    #profile_image_input { display: none; }
    .tabs-nav { display: flex; border-bottom: 2px solid var(--border-color); margin-bottom: 1.5rem; }
    .tab-link { padding: 0.75rem 1.5rem; cursor: pointer; color: var(--text-secondary); font-weight: 500; }
    .tab-link.active { color: var(--accent-color); border-bottom: 2px solid var(--accent-color); margin-bottom: -2px; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* List for CVs or Jobs */
    .item-list .item {
        display: flex; flex-direction: column; gap: 0.5rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .item-list .item:last-child { border-bottom: none; }
    .item-info { flex-grow: 1; }
    .item-title { font-weight: 500;
    font-size: 1.05rem;
    margin-bottom: 0.3rem;}
    .item-title-span { font-size: 0.8em; color: var(--text-secondary); }
    .item-meta { font-size: 0.9em; color: var(--text-secondary); }
    .item-actions { display: flex; gap: 1rem; margin-top: 0.5rem; }
    .item-actions a { color: var(--accent-color); font-weight: 500; }

    .empty-state { text-align: center; padding: 2rem; color: var(--text-secondary); }
    .recruitee-header >p{font-size: 1.1rem; color: var(--text-secondary); margin: 0 auto 1rem; text-align:center;}
    .cta-buttons{    display: flex; justify-content: center; gap: 1rem;  flex-wrap: wrap; padding-bottom: 1rem; border-bottom: 1px solid #75787d; margin-bottom: 1.5rem;}
    .add-new-btn { padding: 0.8rem; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: transform 0.3s ease;}
    .add-new-btn:hover { transform: translateY(-5px);}
    .btn-color{ background: var(--accent-color);}
    .btn-manual{ background: #535e71;}

    /* Desktop Layout */
    @media (min-width: 992px) {
        .profile-layout { grid-template-columns: 350px 1fr; }
        .item-list .item { flex-direction: row; align-items: center; }
        .item-actions { margin-top: 0; }
    }
    .item-actions a, .item-actions button {
        color: var(--accent-color);
        font-weight: 500;
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 1em;
    }
    .item-actions .delete-btn { color: var(--error-color); }

    /* Privacy Toggle Switch */
    .privacy-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9em;
    }
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--success-color); }
    input:checked + .slider:before { transform: translateX(20px); }

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
    #btn-confirm-delete { background: var(--error-color); color: white; }
    .red-notice{font-size: small; color: #cb6767;}
    #company-logo-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--accent-color);
        cursor: pointer;
        background: white;
    }
    .skills-summary-box, .company-profile-box {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    .talent-skills {
        display: flex; flex-wrap: wrap; gap: 0.5rem;
    }
    .skill-tag {
        background: var(--primary-bg); color: var(--text-secondary);
        padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem;
    }
    @media (max-width: 480px) {
        .profile-layout {padding:0rem;}
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
</style>

<div class="profile-layout">
    <aside class="profile-card">
        <h2>My Profile</h2>
        <form id="profile-form" enctype="multipart/form-data">
            <div class="avatar-upload-area">
                <label for="profile_image_input">
                    <img src="<?php echo $profile_avatar_url; ?>" alt="Your Avatar" id="avatar-preview" title="Change Profile Picture">
                </label>
                <input type="file" name="profile_image" id="profile_image_input" accept="image/*">
            </div>
            <div class="form-group"><label for="name">Full Name</label><input type="text" name="name"id="name" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
            <div class="form-group"><label for="headline">Short Bio</label><input type="text" name="headline" id="headline" class="form-input" value="<?php echo htmlspecialchars($user['headline'] ?? ''); ?>"></div>
            <div class="form-group"><label for="location">Location</label><input type="text" name="location" id="location" class="form-input" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"></div>
            <div class="form-group"><label for="phone">Phone</label><input type="tel" name="phone" id="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"></div>

            <!-- RECRUITEE-ONLY SKILLS FIELD -->
            <?php if ($user_type === 'recruitee'): ?>
            <div class="form-group">
                <label for="skills_cache">My Top Skills (comma-separated)</label>
                <textarea name="skills_cache" id="skills_cache" class="form-input" rows="3"><?php echo htmlspecialchars($user['skills_cache'] ?? ''); ?></textarea>
            </div>
            <?php endif; ?>

            <button type="submit" id="save-profile-btn" class="btn-submit">Save Profile Changes</button>
        </form>

        <!-- Recruiter Company Form -->
        <?php if ($user_type === 'recruiter' && $company): ?>
            <hr style="border-color: var(--border-color); margin: 2rem 0;">
            <h2>Company Profile</h2>
            <form id="company-form" enctype="multipart/form-data">
                <input type="hidden" name="company_id" value="<?php echo $company['id']; ?>">
                <div class="avatar-upload-area">
                    <label for="company_logo_input">
                        <img src="<?php echo BASE_URL . ($company['logo'] ?? 'assets/images/default-avatar.png'); ?>" alt="Company Logo" id="company-logo-preview" title="Change Company Logo">
                    </label>
                    <input type="file" name="logo" id="company_logo_input" accept="image/*" style="display:none;">
                </div>
                <div class="form-group"><label for="company_name">Company Name</label><input type="text" id="company_name" name="company_name" class="form-input" value="<?php echo htmlspecialchars($company['name']); ?>"></div>
                <div class="form-group"><label for="company_website">Company Website</label><input type="url" id="company_website" name="company_website" class="form-input" value="<?php echo htmlspecialchars($company['website']); ?>"></div>
                <div class="form-group"><label for="about">About Company</label><textarea id="about" name="about" class="form-input" rows="4"><?php echo htmlspecialchars($company['about']); ?></textarea></div>
                <button type="submit" class="btn-submit">Save Company Info</button>
            </form>
        <?php endif; ?>
    </aside>

    <section class="content-card">
        <?php if ($user_type === 'recruitee'): ?>
            <!-- RECRUITEE'S DASHBOARD -->
            <div class="recruitee-header">
                <p>Create a professional CV with our powerful tools.</p>
                <div class="cta-buttons">
                    <a href="create_ai.php" class="add-new-btn btn-color"><i class="fas fa-magic"></i> Create With AI</a>
                    <a href="create_manual.php" class="add-new-btn btn-manual"><i class="fas fa-pen-nib"></i> Create Manually</a>
                </div>
            </div>
            <h2>My CVs <span class="red-notice">(Please, make the CV status public to share and apply.)</span> </h2>
            <div class="item-list">
                <?php if (empty($my_cvs)): ?>
                    <p class="empty-state">You haven't created any CVs yet.</p>
                <?php else: ?>
                    <?php foreach ($my_cvs as $cv): ?>
                        <div class="item" data-cv-id="<?php echo $cv['id']; ?>">
                            <div class="item-info">
                                <div class="item-title"><?php echo htmlspecialchars($cv['title']); ?></div>
                                <div class="item-meta">Last updated: <?php echo date('M d, Y', strtotime($cv['updated_at'])); ?></div>
                            </div>
                            <div class="item-actions">
                            <div class="privacy-toggle">
                                <label class="switch">
                                    <input type="checkbox" class="privacy-switch" <?php echo $cv['is_public'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="privacy-status"><?php echo $cv['is_public'] ? 'Public' : 'Private'; ?></span>
                            </div>
                                <a href="edit.php?id=<?php echo $cv['id']; ?>">Edit</a>
                                <a href="view_cv.php?id=<?php echo $cv['id']; ?>" target="_blank">View</a>
                                <button class="delete-btn" data-type="cv" data-id="<?php echo $cv['id']; ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <!-- Previous -->
                <a href="?page=<?php echo max(1, $page - 1); ?>" class="pagination-link <?php echo $page == 1 ? 'disabled' : ''; ?>">&laquo; Prev</a>

                <!-- Page numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <!-- Next -->
                <a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="pagination-link <?php echo $page == $total_pages ? 'disabled' : ''; ?>">Next &raquo;</a>
            </div>
            <?php endif; ?>

        <?php else: // RECRUITER'S DASHBOARD ?>
            <div class="recruitee-header">
                <p>Create new jobs to attract qualified talent.</p>
                <div class="cta-buttons">
                    <a href="post_job.php" class="add-new-btn btn-color">+ Post New Job</a>
                </div>
            </div>
            <h2>My Job Postings</h2>
            <div class="tabs-nav">
                <div class="tab-link active" data-tab="active-jobs">Active (<?php echo count($active_jobs); ?>)</div>
                <div class="tab-link" data-tab="expired-jobs">Expired & Inactive (<?php echo count($expired_jobs); ?>)</div>
            </div>

            <div id="active-jobs" class="tab-content active">
                <div class="item-list">
                    <?php foreach ($active_jobs as $job): ?>
                    <div class="item" data-job-id="<?php echo $job['id']; ?>">
                        <div class="item-info">
                            <div class="item-title"><?php echo htmlspecialchars($job['title']); ?> <span class="item-title-span">(<?php echo $job['applicant_count']; ?> applicants)</span></div>
                            <div class="item-meta">Closes in: <span class="countdown-timer" data-deadline="<?php echo $job['deadline']; ?>"></span></div>
                        </div>
                        <div class="item-actions">
                             <a href="applicants.php?job_id=<?php echo $job['id']; ?>">Applicants</a>
                             <a href="edit_job.php?id=<?php echo $job['id']; ?>">Edit</a>
                             <button class="delete-btn" data-type="job" data-id="<?php echo $job['id']; ?>"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="expired-jobs" class="tab-content">
                <div class="item-list">
                    <?php foreach ($expired_jobs as $job): ?>
                     <div class="item" data-job-id="<?php echo $job['id']; ?>">
                        <div class="item-info">
                            <div class="item-title"><?php echo htmlspecialchars($job['title']); ?> <span class="item-title-span">(<?php echo $job['applicant_count']; ?> applicants)</span></div>
                            <div class="item-meta">Status: <?php echo $job['is_active'] ? 'Expired' : 'Inactive'; ?></div>
                        </div>
                         <div class="item-actions">
                             <a href="applicants.php?job_id=<?php echo $job['id']; ?>">Applicants</a>
                             <button class="delete-btn" data-type="job" data-id="<?php echo $job['id']; ?>"><i class="fas fa-trash"></i></button>
                         </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- Reusable Confirmation Modal -->
<div class="confirm-modal-overlay" id="confirm-modal">
    <div class="confirm-modal">
        <h3 id="confirm-title">Are you sure?</h3>
        <p id="confirm-text">This action cannot be undone.</p>
        <div class="confirm-actions">
            <button id="btn-cancel-delete" class="btn-submit btn-manual">Cancel</button>
            <button id="btn-confirm-delete" class="btn-submit">Yes, Delete</button>
        </div>
    </div>
</div>

<?php 
include_once __DIR__ . '/includes/footer.php'; 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // job active / inactive 
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            tabLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            tabContents.forEach(content => {
                content.id === tabId ? content.classList.add('active') : content.classList.remove('active');
            });
        });
    });
    // --- Live Avatar Preview ---
    const avatarInput = document.getElementById('profile_image_input');
    const avatarPreview = document.getElementById('avatar-preview');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }

    // --- NEW: Live Company Logo Preview ---
    const logoInput = document.getElementById('company_logo_input');
    const logoPreview = document.getElementById('company-logo-preview');
    if (logoInput) {
        logoInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { logoPreview.src = e.target.result; }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }

    // --- AJAX Profile Update ---
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        const saveBtn = document.getElementById('save-profile-btn');
        profileForm.addEventListener('submit', function(event) {
            event.preventDefault();
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            const formData = new FormData(profileForm);
            
            fetch('<?php echo BASE_URL; ?>api.php?action=update_profile', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                    if (data.new_image_url) {
                        document.querySelectorAll('.header-avatar, .mobile-nav-avatar').forEach(img => {
                            img.src = data.new_image_url;
                        });
                    }
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(error => {
                showToast('A network error occurred.', 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Changes';
            });
        });
    }

    // --- Delete and Privacy Toggle Logic ---
    const contentCard = document.querySelector('.content-card');
    const confirmModal = document.getElementById('confirm-modal');
    let deleteEndpoint = '';
    let elementToRemove = null;

    if (contentCard) {
        contentCard.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) {
                const button = e.target.closest('.delete-btn');
                const type = button.dataset.type;
                const id = button.dataset.id;

                if (type === 'cv') {
                    document.getElementById('confirm-text').textContent = 'This CV and all its data will be permanently deleted.';
                    deleteEndpoint = 'api.php?action=delete_cv';
                    elementToRemove = document.querySelector(`.item[data-cv-id="${id}"]`);
                } else if (type === 'job') {
                    document.getElementById('confirm-text').textContent = 'This job posting and all its applications will be permanently deleted.';
                    deleteEndpoint = 'api.php?action=delete_job';
                    elementToRemove = document.querySelector(`.item[data-job-id="${id}"]`);
                }
                
                const formData = new FormData();
                formData.append(type + '_id', id);                 
                document.getElementById('btn-confirm-delete').onclick = () => {
                    fetch(deleteEndpoint, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Item deleted successfully.', 'success');
                            if (elementToRemove) elementToRemove.remove();
                        } else {
                            showToast(data.message || 'Failed to delete item.', 'error');
                        }
                    })
                    .finally(() => confirmModal.classList.remove('active'));
                };

                confirmModal.classList.add('active');
            }
        });

        contentCard.querySelectorAll('.privacy-switch').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const cvId = this.closest('.item').dataset.cvId;
                const statusSpan = this.closest('.privacy-toggle').querySelector('.privacy-status');
                const formData = new FormData();
                formData.append('cv_id', cvId);

                fetch('api.php?action=toggle_cv_privacy', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        statusSpan.textContent = data.is_public ? 'Public' : 'Private';
                        showToast('Privacy status updated.', 'success');
                    } else {
                        this.checked = !this.checked;
                        showToast('Failed to update status.', 'error');
                    }
                });
            });
        });
    }

    // --- Modal Cancel Button ---
    if (confirmModal) {
        document.getElementById('btn-cancel-delete').addEventListener('click', () => {
            confirmModal.classList.remove('active');
        });
    }

    // --- AJAX for Company Profile Update ---
    const companyForm = document.getElementById('company-form');
    if (companyForm) {
        companyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            
            const formData = new FormData(this);

            fetch('api.php?action=update_company_profile', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Company profile updated!', 'success');
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Company Info';
            });
        });
    }
});
</script>