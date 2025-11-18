<?php
    $page_title = "Khojsuru: Begin Here.";
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php'; 

    // --- Pagination setup ---
    $per_page = 30;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;

    // --- Fetch total count of jobs for pagination ---
    $search_term = trim($_GET['q'] ?? '');
    $sql_params = [];
    $count_sql = "SELECT COUNT(*) FROM jobs j 
                JOIN companies c ON j.company_id = c.id 
                WHERE j.is_active = 1";

    if (!empty($search_term)) {
        $count_sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)";
        $sql_params = ['%' . $search_term . '%', '%' . $search_term . '%', '%' . $search_term . '%'];
    }

    $stmt_count = $pdo->prepare($count_sql);
    $stmt_count->execute($sql_params);
    $total_jobs = $stmt_count->fetchColumn();
    $total_pages = ceil($total_jobs / $per_page);

    // --- Fetch jobs for current page ---
    $sql = "SELECT 
                j.id, j.title, j.location, j.job_type, j.is_remote, j.posted_at, j.is_featured, j.deadline,
                c.name as company_name,
                u.profile_image as recruiter_avatar,
                u.name AS recruiter_name,
                (SELECT COUNT(*) FROM job_likes WHERE job_id = j.id) as like_count,
                (SELECT COUNT(*) FROM job_comments WHERE job_id = j.id) as comment_count
            FROM jobs j
            JOIN companies c ON j.company_id = c.id
            JOIN users u ON j.recruiter_user_id = u.id
            WHERE j.is_active = 1 AND j.deadline > UTC_TIMESTAMP()"; // <-- CRITICAL CHANGE HERE

    if (!empty($search_term)) {
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)";
    }

    // <!-- CRITICAL CHANGE: Order by is_featured DESC first! -->
    $sql .= " ORDER BY j.is_featured DESC, j.posted_at DESC LIMIT ? OFFSET ?";

    $stmt = $pdo->prepare($sql);

    // Merge search params with LIMIT and OFFSET
    $execute_params = $sql_params;
    $execute_params[] = $per_page;
    $execute_params[] = $offset;

    $stmt->execute($execute_params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* featured jobs  */
    .featured-tag {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 0.8em;
        font-weight: 600;
        color: #f59e0b;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    /* Main container */
    .main-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Hero section */
    .hero-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .hero-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        margin: 0 0 1rem 0;
        background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.1;
    }

    .hero-subtitle {
        color: var(--text-secondary);
        font-size: 1.25rem;
        max-width: 600px;
        margin: 0 auto 2rem;
        line-height: 1.6;
    }

    /* Search section */
    .search-section {
        margin-bottom: 3rem;
    }

    .search-container {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .search-form {
        display: flex;
        background: var(--secondary-bg);
        border-radius: 16px;
        padding: 8px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .search-form:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .search-input {
        flex: 1;
        background: transparent;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        color: var(--text-primary);
        border-radius: 12px;
        outline: none;
    }

    .search-input::placeholder {
        color: var(--text-secondary);
    }

    .search-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-btn:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    /* Results header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .results-info {
        color: var(--text-secondary);
        font-size: 1rem;
    }

    .results-count {
        font-weight: 600;
        color: var(--accent-color);
    }

    /* Job grid */
    .jobs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    /* Job card */
    .feed-item { background: var(--secondary-bg); border-radius: 12px; margin-bottom: 1rem; border: 1px solid var(--border-color); overflow: hidden; position:relative;}
    .feed-item:hover{transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--accent-color);}
    .item-header { padding: 1rem; display: flex; align-items: center; gap: 1rem; }
    .item-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: contain;}
    .item-author h4 { margin: 0; font-size: 1.1rem; }
    .item-author p { margin: 0; font-size: 0.9rem; color: var(--text-secondary); }
    .job-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.3;
        padding: 0rem 1rem;
    }
    .item-content { padding: 0 1rem 1rem; }
    .job-meta { display: flex; flex-wrap: wrap; gap: 0.5rem 1rem; color: var(--text-secondary); font-size: 0.9rem; margin-top: 1rem; }
    .item-footer { border-top: 1px solid var(--border-color); padding: 0.75rem 1rem;
    display: flex; justify-content: space-between;align-items: center; flex-wrap: wrap; gap:1rem;}
    .social-counts{display: flex; gap: 1.5rem;  font-size: 0.9em; color: var(--text-secondary);}
    .social-counts i{color: var(--accent-color);}
    .remote-badge {
        color: #10b981;
    }
    .apply-btn a:hover {
        color: var(--text-secondary);
        transform: translateY(-2px);
    }
    .btn-submit:hover{background-color:#235ee1;}
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

    .login-btn {
        background: var(--secondary-bg);
        color: var(--accent-color);
        border: 2px solid var(--accent-color);
    }

    .login-btn:hover {
        background: var(--accent-color);
        color: white;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--secondary-bg);
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .empty-state-icon {
        font-size: 4rem;
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--text-primary);
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .fa-building{
        font-size: 0.8rem;
    }
    .pagination-link-left{padding-left: 0.15rem;}
    .pagination-link-right{padding-right: 0.15rem;}

    /* Responsive design */
    @media (max-width: 768px) {
        .main-container {
            padding: 0rem;
        }

        .hero-section {
            padding: 2rem 1rem;
            margin-bottom: 0rem;
        }

        .jobs-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .search-form {
            flex-direction: column;
            gap: 0.5rem;
        }

        .search-btn {
            width: 100%;
            justify-content: center;
        }

        .job-card-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .apply-btn {
            justify-content: center;
        }
        
        .pagination-container {
            gap: 6px;
        }
        .pagination-link {
            min-width: 5px;
            height: 30px;
            padding: 0 7px;
            border-radius: 5px;
        }
    }
    @media (max-width: 480px) {
        .results-header {
            flex-direction: column;
            align-items: stretch;
        }

        .job-meta {
            flex-direction: column;
        }
        .pagination-link-left,
        .pagination-link-right{display:none;}
    }
    .countdown-timer { font-size: 0.9em; font-weight: 600; }
</style>

<div class="main-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Find Your Next Opportunity</h1>
        <p class="hero-subtitle">Discover amazing job opportunities from top companies worldwide. Your dream career is waiting for you.</p>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <div class="search-container">
            <form class="search-form" method="GET" action="<?php echo BASE_URL; ?>index.php">
                <input type="search" name="q" class="search-input" placeholder="Search jobs, companies, or keywords..." value="<?php echo htmlspecialchars($search_term); ?>" autocomplete="off">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                    Search Jobs
                </button>
            </form>
        </div>
    </div>

    <!-- Results Header -->
    <?php if (!empty($jobs) || !empty($search_term)): ?>
    <div class="results-header">
        <div class="results-info">
            <?php if (!empty($search_term)): ?>
                <span class="results-count"><?php echo $total_jobs; ?></span> results for "<?php echo htmlspecialchars($search_term); ?>"
            <?php else: ?>
                Showing <span class="results-count"><?php echo $total_jobs; ?></span> available positions
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Job Listings -->
    <div class="jobs-grid">
        <?php if (empty($jobs)): ?>
            <div class="empty-state" style="grid-column: 1 / -1;">
                <div class="empty-state-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <?php if (!empty($search_term)): ?>
                    <h3>No jobs found</h3>
                    <p>We couldn't find any jobs matching "<?php echo htmlspecialchars($search_term); ?>". Try adjusting your search terms.</p>
                <?php else: ?>
                    <h3>No jobs available</h3>
                    <p>There are no active job postings at the moment. Check back soon for new opportunities!</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="feed-item <?php if ($job['is_featured']) echo 'featured'; ?>">
                    <div class="item-header">
                        <img src="<?php echo BASE_URL . ($job['recruiter_avatar'] ?? 'assets/images/default-avatar.png'); ?>" alt="Recruiter Avatar" class="item-avatar">
                        <div class="item-author">
                            <h4><?php echo htmlspecialchars($job['recruiter_name']); ?></h4>
                            <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['company_name']); ?></p>
                        </div>
                    </div>
                    <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                    <div class="item-content">
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                            <?php if($job['is_remote']): ?><span><i class="fas fa-wifi remote-badge"></i> Remote</span><?php endif; ?>
                            
                            <?php if ($job['is_featured']): ?>
                                <span class="featured-tag"><i class="fas fa-star"></i> Featured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="item-footer">
                        <div class="social-counts">
                            <span><i class="fas fa-thumbs-up"></i> <?php echo $job['like_count']; ?></span>
                            <span><i class="fas fa-comment"></i> <?php echo $job['comment_count']; ?></span>
                            <span style="min-width: 10rem;"><i class="fas fa-clock"></i> <span class="countdown-timer" data-deadline="<?php echo $job['deadline']; ?>"></span></span>
                        </div>
                        <div class="apply-btn">
                            <?php if ($is_logged_in): ?>
                                <a href="view_job.php?id=<?php echo $job['id']; ?>" class="btn-action">View & Apply</a>
                            <?php else: ?>
                                <a href="login.php" class="btn-action">Login to View & Apply</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <!-- Previous -->
            <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo max(1, $page - 1); ?>" 
               class="pagination-link <?php echo $page == 1 ? 'disabled' : ''; ?>">&laquo; <span class="pagination-link-left">Prev</span>
            </a>

            <!-- Page numbers -->
            <?php
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $page + 2);
            
            if ($start_page > 1): ?>
                <a href="?q=<?php echo urlencode($search_term); ?>&page=1" class="pagination-link">1</a>
                <?php if ($start_page > 2): ?>
                    <span class="pagination-link disabled">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo $i; ?>" 
                   class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                    <span class="pagination-link disabled">...</span>
                <?php endif; ?>
                <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo $total_pages; ?>" class="pagination-link"><?php echo $total_pages; ?></a>
            <?php endif; ?>

            <!-- Next -->
            <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo min($total_pages, $page + 1); ?>" 
               class="pagination-link <?php echo $page == $total_pages ? 'disabled' : ''; ?>"><span class="pagination-link-right">Next</span> &raquo;
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>