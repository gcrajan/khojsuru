<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    $slug = $_GET['slug'] ?? '';
    if (empty($slug)) {
        header("Location: " . BASE_URL . "404.php");
        exit();
    }
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
    if (!$post) {
        header("Location: " . BASE_URL . "404.php");
        exit();
    }
    $page_title = htmlspecialchars($post['title']);
    require_once __DIR__ . '/includes/header.php';
?>

<style>
    main{padding:0rem;}

    .article-universe {
        min-height: 100vh;
        background: 
            radial-gradient(circle at 25% 75%, rgba(59, 130, 246, 0.03) 0%, transparent 40%),
            radial-gradient(circle at 75% 25%, rgba(16, 185, 129, 0.02) 0%, transparent 40%),
            var(--primary-bg);
        position: relative;
        overflow-x: hidden;
    }

    /* Top Reading Progress Bar */
    .reading-progress-bar {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-color), var(--success-color));
        z-index: 1000;
        transition: width 0.3s ease;
    }

    /* Hero Section - More Elegant */
    .article-hero {
        position: relative;
        height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: linear-gradient(135deg, var(--primary-bg), var(--secondary-bg));
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 110%;
        height: 110%;
        background-size: cover;
        background-position: center;
        filter: brightness(0.4) blur(0.5px);
        transform: scale(1.05);
        transition: transform 0.8s ease;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.7) 50%, rgba(15, 23, 42, 0.9) 100%);
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 900px;
        padding: 0 2rem;
        animation: heroFadeIn 1.2s ease-out;
    }

    @keyframes heroFadeIn {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-title {
        font-size: clamp(2.2rem, 6vw, 4.5rem);
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 2rem;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .hero-meta {
        display: flex;
        justify-content: center;
        gap: 2.5rem;
        margin: 2rem 0;
        flex-wrap: wrap;
    }

    .meta-badge {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        padding: 0.8rem 1.2rem;
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .meta-badge:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-1px);
    }

    .meta-icon {
        width: 18px;
        height: 18px;
        color: var(--accent-color);
    }

    .scroll-indicator {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        color: var(--text-secondary);
        animation: gentleBounce 3s ease-in-out infinite;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .scroll-indicator:hover {
        opacity: 1;
    }

    .scroll-indicator svg {
        width: 28px;
        height: 28px;
    }

    @keyframes gentleBounce {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-8px); }
    }

    /* Content Section - Clean and Professional */
    .content-wrapper {
        position: relative;
        background: var(--primary-bg);
        margin-top: -60px;
        z-index: 0;
        border-radius: 30px 30px 0 0;
        padding: 4rem 0 2rem;
    }

    .article-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
    }

    /* Professional Typography */
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        font-weight: 600;
        line-height: 1.3;
        margin: 2.5rem 0 1.2rem;
        color: var(--text-primary);
        scroll-margin-top: 2rem;
    }

    .article-content h1 { 
        font-size: 2.5rem;
        font-weight: 700;
    }

    .article-content h2 { 
        font-size: 2rem;
        color: var(--text-primary);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .article-content h2::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background: var(--accent-color);
    }

    .article-content h3 { 
        font-size: 1.6rem;
        color: var(--accent-color);
    }

    .article-content h4 { 
        font-size: 1.3rem;
    }

    .article-content h5 { 
        font-size: 1.1rem;
    }

    .article-content h6 { 
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Clean Paragraph Styling */
    .article-content p {
        font-size: 1.125rem;
        line-height: 1.75;
        color: var(--text-secondary);
        margin: 1.5rem 0;
        text-align: left;
    }

    /* Professional Link Styling */
    .article-content a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .article-content a:hover {
        border-bottom-color: var(--accent-color);
        color: var(--text-primary);
    }

    /* Enhanced Lists */
    .article-content ul,
    .article-content ol {
        margin: 1.5rem 0;
        padding-left: 1.8rem;
    }

    .article-content li {
        margin: 0.8rem 0;
        line-height: 1.6;
        color: var(--text-secondary);
    }

    .article-content ul li {
        position: relative;
    }

    .article-content ul li::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.7rem;
        width: 6px;
        height: 6px;
        background: var(--accent-color);
        border-radius: 50%;
    }

    /* Professional Image Styling */
    .article-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 2.5rem auto;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transition: all 0.4s ease;
    }

    .article-content img:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
    }

    /* Elegant Blockquotes */
    .article-content blockquote {
        margin: 2.5rem 0;
        padding: 1.5rem 2rem;
        background: var(--secondary-bg);
        border-left: 4px solid var(--accent-color);
        border-radius: 0 8px 8px 0;
        font-style: italic;
        font-size: 1.1rem;
        color: var(--text-primary);
        position: relative;
    }

    .article-content blockquote::before {
        content: '"';
        font-size: 3rem;
        color: var(--accent-color);
        position: absolute;
        top: 0.5rem;
        left: 1rem;
        opacity: 0.3;
        font-family: serif;
    }

    /* Professional Code Styling */
    .article-content pre {
        background: var(--secondary-bg);
        padding: 1.5rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 2rem 0;
        border: 1px solid var(--border-color);
    }

    .article-content code {
        background: var(--secondary-bg);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-family: 'Fira Code', 'Monaco', monospace;
        font-size: 0.9rem;
        color: var(--accent-color);
    }

    .article-content pre code {
        background: none;
        padding: 0;
        color: var(--text-secondary);
    }

    /* Floating Action Panel */
    .action-panel {
        position: fixed;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        z-index: 1;
    }

    .action-btn {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-btn:hover {
        background: var(--accent-color);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
    }

    .action-btn svg {
        width: 20px;
        height: 20px;
    }

    /* Professional Navigation */
    .navigation-footer {
        text-align: center;
        padding: 3rem 0;
        /* background: var(--secondary-bg); */
        border-top: 1px solid var(--border-color);
        margin-top: 4rem;
    }

    .nav-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        background: var(--accent-color);
        color: white;
        text-decoration: none;
        padding: 1rem 2rem;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    .nav-btn:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.35);
    }

    .nav-btn svg {
        width: 18px;
        height: 18px;
    }

    /* Table Styling */
    .article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
        background: var(--secondary-bg);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .article-content th,
    .article-content td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .article-content th {
        background: var(--primary-bg);
        font-weight: 600;
        color: var(--text-primary);
    }

    .article-content td {
        color: var(--text-secondary);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .article-hero {
            height: 70vh;
        }
        
        .hero-content {
            padding: 0 1.5rem;
        }
        
        .hero-meta {
            gap: 1rem;
            flex-direction: column;
            align-items: center;
        }
        
        .article-content {
            padding: 0 1.5rem;
        }
        
        .action-panel {
            right: 1rem;
        }
        
        .action-btn {
            width: 46px;
            height: 46px;
        }
        
        .article-content p {
            font-size: 1.05rem;
            line-height: 1.7;
        }
    }

    @media (max-width: 480px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .article-content {
            padding: 0 1rem;
        }
        
        .action-panel {
            display: none;
        }
        
        .article-content h2 {
            font-size: 1.6rem;
        }
        
        .article-content h3 {
            font-size: 1.3rem;
        }
    }

    /* Light Theme Adjustments */
    body.light-theme .hero-overlay {
        background: 
            linear-gradient(135deg, rgba(241, 245, 249, 0.9) 0%, rgba(255, 255, 255, 0.8) 50%, rgba(241, 245, 249, 0.95) 100%);
    }

    body.light-theme .meta-badge {
        background: rgba(15, 23, 42, 0.08);
        border-color: rgba(15, 23, 42, 0.1);
    }

    body.light-theme .action-btn {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    body.light-theme .nav-btn {
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    body.light-theme .nav-btn:hover {
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    /* Smooth Content Animation */
    .content-wrapper {
        animation: contentSlideUp 0.8s ease-out;
    }

    @keyframes contentSlideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Top Reading Progress Bar -->
<div class="reading-progress-bar" id="reading-progress"></div>

<div class="article-universe">
    <!-- Clean Hero Section -->
    <section class="article-hero">
        <?php if (!empty($post['featured_image'])): ?>
            <div class="hero-background" style="background-image: url('<?php echo BASE_URL . htmlspecialchars($post['featured_image']); ?>')"></div>
        <?php endif; ?>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <h1 class="hero-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="hero-meta">
                <div class="meta-badge">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                </div>
                
                <div class="meta-badge">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php 
                    $word_count = str_word_count(strip_tags($post['content_html']));
                    $reading_time = max(1, ceil($word_count / 200));
                    echo $reading_time . ' min read';
                    ?>
                </div>
                
                <div class="meta-badge">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <?php echo number_format($word_count); ?> words
                </div>
            </div>
        </div>
        
        <div class="scroll-indicator" onclick="document.querySelector('.content-wrapper').scrollIntoView({behavior: 'smooth'})">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- Professional Content Section -->
    <section class="content-wrapper">
        <div class="article-content">
            <?php echo $post['content_html']; ?>
        </div>
        
        <!-- Professional Navigation Footer -->
        <div class="navigation-footer">
            <a href="<?php echo BASE_URL; ?>blog.php" class="nav-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Articles
            </a>
        </div>
    </section>

    <!-- Clean Floating Actions -->
    <div class="action-panel">
        <a href="#" class="action-btn" title="Share Article" onclick="navigator.share ? navigator.share({title: document.title, url: window.location.href}) : copyToClipboard(window.location.href)">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
            </svg>
        </a>
        
        <div class="action-btn" title="View Other Articles" 
            onclick="window.location.href='<?php echo BASE_URL; ?>blog.php';">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="26" height="26">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 
                        012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 8h6v4H7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 16h6M15 16h2M15 12h2"/>
            </svg>
        </div>

        
        <div class="action-btn" title="Back to Top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Subtle parallax for hero background
    const heroBackground = document.querySelector('.hero-background');
    if (heroBackground) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (scrolled < window.innerHeight) {
                heroBackground.style.transform = `scale(1.05) translateY(${scrolled * 0.3}px)`;
            }
        });
    }

    // Top reading progress bar
    function updateReadingProgress() {
        const contentWrapper = document.querySelector('.content-wrapper');
        const progressBar = document.getElementById('reading-progress');
        
        if (contentWrapper && progressBar) {
            const contentTop = contentWrapper.offsetTop;
            const contentHeight = contentWrapper.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;
            
            const contentBottom = contentTop + contentHeight;
            const windowBottom = scrollTop + windowHeight;
            
            if (scrollTop >= contentTop && scrollTop <= contentBottom - windowHeight) {
                const progress = (scrollTop - contentTop) / (contentHeight - windowHeight);
                progressBar.style.width = Math.min(100, Math.max(0, progress * 100)) + '%';
            } else if (windowBottom >= contentBottom) {
                progressBar.style.width = '100%';
            } else if (scrollTop < contentTop) {
                progressBar.style.width = '0%';
            }
        }
    }

    window.addEventListener('scroll', updateReadingProgress);
    updateReadingProgress();

    // Enhanced image loading
    document.querySelectorAll('.article-content img').forEach(img => {
        img.addEventListener('click', function() {
            if (this.requestFullscreen) {
                this.requestFullscreen();
            }
        });
        
        img.style.opacity = '0';
        img.addEventListener('load', function() {
            this.style.transition = 'opacity 0.5s ease';
            this.style.opacity = '1';
        });
    });

    // Copy to clipboard function
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target.closest('.action-btn');
            const originalBg = btn.style.backgroundColor;
            btn.style.backgroundColor = 'var(--success-color)';
            btn.style.color = 'white';
            setTimeout(() => {
                btn.style.backgroundColor = originalBg || 'var(--secondary-bg)';
                btn.style.color = 'var(--text-secondary)';
            }, 1500);
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        });
    };

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>