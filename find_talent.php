<?php
$page_title = "Find Talent: Khojsuru";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

if ($_SESSION['user_type'] !== 'recruiter') {
    header('Location: ' . BASE_URL);
    exit();
}

$search_results = [];
$search_term = '';
$total_results = 0;
$limit = 12; // candidates per page
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

if (isset($_GET['q'])) {
    $search_term = trim($_GET['q']);
    if (!empty($search_term)) {
        $like_term = '%' . $search_term . '%';

        // Count total results
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) 
             FROM users
             WHERE user_type = 'recruitee'
             AND (name LIKE ? OR location LIKE ? OR phone LIKE ? OR skills_cache LIKE ?)"
        );
        $stmt->execute([$like_term, $like_term, $like_term, $like_term]);
        $total_results = (int)$stmt->fetchColumn();

        // Fetch paginated results
        $stmt = $pdo->prepare(
            "SELECT id, name, headline, profile_image, phone, location, skills_cache
             FROM users
             WHERE user_type = 'recruitee'
             AND (name LIKE ? OR location LIKE ? OR phone LIKE ? OR skills_cache LIKE ?)
             ORDER BY id DESC
             LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $like_term);
        $stmt->bindValue(2, $like_term);
        $stmt->bindValue(3, $like_term);
        $stmt->bindValue(4, $like_term);
        $stmt->bindValue(5, $limit, PDO::PARAM_INT);
        $stmt->bindValue(6, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $search_results = $stmt->fetchAll();
    }
}

$total_pages = $total_results > 0 ? ceil($total_results / $limit) : 1;
?>

<style>
    main{
        min-height:80vh
    }
    .talent-search-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .search-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }

    /* .search-header::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), #8b5cf6);
        border-radius: 2px;
    } */

    .search-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .search-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin: 0;
        opacity: 0.9;
    }

    .search-form {
        position: relative;
        max-width: 600px;
        margin: 0 auto 3rem auto;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        background: var(--secondary-bg);
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 4px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    body.light-theme .search-input-wrapper {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .search-input-wrapper:focus-within {
        border-color: var(--accent-color);
        box-shadow: 0 6px 30px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }

    .search-input {
        flex: 1;
        background: transparent;
        border: none;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        color: var(--text-primary);
        outline: none;
        border-radius: 12px;
    }

    .search-input::placeholder {
        color: var(--text-secondary);
        opacity: 0.7;
    }

    .search-btn {
        background: var(--accent-color);
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 120px;
        justify-content: center;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .search-btn:active {
        transform: translateY(0);
    }

    .search-icon {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        opacity: 0.6;
        pointer-events: none;
    }

    .search-input {
        padding-left: 3rem;
    }

    .results-section {
        margin-top: 2rem;
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .results-count {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
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

    .talent-card {
        background: var(--secondary-bg);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        animation: slideInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    body.light-theme .talent-card {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    .talent-card:nth-child(1) { animation-delay: 0.1s; }
    .talent-card:nth-child(2) { animation-delay: 0.2s; }
    .talent-card:nth-child(3) { animation-delay: 0.3s; }
    .talent-card:nth-child(4) { animation-delay: 0.4s; }
    .talent-card:nth-child(5) { animation-delay: 0.5s; }
    .talent-card:nth-child(6) { animation-delay: 0.6s; }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* .talent-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent-color), #8b5cf6);
        opacity: 0;
        transition: opacity 0.3s ease;
    } */

    .talent-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--accent-color);
    }

    body.light-theme .talent-card:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .talent-card:hover::before {
        opacity: 1;
    }

    .talent-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .talent-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--accent-color);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
    }

    .talent-card:hover .talent-avatar {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .talent-name {
        margin: 0.5rem 0;
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .talent-details{
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
    }

    .talent-info {
        color: var(--text-secondary);
        margin: 0.5rem 0;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: color 0.3s ease;
    }

    .talent-card:hover .talent-info {
        color: var(--text-primary);
    }

    .talent-info i {
        color: var(--accent-color);
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .talent-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
        margin-bottom: 1.5rem;
        min-height: 40px;
        align-items: center;
    }

    .skill-tag {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-color);
        padding: 0.4rem 0.9rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid rgba(59, 130, 246, 0.2);
        backdrop-filter: blur(10px);
    }

    .skill-tag:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .talent-actions {
        margin-top: 1.5rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--accent-color), #8b5cf6);
        color: white;
        font-weight: 600;
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        min-width: 140px;
        justify-content: center;
    }

    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        text-decoration: none;
        color: white;
    }

    .btn-action i {
        font-size: 0.9rem;
    }

    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
        animation: fadeIn 0.6s ease-out;
    }

    .no-results i {
        font-size: 4rem;
        color: var(--accent-color);
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .no-results h3 {
        font-size: 1.5rem;
        margin: 1rem 0;
        color: var(--text-primary);
    }

    .no-results p {
        font-size: 1.1rem;
        max-width: 500px;
        margin: 0 auto;
        line-height: 1.6;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin: 2rem 0;
    }

    .page-link {
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

    .page-link:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
        transform: translateY(-2px);
    }

    .page-link.active {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }

    .page-link.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    .pagination-link-left{padding-left: 0.15rem;}
    .pagination-link-right{padding-right: 0.15rem;}

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .talent-search-container {
            padding: 1rem;
        }

        .search-title {
            font-size: 2rem;
        }

        .search-form {
            margin-bottom: 2rem;
        }

        .search-input-wrapper {
            flex-direction: column;
            gap: 0.5rem;
            padding: 0.5rem;
        }

        .search-btn {
            width: 100%;
            justify-content: center;
        }

        .results-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .talent-card {
            padding: 1.5rem;
        }

        .talent-actions {
            flex-direction: column;
            gap: 0.8rem;
        }
        .pagination{
            gap: 6px;
        }
        .page-link {
            min-width: 5px;
            height: 30px;
            padding: 0 7px;
            border-radius: 5px;
        }
    }

    @media (max-width: 480px) {
        .search-input {
            padding-left: 1rem;
        }

        .search-icon {
            display: none;
        }
        .pagination-link-left,
        .pagination-link-right{display:none;}
    }
