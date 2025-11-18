<?php
    $page_title = "View CV: Khojsuru";

    // --- STEP 1: All PHP Logic FIRST ---
    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';

    // --- STEP 2: Header will handle security ---
    require_once __DIR__ . '/includes/header.php';

    // --- STEP 3: Now we can safely perform our logic ---
    $cv_id = (int)($_GET['id'] ?? 0);
    if (!$cv_id) {
        header('Location: ' . BASE_URL . 'dashboard.php');
        exit();
    }

    // --- 2. Fetch CV & Owner Data ---
    $stmt = $pdo->prepare(
        "SELECT cvs.*, users.id as owner_id, users.name as owner_name, users.headline as owner_headline, 
        users.profile_image as owner_avatar, users.email as owner_email, users.phone as owner_phone 
        FROM cvs JOIN users ON cvs.user_id = users.id WHERE cvs.id = :cv_id"
    );
    $stmt->execute(['cv_id' => $cv_id]);
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cv) { exit('CV not found.'); }

    // --- 3. Check Privacy ---
    $is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $cv['user_id'];
    if (!$cv['is_public'] && !$is_owner) { exit('This CV is private and cannot be viewed.'); }
    $page_title = htmlspecialchars($cv['title']);

    // --- 4. Fetch related CV data ---
    $experiences = $pdo->prepare("SELECT * FROM cv_experience WHERE cv_id = ? ORDER BY id ASC");
    $experiences->execute([$cv_id]);
    $educations = $pdo->prepare("SELECT * FROM cv_education WHERE cv_id = ? ORDER BY id ASC");
    $educations->execute([$cv_id]);
    $projects = $pdo->prepare("SELECT * FROM cv_projects WHERE cv_id = ? ORDER BY id ASC");
    $projects->execute([$cv_id]);
    $certificates = $pdo->prepare("SELECT * FROM cv_certificates WHERE cv_id = ? ORDER BY id ASC");
    $certificates->execute([$cv_id]);
    $skills = $pdo->prepare("SELECT * FROM cv_skills WHERE cv_id = ?");
    $skills->execute([$cv_id]);
    // --- 5. Prepare Share Data ---
    $share_url = BASE_URL . "project/recruitercv/view_cv.php?id=" . $cv_id;
    $avatar_src = BASE_URL . 'assets/images/default-avatar.png';
    if (!empty($cv['owner_avatar'])) {
        $img_path = $cv['owner_avatar'];
        if (filter_var($img_path, FILTER_VALIDATE_URL)) {
            $avatar_src = htmlspecialchars($img_path);
        } elseif (file_exists(ROOT_PATH . $img_path)) {
            $avatar_src = BASE_URL . htmlspecialchars($img_path);
        }
    }
    include_once 'includes/header.php';
