<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';

    $error_message = '';
    $success_message = $_SESSION['success_message'] ?? '';
    if (isset($_SESSION['success_message'])) {
        unset($_SESSION['success_message']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error_message = "Please enter both email and password.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // CRITICAL CHECK 2: Is the account suspended?
                if ($user['is_active'] == 0) {
                    $error_message = "Your account has been suspended by an administrator. Please contact us at support@khojsuru.com";
                } 
                // All checks passed
                else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_type'] = $user['user_type'];
                    // ... rest of your session variables
                    $_SESSION['user_image'] = $user['profile_image'];
                    $_SESSION['user_email'] = $user['email'];

                    if ($user['user_type'] === 'admin') {
                        header("Location: " . BASE_URL . "admin/index.php");
                    } else {
                        header("Location: " . BASE_URL . "index.php");
                    }
                    exit();
                }

            } else {
                $error_message = "Invalid email or password.";
            }
        }
    }

    $page_title = "Sign In";
    include_once __DIR__ . '/includes/header.php';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap" rel="stylesheet">
<style>
    main{
        padding: 0px;
    }
    .login-container {
        display: flex;
        background: linear-gradient(135deg, var(--primary-bg) 0%, var(--secondary-bg) 100%);
        min-height: 90vh;
        justify-content: center;
        flex-direction: row-reverse;
    }

    .recruitment-section {
        flex: 1;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
        background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
    }

    .recruitment-hub {
        position: relative;
        width: 100%;
        max-width: 320px;
        height: 320px;
    }
    .recruitment-title{
        font-family: "Cedarville Cursive", cursive;
        font-weight: 400;
        font-style: italic;
        position: absolute;
        top: -4.25rem;
        left: 1rem;
        font-size: xxx-large;
        z-index: 2;
        color: #10b981;
    }

    /* Job Cards - Mobile Optimized */
    .job-card {
        position: absolute;
        width: 120px;
        height: 70px;
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 0.7rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        animation: float 6s ease-in-out infinite;
        cursor: pointer;
    }

    .job-card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
    }

    .job-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.2rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .job-company {
        color: var(--text-secondary);
        font-size: 0.65rem;
        margin-bottom: 0.2rem;
    }

    .job-salary {
        color: var(--success-color);
        font-size: 0.6rem;
        font-weight: 500;
    }

    /* CV Cards - Mobile Optimized */
    .cv-card {
        position: absolute;
        width: 80px;
        height: 100px;
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.4rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        animation: float 5s ease-in-out infinite;
        cursor: pointer;
    }

    .cv-card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
    }

    .cv-avatar {
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, var(--success-color), #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }

    .cv-name {
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.2rem;
    }

    .cv-role {
        font-size: 0.55rem;
        color: var(--text-secondary);
        line-height: 1.2;
    }

    /* Connection Lines */
    .connection-line {
        position: absolute;
        height: 2px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            var(--accent-color) 30%,
            var(--success-color) 70%,
            transparent 100%);
        transform-origin: left center;
        animation: connectionFlow 4s ease-in-out infinite;
        opacity: 0.7;
    }

    /* ATS Badge - Mobile Position */
    .ats-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: linear-gradient(135deg, var(--success-color), #059669);
        color: white;
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        animation: atsPulse 2s ease-in-out infinite;
    }

    /* Stats Display - Mobile */
    .stats-display {
        position: absolute;
        bottom: 0rem;
        left: 0.5rem;
        background: var(--secondary-bg);
        padding: 0.6rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        min-width: 140px;
        font-size: 0.7rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.3rem;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-label {
        color: var(--text-secondary);
    }

    .stat-value {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Login Form Section */
    .login-section {
        padding: 1rem;
        display: flex;
        flex:1;
        align-items: center;
        justify-content: center;
    }

    .login-form {
        width: 100%;
        max-width: 400px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .form-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-header p {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .password-wrapper {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 1rem;
        transition: color 0.3s ease;
        padding: 0.25rem;
    }

    .toggle-password:hover {
        color: var(--text-primary);
    }

    .forgot-password-link {
        display: block;
        text-align: right;
        margin-top: 0.5rem;
        color: var(--accent-color);
        text-decoration: none;
        font-size: 0.8rem;
        transition: opacity 0.3s ease;
    }

    .forgot-password-link:hover {
        opacity: 0.8;
    }

    .btn-submit {
        transition: all 0.3s ease;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
    }

    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .auth-switch-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid var(--border-color);
    }

    .auth-switch-link a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
    }

    /* Message Styles */
    .message {
        padding: 0.875rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .error-message {
        background: rgba(239, 68, 68, 0.1);
        color: var(--error-color);
        border: 1px solid var(--error-color);
    }

    .success-message {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        border: 1px solid var(--success-color);
    }

    /* Animations */
    @keyframes centralPulse {
        0%, 100% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 30px rgba(59, 130, 246, 0.6); }
        50% { transform: translate(-50%, -50%) scale(1.1); box-shadow: 0 0 40px rgba(59, 130, 246, 0.8); }
    }

    @keyframes connectionFlow {
        0% { opacity: 0; transform: scaleX(0); }
        50% { opacity: 0.7; transform: scaleX(1); }
        100% { opacity: 0; transform: scaleX(1); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    @keyframes atsPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes slideInUp {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    @keyframes slideInDown {
        from { transform: translateY(-100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Tablet Styles */
    @media (min-width: 1000px) {
        .recruitment-section {
            display: flex;
            background: var(--secondary-bg);
            border-right: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }
    }
    @media (min-width: 768px) {
        .recruitment-hub {
            max-width: 420px;
            height: 420px;
        }

        .job-card {
            width: 140px;
            height: 80px;
            padding: 0.75rem;
            font-size: 0.75rem;
        }

        .job-title {
            font-size: 0.85rem;
        }

        .job-company {
            font-size: 0.7rem;
        }

        .job-salary {
            font-size: 0.65rem;
        }

        .cv-card {
            width: 90px;
            height: 110px;
            padding: 0.5rem;
        }

        .cv-avatar {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }

        .cv-name {
            font-size: 0.7rem;
        }

        .cv-role {
            font-size: 0.6rem;
        }

        .login-form {
            padding: 2.5rem;
            max-width: 450px;
        }

        .form-header h1 {
            font-size: 1.75rem;
        }

        .form-header p {
            font-size: 1rem;
        }

        .stats-display {
            min-width: 180px;
            padding: 0.8rem;
            font-size: 0.8rem;
        }

        .ats-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
        }
    }

    /* Desktop Styles */
    @media (min-width: 1024px) {
        .login-container {
            align-items: stretch;
        }

        .recruitment-section {
            flex: 1;
            padding: 4rem;
        }

        .login-section {
            flex: 1;
            padding: 4rem 2rem;
        }

        .recruitment-hub {
            max-width: 500px;
            height: 500px;
        }

        .job-card {
            width: 160px;
            height: 90px;
            padding: 0.875rem;
            font-size: 0.8rem;
        }

        .job-title {
            font-size: 0.9rem;
        }

        .job-company {
            font-size: 0.75rem;
        }

        .job-salary {
            font-size: 0.7rem;
        }

        .cv-card {
            width: 100px;
            height: 120px;
            padding: 0.6rem;
        }

        .cv-avatar {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .cv-name {
            font-size: 0.75rem;
        }

        .cv-role {
            font-size: 0.65rem;
        }

        .login-form {
            padding: 3rem;
            max-width: 500px;
        }

        .form-header h1 {
            font-size: 2rem;
        }

        .stats-display {
            min-width: 200px;
            padding: 1rem;
            font-size: 0.85rem;
        }

        .ats-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }
    }

    /* Light theme adjustments */
    body.light-theme .job-card,
    body.light-theme .cv-card,
    body.light-theme .stats-display {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    body.light-theme .form-input {
        background: rgba(0, 0, 0, 0.02);
    }

    body.light-theme .theme-toggle:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<div class="login-container">
    <!-- Login Form Section -->
    <div class="login-section">
        <form class="login-form" method="POST" action="login.php">
            <div class="form-header">
                <h1>Welcome Back!</h1>
                <p>Sign in to continue to Khojsuru</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if($error_message): ?>
                <div class="error-message message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if($success_message): ?>
                <div class="success-message message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    required 
                    placeholder="Enter your email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password">
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <a href="forgot-password.php" class="forgot-password-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>

            <div class="auth-switch-link">
                <p>New to Khojsuru? <a href="signup.php">Join now</a></p>
            </div>
        </form>
    </div>
    
    <!-- Recruitment Hub Section -->
    <div class="recruitment-section">
        <div class="recruitment-hub">
            <div class="recruitment-title">
                join us
            </div>
            <!-- Job Cards -->
            <div class="job-card" style="top: 8%; left: 65%; animation-delay: 0s;">
                <div class="job-title">Senior Developer</div>
                <div class="job-company">TechCorp Inc.</div>
                <div class="job-salary">$120k - $150k</div>
            </div>

            <div class="job-card" style="top: 25%; left: 80%; animation-delay: 1s;">
                <div class="job-title">UI/UX Designer</div>
                <div class="job-company">Design Studio</div>
                <div class="job-salary">$80k - $100k</div>
            </div>

            <div class="job-card" style="bottom: 15%; left: 75%; animation-delay: 2s;">
                <div class="job-title">Data Scientist</div>
                <div class="job-company">AI Solutions</div>
                <div class="job-salary">$130k - $180k</div>
            </div>

            <div class="job-card" style="top: 75%; left: 8%; animation-delay: 3s;">
                <div class="job-title">Product Manager</div>
                <div class="job-company">StartupXYZ</div>
                <div class="job-salary">$110k - $140k</div>
            </div>

            <!-- CV Cards -->
            <div class="cv-card" style="top: 5%; left: 20%; animation-delay: 0.5s;">
                <div class="cv-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="cv-name">Samip Shrestha</div>
                <div class="cv-role">Full Stack Dev</div>
            </div>

            <div class="cv-card" style="top: 40%; left: 8%; animation-delay: 1.5s;">
                <div class="cv-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="cv-name">Sujan Khadka</div>
                <div class="cv-role">UX Designer</div>
            </div>

            <div class="cv-card" style="bottom: 8%; left: 45%; animation-delay: 2.5s;">
                <div class="cv-avatar">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="cv-name">Simran Magar</div>
                <div class="cv-role">Data Analyst</div>
            </div>

            <div class="cv-card" style="top: 30%; right: 26%; animation-delay: 3.5s;">
                <div class="cv-avatar">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="cv-name">Abishek Kafle</div>
                <div class="cv-role">DevOps Engineer</div>
            </div>

            <!-- Connection Lines -->
            <div class="connection-line" style="top: 20%; left: 30%; width: 25%; transform: rotate(25deg); animation-delay: 1s;"></div>
            <div class="connection-line" style="top: 50%; left: 25%; width: 30%; transform: rotate(-15deg); animation-delay: 2s;"></div>
            <div class="connection-line" style="top: 45%; left: 70%; width: 20%; transform: rotate(45deg); animation-delay: 3s;"></div>
            <div class="connection-line" style="bottom: 40%; left: 20%; width: 25%; transform: rotate(15deg); animation-delay: 4s;"></div>

            <!-- ATS Badge -->
            <div class="ats-badge">
                <i class="fas fa-robot" style="margin-right: 0.3rem;"></i>
                ATS Friendly
            </div>

            <!-- Stats Display -->
            <div class="stats-display">
                <div class="stat-item">
                    <span class="stat-label">Active Jobs</span>
                    <span class="stat-value" id="activeJobs">47</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">CVs Created</span>
                    <span class="stat-value" id="cvsCreated">692</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Matches Today</span>
                    <span class="stat-value" id="matchesToday">12</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password Toggle Functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            togglePassword.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    }

    // Form Enhancement and Validation
    const form = document.querySelector('.login-form');
    const inputs = form.querySelectorAll('.form-input');
    const submitBtn = document.getElementById('submitBtn');

    // Add focus/blur effects to inputs
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.style.transform = 'scale(1.02)';
            input.parentElement.style.transition = 'transform 0.3s ease';
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.style.transform = 'scale(1)';
        });

        // Real-time validation feedback
        input.addEventListener('input', () => {
            if (input.validity.valid) {
                input.style.borderColor = 'var(--success-color)';
            } else if (input.value.length > 0) {
                input.style.borderColor = 'var(--error-color)';
            } else {
                input.style.borderColor = 'var(--border-color)';
            }
        });
    });

    // Enhanced form submission with loading state
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        // Client-side validation
        if (!email || !password) {
            e.preventDefault();
            showNotification('Please enter both email and password.', 'error');
            return;
        }

        if (!isValidEmail(email)) {
            e.preventDefault();
            showNotification('Please enter a valid email address.', 'error');
            return;
        }

        // Show loading state
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
        submitBtn.disabled = true;
        
        // Re-enable after timeout (in case of server issues)
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }
        }, 10000);
    });

    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Notification system
    function showNotification(message, type) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = `notification ${type}-message message`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
            max-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Dynamic stats animation
    function animateStats() {
        const stats = {
            activeJobs: { element: document.getElementById('activeJobs'), target: 2847, current: 0 },
            cvsCreated: { element: document.getElementById('cvsCreated'), target: 15692, current: 0 },
            matchesToday: { element: document.getElementById('matchesToday'), target: 156, current: 0 }
        };

        Object.keys(stats).forEach(key => {
            const stat = stats[key];
            const increment = stat.target / 100;
            const timer = setInterval(() => {
                stat.current += increment;
                if (stat.current >= stat.target) {
                    stat.current = stat.target;
                    clearInterval(timer);
                }
                stat.element.textContent = Math.floor(stat.current).toLocaleString();
            }, 50);
        });
    }

    // Add more connection lines dynamically
    const recruitmentHub = document.querySelector('.recruitment-hub');
    
    for (let i = 0; i < 3; i++) {
        const connectionLine = document.createElement('div');
        connectionLine.className = 'connection-line';
        connectionLine.style.top = Math.random() * 60 + 20 + '%';
        connectionLine.style.left = Math.random() * 40 + 20 + '%';
        connectionLine.style.width = Math.random() * 25 + 15 + '%';
        connectionLine.style.transform = `rotate(${Math.random() * 60 - 30}deg)`;
        connectionLine.style.animationDelay = Math.random() * 5 + 's';
        recruitmentHub.appendChild(connectionLine);
    }

    // Job matching simulation
    function simulateJobMatching() {
        const jobCards = document.querySelectorAll('.job-card');
        const cvCards = document.querySelectorAll('.cv-card');
        
        setInterval(() => {
            const randomJob = jobCards[Math.floor(Math.random() * jobCards.length)];
            const randomCV = cvCards[Math.floor(Math.random() * cvCards.length)];
            
            // Add matching glow effect
            randomJob.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.6)';
            randomCV.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.6)';
            
            setTimeout(() => {
                randomJob.style.boxShadow = '';
                randomCV.style.boxShadow = '';
            }, 2000);
        }, 8000);
    }

    // Interactive hover effects for cards
    const allCards = document.querySelectorAll('.job-card, .cv-card');
    allCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.zIndex = '100';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.zIndex = '';
        });
    });

    // Keyboard navigation support
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.tagName === 'BUTTON') {
            e.target.click();
        }
    });

    // Start animations when page loads
    setTimeout(() => {
        animateStats();
        simulateJobMatching();
    }, 1000);

    // Entrance animations
    // setTimeout(() => {
    //     form.style.animation = 'slideInUp 0.8s ease-out';
    //     document.querySelector('.recruitment-hub').style.animation = 'slideInDown 0.8s ease-out';
    // }, 100);

    // Add CSS for notification animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        .notification {
            cursor: pointer;
        }
        
        .notification:hover {
            transform: scale(1.02);
        }
    `;
    document.head.appendChild(style);

    // Make notifications clickable to dismiss
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('notification')) {
            e.target.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => e.target.remove(), 300);
        }
    });

    // Add loading overlay for better UX during form submission
    function createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(3px);
        `;
        
        const spinner = document.createElement('div');
        spinner.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 3rem; color: var(--accent-color);"></i>';
        overlay.appendChild(spinner);
        
        return overlay;
    }

    // Handle PHP form errors/success messages with better styling
    const phpMessages = document.querySelectorAll('.message');
    phpMessages.forEach(message => {
        if (message.classList.contains('error-message')) {
            message.style.animation = 'slideInUp 0.5s ease-out';
        }
        if (message.classList.contains('success-message')) {
            message.style.animation = 'slideInUp 0.5s ease-out';
            // Auto-hide success messages after 3 seconds
            setTimeout(() => {
                message.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => message.remove(), 300);
            }, 3000);
        }
    });

    // Performance optimization: Reduce animations on low-end devices
    // const isLowEndDevice = navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;
    // if (isLowEndDevice) {
    //     const style = document.createElement('style');
    //     style.textContent = `
    //         *, *::before, *::after {
    //             animation-duration: 0.5s !important;
    //             transition-duration: 0.5s !important;
    //         }
    //     `;
    //     document.head.appendChild(style);
    // }

    // Accessibility improvements
    const focusableElements = form.querySelectorAll('input, button, a');
    focusableElements.forEach((element, index) => {
        element.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const isShift = e.shiftKey;
                const nextIndex = isShift ? index - 1 : index + 1;
                
                if (nextIndex < 0 || nextIndex >= focusableElements.length) {
                    return; // Let default behavior handle wrapping
                }
            }
        });
    });

    // console.log('Khojsuru Login: Enhanced login page loaded successfully!');
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>