</style>

<div class="talent-search-container">
    <div class="search-header">
        <h1 class="search-title">Find Top Talent</h1>
        <p class="search-subtitle">Discover exceptional candidates by name, location, skills, or expertise</p>
    </div>

    <form class="search-form" method="GET">
        <div class="search-input-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input 
                type="search" 
                name="q" 
                class="search-input" 
                placeholder="Search for talent... (e.g., Rajan, Kathmandu, Python, React)" 
                value="<?php echo htmlspecialchars($search_term); ?>"
                autocomplete="off"
            >
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
                Search
            </button>
        </div>
    </form>

    <?php if (isset($_GET['q']) && !empty($search_term)): ?>
        <div class="results-section">
            <?php if (!empty($search_results)): ?>
                <div class="results-header">
                    <h2 class="results-count">
                        Found <?php echo count($search_results); ?> 
                        <?php echo count($search_results) === 1 ? 'candidate' : 'candidates'; ?> 
                        for "<?php echo htmlspecialchars($search_term); ?>"
                    </h2>
                </div>

                <div class="results-grid">
                    <?php foreach ($search_results as $candidate): ?>
                        <div class="talent-card">
                            <div class="talent-avatar-wrapper">
                                <img src="<?php echo BASE_URL . ($candidate['profile_image'] ?? 'assets/images/default-avatar.png'); ?>" 
                                     class="talent-avatar" 
                                     alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                            </div>

                            <h3 class="talent-name">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($candidate['name']); ?>
                            </h3>

                            <div class="talent-details">
                                <?php if (!empty($candidate['location'])): ?>
                                    <div class="talent-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($candidate['location']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($candidate['phone'])): ?>
                                    <div class="talent-info">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($candidate['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="talent-skills">
                                <?php if (!empty($candidate['skills_cache'])): ?>
                                    <?php $skills = array_slice(explode(',', $candidate['skills_cache']), 0, 5); ?>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="skill-tag"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="skill-tag" style="opacity: 0.5;">No skills listed</span>
                                <?php endif; ?>
                            </div>

                            <div class="talent-actions">
                                <a href="profile.php?id=<?php echo $candidate['id']; ?>" class="btn-action">
                                    <i class="fas fa-user-circle"></i>
                                    View Profile
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo $page - 1; ?>" class="page-link">&laquo; <span class="pagination-link-left">Prev</span></a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo $i; ?>" 
                        class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?q=<?php echo urlencode($search_term); ?>&page=<?php echo $page + 1; ?>" class="page-link"><span class="pagination-link-right">Next</span> &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search-minus"></i>
                    <h3>No Results Found</h3>
                    <p>We couldn't find any talent matching your search for "<strong><?php echo htmlspecialchars($search_term); ?></strong>". Try adjusting your search terms or browse all available candidates.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>