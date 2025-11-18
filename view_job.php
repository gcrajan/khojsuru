<?php
    $page_title = "View Job: Khojsuru";

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/includes/header.php';

    $job_id = (int)($_GET['id'] ?? 0);
    if ($job_id === 0) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }

    $stmt = $pdo->prepare(
        "SELECT 
            j.*, 
            c.name as company_name, c.website as company_website, c.logo as company_logo, c.about as company_about,
            u.name as recruiter_name, u.profile_image as recruiter_avatar
        FROM jobs j
        JOIN companies c ON j.company_id = c.id
        JOIN users u ON j.recruiter_user_id = u.id
        WHERE j.id = ?"
    );
    $stmt->execute([$job_id]);
    $job = $stmt->fetch();
    if (!$job) { die("Job not found."); }

    $job_poster_id = $job['recruiter_user_id'];
    $page_title = htmlspecialchars($job['title']);

        $user_cvs = [];
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'recruitee') {
            $cv_stmt = $pdo->prepare("SELECT id, title FROM cvs WHERE user_id = ? AND is_public = 1");
            $cv_stmt->execute([$_SESSION['user_id']]);
            $user_cvs = $cv_stmt->fetchAll();
        }

        // Get Like Count
        $likes_stmt = $pdo->prepare("SELECT COUNT(*) FROM job_likes WHERE job_id = ?");
        $likes_stmt->execute([$job_id]);
        $like_count = $likes_stmt->fetchColumn();

        // Check if the current user has liked this job
        $user_has_liked = false;
        if (isset($_SESSION['user_id'])) {
            $user_like_stmt = $pdo->prepare("SELECT job_id FROM job_likes WHERE job_id = ? AND user_id = ?");
            $user_like_stmt->execute([$job_id, $_SESSION['user_id']]);
            $user_has_liked = $user_like_stmt->fetch() ? true : false;
        }

        // Fetch and organize comments
        $comments_stmt = $pdo->prepare(
            "SELECT c.*, u.name as user_name, u.profile_image, u.user_type
            FROM job_comments c JOIN users u ON c.user_id = u.id
            WHERE c.job_id = ? ORDER BY c.created_at ASC"
        );
        $comments_stmt->execute([$job_id]);
        $all_comments = $comments_stmt->fetchAll();

        $comments_threaded = [];
        foreach ($all_comments as $comment) {
            if ($comment['parent_comment_id'] === null) {
                $comments_threaded[$comment['id']] = $comment;
                $comments_threaded[$comment['id']]['replies'] = [];
            } else {
                if (isset($comments_threaded[$comment['parent_comment_id']])) {
                    $comments_threaded[$comment['parent_comment_id']]['replies'][] = $comment;
                }
            }
        }
        $comments_threaded = array_reverse($comments_threaded);
?>

