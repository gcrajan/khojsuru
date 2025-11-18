<?php
    $page_title = "Admin Dashboard";
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db_connect.php';
    require_once __DIR__ . '/../includes/session_handler.php';
    require_admin();

    // --- Fetch Stats ---
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type != 'admin'")->fetchColumn();
    $total_recruitees = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'recruitee'")->fetchColumn();
    $total_recruiters = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'recruiter'")->fetchColumn();
    $total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
    $total_applications = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
    $total_blogs = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
    $today_signups = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $active_jobs = $pdo->query("SELECT COUNT(*) FROM jobs WHERE is_active = 1")->fetchColumn();
    $pending_applications = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'submitted'")->fetchColumn();
    
    // --- CHART DATA PREPARATION ---
    // 1. Signups for the Last 30 Days (Separated by Role)
    $recruitee_signups_db = $pdo->query("SELECT DATE(created_at) as day, COUNT(*) as count FROM users WHERE user_type = 'recruitee' AND created_at >= CURDATE() - INTERVAL 29 DAY GROUP BY day")->fetchAll(PDO::FETCH_KEY_PAIR);
    $recruiter_signups_db = $pdo->query("SELECT DATE(created_at) as day, COUNT(*) as count FROM users WHERE user_type = 'recruiter' AND created_at >= CURDATE() - INTERVAL 29 DAY GROUP BY day")->fetchAll(PDO::FETCH_KEY_PAIR);

    $thirty_day_labels = [];
    $recruitee_values = [];
    $recruiter_values = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = date("Y-m-d", strtotime("-{$i} days"));
        $thirty_day_labels[] = date("M d", strtotime($date));
        $recruitee_values[] = $recruitee_signups_db[$date] ?? 0;
        $recruiter_values[] = $recruiter_signups_db[$date] ?? 0;
    }

    // 2. Applications for the Last 6 Months (applications.application_date)
    $apps_data_from_db = $pdo->query(
        "SELECT DATE_FORMAT(application_date, '%Y-%m') as app_month, COUNT(*) as count 
        FROM applications 
        WHERE application_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
        GROUP BY DATE_FORMAT(application_date, '%Y-%m')"
    )->fetchAll(PDO::FETCH_KEY_PAIR);

    $app_labels = [];
    $app_values = [];
    for ($i = 5; $i >= 0; $i--) {
        $month_key = date("Y-m", strtotime("-{$i} months"));
        $app_labels[] = date("M Y", strtotime($month_key . "-01"));
        $app_values[] = $apps_data_from_db[$month_key] ?? 0;
    }

    // --- NEW: Recent Activity Feed ---
    $recent_activity_stmt = $pdo->query(
    "(SELECT 'signup' as type, name, '' as content, created_at, user_type FROM users WHERE user_type != 'admin' ORDER BY created_at DESC LIMIT 3)
     UNION ALL
     (SELECT 'job' as type, u.name, j.title as content, j.posted_at as created_at, '' as user_type FROM jobs j JOIN users u ON j.recruiter_user_id = u.id ORDER BY j.posted_at DESC LIMIT 3)
     UNION ALL
     (SELECT 'application' as type, u.name, j.title as content, a.application_date as created_at, '' as user_type FROM applications a JOIN users u ON a.recruitee_user_id = u.id JOIN jobs j ON a.job_id = j.id ORDER BY a.application_date DESC LIMIT 3)
     ORDER BY created_at DESC LIMIT 5"
    );
    $recent_activity = $recent_activity_stmt->fetchAll();
    require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .chart-card canvas {
    max-height: 280px; /* ✅ locks height */
}

    /* Admin Dashboard Styles */
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
        background: var(--primary-bg);
        min-height: 100vh;
    }

    .admin-header {
        margin-bottom: 3rem;
        text-align: center;
    }

    .admin-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem;
        background: linear-gradient(135deg, var(--accent-color), #06b6d4);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .admin-header p {
        color: var(--text-secondary);
        font-size: 1.1rem;
        margin: 0;
    }

    .welcome-card {
        background: linear-gradient(135deg, var(--accent-color), #06b6d4);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 3rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(50px, -50px);
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-content h2 {
        font-size: 1.8rem;
        margin: 0 0 0.5rem;
        font-weight: 600;
    }

    .welcome-content p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--secondary-bg);
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient, var(--accent-color));
    }

    .stat-card.users::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
    .stat-card.jobs::before { background: linear-gradient(90deg, #10b981, #047857); }
    .stat-card.applications::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .stat-card.blogs::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-card.users .stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-card.jobs .stat-icon { background: linear-gradient(135deg, #10b981, #047857); }
    .stat-card.applications .stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card.blogs .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    .stat-title {
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 600;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0.5rem 0;
    }

    .stat-change {
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-change.positive {
        color: var(--success-color);
    }

    .stat-change.neutral {
        color: var(--text-secondary);
    }

    /* Charts and Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .chart-card {
        background: var(--secondary-bg);
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .chart-period {
        font-size: 0.875rem;
        color: var(--text-secondary);
        background: var(--primary-bg);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    /* Recent Activity */
    .activity-card {
        background: var(--secondary-bg);
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .activity-header {
        margin-bottom: 1.5rem;
    }

    .activity-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .activity-item { /* Replaces user-item */
        display: flex; gap: 1rem; padding: 1rem;
        /* ... other styles ... */
    }
    .activity-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; color: white;
    }
    .activity-text { font-size: 0.95rem; }
    .activity-time { font-size: 0.8em; color: var(--text-secondary); margin-top: 0.25rem; }

    .user-list {
        space-y: 1rem;
    }

    .user-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--primary-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .user-item:hover {
        transform: translateX(4px);
        border-color: var(--accent-color);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.25rem;
        font-size: 0.95rem;
    }

    .user-email {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .user-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .user-badge.recruitee {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        border: 1px solid var(--success-color);
    }

    .user-badge.recruiter {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent-color);
        border: 1px solid var(--accent-color);
    }

    .user-date {
        font-size: 0.75rem;
        color: var(--text-secondary);
        text-align: right;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.1);
        border-color: var(--accent-color);
    }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        background: var(--accent-color);
    }

    .action-text {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
        }
        
        .admin-header h1 {
            font-size: 2rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .welcome-card {
            padding: 1.5rem;
        }
        
        .stat-card {
            padding: 1.5rem;
        }
        
        .chart-card,
        .activity-card {
            padding: 1.5rem;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }

    /* Chart Canvas Styling */
    canvas {
        background: transparent !important;
        border-radius: 8px;
    }

    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-top: 2px solid var(--accent-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transform: translate(-50%, -50%);
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>

<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back! Here's what's happening with RecruiterCV today.</p>
    </div>

    <!-- Quick Actions (Moved up for better UX) -->
    <div class="quick-actions">
        <a href="<?php echo BASE_URL; ?>admin/manage_users.php" class="action-btn">
            <div class="action-icon" style="background: #3b82f6;"><i class="fas fa-users"></i></div>
            <div class="action-text">Manage Users</div>
        </a>
        <a href="<?php echo BASE_URL; ?>admin/manage_jobs.php" class="action-btn">
            <div class="action-icon" style="background: #10b981;"><i class="fas fa-briefcase"></i></div>
            <div class="action-text">Manage Jobs</div>
        </a>
        <a href="<?php echo BASE_URL; ?>admin/manage_blogs.php" class="action-btn">
            <div class="action-icon" style="background: #8b5cf6;"><i class="fas fa-newspaper"></i></div>
            <div class="action-text">Manage Blog</div>
        </a>
        <a href="#" class="action-btn" style="opacity: 0.5; cursor: not-allowed;">
            <div class="action-icon" style="background: #64748b;"><i class="fas fa-cog"></i></div>
            <div class="action-text">Settings</div>
        </a>
    </div>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="welcome-content">
            <h2>Good <?php echo date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening'); ?>! 👋</h2>
            <p>You have <?php echo $today_signups; ?> new signups today and <?php echo $pending_applications; ?> applications pending review.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card users">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-change positive">+<?php echo $today_signups; ?> today</div>
            </div>
            <div class="stat-value"><?php echo number_format($total_users); ?></div>
            <h3 class="stat-title">Total Users</h3>
        </div>
        <div class="stat-card jobs">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                <div class="stat-change positive"><?php echo $active_jobs; ?> active</div>
            </div>
            <div class="stat-value"><?php echo number_format($total_jobs); ?></div>
            <h3 class="stat-title">Jobs Posted</h3>
        </div>
        <div class="stat-card applications">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-change neutral"><?php echo $pending_applications; ?> pending</div>
            </div>
            <div class="stat-value"><?php echo number_format($total_applications); ?></div>
            <h3 class="stat-title">Applications</h3>
        </div>
        <div class="stat-card blogs">
            <div class="stat-header">
                <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                <div class="stat-change positive"><?php echo $total_blogs; ?> published</div>
            </div>
            <div class="stat-value"><?php echo number_format($total_blogs); ?></div>
            <h3 class="stat-title">Blog Posts</h3>
        </div>
    </div>

    <!-- Charts and Activity -->
    <div class="content-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">User Signups</h3>
                <span class="chart-period">Last 30 Days</span>
            </div>
            <canvas id="signupsChart"></canvas>
        </div>

        <div class="activity-card">
            <div class="activity-header"><h3 class="activity-title">Recent Activity</h3></div>
            <div class="activity-list">
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-item">
                        <?php 
                            $icon = 'fa-bell'; $color = '#64748b';
                            if ($activity['type'] == 'signup') { $icon = 'fa-user-plus'; $color = '#3b82f6'; }
                            if ($activity['type'] == 'job') { $icon = 'fa-briefcase'; $color = '#10b981'; }
                            if ($activity['type'] == 'application') { $icon = 'fa-file-alt'; $color = '#f59e0b'; }
                        ?>
                        <div class="activity-icon" style="background: <?php echo $color; ?>;"><i class="fas <?php echo $icon; ?>"></i></div>
                        <div>
                            <div class="activity-text">
                                <?php if ($activity['type'] == 'signup'): ?>
                                    <strong><?php echo htmlspecialchars($activity['name']); ?></strong> signed up as a <?php echo $activity['user_type']; ?>.
                                <?php elseif ($activity['type'] == 'job'): ?>
                                    <strong><?php echo htmlspecialchars($activity['name']); ?></strong> posted a new job: "<?php echo htmlspecialchars($activity['content']); ?>".
                                <?php elseif ($activity['type'] == 'application'): ?>
                                    <strong><?php echo htmlspecialchars($activity['name']); ?></strong> applied for "<?php echo htmlspecialchars($activity['content']); ?>".
                                <?php endif; ?>
                            </div>
                            <div class="activity-time"><?php echo date('M d, h:i A', strtotime($activity['created_at'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Additional Charts -->
    <div class="content-grid">
         <div class="chart-card">
            <div class="chart-header"><h3 class="chart-title">Applications Trend</h3><span class="chart-period">Last 6 Months</span></div>
            <canvas id="applicationsChart"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-header"><h3 class="chart-title">User Distribution</h3></div>
            <canvas id="rolesChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get CSS custom property values
    const rootStyles = getComputedStyle(document.documentElement);
    const accentColor   = rootStyles.getPropertyValue('--accent-color').trim();
    const successColor  = rootStyles.getPropertyValue('--success-color').trim();
    const textSecondary = rootStyles.getPropertyValue('--text-secondary').trim();
    const textPrimary   = rootStyles.getPropertyValue('--text-primary').trim(); // ✅ FIXED

    Chart.defaults.color = textSecondary;
    Chart.defaults.font.family = "'Inter', sans-serif";

    // --- Signups Chart (Line) ---
    const signupsCtx = document.getElementById('signupsChart').getContext('2d');
    new Chart(signupsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($thirty_day_labels); ?>,
            datasets: [
                {
                    label: 'Recruitees',
                    data: <?php echo json_encode($recruitee_values); ?>,
                    borderColor: successColor,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Recruiters',
                    data: <?php echo json_encode($recruiter_values); ?>,
                    borderColor: accentColor,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: { legend: { display: true, position: 'top' } }
        }
    });


    // --- Roles Chart (Doughnut) ---
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Job Seekers', 'Recruiters'],
            datasets: [{
                data: [<?php echo $total_recruitees; ?>, <?php echo $total_recruiters; ?>],
                backgroundColor: [successColor, accentColor],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        color: textPrimary // ✅ FIXED
                    }
                }
            }
        }
    });

    // --- Applications Chart (Bar) ---
    const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
    new Chart(applicationsCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($app_labels); ?>,
            datasets: [{
                label: 'Applications',
                data: <?php echo json_encode($app_values); ?>,
                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                borderColor: '#f59e0b',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(156, 163, 175, 0.1)' },
                    ticks: { color: textSecondary }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textSecondary }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
});

// Auto-refresh every 5 minutes
setInterval(() => { location.reload(); }, 300000);
</script>


<?php include_once __DIR__ . '/../includes/footer.php'; ?>