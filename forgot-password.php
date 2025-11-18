<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/vendor/autoload.php';

    // Set timezone to UTC
    date_default_timezone_set('UTC');

    $error_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } else {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                // Generate secure reset token
                $token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // UTC

                // Insert reset request
                $ins_stmt = $pdo->prepare(
                    "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
                );
                $ins_stmt->execute([$email, $token_hash, $expires_at]);

                // Create reset link
                $reset_link = BASE_URL . 'reset-password.php?token=' . urlencode($token);

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = SMTP_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = SMTP_USER;
                    $mail->Password   = SMTP_PASS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = SMTP_PORT;

                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request for RecruiterCV';
                    $mail->Body    = "<h1>Password Reset</h1>
                                    <p>You requested a password reset. Click below to create a new password. 
                                    Link valid for 1 hour.</p>
                                    <a href='{$reset_link}'>Reset Your Password</a><p>If you don't allow the use of this email address to change password, ignore this email. Also, do not reply to this mail as we won't be replying back.</p>";
                    $mail->AltBody = "You requested a password reset. Visit this link: {$reset_link}";

                    $mail->send();
                } catch (Exception $e) {
                    // Optionally log $e->getMessage()
                }
            }
            $_SESSION['success_message'] = "If an account with that email exists, a password reset link has been sent.";
            header('Location: login.php');
            exit();
        }
    }

    $page_title = "Forgot Password: Khojsuru";
    require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Auth Layout Styles */
    .auth-layout {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-card {
        max-width: 480px;
        width: 100%;
        position: relative;
        overflow: hidden;
        background: none;
        padding: 0rem;
        border-radius: 0px;
        border: none;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .auth-header .icon {
        width: 64px;
        height: 64px;
        background: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 1.5rem;
    }

    .auth-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.75rem;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .auth-header p {
        font-size: 1rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.5;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        background: var(--secondary-bg);
        color: var(--text-primary);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-1px);
    }

    .form-input::placeholder {
        color: var(--text-secondary);
    }

    /* Button Styles */
    .btn-submit {
        transition: all 0.2s ease;
        margin-top: 0.5rem;
        position: relative;
        overflow: hidden;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        background: #2563eb;
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    /* Message Styles */
    .error-message {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--error-color);
        color: var(--error-color);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .error-message::before {
        content: '⚠️';
        font-size: 1.2rem;
    }

    .success-message {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid var(--success-color);
        color: var(--success-color);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .success-message::before {
        content: '✅';
        font-size: 1.2rem;
    }

    /* Auth Switch Link */
    .auth-switch-link {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .auth-switch-link p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .auth-switch-link a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .auth-switch-link a:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 640px) { 
        .auth-header h1 {
            font-size: 1.75rem;
        }
        
        .auth-header .icon {
            width: 56px;
            height: 56px;
            font-size: 1.3rem;
        }
    }

    /* Loading State */
    .btn-submit.loading {
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }

    .btn-submit.loading::after {
        content: '';
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        display: inline-block;
        animation: spin 1s linear infinite;
        margin-left: 0.5rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    body.light-theme .form-input {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    body.light-theme .form-input:focus {
        background: white;
    }
</style>

<div class="auth-layout">
    <div class="auth-card">
        <div class="auth-header">
            <div class="icon">🔐</div>
            <h1>Forgot Your Password?</h1>
            <p>No problem. Enter your email below and we'll send you a secure link to reset your password.</p>
        </div>

        <?php if($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="forgot-password.php" id="forgotPasswordForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="Enter your email address"
                    required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>
            <button type="submit" class="btn-submit" id="submitBtn">
                Send Reset Link
            </button>
        </form>

        <div class="auth-switch-link">
            <p>Remembered your password? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</div>

<script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Sending...';
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>