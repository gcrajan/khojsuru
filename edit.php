<?php
    $page_title = "CV Editor: Khojsuru";

    require_once __DIR__ . '/includes/config.php';
    require_once __DIR__ . '/includes/db_connect.php';
    require_once __DIR__ . '/includes/session_handler.php';
    require_once __DIR__ . '/includes/header.php';

    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'recruitee') {
        header('Location: ' . BASE_URL);
        exit();
    }

    $cv_id = (int)($_GET['id'] ?? 0);
    if ($cv_id === 0) {
        header('Location: ' . BASE_URL . 'dashboard.php');
        exit();
    }

    // --- NEW AI DATA HANDLING LOGIC ---
    if (isset($_SESSION['ai_generated_data'])) {
        $ai_data = $_SESSION['ai_generated_data'];
        
        // Pre-format the data to match what the JavaScript expects
        $experiences = $ai_data['experience'] ?? [];
        $projects = $ai_data['projects'] ?? [];
        $skills_string = $ai_data['skills'] ?? '';
        $skills = array_map('trim', explode(',', $skills_string));
        
        $educations = [];
        if (!empty($ai_data['education'])) {
            foreach ($ai_data['education'] as $edu_item) {
                $educations[] = [
                    'degree' => $edu_item[0], 'institution' => $edu_item[1],
                    'start_date' => $edu_item[2], 'end_date' => $edu_item[3]
                ];
            }
        }
        
        // Fetch the main CV details that were just created by the API
        $stmt = $pdo->prepare("SELECT * FROM cvs WHERE id = ? AND user_id = ?");
        $stmt->execute([$cv_id, $_SESSION['user_id']]);
        $cv = $stmt->fetch();
        if (!$cv) {
            // If something went wrong, prevent errors
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit();
        }
        
        // IMPORTANT: Unset the session data so it's not used again
        unset($_SESSION['ai_generated_data']);

    } else {
        // --- STANDARD DATA FETCHING (if not coming from AI) ---
        $stmt = $pdo->prepare("SELECT * FROM cvs WHERE id = ? AND user_id = ?");
        $stmt->execute([$cv_id, $_SESSION['user_id']]);
        $cv = $stmt->fetch();
        if (!$cv) {
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit();
        }

        $exp_stmt = $pdo->prepare("SELECT * FROM cv_experience WHERE cv_id = ? ORDER BY id ASC");
        $exp_stmt->execute([$cv_id]);
        $experiences = $exp_stmt->fetchAll();

        $edu_stmt = $pdo->prepare("SELECT * FROM cv_education WHERE cv_id = ? ORDER BY id ASC");
        $edu_stmt->execute([$cv_id]);
        $educations = $edu_stmt->fetchAll();

        $proj_stmt = $pdo->prepare("SELECT * FROM cv_projects WHERE cv_id = ? ORDER BY id ASC");
        $proj_stmt->execute([$cv_id]);
        $projects = $proj_stmt->fetchAll();

        $cert_stmt = $pdo->prepare("SELECT * FROM cv_certificates WHERE cv_id = ? ORDER BY id ASC");
        $cert_stmt->execute([$cv_id]);
        $certificates = $cert_stmt->fetchAll();

        $skill_stmt = $pdo->prepare("SELECT skill_name FROM cv_skills WHERE cv_id = ?");
        $skill_stmt->execute([$cv_id]);
        $skills = $skill_stmt->fetchAll(PDO::FETCH_COLUMN);
    }
?>

