<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/includes/header.php';

    $profile_user_id = (int)($_GET['id'] ?? 0);
    if ($profile_user_id === 0) {
        header('Location: ' . BASE_URL . '404.php');
        exit();
    }

    // Fetch the profile user's public data
    $user_stmt = $pdo->prepare("SELECT id, name, user_type, headline, location, phone, email, profile_image, skills_cache FROM users WHERE id = ?");
    $user_stmt->execute([$profile_user_id]);
    $profile_user = $user_stmt->fetch();
    if (!$profile_user) { die("User profile not found."); }
    $page_title = htmlspecialchars($profile_user['name']);

    // Fetch public content (CVs or Jobs)
    $public_content = [];
    if ($profile_user['user_type'] === 'recruitee') {
        $cv_stmt = $pdo->prepare("SELECT id, title, updated_at FROM cvs WHERE user_id = ? AND is_public = 1 ORDER BY updated_at DESC");
        $cv_stmt->execute([$profile_user_id]);
        $public_content = $cv_stmt->fetchAll();
    } else { // recruiter
        $job_stmt = $pdo->prepare("SELECT j.id, j.title, j.posted_at, c.name as company_name 
                                FROM jobs j JOIN companies c ON j.company_id = c.id 
                                WHERE j.recruiter_user_id = ? AND j.is_active = 1 
                                ORDER BY j.posted_at DESC");
        $job_stmt->execute([$profile_user_id]);
        $public_content = $job_stmt->fetchAll();
    }

    // Fetch Rating Data
    $avg_rating = 0;
    $rating_count = 0;
    $my_rating = 0;
    if ($profile_user['user_type'] === 'recruitee') {
        $rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_r, COUNT(rating) as count_r FROM recruitee_ratings WHERE recruitee_user_id = ?");
        $rating_stmt->execute([$profile_user_id]);
        $ratings = $rating_stmt->fetch();
        if($ratings && $ratings['avg_r'] !== null) { $avg_rating = $ratings['avg_r']; $rating_count = $ratings['count_r']; }
        if ($is_logged_in) {
            $my_rating_stmt = $pdo->prepare("SELECT rating FROM recruitee_ratings WHERE recruitee_user_id = ? AND recruiter_user_id = ?");
            $my_rating_stmt->execute([$profile_user_id, $_SESSION['user_id']]);
            $my_rating = $my_rating_stmt->fetchColumn() ?: 0;
        }
    } else { // recruiter
        $rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_r, COUNT(rating) as count_r FROM recruiter_ratings WHERE recruiter_user_id = ?");
        $rating_stmt->execute([$profile_user_id]);
        $ratings = $rating_stmt->fetch();
        if($ratings && $ratings['avg_r'] !== null) { $avg_rating = $ratings['avg_r']; $rating_count = $ratings['count_r']; }
        if ($is_logged_in) {
            $my_rating_stmt = $pdo->prepare("SELECT rating FROM recruiter_ratings WHERE recruiter_user_id = ? AND recruitee_user_id = ?");
            $my_rating_stmt->execute([$profile_user_id, $_SESSION['user_id']]);
            $my_rating = $my_rating_stmt->fetchColumn() ?: 0;
        }
    }
    $name = htmlspecialchars($profile_user['name']);
    $possessive_name = rtrim($name) . (str_ends_with(strtolower($name), 's') ? "'" : "'s");
    $page_title = "{$possessive_name} Profile: Khojsuru";
?>

<style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header {
        background: var(--secondary-bg);
        border-radius: 24px;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }

    body.light-theme .profile-header {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    /* .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--accent-color), #8b5cf6, #ec4899);
    } */

    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid var(--accent-color);
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.4);
    }

    .user-type-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: linear-gradient(135deg, var(--accent-color), #8b5cf6);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .profile-name {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .profile-headline {
        font-size: 1.2rem;
        color: var(--text-secondary);
        margin: 0 0 1.5rem 0;
        font-weight: 500;
    }

    .profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 2rem;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    .meta-item:hover {
        color: var(--text-primary);
    }

    .meta-item i {
        color: var(--accent-color);
        font-size: 1rem;
        width: 16px;
        text-align: center;
    }

    .profile-skills {
        margin-top: 1.5rem;
    }

    .skills-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        text-align: center;
    }

    .skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        justify-content: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .skill-badge {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-color);
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 2px solid rgba(59, 130, 246, 0.2);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .skill-badge:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
        border-color: var(--accent-color);
    }

    /* Rating System */
    .rating-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .rating-display {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .rating-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .stars-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stars-outer {
        position: relative;
        display: inline-block;
        font-size: 1.5rem;
    }

    .stars-inner {
        position: absolute;
        top: 0;
        left: 0;
        white-space: nowrap;
        overflow: hidden;
    }

    .stars-outer::before,
    .stars-inner::before {
        content: "\f005 \f005 \f005 \f005 \f005";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
    }

    .stars-outer::before {
        color: var(--border-color);
    }

    .stars-inner::before {
        color: #f59e0b;
    }

    .rating-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .rating-number {
        font-weight: 600;
        color: var(--accent-color);
        font-size: 1.1rem;
    }

    .rating-interaction {
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
    }

    .rating-interaction-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .star-rater {
        display: inline-flex;
        gap: 0.3rem;
    }

    .star-rater i {
        font-size: 2rem;
        color: var(--border-color);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .star-rater i:hover,
    .star-rater i.hover-active {
        color: #f59e0b;
        transform: scale(1.1);
    }

    .star-rater i.selected {
        color: #f59e0b;
    }

    /* Content Section */
    .content-section {
        background: var(--secondary-bg);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 6px 28px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }

    body.light-theme .content-section {
        box-shadow: 0 6px 28px rgba(0, 0, 0, 0.06);
    }

    /* .content-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), #8b5cf6);
    } */

    .content-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2rem 0;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .content-title i {
        color: var(--accent-color);
        font-size: 1.5rem;
    }

    .content-list {
        display: flex;
        flex-direction: column;
    }
    
    .content-item:last-child {
        border-bottom: none;
    }

    .content-item {
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .content-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    body.light-theme .content-item:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .content-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
    }

    .content-meta {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .content-meta i {
        color: var(--accent-color);
        font-size: 0.8rem;
    }

    .btn-view {
        /* background: linear-gradient(135deg, var(--accent-color), #8b5cf6); */
        /* color: white; */
        /* padding: 0.8rem 1.5rem; */
        /* border-radius: 12px; */
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex
    ;
        align-items: center;
        gap: 0.5rem;
        /* transition: all 0.3s ease; */
        /* flex-shrink: 0; */
        /* min-width: 120px; */
        justify-content: center;
    }

    .btn-view:hover {
        color: white;
    }

    .btn-view i {
        font-size: 0.9rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 1rem;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 1.3rem;
        color: var(--text-primary);
        margin: 1rem 0 0.5rem 0;
    }

    .empty-state p {
        font-size: 1rem;
        margin: 0;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .profile-container {
            padding: 0rem;
        }

        .profile-header {
            padding: 2rem 1.5rem;
        }

        .profile-name {
            font-size: 2rem;
        }

        .profile-meta {
            flex-direction: column;
            gap: 0.8rem;
            align-items: center;
        }

        .meta-item {
            justify-content: center;
        }

        .skills-container {
            gap: 0.5rem;
        }

        .skill-badge {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .content-section {
            padding: 1.5rem;
        }

        .content-item {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .btn-view {
            width: 100%;
        }

        .rating-display {
            flex-direction: column;
            gap: 0.8rem;
        }

        .star-rater i {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 480px) {
        .profile-name {
            font-size: 1.8rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
        }

        .content-title {
            font-size: 1.3rem;
            margin: 0rem;
        }

        .content-title i {
            font-size: 1.3rem;
        }

        .star-rater i {
            font-size: 1.6rem;
        }
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-wrapper">
            <img src="<?php echo BASE_URL . ($profile_user['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                 alt="<?php echo htmlspecialchars($profile_user['name']); ?>" 
                 class="profile-avatar">
            <div class="user-type-badge">
                <i class="fas <?php echo $profile_user['user_type'] === 'recruitee' ? 'fa-user-graduate' : 'fa-user-tie'; ?>"></i>
                <?php echo ucfirst($profile_user['user_type']); ?>
            </div>
        </div>

        <h1 class="profile-name"><?php echo htmlspecialchars($profile_user['name']); ?></h1>
        
        <?php if (!empty($profile_user['headline'])): ?>
            <p class="profile-headline"><?php echo htmlspecialchars($profile_user['headline']); ?></p>
        <?php endif; ?>

        <div class="profile-meta">
            <?php if (!empty($profile_user['location'])): ?>
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($profile_user['location']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile_user['email'])): ?>
                <div class="meta-item">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo htmlspecialchars($profile_user['email']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($profile_user['phone'])): ?>
                <div class="meta-item">
                    <i class="fas fa-phone"></i>
                    <span><?php echo htmlspecialchars($profile_user['phone']); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($profile_user['skills_cache'])): ?>
            <div class="profile-skills">
                <div class="skills-title">
                    <i class="fas fa-code"></i> Skills & Expertise
                </div>
                <div class="skills-container">
                    <?php 
                    $skills = array_map('trim', explode(',', $profile_user['skills_cache']));
                    foreach ($skills as $skill): ?>
                        <span class="skill-badge"><?php echo htmlspecialchars($skill); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="rating-section">
            <div class="rating-display">
                <span class="rating-label">Overall Rating:</span>
                <div class="stars-wrapper">
                    <div class="stars-outer">
                        <div class="stars-inner" style="width: <?php echo ($avg_rating / 5) * 100; ?>%;"></div>
                    </div>
                    <div class="rating-info">
                        <span class="rating-number"><?php echo number_format($avg_rating, 1); ?></span>
                        <span>•</span>
                        <span><?php echo $rating_count; ?> <?php echo $rating_count === 1 ? 'review' : 'reviews'; ?></span>
                    </div>
                </div>
            </div>

            <?php if ($is_logged_in && $_SESSION['user_id'] != $profile_user_id && $_SESSION['user_type'] != $profile_user['user_type']): ?>
                <div class="rating-interaction">
                    <div class="rating-interaction-title">Rate this <?php echo $profile_user['user_type']; ?>:</div>
                    <div class="star-rater" data-profile-id="<?php echo $profile_user_id; ?>" data-profile-type="<?php echo $profile_user['user_type']; ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo ($i <= $my_rating) ? 'selected' : ''; ?>" data-value="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-section">
        <?php if ($profile_user['user_type'] === 'recruitee'): ?>
            <h2 class="content-title">
                <i class="fas fa-file-alt"></i>
                Public CVs
            </h2>
            <?php if (empty($public_content)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Public CVs</h3>
                    <p>This candidate hasn't made any CVs public yet. Check back later or contact them directly.</p>
                </div>
            <?php else: ?>
                <div class="content-list">
                    <?php foreach ($public_content as $cv): ?>
                        <div class="content-item">
                            <div class="content-info">
                                <h3><?php echo htmlspecialchars($cv['title']); ?></h3>
                                <p class="content-meta">
                                    <i class="fas fa-calendar-alt"></i>
                                    Last updated: <?php echo date('M d, Y', strtotime($cv['updated_at'])); ?>
                                </p>
                            </div>
                            <a href="view_cv.php?id=<?php echo $cv['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i>
                                View CV
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php else: // recruiter ?>
            <h2 class="content-title">
                <i class="fas fa-briefcase"></i>
                Active Job Postings
            </h2>
            <?php if (empty($public_content)): ?>
                <div class="empty-state">
                    <i class="fas fa-briefcase"></i>
                    <h3>No Active Jobs</h3>
                    <p>This recruiter doesn't have any active job postings at the moment.</p>
                </div>
            <?php else: ?>
                <div class="content-list">
                    <?php foreach ($public_content as $job): ?>
                        <div class="content-item">
                            <div class="content-info">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <div class="content-meta">
                                    <div>
                                        <i class="fas fa-building"></i>
                                        <?php echo htmlspecialchars($job['company_name']); ?>
                                    </div>
                                    <span>•</span>
                                    <div>
                                        <i class="fas fa-calendar-alt"></i>
                                        Posted: <?php echo date('M d, Y', strtotime($job['posted_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn-view">
                                <i class="fas fa-eye"></i>
                                View Job
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starRater = document.querySelector('.star-rater');
        if (starRater) {
            const stars = starRater.querySelectorAll('i');
            
            // Hover effects
            stars.forEach((star, index) => {
                star.addEventListener('mouseenter', function() {
                    highlightStars(index + 1);
                });
                
                star.addEventListener('mouseleave', function() {
                    resetStarsToSelected();
                });
            });
            
            // Click to rate
            starRater.addEventListener('click', function(e) {
                if (e.target.classList.contains('fa-star')) {
                    const rating = parseInt(e.target.dataset.value);
                    const profileId = this.dataset.profileId;
                    const profileType = this.dataset.profileType;

                    // Update visual state immediately
                    updateSelectedStars(rating);

                    // Send the rating to the server
                    const formData = new FormData();
                    formData.append('profile_user_id', profileId);
                    formData.append('profile_user_type', profileType);
                    formData.append('rating', rating);

                    fetch('api.php?action=submit_rating', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            showToast('Rating submitted successfully!', 'success');
                            // Optionally reload to update the average rating
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Could not submit rating.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Rating submission failed:', error);
                        showToast('Network error. Please try again.', 'error');
                    });
                }
            });
            
            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    star.classList.toggle('hover-active', index < rating);
                });
            }
            
            function resetStarsToSelected() {
                stars.forEach(star => {
                    star.classList.remove('hover-active');
                });
            }
            
            function updateSelectedStars(rating) {
                stars.forEach((star, index) => {
                    star.classList.toggle('selected', index < rating);
                });
            }
        }
    });
</script>