<style>
    .job-view-container {
        max-width: 1200px;
        margin: 2rem auto;
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        align-items: flex-start;
    }

    .job-main-content {
        background: var(--secondary-bg);
        border-radius: 12px;
        padding: 1.5rem;
    }
    .job-header{position:relative;}    
    .countdown-timer-div i{ color: var(--accent-color);}
    .countdown-timer-div { position: absolute;
    right: 0px;
    top: 0px;
    font-size: 1rem;
    font-weight: 600;}
    .job-header h1 { font-size: 2.5rem; margin: 0 0 0.5rem; }
    .job-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-secondary);
        font-size: small;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .job-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .job-meta-item i {
        color: var(--accent-color);
    }

    .job-meta-divider {
        width: 4px;
        height: 4px;
        background: var(--text-secondary);
        border-radius: 50%;
        opacity: 0.5;
    }
    /* .job-description h3 { font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; } */
    .job-description { line-height: 1.7; }
    /* .job-details{min-height:8rem} */
    .job-details {
        /* max-height: 400px; */
        min-height:8rem
        overflow: auto;
    }

    /* Ensure images don't overflow */
    .job-details img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    /* --- Right Column: Sidebar --- */
    .job-sidebar {
        background: var(--secondary-bg);
        border-radius: 12px;
        padding: 1.5rem;
        height: fit-content;
    }
    .recruiter-card {
        text-align: center;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    .recruiter-card h1 {     color: var(--text-secondary); font-size: 1.3rem; }
    .recruiter-avatar {width: 80px; height: 80px; border-radius: 50%; object-fit: contain; margin: 0 auto 1rem; background: white; border: 1px solid var(--text-secondary); }
    .recruiter-card h3 { margin: 0rem 0rem 1rem 0rem; }
    .recruiter-card a {font-size: 0.9em;}
    .company-card { text-align: center; }
    .company-logo { width: 80px; height: 80px; border-radius: 50%; object-fit: contain; margin: 0 auto 1rem; background: white; border: 1px solid var(--text-secondary);}
    .company-card h1 {     color: var(--text-secondary); font-size: 1.3rem; }
    .company-card h3 { margin: 0rem 0rem 1rem 0rem; }
    .company-card p { margin: 0rem 0rem 1rem 0rem; color: var(--text-secondary);     font-size: small; }
    .company-card a { font-size: 0.9em; }
    .apply-btn { width: 100%; text-align: center; margin-top: 1.5rem; }

    /* --- Social & Comments Section --- */
    .job-section { margin-top: 2.5rem; }
    .social-actions { display: flex; align-items: center; flex-wrap:wrap; border-top: 1px solid var(--border-color); padding-top: 1rem; }
    .like-btn, .share-btn {
        background: #ffffff00; border: none; color: var(--text-secondary);
        padding: 0.5rem 1rem; cursor: pointer; font-weight: 500;
        display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease; font-size:1rem;
    }
    .comment-title{color: var(--text-secondary);
    font-size: 1rem; padding: 0.5rem 1rem; font-weight: 500; margin: 0rem;}
    .like-btn:hover, .share-btn:hover, .comment-title:hover { color: var(--text-primary); }
    .like-btn.active{ color: var(--accent-color); }
    @keyframes pop {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.4);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes burst {
        0% {
            opacity: 0.8;
            transform: scale(0.5);
        }
        100% {
            opacity: 0;
            transform: scale(2.5);
        }
    }

    .like-btn.animated i {
        animation: pop 0.3s ease-in-out;
    }

    .burst-effect {
        position: absolute;
        width: 20px;
        height: 20px;
        background: var(--accent-color);
        border-radius: 50%;
        z-index: 1;
        animation: burst 0.5s ease-out forwards;
        pointer-events: none;
    }

    .comment-form{
        background: var(--primary-bg);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem;
        transition: border-color 0.3s ease;
    }
    .comment-form:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .comments-section { margin-top: 1rem; }
    .comment-form textarea { 
        background: transparent;
        border: none;
        width: 100%;
        min-height: 100px;
        resize: vertical;
        color: var(--text-primary);
        font-size: 1rem;
        outline: none; 
    }
    .comment-list { margin-top: 2rem; }
    .comment-item { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .comment-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .comment-content { flex: 1; }
    .comment-content strong { font-size: 1rem;     color: var(--text-secondary);}
    .comment-content p { margin: 0.25rem 0; line-height: 1.6; }
    .comment-actions { font-size: 0.8em; margin-top: 0.5rem; }
    .comment-date { color: var(--text-secondary); }
    .comment-form button {
        background: linear-gradient(135deg, var(--accent-color), #8b5cf6);
        color: white;
        border: none;
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: fit-content;
    }
    .comment-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
    }
    .comment-actions button {
        background: none; border: none; cursor: pointer; font-size: 0.8em; font-weight: 500; padding: 0 4px;
    }
    .edit-comment-form{margin-top:5px}
    .edit-comment-btn{color: var(--success-color);}
    .delete-comment-btn{color: #ff7474;}
    .reply-btn{color: var(--accent-color);}
    .comment-actions button:hover { color: var(--text-secondary); text-decoration: underline; }
    .edit-comment-form textarea { min-height: 80px; margin-bottom: 3px; }
    .edit-comment-form .btn-secondary {background: var(--error-color);
    color: white;}
    .edit-comment-form-btns{    display: flex; gap: 1rem; align-items: center;}
    .edit-comment-form button {     font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    border: none;
    width: fit-content;}
    .reply-btn { background: none; border: none; color: var(--accent-color); cursor: pointer; font-weight: bold; padding: 0; }
    .reply-btn:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }
    .reply-form { 
        display: none;
        margin-left: 3rem;
        margin-top: 1rem;
        background: var(--secondary-bg);
        border-radius: 12px;
        padding: 0.5rem;
        border: 1px solid var(--border-color);
    }
    .reply-form.active { display: block; }
    .comment-replies { margin-left: 50px; border-left: 2px solid var(--border-color); border-radius: 0px 1rem; padding-left: 1rem; margin-top: 1rem; }
    
    /* --- Modal Styles --- */
    #apply-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 2000; display: none; align-items: center; justify-content: center; }
    #apply-modal.active { display: flex; }
    .modal-content { background: var(--secondary-bg); padding: 1rem; border-radius: 12px; max-width: 500px; width: 90%; margin: 0rem 1rem; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .close-modal { background:none; border:none; color: var(--text-primary); font-size: 1.5rem; cursor: pointer; }
    .apply-option { margin-bottom: 1rem; }
    .job-description i,.section-title i {
        color: var(--accent-color);
        font-size: 1.3rem;
    }
    /* --- Modal Styles --- */
    #delete-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(5px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    #delete-modal.active {
        display: flex;
    }

    .modal-content-delete {
        background: var(--secondary-bg);
        border-radius: 16px;
        padding: 2rem;
        width: 90%;
        max-width: 400px;
        text-align: center;
    }

    .modal-header-delete {text-align:center; width: -webkit-fill-available; margin: 2rem 0 0 0;}

    .modal-body p{text-align: center; padding-bottom: 1rem;}

    .close-modal {
        background: none;
        border: none;
        color: var(--text-primary);
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        gap: 1rem;
        padding: 0 1rem 1rem 1rem;
    }

    #confirm-delete-btn {
        background-color: #f44336;
        color: white;
        flex: 1;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    #cancel-delete-btn {
        flex: 1;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        background: #535e71;
        color: white;
    }

    /* --- Desktop Layout --- */
    @media (min-width: 992px) {
        .job-view-container { grid-template-columns: 340px 1fr; }
        .job-main-content { padding: 2.5rem; }
        .job-sidebar { position: sticky; top: 100px; }
    }
    @media (max-width: 768px) {
        .job-meta {
            gap: 0.8rem;
        }
        .comment-replies {
            margin-left: 5px;
            padding-left: 0.5rem;
            margin-top: 0.5rem;
        }
        .reply-form{
            margin-left: 0rem;
            margin-top: 0rem;
        }
    }
    @media (max-width: 480px) {
        .job-view-container {margin:0rem;}
        .job-main-content { padding: 1rem;}
        .social-actions {gap: 0.75rem;}
        .like-btn, .share-btn, .comment-title{padding: 0rem; font-size: 0.85rem;}
        .comment-form { border-radius: 10px; padding: 0.5rem;}
        .form-input {padding: 0.25rem 0.5rem;}
        .comment-form textarea {font-size: 0.9rem;}
    }
</style>

<div class="job-view-container">

    <!-- Right Column: Sidebar -->
    <aside class="job-sidebar">
        <div class="recruiter-card">
            <h1>Posted By</h1>
            <img src="<?php echo BASE_URL . ($job['recruiter_avatar'] ?? 'assets/images/default-avatar.png'); ?>" alt="Recruiter Avatar" class="recruiter-avatar">
            <h3><?php echo htmlspecialchars($job['recruiter_name']); ?></h3>
            <a href="profile.php?id=<?php echo $job_poster_id; ?>">View Profile</a>
        </div>
        <div class="company-card">
            <h1>Company details</h1>
            <img src="<?php echo BASE_URL . ($job['company_logo'] ?? 'assets/images/default-avatar.png'); ?>" alt="Company Logo" class="company-logo">
            <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
            <?php if (!empty($job['company_about'])): ?>
                <p class="card-description"><?php echo htmlspecialchars(substr($job['company_about'], 0, 120)); ?><?php echo strlen($job['company_about']) > 120 ? '...' : ''; ?></p>
            <?php endif; ?>
            <?php if (!empty($job['company_website'])): ?>
                <a href="<?php echo htmlspecialchars($job['company_website']); ?>" target="_blank" rel="noopener noreferrer">Visit Website</a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'recruitee'): ?>
            <button id="open-apply-modal" class="btn-submit apply-btn">Apply Now</button>
        <?php endif; ?>
    </aside>
    
    <!-- Left Column: Main Content -->
    <div class="job-main-content">
        <div class="job-header">
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <div class="countdown-timer-div">
                <i class="fas fa-clock"></i> <span class="countdown-timer" data-deadline="<?php echo $job['deadline']; ?>"></span>
            </div>
            <div class="job-meta">
                <div class="job-meta-item">
                    <i class="fas fa-building"></i>
                    <span><?php echo htmlspecialchars($job['company_name']); ?></span>
                </div>
                <div class="job-meta-divider"></div>
                <div class="job-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($job['location']); ?></span>
                </div>
                <div class="job-meta-divider"></div>
                <div class="job-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Posted <?php echo date('M d, Y', strtotime($job['posted_at'])); ?></span>
                </div>
            </div>
        </div>
        <div class="job-description">
            <!-- <h3> Job Description</h3> -->
            <div class="job-details">
                <?php echo $job['description']; ?>
            </div>
        </div>

        <div class="job-section">
            <div class="social-actions">
                <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'recruitee'): ?>
                    <button class="like-btn <?php echo $user_has_liked ? 'active' : ''; ?>" id="like-btn" data-job-id="<?php echo $job_id; ?>">
                        <i class="fas fa-thumbs-up"></i>
                        <span id="like-count"><?php echo $like_count; ?></span> Likes
                    </button>
                <?php else: ?>
                    <div class="like-btn" style="cursor: default; pointer-events: none;">
                        <i class="fas fa-thumbs-up"></i>
                        <span id="like-count"><?php echo $like_count; ?></span> Likes
                    </div>
                <?php endif; ?>
                <button class="share-btn"><i class="fas fa-share"></i> Share</button>
                <h3 class="comment-title"><i class="fas fa-comment"></i> Comments (<?php echo count($all_comments); ?>)</h3>
            </div>
            <div class="comments-section">
                <form id="comment-form" class="comment-form" method="POST">
                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                    <textarea name="comment_text" class="form-input" placeholder="Ask a question or leave a comment..." required></textarea>
                    <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Post Comment</button>
                </form>
                <div class="comment-list" id="comment-list">
                    <?php foreach($comments_threaded as $comment): ?>
                        <div class="comment-item" id="comment-<?php echo $comment['id']; ?>">
                             <img src="<?php echo BASE_URL . ($comment['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" class="comment-avatar">
                            <div class="comment-content">
                             <a href="profile.php?id=<?php echo ($comment['user_id']); ?>">
                                <strong><?php echo htmlspecialchars($comment['user_name']); ?></strong>
                            </a>

                            <div class="comment-body">
                                <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                
                                <!-- Hidden Edit Form -->
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                                <form class="edit-comment-form" style="display:none;" data-comment-id="<?php echo $comment['id']; ?>">
                                    <textarea name="comment_text" class="form-input" required><?php echo htmlspecialchars($comment['comment_text']); ?></textarea>
                                    <div class="edit-comment-form-btns">
                                        <button type="submit" class="btn-submit">Save</button>
                                        <button type="button" class="cancel-edit-btn btn-secondary">Cancel</button>
                                    </div>
                                </form>
                                <?php endif; ?>
                            </div>
                                <!-- <p><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p> -->
                                <div class="comment-actions">
                                    <small class="comment-date"><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></small>
                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                                        &middot; <button class="edit-comment-btn" data-comment-id="<?php echo $comment['id']; ?>">Edit</button>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $comment['user_id'] || $_SESSION['user_type'] === 'admin')): ?>
                                        &middot; <button class="delete-comment-btn" data-comment-id="<?php echo $comment['id']; ?>">Delete</button>
                                    <?php endif; ?>
                                    <!-- <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $job_poster_id): ?>
                                    &middot; <button class="reply-btn" data-comment-id="<?php echo $comment['id']; ?>">Reply</button>
                                    <?php endif; ?> -->
                                    &middot; <button class="reply-btn" data-comment-id="<?php echo $comment['id']; ?>">Reply?</button>
                                </div>
                                <div class="comment-replies">
                                    <?php foreach ($comment['replies'] as $reply): ?>
                                        <div class="comment-item" id="comment-<?php echo $reply['id']; ?>">
                                            <img src="<?php echo BASE_URL . ($reply['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" class="comment-avatar">
                                            <div class="comment-content">
                                                <a href="profile.php?id=<?php echo ($reply['user_id']); ?>">
                                                    <strong><?php echo htmlspecialchars($reply['user_name']); ?></strong>
                                                </a>

                                                <div class="comment-body">
                                                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($reply['comment_text'])); ?></p>
                                                    
                                                    <!-- Hidden Edit Form for the Reply -->
                                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $reply['user_id']): ?>
                                                    <form class="edit-comment-form" style="display:none;" data-comment-id="<?php echo $reply['id']; ?>">
                                                        <textarea name="comment_text" class="form-input" required><?php echo htmlspecialchars($reply['comment_text']); ?></textarea>
                                                        <div class="edit-comment-form-btns">
                                                            <button type="submit" class="btn-submit">Save</button>
                                                            <button type="button" class="cancel-edit-btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </form>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="comment-actions">
                                                    <small class="comment-date"><?php echo date('M d, Y', strtotime($reply['created_at'])); ?></small>
                                                    
                                                    <!-- Conditional Edit/Delete Buttons for the Reply -->
                                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $reply['user_id']): ?>
                                                        &middot; <button class="edit-comment-btn" data-comment-id="<?php echo $reply['id']; ?>">Edit</button>
                                                    <?php endif; ?>
                                                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] === $reply['user_id'] || $_SESSION['user_type'] === 'admin')): ?>
                                                        &middot; <button class="delete-comment-btn" data-comment-id="<?php echo $reply['id']; ?>">Delete</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $job_poster_id): ?>
                                <form class="reply-form" data-parent-id="<?php echo $comment['id']; ?>" method="POST">
                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                    <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                    <textarea name="comment_text" class="form-input" placeholder="Write a reply..." required rows="2"></textarea>
                                    <button type="submit" class="btn-submit" style="margin-top:0.5rem; padding: 0.4rem 0.8rem;">Submit Reply</button>
                                </form>
                                <?php endif; ?> -->
                                <form class="reply-form" data-parent-id="<?php echo $comment['id']; ?>" method="POST">
                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                    <input type="hidden" name="parent_comment_id" value="<?php echo $comment['id']; ?>">
                                    <textarea name="comment_text" class="form-input" placeholder="Write a reply..." required rows="2"></textarea>
                                    <button type="submit" class="btn-submit" style="margin-top:0.5rem; padding: 0.4rem 0.8rem;">Submit Reply</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Application Modal -->
