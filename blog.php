<?php
    $page_title = "Blog: Khojsuru";

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';

    // --- PAGINATION LOGIC ---
    $posts_per_page = 15; // Set how many posts to show per page
    $current_page = (int)($_GET['page'] ?? 1);
    if ($current_page < 1) { $current_page = 1; }
    $offset = ($current_page - 1) * $posts_per_page;

    // --- SEARCH & DATA FETCHING LOGIC ---
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $search_params = [];
    $base_sql = "FROM blog_posts WHERE status = 'published'";

    if (!empty($search_query)) {
        $base_sql .= " AND (title LIKE ? OR content_html LIKE ?)";
        $search_term = '%' . $search_query . '%';
        $search_params = [$search_term, $search_term];
    }

    // First, get the TOTAL count of posts for pagination
    $count_sql = "SELECT COUNT(*) " . $base_sql;
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($search_params);
    $total_posts = (int)$count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $posts_per_page);

    // Now, fetch only the posts for the CURRENT page
    $posts_sql = "SELECT * " . $base_sql . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $posts_stmt = $pdo->prepare($posts_sql);
    // Bind parameters for the main query
    foreach ($search_params as $key => $value) {
        $posts_stmt->bindValue($key + 1, $value);
    }
    $posts_stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
    $posts_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $posts_stmt->execute();
    $posts = $posts_stmt->fetchAll();


    require_once __DIR__ . '/includes/header.php';
?>

