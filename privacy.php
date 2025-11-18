<?php
// /privacy.php

require_once __DIR__ . '/includes/config.php';

$page_title = "Privacy Policy & Terms of Use";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Khojsuru' : 'Khojsuru'; ?></title>
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/favicon.png" type="image/x-icon">
    
    <!-- Fonts, Icons, and Stylesheet -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css"> 
    <style>
        .legal-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2.5rem;
            background: var(--secondary-bg);
            border-radius: 12px;
            line-height: 1.8;
        }
        .legal-container h1 { margin-top: 0; }
        .legal-container h2 {
            margin-top: 2.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        .legal-container p, .legal-container li {
            color: var(--text-secondary);
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
        </div>
    </div>
</header>
<main>
    <div class="legal-container">
        <h1>Privacy Policy & Terms of Use</h1>
        <p><em>Last updated: <?php echo date('F j, Y'); ?></em></p>

        <h2>1. Introduction</h2>
        <p>
            Welcome to Khojsuru. By choosing to use this platform, you acknowledge that you have read, understood, 
            and agree to be bound by this Privacy Policy and Terms of Use. This agreement governs your relationship with 
            Khojsuru and outlines both your rights and responsibilities. If you do not agree with these terms, 
            you must immediately discontinue use of the platform.
        </p>
        <p>
            Khojsuru is designed as a tool to assist individuals in creating resumes, sharing professional information, 
            and interacting with recruiters or employers. However, we do not operate as a recruitment agency, 
            guarantee employment outcomes, or verify the authenticity of job postings or user profiles. 
            Your participation on the platform is entirely voluntary and at your own risk.
        </p>

        <h2>2. Information We Collect</h2>
        <p>
            To provide our services, we collect information that you voluntarily submit during account registration, profile setup, 
            and usage of our services. This may include but is not limited to:
        </p>
        <ul>
            <li><strong>Personal identifiers</strong> – such as your full name, email address, and contact details.</li>
            <li><strong>Profile details</strong> – such as your professional headline, skills, employment history, education, and uploaded CVs.</li>
            <li><strong>Media</strong> – such as your profile photo or any files you upload as part of your CV or job postings.</li>
            <li><strong>Usage information</strong> – such as your activity on the platform, interactions with recruiters or job seekers, and technical information (browser type, device, IP address).</li>
        </ul>
        <p>
            All information you choose to share becomes part of your Khojsuru profile and may be visible to other registered users 
            (depending on your account settings). Khojsuru cannot control how others may use information you choose to disclose.
        </p>

        <h2>3. How We Use Your Information</h2>
        <p>Your data is used exclusively for providing and improving Khojsuru’s services, including:</p>
        <ul>
            <li>Creating and managing your user account and profile.</li>
            <li>Enabling you to build and share CVs or job postings with others.</li>
            <li>Facilitating communication between recruiters and job seekers.</li>
            <li>Sending essential service-related notifications and updates.</li>
            <li>Improving features and overall user experience based on usage patterns.</li>
            <li>Complying with applicable legal requirements and responding to lawful requests by government authorities.</li>
        </ul>
        <p>
            Khojsuru does <strong>not sell your data</strong> to third parties. Any disclosures to third parties occur only as required by law 
            or when you choose to share your information with another user.
        </p>

        <h2>4. Terms of Use</h2>
        <p>
            By using Khojsuru, you agree to follow these rules:
        </p>
        <ul>
            <li>You must provide accurate and truthful information when creating an account or profile.</li>
            <li>You may not use Khojsuru for illegal activities, including but not limited to fraud, harassment, 
                or spreading harmful content.</li>
            <li>You may not post or upload content that infringes intellectual property rights or violates the privacy 
                of others.</li>
            <li>Khojsuru reserves the right to suspend, restrict, or permanently terminate accounts that 
                violate these terms or misuse the platform.</li>
        </ul>
        <p>
            Khojsuru is a neutral platform. We are not responsible for verifying the accuracy of user-submitted content, 
            including job postings or resumes. Users are advised to exercise caution when sharing information or engaging with others.
        </p>

        <h2>5. Your Responsibilities</h2>
        <p>
            You, as a user of Khojsuru, are solely responsible for your actions and the content you provide. This includes:
        </p>
        <ul>
            <li>Keeping your login credentials safe and secure.</li>
            <li>Ensuring your CV, job postings, and profile information are accurate, lawful, and truthful.</li>
            <li>Avoiding the upload of malicious files, spam, or harmful material.</li>
            <li>Understanding that information you publish may be accessed, copied, or reused by other users or third parties.</li>
        </ul>
        <p>
            Khojsuru cannot be held accountable for losses or disputes arising from information you voluntarily share on the platform.
        </p>

        <h2>6. Limitation of Liability</h2>
        <p>
            Khojsuru is provided strictly on an <em>"as is"</em> and <em>"as available"</em> basis. 
            We make no guarantees that the service will be free of errors, uninterrupted, secure, or suitable for any particular purpose. 
            Your use of Khojsuru is entirely at your own risk.
        </p>
        <p>
            To the maximum extent permitted by applicable law, Khojsuru and its partners, affiliates, and service providers 
            shall not be liable for any form of damages, including but not limited to:
        </p>
        <ul>
            <li>Loss of employment opportunities, loss of income, or reputational damage.</li>
            <li>Data loss, corruption of files, or security breaches.</li>
            <li>Any reliance on information provided by other users of the platform.</li>
            <li>Disputes, claims, or damages resulting from interactions with third parties, 
                recruiters, or other users.</li>
        </ul>
        <p>
            Khojsuru does not provide employment guarantees, endorse recruiters, or verify job postings. 
            Any reliance on the platform for employment decisions is solely your responsibility.
        </p>
        <p>
            We reserve the right to disclose user information if legally required by courts, 
            government authorities, or regulators in accordance with applicable law.
        </p>

        <h2>7. Admin’s Role</h2>
        <div class="admin-role">
            <p>
                The administrator ("Admin") of Khojsuru maintains the right to monitor and moderate activity on the platform to ensure a safe, legal, and professional environment for all users. The Admin’s responsibilities include but are not limited to the following:
            </p>
            <ul>
                <li><strong>Account Suspension:</strong> Admin may suspend or permanently disable any user account that is found to be violating our Terms of Use, posting misleading information, or misusing the platform in any way. Our top priority is to facilitate professional interactions between job seekers and recruiters. If your account has been suspended and you believe it to be in error, please contact us at <a href="mailto:support@khojsuru.com">support@khojsuru.com</a>.</li>

                <li><strong>Content Moderation:</strong> Admin reserves the right to inactive job postings(not delete) that are:
                    <ul>
                        <li>Irrelevant or not related to job opportunities.</li>
                        <li>Spammy, deceptive, or misleading in nature.</li>
                        <li>Posted for self-promotion without a valid job listing.</li>
                    </ul>
                    All job posts must be relevant, professional, and in line with the intended purpose of Khojsuru.
                </li>
            </ul>
            <p>
                The Admin team is committed to maintaining a fair and supportive platform. Decisions made by the Admin are final but may be reviewed upon written request.
            </p>
        </div>
       

        <h2>8. Contact Information</h2>
        <p>
            For any questions, concerns, or complaints regarding this Privacy Policy & Terms of Use, you may contact us at:<br>
            <strong>Email:</strong> <a href="mailto:khojsuru@gmail.com">khojsuru@gmail.com</a> or through our <a href="<?php echo BASE_URL; ?>contact.php">
                    Contact Page
                </a>.
        </p>
    </div>
</main>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