<div id="apply-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Apply for: <?php echo htmlspecialchars($job['title']); ?></h2>
            <button id="close-apply-modal" class="close-modal">&times;</button>
        </div>
        <form id="application-form" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
            <div class="apply-option">
                <label>Option 1: Choose a public CV from your profile</label>
                <select name="cv_id" class="form-input" style="margin-top: 0.5rem;">
                    <option value="">-- Select a CV --</option>
                    <?php foreach($user_cvs as $cv): ?>
                        <option value="<?php echo $cv['id']; ?>"><?php echo htmlspecialchars($cv['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <p style="text-align:center; color: var(--text-secondary);">- OR -</p>
             <div class="apply-option">
                <label for="uploaded_cv">Option 2: Upload a new CV (PDF only)</label>
                <input type="file" id="uploaded_cv" name="uploaded_cv" class="form-input" accept=".pdf" style="margin-top: 0.5rem;">
            </div>
            <button type="submit" id="submit-application-btn" class="btn-submit" style="width: 100%; margin-top: 1.5rem;">Submit Application</button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal">
    <div class="modal-content-delete">
        <div class="modal-header">
            <h3 class="modal-header-delete">Are you sure?</h3>
        </div>
        <div class="modal-body">
            <p>This comment posting and all its replies will be permanently deleted.</p>
        </div>
        <div class="modal-footer">
            <button id="confirm-delete-btn" class="btn-submit">Delete</button>
            <button id="cancel-delete-btn" class="btn-secondary">Cancel</button>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Modal Control ---
    const modal = document.getElementById('apply-modal');
    const openBtn = document.getElementById('open-apply-modal');
    if (openBtn) {
        const closeBtn = document.getElementById('close-apply-modal');
        openBtn.addEventListener('click', () => modal.classList.add('active'));
        closeBtn.addEventListener('click', () => modal.classList.remove('active'));
    }

    // --- Application Form Submission ---
    const appForm = document.getElementById('application-form');
    if (appForm) {
        const submitBtn = document.getElementById('submit-application-btn');
        appForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            const formData = new FormData(appForm);
            fetch('<?php echo BASE_URL; ?>api.php?action=submit_application', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Application submitted successfully!', 'success');
                    modal.classList.remove('active');
                    openBtn.textContent = 'Applied';
                    openBtn.disabled = true;
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Application';
                }
            })
            .catch(err => {
                showToast('A network error occurred.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Application';
            });
        });
    }

    // --- Like Button Logic ---
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        const likeCountSpan = document.getElementById('like-count');
        likeBtn.addEventListener('click', function () {
            const jobId = this.dataset.jobId;
            const formData = new FormData();
            formData.append('job_id', jobId);
            const icon = this.querySelector('i');

            // Animation: Add "animated" class
            this.classList.add('animated');

            // Optional: Create burst effect
            const burst = document.createElement('span');
            burst.classList.add('burst-effect');
            const rect = this.getBoundingClientRect();
            burst.style.left = `${this.offsetWidth / 2 - 10}px`;
            burst.style.top = `${this.offsetHeight / 2 - 10}px`;
            this.appendChild(burst);

            setTimeout(() => {
                this.classList.remove('animated');
                burst.remove();
            }, 500);

            fetch('<?php echo BASE_URL; ?>api.php?action=toggle_job_like', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        likeCountSpan.textContent = data.like_count;
                        this.classList.toggle('active', data.user_has_liked);
                    }
                });
        });
    }

    // --- Commenting Logic ---
    const commentList = document.getElementById('comment-list');
    const mainCommentForm = document.getElementById('comment-form');

    if (mainCommentForm) {
        mainCommentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            const formData = new FormData(this);
            fetch('<?php echo BASE_URL; ?>api.php?action=post_job_comment', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    commentList.insertAdjacentHTML('afterbegin', data.comment_html);
                    this.reset();
                } else {
                    showToast(data.message || 'Could not post comment.', 'error');
                }
            })
            .finally(() => { submitBtn.disabled = false; });
        });
    }

    const deleteModal = document.getElementById('delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    let commentToDeleteId = null;

    if (commentList) {
        commentList.addEventListener('click', function(e) {
            // Handle Reply Button Click
            if (e.target.matches('.reply-btn')) {
                const commentId = e.target.dataset.commentId;
                const replyForm = document.querySelector(`.reply-form[data-parent-id="${commentId}"]`);
                if (replyForm) { replyForm.classList.toggle('active'); }
            }

            // Handle Edit Button Click
            if (e.target.matches('.edit-comment-btn')) {
                const commentId = e.target.dataset.commentId;
                const commentItem = document.getElementById(`comment-${commentId}`);
                commentItem.querySelector('.comment-text').style.display = 'none';
                commentItem.querySelector('.comment-actions').style.display = 'none';
                commentItem.querySelector('.edit-comment-form').style.display = 'block';
            }

            // Handle Cancel Edit Button Click
            if (e.target.matches('.cancel-edit-btn')) {
                const commentId = e.target.closest('form').dataset.commentId;
                const commentItem = document.getElementById(`comment-${commentId}`);
                commentItem.querySelector('.edit-comment-form').style.display = 'none';
                commentItem.querySelector('.comment-text').style.display = 'block';
                commentItem.querySelector('.comment-actions').style.display = 'block';
            }

            // Handle Delete Button Click -> Opens the modal
            if (e.target.matches('.delete-comment-btn')) {
                commentToDeleteId = e.target.dataset.commentId;
                deleteModal.classList.add('active');
            }
        });

            
        if (deleteModal) {
        // Close modal when "Cancel" is clicked
        cancelDeleteBtn.addEventListener('click', () => {
            deleteModal.classList.remove('active');
            commentToDeleteId = null;
        });

        // Handle the final deletion when "Delete" is clicked
        confirmDeleteBtn.addEventListener('click', function() {
            if (!commentToDeleteId) return;

            const formData = new FormData();
            formData.append('comment_id', commentToDeleteId);
            
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.textContent = 'Deleting...';

            fetch('<?php echo BASE_URL; ?>api.php?action=delete_job_comment', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const commentElement = document.getElementById(`comment-${commentToDeleteId}`);
                    if (commentElement) {
                        commentElement.remove();
                    }
                    showToast('Comment deleted.', 'success');
                } else {
                    showToast(data.message || 'Could not delete comment.', 'error');
                }
            })
            .finally(() => {
                // Reset everything
                deleteModal.classList.remove('active');
                commentToDeleteId = null;
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.textContent = 'Delete';
            });
        });
    }

        // Event listener for submitting both reply and edit forms
        commentList.addEventListener('submit', function(e) {
            e.preventDefault();

            // Handle Reply Form Submission
            if (e.target.classList.contains('reply-form')) {
                e.preventDefault();
                const form = e.target;
                const parentId = form.dataset.parentId;
                const replyContainer = document.querySelector(`#comment-${parentId} .comment-replies`);
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                const formData = new FormData(form);
                fetch('<?php echo BASE_URL; ?>api.php?action=post_job_comment', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        replyContainer.insertAdjacentHTML('beforeend', data.comment_html);
                        form.reset();
                        form.classList.remove('active');
                    } else { showToast(data.message, 'error'); }
                })
                .finally(() => { submitBtn.disabled = false; });
            }

            // Handle Edit Form Submission
            if (e.target.matches('.edit-comment-form')) {
                const form = e.target;
                const commentId = form.dataset.commentId;
                const commentText = form.querySelector('textarea').value;
                const commentItem = document.getElementById(`comment-${commentId}`);

                const formData = new FormData();
                formData.append('comment_id', commentId);
                formData.append('comment_text', commentText);

                fetch('<?php echo BASE_URL; ?>api.php?action=edit_job_comment', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update the text and switch back to view mode
                        commentItem.querySelector('.comment-actions').style.display = 'block';
                        const textElement = commentItem.querySelector('.comment-text');
                        textElement.innerHTML = data.updated_html;
                        form.style.display = 'none';
                        textElement.style.display = 'block';
                        showToast('Comment updated!', 'success');
                    } else {
                        showToast(data.message || 'Failed to update.', 'error');
                    }
                });
            }
        });
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>