<style>
    main{padding:0rem;}
    .blog-universe {
        min-height: 100vh;
        background: 
            radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.02) 0%, transparent 50%),
            var(--primary-bg);
    }

    /* Hero Header Section */
    .blog-hero {
        text-align: center;
        padding: 2rem;
        position: relative;
        margin-bottom: 2rem;
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .hero-title {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 800;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-color) 50%, var(--success-color) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.02em;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-secondary);
        margin-bottom: 2.5rem;
        line-height: 1.6;
    }

    /* Advanced Search Section */
    .search-section {
        max-width: 600px;
        margin: 0 auto 2rem;
        position: relative;
    }

    .search-container {
        position: relative;
        background: var(--secondary-bg);
        border-radius: 50px;
        border: 2px solid var(--border-color);
        padding: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .search-container:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }

    .search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: none;
        background: transparent;
        font-size: 1.1rem;
        color: var(--text-primary);
        outline: none;
        padding-right: 120px;
    }

    .search-input::placeholder {
        color: var(--text-secondary);
    }

    .search-button {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 40px;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-button:hover {
        background: #2563eb;
        transform: translateY(-50%) scale(1.02);
    }

    .search-button svg {
        width: 18px;
        height: 18px;
    }

    /* Search Results Info */
    .search-results-info {
        text-align: center;
        margin-bottom: 2rem;
        color: var(--text-secondary);
        font-size: 1.1rem;
    }

    .search-results-info.has-results {
        background: var(--secondary-bg);
        padding: 1rem 2rem;
        border-radius: 50px;
        border: 1px solid var(--border-color);
        display: inline-block;
    }

    .clear-search {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
        margin-left: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        border: 1px solid var(--accent-color);
        transition: all 0.3s ease;
    }

    .clear-search:hover {
        background: var(--accent-color);
        color: white;
    }

    /* Blog Grid Container */
    .blog-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 4rem;
    }

    /* Modern Post Cards */
    .post-card {
        /* background: var(--secondary-bg); */
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); */
    }

    .post-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .post-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .post-card:hover::before {
        opacity: 1;
    }

    /* Image Section */
    .post-image {
        position: relative;
        overflow: hidden;
        height: 210px;
        background: var(--primary-bg);
    }

    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .post-card:hover .post-image img {
        transform: scale(1.05);
    }

    .post-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.2) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .post-card:hover .post-image-overlay {
        opacity: 1;
    }

    /* Reading Time Badge */
    .reading-time {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(10px);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Content Section */
    .post-content {
        padding: 1rem 1.5rem;
    }

    .post-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 0 0 0.5rem 0;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .meta-icon {
        width: 16px;
        height: 16px;
    }

    .post-title {
        margin: 0 0 0.5rem 0;
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.3;
    }

    .post-title a {
        color: var(--text-primary);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .post-title a:hover {
        color: var(--accent-color);
    }

    .post-excerpt {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    /* Modern Read More Button */
    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: transparent;
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
        padding-top: 0.8rem;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .read-more-btn:hover {
        border-bottom-color: var(--accent-color);
        transform: translateX(4px);
    }

    .read-more-btn svg {
        width: 16px;
        height: 16px;
        transition: transform 0.3s ease;
    }

    .read-more-btn:hover svg {
        transform: translateX(4px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
        font-size: 1.2rem;
        background: var(--secondary-bg);
        border-radius: 20px;
        border: 1px solid var(--border-color);
        margin: 2rem 0;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        color: var(--text-secondary);
        opacity: 0.5;
    }

    /* Load More Button */
    .load-more-section {
        text-align: center;
        margin-top: 4rem;
    }

    .load-more-btn {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.25);
    }

    .load-more-btn:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.35);
    }

    /* Featured Post Highlight */
    .post-card.featured {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        max-width: none;
        margin-bottom: 2rem;
    }

    .post-card.featured .post-image {
        height: auto;
        min-height: 300px;
    }

    .post-card.featured .post-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem;
    }

    .post-card.featured .post-title {
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .blog-hero {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .search-section {
            margin: 0 1rem 2rem;
        }
        
        .blog-container {
            padding: 0 1rem;
        }
        
        .blog-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .post-card.featured {
            grid-template-columns: 1fr;
        }
        
        .post-card.featured .post-content {
            padding: 2rem;
        }
        
        .post-card.featured .post-title {
            font-size: 1.6rem;
        }
        
        .search-input {
            padding-right: 100px;
        }
        
        .search-button {
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .post-content {
            padding: 1.5rem;
        }
        
        .search-input {
            font-size: 1rem;
            padding: 0.9rem 1.2rem;
            padding-right: 90px;
        }
        
        .search-button {
            padding: 0.6rem 1rem;
        }
        
        .search-button span {
            display: none;
        }
    }

    /* Light Theme Adjustments */
    body.light-theme .search-container {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    body.light-theme .search-container:focus-within {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.1);
    }

    body.light-theme .post-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    body.light-theme .post-card:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
    }

    body.light-theme .load-more-btn {
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.2);
    }

    body.light-theme .load-more-btn:hover {
        box-shadow: 0 8px 30px rgba(37, 99, 235, 0.3);
    }

    /* Animation for card entrance */
    .post-card {
        animation: cardSlideUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    .post-card:nth-child(1) { animation-delay: 0.1s; }
    .post-card:nth-child(2) { animation-delay: 0.2s; }
    .post-card:nth-child(3) { animation-delay: 0.3s; }
    .post-card:nth-child(4) { animation-delay: 0.4s; }
    .post-card:nth-child(5) { animation-delay: 0.5s; }
    .post-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes cardSlideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin: 4rem 0 2rem;
    }
    .pagination-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
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