<style>
     .editor-layout { display: flex; min-height: 100vh;}
    .sidebar { width: fit-content; background: var(--secondary-bg); padding: 1.5rem; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a {
        display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; text-decoration: none;
        color: var(--text-secondary); border-radius: 8px; margin-bottom: 0.5rem;
    }
    .sidebar-nav a:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .sidebar-nav a.active { background: var(--accent-color); color: white; }
    .sidebar-actions a {
        display: flex; justify-content: center; align-items: center; gap: 0.5rem; padding: 0.75rem; text-decoration: none;
        border-radius: 8px; font-weight: 600; text-align: center; margin-bottom: 0.5rem;
    }
    .btn-preview { background: #475569; color: white; }
    .btn-download { background: #166534; color: white; }
    .main-content { flex: 1; padding: 2rem 3rem; }
    .form-section { display: none; }
    .form-section.active { display: block; animation: fadeIn 0.5s; padding-bottom: 1rem; }
    .form-section h1 { font-size: 2rem; margin: 0 0 2rem 0; }
    .form-group { margin-bottom: 1rem; }
    .form-group.full-width { grid-column: span 2; }
    .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
    .form-input, .form-textarea {
        width: -webkit-fill-available; padding: 0.75rem 1rem; background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary); font-size: 1rem;
    }
    .form-textarea { resize: vertical; min-height: 120px; }
    .item-block { background: var(--secondary-bg); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; border-left: 3px solid var(--accent-color); }
    .item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .item-header h4 { margin: 0; }
    .btn-delete { background: #991b1b; color: white; border: none; padding: 0.5rem 0.75rem; border-radius: 5px; cursor: pointer; }
    .btn-add {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem;
        background: rgba(255,255,255,0.1); border: 1px solid var(--border-color); color: var(--text-primary);
        border-radius: 8px; cursor: pointer; font-weight: 500;
    }
    .save-bar {position: sticky; */
    bottom: 0;
    padding-top: 1rem;
    margin-top: 2rem;
    text-align: right;
    border-top: 1px solid var(--border-color);}
    .btn-save { padding: 0.75rem 2rem; background: var(--accent-color); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .success-toast { position: fixed; top: 20px; right: 20px; background: var(--success-color); color: white; padding: 1rem 1.5rem; border-radius: 8px; z-index: 1001; animation: slideIn 0.5s; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    .success-toast { 
        position: fixed; top: 20px; right: 20px; 
        background: var(--success-color); color: white; 
        padding: 1rem 1.5rem; border-radius: 8px; z-index: 1001; 
        /* START HIDDEN */
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }
    @media (min-width: 750px) {
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
    }
    @media (max-width: 850px) { 
        main { padding: 0rem;}
        .form-section h1 {margin: 0.5rem 0 1.5rem 0;}
        .main-content { padding: 1rem 1.5rem;}
        .form-grid { gap: 1rem;}
    }
    @media (max-width: 650px) and (min-width: 485px) {
        .sidebar {padding: 1rem 0.25rem;}
        .sidebar-nav {margin-bottom: 1rem;}
        .sidebar-nav a.active {    padding: 0.75rem 0.25rem; margin-bottom: 0rem; border-radius: 4px;}
    }
    @media (max-width: 485px) {
    .nav-link span,.sidebar-actions span{display:none;}
    .sidebar-nav{width: fit-content;}
    .sidebar{padding: 1rem 0.5rem;}
    .form-section h1 { font-size: 1.75rem;}
    .main-content { padding: 1rem 0.5rem;}
    .item-block {padding: 0.5rem;}
    }
</style>

<div class="editor-layout">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="#personal" class="nav-link active"><i class="fas fa-user"></i> <span>Personal Details</span></a>
            <a href="#experience" class="nav-link"><i class="fas fa-briefcase"></i> <span>Experience</span>
            </a>
            <a href="#education" class="nav-link"><i class="fas fa-graduation-cap"></i> <span>Education</span></a>
            <a href="#projects" class="nav-link"><i class="fas fa-project-diagram"></i> <span>Projects</span></a>
            <a href="#certificates" class="nav-link"><i class="fas fa-award"></i> <span>Certificates</span></a>
            <a href="#skills" class="nav-link"><i class="fas fa-lightbulb"></i> <span>Skills</span></a>
        </nav>
        <div class="sidebar-actions">
            <a href="view_cv.php?id=<?php echo $cv_id; ?>" target="_blank" class="btn-preview"><i class="fas fa-eye"></i> <span>Preview CV</span></a>
            <a href="generate_pdf.php?id=<?php echo $cv_id; ?>" class="btn-download"><i class="fas fa-download"></i> <span>Download PDF</span></a>
        </div>
    </aside>

    <main class="main-content">
        <form id="cv-editor-form">
            <input type="hidden" name="cv_id" value="<?php echo $cv_id; ?>">

            <!-- Personal Details -->
            <section id="personal" class="form-section active">
                <h1>Personal Details</h1>
                <div class="form-grid">
                    <div class="form-group"><label for="name">Full Name</label><input type="text" name="full_name" class="form-input" id="name" value="<?php echo htmlspecialchars($cv['full_name'] ?? ''); ?>"></div>
                    <div class="form-group"><label for="email">Email</label><input type="email" name="email" class="form-input" id="email" value="<?php echo htmlspecialchars($cv['email'] ?? ''); ?>"></div>
                    <div class="form-group"><label for="phone">Phone</label><input type="tel" name="phone" class="form-input" id="phone" value="<?php echo htmlspecialchars($cv['phone'] ?? ''); ?>"></div>
                    <div class="form-group"><label for="address">Address</label><input type="text" name="address" class="form-input" id="address" value="<?php echo htmlspecialchars($cv['address'] ?? ''); ?>"></div>
                    <div class="form-group"><label  for="linkedin">LinkedIn URL</label><input type="url" name="linkedin_url" class="form-input" id="linkedin" value="<?php echo htmlspecialchars($cv['linkedin_url'] ?? ''); ?>"></div>
                    <div class="form-group"><label for="github">GitHub URL</label><input type="url" name="github_url" class="form-input" id="github" value="<?php echo htmlspecialchars($cv['github_url'] ?? ''); ?>"></div>
                    <div class="form-group full-width"><label for="summary-textarea">Professional Summary</label><textarea name="summary" id="summary-textarea" class="form-input" rows="6"><?php echo htmlspecialchars($cv['summary'] ?? ''); ?></textarea></textarea></div>
                </div>
            </section>
            
            <!-- Experience -->
            <section id="experience" class="form-section">
                <h1>Work Experience</h1>
                <div id="experience-container">
                    <!-- JS will populate this -->
                </div>
                <button type="button" id="add-experience-btn" class="btn-add"><i class="fas fa-plus"></i> Add Experience</button>
            </section>

            <!-- Education -->
            <section id="education" class="form-section">
                <h1>Education</h1>
                <div id="education-container">
                     <!-- JS will populate this -->
                </div>
                <button type="button" id="add-education-btn" class="btn-add"><i class="fas fa-plus"></i> Add Education</button>
            </section>

            <!-- Projects Section -->
            <section id="projects" class="form-section">
                <h1>Projects</h1>
                <div id="project-container"></div>
                <button type="button" id="add-project-btn" class="btn-add"><i class="fas fa-plus"></i> Add Project</button>
            </section>

            <!-- Certificates Section -->
            <section id="certificates" class="form-section">
                <h1>Licenses & Certificates</h1>
                <div id="certificate-container"></div>
                <button type="button" id="add-certificate-btn" class="btn-add"><i class="fas fa-plus"></i> Add Certificate</button>
            </section>

            <!-- Skills -->
             <section id="skills" class="form-section">
                 <h1>Skills</h1>
                <div class="form-group">
                    <label for="skills-input">Enter skills separated by commas</label>
                    <!-- FIX #2: Added id="skills-input" to the input -->
                    <input type="text" id="skills-input" name="skills" class="form-input" placeholder="e.g., PHP, JavaScript, AWS" value="<?php echo htmlspecialchars(implode(', ', $skills)); ?>">
                </div>
            </section>

            <div class="save-bar">
                <button type="submit" id="save-cv-btn" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </main>
</div>

<!-- ======================= HIDDEN TEMPLATES (WITH FIXES) ======================= -->
<template id="experience-template">
    <div class="item-block">
        <div class="item-header"><h4>New Experience</h4><button type="button" class="btn-delete">&times;</button></div>
        <div class="form-group"><label>Job Title</label><input type="text" name="exp_title[]" class="form-input"></div>
        <!-- Use consistent names that match the API -->
        <div class="form-group"><label>Company</label><input type="text" name="company_name[]" class="form-input"></div>
        <div class="form-grid">
            <div class="form-group"><label>Start Date</label><input type="text" name="start_date[]" class="form-input"></div>
            <div class="form-group"><label>End Date</label><input type="text" name="end_date[]" class="form-input" placeholder="e.g., Present"></div>
        </div>
        <div class="form-group full-width"><label>Description (one achievement per line)</label><textarea name="description[]" class="form-textarea" rows="4"></textarea></div>
    </div>
</template>

<template id="education-template">
     <div class="item-block">
        <div class="item-header"><h4>New Education</h4><button type="button" class="btn-delete">&times;</button></div>
        <!-- Use consistent names that match the API -->
        <div class="form-group"><label>Degree / Certificate</label><input type="text" name="edu_degree[]" class="form-input"></div>
        <div class="form-group"><label>Institution</label><input type="text" name="edu_institution[]" class="form-input"></div>
        <div class="form-grid">
            <div class="form-group"><label>Start Date</label><input type="text" name="edu_start_date[]" class="form-input"></div>
            <div class="form-group"><label>End Date</label><input type="text" name="edu_end_date[]" class="form-input"></div>
        </div>
    </div>
</template>

<template id="project-template">
    <div class="item-block">
        <div class="item-header"><h4>New Project</h4><button type="button" class="btn-delete">&times;</button></div>
        <div class="form-group"><label>Project Name</label><input type="text" name="project_name[]" class="form-input"></div>
        <div class="form-group"><label>Project URL (Optional)</label><input type="url" name="project_url[]" class="form-input"></div>
        <div class="form-group full-width"><label>Description</label><textarea name="project_description[]" class="form-textarea" rows="4"></textarea></div>
    </div>
</template>

<template id="certificate-template">
    <div class="item-block">
        <div class="item-header"><h4>New Certificate</h4><button type="button" class="btn-delete">&times;</button></div>
        <div class="form-group"><label>Certificate Name</label><input type="text" name="certificate_name[]" class="form-input"></div>
        <div class="form-group"><label>Issuing Organization</label><input type="text" name="issuing_organization[]" class="form-input"></div>
        <div class="form-grid">
            <div class="form-group"><label>Issue Date</label><input type="text" name="issue_date[]" class="form-input" placeholder="e.g., June 2023"></div>
            <div class="form-group"><label>Credential URL (Optional)</label><input type="url" name="credential_url[]" class="form-input"></div>
        </div>
    </div>
</template>

<!-- ======================= JAVASCRIPT PART (WITH FIXES) ======================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Section Navigation ---
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    const formSections = document.querySelectorAll('.form-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            formSections.forEach(section => {
                section.classList.remove('active');
                if (section.id === targetId) section.classList.add('active');
            });
        });
    });

    // --- Dynamic Form Block Logic ---
    const experienceContainer = document.getElementById('experience-container');
    const educationContainer = document.getElementById('education-container');
    // ADDED: Get new containers
    const projectContainer = document.getElementById('project-container');
    const certificateContainer = document.getElementById('certificate-container');

    function addBlock(templateId, container, data = {}) {
        const template = document.getElementById(templateId);
        const clone = template.content.cloneNode(true);
        
        for (const key in data) {
            // Updated selector to be more robust
            const input = clone.querySelector(`[name^="${key}"]`);
            if (input) {
                input.value = data[key] || '';
            }
        }
        container.append(clone);
    }
    
    // Use event delegation for remove buttons
    document.querySelector('.main-content').addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-delete')) {
            event.target.closest('.item-block').remove();
        }
    });
    
    // --- Populate existing data from the server ---
    const experiencesData = <?php echo json_encode($experiences); ?>;
    experiencesData.forEach(exp => addBlock('experience-template', experienceContainer, { 'exp_title': exp.job_title, 'company_name': exp.company_name, 'start_date': exp.start_date, 'end_date': exp.end_date, 'description': exp.description }));

    const educationsData = <?php echo json_encode($educations); ?>;
    educationsData.forEach(edu => addBlock('education-template', educationContainer, { 'edu_degree': edu.degree, 'edu_institution': edu.institution, 'edu_start_date': edu.start_date, 'edu_end_date': edu.end_date }));

    // ADDED: Populate Projects & Certificates
    const projectsData = <?php echo json_encode($projects); ?>;
    projectsData.forEach(proj => addBlock('project-template', projectContainer, { 'project_name': proj.project_name, 'project_url': proj.project_url, 'project_description': proj.description }));

    const certificatesData = <?php echo json_encode($certificates); ?>;
    certificatesData.forEach(cert => addBlock('certificate-template', certificateContainer, { 'certificate_name': cert.certificate_name, 'issuing_organization': cert.issuing_organization, 'issue_date': cert.issue_date, 'credential_url': cert.credential_url }));

    // --- Event listeners for "Add" buttons ---
    document.getElementById('add-experience-btn').addEventListener('click', () => addBlock('experience-template', experienceContainer));
    document.getElementById('add-education-btn').addEventListener('click', () => addBlock('education-template', educationContainer));
    // ADDED: New button listeners
    document.getElementById('add-project-btn').addEventListener('click', () => addBlock('project-template', projectContainer));
    document.getElementById('add-certificate-btn').addEventListener('click', () => addBlock('certificate-template', certificateContainer));


    // --- AJAX Form Submission ---
    const cvForm = document.getElementById('cv-editor-form');
    cvForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const saveBtn = document.getElementById('save-cv-btn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        
        const formData = new FormData(cvForm);
        
        fetch('<?php echo BASE_URL; ?>api.php?action=save_cv', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('🎉 CV saved successfully!');
            } else {
                showToast(data.message || 'Failed to save CV.', 'error');
            }
        })
        .catch(() => showToast('A network error occurred.', 'error'))
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        });
    });

    // Helper for showing toast messages
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'success-toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        // Trigger the animation
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Hide and remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>