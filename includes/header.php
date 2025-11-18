<?php
    require_once __DIR__ . '/session_handler.php';
    require_once __DIR__ . '/config.php';

    $is_logged_in = isset($_SESSION['user_id']);

    $unread_notifications = 0;
    if ($is_logged_in) {
        require_once __DIR__ . '/db_connect.php';
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $count_stmt->execute([$_SESSION['user_id']]);
        $unread_notifications = $count_stmt->fetchColumn();
    }

    $profile_avatar_url = BASE_URL . 'assets/images/default-avatar.png';
    if ($is_logged_in && !empty($_SESSION['user_image'])) {
        $profile_avatar_url = BASE_URL . htmlspecialchars($_SESSION['user_image']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Khojsuru' : 'Khojsuru'; ?></title>
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/favicon.png" type="image/x-icon">
    
    <!-- Fonts, Icons, Stylesheet -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <style>
        .header-right { display: flex; align-items: center; gap: 1rem; }
        .header-cta a { display: none; }

        /* --- UNIFIED Profile Dropdown (Desktop) --- */
        .profile-dropdown { position: relative; display: none; }
        .profile-btn { background: none; border: none; cursor: pointer; padding: 0; display: block; position: relative; }
        .header-avatar {
            width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
            border: 2px solid var(--border-color); transition: border-color 0.2s ease;
        }
        .profile-btn:hover .header-avatar { border-color: var(--accent-color); }
        .notification-badge {
            position: absolute; top: -2px; right: -2px;
            background: var(--error-color); color: white;
            width: 18px; height: 18px; border-radius: 50%;
            font-size: 0.7em; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--secondary-bg);
        }
        .notification-badge-mobile {
            position: absolute;
            top: 0.65rem;
            right: 1.3rem;
            background: var(--error-color);
            padding: 0.15rem;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.45em;
            font-weight: bold;
            display: flex
        ;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--secondary-bg);
        }

        .dropdown-menu {
            display: none; position: absolute; top: 120%; right: 0;
            background: var(--secondary-bg); border: 1px solid var(--border-color);
            border-radius: 8px; width: 250px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1002; padding: 0.5rem;
        }
        .dropdown-menu.active { display: block; }
        .dropdown-menu a {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1rem; color: var(--text-secondary);
            border-radius: 6px; font-weight: 500;
        }
        .dropdown-menu a:hover { background-color: var(--accent-color); color: white; }
        .dropdown-divider { height: 1px; background: var(--border-color); margin: 0.5rem 0; }


        /* .notifications-bell { position: relative; }
        .notification-badge {
            position: absolute; top: -5px; right: -5px;
            background: var(--error-color); color: white;
            width: 18px; height: 18px; border-radius: 50%;
            font-size: 0.7em; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
        }
        .notifications-dropdown {
            display: none;
            position: absolute;
            top: 120%;
            right: 0;
            background: var(--secondary-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1002;
        }
        .notifications-dropdown.active { display: block; }
        .notification-item {
            display: block;
            padding: 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
        }
        .notification-item:hover { background: var(--primary-bg); }
        .notification-item.unread { background: rgba(59, 130, 246, 0.1); }
        .notification-footer { display: block; text-align: center; padding: 1rem; font-weight: 500; } */


        
        /* --- Mobile Nav Overlay --- */
        .hamburger-btn { display: none; border: none; background: none; font-size: 2rem; color: var(--text-secondary);}
        .mobile-nav-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh;
            background: var(--primary-bg);
            display: flex; flex-direction: column; padding: 1rem;
            transform: translateX(-100%); transition: transform 0.3s ease-in-out;
            z-index: 1000; box-sizing: border-box;
        }
        .mobile-nav-overlay.active { transform: translateX(0); }
        .mobile-nav-header { display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; }
        .mobile-nav-user { display: flex; align-items: center; gap: 1rem; }
        .mobile-nav-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .mobile-nav-user h4 { margin: 0; font-size:1.5rem;}
        .mobile-nav-links { margin-top: 1rem; }
        .mobile-nav-links a { 
            display: block;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            border-radius: 6px;
            font-weight: 500;
            font-size: 1.2rem;
        }
        .mobile-nav-links .divider { height: 1px; background: var(--border-color); margin: 1rem 0; }
        .dropdown-menu a i {
            margin-right: 0.5rem;
            width: 16px;
            text-align: center;
        }


        
        /* --- Main Navigation Bar (Logged-out users) --- */
        .header-nav { display: none; }
        .header-nav ul { list-style: none; padding: 0; margin: 0; display: flex; gap: 0.5rem; }
        .header-nav ul a { color: var(--text-secondary); padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500; }
        .header-nav ul a:hover { background-color: var(--accent-color); color: white; }
        .header-nav ul a.btn-primary { background-color: var(--accent-color); color: white; }
        .btn-primary:hover{background-color:#235ee1;}
        .header-cta a {padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500;}
        /* --- MEDIA QUERIES --- */
        @media (max-width: 767px) {
            .header-cta { display: none; }
            <?php if ($is_logged_in): ?>
                .hamburger-btn { display: block; }
            <?php else: ?>
                .header-nav { display: block; }
            <?php endif; ?>
        }
        @media (min-width: 768px) {
            .header-cta a { display: inline-block; }
            .hamburger-btn { display: none; }
            <?php if ($is_logged_in): ?>
                .profile-dropdown { display: block; }
            <?php else: ?>
                .header-nav { display: block; }
            <?php endif; ?>
        }
        @media (max-width: 480px) {            
            .logo img {
                height: 40px;
            }
            .logo>a>span {
                display:none;
            }
            .login-form{
                padding: 0rem !important;
            }
            .site-header {padding: 1rem;}
        }
    </style>
</head>
<body>

<header class="site-header">
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Khojsuru Logo">
                    <span>Khojsuru</span>
                </a>
            </div>
        </div>
        
        <div class="header-right">
            <button class="theme-toggle-btn" id="theme-toggle" title="Toggle Theme"><i class="fas fa-sun"></i></button>

            <?php if ($is_logged_in): ?>
                <div class="header-cta">
                    <?php if ($_SESSION['user_type'] === 'recruiter'): ?>
                        <a href="<?php echo BASE_URL; ?>post_job.php" class="btn-primary">Post a Job</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>dashboard.php" class="btn-primary">Manage CVs</a>
                    <?php endif; ?>
                </div>

                <!-- DESKTOP: UNIFIED Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="profile-btn" id="profile-menu-btn">
                        <img src="<?php echo $profile_avatar_url; ?>" alt="My Profile" class="header-avatar">
                        <?php if ($unread_notifications > 0): ?>
                            <span class="notification-badge" id="notification-badge-desktop"><?php echo $unread_notifications; ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu" id="profile-dropdown-menu"> 
                        <a href="<?php echo BASE_URL; ?>"><i class="fa-solid fa-house"></i> Home</a>
                        <a href="<?php echo BASE_URL; ?>dashboard.php"><i class="fa-solid fa-user"></i> Profile</a>
                        <?php if ($_SESSION['user_type'] === 'recruiter'): ?>
                            <a href="<?php echo BASE_URL; ?>find_talent.php"><i class="fa-solid fa-search"></i> Find Talent</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>notifications.php"><i class="fa-solid fa-bell"></i> Notifications <?php if ($unread_notifications > 0) echo "($unread_notifications)"; ?></a>
                        <a href="<?php echo BASE_URL; ?>blog.php"><i class="fa-solid fa-user"></i> Blog</a>
                        <a href="<?php echo BASE_URL; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo BASE_URL; ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>

                <!-- MOBILE: Hamburger Button -->
                <button class="hamburger-btn" id="hamburger-btn"><i class="fas fa-bars"></i>
                    <?php if ($unread_notifications > 0): ?>
                        <span class="notification-badge-mobile" id="notification-badge-desktop"><?php echo $unread_notifications; ?></span>
                    <?php endif; ?>
                </button>

            <?php else: ?>
                <!-- LOGGED-OUT VIEW -->
                <nav class="header-nav">
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>login.php">Login</a></li>
                        <li><a href="<?php echo BASE_URL; ?>signup.php" class="btn-primary">Sign Up</a></li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($is_logged_in): ?>
<!-- MOBILE NAVIGATION OVERLAY -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay">
    <div class="mobile-nav-header">
        <div class="mobile-nav-user">
            <img src="<?php echo $profile_avatar_url; ?>" alt="My Profile" class="mobile-nav-avatar">
            <h4><?php echo htmlspecialchars($_SESSION['user_name']); ?></h4>
        </div>
        <button class="hamburger-btn" id="hamburger-close-btn" style="display:block;"><i class="fas fa-times"></i></button>
    </div>
    <nav class="mobile-nav-links">
        <a href="<?php echo BASE_URL; ?>"><i class="fa-solid fa-house"></i> Home</a>
        <a href="<?php echo BASE_URL; ?>dashboard.php"><i class="fa-solid fa-user"></i> Profile</a>
        <?php if ($_SESSION['user_type'] === 'recruiter'): ?>
            <a href="<?php echo BASE_URL; ?>find_talent.php"><i class="fa-solid fa-search"></i> Find Talent</a>
        <?php endif; ?>
        <a href="<?php echo BASE_URL; ?>notifications.php"><i class="fa-solid fa-bell"></i> Notifications <?php if ($unread_notifications > 0) echo "($unread_notifications)"; ?></a>
        <a href="<?php echo BASE_URL; ?>blog.php"><i class="fa-solid fa-user"></i> Blog</a>
        <a href="<?php echo BASE_URL; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
        <div class="divider"></div>
        <a href="<?php echo BASE_URL; ?>logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</div>
<?php endif; ?>

<main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Hamburger Menu Logic ---
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const hamburgerCloseBtn = document.getElementById('hamburger-close-btn');
    const mobileNavOverlay = document.getElementById('mobile-nav-overlay');

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            mobileNavOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    if (hamburgerCloseBtn) {
        hamburgerCloseBtn.addEventListener('click', () => {
            mobileNavOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // --- Profile Dropdown Logic ---
    const profileBtn = document.getElementById('profile-menu-btn');
    const profileDropdownMenu = document.getElementById('profile-dropdown-menu');

    if (profileBtn) {
        profileBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            profileDropdownMenu.classList.toggle('active');
        });
    }

    // Close dropdown if clicking outside
    window.addEventListener('click', (event) => {
        if (profileDropdownMenu && profileDropdownMenu.classList.contains('active')) {
            if (!profileBtn.contains(event.target)) {
                 profileDropdownMenu.classList.remove('active');
            }
        }
    });
});
</script>