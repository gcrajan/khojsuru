<?php
header('Content-Type: application/json');

// --- ADD THESE LINES ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/vendor/autoload.php';

function json_error($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

$action = $_GET['action'] ?? '';
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? 0;

switch ($action) {

    case 'create_user_post':
        if ($_SESSION['user_type'] !== 'recruitee') {
            json_error('Permission denied.');
        }
        $content = trim($_POST['content'] ?? '');
        if (empty($content)) {
            json_error('Post content cannot be empty.');
        }
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->execute([$user_id, $content]);
        echo json_encode(['success' => true]);
    break;

    case 'create_company_profile':
        if ($_SESSION['user_type'] !== 'recruiter') {
            json_error('Permission denied.');
        }
        $company_name = trim($_POST['company_name'] ?? '');
        $website = trim($_POST['website'] ?? '');
        if (empty($company_name)) {
            json_error('Company name is required.');
        }
        $stmt = $pdo->prepare("INSERT INTO companies (name, website, created_by_user_id) VALUES (?, ?, ?)");
        $stmt->execute([$company_name, $website, $user_id]);
        echo json_encode(['success' => true]);
    break;

    case 'update_company_profile':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'recruiter') { json_error('Permission denied.'); }

        $company_id = (int)($_POST['company_id'] ?? 0);
        $company_name = trim($_POST['company_name'] ?? '');
        $website = trim($_POST['company_website'] ?? '');
        $about = trim($_POST['about'] ?? '');
        $new_logo_path = null;
        $new_logo_url  = null;

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $target_dir = ROOT_PATH . "uploads/logos/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $logo_name = uniqid() . '-' . basename($_FILES["logo"]["name"]);
            $target_file = $target_dir . $logo_name;
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $new_logo_path = "uploads/logos/" . $logo_name;
                $new_logo_url  = BASE_URL . $new_logo_path;
            }
        }

        if ($new_logo_path) {
            $stmt = $pdo->prepare("UPDATE companies SET name=?, website=?, about=?, logo=? WHERE id=? AND created_by_user_id=?");
            $stmt->execute([$company_name, $website, $about, $new_logo_path, $company_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE companies SET name=?, website=?, about=? WHERE id=? AND created_by_user_id=?");
            $stmt->execute([$company_name, $website, $about, $company_id, $user_id]);
        }

        echo json_encode(['success' => true, 'new_logo_url' => $new_logo_url]);
    break;

    case 'post_new_job':
        if ($_SESSION['user_type'] !== 'recruiter') { json_error('Permission denied.'); }

        $company_id = (int)($_POST['company_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $job_type = $_POST['job_type'] ?? 'Full-time';
        $is_remote = isset($_POST['is_remote']) ? 1 : 0;
        
        // --- MODIFIED: Handle and Validate Deadline ---
        $deadline_str = $_POST['deadline'] ?? '';
        if (empty($deadline_str)) {
            json_error('Application deadline is a required field.');
        }
        // Convert to UTC for storage
        $deadline = gmdate('Y-m-d H:i:s', strtotime($deadline_str));
        // Validate that the deadline is in the future
        if (strtotime($deadline) <= time()) {
            json_error('The deadline must be a future date and time.');
        }
        
        // Now that the autoloader is included, this will work.
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $description = $purifier->purify($_POST['description'] ?? '');

        if (empty($title) || empty($description) || empty($company_id)) {
            json_error('Title, description, and company are required.');
        }

        // --- MODIFIED: Update SQL Query ---
        $stmt = $pdo->prepare(
            "INSERT INTO jobs (recruiter_user_id, company_id, title, description, location, job_type, is_remote, deadline)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $company_id, $title, $description, $location, $job_type, $is_remote, $deadline]);
        
        echo json_encode(['success' => true]);
    break;

    case 'save_cv':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'recruitee') {
            json_error('Permission denied.');
        }

        $cv_id = (int)($_POST['cv_id'] ?? 0);
        if ($cv_id === 0) { json_error('Invalid CV ID.'); }

        $stmt = $pdo->prepare("SELECT user_id FROM cvs WHERE id = ?");
        $stmt->execute([$cv_id]);
        if ($stmt->fetchColumn() != $user_id) {
            json_error('Permission denied to edit this CV.');
        }

        try {
            $pdo->beginTransaction();

            // 1. Update the main cvs table
            $main_stmt = $pdo->prepare("UPDATE cvs SET full_name=?, email=?, phone=?, address=?, linkedin_url=?, github_url=?, summary=? WHERE id = ?");
            $main_stmt->execute([$_POST['full_name'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['linkedin_url'], $_POST['github_url'], $_POST['summary'], $cv_id]);

            // 2. Update Experience 
            $pdo->prepare("DELETE FROM cv_experience WHERE cv_id = ?")->execute([$cv_id]);
            if (!empty($_POST['exp_title'])) {
                $exp_insert = $pdo->prepare("INSERT INTO cv_experience (cv_id, job_title, company_name, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($_POST['exp_title'] as $key => $title) {
                    if (!empty($title)) {
                        $exp_insert->execute([
                            $cv_id, $title, $_POST['company_name'][$key],
                            $_POST['start_date'][$key], $_POST['end_date'][$key],
                            $_POST['description'][$key]
                        ]);
                    }
                }
            }

            // 3. Update Education
            $pdo->prepare("DELETE FROM cv_education WHERE cv_id = ?")->execute([$cv_id]);
            if (!empty($_POST['edu_degree'])) {
                $edu_insert = $pdo->prepare("INSERT INTO cv_education (cv_id, degree, institution, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
                foreach ($_POST['edu_degree'] as $key => $degree) {
                    if (!empty($degree)) {
                        $edu_insert->execute([
                            $cv_id, $degree, $_POST['edu_institution'][$key],
                            $_POST['edu_start_date'][$key], $_POST['edu_end_date'][$key]
                        ]);
                    }
                }
            }
            
            // UPDATE FOR PROJECTS
            $pdo->prepare("DELETE FROM cv_projects WHERE cv_id = ?")->execute([$cv_id]);
            if (!empty($_POST['project_name'])) {
                $proj_insert = $pdo->prepare("INSERT INTO cv_projects (cv_id, project_name, project_url, description) VALUES (?, ?, ?, ?)");
                foreach ($_POST['project_name'] as $key => $name) {
                    if (!empty($name)) {
                        $proj_insert->execute([
                            $cv_id, $name,
                            $_POST['project_url'][$key],
                            $_POST['project_description'][$key]
                        ]);
                    }
                }
            }

            // Update CERTIFICATES
            $pdo->prepare("DELETE FROM cv_certificates WHERE cv_id = ?")->execute([$cv_id]);
            if (!empty($_POST['certificate_name'])) {
                $cert_insert = $pdo->prepare("INSERT INTO cv_certificates (cv_id, certificate_name, issuing_organization, issue_date, credential_url) VALUES (?, ?, ?, ?, ?)");
                foreach ($_POST['certificate_name'] as $key => $name) {
                    if (!empty($name)) {
                        $cert_insert->execute([
                            $cv_id, $name,
                            $_POST['issuing_organization'][$key],
                            $_POST['issue_date'][$key],
                            $_POST['credential_url'][$key]
                        ]);
                    }
                }
            }
            
            // 4. Update Skills and Synchronize Cache
            $pdo->prepare("DELETE FROM cv_skills WHERE cv_id = ?")->execute([$cv_id]);
            if (!empty($_POST['skills'])) {
                // ... (rest of the skills logic remains the same)
                $skills = explode(',', $_POST['skills']);
                $skill_insert = $pdo->prepare("INSERT INTO cv_skills (cv_id, skill_name) VALUES (?, ?)");
                foreach ($skills as $skill) {
                    $trimmed_skill = trim($skill);
                    if (!empty($trimmed_skill)) {
                        $skill_insert->execute([$cv_id, $trimmed_skill]);
                    }
                }
            }
            
            // CRITICAL SYNCHRONIZATION STEP
            $all_skills_stmt = $pdo->prepare(
                "SELECT DISTINCT s.skill_name FROM cv_skills s
                JOIN cvs c ON s.cv_id = c.id
                WHERE c.user_id = ? 
                ORDER BY s.id DESC LIMIT 15"
            );
            $all_skills_stmt->execute([$user_id]);
            $all_user_skills = $all_skills_stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $cache_string = implode(', ', $all_user_skills);
            $cache_update = $pdo->prepare("UPDATE users SET skills_cache = ? WHERE id = ?");
            $cache_update->execute([$cache_string, $user_id]);
            
            $pdo->commit();
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            $pdo->rollBack();
            json_error('Database error: ' . $e->getMessage());
        }
    break;

    case 'update_profile':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) { json_error('Name cannot be empty.'); }
        
        // Prepare data for update
        $headline = trim($_POST['headline'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $new_image_path = null;
        $new_image_url = null;

        // Handle Profile Picture Upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $target_dir = ROOT_PATH . "uploads/avatars/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $image_name = uniqid() . '-' . basename($_FILES["profile_image"]["name"]);
            $target_file = $target_dir . $image_name;
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($image_type, $allowed_types) && move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $new_image_path = "uploads/avatars/" . $image_name;
            } else {
                json_error('Invalid file type or size for profile picture.');
            }
        }
        
        try {
            $pdo->beginTransaction();

            $sql_parts = ["name = ?", "headline = ?", "location = ?", "phone = ?"];
            $params = [$name, $headline, $location, $phone];
            
            if ($new_image_path) {
                $sql_parts[] = "profile_image = ?";
                $params[] = $new_image_path;
            }

            // If the user is a recruitee and submitted the skills form
            if ($_SESSION['user_type'] === 'recruitee' && isset($_POST['skills_cache'])) {
                $sql_parts[] = "skills_cache = ?";
                $params[] = trim($_POST['skills_cache']);
            }
            
            $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
            $params[] = $user_id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $pdo->commit();

            // Update session variables
            $_SESSION['user_name'] = $name;
            if ($new_image_path) {
                $_SESSION['user_image'] = $new_image_path;
                $new_image_url = BASE_URL . $new_image_path;
            }
            
            echo json_encode(['success' => true, 'new_image_url' => $new_image_url]);

        } catch (Exception $e) {
            $pdo->rollBack();
            json_error('Database error: ' . $e->getMessage());
        }
    break;
    
    case 'submit_application':
        if ($_SESSION['user_type'] !== 'recruitee') {
            json_error('Only job seekers can apply for jobs.');
        }

        $job_id = (int)($_POST['job_id'] ?? 0);
        $cv_id = (int)($_POST['cv_id'] ?? 0);
        $uploaded_cv_path = null;

        // --- Validation: Must provide either a selected CV or upload one ---
        if ($cv_id === 0 && (!isset($_FILES['uploaded_cv']) || $_FILES['uploaded_cv']['error'] != 0)) {
            json_error('You must either select an existing CV or upload a new one.');
        }
        
        // --- Handle File Upload if one was provided ---
        if ($cv_id === 0) {
            $target_dir = ROOT_PATH . "uploads/applications/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }

            $file_name = uniqid() . '-' . basename($_FILES["uploaded_cv"]["name"]);
            $target_file = $target_dir . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($file_type != 'pdf' || $_FILES['uploaded_cv']['size'] > 5000000) { // < 5MB
                json_error('Invalid file. Please upload a PDF under 5MB.');
            }
            if (move_uploaded_file($_FILES["uploaded_cv"]["tmp_name"], $target_file)) {
                $uploaded_cv_path = "uploads/applications/" . $file_name;
            } else {
                json_error('Sorry, there was an error uploading your CV.');
            }
        }

        // --- Insert into the applications table ---
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO applications (job_id, recruitee_user_id, cv_id, uploaded_cv_path) 
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $job_id,
                $user_id,
                $cv_id ?: null,
                $uploaded_cv_path
            ]);

            // --- CREATE NOTIFICATION ---
            $job_info_stmt = $pdo->prepare("SELECT recruiter_user_id, title FROM jobs WHERE id = ?");
            $job_info_stmt->execute([$job_id]);
            $job_info = $job_info_stmt->fetch();

            if ($job_info) {
                $recruiter_id = $job_info['recruiter_user_id'];
                $job_title = htmlspecialchars($job_info['title']);
                $applicant_name = htmlspecialchars($_SESSION['user_name']);

                $notification_message = "<strong>{$applicant_name}</strong> applied for your '<strong>{$job_title}</strong>' job.";
                $notification_link = "applicants.php?job_id=" . $job_id;
                
                $notify_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'new_applicant', ?, ?)");
                $notify_stmt->execute([$recruiter_id, $notification_message, $notification_link]);
            }
            
            echo json_encode(['success' => true]);

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                json_error('You have already applied for this job.');
            }
            json_error('A database error occurred.');
        }
    break;

    case 'update_application_status':
        if ($_SESSION['user_type'] !== 'recruiter') {
            json_error('Permission denied.');
        }

        $application_id = (int)($_POST['application_id'] ?? 0);
        $new_status = $_POST['status'] ?? '';
        $allowed_statuses = ['submitted', 'viewed', 'interviewing', 'rejected', 'hired'];

        if ($application_id === 0 || !in_array($new_status, $allowed_statuses)) {
            json_error('Invalid data provided.');
        }
        
        // --- Security Check: Ensure the recruiter owns the job this application is for ---
        $stmt = $pdo->prepare(
            "SELECT a.id FROM applications a
             JOIN jobs j ON a.job_id = j.id
             WHERE a.id = ? AND j.recruiter_user_id = ?"
        );
        $stmt->execute([$application_id, $user_id]);
        if (!$stmt->fetch()) {
            json_error('Permission denied. You do not own the job this application belongs to.');
        }

        // --- If security check passes, update the status ---
        $update_stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $application_id]);

        // --- CREATE NOTIFICATION ---
        $app_info_stmt = $pdo->prepare(
            "SELECT a.recruitee_user_id, j.id as job_id, j.title, c.name as company_name 
             FROM applications a 
             JOIN jobs j ON a.job_id = j.id
             JOIN companies c ON j.company_id = c.id
             WHERE a.id = ?"
        );
        $app_info_stmt->execute([$application_id]);
        $app_info = $app_info_stmt->fetch();

        if ($app_info) {
            $company_name = htmlspecialchars($app_info['company_name']);
            $job_title = htmlspecialchars($app_info['title']);

            $notification_message = "<strong>{$company_name}</strong> updated your application for '<strong>{$job_title}</strong>' to <strong>{$new_status}</strong>.";
            $notification_link = "view_job.php?id=" . $app_info['job_id'];
            
            $notify_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'status_change', ?, ?)");
            $notify_stmt->execute([$app_info['recruitee_user_id'], $notification_message, $notification_link]);
        }
        
        echo json_encode(['success' => true]);
    break;

    case 'toggle_job_like':
        if (!$is_logged_in) { json_error('You must be logged in to like a job.'); }
        
        $job_id = (int)($_POST['job_id'] ?? 0);
        if ($job_id === 0) { json_error('Invalid job ID.'); }

        $like_stmt = $pdo->prepare("SELECT job_id FROM job_likes WHERE job_id = ? AND user_id = ?");
        $like_stmt->execute([$job_id, $user_id]);
        
        if ($like_stmt->fetch()) {
            $pdo->prepare("DELETE FROM job_likes WHERE job_id = ? AND user_id = ?")->execute([$job_id, $user_id]);
            $user_has_liked = false;
        } else {
            $pdo->prepare("INSERT INTO job_likes (job_id, user_id) VALUES (?, ?)")->execute([$job_id, $user_id]);
            $user_has_liked = true;
        }

        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM job_likes WHERE job_id = ?");
        $count_stmt->execute([$job_id]);
        $like_count = $count_stmt->fetchColumn();

        echo json_encode([
            'success' => true,
            'like_count' => $like_count,
            'user_has_liked' => $user_has_liked
        ]);
    break;

    case 'get_job_comments':
        $job_id = (int)($_GET['job_id'] ?? 0);
        if ($job_id === 0) {
            json_error('Invalid job ID.');
        }

        $stmt = $pdo->prepare("
            SELECT c.*, u.name, u.profile_image
            FROM job_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.job_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$job_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'comments' => $comments]);
    break;

    case 'post_job_comment':
        if (!$is_logged_in) { json_error('You must be logged in to comment.'); }

        $job_id = (int)($_POST['job_id'] ?? 0);
        $comment_text = trim($_POST['comment_text'] ?? '');
        $parent_comment_id = (int)($_POST['parent_comment_id'] ?? 0);

        if ($job_id === 0 || empty($comment_text)) {
            json_error('Invalid data provided.');
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO job_comments (job_id, user_id, parent_comment_id, comment_text) VALUES (?, ?, ?, ?)");
            $stmt->execute([$job_id, $user_id, $parent_comment_id ?: null, $comment_text]);
            
            $new_comment_id = $pdo->lastInsertId();
            $profile_avatar_url = BASE_URL . ($_SESSION['user_image'] ?? 'assets/images/default-avatar.png');
            $user_name_safe = htmlspecialchars($_SESSION['user_name']);
            $comment_text_safe = nl2br(htmlspecialchars($comment_text));
            $comment_text_raw = htmlspecialchars($comment_text); // For textarea

            $comment_html = '';

            // --- START: MODIFIED LOGIC ---
            // Generate different HTML based on whether it's a parent comment or a reply.
            if ($parent_comment_id > 0) {
                // --- This is a REPLY ---
                $comment_html = '
                <div class="comment-item" id="comment-'. $new_comment_id .'">
                    <img src="'. $profile_avatar_url .'" class="comment-avatar">
                    <div class="comment-content">
                        <strong>'. $user_name_safe .'</strong>
                        <div class="comment-body">
                            <p class="comment-text">'. $comment_text_safe .'</p>
                            <form class="edit-comment-form" style="display:none;" data-comment-id="'. $new_comment_id .'">
                                <textarea name="comment_text" class="form-input" required>'. $comment_text_raw .'</textarea>
                                <button type="submit" class="btn-submit">Save</button>
                                <button type="button" class="cancel-edit-btn btn-secondary">Cancel</button>
                            </form>
                        </div>
                        <div class="comment-actions">
                            <small class="comment-date">Just now</small>
                            &middot; <button class="edit-comment-btn" data-comment-id="'. $new_comment_id .'">Edit</button>
                            &middot; <button class="delete-comment-btn" data-comment-id="'. $new_comment_id .'">Delete</button>
                        </div>
                    </div>
                </div>';
            } else {
                // --- This is a PARENT COMMENT ---
                $comment_html = '
                <div class="comment-item" id="comment-'. $new_comment_id .'">
                    <img src="'. $profile_avatar_url .'" class="comment-avatar">
                    <div class="comment-content">
                        <strong>'. $user_name_safe .'</strong>
                        <div class="comment-body">
                            <p class="comment-text">'. $comment_text_safe .'</p>
                            <form class="edit-comment-form" style="display:none;" data-comment-id="'. $new_comment_id .'">
                                <textarea name="comment_text" class="form-input" required>'. $comment_text_raw .'</textarea>
                                <button type="submit" class="btn-submit">Save</button>
                                <button type="button" class="cancel-edit-btn btn-secondary">Cancel</button>
                            </form>
                        </div>
                        <div class="comment-actions">
                            <small class="comment-date">Just now</small>
                            &middot; <button class="reply-btn" data-comment-id="'. $new_comment_id .'">Reply</button>
                            &middot; <button class="edit-comment-btn" data-comment-id="'. $new_comment_id .'">Edit</button>
                            &middot; <button class="delete-comment-btn" data-comment-id="'. $new_comment_id .'">Delete</button>
                        </div>
                        <div class="comment-replies"></div>
                        <form class="reply-form" data-parent-id="'. $new_comment_id .'">
                            <input type="hidden" name="job_id" value="'. $job_id .'">
                            <input type="hidden" name="parent_comment_id" value="'. $new_comment_id .'">
                            <textarea name="comment_text" class="form-input" placeholder="Write a reply..." required rows="2"></textarea>
                            <button type="submit" class="btn-submit">Submit Reply</button>
                        </form>
                    </div>
                </div>';
            }

            // --- CREATE NOTIFICATION ---
            $job_title_stmt = $pdo->prepare("SELECT title FROM jobs WHERE id = ?");
            $job_title_stmt->execute([$job_id]);
            $job_title = htmlspecialchars($job_title_stmt->fetchColumn());
            $commenter_name = htmlspecialchars($_SESSION['user_name']);
            
            if ($parent_comment_id > 0) {
                // This is a reply
                $parent_commenter_stmt = $pdo->prepare("SELECT user_id FROM job_comments WHERE id = ?");
                $parent_commenter_stmt->execute([$parent_comment_id]);
                $notify_user_id = $parent_commenter_stmt->fetchColumn();
                $notification_message = "<strong>{$commenter_name}</strong> replied to your comment on '<strong>{$job_title}</strong>'.";
            } else {
                // This is a new comment
                $job_owner_stmt = $pdo->prepare("SELECT recruiter_user_id FROM jobs WHERE id = ?");
                $job_owner_stmt->execute([$job_id]);
                $notify_user_id = $job_owner_stmt->fetchColumn();
                $notification_message = "<strong>{$commenter_name}</strong> commented on your job '<strong>{$job_title}</strong>'.";
            }

            if ($notify_user_id && $notify_user_id != $user_id) {
                $notification_link = "view_job.php?id=" . $job_id . "#comment-" . $new_comment_id;
                $notify_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'new_comment', ?, ?)");
                $notify_stmt->execute([$notify_user_id, $notification_message, $notification_link]);
            }
            echo json_encode(['success' => true, 'comment_html' => $comment_html]);

        } catch (PDOException $e) {
            json_error("Database error: " . $e->getMessage());
        }
    break;

    case 'edit_job_comment':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $comment_id = (int)($_POST['comment_id'] ?? 0);
        $comment_text = trim($_POST['comment_text'] ?? '');

        if ($comment_id === 0 || empty($comment_text)) {
            json_error('Invalid data provided.');
        }

        // Security: Update only if the comment ID belongs to the current user.
        $stmt = $pdo->prepare("UPDATE job_comments SET comment_text = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$comment_text, $comment_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'updated_html' => nl2br(htmlspecialchars($comment_text))
            ]);
        } else {
            json_error('Could not update comment or permission denied.');
        }
    break;

    case 'delete_job_comment':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $comment_id = (int)($_POST['comment_id'] ?? 0);
        if ($comment_id === 0) { json_error('Invalid Comment ID.'); }

        // Security Check: Get the comment's author ID first.
        $owner_stmt = $pdo->prepare("SELECT user_id FROM job_comments WHERE id = ?");
        $owner_stmt->execute([$comment_id]);
        $comment_owner_id = $owner_stmt->fetchColumn();

        // Allow deletion if the user is the owner OR is an admin.
        if ($comment_owner_id == $user_id || $_SESSION['user_type'] === 'admin') {
            // Delete the comment and any replies it has.
            $delete_stmt = $pdo->prepare("DELETE FROM job_comments WHERE id = ? OR parent_comment_id = ?");
            $delete_stmt->execute([$comment_id, $comment_id]);
            echo json_encode(['success' => true]);
        } else {
            json_error('Permission denied to delete this comment.');
        }
    break;

    case 'create_manual_cv':
        if (!$is_logged_in || $_SESSION['user_type'] !== 'recruitee') {
            json_error('Permission denied.');
        }

        $cv_title = trim($_POST['cv_title'] ?? '');
        $target_role = trim($_POST['target_role'] ?? '');
        $template_name = $_POST['template_name'] ?? 'modern';

        if (empty($cv_title) || empty($target_role)) {
            json_error('Title and Target Role are required.');
        }
        
        // Ensure user's email is available
        $user_email = $_SESSION['user_email'] ?? '';
        if (empty($user_email)) {
            $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_email = $stmt->fetchColumn();
        }

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO cvs (user_id, title, target_role, template_name, full_name, email) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $user_id, $cv_title, $target_role,
                $template_name, $_SESSION['user_name'], $user_email
            ]);
            $new_cv_id = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'cv_id' => $new_cv_id
            ]);

        } catch (PDOException $e) {
            json_error('A database error occurred while creating the CV.');
        }
    break;

    case 'toggle_cv_privacy':
        if ($_SESSION['user_type'] !== 'recruitee') {
            json_error('Permission denied.');
        }

        $cv_id = (int)($_POST['cv_id'] ?? 0);
        if ($cv_id === 0) {
            json_error('Invalid CV ID.');
        }
        $stmt = $pdo->prepare("UPDATE cvs SET is_public = 1 - is_public WHERE id = ? AND user_id = ?");
        $stmt->execute([$cv_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            // Fetch the new status to send back to the frontend
            $status_stmt = $pdo->prepare("SELECT is_public FROM cvs WHERE id = ?");
            $status_stmt->execute([$cv_id]);
            $new_status = (bool) $status_stmt->fetchColumn();
            echo json_encode(['success' => true, 'is_public' => $new_status]);
        } else {
            json_error('Could not update privacy or permission denied.');
        }
    break;

    case 'delete_cv':
        if ($_SESSION['user_type'] !== 'recruitee') {
            json_error('Permission denied.');
        }
        
        $cv_id = (int)($_POST['cv_id'] ?? 0);
        if ($cv_id === 0) { json_error('Invalid CV ID.'); }

        // Security: Ensure user can only delete their OWN CV.
        $stmt = $pdo->prepare("DELETE FROM cvs WHERE id = ? AND user_id = ?");
        $stmt->execute([$cv_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not delete CV or permission denied.');
        }
    break;

    case 'delete_job':
        if ($_SESSION['user_type'] !== 'recruiter') {
            json_error('Permission denied.');
        }
        
        $job_id = (int)($_POST['job_id'] ?? 0);
        if ($job_id === 0) { json_error('Invalid Job ID.'); }

        // Security: Ensure user can only delete their OWN job.
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND recruiter_user_id = ?");
        $stmt->execute([$job_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not delete job or permission denied.');
        }
    break;

    case 'update_job':
        if ($_SESSION['user_type'] !== 'recruiter') {
            json_error('Permission denied.');
        }
        
        $job_id = (int)($_POST['job_id'] ?? 0);
        if ($job_id === 0) { json_error('Invalid Job ID.'); }

        // --- Security Check: Verify this recruiter owns the job before updating ---
        $owner_check = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND recruiter_user_id = ?");
        $owner_check->execute([$job_id, $user_id]);
        if (!$owner_check->fetch()) {
            json_error('Permission denied to edit this job.');
        }

        // --- Sanitize and collect data ---
        $title = trim($_POST['title'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $job_type = $_POST['job_type'] ?? 'Full-time';
        $is_remote = isset($_POST['is_remote']) ? 1 : 0;
        
        // Sanitize the HTML from CKEditor
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,b,strong,i,em,ul,ol,li,a[href],h2,h3,h4,img[src]');
        $purifier = new HTMLPurifier($config);
        $description = $purifier->purify($_POST['description'] ?? '');

        if (empty($title) || empty($description)) {
            json_error('Title and description are required.');
        }

        // --- Update the database ---
        $stmt = $pdo->prepare(
            "UPDATE jobs SET title=?, description=?, location=?, job_type=?, is_remote=? 
             WHERE id = ?"
        );
        $stmt->execute([$title, $description, $location, $job_type, $is_remote, $job_id]);
        
        echo json_encode(['success' => true]);
    break;

    // user rating 
    case 'submit_rating':
        if (!$is_logged_in) {
            json_error('You must be logged in to rate a user.');
        }

        $profile_user_id = (int)($_POST['profile_user_id'] ?? 0);
        $profile_user_type = $_POST['profile_user_type'] ?? '';
        $rating = (int)($_POST['rating'] ?? 0);

        if ($profile_user_id <= 0 || $rating < 1 || $rating > 5) {
            json_error('Invalid input.');
        }
        if ($profile_user_id === $user_id || $profile_user_type === $_SESSION['user_type']) {
            json_error('You cannot rate this user.');
        }

        try {
            if ($profile_user_type === 'recruitee' && $_SESSION['user_type'] === 'recruiter') {
                $stmt = $pdo->prepare("INSERT INTO recruitee_ratings (recruitee_user_id, recruiter_user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
                $stmt->execute([$profile_user_id, $user_id, $rating]);
            } elseif ($profile_user_type === 'recruiter' && $_SESSION['user_type'] === 'recruitee') {
                $stmt = $pdo->prepare("INSERT INTO recruiter_ratings (recruiter_user_id, recruitee_user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
                $stmt->execute([$profile_user_id, $user_id, $rating]);
            } else {
                json_error('This rating action is not allowed.');
            }

            // --- CREATE NOTIFICATION ---
            $rater_name = htmlspecialchars($_SESSION['user_name']);
            $notification_message = "<strong>{$rater_name}</strong> gave you a <strong>{$rating}-star rating</strong> on your profile.";
            $notification_link = "profile.php?id=" . $user_id; // Link back to the rater's profile
            
            $notify_stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'new_rating', ?, ?)");
            $notify_stmt->execute([$profile_user_id, $notification_message, $notification_link]);
            
            echo json_encode(['success' => true, 'message' => 'Rating submitted!']);

        } catch (Exception $e) {
            json_error($e->getMessage());
        }
    break;

    // notification 
    case 'get_notifications':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $stmt = $pdo->prepare("SELECT message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll();

        // Mark these 5 as read
        $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5")->execute([$user_id]);

        echo json_encode(['success' => true, 'notifications' => $notifications]);
    break;

    case 'delete_notification':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $notification_id = (int)($_POST['notification_id'] ?? 0);
        if ($notification_id === 0) { json_error('Invalid notification ID.'); }

        // Delete only if the notification belongs to the logged-in user
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->execute([$notification_id, $user_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Notification deleted.']);
        } else {
            json_error('Notification not found or already deleted.');
        }
    break;

    case 'delete_all_notifications':
        if (!$is_logged_in) { json_error('Authentication required.'); }

        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode(['success' => true, 'message' => 'All notifications have been deleted.']);
    break;

    // job post
    case 'ckeditor_image_upload':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'recruiter') {
            json_error('Permission denied.');
        }

        if (isset($_FILES['upload']) && $_FILES['upload']['error'] == 0) {
            $target_dir = ROOT_PATH . "uploads/job_images/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            
            // File validation
            $image_name = uniqid() . '-' . basename($_FILES["upload"]["name"]);
            $target_file = $target_dir . $image_name;
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($image_type, $allowed_types) && $_FILES['upload']['size'] < 5000000) { // < 5MB
                if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
                    echo json_encode([
                        'uploaded' => 1,
                        'fileName' => $image_name,
                        'url' => BASE_URL . 'uploads/job_images/' . $image_name
                    ]);
                    exit();
                }
            }
        }

        echo json_encode([
            'uploaded' => 0,
            'error' => [
                'message' => 'The image upload failed. Please check the file type (JPG, PNG, GIF) and size (< 5MB).'
            ]
        ]);
        exit();
    break;

    case 'generate_ai_cv':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'recruitee') {
            die("Error: Permission denied.");
        }
        
        // --- 1. Get User Input ---
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $location = trim($_POST['location']);
        $job_title = trim($_POST['job_title']);
        $job_description = trim($_POST['job_description']);
        $about_company = trim($_POST['about_company']);

        // --- Format multiple education entries ---
        $education_history_text = "";
        if (!empty($_POST['edu_degree']) && is_array($_POST['edu_degree'])) {
            foreach ($_POST['edu_degree'] as $key => $degree) {
                $institution = $_POST['edu_institution'][$key] ?? '';
                $start_date = $_POST['edu_start_date'][$key] ?? '';
                $end_date = !empty($_POST['edu_end_date'][$key]) ? $_POST['edu_end_date'][$key] : 'Present';
                
                if (!empty($degree) && !empty($institution)) {
                    $education_history_text .= "- {$degree} from {$institution} ({$start_date} - {$end_date})\n";
                }
            }
        }
        
        // --- 2. Set Up API Call to Google Gemini ---
        $apiKeys = GEMINI_API_KEYS;
        $final_response = null;
        $request_succeeded = false;
        $http_code = 0;
        $curl_error = '';

        foreach ($apiKeys as $apiKey) {
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

            $data = [
                "contents" => [["parts" => [["text" => $prompt]]]],
                "generationConfig" => ["response_mime_type" => "application/json"]
            ];
            
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            // Check the response
            if ($http_code == 200) {
                $final_response = $response;
                $request_succeeded = true;
                break; // Success! Exit the loop.
            } elseif ($http_code == 429) {
                error_log("Gemini API key ending in " . substr($apiKey, -4) . " has been rate-limited.");
                continue; // Try the next key.
            } else {
                error_log("Gemini API Error. HTTP Code: {$http_code} | Response: {$response}");
                break; // A different error occurred, no point trying other keys.
            }
        }

        if (!$request_succeeded) {
            die("Error: The AI service is currently unavailable or has reached its limit. Please try again later.");
        }

        // --- 3. Construct a Detailed Prompt ---
        $prompt = "Act as an expert CV writer. Based on the user's details and the target job, generate a complete CV draft.
        USER'S DETAILS:
        - Name: {$full_name}
        - Contact: {$email}, {$phone}
        - Location: {$location}
        - Education History:\n{$education_history_text}
        
        TARGET JOB:
        - Title: {$job_title}
        - Description: {$job_description}
        - About Company: {$about_company}

        YOUR TASK:
        Generate a valid JSON object with FOUR keys: 'summary', 'experience', 'project', and 'skills'.
        - 'summary': A professional summary (string), tailored to the job.
        - 'experience': A JSON array of two fictional but highly relevant work experiences. Each object must have keys: 'job_title', 'company_name', 'start_date', 'end_date', and 'description' (a string with 2-3 bullet points separated by '\\n').
        - 'project': A JSON array of two fictional but highly relevant projects. Each object must have keys: 'project_name', 'project_url', and 'description' (a string with 2-3 sentences of a paragraph).
        - 'skills': A JSON array of the top 10-15 most relevant skills (strings) from the job description.";

        // --- 4. Make the API Call using cURL ---
        $data = [
            "contents" => [["parts" => [["text" => $prompt]]]],
            "generationConfig" => ["response_mime_type" => "application/json"]
        ];
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        // Optional: Add a timeout to prevent the script from hanging indefinitely
        curl_setopt($ch, CURLOPT_TIMEOUT, 90); // 90 seconds timeout
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // --- 5. Process the API Response ---
        if ($curl_error) {
            die("cURL Error: " . $curl_error);
        }
        if ($http_code != 200) {
            die("Error: Failed to connect to AI service. HTTP Code: {$http_code} | Response: {$response}");
        }

        $result = json_decode($response, true);
        // Navigate through the Gemini API's nested structure
        $ai_text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $ai_data = json_decode($ai_text, true);

        if (json_last_error() !== JSON_ERROR_NONE || !$ai_data || !isset($ai_data['summary'])) {
            // Log the problematic response for debugging
            error_log("Invalid JSON from AI: " . $ai_text);
            die("Error: The AI service returned an unexpected format. Please try rephrasing your job description.");
        }
        
        // --- 6. Save Everything to the Database in a Transaction ---
        try {
            $pdo->beginTransaction();
            // Create the main CV record
            $cv_stmt = $pdo->prepare(
                "INSERT INTO cvs (user_id, title, target_role, template_name, full_name, email, phone, address, summary) 
                 VALUES (?, ?, ?, 'modern', ?, ?, ?, ?, ?)"
            );
            $cv_stmt->execute([$user_id, $job_title, $job_title, $full_name, $email, $phone, $location, $ai_data['summary']]);
            $new_cv_id = $pdo->lastInsertId();

            // Save the user's REAL education history
            if (!empty($_POST['edu_degree']) && is_array($_POST['edu_degree'])) {
                $edu_stmt = $pdo->prepare("INSERT INTO cv_education (cv_id, degree, institution, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
                foreach ($_POST['edu_degree'] as $key => $degree) {
                    $institution = $_POST['edu_institution'][$key] ?? '';
                    $start_date = $_POST['edu_start_date'][$key] ?? '';
                    $end_date = !empty($_POST['edu_end_date'][$key]) ? $_POST['edu_end_date'][$key] : 'Present';
                    
                    if (!empty($degree) && !empty($institution)) {
                        $edu_stmt->execute([$new_cv_id, $degree, $institution, $start_date, $end_date]);
                    }
                }
            }
            
            // Save the AI-generated skills
            if (!empty($ai_data['skills']) && is_array($ai_data['skills'])) {
                $skill_stmt = $pdo->prepare("INSERT INTO cv_skills (cv_id, skill_name) VALUES (?, ?)");
                foreach ($ai_data['skills'] as $skill) {
                    $skill_stmt->execute([$new_cv_id, trim($skill)]);
                }
            }

            // Save the AI-generated experience
            if (!empty($ai_data['experience']) && is_array($ai_data['experience'])) {
                $exp_stmt = $pdo->prepare("INSERT INTO cv_experience (cv_id, job_title, company_name, start_date, end_date, description) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($ai_data['experience'] as $exp) {
                    $exp_stmt->execute([$new_cv_id, $exp['job_title'], $exp['company_name'], $exp['start_date'], $exp['end_date'], $exp['description']]);
                }
            }

            // Save the AI-generated project
            if (!empty($ai_data['project']) && is_array($ai_data['project'])) {
                $exp_stmt = $pdo->prepare("INSERT INTO cv_projects (cv_id, project_name, project_url, description) VALUES (?, ?, ?, ?)");
                foreach ($ai_data['project'] as $exp) {
                    $exp_stmt->execute([$new_cv_id, $exp['project_name'], $exp['project_url'], $exp['description']]);
                }
            }
            
            $pdo->commit();
            // --- 7. Redirect to the editor for review ---
            header("Location: " . BASE_URL . "edit.php?id=" . $new_cv_id);
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Database error during AI CV creation: " . $e->getMessage());
        }
    break;

    // sign up 
    case 'send_otp':
        // 1. Collect and validate all form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user_type = $_POST['user_type'] ?? '';
        $company_name = trim($_POST['company_name'] ?? '');
        $company_website = trim($_POST['company_website'] ?? '');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { json_error("Please enter a valid email address."); }
        if (strlen($password) < 8) { json_error("Password must be at least 8 characters long."); }
        
        // Check if a VERIFIED user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) { json_error("An account with this email already exists. Please log in."); }
        
        // 2. Generate OTP and store all data
        $otp = random_int(100000, 999999); // Generate a 6-digit OTP
        $otp_hash = password_hash((string)$otp, PASSWORD_DEFAULT);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $expires_at = gmdate('Y-m-d H:i:s', strtotime('+10 minutes')); // OTPs should have a short lifespan

        $profile_image_data = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $imageData = file_get_contents($_FILES['profile_image']['tmp_name']);
            if ($imageData) {
                $profile_image_data = ['type' => $_FILES['profile_image']['type'], 'data' => base64_encode($imageData)];
            }
        }
        
        $form_data = json_encode([
            'name' => $name, 'password_hash' => $password_hash, 'user_type' => $user_type,
            'company_name' => $company_name, 'company_website' => $company_website,
            'profile_image_data' => $profile_image_data
        ]);

        // Use INSERT...ON DUPLICATE KEY UPDATE to handle resend requests
        $stmt = $pdo->prepare(
            "INSERT INTO pending_signups (email, otp_hash, expires_at, form_data) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE otp_hash = VALUES(otp_hash), expires_at = VALUES(expires_at), form_data = VALUES(form_data)"
        );
        $stmt->execute([$email, $otp_hash, $expires_at, $form_data]);

        // 3. Send the OTP email
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
            $mail->Subject = 'Your Khojsuru Verification Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #2563eb;'>Welcome to Khojsuru!</h2>
                    <p>Your verification code is:</p>
                    <h1 style='font-size: 42px; letter-spacing: 10px; text-align: center; color: #1e40af;'>
                        <strong>{$otp}</strong>
                    </h1>
                    <p>This code expires in <strong>10 minutes</strong>.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <hr>
                    <small>Khojsuru – Nepal's Job Platform</small>
                </div>
            ";
            $mail->AltBody = "Your verification code is: {$otp}. It expires in 10 minutes.";
        
            $mail->send();
        } catch (Exception $e) {
            error_log("Brevo SMTP Error: " . $mail->ErrorInfo);
            json_error("Failed to send verification email. Please try again later.");
        }
        
        echo json_encode(['success' => true, 'message' => 'Verification OTP sent to your email.']);
    break;

    case 'verify_otp':
        // 1. Get OTP and email
        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            json_error("Email and OTP are required.");
        }

        // 2. Find the pending signup
        $stmt = $pdo->prepare("SELECT * FROM pending_signups WHERE email = ? AND expires_at > UTC_TIMESTAMP()");
        $stmt->execute([$email]);
        $pending_user = $stmt->fetch();

        if (!$pending_user) {
            json_error("Invalid or expired OTP. Please request a new one.");
        }

        // 3. Verify the OTP
        if (!password_verify($otp, $pending_user['otp_hash'])) {
            json_error("The OTP you entered is incorrect.");
        }

        // 4. If OTP is correct, create the final user account
        $form_data = json_decode($pending_user['form_data'], true);
        $pdo->beginTransaction();
        try {
            $profile_image_path = null;
            if (!empty($form_data['profile_image_data'])) {
                $image_info = $form_data['profile_image_data'];
                $imageData = base64_decode($image_info['data']);
                $extension = 'jpg'; // Default extension

                // --- THIS IS THE ROBUST FIX ---
                // Check if finfo is available before using it
                if (function_exists('finfo_open') && $imageData) {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $finfo->buffer($imageData);
                    // Make sure it's a valid image mime type
                    if (strpos($mime_type, 'image/') === 0) {
                        $extension = explode('/', $mime_type)[1];
                    }
                }
                
                $target_dir = ROOT_PATH . "uploads/avatars/";
                if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
                $image_name = uniqid() . '.' . $extension;
                $target_file = $target_dir . $image_name;

                if (file_put_contents($target_file, $imageData)) {
                    $profile_image_path = "uploads/avatars/" . $image_name;
                }
            }

            // ... (rest of the user and company creation logic is correct)
            $user_stmt = $pdo->prepare("INSERT INTO users (user_type, name, email, password_hash, profile_image) VALUES (?, ?, ?, ?, ?)");
            $user_stmt->execute([$form_data['user_type'], $form_data['name'], $email, $form_data['password_hash'], $profile_image_path]);
            $new_user_id = $pdo->lastInsertId();

            if ($form_data['user_type'] === 'recruiter') {
                $company_stmt = $pdo->prepare("INSERT INTO companies (name, website, created_by_user_id) VALUES (?, ?, ?)");
                $company_stmt->execute([$form_data['company_name'], $form_data['company_website'], $new_user_id]);
            }
            
            $pdo->prepare("DELETE FROM pending_signups WHERE email = ?")->execute([$email]);
            $pdo->commit();

            // 5. Automatically log the user in (no change)
            session_regenerate_id(true);
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['user_name'] = $form_data['name'];
            $_SESSION['user_type'] = $form_data['user_type'];
            $_SESSION['user_image'] = $profile_image_path;
            $_SESSION['user_email'] = $email;
            
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("OTP Verification Error: " . $e->getMessage());
            json_error("A database error occurred. Please try again.");
        }
    break;

    // login 
    case 'login':
        // 1. Collect and sanitize form data
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            json_error("Invalid email or password.");
        }

        // 2. Find the user by email
        $stmt = $pdo->prepare("SELECT id, name, email, user_type, password_hash, profile_image, is_active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // 3. Verify user and password
        if (!$user || !password_verify($password, $user['password_hash'])) {
            json_error("Incorrect login credentials.");
        }

        // 4. CRITICAL: Check if the account is suspended
        if ($user['is_active'] == 0) {
            json_error("Your account has been suspended by an administrator. Please mail us at support@khojsuru.com.np");
        }

        // 5. If all checks pass, create the session
        session_regenerate_id(true); // Security measure
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_image'] = $user['profile_image'];
        $_SESSION['user_email'] = $user['email'];

        echo json_encode(['success' => true]);
    break;

    // admin 
    case 'admin_get_user_details':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { json_error('Permission denied.'); }
        
        $user_id_to_fetch = (int)($_GET['user_id'] ?? 0);
        $stmt = $pdo->prepare("SELECT id, name, email, headline FROM users WHERE id = ?");
        $stmt->execute([$user_id_to_fetch]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            json_error('User not found.');
        }
    break;
        
    case 'admin_toggle_suspension':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { 
            json_error('Permission denied.'); 
        }

        $user_id_to_toggle = (int)($_POST['user_id'] ?? 0);
        if ($user_id_to_toggle === 0) { 
            json_error('Invalid User ID.'); 
        }

        // The `1 - is_active` trick flips the value between 0 and 1
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1 - is_active WHERE id = ?");
        $stmt->execute([$user_id_to_toggle]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not update user status.');
        }
    break;

    case 'admin_toggle_job_status':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { json_error('Permission denied.'); }

        $job_id = (int)($_POST['job_id'] ?? 0);
        if ($job_id === 0) { json_error('Invalid Job ID.'); }
        
        // Flips the value of is_active between 0 and 1
        $stmt = $pdo->prepare("UPDATE jobs SET is_active = 1 - is_active WHERE id = ?");
        $stmt->execute([$job_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not update job status.');
        }
    break;

    case 'admin_toggle_job_featured':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { json_error('Permission denied.'); }
        
        $job_id = (int)($_POST['job_id'] ?? 0);
        if ($job_id === 0) { json_error('Invalid Job ID.'); }
        
        // Flips the value of is_featured between 0 and 1
        $stmt = $pdo->prepare("UPDATE jobs SET is_featured = 1 - is_featured WHERE id = ?");
        $stmt->execute([$job_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not update featured status.');
        }
    break;

    case 'admin_save_blog_post':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            json_error('Permission denied.');
        }

        $post_id = (int)($_POST['post_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $status = $_POST['status'] ?? 'draft';

        // Sanitize CKEditor HTML
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed',
            'p,b,strong,i,em,u,ul,ol,li,a[href],h1,h2,h3,h4,h5,h6,img[src|alt|width|height],br'
        );
        $purifier = new HTMLPurifier($config);
        $content_html = $purifier->purify($_POST['content_html'] ?? '');

        if (empty($title) || empty($content_html)) {
            json_error('Title and content are required.');
        }

        // Slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        // Featured Image Upload
        $featured_image_path = $_POST['existing_image'] ?? null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $target_dir = ROOT_PATH . "uploads/blog/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $image_name = uniqid() . '-' . basename($_FILES["featured_image"]["name"]);
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                $featured_image_path = "uploads/blog/" . $image_name;
            }
        }

        try {
            if ($post_id > 0) { // UPDATE
                $stmt = $pdo->prepare(
                    "UPDATE blog_posts SET title=?, slug=?, content_html=?, status=?, featured_image=?, updated_at=NOW() WHERE id=?"
                );
                $stmt->execute([$title, $slug, $content_html, $status, $featured_image_path, $post_id]);
            } else { // INSERT
                $stmt = $pdo->prepare(
                    "INSERT INTO blog_posts (author_user_id, title, slug, content_html, status, featured_image, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
                );
                $stmt->execute([$user_id, $title, $slug, $content_html, $status, $featured_image_path]);
            }
            echo json_encode(['success' => true, 'message' => 'Blog post saved successfully.']);

        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                json_error('A post with a similar title already exists. Please choose a unique title.');
            }
            json_error('A database error occurred: ' . $e->getMessage());
        }
    break;

    case 'admin_delete_blog_post':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            json_error('Permission denied.');
        }
        
        $post_id = (int)($_POST['post_id'] ?? 0);
        if ($post_id === 0) { json_error('Invalid Post ID.'); }

        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            json_error('Could not delete post or it was already deleted.');
        }
    break;

    case 'blogs_ckeditor_image':
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            echo json_encode([ 'uploaded' => false, 'error' => [ 'message' => 'Permission denied.' ] ]);
            exit();
        }

        if (!isset($_FILES['upload']) || $_FILES['upload']['error'] != 0) {
            echo json_encode([ 'uploaded' => false, 'error' => [ 'message' => 'No file uploaded.' ] ]);
            exit();
        }

        $file = $_FILES['upload'];
        $target_dir = ROOT_PATH . "uploads/blog/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            echo json_encode([ 'uploaded' => false, 'error' => [ 'message' => 'Invalid file type.' ] ]);
            exit();
        }

        $newName = uniqid("img_") . "." . $ext;
        $target_file = $target_dir . $newName;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            echo json_encode([
                'uploaded' => true,
                'url' => BASE_URL . "uploads/blog/" . $newName
            ]);
        } else {
            echo json_encode([ 'uploaded' => false, 'error' => [ 'message' => 'Upload failed.' ] ]);
        }
        exit();
    break;

    default:
        json_error('Invalid API action specified.');
    break;
}
?>