?>
<meta property="og:title" content="<?php echo htmlspecialchars($cv['owner_name']); ?>" />
<meta property="og:description" content="<?php echo htmlspecialchars($cv['owner_headline'] ?? 'Professional CV'); ?>" />
<meta property="og:image" content="<?php echo $avatar_src; ?>" />
<meta property="og:url" content="<?php echo $share_url; ?>" />
<meta name="twitter:card" content="summary_large_image">
<style>
    body{color:white !important;}
    .recruitercv-body{ font-family: 'Inter', sans-serif; font-size: 11pt; line-height: 1.6; background: var(--primary-color) !important; }
    .cv-view-layout { display: grid; grid-template-columns: 1fr 320px; gap: 2.5rem; max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
    .action-panel{min-width:300px}
    .cv-container { margin: 0rem !important; }
    .cv-render-area { background: white; color: black; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 8.5in; }
    .action-panel .panel-card { background: var(--primary-bg); border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border-color); color: white; }
    .btn-action-panel { display: block; width: -webkit-fill-available; text-align: center; padding: 0.75rem; margin-bottom: 1rem; text-decoration: none; border-radius: 8px; font-weight: 600; background: #3b82f6; color: white; border: none; cursor: pointer; }
    .ad-space-cv-view { margin-top: 2rem; min-height: 250px; border: 1px solid var(--border-color); color: var(--text-secondary); border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    
    @media (max-width: 992px) { .cv-view-layout { grid-template-columns: 1fr; } .action-panel .panel-card { position: static; } }
    .share-div {
        margin-top: 1.5rem;
        text-align: center;
    }
    .share-icons {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
    }
    .share-icons a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        text-decoration:none;
    }
    .linkedin{ background-color: #0a66c2;}
    .fb{ background-color: #0866ff;}
    .x{ background-color: #0c0b0b;}
    .whatsapp{ background-color: #4ddb61;}
    .copylink{ background-color: #4ec3c1;}
    .share-icons a:hover {
        background: #334155;
        transform: scale(1.1);
    }
    .copy-feedback {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: #10b981;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
        pointer-events: none;
    }
    .copy-feedback.show {
        opacity: 1;
        transform: translateX(-50%) translateY(-5px);
    }
    .owner-contact-info h3{color: var(--accent-color); margin: 0rem;}
    .owner-contact-info p{color: var(--text-secondary); margin-top: 0.75rem; }
    @media (max-width: 750px) {
        .cv-container{padding: 0.3in !important;}
    }
    @media (max-width: 480px) {
        main{ padding: 0rem;}
        .cv-view-layout {padding:0.5rem}
    }

</style>

<div class="cv-view-layout">
    <div class="cv-render-area">
        <?php
            $template_file = __DIR__ . '/templates/' . basename($cv['template_name']) . '.php';
            if (file_exists($template_file)) {
                $is_pdf = false;
                include $template_file;
            } else {
                echo "<p>Error: Could not find the '{$cv['template_name']}' template.</p>";
            }
        ?>
    </div>
    <aside class="action-panel">
        <div class="panel-card">
            <div class="owner-contact-info">
                <h3>Contact Information</h3>
                <p><i class="fas fa-envelope fa-fw"></i> <?php echo htmlspecialchars($cv['owner_email'] ?? 'Not provided'); ?></p>
                <?php if (!empty($cv['owner_phone'])): ?>
                    <p><i class="fas fa-phone fa-fw"></i> <?php echo htmlspecialchars($cv['owner_phone']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!$is_owner): ?>
                <?php
                    $recipient_avatar_src = BASE_URL . 'assets/images/default-avatar.png';
                    if (!empty($cv['owner_avatar'])) {
                        $img_path = $cv['owner_avatar'];
                        if (filter_var($img_path, FILTER_VALIDATE_URL)) {
                            $recipient_avatar_src = htmlspecialchars($img_path);
                        } elseif (file_exists(ROOT_PATH . $img_path)) {
                            $recipient_avatar_src = BASE_URL . htmlspecialchars($img_path);
                        }
                    }
                ?>
            <?php else: ?>
                <p style="color: #94a3b8; text-align: center;">This is your CV.</p>
                <a href="edit.php?id=<?php echo $cv['id']; ?>" class="btn-action-panel">Edit This CV</a>
            <?php endif; ?>

            <?php if ($is_owner || $cv['is_public']): ?>
            <a href="generate_pdf.php?id=<?php echo $cv_id; ?>" class="btn-action-panel"><i class="fas fa-download"></i> Download PDF</a>
            <?php endif; ?>

            <div class="share-div">
                <h4 style="color:#94a3b8; font-size:0.9rem; margin-bottom:0.5rem;">Share this CV</h4>
                <div class="share-icons">
                    <!-- LinkedIn -->
                    <a class="linkedin" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($share_url); ?>" target="_blank" title="Share on LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <!-- Facebook -->
                    <a class="fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>" target="_blank" title="Share on Facebook"><i class="fab fa-facebook"></i></a>
                    <!-- X (Twitter) -->
                    <a class="x" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode('Check out this professional CV:'); ?>" target="_blank" title="Share on X"><i class="fab fa-x-twitter"></i></a>
                    <!-- WhatsApp -->
                    <a class="whatsapp" href="https://wa.me/?text=<?php echo urlencode('Check out this CV: ' . $share_url); ?>" target="_blank" title="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <!-- Copy Link Button -->
                    <a href="#" id="copy-link-btn" class="copylink" title="Copy Link" style="position:relative;">
                        <i class="fas fa-link"></i>
                        <span class="copy-feedback" id="copy-feedback">Link Copied!</span>
                    </a>
                </div>
                 <!-- Developer Note about localhost -->
                <!-- <p style="font-size: 0.75rem; color: #64748b; margin-top: 1rem; font-style: italic;">
                    Note: Social media previews will only work when this site is on a public domain, not on localhost.
                </p> -->
            </div>

        </div>

        <div class="ad-space-cv-view">
            <p>Ad Space (300*300px)</p>
            <p>Contact Us at:</p>
            <p>jhamghatltd@gmail.com</p> 
        </div>
        <div class="ad-space-cv-view">
            <p>Ad Space (300*300px)</p>
            <p>Contact Us at:</p>
            <p>jhamghatltd@gmail.com</p> 
        </div>
    </aside>
</div>

<?php include_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const APP_URL = document.body.dataset.appUrl || '';
    const MY_USER_ID = parseInt(document.body.dataset.userId || 0);

    
    const copyBtn = document.getElementById('copy-link-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the link from navigating
            
            const linkToCopy = "<?php echo $share_url; ?>";
            const feedbackEl = document.getElementById('copy-feedback');

            navigator.clipboard.writeText(linkToCopy).then(() => {
                // Success! Show feedback.
                feedbackEl.classList.add('show');
                // Hide feedback after 2 seconds
                setTimeout(() => {
                    feedbackEl.classList.remove('show');
                }, 2000);
            }, () => {
                // Error (less common, but good to handle)
                alert("Failed to copy link.");
            });
        });
    }
});
</script>