<div class="blog-universe">
    <!-- Hero Header -->
    <section class="blog-hero">
        <div class="hero-content">
            <h1 class="hero-title">Career Hub</h1>
            <p class="hero-subtitle">
                Discover insights, tips, and resources to accelerate your professional journey and unlock your career potential.
            </p>
            
            <!-- Advanced Search -->
            <div class="search-section">
                <form method="GET" action="" class="search-container">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search articles, topics, tips..." 
                        value="<?php echo htmlspecialchars($search_query); ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="search-button">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Search</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Search Results Info -->
    <?php if (!empty($search_query)): ?>
        <div class="blog-container">
            <div class="search-results-info has-results">
                <?php if (count($posts) > 0): ?>
                    Found <strong><?php echo count($posts); ?></strong> article<?php echo count($posts) !== 1 ? 's' : ''; ?> 
                    for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                <?php else: ?>
                    No articles found for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                <?php endif; ?>
                <a href="blog.php" class="clear-search">Clear Search</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="blog-container">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <?php if (!empty($search_query)): ?>
                    <p>No articles match your search. Try different keywords or <a href="blog.php" style="color: var(--accent-color);">browse all articles</a>.</p>
                <?php else: ?>
                    <p>No blog posts have been published yet. Check back soon for exciting content!</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach($posts as $index => $post): ?>
                    <article class="post-card <?php echo $index === 0 ? 'featured' : ''; ?>">
                        <?php if (!empty($post['featured_image'])): ?>
                            <div class="post-image">
                                <a href="article.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                    <img src="<?php echo BASE_URL . htmlspecialchars($post['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                                         loading="lazy">
                                </a>
                                <div class="post-image-overlay"></div>
                                <div class="reading-time">
                                    <?php 
                                    $word_count = str_word_count(strip_tags($post['content_html']));
                                    $reading_time = max(1, ceil($word_count / 200));
                                    echo $reading_time . ' min read';
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <div class="post-meta">
                                <div class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </div>
                                <div class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <?php echo number_format($word_count); ?> words
                                </div>
                            </div>
                            
                            <h2 class="post-title">
                                <a href="article.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h2>
                            
                            <div class="post-excerpt">
                                <?php 
                                // Create a smart excerpt
                                $content = strip_tags($post['content_html']);
                                $excerpt = substr($content, 0, $index === 0 ? 200 : 150);
                                // Find the last complete word
                                $excerpt = substr($excerpt, 0, strrpos($excerpt, ' '));
                                echo htmlspecialchars($excerpt) . '...';
                                ?>
                            </div>
                            
                            <a href="article.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="read-more-btn">
                                Continue Reading
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($total_pages > 1): ?>
        
            <!-- pagination  -->
        <nav class="pagination-container" aria-label="Blog post navigation">
            <!-- Previous Button -->
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search_query); ?>" 
               class="pagination-link <?php if($current_page <= 1) echo 'disabled'; ?>">
               &laquo;
            </a>

            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" 
                   class="pagination-link <?php if($current_page == $i) echo 'active'; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Next Button -->
             <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search_query); ?>" 
               class="pagination-link <?php if($current_page >= $total_pages) echo 'disabled'; ?>">
               &raquo;
            </a>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced search functionality
        const searchInput = document.querySelector('.search-input');
        const searchForm = document.querySelector('.search-container');
        
        // Auto-focus search when pressing '/' key
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && !searchInput.matches(':focus')) {
                e.preventDefault();
                searchInput.focus();
            }
            
            // Clear search on Escape
            if (e.key === 'Escape' && searchInput.matches(':focus')) {
                searchInput.blur();
            }
        });
        
        // Search suggestions (you can extend this with AJAX)
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            // You can add live search suggestions here
        });
        
        // Smooth scroll for cards on mobile
        if (window.innerWidth <= 768) {
            const cards = document.querySelectorAll('.post-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = '0s';
                    }
                });
            }, {
                threshold: 0.1
            });
            
            cards.forEach(card => observer.observe(card));
        }
        
        // Image lazy loading enhancement
        const images = document.querySelectorAll('.post-image img');
        images.forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            img.addEventListener('error', function() {
                this.style.opacity = '0.5';
                this.alt = 'Image not available';
            });
        });
        
        // Enhanced card interactions
        const postCards = document.querySelectorAll('.post-card');
        postCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '1';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '0';
            });
            
            // Click anywhere on card to navigate (except links)
            card.addEventListener('click', function(e) {
                if (!e.target.closest('a')) {
                    const link = this.querySelector('.post-title a');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            });
        });
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>