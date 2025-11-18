<?php
    $page_title = "Signup: Khojsuru";
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/session_handler.php';
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
        max-width: 800px;
        width: 100%;
        background: none;
        padding: 0rem;
        border-radius: 0px;
        border: none;
    }

    /* Multi-step form container */
    .signup-container {
        overflow: hidden;
        position: relative;
    }

    /* Progress indicator */
    .progress-container {
        padding: 2rem 2.5rem 1rem;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        position: relative;
    }

    .progress-steps::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--border-color);
        transform: translateY(-50%);
        z-index: 1;
    }

    .progress-line {
        position: absolute;
        top: 50%;
        left: 0;
        height: 2px;
        background: var(--accent-color);
        transform: translateY(-50%);
        transition: width 0.3s ease;
        z-index: 1;
    }

    .progress-step {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--border-color);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .progress-step.active {
        background: var(--accent-color);
        color: white;
    }

    .progress-step.completed {
        background: var(--success-color);
        color: white;
    }

    .step-label {
        text-align: center;
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* Form steps */
    .form-step {
        display: none;
        padding: 0 2.5rem 2.5rem;
    }

    .form-step.active {
        display: block;
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .step-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 0.5rem;
    }

    .step-header p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    /* Role Selection */
    .role-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .role-card {
        background: var(--secondary-bg);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .role-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.5s;
    }

    .role-card:hover::before {
        left: 100%;
    }

    .role-card:hover {
        border-color: var(--accent-color);
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.25);
    }

    .role-card.selected {
        border-color: var(--accent-color);
        background: rgba(59, 130, 246, 0.05);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.25);
    }

    .role-icon {
        width: 80px;
        height: 80px;
        background: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: white;
    }

    .role-card.selected .role-icon {
        background: var(--success-color);
    }

    .role-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.5rem;
    }

    .role-card p {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.4;
    }

    .role-card input[type="radio"] {
        display: none;
    }

    /* Avatar Upload */
    .avatar-section {
        text-align: center;
        margin-bottom: 2rem;
    }

    .avatar-upload {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--border-color);
        background: var(--primary-bg);
        transition: all 0.2s ease;
    }

    .avatar-upload:hover .avatar-preview {
        border-color: var(--accent-color);
    }

    .avatar-upload-overlay {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 36px;
        height: 36px;
        background: #6d47f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        font-size: 1rem;
        transition: all 0.2s ease;
        border: 3px solid var(--secondary-bg);
    }

    .avatar-upload-overlay:hover {
        background: #8768f7ff;
        transform: scale(1.1);
    }

    .avatar-input {
        display: none;
    }

    .avatar-text {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Form Inputs */
    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
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

    #toggle-password {
        position: absolute;
        right: 1rem;
        top: 70%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 1.1rem;
        padding: 0.25rem;
        border-radius: 4px;
        transition: color 0.2s ease;
    }

    #toggle-password-:hover {
        color: var(--text-primary);
    }

    /* Terms checkbox */
    .terms-group {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin: 1.5rem 0;
        padding: 1rem;
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.1);
        border-radius: 12px;
    }

    .terms-group input[type="checkbox"] {
        margin-top: 0.2rem;
        transform: scale(1.2);
    }

    .terms-group label {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.4;
        margin: 0;
    }

    .terms-group a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
    }

    .terms-group a:hover {
        text-decoration: underline;
    }

    /* Navigation buttons */
    .form-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }

    .btn-back {
        background: transparent;
        color: var(--text-secondary);
        border: 2px solid var(--border-color);
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        color: var(--text-primary);
        border-color: var(--text-primary);
        transform: translateY(-1px);
    }

    .btn-next, .btn-submit {
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        width: fit-content;
        overflow: hidden;
    }

    .btn-next:hover, .btn-submit:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
    }

    .btn-next:disabled, .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Error Messages */
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
        content: '';
        font-size: 1.2rem;
    }

    /* Sign in link */
    .auth-switch-link {
        text-align: center;
        padding: 1.5rem 2.5rem;
        background: var(--primary-bg);
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
    @media (min-width: 480px) and (max-width: 640px) {
        .auth-layout {
            padding: 1rem;
        }
        
        .progress-container,
        .form-step {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .auth-switch-link {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .role-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .role-card {
            padding: 1.5rem 1rem;
        }
        
        .role-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .step-header h2 {
            font-size: 1.5rem;
        }
        
        .avatar-preview {
            width: 100px;
            height: 100px;
        }
        
        .form-navigation {
            flex-direction: column;
            gap: 1rem;
        }

        .btn-submit {
            width: -webkit-fill-available;
        }

        .btn-next:disabled, .btn-submit:disabled {
            width: -webkit-fill-available;
        }
    }

    @media (max-width: 480px) {
        .btn-next:disabled, .btn-submit:disabled {
            width: -webkit-fill-available;
        }
        
        .btn-submit {
            width: -webkit-fill-available;
        }

        .auth-layout {
            padding: 0rem;
        }
        
        .progress-container,
        .form-step {
            padding-left: 0rem;
            padding-right: 0rem;
        }
        
        .auth-switch-link {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .role-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .role-card {
            padding: 1.5rem 1rem;
        }
        
        .role-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .step-header h2 {
            font-size: 1.5rem;
        }
        
        .avatar-preview {
            width: 100px;
            height: 100px;
        }
        
        .form-navigation {
            flex-direction: column;
            gap: 1rem;
        }
    }

    /* Loading states */
    .btn-next.loading, .btn-submit.loading {
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }

    .btn-next.loading::after, .btn-submit.loading::after {
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

    body.light-theme .role-card {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    body.light-theme .terms-group {
        background: rgba(59, 130, 246, 0.03);
    }

    .fa-eye-slash:before {
        content: "\f070";
    }
    /* otp  */
    .otp-section {
        padding: 2.5rem;
        text-align: center;
    }
    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 2rem 0;
    }
    .otp-input {
        width: 45px;
        height: 50px;
        font-size: 1.5rem;
        text-align: center;
        border-radius: 8px;
        border: 2px solid var(--border-color);
        background: var(--secondary-bg);
        color: var(--text-primary);
    }
    .otp-input:focus {
        border-color: var(--accent-color);
        outline: none;
    }
    .resend-container {
        margin-top: 1.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    #resend-otp-btn {
        background: none;
        border: none;
        color: var(--accent-color);
        cursor: pointer;
        font-weight: 600;
    }
    #resend-otp-btn:disabled {
        color: var(--text-secondary);
        cursor: not-allowed;
    }
</style>

<div class="auth-layout">
    <div class="auth-card">
        <div class="signup-container" id="signupContainer">
            <!-- Progress Indicator -->
            <div class="progress-container">
                <div class="progress-steps">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="progress-step active" id="step1">1</div>
                    <div class="progress-step" id="step2">2</div>
                    <div class="progress-step" id="step3">3</div>
                </div>
            </div>

            <form id="signupForm" enctype="multipart/form-data">                    
                <!-- Step 1: Role Selection -->
                <div class="form-step active" id="stepRole">
                    <div class="step-header">
                        <h2>Choose Your Role</h2>
                        <p>Select how you'll be using Khojsuru</p>
                    </div>
                    
                    <div class="role-grid">
                        <label class="role-card" data-role="recruitee">
                            <div class="role-icon">👔</div>
                            <h3>Job Seeker</h3>
                            <p>Looking for new opportunities and career advancement</p>
                            <input type="radio" name="user_type" value="recruitee" required>
                        </label>
                        
                        <label class="role-card" data-role="recruiter">
                            <div class="role-icon">🏢</div>
                            <h3>Recruiter</h3>
                            <p>Hiring talent and building amazing teams</p>
                            <input type="radio" name="user_type" value="recruiter" required>
                        </label>
                    </div>
                    
                    <div class="form-navigation">
                        <div></div>
                        <button type="button" class="btn-next" id="nextToProfile" disabled>Continue</button>
                    </div>
                </div>

                <!-- Step 2: Profile Information -->
                <div class="form-step" id="stepProfile">
                    <div class="step-header">
                        <h2>Create Your Profile</h2>
                        <p>Tell us about yourself</p>
                    </div>

                    <div class="avatar-section">
                        <div class="avatar-upload">
                            <img src="<?php echo BASE_URL; ?>assets/images/default-avatar.png" alt="Profile" class="avatar-preview" id="avatarPreview">
                            <label for="profileImage" class="avatar-upload-overlay">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" name="profile_image" id="profileImage" class="avatar-input" accept="image/*">
                        </div>
                        <div class="avatar-text">Click to upload your photo (optional)</div>
                    </div>

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" required minlength="3">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create a strong password (8+ characters)" required minlength="8">
                        <i class="fas fa-eye" id="toggle-password"></i>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-back" onclick="goToStep('stepRole')">Back</button>
                        <button type="button" class="btn-next" id="nextToCompany">Continue</button>
                    </div>
                </div>

                <!-- Step 3: Company Information (for recruiters) / Terms (for job seekers) -->
                <div class="form-step" id="stepFinal">
                    <div class="step-header">
                        <h2 id="finalStepTitle">Almost Done!</h2>
                        <p id="finalStepDesc">Just a few more details</p>
                    </div>

                    <!-- Company fields (shown only for recruiters) -->
                    <div id="companyFields" style="display: none;">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" id="company_name" name="company_name" class="form-input" placeholder="Enter your company name">
                        </div>

                        <div class="form-group">
                            <label for="company_website">Company Website <span style="color: var(--text-secondary);">(Optional)</span></label>
                            <input type="url" id="company_website" name="company_website" class="form-input" placeholder="https://www.khojsuru.com">
                        </div>
                    </div>

                    <div class="terms-group">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="privacy.php" target="_blank">Privacy Policy & Terms of Use</a> and consent to receiving emails from Khojsuru.</label>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn-back" onclick="goToStep('stepProfile')">Back</button>
                        <button type="submit" class="btn-submit" id="submitBtn">Create Account</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="otp-section" id="otpSection" style="display: none;">
            <div class="step-header">
                <h2>Verify Your Email</h2>
                <p>We've sent a 6-digit code to <b id="otpEmailDisplay"></b>. Please enter it below.</p>
            </div>
            <form id="otpForm">
                <div class="otp-inputs" id="otpInputs">
                    <input type="tel" class="otp-input" maxlength="1" required>
                    <input type="tel" class="otp-input" maxlength="1" required>
                    <input type="tel" class="otp-input" maxlength="1" required>
                    <input type="tel" class="otp-input" maxlength="1" required>
                    <input type="tel" class="otp-input" maxlength="1" required>
                    <input type="tel" class="otp-input" maxlength="1" required>
                </div>
                <button type="submit" class="btn-submit" id="verifyOtpBtn">Verify & Create Account</button>
            </form>
            <div class="resend-container">
                Didn't get a code? 
                <button id="resend-otp-btn">Resend OTP</button>
                <span id="resend-timer"></span>
            </div>
        </div>

        <div class="auth-switch-link">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- PART 1: ELEMENT REFERENCES & STATE VARIABLES ---
        let currentStep = 1;
        let selectedRole = null;

        // Multi-step form elements
        const signupForm = document.getElementById('signupForm');
        const submitBtn = document.getElementById('submitBtn');
        const steps = {
            stepRole: document.getElementById('stepRole'),
            stepProfile: document.getElementById('stepProfile'),
            stepFinal: document.getElementById('stepFinal')
        };
        const progressSteps = { 1: document.getElementById('step1'), 2: document.getElementById('step2'), 3: document.getElementById('step3') };
        const progressLine = document.getElementById('progressLine');
        
        // Dynamic content elements
        const finalStepTitle = document.getElementById('finalStepTitle');
        const finalStepDesc = document.getElementById('finalStepDesc');
        const companyFields = document.getElementById('companyFields');
        const companyNameInput = document.getElementById('company_name');
        
        // OTP section elements
        const signupContainer = document.getElementById('signupContainer');
        const otpSection = document.getElementById('otpSection');
        const otpForm = document.getElementById('otpForm');
        const otpInputsContainer = document.getElementById('otpInputs');
        const otpInputs = otpInputsContainer.querySelectorAll('.otp-input');
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const resendBtn = document.getElementById('resend-otp-btn');
        const timerSpan = document.getElementById('resend-timer');


        // --- PART 2: HELPER & NAVIGATION FUNCTIONS ---

        function goToStep(stepId) {
            Object.values(steps).forEach(step => step.classList.remove('active'));
            document.getElementById(stepId).classList.add('active');
            updateProgress(stepId);
        }

        function updateProgress(stepId) {
            const stepOrder = ['stepRole', 'stepProfile', 'stepFinal'];
            currentStep = stepOrder.indexOf(stepId) + 1;

            for (let i = 1; i <= 3; i++) {
                const stepEl = progressSteps[i];
                stepEl.classList.remove('active', 'completed');
                if (i < currentStep) {
                    stepEl.classList.add('completed');
                    stepEl.innerHTML = '&#10003;'; // Checkmark
                } else if (i === currentStep) {
                    stepEl.classList.add('active');
                    stepEl.innerHTML = i;
                } else {
                    stepEl.innerHTML = i;
                }
            }
            progressLine.style.width = `${((currentStep - 1) / (stepOrder.length - 1)) * 100}%`;
        }

        function validateProfileStep() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            if (name.length < 3) { showToast('Name must be at least 3 characters.', 'error'); return false; }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showToast('Please enter a valid email.', 'error'); return false; }
            if (password.length < 8) { showToast('Password must be at least 8 characters.', 'error'); return false; }
            return true;
        }

        function startResendTimer() {
            let timeLeft = 60;
            resendBtn.disabled = true;
            timerSpan.textContent = `(${timeLeft}s)`;
            
            const timer = setInterval(() => {
                timeLeft--;
                timerSpan.textContent = `(${timeLeft}s)`;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    timerSpan.textContent = '';
                }
            }, 1000);
        }

        // --- PART 3: EVENT LISTENERS ---

        // Role Selection (Step 1)
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedRole = this.dataset.role;
                this.querySelector('input[name="user_type"]').checked = true;
                document.getElementById('nextToProfile').disabled = false;
                setTimeout(() => goToStep('stepProfile'), 300);
            });
        });

        // Navigation Buttons (Between steps)
        document.getElementById('nextToProfile').addEventListener('click', () => goToStep('stepProfile'));
        
        document.getElementById('nextToCompany').addEventListener('click', () => {
            if (validateProfileStep()) {
                if (selectedRole === 'recruiter') {
                    finalStepTitle.textContent = 'Company Information';
                    finalStepDesc.textContent = 'Tell us about the company you represent';
                    companyFields.style.display = 'block';
                    companyNameInput.required = true;
                } else {
                    finalStepTitle.textContent = 'Final Step';
                    finalStepDesc.textContent = 'Agree to our terms to complete your account';
                    companyFields.style.display = 'none';
                    companyNameInput.required = false;
                }
                goToStep('stepFinal');
            }
        });

        document.querySelectorAll('.btn-back').forEach(btn => {
            btn.addEventListener('click', () => {
                const prevStep = currentStep === 3 ? 'stepProfile' : 'stepRole';
                goToStep(prevStep);
            });
        });

        // Final Signup Form Submission (Sends OTP)
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (selectedRole === 'recruiter' && companyNameInput.value.trim() === '') {
                showToast('Company name is required for recruiters.', 'error');
                return;
            }
            if (!document.getElementById('terms').checked) {
                showToast('You must agree to the Terms of Use.', 'error');
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            const formData = new FormData(signupForm);
            const userEmail = formData.get('email');

            fetch('api.php?action=send_otp', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Switch to OTP view
                    signupContainer.style.display = 'none';
                    document.getElementById('otpEmailDisplay').textContent = userEmail;
                    otpSection.style.display = 'block';
                    startResendTimer();
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(err => {
                showToast('A network error occurred.', 'error');
            })
            .finally(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        });

        // OTP Form Submission (Verifies OTP)
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            let otp = '';
            otpInputs.forEach(input => { otp += input.value; });

            if (otp.length < 6) {
                showToast('Please enter the complete 6-digit OTP.', 'error');
                return;
            }

            verifyOtpBtn.classList.add('loading');
            verifyOtpBtn.disabled = true;

            const formData = new FormData();
            formData.append('email', document.getElementById('email').value);
            formData.append('otp', otp);

            fetch('api.php?action=verify_otp', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Verification successful! Welcome!', 'success');
                    // Auto-login successful, redirect to the main page
                    setTimeout(() => { window.location.href = '<?php echo BASE_URL; ?>'; }, 1500);
                } else {
                    showToast(data.message, 'error');
                    verifyOtpBtn.classList.remove('loading');
                    verifyOtpBtn.disabled = false;
                }
            });
        });

        // OTP Input Handling (Auto-tab and backspace)
        otpInputsContainer.addEventListener('input', (e) => {
            const target = e.target;
            if (!/^[0-9]$/.test(target.value)) { // Allow only digits
                target.value = '';
                return;
            }
            const next = target.nextElementSibling;
            if (next && target.value) { next.focus(); }
        });

        otpInputsContainer.addEventListener('keyup', (e) => {
            if (e.key === "Backspace" || e.key === "Delete") {
                const prev = e.target.previousElementSibling;
                if (prev) {
                    e.target.value = '';
                    prev.focus();
                }
            }
        });

        // Resend OTP Button
        resendBtn.addEventListener('click', () => {
            // Re-trigger the main form submission to send a new OTP
            signupForm.requestSubmit(submitBtn);
        });

        // --- UI Helpers ---
        // Avatar Preview
        document.getElementById('profileImage').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => document.getElementById('avatarPreview').src = e.target.result;
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Password Toggle
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>