<?php
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';

    // Use UTC timezone to avoid expiration issues
    date_default_timezone_set('UTC');

    $error_message = '';
    $token = $_GET['token'] ?? '';
    $is_token_valid = false;
    $user_email = null;

    // Validate token
    if (!empty($token)) {
        $token_hash = hash('sha256', $token);

        // Use UTC_TIMESTAMP() for safety
        $stmt = $pdo->prepare(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > UTC_TIMESTAMP()"
        );
        $stmt->execute([$token_hash]);
        $reset_request = $stmt->fetch();

        if ($reset_request) {
            $is_token_valid = true;
            $user_email = $reset_request['email'];
        } else {
            $error_message = "This password reset link is invalid or has expired. Please request a new one.";
        }
    } else {
        $error_message = "No reset token provided in the URL.";
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_token_valid) {
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } elseif ($password !== $password_confirm) {
            $error_message = "Passwords do not match.";
        } else {
            $new_password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Update user password
            $update_stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $update_stmt->execute([$new_password_hash, $user_email]);

            // Delete used token
            $del_stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $del_stmt->execute([$token_hash]);

            $_SESSION['success_message'] = "Your password has been reset successfully. Please log in.";
            header('Location: ' . BASE_URL . 'login.php');
            exit();
        }
    }

    $page_title = "Reset Password: Khojsuru";
    require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Basic Layout */
    .auth-layout { display: flex; justify-content: center; align-items: start; min-height: 90vh; padding: 2rem; background: var(--primary-bg);}
    .auth-card { max-width: 480px; width: 100%; background: none; padding: 0rem; border-radius: 0px; border: none;}
    .auth-header { text-align:center; margin-bottom:2.5rem; }
    .auth-header .icon { width:64px; height:64px; background: var(--success-color); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; color:white; font-size:1.5rem;}
    .auth-header h1 { font-size:2rem; font-weight:700; margin:0 0 0.75rem;}
    .auth-header p { font-size:1rem; color:var(--text-secondary); margin:0; line-height:1.5; }

    /* Form */
    .form-group { margin-bottom:1.5rem; }
    .form-group label { display:block; font-weight:600; margin-bottom:0.5rem; font-size:0.95rem; }
    .form-input { width:100%; padding:0.875rem 1rem; border:2px solid var(--border-color); border-radius:12px; font-size:1rem; background:var(--secondary-bg); color:var(--text-primary); box-sizing:border-box;}
    .form-input:focus { outline:none; border-color:var(--accent-color); box-shadow:0 0 0 3px rgba(59,130,246,0.1); }
    .password-input-wrapper { position:relative; }
    .password-toggle { position:absolute; right:1rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-secondary); cursor:pointer; font-size:1.2rem;}
    .password-toggle:hover { color:var(--text-primary);}
    .password-input-wrapper input { padding-right:3rem; }

    /* Password Strength */
    .password-strength { margin-top:0.5rem; display:none; }
    .strength-bar { height:4px; background:var(--border-color); border-radius:2px; overflow:hidden; margin-bottom:0.5rem;}
    .strength-fill { height:100%; transition:all 0.3s ease; border-radius:2px; }
    .strength-weak{background:var(--error-color); width:25%;}
    .strength-fair{background:#f59e0b;width:50%;}
    .strength-good{background:#06b6d4;width:75%;}
    .strength-strong{background:var(--success-color);width:100%;}
    .strength-text { font-size:0.8rem; color:var(--text-secondary); }

    /* Password Requirements */
    .password-requirements { margin-top:0.75rem; padding:0rem 1rem; background: rgba(59,130,246,0.05); border:1px solid rgba(59,130,246,0.1); border-radius:8px; font-size:0.875rem;}
    .password-requirements ul { padding-left:1.25rem; color:var(--text-secondary); }
    .password-requirements li.valid { color:var(--success-color); text-decoration: line-through;}

    /* Buttons */
    .btn-submit { width:100%; background:var(--accent-color); color:white; border:none; padding:0.875rem 1.5rem; border-radius:12px; font-size:1rem; font-weight:600; cursor:pointer; margin-top:0.5rem; position:relative; overflow:hidden;}
    .btn-submit:disabled { opacity:0.5; cursor:not-allowed; }

    /* Error/Success */
    .error-message { background:rgba(239,68,68,0.1); border:1px solid var(--error-color); color:var(--error-color); padding:1rem; border-radius:12px; margin-bottom:1.5rem; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem;}
    .success-message { background:rgba(16,185,129,0.1); border:1px solid var(--success-color); color:var(--success-color); padding:1rem; border-radius:12px; margin-bottom:1.5rem; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem; }
    .invalid-token { text-align:center; }
    .invalid-token h2 { color:var(--error-color); margin-bottom:1rem; font-size:1.5rem; }
    .invalid-token p { color:var(--text-secondary); margin-bottom:2rem; }
    .btn-secondary { padding:0.75rem 1.5rem; text-decoration:none; font-weight:600; }
    .btn-secondary:hover { text-decoration: underline;
        color: var(--text-secondary); }
</style>

<div class="auth-layout">
    <div class="auth-card">
        <?php if (!$is_token_valid): ?>
            <div class="invalid-token">
                <h2>Invalid or Expired Link</h2>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <p>This password reset link is no longer valid. Links expire after 1 hour for security reasons.</p>
                <a href="forgot-password.php" class="btn-secondary">Request New Reset Link ?</a>
            </div>
        <?php else: ?>
            <div class="auth-header">
                <div class="icon">🔑</div>
                <h1>Create New Password</h1>
                <p>Enter your new password below. Make sure it's strong and memorable.</p>
            </div>

            <?php if($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" id="resetPasswordForm">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" required minlength="8" placeholder="Enter your new password">
                        <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Password strength</div>
                    </div>
                    <div class="password-requirements">
                        <h4>Password must contain:</h4>
                        <ul id="requirements">
                            <li id="req-length">At least 8 characters</li>
                            <li id="req-uppercase">One uppercase letter</li>
                            <li id="req-lowercase">One lowercase letter</li>
                            <li id="req-number">One number</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input" required placeholder="Confirm your new password">
                        <button type="button" class="password-toggle"><i class="fas fa-eye"></i></button>
                    </div>
                    <div id="passwordMatch" style="margin-top:0.5rem;font-size:0.875rem;"></div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    // --- THIS IS THE FIX for the password toggle ---
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const wrapper = this.parentElement;
            const input = wrapper.querySelector('input');
            const icon = this.querySelector('i');

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    });
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let score = 0;
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password)
        };

        // Update requirement indicators
        Object.keys(requirements).forEach(req => {
            const element = document.getElementById(`req-${req}`);
            if (requirements[req]) {
                element.classList.add('valid');
                score++;
            } else {
                element.classList.remove('valid');
            }
        });

        return { score, requirements };
    }

    function updatePasswordStrength(password) {
        const { score, requirements } = checkPasswordStrength(password);
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        if (password.length === 0) {
            strengthIndicator.style.display = 'none';
            return;
        }

        strengthIndicator.style.display = 'block';

        // Remove all strength classes
        strengthFill.classList.remove('strength-weak', 'strength-fair', 'strength-good', 'strength-strong');

        if (score === 1) {
            strengthFill.classList.add('strength-weak');
            strengthText.textContent = 'Weak password';
        } else if (score === 2) {
            strengthFill.classList.add('strength-fair');
            strengthText.textContent = 'Fair password';
        } else if (score === 3) {
            strengthFill.classList.add('strength-good');
            strengthText.textContent = 'Good password';
        } else if (score === 4) {
            strengthFill.classList.add('strength-strong');
            strengthText.textContent = 'Strong password';
        }

        return score >= 3 && requirements.length;
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirm').value;
        const matchIndicator = document.getElementById('passwordMatch');
        
        if (confirmPassword.length === 0) {
            matchIndicator.textContent = '';
            return false;
        }

        if (password === confirmPassword) {
            matchIndicator.textContent = '✅ Passwords match';
            matchIndicator.style.color = 'var(--success-color)';
            return true;
        } else {
            matchIndicator.textContent = '❌ Passwords do not match';
            matchIndicator.style.color = 'var(--error-color)';
            return false;
        }
    }

    function updateSubmitButton() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirm').value;
        const submitBtn = document.getElementById('submitBtn');
        
        const isPasswordStrong = updatePasswordStrength(password);
        const doPasswordsMatch = checkPasswordMatch();
        
        if (isPasswordStrong && doPasswordsMatch && confirmPassword.length > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // Event listeners
    document.getElementById('password').addEventListener('input', updateSubmitButton);
    document.getElementById('password_confirm').addEventListener('input', updateSubmitButton);

    // Form submission
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        
        if (!submitBtn.disabled) {
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Resetting Password...';
        }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateSubmitButton();
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>