-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2025 at 04:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cv`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `recruitee_user_id` int(11) NOT NULL,
  `cv_id` int(11) DEFAULT NULL,
  `uploaded_cv_path` varchar(255) DEFAULT NULL,
  `application_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted','viewed','interviewing','rejected','hired') NOT NULL DEFAULT 'submitted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `recruitee_user_id`, `cv_id`, `uploaded_cv_path`, `application_date`, `status`) VALUES
(1, 2, 2, NULL, 'uploads/applications/68b29e9e03ea7-Enhanced_CV-Rajan-GC.pdf', '2025-08-30 12:32:58', 'hired'),
(3, 1, 2, 10, NULL, '2025-08-30 22:09:55', 'submitted'),
(4, 6, 2, 10, NULL, '2025-09-06 22:13:32', 'viewed'),
(5, 8, 2, 8, NULL, '2025-09-07 15:25:54', 'rejected'),
(6, 26, 2, 10, NULL, '2025-09-14 11:28:30', 'submitted'),
(7, 27, 2, NULL, 'uploads/applications/68c6565883173-IELTS-writing-task-1-vocabulary.pdf', '2025-09-14 11:29:56', 'submitted'),
(8, 27, 4, NULL, 'uploads/applications/68cc00b2aa63d-CV-test1 by Khojsuru.pdf', '2025-09-18 18:38:06', 'submitted'),
(9, 28, 4, 24, NULL, '2025-09-19 01:01:38', 'submitted'),
(10, 44, 4, 26, NULL, '2025-09-19 01:11:01', 'submitted');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_user_id` int(11) NOT NULL COMMENT 'Admin/Editor''s user ID',
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content_html` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `author_user_id`, `title`, `slug`, `content_html`, `featured_image`, `status`, `created_at`, `updated_at`) VALUES
(4, 3, 'Welcome note!', 'welcome-note-', '<h3>RecruiterCV: Connecting Ambition with Possibility.</h3><p>A very warm welcome to RecruiterCV! Whether you’re a talented professional ready to take the next step in your career or a visionary recruiter searching for the perfect candidate, you’ve come to the right place. We are thrilled to have you here.</p><p>Our mission at RecruiterCV is simple but powerful: to bridge the gap between exceptional talent and the innovative companies that need them. We believe that the right opportunity can change a life, and the right person can transform a business. This platform was built to make that connection seamless, efficient, and inspiring.</p><p>And that’s where the <strong>Career Hub</strong> comes in. Think of this blog as your personal career coach, your industry insider, and your ultimate resource for navigating the world of recruitment and career development. It’s much more than a feature; it’s the heart of our community.</p><h3>What to Expect from This Blog</h3><p>We\'re committed to providing you with high-value content that empowers you on your journey. We won\'t just be talking about jobs; we\'ll be talking about careers, growth, and the future of work. Here’s a glimpse of what you’ll find:</p><h3>For Our Talented Professionals (Recruitees)</h3><p>Your journey to a new career starts with a single, powerful step: creating a standout CV. This is your professional story, your first impression, and your key to unlocking incredible opportunities. On RecruiterCV, you have two fantastic tools at your disposal: our intuitive <strong>\"Create with AI\"</strong> feature for a fast, intelligent draft, or the <strong>\"Manual Editor\"</strong> for full creative control. Create your CV, set it to public, and let the opportunities find you. Your next great role could be just one click away from a recruiter who is searching for someone exactly like you.</p><p><strong>Ready to get started? Build Your CV Now!</strong></p><h3>For Our Visionary Recruiters</h3><p>The perfect candidate is out there, and we\'re here to help you connect with them. A clear, detailed, and engaging job post is the first step to attracting the high-quality applicants your team deserves. Post your first job today and reach a growing community of skilled professionals. But why wait for them to come to you? Use our powerful <strong>\"Find Talent\"</strong> feature to proactively search our database and discover hidden gems who have made their CVs public. The proactive approach often lands the most remarkable hires.</p><p><strong>Looking for your next great hire? Post a Job Today!</strong></p><h3>Let\'s Build the Future, Together</h3><p>We believe that RecruiterCV is more than just a platform; it\'s a community dedicated to professional growth and success. This Career Hub is for you, and we are incredibly excited to embark on this journey with you.</p><p>So go ahead, explore the platform, build that amazing CV, post that game-changing job, and know that we are here to support you every step of the way. Welcome to the future of recruitment. Welcome to RecruiterCV. </p>', 'uploads/blog/68c29cef3a6d3-Image_fx.jpg', 'published', '2025-09-11 15:32:39', '2025-09-11 15:42:03'),
(6, 3, 'test2', 'test2', '<p>hi, there, can we talk for a second.</p><p>yes</p><p>dssdfs s fsdfs sd sd fsd fsd</p><p><img src=\"http://localhost/recruitercv/uploads/blog/68c2baa1744fc-Image_fx%20(1).jpg\" alt=\"test\" /></p><p>yes and what do you deoeo adf sf sfd sd fdsd fd sd fs df sd</p><ul><li>fsfsfsdfsfs</li><li>asdsafdsdds</li><li>sdfsfdsdf</li><li>sfdsdf</li><li>dsfsd</li><li>fsd</li><li>fsdfsfsfdsfsdf</li></ul><p>sfsfdfdfd a ss sdfsdf sf </p><p> </p><p><img src=\"http://localhost/recruitercv/uploads/job_images/68bd8099a4a57-logo.png\" alt=\"68bd8099a4a57-logo.png\" /></p>', 'uploads/blog/68c2baaa56e41-Image_fx (1).jpg', 'published', '2025-09-11 17:48:54', '2025-09-13 23:53:06'),
(9, 3, 'test1', 'test1', '<p>test</p>', 'uploads/blog/68c55b1e66366-Image_fx.jpg', 'published', '2025-09-13 17:38:02', '2025-09-13 17:38:02'),
(10, 3, 'test3', 'test3', '<p>lwts see</p><p>{ \"uploaded\": true, \"url\": \"https://yourdomain/uploads/blog/img_xxx.jpg\" }<br /> </p>', 'uploads/blog/68c560b06acc9-ChatGPT Image Aug 26, 2025, 12_33_31 PM.png', 'published', '2025-09-13 18:01:48', '2025-09-13 23:53:39'),
(11, 3, 'test4', 'test4', '<p>test</p><p><img src=\"http://localhost/recruitercv/uploads/blog/img_68c58ba872f7e.jpg\" alt=\"img_68c58ba872f7e.jpg\" width=\"1200\" height=\"768\" /></p><p>woowww</p><p><img src=\"http://localhost/recruitercv/uploads/blog/img_68c58bb9a936d.png\" alt=\"img_68c58bb9a936d.png\" width=\"500\" height=\"500\" /></p>', 'uploads/blog/68c649b646a56-Image_fx (1).jpg', 'published', '2025-09-13 21:05:31', '2025-09-14 10:36:02'),
(12, 3, 'test5', 'test5', '<p>13</p>', 'uploads/blog/68c649f30777d-Image_fx.jpg', 'published', '2025-09-13 23:52:29', '2025-09-14 10:37:03'),
(13, 3, 'test6', 'test6', '<h3>RecruiterCV: Connecting Ambition with Possibility.</h3><p>A very warm welcome to RecruiterCV! Whether you’re a talented professional ready to take the next step in your career or a visionary recruiter searching for the perfect candidate, you’ve come to the right place. We are thrilled to have you here.</p><p>Our mission at RecruiterCV is simple but powerful: to bridge the gap between exceptional talent and the innovative companies that need them. We believe that the right opportunity can change a life, and the right person can transform a business. This platform was built to make that connection seamless, efficient, and inspiring.</p><p>And that’s where the <strong>Career Hub</strong> comes in. Think of this blog as your personal career coach, your industry insider, and your ultimate resource for navigating the world of recruitment and career development. It’s much more than a feature; it’s the heart of our community.</p><img src=\"http://localhost/recruitercv/uploads/blog/img_68c6498e3b186.png\" width=\"759\" height=\"396\" alt=\"img_68c6498e3b186.png\" /><h3>What to Expect from This Blog</h3><p>We\'re committed to providing you with high-value content that empowers you on your journey. We won\'t just be talking about jobs; we\'ll be talking about careers, growth, and the future of work. Here’s a glimpse of what you’ll find:</p><h3>For Our Talented Professionals (Recruitees)</h3><p>Your journey to a new career starts with a single, powerful step: creating a standout CV. This is your professional story, your first impression, and your key to unlocking incredible opportunities. On RecruiterCV, you have two fantastic tools at your disposal: our intuitive <strong>\"Create with AI\"</strong> feature for a fast, intelligent draft, or the <strong>\"Manual Editor\"</strong> for full creative control. Create your CV, set it to public, and let the opportunities find you. Your next great role could be just one click away from a recruiter who is searching for someone exactly like you.</p><p><strong>Ready to get started? Build Your CV Now!</strong></p><h3>For Our Visionary Recruiters</h3><p>The perfect candidate is out there, and we\'re here to help you connect with them. A clear, detailed, and engaging job post is the first step to attracting the high-quality applicants your team deserves. Post your first job today and reach a growing community of skilled professionals. But why wait for them to come to you? Use our powerful <strong>\"Find Talent\"</strong> feature to proactively search our database and discover hidden gems who have made their CVs public. The proactive approach often lands the most remarkable hires.</p><p><strong>Looking for your next great hire? Post a Job Today!</strong></p><img src=\"http://localhost/recruitercv/uploads/blog/img_68c6499c35ebb.png\" width=\"754\" height=\"388\" alt=\"img_68c6499c35ebb.png\" /><h3>Let\'s Build the Future, Together</h3><p>We believe that RecruiterCV is more than just a platform; it\'s a community dedicated to professional growth and success. This Career Hub is for you, and we are incredibly excited to embark on this journey with you.</p><p>So go ahead, explore the platform, build that amazing CV, post that game-changing job, and know that we are here to support you every step of the way. Welcome to the future of recruitment. Welcome to RecruiterCV. </p>', 'uploads/blog/68c6499e92172-Image_fx (2).jpg', 'published', '2025-09-13 23:54:17', '2025-09-14 10:35:38'),
(14, 3, 'test7', 'test7', '<h3>RecruiterCV: Connecting Ambition with Possibility.</h3><p>A very warm welcome to RecruiterCV! Whether you’re a talented professional ready to take the next step in your career or a visionary recruiter searching for the perfect candidate, you’ve come to the right place. We are thrilled to have you here.</p><p>Our mission at RecruiterCV is simple but powerful: to bridge the gap between exceptional talent and the innovative companies that need them. We believe that the right opportunity can change a life, and the right person can transform a business. This platform was built to make that connection seamless, efficient, and inspiring.</p><p>And that’s where the <strong>Career Hub</strong> comes in. Think of this blog as your personal career coach, your industry insider, and your ultimate resource for navigating the world of recruitment and career development. It’s much more than a feature; it’s the heart of our community.</p><h3>What to Expect from This Blog</h3><p>We\'re committed to providing you with high-value content that empowers you on your journey. We won\'t just be talking about jobs; we\'ll be talking about careers, growth, and the future of work. Here’s a glimpse of what you’ll find:</p><h3>For Our Talented Professionals (Recruitees)</h3><p>Your journey to a new career starts with a single, powerful step: creating a standout CV. This is your professional story, your first impression, and your key to unlocking incredible opportunities. On RecruiterCV, you have two fantastic tools at your disposal: our intuitive <strong>\"Create with AI\"</strong> feature for a fast, intelligent draft, or the <strong>\"Manual Editor\"</strong> for full creative control. Create your CV, set it to public, and let the opportunities find you. Your next great role could be just one click away from a recruiter who is searching for someone exactly like you.</p><p><strong>Ready to get started? Build Your CV Now!</strong></p><h3>For Our Visionary Recruiters</h3><p>The perfect candidate is out there, and we\'re here to help you connect with them. A clear, detailed, and engaging job post is the first step to attracting the high-quality applicants your team deserves. Post your first job today and reach a growing community of skilled professionals. But why wait for them to come to you? Use our powerful <strong>\"Find Talent\"</strong> feature to proactively search our database and discover hidden gems who have made their CVs public. The proactive approach often lands the most remarkable hires.</p><p><strong>Looking for your next great hire? Post a Job Today!</strong></p><h3>Let\'s Build the Future, Together</h3><p>We believe that RecruiterCV is more than just a platform; it\'s a community dedicated to professional growth and success. This Career Hub is for you, and we are incredibly excited to embark on this journey with you.</p><p>So go ahead, explore the platform, build that amazing CV, post that game-changing job, and know that we are here to support you every step of the way. Welcome to the future of recruitment. Welcome to RecruiterCV. </p>', 'uploads/blog/68c64973d19c9-Image_fx (1).jpg', 'published', '2025-09-13 23:54:28', '2025-09-14 10:34:55');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `sender_user_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_threads`
--

CREATE TABLE `chat_threads` (
  `id` int(11) NOT NULL,
  `user_one_id` int(11) NOT NULL,
  `user_two_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `website`, `logo`, `about`, `created_by_user_id`) VALUES
(1, 'Jhamghat', 'https://jhamghat.free.nf', 'uploads/logos/68bac8ab18908-logo.png', 'At Jhamghat, we bridge the gap between complex challenges and innovative technology. We develop cutting-edge applications and websites that solve real problems and enhance human experiences.', 1),
(2, 'Bajra', 'https://claude.ai', NULL, 'woww wwpw pw pdfd wpwpwp', 5),
(3, 'code himalaya test', 'https://jhamghat.free.nf', NULL, '', 7),
(4, 'Jhamghat ltd', 'https://jhamghat.free.nf/', NULL, NULL, 12);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cvs`
--

CREATE TABLE `cvs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `target_role` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `template_name` varchar(50) NOT NULL DEFAULT 'modern',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cvs`
--

INSERT INTO `cvs` (`id`, `user_id`, `title`, `target_role`, `is_public`, `template_name`, `created_at`, `updated_at`, `full_name`, `email`, `phone`, `address`, `linkedin_url`, `github_url`, `summary`) VALUES
(1, 2, 'Leapfrog', 'Jr Frontend JS developer', 0, 'modern', '2025-08-30 11:22:52', '2025-08-30 11:22:52', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(4, 2, 'final', 'Database', 0, 'modern', '2025-08-30 12:49:01', '2025-08-30 17:09:28', 'Rajan', 'test2@gmail.com', '9840402206', '', '', '', 'test'),
(5, 2, 'Bajra', 'Jr Frontend JS developer', 0, 'modern', '2025-08-30 20:29:06', '2025-08-30 20:37:15', 'Rajan', 'test2@gmail.com', '9840402206', 'Bhaluhi, Rupandehi', 'http://gcrajan.com.np/', 'http://gcrajan.com.np/', 'te efw fw fewf ef w ef wefw few fw ef wef w f wef w fw f ewef w f wf  f w f ef wf w f  ew fe f w efw f wf w ef w f w fw ef w efw f w f wfw f w f w f w f wf w f w fw f  dsd g dfg f h gf.'),
(8, 2, 'final test', 'sfsfdsfs', 1, 'modern', '2025-08-30 21:29:34', '2025-08-30 22:43:53', 'Rajan', 'test2@gmail.com', '32423424', 'address', 'http://gcrajan.com.np/', 'http://gcrajan.com.np/', 'Professional Summary 1'),
(9, 2, 'Title', 'Job Role', 0, 'modern', '2025-08-30 21:47:07', '2025-08-30 21:47:07', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(10, 2, 'CV Title', 'Target Job Role', 1, 'modern', '2025-08-30 21:59:48', '2025-08-30 22:43:48', 'Rajan', 'test2@gmail.com', '97774374734', 'Address', 'http://gcrajan.com.np/', 'http://gcrajan.com.np/', 'Professional Summary 1'),
(11, 2, 'test', 'Jr Frontend JS developer', 0, 'classic', '2025-09-06 02:36:23', '2025-09-06 02:37:31', 'Rajan', 'test2@gmail.com', '435346', 'sfs', 'http://gcrajan.com.np/', 'http://gcrajan.com.np/', 'fsdsfs'),
(12, 2, 'test', 'Jr Frontend JS developer', 0, 'creative', '2025-09-07 16:29:21', '2025-09-07 18:02:04', 'Rajan', 'test2@gmail.com', '9840402206', 'Bhaluhi', 'https://chatgpt.com/', 'https://chatgpt.com/', 'https ://cha tgpt .com/\r\nhttp s:// chatgpt. com/\r\nhttps://chatgpt. co m/h ttps:// chatg t.co m/htt ps:// chatgp t.com/ htt ps://c hatg pt.com/'),
(13, 2, 'Sr. Manager - Sales', 'Sr. Manager - Sales', 0, 'modern', '2025-09-08 13:48:56', '2025-09-14 12:40:05', 'Rajan', 'test2@gmail.com', '9840402206', 'butwal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'Highly motivated and results-oriented sales professional with a proven track record of success in developing and implementing effective sales strategies.  Seeking to leverage extensive experience and leadership skills to excel as a Sr. Manager - Sales, contributing to the growth and profitability of a dynamic organization. Proven ability to build and manage high-performing sales teams, exceeding targets consistently while fostering strong customer relationships.'),
(14, 2, 'Sr. Manager - Sales', 'Sr. Manager - Sales', 0, 'modern', '2025-09-08 13:50:44', '2025-09-14 12:10:28', 'Rajan', 'test2@gmail.com', '9840402206', 'butwal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'Highly motivated and results-oriented sales professional with a proven track record of success in the automobile industry.  Seeking a challenging Sr. Manager - Sales position where I can leverage my extensive experience in developing and implementing successful sales strategies, leading and motivating high-performing teams, and exceeding sales targets. Proven ability to build strong relationships with dealers and customers while maintaining a sharp focus on market trends and competitor analysis.'),
(15, 2, 'dada', 'da', 0, 'modern', '2025-09-14 01:45:20', '2025-09-14 01:45:20', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(16, 2, 'da', 'da', 0, 'modern', '2025-09-14 01:45:38', '2025-09-14 01:45:38', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(17, 2, 'ld', 'ds;', 0, 'modern', '2025-09-14 01:45:47', '2025-09-14 01:45:47', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(18, 2, 'da', 'ad', 0, 'modern', '2025-09-14 01:45:56', '2025-09-14 01:45:56', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(19, 2, 'da', 'dsa', 0, 'modern', '2025-09-14 01:46:17', '2025-09-14 01:46:17', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(20, 2, 'da', 'da', 0, 'modern', '2025-09-14 01:46:29', '2025-09-14 01:46:29', 'Rajan', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL),
(21, 2, 'Bajra\'s cv', 'Senior Marketing Manager', 0, 'creative', '2025-09-14 11:46:12', '2025-09-14 11:48:42', 'Rajan', 'test2@gmail.com', '9840402206', 'butwal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'Highly motivated and results-oriented sales professional with a proven track record of success in the automobile industry.  Seeking a challenging Sr. Manager - Sales position where I can leverage my extensive experience in developing and implementing successful sales strategies, leading and motivating high-performing teams, and exceeding sales targets. Proven ability to build strong relationships with dealers and customers while maintaining a sharp focus on market trends and competitor analysis.'),
(22, 2, 'Leapfrog CV', 'Senior Manager', 0, 'classic', '2025-09-14 12:45:40', '2025-09-14 12:48:19', 'Rajan', 'test2@gmail.com', '9840402206', 'Gyaneshwor-Kathmandu,Nepal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'Highly motivated and results-oriented sales professional with a proven track record of success in developing and implementing effective sales strategies.  Seeking to leverage extensive experience and leadership skills to excel as a Sr. Manager - Sales, contributing to the growth and profitability of a dynamic organization. Proven ability to build and manage high-performing sales teams, exceeding targets consistently while fostering strong customer relationships.'),
(23, 2, 'test', 'Jr Frontend JS developer', 0, 'modern', '2025-09-14 23:12:04', '2025-09-15 00:01:10', 'Rajan', 'test2@gmail.com', '9840403305', 'Bhaluhi', 'https://gcrajan.com.np', 'https://gcrajan.com.np', 'wow just test'),
(24, 4, 'Leapfrog CV', 'C# / .NET Developer', 1, 'modern', '2025-09-18 14:13:18', '2025-09-19 01:01:28', 'test1', 'test5@gmail.com', '9840402206', 'Gyaneshwor-Kathmandu,Nepal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'gvshfs ffsdfs fdsfd sd fsd f sd fs fs'),
(25, 4, 'samip', 'QA', 1, 'creative', '2025-09-18 15:05:37', '2025-09-19 01:01:26', 'Samip Shrestha', 'test5@gmail.com', '9840402206', 'Gyaneshwor-Kathmandu,Nepal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'wow sd f s fs dfs f sf sdd fs f sf sfd sf s fs fs f sdfsf dsfs fsfds'),
(26, 4, 'Bajra CV', 'Classic', 1, 'classic', '2025-09-18 18:39:02', '2025-09-19 01:01:27', 'test1', 'test5@gmail.com', '9840402206', 'Gyaneshwor-Kathmandu,Nepal', 'https://www.linkedin.com/in/gcrajan/', 'https://github.com/gcrajan/', 'aa se se sf s'),
(27, 10, 'wow', 'Jr Frontend JS developer', 1, 'modern', '2025-09-22 00:21:27', '2025-09-22 00:27:42', 'tester one', 'tester1@gmail.com', '9840403305', '', 'https://gcrajan.com.np', 'https://gcrajan.com.np', '');

-- --------------------------------------------------------

--
-- Table structure for table `cv_certificates`
--

CREATE TABLE `cv_certificates` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `certificate_name` varchar(150) NOT NULL,
  `issuing_organization` varchar(100) DEFAULT NULL,
  `issue_date` varchar(50) DEFAULT NULL,
  `credential_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_certificates`
--

INSERT INTO `cv_certificates` (`id`, `cv_id`, `certificate_name`, `issuing_organization`, `issue_date`, `credential_url`) VALUES
(2, 24, 'Jr developer', 'innovativenepalese', 'June 2020', 'https://www.gcrajan.com.np/'),
(4, 26, 'test certificate', 'innovativenepalese', 'jul', 'https://www.gcrajan.com.np/'),
(5, 25, 'Sabaishare ceritidi', 'Innovativenepalese', 'july 1999', 'https://www.gcrajan.com.np/');

-- --------------------------------------------------------

--
-- Table structure for table `cv_education`
--

CREATE TABLE `cv_education` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `degree` varchar(100) NOT NULL,
  `institution` varchar(100) DEFAULT NULL,
  `start_date` varchar(50) DEFAULT NULL,
  `end_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_education`
--

INSERT INTO `cv_education` (`id`, `cv_id`, `degree`, `institution`, `start_date`, `end_date`) VALUES
(6, 7, 'qeqeqeq', 'eqqeq', NULL, NULL),
(13, 8, 'Education1', 'Institution', '2012/12/30', '2023/11/12'),
(14, 8, 'Education2', 'Institution', '2023/11/19', '2024/11/12'),
(15, 10, 'Education1', 'Institution', '2010/12/12', '2013/12/12'),
(16, 10, 'Education2', 'Institution', '2014/12/12', '2017/12/12'),
(17, 11, 'sffsf', 'fssdfs', 'fs', 'fs'),
(19, 12, 'sdsfsfsdfs', 'fsdfsfsfs', '2021/12/12', '2024/12/12'),
(24, 21, '+2 / High School', 'Goldengate college', 'March 2022', 'April 2024'),
(25, 14, '+2 / High School', 'Goldengate college', 'March 2022', 'April 2024'),
(26, 13, '+2 / High School', 'Goldengate college', 'March 2022', 'April 2024'),
(27, 13, 'Bachelor\'s Degree', 'New Summit', 'Jan 2025', 'Present'),
(28, 22, '+2 / High School', 'Goldengate college', '2020/12/23', '2022/12/23'),
(29, 22, 'Bsc CSIT', 'New Summit College', '2018/05/01', '2020/10/12'),
(30, 23, 'wow', 'unknown', 'jul 20', 'march 20'),
(32, 24, '+2', 'Goldengate College', '2020/12/23', '2024/12/23'),
(34, 26, '+2', 'Goldengate College', '2020/12/23', '2024/12/23'),
(35, 25, '+2', 'Goldengate College', '2018/05/01', '2022/12/23');

-- --------------------------------------------------------

--
-- Table structure for table `cv_experience`
--

CREATE TABLE `cv_experience` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `start_date` varchar(50) DEFAULT NULL,
  `end_date` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_experience`
--

INSERT INTO `cv_experience` (`id`, `cv_id`, `job_title`, `company_name`, `start_date`, `end_date`, `description`) VALUES
(17, 5, 'asdad', 'dasdad', NULL, NULL, 'ewqeqew eqwqqwe qeeqewqe ewqweqweq ewqeqeq eqeqewe eqqeweqeq\r\nweqe eqe qe qeqeqq eqe \r\neqq  e q qwqeqe eqww qwe \r\neqeq eqeqe q qwewe e qe\r\newqq q eqeeq'),
(18, 6, 'dasad', 'dadsad', NULL, NULL, 'wdew wrwr wer wr wr wr wr wr werewr rewr\r\nwerew w ewr werer  ewt\r\nweteteet  etet e e tert\r\netretert e tet et er ett \r\ntere rt e tet et e'),
(21, 7, 'dexf', NULL, NULL, NULL, NULL),
(28, 8, 'Experience1', 'Company', '2012/12/23', '2023/12/30', 'Description :\r\newqeqeqeqeqeq\r\nweqweqeq\r\nqeqweqewqe\r\nqweqeqweqeqwq'),
(29, 8, 'Experience2', 'Company', '2012/12/23', '2023/12/30', 'Description :\r\newqeqeqeqeqeq\r\nweqweqeq\r\nqeqweqewqe\r\nqweqeqweqeqwq'),
(30, 10, 'job Title 1', 'Company', '2020/10/23', '2022/10/23', 'Description (one achievement per line)\r\nDescription (one achievement per line)\r\nDescription (one achievement per line)\r\n'),
(31, 10, 'job Title 2', 'Company', '2023/10/23', '2024/10/23', 'Description (one achievement per line)\r\nDescription (one achievement per line)\r\nDescription (one achievement per line)\r\n'),
(32, 11, 'sdsfsf', 'fsfsf', 'sfdd', 'fs', 'fsfdsffsf'),
(34, 12, 'asdasda', 'afsadfa', '2021/12/12', '2024/12/12', 'wfewf fdwf wfw fwefw few fw fw few f w few f wfe w fw ef ef w feew  fw ef fw fw fw.'),
(41, 21, 'Sales Manager', 'ABC Motors', '2020-05-01', '2022-04-30', 'Developed and implemented annual sales plans that consistently exceeded targets by 15%.\r\nManaged a team of 10 sales representatives, providing training, mentorship, and performance feedback.\r\nSuccessfully negotiated key partnerships with dealerships and improved customer satisfaction scores by 10%.'),
(42, 21, 'Assistant Sales Manager', 'XYZ Automobiles', '2018-06-01', '2020-04-30', 'Supported the Sales Manager in the development and execution of sales strategies.\r\nManaged day-to-day sales operations, ensuring efficiency and effectiveness.\r\nBuilt strong relationships with key customers and contributed to a 5% increase in sales revenue.'),
(43, 14, 'Sales Manager', 'ABC Motors', '2020-05-01', '2022-04-30', 'Developed and implemented annual sales plans that consistently exceeded targets by 15%.\r\nManaged a team of 10 sales representatives, providing training, mentorship, and performance feedback.\r\nSuccessfully negotiated key partnerships with dealerships and improved customer satisfaction scores by 10%.'),
(44, 14, 'Assistant Sales Manager', 'XYZ Automobiles', '2018-06-01', '2020-04-30', 'Supported the Sales Manager in the development and execution of sales strategies.\r\nManaged day-to-day sales operations, ensuring efficiency and effectiveness.\r\nBuilt strong relationships with key customers and contributed to a 5% increase in sales revenue.'),
(45, 13, 'Sales Manager', 'ABC Motors', 'June 2021', 'May 2023', 'Developed and implemented annual sales plans that consistently exceeded targets by 15%.\r\nManaged a team of 10 sales representatives, providing training, coaching, and mentoring to improve performance.\r\nCultivated strong relationships with key dealers, resulting in increased market share and customer satisfaction.'),
(46, 13, 'Assistant Sales Manager', 'XYZ Auto Group', 'January 2019', 'June 2021', 'Supported the Sales Manager in developing and executing sales strategies.\r\nAssisted in the training and development of new sales representatives.\r\nManaged day-to-day sales operations, ensuring efficient workflow and effective communication.'),
(47, 22, 'Sales Manager', 'innovativenepalese', 'June 2021', 'May 2023', 'Developed and implemented annual sales plans that consistently exceeded targets by 15%.\r\nManaged a team of 10 sales representatives, providing training, coaching, and mentoring to improve performance.\r\nCultivated strong relationships with key dealers, resulting in increased market share and customer satisfaction.'),
(48, 22, 'Assistant Sales Manager', 'XYZ Auto Group', 'January 2019', 'June 2021', 'Supported the Sales Manager in developing and executing sales strategies.\r\nAssisted in the training and development of new sales representatives.\r\nManaged day-to-day sales operations, ensuring efficient workflow and effective communication.'),
(49, 23, 'sss', 'sd', 'jun 1', 'july 30', 'adasda\r\ndasda\r\nsdad\r\nada'),
(51, 24, 'Intern', 'innovativenepalese', 'jun 2020', 'july 2024', 'dsd s sf sd fsd f ds f sd f s fd sfd ds fs '),
(54, 26, 'Intern', 'innovativenepalese', 'Jul 2020', 'june 2024', 'jsfks fsdfsd fs f sf s'),
(55, 25, 'Intern', 'innovativenepalese', 'June 2020', 'July2023', 'sf sdfs fd sf sfs fs df s f');

-- --------------------------------------------------------

--
-- Table structure for table `cv_projects`
--

CREATE TABLE `cv_projects` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `project_name` varchar(150) NOT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_projects`
--

INSERT INTO `cv_projects` (`id`, `cv_id`, `project_name`, `project_url`, `description`) VALUES
(1, 23, 'test', 'https://gcrajan.com.np', 'wow sa dsa'),
(3, 24, 'Sabaishare', 'https://www.gcrajan.com.np/', 'this snsds as sd ada sd as das'),
(5, 26, 'test project', '', 'test sd sdf sf sdf sf'),
(6, 25, 'Sabaishare', 'https://www.gcrajan.com.np/', 'sfd https://www.gcrajan.com.np/ gdfg  fds sf s');

-- --------------------------------------------------------

--
-- Table structure for table `cv_skills`
--

CREATE TABLE `cv_skills` (
  `id` int(11) NOT NULL,
  `cv_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cv_skills`
--

INSERT INTO `cv_skills` (`id`, `cv_id`, `skill_name`) VALUES
(9, 5, 'test'),
(10, 5, 'adda asd'),
(11, 5, 'adasda das'),
(12, 5, 'dasdad'),
(25, 7, 'eqrqq'),
(26, 7, 'qe qwe'),
(27, 7, 'eqe qee'),
(28, 7, 'e qe'),
(29, 7, 'qe eq'),
(30, 7, 'eqe'),
(31, 10, 'bode js'),
(32, 10, 'skills'),
(33, 11, 'sf'),
(34, 11, 'fsfs'),
(35, 11, 'fsf'),
(39, 12, 'js'),
(40, 12, 'node'),
(41, 12, 'sql'),
(87, 21, 'Sales Strategy Development'),
(88, 21, 'Annual/Monthly Sales Planning'),
(89, 21, 'Team Management'),
(90, 21, 'Dealer Relationship Management'),
(91, 21, 'Sales Training & Development'),
(92, 21, 'Market Trend Analysis'),
(93, 21, 'Competitor Analysis'),
(94, 21, 'Sales Forecasting & Reporting'),
(95, 21, 'Negotiation'),
(96, 21, 'Leadership'),
(97, 21, 'Customer Relationship Management'),
(98, 21, 'Performance Analysis'),
(99, 21, 'Communication'),
(100, 21, 'Presentation Skills'),
(101, 21, 'Problem-solving'),
(102, 14, 'Sales Strategy Development'),
(103, 14, 'Annual/Monthly Sales Planning'),
(104, 14, 'Team Management'),
(105, 14, 'Dealer Relationship Management'),
(106, 14, 'Sales Training & Development'),
(107, 14, 'Market Trend Analysis'),
(108, 14, 'Competitor Analysis'),
(109, 14, 'Sales Forecasting & Reporting'),
(110, 14, 'Negotiation'),
(111, 14, 'Leadership'),
(112, 14, 'Customer Relationship Management'),
(113, 14, 'Performance Analysis'),
(114, 14, 'Communication'),
(115, 14, 'Presentation Skills'),
(116, 14, 'Problem-solving'),
(117, 13, 'Sales Strategy Development'),
(118, 13, 'Team Management'),
(119, 13, 'Leadership'),
(120, 13, 'Dealer Relationship Management'),
(121, 13, 'Sales Forecasting'),
(122, 13, 'Performance Analysis'),
(123, 13, 'Negotiation'),
(124, 13, 'Communication'),
(125, 13, 'Presentation Skills'),
(126, 13, 'Market Trend Analysis'),
(127, 13, 'Customer Relationship Management'),
(128, 13, 'Sales Training'),
(129, 13, 'Report Preparation'),
(130, 13, 'Problem-solving'),
(131, 13, 'Team Motivation'),
(132, 22, 'Sales Strategy Development'),
(133, 22, 'Team Management'),
(134, 22, 'Leadership'),
(135, 22, 'Dealer Relationship Management'),
(136, 22, 'Sales Forecasting'),
(137, 22, 'Performance Analysis'),
(138, 22, 'Negotiation'),
(139, 22, 'Communication'),
(140, 22, 'Presentation Skills'),
(141, 22, 'Market Trend Analysis'),
(142, 22, 'Customer Relationship Management'),
(143, 22, 'Sales Training'),
(144, 22, 'Report Preparation'),
(145, 22, 'Problem-solving'),
(146, 22, 'Team Motivation'),
(147, 23, 'php'),
(148, 23, 'js'),
(149, 23, 'html'),
(153, 24, 'js'),
(154, 24, 'php'),
(155, 24, 'my sql'),
(168, 26, 'js'),
(169, 26, 'C#'),
(170, 26, 'my sql'),
(171, 26, 'tailwind css'),
(172, 26, 'bootstrap'),
(173, 26, 'figma'),
(174, 26, 'react'),
(175, 25, 'AWS'),
(176, 25, 'Linux'),
(177, 25, 'Kubernetes'),
(178, 25, 'Terraform'),
(179, 25, 'Docker'),
(180, 25, 'CI/CD (Jenkins'),
(181, 25, 'GitHub Actions)'),
(182, 25, 'Bash/Python scripting'),
(183, 25, 'MySQL/PostgreSQL'),
(184, 25, 'Networking (TCP/IP'),
(185, 25, 'DNS)'),
(186, 25, 'Security best practices');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `recruiter_user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Contract','Internship') NOT NULL DEFAULT 'Full-time',
  `is_remote` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `posted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deadline` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_user_id`, `company_id`, `title`, `description`, `location`, `job_type`, `is_remote`, `is_active`, `is_featured`, `posted_at`, `deadline`) VALUES
(2, 1, 1, 'Data Science', 'fd sf fsf sf sfs fsd', 'Butwal-10, Rupandehi', 'Full-time', 1, 1, 0, '2025-08-30 10:18:26', '2025-09-29 10:18:26'),
(3, 1, 1, 'Title', 'Job Description', 'Location', 'Full-time', 0, 1, 1, '2025-08-31 00:21:34', '2025-09-30 00:21:34'),
(4, 5, 2, 'test', 'test', 'test', 'Full-time', 0, 1, 0, '2025-09-01 19:56:42', '2025-10-01 19:56:42'),
(5, 7, 3, 'test by code/dude', 'test', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-01 21:44:51', '2025-10-01 21:44:51'),
(6, 1, 1, 'ds', 's', 'sfs', 'Full-time', 0, 1, 0, '2025-09-06 12:54:03', '2025-10-06 12:54:03'),
(7, 1, 1, 'test1', '<h2>Yo you will love this.</h2><p><a href=\"www.gcrajan.com.np\">tesy</a></p><p>apply for it now.</p><ul><li>jgu vhhh jjb</li><li>jbj bbnj jbj</li><li>hhhffh</li></ul>', 'resdf', 'Full-time', 1, 1, 0, '2025-09-07 14:28:20', '2025-10-07 14:28:20'),
(8, 5, 2, 'React js', '<h3>📝 <strong>Job Description:</strong></h3><p>TechNova Innovations is seeking a passionate and experienced <strong>Frontend Developer (React.js)</strong> to join our dynamic and fast-growing product development team. You’ll be responsible for building rich, interactive user experiences and crafting pixel-perfect interfaces that scale.</p><p>As a member of our frontend team, you’ll collaborate closely with UX designers, backend developers, and product managers to deliver high-quality web applications that delight users.</p><h3>🎯 <strong>Key Responsibilities:</strong></h3><ul><li>Develop new user-facing features using <strong>React.js</strong></li><li>Build reusable components and frontend libraries for future use</li><li>Translate UI/UX wireframes into high-quality code</li><li>Optimize components for maximum performance across a vast array of devices and browsers</li><li>Collaborate with cross-functional teams in an Agile environment</li><li>Participate in code reviews and contribute to best practices</li><li>Troubleshoot and debug UI issues</li><li>Stay up-to-date with the latest industry trends and technologies</li></ul><h3>✅ <strong>Requirements:</strong></h3><ul><li>Bachelor\'s degree in Computer Science or related field, or equivalent experience</li><li>2+ years of experience with <strong>React.js</strong> and frontend development</li><li>Proficiency in <strong>JavaScript (ES6+), HTML5, CSS3</strong></li><li>Experience with state management libraries like Redux or Context API</li><li>Familiarity with RESTful APIs and JSON</li><li>Good understanding of responsive and adaptive design</li><li>Version control (Git) and experience with GitHub/GitLab</li><li>Attention to detail and a passion for great user experience</li></ul><h3>🌟 <strong>Nice to Have:</strong></h3><ul><li>Experience with <strong>TypeScript</strong></li><li>Familiarity with testing frameworks (Jest, React Testing Library)</li><li>Knowledge of frontend build tools (Webpack, Babel, Vite)</li><li>Exposure to CI/CD pipelines</li><li>Experience with accessibility standards (WCAG)</li></ul><h3>🎁 <strong>Benefits:</strong></h3><ul><li>💼 Competitive salary: $75,000 – $95,000/year (based on experience)</li><li>🏥 Full health, dental &amp; vision insurance</li><li>💻 New MacBook Pro &amp; hardware budget</li><li>🏖️ Flexible PTO and remote work days</li><li>🏋️ Onsite gym + wellness reimbursement</li><li>📚 Annual learning &amp; development stipend ($1,500)</li><li>🎉 Team off-sites, hackathons, and virtual coffee chats</li></ul><h3>📅 <strong>Posted on:</strong> September 7, 2025</h3><h3>📅 <strong>Application Deadline:</strong> October 10, 2025</h3><h3>🧍‍♂️ <strong>Hiring Manager:</strong></h3><p><strong>Jordan Smith</strong>Senior Engineering Manager📧 <a href=\"mailto:jordan.smith@technovainc.com\">jordan.smith@technovainc.com</a></p><p>📂 <strong>How to Apply:</strong></p><p>Click the <strong>\"Apply Now\"</strong> button or submit your resume and portfolio to careers@technovainc.com.</p><p>We’re excited to meet the next great mind to join our mission of shaping the future of digital products!</p>', 'Gatthaghar', 'Full-time', 0, 1, 1, '2025-09-07 15:23:30', '2025-10-07 15:23:30'),
(9, 1, 1, 'test', '<p>hi, there, can we talk for a second.</p><p>yes</p><p>dssdfs s fsdfs sd sd fsd fsd</p><img src=\"http://localhost/recruitercv/uploads/job_images/68bd805d90a12-recruiter.png\" alt=\"68bd805d90a12-recruiter.png\" /><p>yes and what do you deoeo adf sf sfd sd fdsd fd sd fs df sd</p><ul><li>fsfsfsdfsfs</li><li>asdsafdsdds</li><li>sdfsfdsdf</li><li>sfdsdf</li><li>dsfsd</li><li>fsd</li><li>fsdfsfsfdsfsdf</li></ul><p>sfsfdfdfd a ss sdfsdf sf </p><p> </p><img src=\"http://localhost/recruitercv/uploads/job_images/68bd8099a4a57-logo.png\" alt=\"68bd8099a4a57-logo.png\" />', 'dsa', 'Full-time', 0, 0, 0, '2025-09-07 15:43:10', '2025-10-07 15:43:10'),
(10, 7, 3, 'test', '<h3>Welcome to the RecruiterCV Career Hub! Your Journey Starts Here.</h3><p>A very warm welcome to RecruiterCV! Whether you’re a talented professional ready to take the next step in your career or a visionary recruiter searching for the perfect candidate, you’ve come to the right place. We are thrilled to have you here.</p><p>Our mission at RecruiterCV is simple but powerful: to bridge the gap between exceptional talent and the innovative companies that need them. We believe that the right opportunity can change a life, and the right person can transform a business. This platform was built to make that connection seamless, efficient, and inspiring.</p><p>And that’s where the <strong>Career Hub</strong> comes in. Think of this blog as your personal career coach, your industry insider, and your ultimate resource for navigating the world of recruitment and career development. It’s much more than a feature; it’s the heart of our community.</p><img src=\"http://localhost/recruitercv/uploads/job_images/68c29df71f412-Image_fx%20(2).jpg\" alt=\"68c29df71f412-Image_fx%20(2).jpg\" /><h3>What to Expect from This Blog</h3><p>We\'re committed to providing you with high-value content that empowers you on your journey. We won\'t just be talking about jobs; we\'ll be talking about careers, growth, and the future of work. Here’s a glimpse of what you’ll find:</p><h3>For Our Talented Professionals (Recruitees)</h3><p>Your journey to a new career starts with a single, powerful step: creating a standout CV. This is your professional story, your first impression, and your key to unlocking incredible opportunities. On RecruiterCV, you have two fantastic tools at your disposal: our intuitive <strong>\"Create with AI\"</strong> feature for a fast, intelligent draft, or the <strong>\"Manual Editor\"</strong> for full creative control. Create your CV, set it to public, and let the opportunities find you. Your next great role could be just one click away from a recruiter who is searching for someone exactly like you.</p><p><strong>Ready to get started? Build Your CV Now!</strong></p><h3>For Our Visionary Recruiters</h3><p>The perfect candidate is out there, and we\'re here to help you connect with them. A clear, detailed, and engaging job post is the first step to attracting the high-quality applicants your team deserves. Post your first job today and reach a growing community of skilled professionals. But why wait for them to come to you? Use our powerful <strong>\"Find Talent\"</strong> feature to proactively search our database and discover hidden gems who have made their CVs public. The proactive approach often lands the most remarkable hires.</p><img src=\"http://localhost/recruitercv/uploads/job_images/68c29e1c791e8-Image_fx%20(2).jpg\" alt=\"68c29e1c791e8-Image_fx%20(2).jpg\" /><p><strong>Looking for your next great hire? Post a Job Today!</strong></p><h3>Let\'s Build the Future, Together</h3><p>We believe that RecruiterCV is more than just a platform; it\'s a community dedicated to professional growth and success. This Career Hub is for you, and we are incredibly excited to embark on this journey with you.</p><p>So go ahead, explore the platform, build that amazing CV, post that game-changing job, and know that we are here to support you every step of the way. Welcome to the future of recruitment. Welcome to RecruiterCV.</p>', 'Butwal-10, Rupandehi', 'Full-time', 0, 1, 0, '2025-09-11 15:47:10', '2025-10-11 15:47:10'),
(11, 5, 2, 'test1', '<p>sa</p>', 'a', 'Full-time', 0, 1, 0, '2025-09-14 01:41:11', '2025-10-14 01:41:11'),
(12, 5, 2, 'test2', '<p>a</p>', 's', 'Full-time', 0, 1, 0, '2025-09-14 01:41:25', '2025-10-14 01:41:25'),
(13, 5, 2, 'test3', '<p>sa</p>', '2', 'Full-time', 0, 1, 0, '2025-09-14 01:41:37', '2025-10-14 01:41:37'),
(14, 5, 2, 'test4', '<p>da</p>', 'a', 'Full-time', 0, 1, 0, '2025-09-14 01:41:47', '2025-10-14 01:41:47'),
(15, 5, 2, 'test5', '<img src=\"http://localhost/recruitercv/uploads/job_images/68c5cc912d768-Image_fx%20(1).jpg\" alt=\"68c5cc912d768-Image_fx%20(1).jpg\" />', 's', 'Full-time', 0, 1, 0, '2025-09-14 01:42:07', '2025-10-14 01:42:07'),
(16, 5, 2, 'test6', '<p>da</p>', 'sa', 'Full-time', 0, 1, 0, '2025-09-14 01:42:20', '2025-10-14 01:42:20'),
(17, 5, 2, 'test6', '<p>ds</p>', 'a', 'Full-time', 0, 1, 0, '2025-09-14 01:42:49', '2025-10-14 01:42:49'),
(18, 5, 2, 'test6', '<p>da</p>', 'asd', 'Full-time', 0, 1, 0, '2025-09-14 01:43:04', '2025-10-14 01:43:04'),
(19, 5, 2, 'test8', '<p>da</p>', 'sad', 'Full-time', 0, 1, 0, '2025-09-14 01:43:15', '2025-10-14 01:43:15'),
(20, 5, 2, 'da', '<p>da</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-14 01:43:25', '2025-10-14 01:43:25'),
(21, 5, 2, 'sa', '<p>da</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-14 01:44:15', '2025-10-14 01:44:15'),
(22, 5, 2, 'dadad', '<p>da</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-14 01:44:30', '2025-10-14 01:44:30'),
(23, 5, 2, 'das', '<p>da</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-14 01:44:42', '2025-10-14 01:44:42'),
(24, 5, 2, 'da', '<p>da</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-14 01:44:54', '2025-10-14 01:44:54'),
(25, 5, 2, 'Jr. Java developer', '<p>wo, fisd dsfs fsdf sfd d fd</p>', 'Gatthaghar, Bhaktapur', 'Full-time', 1, 1, 0, '2025-09-14 09:20:00', '2025-10-14 09:20:00'),
(26, 5, 2, 'Database engineer', '<p>test</p>', 'Gatthaghar', 'Full-time', 0, 1, 0, '2025-09-14 10:24:38', '2025-10-14 10:24:38'),
(27, 5, 2, 'Jr. Tester', '<p>test</p>', 'Gatthaghar', 'Full-time', 0, 1, 1, '2025-09-14 10:24:57', '2025-10-14 10:24:57'),
(28, 7, 3, 'test', '<p>test</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 20:20:30', '2025-10-18 20:20:30'),
(29, 7, 3, 'test1', '<p>test</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 20:30:27', '2025-10-18 20:30:27'),
(30, 7, 3, 'tets', '<p>test</p><img src=\"http://localhost/recruitercv/uploads/job_images/68cc1b23f1213-ChatGPT%20Image%20Aug%2026,%202025,%2012_33_31%20PM.png\" alt=\"68cc1b23f1213-ChatGPT%20Image%20Aug%2026,%202025,%2012_33_31%20PM.png\" />', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 20:31:00', '2025-10-18 20:31:00'),
(31, 7, 3, 'test', '<p>test</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 20:31:14', '2025-10-18 20:31:14'),
(32, 7, 3, 'wow', '<p>twstsa</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 20:31:26', '2025-10-18 20:31:26'),
(33, 7, 3, 'wow', '<p>sda</p>', 'test', 'Full-time', 0, 1, 0, '2025-09-18 20:31:37', '2025-10-18 20:31:37'),
(34, 7, 3, 'ada', '<p>dada</p>', 'Butwal-10, Rupandehi', 'Full-time', 0, 1, 0, '2025-09-18 20:31:50', '2025-10-18 20:31:50'),
(35, 7, 3, 'ad', '<p>da</p>', 'sad', 'Full-time', 0, 1, 0, '2025-09-18 20:31:58', '2025-10-18 20:31:58'),
(36, 7, 3, 'test', '<p>ad</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-18 21:23:50', '2025-10-18 21:23:50'),
(37, 7, 3, 'scs', '<p>dasa</p>', 'da', 'Full-time', 0, 1, 0, '2025-09-18 21:24:13', '2025-10-18 21:24:13'),
(38, 7, 3, 'sad', '<p>fs</p>', 'dsf', 'Full-time', 0, 1, 0, '2025-09-18 21:24:29', '2025-10-18 21:24:29'),
(39, 7, 3, 'fds', '<p>fs</p>', 'fds', 'Full-time', 0, 1, 0, '2025-09-18 21:24:38', '2025-10-18 21:24:38'),
(40, 7, 3, 'ger', '<p>egre</p>', 'geege', 'Full-time', 0, 1, 0, '2025-09-18 21:24:53', '2025-10-18 21:24:53'),
(41, 7, 3, 'eger', '<p>ger</p>', 'ge', 'Full-time', 0, 1, 0, '2025-09-18 21:25:01', '2025-10-18 21:25:01'),
(42, 7, 3, 'eg', '<p>ge</p>', 'ger', 'Full-time', 0, 1, 0, '2025-09-18 21:25:10', '2025-10-18 21:25:10'),
(43, 7, 3, 'ge', '<p>ge</p>', 'egr', 'Full-time', 0, 1, 0, '2025-09-18 21:25:20', '2025-10-18 21:25:20'),
(44, 7, 3, 'gereg', '<p>gere</p>', 'gereg', 'Full-time', 0, 1, 1, '2025-09-18 21:25:36', '2025-10-18 21:25:36'),
(46, 7, 3, 'Tester QA', '<p>test</p>', 'Kathmandu', 'Full-time', 1, 1, 0, '2025-09-21 18:26:54', '2025-09-21 16:27:00'),
(47, 7, 3, 'latest job', '<p>test</p>', 'Kathmandu', 'Full-time', 0, 1, 0, '2025-09-21 18:29:38', '2025-11-28 17:31:00');

-- --------------------------------------------------------

--
-- Table structure for table `job_comments`
--

CREATE TABLE `job_comments` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_comments`
--

INSERT INTO `job_comments` (`id`, `job_id`, `user_id`, `parent_comment_id`, `comment_text`, `created_at`) VALUES
(1, 1, 2, NULL, 'wow', '2025-08-30 10:11:13'),
(2, 1, 1, 1, 'yes, that is great www.gcrajan.com.np', '2025-08-30 10:11:57'),
(3, 2, 2, NULL, 'wow', '2025-08-30 10:20:30'),
(4, 2, 2, NULL, 'yes', '2025-08-30 12:32:39'),
(5, 1, 1, NULL, 'dqw', '2025-08-30 12:33:57'),
(6, 2, 1, NULL, 'jt', '2025-08-31 00:19:27'),
(7, 3, 1, NULL, 'as', '2025-08-31 00:21:53'),
(8, 3, 2, NULL, 'asaada', '2025-08-31 00:22:05'),
(9, 4, 2, NULL, 'test recruitee', '2025-09-01 19:59:32'),
(10, 6, 2, NULL, 'wow', '2025-09-06 12:54:21'),
(11, 6, 1, NULL, 't', '2025-09-06 13:04:33'),
(12, 6, 1, 11, 'wow', '2025-09-06 20:17:18'),
(13, 6, 1, 10, 'hell yes', '2025-09-06 20:17:28'),
(14, 6, 2, 11, 'yep', '2025-09-06 20:17:58'),
(15, 6, 2, NULL, 'hey', '2025-09-07 01:19:01'),
(16, 6, 2, NULL, 'test again', '2025-09-07 01:20:42'),
(17, 6, 2, NULL, 'test again', '2025-09-07 01:20:47'),
(18, 6, 2, NULL, 'paste', '2025-09-07 01:21:04'),
(19, 6, 2, NULL, 'test', '2025-09-07 01:30:41'),
(20, 6, 2, NULL, 'wow', '2025-09-07 01:35:07'),
(21, 6, 2, NULL, 'wow', '2025-09-07 01:35:15'),
(22, 6, 2, NULL, 'test', '2025-09-07 01:38:03'),
(23, 6, 2, NULL, 'hi', '2025-09-07 01:43:48'),
(24, 9, 2, NULL, 'frdd', '2025-09-07 15:43:46'),
(25, 8, 2, NULL, 'test', '2025-09-07 16:06:50'),
(26, 8, 5, 25, 'yep vhj, hb  kvk vvhkhv vmhvgvhhh v vgvg  v kv  vg vvmhjb nk vqwqqe qw qeqe wqe qwewqeq.', '2025-09-07 16:07:39'),
(27, 8, 5, 25, 'ffjg dada g gccf dadad addad', '2025-09-07 16:10:41'),
(28, 8, 2, 25, 'dqw wq dq dwqd', '2025-09-07 16:11:40'),
(29, 8, 3, NULL, 'wow', '2025-09-11 14:25:04'),
(30, 26, 2, NULL, 'test', '2025-09-14 11:27:45'),
(31, 27, 2, NULL, 'test', '2025-09-14 11:29:42'),
(32, 27, 2, 31, 'test', '2025-09-14 11:32:10'),
(33, 19, 3, NULL, 'wow', '2025-09-18 21:28:46'),
(36, 19, 3, NULL, 'yes', '2025-09-18 22:44:38'),
(38, 19, 3, NULL, 'yes', '2025-09-18 22:46:03'),
(52, 28, 7, NULL, 'cjheck', '2025-09-19 00:38:20'),
(53, 28, 4, NULL, 'test', '2025-09-19 00:40:09'),
(58, 28, 4, NULL, 'wow', '2025-09-19 01:00:42'),
(59, 28, 4, 52, 'fucl', '2025-09-19 01:00:51');

-- --------------------------------------------------------

--
-- Table structure for table `job_likes`
--

CREATE TABLE `job_likes` (
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_likes`
--

INSERT INTO `job_likes` (`job_id`, `user_id`, `created_at`) VALUES
(1, 1, '2025-08-30 06:48:50'),
(1, 2, '2025-08-29 18:17:26'),
(2, 1, '2025-08-30 06:48:39'),
(2, 2, '2025-08-30 04:34:58'),
(2, 3, '2025-08-30 18:16:29'),
(2, 4, '2025-08-30 18:19:52'),
(3, 2, '2025-08-30 18:36:59'),
(4, 2, '2025-09-01 14:14:19'),
(6, 2, '2025-09-07 03:42:45'),
(27, 2, '2025-09-14 05:47:30'),
(27, 4, '2025-09-18 12:51:59'),
(28, 4, '2025-09-18 15:44:17'),
(44, 4, '2025-09-18 19:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The ID of the user who will RECEIVE the notification.',
  `type` enum('new_applicant','status_change','new_comment','new_reply','new_rating') NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL COMMENT 'The URL to go to when clicked.',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:19:01'),
(2, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:20:42'),
(3, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:20:47'),
(4, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:21:04'),
(5, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:30:41'),
(6, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:35:07'),
(7, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:35:15'),
(8, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-', 1, '2025-09-07 01:38:03'),
(9, 1, 'new_comment', 'Rajan commented on your job.', 'view_job.php?id=6#comment-23', 1, '2025-09-07 01:43:48'),
(10, 1, 'new_rating', 'Rajan gave you a 2-star rating.', 'profile.php?id=2', 1, '2025-09-07 01:43:58'),
(11, 1, 'new_rating', 'Rajan gave you a 4-star rating.', 'profile.php?id=2', 1, '2025-09-07 09:48:08'),
(12, 1, 'new_rating', 'Rajan gave you a 5-star rating.', 'profile.php?id=2', 1, '2025-09-07 09:48:32'),
(13, 1, 'new_rating', 'Rajan gave you a 1-star rating.', 'profile.php?id=2', 1, '2025-09-07 09:59:26'),
(14, 1, 'new_rating', 'Rajan gave you a 4-star rating.', 'profile.php?id=2', 1, '2025-09-07 09:59:33'),
(16, 1, 'new_rating', '<strong>Rajan</strong> gave you a <strong>2-star rating</strong> on your profile.', 'profile.php?id=2', 1, '2025-09-07 10:31:52'),
(20, 1, 'new_comment', '<strong>Rajan</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=9#comment-24', 1, '2025-09-07 15:43:46'),
(24, 4, 'new_rating', '<strong>test</strong> gave you a <strong>4-star rating</strong> on your profile.', 'profile.php?id=3', 0, '2025-09-10 15:02:24'),
(28, 5, 'new_comment', '<strong>Rajan</strong> commented on your job \'<strong>Database engineer</strong>\'.', 'view_job.php?id=26#comment-30', 1, '2025-09-14 11:27:45'),
(29, 5, 'new_applicant', '<strong>Rajan</strong> applied for your \'<strong>Database engineer</strong>\' job.', 'applicants.php?job_id=26', 1, '2025-09-14 11:28:31'),
(30, 5, 'new_comment', '<strong>Rajan</strong> commented on your job \'<strong>Jr. Tester</strong>\'.', 'view_job.php?id=27#comment-31', 0, '2025-09-14 11:29:42'),
(31, 5, 'new_applicant', '<strong>Rajan</strong> applied for your \'<strong>Jr. Tester</strong>\' job.', 'applicants.php?job_id=27', 0, '2025-09-14 11:29:56'),
(32, 5, 'new_rating', '<strong>Rajan</strong> gave you a <strong>4-star rating</strong> on your profile.', 'profile.php?id=2', 0, '2025-09-14 11:32:45'),
(33, 5, 'new_applicant', '<strong>test1</strong> applied for your \'<strong>Jr. Tester</strong>\' job.', 'applicants.php?job_id=27', 0, '2025-09-18 18:38:06'),
(34, 5, 'new_comment', '<strong>test</strong> commented on your job \'<strong>test8</strong>\'.', 'view_job.php?id=19#comment-33', 0, '2025-09-18 21:28:46'),
(35, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-34', 1, '2025-09-18 21:29:29'),
(36, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-35', 1, '2025-09-18 22:21:01'),
(37, 5, 'new_comment', '<strong>test</strong> commented on your job \'<strong>test8</strong>\'.', 'view_job.php?id=19#comment-36', 0, '2025-09-18 22:44:38'),
(38, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-37', 1, '2025-09-18 22:45:35'),
(39, 5, 'new_comment', '<strong>test</strong> commented on your job \'<strong>test8</strong>\'.', 'view_job.php?id=19#comment-38', 0, '2025-09-18 22:46:03'),
(40, 4, 'new_comment', '<strong>dude</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-44', 0, '2025-09-19 00:12:42'),
(41, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-45', 1, '2025-09-19 00:29:16'),
(42, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-46', 1, '2025-09-19 00:35:30'),
(43, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-48', 1, '2025-09-19 00:35:49'),
(44, 7, 'new_comment', '<strong>test1</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-50', 1, '2025-09-19 00:36:35'),
(45, 4, 'new_comment', '<strong>dude</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-51', 0, '2025-09-19 00:38:15'),
(46, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-53', 1, '2025-09-19 00:40:09'),
(47, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-54', 1, '2025-09-19 00:58:39'),
(48, 4, 'new_comment', '<strong>dude</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-56', 0, '2025-09-19 00:59:38'),
(49, 4, 'new_comment', '<strong>dude</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-57', 0, '2025-09-19 00:59:43'),
(50, 7, 'new_comment', '<strong>test1</strong> commented on your job \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-58', 1, '2025-09-19 01:00:42'),
(51, 7, 'new_comment', '<strong>test1</strong> replied to your comment on \'<strong>test</strong>\'.', 'view_job.php?id=28#comment-59', 1, '2025-09-19 01:00:51'),
(52, 7, 'new_applicant', '<strong>test1</strong> applied for your \'<strong>test</strong>\' job.', 'applicants.php?job_id=28', 1, '2025-09-19 01:01:38'),
(53, 7, 'new_applicant', '<strong>test1</strong> applied for your \'<strong>gereg</strong>\' job.', 'applicants.php?job_id=44', 1, '2025-09-19 01:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(1, 'test@gmail.com', '92ea7d77b6ab84524312b995d135b854cc2c722d31c979a477fd9bc3bca5476d', '2025-09-09 11:02:44'),
(3, 'test@gmail.com', '527596cbc48061178382a2d9988fe309e036648a87d11379fa4d77d4d3427614', '2025-09-09 09:20:56'),
(4, 'test@gmail.com', '03b7039b57be74ca2ec2418f9b675b8f88f2bb0648818ff884fb020b44a2334d', '2025-09-09 16:44:22'),
(6, 'test@gmail.com', '182c8183cbeb118773c16ac6ab59b12d8fc2938c12adff8480816613a7509f21', '2025-09-10 05:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `pending_signups`
--

CREATE TABLE `pending_signups` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `form_data` text NOT NULL COMMENT 'JSON encoded data from the signup form',
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_signups`
--

INSERT INTO `pending_signups` (`id`, `email`, `otp_hash`, `form_data`, `expires_at`) VALUES
(8, 'tester11@gmail.com', '$2y$10$TB/Z5VoO5YFJIjCAf1x.PuMjBTKC.0cCDY4.miC8pWuFGnbsTRF56', '{\"name\":\"tester eleven\",\"password_hash\":\"$2y$10$PY9tvonwmEpF8eSTs7MCaORvcyyUPTnT5jnHNIXbYhq7RZzTlyhE.\",\"user_type\":\"recruiter\",\"company_name\":\"Jhamghat ltd\",\"company_website\":\"https:\\/\\/jhamghat.free.nf\\/\",\"profile_image_data\":{\"type\":\"image\\/jpeg\",\"data\":\"\\/9j\\/4AAQSkZJRgABAQAAAQABAAD\\/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH\\/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH\\/wAARCANABcADASIAAhEBAxEB\\/8QAHwAAAAYDAQEBAAAAAAAAAAAAAgMEBQYHAQgJAAoL\\/8QAVxAAAQMDAwMDAwIEAwYEAAEdAQIDEQQFIQYSMQAHQRMiUQgUYTJxFSNCgQlSkRYkM2KhsRc0Q3IlgsEmRFPR4Rg1Y5Ki8DZFVPEKc8IZJ1VWZGV0g7L\\/xAAeAQAABgMBAQAAAAAAAAAAAAACAwQFBgcAAQgJCv\\/EAFMRAAEDAgQDBQUGBAUCBQEBEQECAxEEIQAFEjEGQVEHEyJhcRSBkaHwCCMyscHRFULh8QkWJDNSQ2IlNFNyghdjRJKiNUVUc4OywhgmVWSF0uL\\/2gAMAwEAAhEDEQA\\/APjGSgAABIGAMxtwBiYgEnOAI6NAScYHmPPyYgDBGAIMnIxHWBEYI5EgjBVGPiZOMkjA\\/boYBHO4qGTlQJMzABM+RJBJBMAGDNopHkTYWF7TFxz8vdIIxKEJJMwAJn8pHT66YCSExBwRJ2+MRJwTgjgZMxPnoYSCDIAVMQAfkYggkgAgQDgdeifMQD5ORPk8\\/q4ISZAiPgWDGf8AKAREgGIyognM+2cHn8mJEkquJ5HkTB3nByQQSfhc222+V97YykDkgTiYkDjMzJwMgTOR\\/YW0RkiRiZiZjkkR5JnED9uvR+PgxKoIzBEYyZwB\\/wDGHWRjKicj84BI3CDAwIjBMkgAjneoX+PI2sOXT37HlGDgLbSDym8Eje2\\/PcCOmMAAH5B5H4yTwkGBiI\\/BnI6GkD25B9o+SSJPEARJkERyP1degkjgRAJPwQT4OCqIBA4B8kdZAI3EnGT7pJnBIIJggGQBMEAkcZ3N4g7xJ2+jy64xIJI3jfeeQjbziPTYc8gCP0gGBAggnMZwYyYgEQInEEZgGYCcEeD7oESPM590Y4zz1mJyZESQAY3RgfJJ90giUnAJmB16CZjJycyBEbY5Kok8wOIOecIne\\/L69PlywOB8I+RB\\/THhECCCdokRj87jt4J4KcnA8Z9g5BAESMR5kA48\\/pGYJHM46zBBA8qAI+T+YBEnEiQTkAkT1mB+Z+ROeMySACZjaROcY6BotvvHuP69I\\/bGkpgnp+UkeXp+XmQQT4k\\/pjiRAgqT8zEZjgQelbwHqmCkbEpTH7ApmSPBIAgSeBxPQGkbnEJgwpScJP5IOMyAJ3AcRGeOjXAC4v2jCjKjyQCcmDgYwRMHdgDHQgkBJB6g\\/wBvS2+\\/MYUNphJtH4YPltF9r8\\/W8HBQgyoiMjAJ9vOOMwIPIx5zPR6BCSVbfGQJHtP4jBEASYOfieiwhQOAY4kZiAZSBI5Jx84yej1DajYIJIMJE5wJnMyABPIJnOD1gABsI8usxF\\/cducnGttRA2AH5eX8sTz64RGN3ABwZ2z\\/ANoBIwMGDB+Ceo3UUTdM+WQhApKlS10ySNqWX1AqfpQoAwlad7rYAxC0AnYAZPtMBIAgkADMgxJBMTkDOI4Byek9VTN1LKmnJAXtKVJyppaZUhxJPJbWQpMDOUmQSDpQgBQ3QZA69U3sARHwGCHGu9QYHiSQUknmIkbTf8vS8EdbVQvpU2Gp3eq0SFHcjcrchYkSIwc+4FIOMl4cQm40qWEjeUpXU2xUSSklSainO73BRUFjYfcHE7k7gROKtpVRTvJcATV0aodCcHcBIWiDJbebO9KVKI2qSCpJSemy31CwsMqKpUsuMKCf+G\\/yFIJUNoeT7SQQEnYvAKySkuIaWpJgs1E72uQARaLgyP054blghUEQSQZ6Ktbnad\\/WYBGGsoSIG0J9wGZjESk+0GZIBEA8Jxx0AiFEGBiEwMk5gkZIBHHGQPMS+3KmCz94hAbDjmyoYSP\\/AC9TiTIIAbqAd6IBTuSoCTHTMpIjEkA+Z3SU5GcHAB2xHnjpI6jQopMSIgjZSSBpO\\/u\\/tgsiN7HaDM+vTeR+++CwBMKEFPzziASfbBGTJH\\/xuvEJIEwCBMRBURmI\\/wA3AwqAZGOsgHGSAAMFWTkQSYkk8FJP\\/eesHmRz54EkHB\\/MqH78D8krGhNuXP026fngMAxO0mCT\\/qBxGBJAImIzPy4UbgSoNqKSFmU+NpEJg84VAjySB\\/ZCUkEH9BmQJIznAGZ4\\/vxAiCNJgzHjMBQUFJIkyk5BUQTn+w8jbWUKSoe\\/zHPrbBbraXW1IIuRb1tH6\\/W8iTtAyBmARxB4P9PkGQJPAhXHQwAOSDxEQVGDkgQORznB4J56T0zvqtBWEqGFgAGDGBEkJnwT4KvwSpH7cyTBJEyCTMjjkjIAjxILokhQChsQDiOrQptRSoQUmD6wD+uMiMHAxzHOJzj9jg+ORnrODmEgciccQfg8yEmCYgfv0Ef3JkQn3SCQfg7ZwMAwYMD5F+Yg8\\/uJB\\/SDiSrz7Y56zrb+tuWA4CPmBkAFIBkxJOD8J8yMRg9C8+CMxxIiZkwPkCAZIj9uvGJJICRg4PIzESQCcCcjGCMGMD95+YHEYMTwY9wGTPBPHWgBqtvHqIgD659bEYz6+vlgQiIwf05HOfAHwY8hOVAYIHWRKoMAAcjIJHM8HImMEAD+3WADG0GQY5k8xMiBBMjnGDAnrwmTIOeTBg8yMRgjg5BGJ8jfPbynny\\/r8MZgPnGfBMcZgiQB5yJHE5+MjwPwIxEz+ADyQQDI5iBAPWYI+B+xEATjgzyRCcyeTyT4gnHBHIlQkiQJ8g5JEYI88RvGfXpjIInIAAPmRxzIIJEgngxgz4n0zHkQnORMAySQIgDkADxnz1gDIwZABmTMAETngETEgnMHjoYEzgRPgYEwcAkSSCCce4\\/g9b9315eeMxiMGDOQJJnn+ngAH9I8R\\/eOvCMQBAGYGYETuGR5gH9pMjrMGf1AzGYB\\/JwSFfiBIHgY6zEZkkgcA\\/tOIEDGficYkdaxmPAcyBkgDGfIiMZkHIgf6g9eCcgEZEBQ+QDBxtjJ8yY\\/fIEJMxg\\/qHMz4BBBJEqIIEJJH5zkcDO0SCI+JEGTHMklIA5xkT1mMwEf2zJG0AniYGBGAqZ5+DGRDMA8jjnOf\\/afBAkCBJx16PHIImcRggEgkgkYB3HKYJ+R14ceCDkjJgxPAJABhJMEkHMHnrcSPz2PMD19AehI3xmMD2mFEz7RAEnBjyB7SIgnJEYMYGJ84POAZkAfgiDnP7HOOvZEyAIOIJ4IBG0qMKnb4gyMgAdZ4\\/7QRzkwMmfdjg7c5kcjAHnt5CZI5H15zJja05\\/T65\\/XPnjwHzGIB90iZySdo5PtIHBj+4xBziBxMZzyPacEAiCBtAkzjoGTIknaqSfcAIMACDnJ85JkSSDOQSYwZEkGFREDj3Tkkx5gmeOtJTIBMzIkGNuXPp57bDGrk9B136Wj5TfY9cCwDwIOeOU8EY\\/MHduHg8EdBwCZg5JgJOIiRxECYKsj+oDB68ZVkCTniBM\\/OQY5gqjgY6yR+yQSMEfndkwQSDMkTx+ehBAN+kC0EE9bzPS1gZGMM2j38+Y8uk9OpjGRjJInGABMAkn8cQJ44gcTnn4wMEczE\\/EHE4A+CB46wkggpjgg8Dafg+4AkczknjrIiMk52hJEYMJiJAgAjxOP2JOJSBMg9PdboTPn8I66m\\/rfr0FrDzPpfnjw+RBkjE4AnaZhIO3AyPx0IY5gxGY9xMgkcR5SDAgDPjr2ZykgAwASZmTwJgzM4MHAA+fDzkkwCc8jnIUREnBGcxEdDgm+kxsTbnvz9JH54AqQN\\/gSYiPmCPidsZyP1GDgCIkZjmBA8SSZTBjz174kjAyIyciCISCB7vkZ\\/wCXAyAcgyADwIAAmciVAmP7xHzPWBIkDP5JiZgkwD53CFEkGMkwJyOUXsLc+kQdr2vbAQY9xn1uLfLHuMAnjPCjgKmYgDxjk4IAjrE7h\\/YEQIJEjJAEEmYIH\\/zpziCEn9QB24PAEkkmJERIMgx+evRMjgzP+UHyJGTOTwI8QeRoAyo+hFthAHzMYFqnmR57wLTPWSD8uROPCPJiDgnkhPIIKecxAIiACZIHQk85yORxwIBBxA8SSTAIMGB0ECc8CBHg443EnIBycSCQY4PQ8nMgHPzKpECVTJzIITExIMnrRA6DptPSLf1tjcztuYPOBEco8uU+8bYxAmD5gcjiQTA+R7vAhUfGJ4BzMAwM45Bgcx4x\\/TmOhYB+MjEqMwYkElM7s+DiN08HIHgwQQOAqB7gQIBISRiSJwZyMgWNz4SRO4+Wm0\\/rjAA8x4AHBOJziJIIBgxn5jrG0ckAGJOIJAyRwIMHwTxHgnrIB85JiIn+rwN2CIAj8nieRATzjJ53AE4M44P4PEnz0IXi0+nqOd\\/eDAHvxq94jcRBtPhAjraxm2AlIPxj54VmfCcEGAMjzniPAA8gDGCZAM54E8eMxEAkHoRGfmc4JkkSRJMf6zzEk89e2k8JPETByYn5+ZkmZxyCehAX\\/CflYiOpkSfy88BAJI2t18oEbfLz6k4xA5KQAJ8ROAN0ZIz8kgGE5MdZgcAfMDBiSDBgHPgARIyPxkCRyR58yf8A3eCSeczAnznIBxmRAnJMeZUIkEGDnjx8EQFjaDYddttj+s+e2NzcRfYWMgkQLgxaBY9edrBITAwkck4IwRgGZjJgZHBzHQIzwMyJIUIkf3AAOfiYg\\/BpAE4GAYBMjBx7eAfOTE4Eg9KlroTQ0zaaWpRcUPVaq6tVXtuUdVTuFgUTFNb00DTlC5SBD5qn13GtFcX2VNsUQpl\\/cCCQZBIFpjmRYED89xtE4HqA023jpAHUzFvd7gJhCABtI2+MQZ\\/PMcfJAwQYwOjEgEjAAkAmCP7fiTPOdxiRx15MBRBSf\\/iiZIEnH+XGNxBAmc8dO15estRcqhzT9BcrbadrCKWju9xYutxSpthpD7tRXU1Dbada6mpS6+EMUjaKdDqWEBfp71CCElta9aAUrQnujq1r1gytMJKNKNIKtSgqVJgKkxoqkpEFQIPoIKYF\\/W3KQbg7tgGOPyDMSRMwAJziODA\\/09zmAY\\/1IEgGAnA5EfgZHXuYxHBzmQDiIEnJ5MH4mSevY5zzExBJ5yUkAYKuQMzOQJLUBzH5XkiYv0G4688AII93kLGBz9f6EyceKRwYgcKIOcg\\/ImR5xxPjrAQVFIGVGOAUkj3YkkjGcxgQfjoYHEHChgZ4JkggKgnIA5MwQImRgkHhQGVTJ3AD9Mef1BKhIAmM5HQUJSVpC9QSSNZSBqCDpJICjc+UibbbjAogEDcAkA7E9D0B6xAuTywsrrLdLSKVdzttbQIrqdusoTW0VTSIraN0fy6ujVUNNirpXchuoY9Vhwj2rJTHSCAfjAn5niSIAyCPkcHHHV695PqM7ud\\/aXt9R909UI1I12t0jT6F0UlNmstoXa9NUz5eaon1Wigof4g+le1JuFcH6xSW0JW8r3FVF5wQYAEg8A+ZgZP4kzzOeXOvZy5FSpOWvvVFMAnSuobS09qIlQUlMxpVIkCJmAAJw15O7nL1AwvP6XL6LNNTofp8srKivokIS+tNOpqpqqSgdcLtOG3HUqpkd06pbaS4hIdVgY2kwriOZmCSMYwJwPEeQB14Az4gxwSDHAJnjmMRmM9eieDHPBI4\\/BGD\\/YwABBHQz71CEpRKQkJQPaSkJExP6lHLhnKiojB6SFIi3kJ945\\/sLX9MOouRY8ojygcwbAi\\/5nGBj4GYAggfkTmPx+\\/Pnrw5HkwZ5wTA4iRKvBPtODgDrJHEJKf7ASQomABKsTjwTHI49JEkCYlO4zAkGZ+SSec+0wSeetFIEDmYvy5A\\/qeW\\/wANEzEAi+\\/U2O\\/Mzf8ALyykARkQSIUQeREiAAIGP\\/iZPAw70q0qSAeQBMEcp8Ef3CQSTIgckHpqCQJ4P9JSZON0meBmQAcgGBMCSe0otHdtAHmQeJBIVtiBgyCMKJMGR0bTqUy5rvBjULbSPyiTz257aAm1vnzPl08+R8rPoHyEniBB\\/IPAmBAkjjEg46EEiABBBIwBJB+MYB48QSUkEdEtKDiQQCMRJBE+3I8ASZk8YggGAVSUpIxAgThRVtyAD4IJiDg\\/9DL8hQWkKGygD9bj88BgC8Df5mB9dffgO0YEAEAAYMqJPgSY+CIP48SrtlsqLvcqC10ZpU1VyraWip111ZS26jS\\/VPJYa+6uFc9T0VFTb1JL1VVPtMMo3uOuIQlRBaU4gjiB4yMyMHIIIyfkD56f7xbrBS2zT1TbL5\\/F7ldLbWPajti7XU0idPVbV0qKalt6a59xbd1VV29li4mppm2mqX7hNKStxtS+jmEpDqVuJK2kKSp1tK0oW4iUgpSsg6VqixAtJkHbBLq\\/CG21FDjoUhpYaW6htYQVBToTEISBPiWgKUAjUCqMEal0teNF3y4advzFHTXW2qpxVNUdxt12pQKump66nU3cLVUVdvqAukqWHiWH1hAdDawlxKkBlAT42xwZ8SSQP0j4iBmcz4IoEkAYJUYSCACtairJBJSVFRyTyfEDrwAwBMggciMCQRHMQeTkGCJz0OqUw5UPKpmls06nFFhpx3vVttTKEKdCUa1JFioJSDvpTtgbKXENNJeW248ltAdcabLTbjgSAtaGluPKbSpQJCFOuFIITrVEnIgZgkD5mCCYnAxwZBzGM4jMDECZAIJ8iBPA5znx5nHWIzg\\/6EgZJgK+TBmQcxORzlJ\\/JKvIM\\/ABwoiBJBMAnkZPSfSBaInnzHMb+733wbtt+\\/Ty+hb1zjERII\\/E\\/wDugcyPxBI8x14ISYwMAySFTmJkRiQQBnBnHjrIGR\\/cjkGQZAI5MKJEyJI5AgnMzEiQPGAZjHOSfjkDg5joJQN7+6P6D+uMBj1tf0+hvgVLQVNe+1SUNK\\/WVdQ4hmnpaRlypqn3Vq2oapqZhtbz7qyCG2mkLWsmEiQOka2VNuLbcQULaWptxLiFIcbWhSkrbcbUErStBSQtCglSFDaqCI6lGmdT3\\/Rd\\/tWqdLXetsWorHVCstF4t6\\/SrKCsQhxCX2VrS4ndtcUhba23W3m3FNvNuIWpKkN2uVxv12uV6u1S5XXa8V9Xc7nWuhsO1tfXVC6mrqnS2lttLj9Q4t1wJbQjcqUpmD0MNtqbsV99rhQKQG+70pSkgySpeqQRAERBOCit\\/wBoIKGvZe5SQ53i++78rhae6LegNBGkhwOlWokFuPEWP04xCYJEEgpnn\\/STEiAfaYIjrwb8\\/AHEGePwBGIOQciAcdLdoAIAlWJO7AOVZxkg4JggCPzIFoUciAZ\\/EKMbuASCYJgcfExJJLIHI+cXAnbmPyA\\/Qcj0vF7T6dcJNkEyAJBKcGT7REScSOfg4AMHovYVEeRwoweQR8AyRE+MkCRB6W7CSJACcxJMjGBJMgmZMSZgAEHPi2QRA\\/pkzE4iCc8g+05BHicklLZ1JMWPI2PSQJ+HrbG0nYjrO\\/yt7+u5wQhACQYH6TAiMSM\\/pM4IBzyBHOWOvAL5G0JAQBmAJPJiD4JEE8cRHUm24PzkeAJAmACSYJmR8iCfBi1YQqodMY3FJAJyQQJkxgnGAef1dJqpACUg85ME9I22639OWDwoHafW9wYt7ufr0whMSdwEzgAZ5I\\/IggAQDJ4Bx0HaBhQAUMcEEnHj3SJkk4B\\/0IMHKjnGUiJSpQOQuf6Qk8pyTg4nrwTuMDKt2eQc4Pn4iZyZOACZbNJnSRuY6zt++2MCgLnl+Yv0uIva8dMFCMQAYE4IBBg+QOT4A+BBJmMgCJEYzPgweRifORIJAwQMdWJq3tbrbROmtC6v1NZXLZYe5Vqrb5ouudfo3DfLVbq5duqq1qmZqHamnaRWNrZCqxphTpO9oLbKT1XfPEkkASAZEAjjJMcEyPgT1t5hbKiHUKQrSlcKBB0rAKTEjdJtcTPO2C2n2X094y4hxGpSNSCCnW2ooWiRbUhaVJUNwoEESDjIjB4MCMcyf2wcwRJj5468IOQAAJyREwPMcHz8CRI+PSFTBEEZSP3HOfCRMzI5\\/AwJ8QVDmDBAIM+AIPPBmI8ZJ0XHofjy5z5+kDqcGSPzPw+vfyxklKdsQcCcef6skgcfEeD1ZfbvTOn9Tu6mGoddaZ0HT2XTNxvlFVamFzcGoLnSKZRSaZstNaqC4Ov3m5+ofRNQilo2Gm3n36tlKPfWitwMx7Rwdoz8qgKgnGTmZ8DHXgZO0gwRklMyZknieYJJJgYjpZQ1CKWqQ87TN1baJCqd0rQ24CmPEptaXIEg+FYJAiRthNVtrqKZ1hqpeo3XEgJqWUtqdZIUlQUhL7bjRNoIWhaSkkRzwJ9W9SoCRJUBAIT4UT+nIJIJ4AGQYx0TAzgbpyc\\/PEATHAkftPEmECSJwZMZkmABgTmCcYwfMdZgkzBB\\/dUftmByk\\/j5gCSmXC1lWkAEqsNgCQUi9\\/CJAuZ57nBoJsJv4dWrmRpg2FiqDa4GApSDEwRxnGTB934gwIJ+Z46Ft\\/8AaRz+fgmIA5iRnkHER0MDMDIOCBMA4\\/8AiSBiIwmcT59tUfEHyPcZMAeeSSTAgA5IPAOAAA29Yv09In0Am2BAW6x67+Ejeeg6enQBSImAYEGUkEHMnH7+AAZ5kYVWygFwrqKh+5o6I1tXT0gq7g+KWhpi+62z69bUFJTT0jBX6j7xCw20layCEkdEAHmIgwJB5A5wokCZmZIMZ56yQCJAMEZV5ODj\\/wCOFQQCQRxHQk6UrQpSdSQpOpEgSkESkGJEgb38hMYxROkgGCRAO943jrO1okXjD5qzTq9Jakvem3blZLw7ZLg\\/bl3XTtxZu1iuDlOU7qm1XOnT6NbSOBX8qoa\\/lr921RCZ6jZEiRtEgSDkwZyBAgDE+cfk9HbIBz7cYgjHHGRE4xG4SR46BBGDHIAjcngKHMGRIknBOP3OnlJUtakN6UkmGwSdIMEJkyogRAJUbc5mCkpUkISo6lAJBURAUoASYBMSbwCYnBZAAExHtM5zx+PM5E7h4MAdeAAiMcGfGCf1Dx4HPGfx0MJBJKZOZIn5IEzAnMgRzBxBzkpOIyQCRzmBifJyY+CSRz0WoSJiIABEfWwPTlg8bW25fV8BKQeQCB5iD4MEcTBzmAMkzzgNhUCBuO0e2AfnOfIGCMiJHAPQwDznEAmT8\\/3wriSMcZBHQhiYnOcq+AR4xzmIGDAjyXAJEzAgmImJBO9twI2m3XAwoj+vu8vL6i4F0ymglRQQCAoZOQSE7h7TAJ3QJI3SJJT0XBAB2ggCT4Ex\\/wC0QMySPiZ5i5u5XfLWXdTTXbDSmpKHR1Lbe0mlhpHTdTpnSNk03dLnbvuPW+61bcrbTs1OpbskIbZTca8l0oG5aVOuvOLqWipmap9DT9YxQMqDri6qqS8402hlpboQlqmQ46667s9NhsJSlTqkAuIb3EL6+nom6gNZfULq2ilvS880KdalrSCtGjWsJ0E6QoqE72M4R0FRXu03eZlSsUNSHKhKmKeqVWthlDy0U7wfLFMVKfYSh9TfdSypZaKl6C4pKlQJEZAkAgiAryMD8GRzBkZEdDA4mCQQSQJJzEcQPAJ5BjHRUCAZKRBIEEHgqhXuUNycgkKUkQNqoIHQ0mTM5xIyMA4hKiJmRmc8EcjpsKIn326GfX1v7xfC1K+vlO\\/QDaLHnf0wKYiZgYP4I5Ewc5A\\/1468PgwCYn9knMDaJBBgHA4B+evYBznEQmQefIBPnyYAIBPBnITMeBtlIMgAyI53ecrKSJ\\/bJ2ALAiLATy3Fukz1F8CmxI8jz5RI9Lbi18ZGSQY9pgYO6TzjzMjIxAycdYHiQPAmDMmMEQRCuBBEwMgGespEDIHIBJ+eFAyYiSZSMnJJ8dZCTCgYJ+Z3AjBmARHHHAJHkR1oJgxBIIvaP5o9fMiZi5FrbSZkTtyJ2sAd4va9seE\\/uMgEjJMCIAED5OSCY8gdCAAAAIA\\/YzOCOExnwByfMdYyPzn4kwSSVfqBH\\/SPHJ6wJzHJyTM\\/EEkGSQZgZAT+Od6YkRM87SL7fCTOBYHMnI\\/bEEggg4icwZBMgR+OsiYH6SRGQM+PEDkECZxySeOvE5zEf5gTJ5gkkg8fPifOCJIk5OSMGZBkj5IkDjbkEEfPW0pjleLnl6RP5jGDaduYF5\\/oeuBJ8AYiBwQJzgkJznMiBgCQYPRyOMgAwJxE\\/GCJH6gmJwYxiOigJEYBB8YIgkbZHkECRB4IJkGVCADkcJIMxtBkAA4+SMSrnIEETgBCjbe+07xzmfOIHPeMZ9ftgQnnED8fgSCYIxgEgAFJnx0AggSBIIkk4iBk4TzlM8iDOT0oCPOCTB4JIhM5mDAgSBmDkZMiUjak4xBPyRIjaADMiYgmAP3I6NDZIVb8IuDzsD5zafhjQVB25gyNpMRePP4fAIwM\\/OQI\\/EzOQIA45gGOTjo1oArHCSCoDGTERiMGcScHj4ALEROD7vd5JzmM\\/JTJ5SIwZ6OakKBA84jgnaPz7jMEERiIMQOtJBlPKTabTt19ff1nCgnnyjf4RtPWfTFiaTp81Kzz6DgREEQpaESRtyQdqEQEjJMwkzKUUTlfWFhsS447tCQFYQ1t3rGCfYlvcRByDHuJBQ6Zpg16wBK1rt9KpI\\/VC01DBVuJjG5YQIPkIyqVdWNpq0K9eqrnSnY0Cw2SoGFQl11ZmEhXuyoH+taZ9pm1shy1x6jpKcggKecU4ZiG0q71ZtsAkRYkzykECvs1zEU9bWVIMkMttsXtrcIQiAR4jr\\/ETEpHmcLbHRMMv1FSuG27RSOIQpG0Bl6sTULJ3KjKaQPryUlCtq0pP6Tvz2C0y9Z9DW+pcZ9Gr1NUO3uoQEqSdlYW26RtQiSXKRmmdJV7v5itwklI1E0dpNep6qyWFlG1erdQpVWLSnc43aadJNW6FAqKPQoaVQJygF0hQIXnfjupqen7S9rdSappSzTVVqtSLTpdmEBIv9xAt9iQy2raHU0lSs3BxlIJ+zt7ytpCSkdA9n9OxQ0mbcR1a+7osppKlaXCpSdDjyG6qoKFgjQpqlYZQgpMFVXpJ5jj77SWdVGbf5R7L8q+9zfivOKNxynHiWppqpTl+XIctZp7NKmqqHFwdDeXlRGkRjRjXFOr6gvqepdMUil1OmtM1Den3KiSplFm0k85Xamq0QSgpuV7craSlekJUH6MCArPT9DCEIQ2hG1ttKUttoVCWmW0pQ20gcJQ02EICQBASEpGM6P\\/AESaBXR2C\\/8AcauaWqs1FVKstpefBLptlrfK7rVhShO24XgpaWsn3qtCle4KjrfVpj5SFTMnaZzgQTIEEzEGAR8AdTzsryt9rJ67iqvC\\/wCLcZ17mc1JWfvG6FRKMsptQJJbRTlTqBqI0vpkGBHP\\/wBoDPKM59k3Z\\/k7oVkPZplNPw7TaRpQ\\/maGmFZvWrSAEB519tDL0JBDzDp\\/mwlQwMAgyfIkwQf0n25IggAkQDk46UJYwRtVOPakGTwOdsZMg\\/B8KiQsSzIEAD+mDgkEEgFQ87j+mARE446WNsDwPyCRB\\/piRJJ4mZIgGeRNmyo8z0uT7hcz09Bjn5xxsJAsIEXHX8vL4W2w2ehIiFHJHn85BxIyPjEzMGaL1e2nTPfXsxqkS0xqSm1Z20uLwO3ea+lp75YUOKgBQVdKR1LKFEhLilFIAlQ2QRS4BAkmMnBmJyZkmMBJgcYwOqH+pO3vU\\/bMaso0k1vbrVWlNfUy20wtKLDeWWq2FAFQQbfX1K3kj2qCAVqwD0w8VBxrJKqtbJK8qco85SJJKk5TWU+YVCABcl2lpqhk6QCUuKTPitJez+pbXxXQZWopSzxGzmPC7xMaU\\/5jy+pyamWSbfdV1ZSPSYCS1qIAnGxiaefBJJBPIMEZgEEmZ4z\\/AKZ6MFMRIjORH52jJ9vJM8eYGOnelLNbTU9ZTqSumq2WaqlXG7cxUtpeYIWmU7VtLaIIIkmQMghYmmjG0xEAkEq44HkQQZmVZ+Z6f0uApCwrUCkKSQqQUwkgggmZF5k7bb4rt2WVqQ6jQttSm1oUIKVpIC0EdULBBtvyE4gl4K20JpWAv1H5LzgEpbptxS4lQICVLfMpSmZASswYyotNLtCABtMAncTAycfPB2k5IAnJkE6oV9w+6UtgAqKEEpgqbRKUHcCcOe9wggbStUEz023y5s6X0\\/dL5Ur2opaV5aSoxKwn+WhPySop2xICiBtKk5Z86zOnyzL62vq3g3T0tO7UuuKUUhLbKFLUZHTSbDba+wsLgbhnMuMOIOHOEcnpF1WbcQZxQZXR07aNblRWZjUtU7KYTJCUqcQFQDCdSrAEnVr6gtUi4Xmm05SPBTVtQX61KXCUmqeSkNoUcZQ0kzkfrIjac65JbESEzkTI4\\/YfBIB8Enz4LpdK+ou9wrLnVkrqK6pdqXDkKBdUpSUAgmAlMITIjBwOekJEqAPGOBEyTBwYMjwfj\\/Xy44z4lqOKuIMyzl9a1CrqFezpWoqLdK2dDDcmbJbSPDsCTj7M+wnspyrsW7KOC+zrKm2kDIMop28xeZQG\\/bc3fSKjNa1ekArXUVq3SFLlSWktomEDHkyRxyPMnOT8SDBiBH4Pznk5EfvPAJAnAgYA\\/wBCCRM5AH7k7RiZgxiJMDAHkmefnCpmZAgHcCCkmMyDJ4JAOInzPUOUDIjn577efWOU8\\/S2zINjHS55aY5nmY5W3xiZMESQBieQBMccwTieInPICTwCRmCTPu5ImAIyQMcf5pz0MgjngkZBGZgkyececAfvyWRPExEHIxAODGYkjyfnHHQYMTb4\\/n68uvIY1ClRe0byT\\/xnynytt5A4CSQSSSADEwYIyRAAJ5EZglIP56LVOAfGAYPI8EZmQYx5iFGcnFPE5x\\/qDnaJJHBM8iD5E9A2wTOT5kgEDBMiYAmIAIIiJE5yelvQ43oPIkGI3Mcp\\/LmD7sEbj5gEGOPGMmBAEgpOfIOSOvEnweCM8jwc+0c5AMgeehkGf7Y8TEYkHOSYPEHP5LWDGROZH5IiAZIKslWQQB8Y61gOkgjz5ibAR6bcvleMFqMjMHODBzzGY8zA8wASc5CCr8cmOeUgCIjgnyIAJMmQejCnnJjifKoJzO4iCSCBngqIz0DaJxmYHBJnzx+5wcSZ4MdZglYO82gcz5W\\/f4EYxu4mYjmPnIn2zMSCBwMzg9FqPycyQDBJwACCIiCY\\/T+M5nrJG0HJVjjJjnmSADIAxjAjEzjb5HE8TmBBMwSnEDjz8zHWH3\\/X19SRgkzy35T6ifywnVJicKwJg5iZ\\/p+MRIjAk4PWAd3gYHGJ8QSIxzCv+pg9DUnODBIgiMfkGOBBxMiefySRBAJMREbhIP8AzAwPBgZEbRxzqPoSOnnb3f3JM7EmRaJ9PKI9\\/TpgKoH4BIB\\/GOOATHI24jJJyOiiTiORMmDEjIE5yQYiR4kiSOjzBJjiRI\\/Efqwr8TPgTIg9EbYPPMZGI4kTIAGIBBPPwD1ob85i\\/Tl5m\\/zt7yUuR6G1\\/cbX8j9RgpRM5EAnGDInwfHAHn+\\/RWYAIByMwc+fI4PnyCJMjHSkgmQQDzIkmYHI+cEA4jH56IIifJj4J5k\\/p4\\/aSfiT5FhI4CTvHoT+Uj47e+YAr5nyIk8jg+AIJMR5Gfz0Uokgz85kZgAjGPPOeCAeOjSZwQYmfzEmJJOR4mQBPnHQFTIJgAGCDInAmTJnx8AifM9am\\/n6\\/puD7ul8InErEx5EXPKIgfE+6+ChAJE4TKSM87gf8uSZAI4PSdRJMkQJAP5AEcAZAPIk4jMiejzABMEDac5jg\\/ng8xE4EfkgxzniMmBHPkwBgECZAJMdCuTMybXn3D69+ErmqwvOkcz0tz8hzGMT+oxEbRjniIJgjBgmACMEnjpDV1LdO0ta1QhCdxVJEYJg4GSMAxHu\\/IAPWsJBJzGfPEgTH+Uj5I3HAgkdV\\/f7l9yv7dpQU02oeoZwtYI9qZ8D9O0E4G6eT0rpmStQmYsVHYCIt6i5+ZthkznNG8polumFVDgKGG5krcMGSL+FMSo7chE4Z7nWuXF5SyT6YKw0kgnakqCiTAAKlRBIzgAAQOkjbYQASmDKfdnPOAIzPEZI2mTz1kACJBCcDmJwTOAeMwMjMz8jSVGYBOTk7iMD4J\\/IPugGRyI6cHFWCE2Ate+959ev64qdsOvPOVVQsuPOqKyo23iwHQRblYRzwcmCZPAMAxEz44PM+DG38dE1DgbTAMKUCJTmTkFQwEwkY88\\/AEnFSWkFSlAe0iBO4EiAnbMiSMEeMHBEtbqi6skkjdJjPJ44OAMkzOcyYwShNwTfYdRHh5GZ9IIO20SKqf7lAQD41QdPQSDfmJ\\/WTbfVXEfHgfpJkCYmAQZnJjbJInPRkGIJg8wSc+4RJJEkSPwQZORJwkASCACYIkQDIMY3CB+5MngzjoY\\/aBIxwTJGT4MxI\\/uSflckTB+F7QDzmec2HSbTjiJCYknnt6WP17\\/dgYJI+RnMSBk4AgRiAVAxGcSIScJPkSAIIBIJynHJ\\/UCFcCAJnITnMeBESYEZMggEe3ggDJyJkaR+5OfkyfIwBPiDiIIHHRkRtb6G9ugjB4QbTsDe48oHznef1wEkCOecQZkCQIyQRESIAOJ89CGTgEATkiOY8H\\/NGf2MdCHj5kGBxglUAYGTmSccEkAE5A\\/Vgc+QAeB+qIgwSBB4VMRzoi9\\/Xl68utrbWvywYOg5W+QPL1v54wkxjJMmIBMyDMkkCAMEDkRHwcgEj2iY9wAJEySQMggmFQP3J4wBwOJmcgfqhR8xmITGEyIwJnrwE85VwDBMZmDnEKxiZOPPWC1un1fGwLj4ActxuPTpgMK8SAAZAnO2ADMDn4Ag\\/PWRPKRPu5wI\\/PncOcnzAOY6HAxgcRP6eIAMSSTzBkSYBJiehAQcAA4kECRJynJkzzzHPE9YQZ3t\\/X8usR7+WyCPKeXwP6zB\\/uECAImTBIn3TkGQPaTHicn4HWYiTAgkeFcSnIBnxHuyAQYPzlIUTwdw5kckEnCcR\\/UCoSBMeevJAzx+EzMAJmASMyRxIMA\\/EHJ6wPePr+m8bY2JuRyievL15idtt8H0g\\/m7huOxC1SQYBCTxwIJMZ9pnAHnwTJlUwBPJAMmc4Pz4OTPjk2mTDdQsCU7dmE\\/5iSSMwCPH7DyOsgcTkEjIJzAGeSDABkYzn4BEdk3BmT+X7fnvg0CAN\\/jPlaw6X88B2\\/PH9XzwBu85jMDnB5PQnfaAZJMjEmRE8yZAOcnniOOsoSSQnMyQU+7ABME\\/tBHyR++MPzvAkAApJA\\/zcYmQTgQJkZ8zOuuMIsYF9\\/Xnf1HxwnQkkqUrJMCPaYGSRkEQD7RBwATEc+iSSfdMkGQCJIAMwCRBEZxGAMdHhO1ABTBUIIjMkyIBkCeZ8FQ5mR7bIzAkpPjkRn85OCBzPOes5frzxgAEWANp\\/aRf54YrpTFARWtJC1Mp21CB7lPUu4kqAnLtKr+YgwpRSVoSCcdQyupw27IKS25C2XQTsz7yhPJCkn3pMwDgYSQLPICpBBIicwQoAHxwQf6hMQTMyB1Ea2jSwtVIf8Ay74WukJyGxMuU+4D9TCvciACWVpAmFHpM82J3CQpQgnZLh59Rr5xz6ThLUMTCx4UmATuQo6YI6A\\/KT5nDVRPrfDqapwuIXLT\\/wDStTaoKHdsf8RKzu3wSpW3IKyOm2pZXTuOtrgrQqMBJCkwIWmPCwQUzGBInPS8trStMEeomUhW4D1EkHakyMhSYQZO1K0tLBncOj3mk1dMHUQpbAIUI9ymgTKSQCrewsARwBuIEdA0lTelX+6zcE\\/zIkAgb33ME25xaEim4TcXEavO97+gEwPfzDFBMSAkHIO2JAJjAOSZxH55JA6CRIIOPjEA+39zJ8gROc5yTT5KfcTgCYkHxHJk\\/MK\\/PM+ShKwv\\/wCaJmEyIUIzgEqJEiTM7AojjpIEk2AuRIn5WthOEE+Vv2\\/fBMBQIA9wP7EQSQRB4VImQZPgSD0EREeMTG08CTH5xJ5mPg9HJI4PBSUmACfxG4SSFAg4BkCT8hWgoJnHJ5BkQMjO3MgzM\\/IIPWyLA+VwYkRA6n+mDQmIncR+QvcdBhVSvem6AogpWdqiof8ANCTkRmeAJIGMjL0meCQJOQQPJ5Ee6fzGIgRBmN8QqZkAqGVGZPOIIJjgiJMckF9pHPVbBUfegAK\\/bG0jzGTO4+SJOJV0rgu2qbiU9ZkW3v6fDDPmNObPDySuL9ADy9w8upwqEDzk5mN0mZk+0iBOCI5456ycDO3HgAxGD+xIAyU5H+vQgCZwTyOQRwBxun98bY8EdCiZyJkSBiSDPgyRnJwYGQTwsIj+4\\/Sfq+GjBf8Ac8H5HI\\/JKRJODI\\/ecHBEkEiMHiZxBHEhRklOUicx5PQzyMZwCAlJHAnkGJkYOZjkEHrwgZEncMzHkFRByN3AMjInOedYzGAJxPxGeeI5Pu8wYBjPJjoUSCDiP+35nwZMgR+EzPXo5iCZBgHIA8\\/OSP0ng8Ex0IREGDkQRIngJBIyIMjI\\/uYnoWmBJ9fdb85O\\/Q8sZguCQTn5mOJjA3GcgbSJGRPPWfn4HAnz+Ek5OTM4+CIyKBPHmNo5OARgjMkkYyOOAZ9BgHAhOQdxAiD5EckTPwAcc7025X2ne5H1sN+pjGYwPGOCYBwTHJA3EzIwIg5PQxODBwI\\/q8Z5kkyYyIPySecRnBjyDgSJ5IEGIPz448EQiAYnGQM4EGeJA4+SJzOOsCTYwL36EbH3E+Qt+WYwrB+QcAZyJOYnAIkE4IieOsQSDAOf3J+TiSFE4HOcxIGRx+BlQPgR\\/mwMSBHKszxBHWNox+n55SARIIGJIkn8SR4z1sC\\/K0SReYjztERMdZjGY8B+OCBPAOQRECcn4wTAkHrJHOMjjkEnBGJnyRtPt+DxOUjwB8ifPgY9wMEZyOcGAOhAD95gxiTnkp4wCBIMDkYPQt\\/fB25WkT0N99\\/QYzAMHEwIOZgxByTknBHBKTwrMT4hRBnMH8GYggiCASY4\\/VJ+J6HwTiDzA+RMznEHByARkDjrEAkZBnGDiMDgiBgYJMmeY62AB9fUTjMY+CCTA8g8jnB9uSR5xA8zOQJ44OfzB2+CdvEgmSCZMRMZ2kmRiAOeDgR5KeSISBIKTBiOsgTIGORExuE4iAVCCD7ZjGQfO8a3g\\/kbEW8v26Y8ACcAcZkgAkBUCADM5zGDMSIkwAQOJ9qhgY4G07oOcHjiCcdeTBHxmRMe4cEcRA3SArgeOehAEgHmARtIMDMcAbSDIEieFfq4611mdxG\\/QfKfdPngslSjABAv5HYTN\\/P54xBMmeARkGMBQkgwQSQIyYyMeQR8xMfEGZAIA4CeYyImdxHRu2T+kJjyYkwQJEzzjlIMA8zHWQJ4GMAASEzEwNvJCjPxmMGCdpBCoFgeWwmBHSxHTz64yDFzuBAsR+IRe8i9vf7yiMSRn+mZ8+1ImDxJjORI8buhecZPB5EwRnJMAyJOQPIx0IgZwOCOYGIgxn3Ak\\/q+fGesyDkAiCY4E5j2gcGR8lMEEzJkRSUzvA57dP1+rY0Ttba\\/lNthAsY+JOAbRJwOYBkIgbSNpmQUgmCfkk5yOscSTAwT+45wFYxAEzmeImTRE\\/ExxiODON04JicYJyB14j48QeYBzg4jJMAHgiIPwNJvpMzvfeIHKfTrvfGvMbg8uW0et\\/XzwAAfjj8AYAEfpjmDjI+Y6wRMYkD+w+ccmZB2gQkYG3oUQRk+QYIBABmQBA5nH6pHMA9CAyfBBA5ScwMYGCSRMFQPjnIoHv68+nlFumNfLHon9pkSmcGZP6gfzHzGMZwADxMwCf1GYzJyCTknE+JJ9vQtv4BAn4GIJCiIPGZMA+DHHWY4wIIxhIkkCQIkZI\\/TjCvB6DBMjp0J3gEb7nYTYDe5xmARjxOAMQTAHOQAZIBM7j8Ec+I4\\/dMwBJg5Ks5TBkzwfEiSbGJEDM5zJBPIgwCT4MTIkY6wkHkAEEj5OYEyPBkEESZJxHQdBIsIvHrtPpG\\/W8RbAgIIJ2sevIH8jfAQI4AxBBgHGIOZxx5EyYGZORmD+CdsyTnEgnmYiOB0OAY\\/PHAM88SBPIkEzBgYnrwj9uTPg5OTBMK5I+YyJOc0knYxboOkyJPKffjRAEX+Ij9Tv9bjBcAf2yBjwQYzknmPkmOSD0LiD5gEmYJgngyfjz589CAjxg4B+cj\\/AKkiYwfk+evf6DiRJ\\/JzA5+BP4MkdG4yTETbpgIBGOSIxHHuOIIgRxjknzkdZgzxOeTnmD4+QDME9Djn+0zz8yDgeeTwOcGOvbYOOSIjwBiZzzxkf6Z6zGhy8jgBGMYj8wTAMjJ+ZEACeFHHWQknIBIjgQfA+SIPGSd0g5IPUv0jobVeuam40Wk7Fcb\\/AFNptdZfLkzbmPWNBaaIoRUV9QdwQ0w2t1tsLWfc64lpG51aEmLKQEkzjmMQJgEQRyJzyMf2gzunQ2h0trDTpUGnClQQ4URrCVEQopJ8UHw2mDgIcbUtbaXEKcbCC42lQK2wv8BWmdQ1wqJABgwTpIBeYMkxIgyMqIxgkzmMgmDBHHQYGCZEgGJJgfuccnnGByZHRsGMwoACRuwIM5+BHI5zJMknrASfA4xAnEk+AeDHPzGBHQAJwITNt8AIx8Cf0yJn4+eY5mRB5HXgBwYPBgElPgkGfO3wYx+oEk9GBPEJJBBnPPg5\\/APiTPBE58AcQM4xH5PJxEGBwdpnxnrIvf6+pn6jApIidheDfcenvE7fn7+3+mJjzgx58mTHmMY+TiDAGJAEHAmRxgEAnJ6FExmJiDkZkQZAjyfxKQAMz1gAkTBMeRIwB+c8q5BHOTnoShI1fvzA9wvPPc7zbGKM\\/Iz6gf1+OPARAIgiIPMwoZSFRGJz4H7p6Fn3AkAZJIgYgfAmQQIgZAyZg9CAMiIIjbEAJSZHkgxkiDOfzmbJ7VWntredVtUPdXVF40hpEW65POXqxWsXm4i5NU6nLfRppCdoaqXgUuOKIA2JQCCpKgOnYVUvNsIW22pxQSFvLS22nzWtZCUi3XyF8JqqpTR071UtDziGEFxTdMyuofUE3hphsFx1Z5IQComwE4rHJ4jCYONxMESJxBj55yfwBgfATmADAgg5znMkEmPiAQellwao011ai3Ovv29NU+mheq20svvUiXHE0rtQwFbGnXGghTqEqUhCztSSmFB0uTmnlWexs2+luTV9aVXHUNXUv0y7dWKcqT\\/D\\/wCHMoZ+5YU1Swh\\/1XENlY9qHSS\\/0YmnI78l1pJYQVBJWT30LbbKWlJSoLUNWsAlI0JUQqYBF3wIYIbdIfIElEFoFvvAXkq0qbFtJEFSVkJKQNREfgfB4nOJ4yI\\/JJ\\/IEZ6DzHx8RJMg+QmCTHA\\/JMyehRMwRJxuj3Z5HIkfAIznMmOsjwPOJkwkZ5ORHOADzgxyQAgmL\\/A9B5W3+pwdcR8eu+MDiRBPBgCAAQIzPmMTIlODkk1tkuKKUltMIW5uccQwNrSCogLcIBcUEkNsiXHVqShtClqAGUoJkDx7iUxAgTkACMxMyCTI+evbYIAAJj9MTMZMYSQMkTBMyCZJ6EgAQVoUtIUJBJCVWkpChNxbbzwWpUghKgFEWJEjlePfHK+AgJ8nbIn\\/ACp43eAZg5kgjwTIyYiZOBAgE5AGcEkgEnIJGQZ3GDyCAYO2SmBEqTtgkKnPuG7xGU7ScpHTrabXVXirTSUYSpxSVuFLjiGwltv9SgtxYSCgEEypKAAd6kp29abStxxCEJKlqIAQL6ieROwiDubwdsb1hIKlHSlNyo8tosJmZkbz7xg1hexCEx7TtAV8FQyQmTunEkzJKvMS5IiPOMcQZOMTJOBIjM\\/OeklIwwHKhl6pAcYLqWAwC8h55CgBLg3N+molS0qBggAJJVuSVgACiMTMAiDxB8x5wk+cnE4fKdCyi+nSFFIggxpgEQJg77nz5zgBUFGZI0xeDcGIIPkD7jbAomYBGDiMkY87ZkmJBzn8wcfkxmBHJkzwYAMY\\/V5H+opzOAAIV7YJggyB5ySSQY8GRPWYxHJOMZyQRKvdjIJA4mQBkypCYBmP05T06DztM3gbnSbjl75tIOwm2\\/SD6lzByPElIjM43REAiCc4xxPWQPBjlIH6cCP0g\\/8AWQTOIjwIEeRx7YOIkEwJP55GJAiBnrAjBkg4JlWRB+MxJO058EEwchOlInkRuDHNMHnF\\/fPTA99tvz2Pw5ftGMDIEYAEjIA5BJjJxmJwOCOhR54H6hKf1e4QYMyYiAP1ZyDzkcn\\/AKcgyCD4JkkDAj8T85g+BHg4PIAIIiYEmCCf7AHoWmDtO3l03uZG9v6EAmdjpiZ8haSbXJPz5bnARMeeM88gAmJ+AcyYBjkEnoQhJkRP\\/wASRjHzwMyD4wDmRiPHInxz4jOZIjg\\/34zkZAHiDEJJyJkZ5HIiIJjzzsIAvvyvB\\/T6vgJUT6c+nK3yPncjAo\\/pkeZHBiZ3AD3CSTyTmD46FCcRiASRIAMiPcRgqkRM8E89BHEiQPySDHBMZmVczHHQgR4IIKcgKnwSBgAY5JM+OQSSPSYt122O4nrzN\\/nE4wEzPQDmPIHfr5\\/pjIBUPdJMjmcE8yCrIHiT8+OhJHmMmPE5wclRBjwQJ\\/BGesRMZ88SBmUyTgiRnJx+CejkgkEGNsfAMyAIOR5A+OeSckyJgEAwIn3jbblBn1GDAAbgC\\/W3kZ3\\/AC69cACAJlIOACnKRycQmeMEkE8Hb0WRO3gxAAwYxEAZ5mSQSZjmej1CCAVAweBnMkkGZgmfHk7cZ6xtE4BxMCJnIJgicEkyCCZGSOi3EGLC0iOVxE3v75M3O98AP4ReTM+fKw+Mnz63OE6\\/0wrBA\\/yiQOBmRMRunzyMnMIdUStxQP6lLzkn9UwDAIJEY4mf7TGtX6dO6SMhBSDB5WNnGMZM5kjg8zD\\/AExMpx4n3DIwIAmOTASeM8TLLmCgHEpiSBy5ajYEWHS\\/9cGNGAZuNogeU+oI63km\\/QsZzBVjI9xyD4OOT\\/0IJmQOhzAJjzwAeRwCQfkmIIn2xxHQ9pGYwJAMCIgGP+aQRj9UQCInrG0kkzPgmDjAP5iDwc492cdN4i5F5\\/tb4fQjAyqT6iDMkCYmBy+flgVRU1NSGEvvvvt0rQZpm3X3HU0zJdceLFOlxSgw16zzrimmkpR6jzi9u9apmXbW16HvGtrBbe4+oqzSOiKutP8AtHqO32p291troWmH3vUo7YySqqqHnW26dsEhKFOhw7kNKSYQU\\/AEY3HyY\\/pkceB4JJxkz1kDgZlU+P1EAwMwfyQTAn\\/mHQ21lL7bziUv6FIJQ6SpKtMWVJmLRY7Wtcgh9ovU7zDbrlKp1p1tFQxoDzC3EkB5rWlTfeoUdaStC0lX4kqFisuLNCi41rVuqXamgRV1CKOpfQGnnqNDjgp3nWkz6a3WEJW4hBWEKMAkAq6ftX6F1LoWotNNqi3G11F9sFo1VbGTWWytXU2C+MCrtVxJttXWIp011MfXbp6osVrSYL9KzuSDFkpgkARn2mCASAI4VjGCJmeJ68QVZmTgkA7iPBIUSDggCJAwQCIA6EXKctu62Sh1a0qbWhUIbClSpJSbxOkBRMAAiJwEofStgJf1NIQUvpcb1PPK0pCFBxKm0tkEFS\\/ulhQVCQggQTHxEFP6cSTImAQOePyQR14JMHjz4gRJkACYJIIPGBmYgnKGJCkkkkbBJUEkAztOCnkbpIKpBzBIROIBEkCN0ZlXtiTJg\\/JOPMT0jUjmDYbEGfj8RPMx5YPuPL9xgEKPIxzxtBxBn5JHAicZ6EEjmMY5+P0yYJMnx+xBI6yBwf0jGI44jycyYHiJPQo4zx8GJgiTJyZ+AIUR8mOtjnadvXce\\/wAvOecY2Oc3\\/uPft9b4AB\\/zYMGMAAyDkCBziATI+ehRPIgiSBITjBGDODjzG4xHJORIwJMAHPu+ZMYODJ\\/zTGcdZiZ5yAeTgwBkgYMxAiPMwM4Pl58hYdPPePTaMGo9+8T15QN7cuY\\/IA8chMcEBMwAc4zGCCYPg8z0ICQIA4AI2wCZH6cwcn2\\/uYkZ6caS1VVVSVdxIDFtolNsPVr4KWTWPIU5TW2lEE1dyfQlbzdGyVOJpm3qx70aVBc6QBGfAVHiCDIEcHORGTCQUjB6FoMAgQCLSDeLGOUE\\/VsblN4UCR+IDdJtE+7ry8sAKcccQoYiQDkKg55zMCfzAINoJwIJgARnmYzEHBGBjEycE0jiIPyBu\\/JmJMzkEHkxk8gJzjP7xBMSOIIIJOeCQZPyQ6TeB5dCY8ucD4eeAFQBiLDYcpEQeRHMHl64CB+oEHnzj3Y\\/5iCf6gJ5yIM9eAlOPgCYxyBiDJk8kmcR5xkDngkncIMeAOABxBP+bEZ56GBkEfkyAcgft45JPlWfPWouTzMDboI6\\/RxiVTyjnF7WA3gCAJ38sFgAQOOT\\/bnI4gQZGTI89eOfjg5H9oGBj+5kYPxJmOAAZH45xkwZkTgyTBJ89egEGZED5\\/qnAOZPOB+JyDnWw8gPr69+BAi4Bk+\\/yG9+fO++CNpkEEE48SI58gycZgj\\/AL9ZgZGUgHkj\\/lBGDMZ4yeMYEdGBIiZjgwo+MHGZTyR5I\\/vB8QAPEwRiYwTPJETPJHj\\/AF3uL3sPPAsEbcmB8E4+IJieBE54JPMRI9sfgfgQciZJTI8k8HOfgk1UgpMfiYM4nGDkwUyT4BA+OgqAgcn5SJVExnMBQBwBEkHjM9AUiJ6iABb5x+fx2xmAx5T\\/AGyZkxkAgDBKQAeCTOOsRnAiJEjjkESBJzJnMKOOI6NjkcRn4JjziVHMBMyMEKBwesgft8RlWJAgiAEnAP4mQCMguI5+XXYja97Hn\\/YUmCAIECfeBf3x8zgMfuYgY3GciCcwDzJMEfsB1kHgwcxyCTBIIAJwDxBCiJzBHQgIGJOQTgZMwQQYBUDCcA8iDE9ZAEjglMiTHAIwAYAIJMkggngDBOGdvy2i35xMW6wMCRFzudojkSN7frtgAA4yJAJCh5gwVCRJjcIBMjjB68AT4nOcEgSSIBkQYMcQMgnwTdo9sgREQAAcgiB+okxxJ4JMgg9CgZkRJ48zOc8ngEkeP9esAve0x7vP4H9ueDMEhJJGP6ZGCRmBwRxEznk\\/HJiZjn8nxxmTg4MbZGRjkjI9gI\\/uJ\\/tmDBxEmDnJ+TPQgOIB4ESd2M8Ec8xn4JOAehpSbcuY63FxBt9fDdrfP6+tsYAAIBJzAJBz5HJ4GIyTKuSSmelTSOMZIBBMTwCBkxxkkZPk5AJIHgZJzwJzJJyZEJGAJHBnM9LUJEZgkSIhMwATAExMHkEQeQQDIwlRVIjYyPO0X5ixsR7uhazaOv6RgQTkSIMqgcYE5CQInORPIwRkdZUklEiI2nAwTAEgQPPHkg\\/E5MAweRtUDjmTgEkYMEAz8QcwJE8NrSj4jME5MYjmeSCADggjIIKrQNDkzz91uk+68RB2GxaTcWmSNtzJERv03w0gEBWMCYO0ZM8kwCAAPM5wfgmN8yMxJIhUQBnMTgztggARH58gAxzifiZInnOMAmRgxPEdGtpk+AVKgg+0GRkfkzxEA4OJwmCbJHuHnEefXC0kSQfLrsYB5ct+ZvbF9aHoPv3n3Er3patlEt1XhK1OJLbeOTuSpajjDQIA2ki1xRrtOk01TrbiFXFwqYeKCQ6\\/XvugAAEuJDbCAveU7NqkFCiN2yFdk6U19DfQlwlwUQZO5K9jRRR1\\/pHCgcF4qWlJhIJ9pJAOwWqLeirptE6cpqZT71Su2ino2kguVDrDNJR01OhIgb6ivraenJSU7ElxQcSEEjoDIqbTw63XpClOOMP92AJK1VFaikCbSSrcREjYXBmhc9zVLPE7dC6pKaZutYNQ4okIZZo6BVetS1WCWwZKiqEgJlRja7vpj0gK+7XbUrqFLp7BQUlhtpUkBCq65Ms1dzcACigLZo0UbKsAo+7eElKkkVJ9bWqa3VGu9CdjtNOevV01dbq24MNKP83VOplN0VlpHwmCDa7S\\/wDcEKI9JV5WTt2FQ6FdubFZu13aX+P3lxtq32Wx3fVt7q0AAViml1lZU1aCUoKxVtNMUttBCVPMfbQ2guJbTyf7C11y7ifVbZe4GqKRVYzdNY3Zl2ocj7an1TftNanu1kpGfUk\\/\\/B1Faap2hSgkUyqSiTgFk9WbxYg5fw5whwA24WazjLM6RebOs3Wzl7lZTu1zyikQUI7xhoKFi1SLBBSFY494FzZXGfa52t9tbrKqvJOzLJK7LuEmXP8AaqM0ayyqp6EMhSkoSr2divrXkD7xmozdhZ0kAnqrorSVv0XpewaUtaZotP2mjtbSyEpU+adsB6rcjaPUrKlT1Y8cFTlQ4VDyZo0z4geJBMmR4MiBIABgDO4DEdKE0xQopOQAD7k+6NpiY4UBGAcmSJ6XNszAAVA4JwfAnMGM8zMY\\/PXR7NO1TNNU7CA2yw02yy2m6W2mm0ttoSB\\/KlKUgdOWOPMxzF+uqqqvrHVP1VZUPVVS8sgrefqHVPPOLFpK3VrXadxfbCdtkYxmBnjgxJAn2nzuiIzEiFrTMZMmcbRmJIxxiFCJmQTAiIKltgwFHgAGIIgQmOROBgqyQPBA6cW6eeATEDIkTOSBwQNp\\/qnPkEEGxPyPLqP6emI+9UgT4vQD+h8zHPCJFPO3ETA8ifB8cz5I+YwemLWemG9VaL1bppxIUm+6ZvtoAUncn1LjbKthggbCVKbecbUMAgpBHuAV1N2qYnwCYyIGOJJwDHGBmcTHTkxTAQQASlSFQBJlMFII\\/wDiSSDJiRugz0mq2m6imfpnE626lh1hxJFlNvILbqTfZSFqE9L72wRS5s5l1dR17C9L9FUsVbC\\/+L1M82+2rlBS40kiALgdZxV30\\/XVzUfZftrc6hZVVjSlttleV\\/rFbZG1WSrChAIX9xbVqUQcyZUkyE2bdn2qMU9MHAKutTUFhIPvDFOlCal8e0goaU4yyJIUXKhKshCiKi7RPW3tloruxS39\\/wCzsHbTuFr6uD3Pp6cuS6PWVtTTpUEpW66xqJunpW0gb3VIaQOD1F+2\\/wBROhu6uo6GxrvNDQ6lujjibPZXVUzVTTIWlDjNnCg8tVRU+i0h9XpobdqapVUp2naIG6LZPm1GzlmRZfW1tOzmYyqlQ4y++0y867RpaoKkoQtaSsqq2nGkhGpSiDCbKIs7iLgjPs2zbjXinJsjzLMOE6HN6mrVXUNI89Tts5y2rOaBClNNqS2yxl9SirqVq0oaaCW1LCnm5vmjtZqCmEKVIEgEbYiM7v6TxzAAmfA1T+pnVCKVy26KonROxNyuyEkwlJSBSMLG4EbgkulJI9sBQyAd9bpS0ejdNXTUF2WhmktVDUXCqW5A2IpkBeyVAAFaoQnHuWsBIBx1xr1VqKu1bqO76iuCiam6Vrr4QSf5TBURT06QCRtZaDbYERvCjAKpNEfaN4w9gyJjhqmeLdXnbhXUBB8ScupljvEqiCA+5obABlSQ4CIBGPSz\\/CF7EVdonarnnbJm9MajhvssaTQ5M482SxVcYZq0oMFrWkpWvKcu9orHCDLTz9EsHxJOGUR8ySMkxIkkg+fkJ4HPJBjoITHgSPGCI5hIkDiY5mCM8HyTIngGBAGeTjBAJEmRiCAesggwTkxIiSR+IIEDAE+PnyeHFg2PSZ+WPpcbkgGPje223LkPXbrgQHkY5HicH9pExkQDwBHkJx4gY4gEmeecDMc4H5PQx+YAEgq4yMweQQZGRyRyTM4In88EpgiYABSYGQeJ\\/YETjpOpRB8vT9Z335e48hwek+frFp+E4LBIGBJMEiTjwZSSRiAIzPJx1hSYImJEpJ54ITMYE+ZkAEYnyZEYg4mMRndiJ84HJE4x8AUNo\\/BIIycZSIImTEeJH46BdXMAm29z+EQfLmN+e+MSmT6391gfXbBYgTkyrkyJOTgjjxEDM5EDrBOc4gkxPgQTugjIB4EfgHzlW7KiMbSFHiQQc8EDn8k\\/OeggYnJAO5IxgRwZiVQnkScxgwDoiJ8vr6mPjbAtJiBBHle5gflf52tAFA8xOJM8HH+vkZIgQR5A6DHJB8zGQYTkwAM+6DPwOOjSIwcHyTgTIJMZPJESeJ46BE\\/uAMDEweMTOYz8Zg9awUv8J+uYwViZInO2RI4\\/pjaRgKVgCIAycSFXmQDII3REyn5k8xwImOeR0YpIEzgRB8xB4Aj888fB+CiCBESfjmYMYkGRJHAkg4ic58sJlJJuPr5\\/kB8cBkAwBBAIIMiRJiAAZAI\\/q4+I6LIzg4J\\/1JzmMZmM52j\\/AFEoA4yIJEgAT43HkRkkER8nxJRiIOTGZAJ8\\/wCaDwQJB8gARka+uXlb693LAFIte45xy2PlYx5evPHlRBBHgwYMmP77oIHA8gcdFECBGBkQcxBnIKZAxyrA3Sk+OjVcY2zBEYkGJghUzgczBInzkr\\/MYAPyAMgRBAGQJwJkACYg9YDPK3n1sdvrbCdSdPv290T8z54KUkmRkndPEAmMTgyQTBjAHJPRck\\/A8TERBI8xjBIjPGejFxgH5Axg85IBmBOSMfGD0UZGBEQBgwBmIPgfEEmSeet+X7co\\/f6tghY53ufUbCb+pj9sBVEE\\/qwf9Dn\\/AK8mJj5M5JKecz7vAB58jggASIE5Hkno1SoIzAyIgmTAIA5mIICSJ+CeiioHkEZEYmcmCMESZI5+JJkjradxIkc\\/kPfuLYSrmTG9tr3EW5eh6YIUkGJjkADgEg4MZkZ8\\/wDx+gqx+w44E4mCfP5I\\/H9xqwCYJk4BmZET+BExIOZxAwClAzAP4yVCMiIHPCsDORE8dCUkDrfYfDCVUqEXJJ5yeYjrG3Ic8AcBS3kckYG6DwQPP5EyRBJAmOkalQnOZzIBCsI5JMn9uScx0pqVQEpjjcSBg\\/5ckHBwDPj9umK516KOnW6TkpKUJVncqCkD4jydwgiBgRJjSO8gDeY6cwZ6nYH5bWw21zzdIl2pfWEsstyVHYQOU8zsAJJnDPfrqKdssMqHqrBEn+hBMKUozg4O0+EgEcmYInJCjBngmMk4KvgyAAnGSfEEEx952qdW44ZKlEkiRyYCf2\\/5QD7pGYA6ylP9lE5TknbAMTOCBiCDBOMmOncIDLYSPxACTMnaL+s\\/3xTuY1rubVqqp3UGgdLDfJDciPKVRqVa8x6eAzMf+48yMGZJ4SJgk88ZMdDwkEqAgCYUckAEnJ5IAEjjB5mes7QMRxnmJ8gEnhI4mBEkAT0hqXQZbbPAO7aSRyYHjwmDx7QAQDPRaQDc7Dc+fScJ3XA0nVbVICBE3t02H1B2wB54uqkH2DAwQJkScmQCYyRIGAcgdASDJI+OcAf2PJCvzJxkdFpjmOIMBOeTHI+JJMyCcyYk4QCMTEwPgx8eMRjAMfv0OAOm4NjO5T59QL9Iw2q1OK1LJKjEzeLiBMbCIGNWQOM\\/gCY+ImJAMzAmJg54A4+ZwPyJGckH8gxwQZMgnrxBUBMgge2SokkAkAjOcnbBIHkzjoW2cQTxyPKT8EyTEAmOBPjpSLJEbkTe+\\/WPWJ5Y43QkdCBbbf8AIjle49958B8nnEyqBxHAJP5P5+J6GPAJAxIP7mRlUD+wIifOZ9tgwM+ZyI8kiDJ90DGJMEfIs+D4EA7REwUnAjkq5BVyEgAydiYvvg8ASNhy6dP2GMpTJOJAPMYjMK5IgyoEAY8T16J8zGYxyMQlOSYg7THPOIPWQAQP+sQDOClSgAcgiORn+ng9Dng4MwAMSZ92ZPx+ozGIHQiZg+6Ijpf+0bbYEB1B3jYb2gSdp\\/bcYCBgE45iTCjIIMZ8yBEDgxBHRs8QOIBJBIyRx5VkSCTkz5joPyBMjJIgAxBAjwkSRIVPBE89ZEyJyIIEAHBiACRkECSCZETOetC4kc7+Z8\\/h8hfGaSbx5\\/X9\\/XlPgI4jjIIJPIV7hESZiRwSSDEz6SYgjxESPaIO04KhKuOCST\\/cwAkSI4mAeIkkZmM\\/3xxiTmBzEDAP5P4AgxmTJInHB6zBmkGPL0v64AIxzEzHmCSATk5BPkgCY5x14jII\\/H6UjOCcSfIP5kxHx0YEn9\\/ke3\\/NMkDg8AQcnyJEj2kfByTiDkgmDAgH\\/NBAEGMies+uv19HGwABECNgPLBqQRTGPaXHxzwfTSCSDMQYOTyeOOsAQMZxJGCfH9uYEDjg9GOgJRToxIbKzOMuKnaABycEwTIJjz1kEHJBxJAKTKgCMpyqJEY8H5wDtUTE7BNtokAmR6zv58jgXUAG28g+VpETcH5bGcYbB3ZO2II24J+MzkngfIIjHRSk717BMFR2k+3z5mNxTnkgFJBk4IUI4VIIJIICQJxJnOQrICQJzkc9BQkKcKoSYzAJkAwYicYEHmTOCM9YANITP8w\\/v\\/SefPGEEQSLAib3mJjb+3njCwPaJEY8nPgSSSQrGCZAkRx0H+kSREJnPGfBUDETHJjzkz0YoErO1JjIHgzO6U4MJIkSDCsSRx1g5EgmJ3RMCJMgkxKskgDB8Rg9axtKZ\\/X6\\/KemCokmVRmP1QcgY4wQJG3jiekVZSfeMFrfscBDlO54bebkIUSmIQr9LiRlSCo5IHThmBBP9Jnz44P6iCqCSII5HQCMiJIg58AgRPyYzJIOcCczpSQoFJuCCOvp8+e43GBlAKSkgXAmevz+iYjEFdStaZ2ht1G9t1sCVoUgHeypQChKFiUkESkgpMqHRbaywvemTuUC+kRABG0OpSST+k7VIyCQTEY6kF0p0oIrWxCT6aasAwdiZS3UyYj0hCHCIJa2KUSGssymiZVmEkwAQeMmQeQUnAMjEiQOko1JIBMuNEQSCAu1jvBmwO8HzwhLemUkAECAbSbC9vM7wBYgW3bq6m9Je9Cf5LuU5BCVEAlGckFICkq3K9vtJx01qSvcFpIMTtVtOOSRAGQBwDIgmNxmX9TqGqZ6ncQpxJRua2keo0oSQFTISEK90xACiDAV00gJUEpOZCSDJ9qoMKEjkGQZmJ2gkcgdShagpFlEBRSAZQqE6wBsQTO3WbHCVSRJTsd9pj4QIt87RghYDgDqBE4UkA+049w4KSTySSSCknPWEgLGcLSJjIKgfdtIkyQRtxG0niIkfubc9w3NrkKVBhQGMARCkj9fggfMQWtBbUFIgiJSrCohMYjBj5MyQQQYnoudyRHIybdARb3e6TvjRR53gCBz2k7if7YLmMGYxgeQByY8jxMzic46V0rhacSSo+mcEKAzuMbo+AIMRJE8T0UpCVAOAgJKtpA4SQR4gbQomQSTJkQSZIElJA59pIyVZBJUMf5pP+gEcdaBKVBQ35m0QNNxtY8+l+V8EuIStKkKAIUIv53B\\/I4lCYAkEQDJkDnBwUwd22OeDOTAJHEADJkwk8gQTKpCuATwDOTzPSKid9RG0kkoBE+VR7UqGZMwRyVSZmSJXAcHjBB8SMARHMFP9RPnzy6oUlaQd1FN5neNrQbRe033xF3mlNOKQbAG07EHTe9\\/o2wA\\/GOODJGfdImMCBkAkEY4HQSOATOSRySfdzBOMGAROcfscUyJgH9wD\\/bJ\\/I8gAk4Gegxk4n\\/NBjOZ4mREEnwIAEgdGjbbncRz9\\/Pa5jrbBOA\\/B8+Qf3EH3EH8DPOOJnIwQc+6AYMn5M\\/BwOQTB9pgEExKRnggADicHIxIEgCSRKiACCZzgDBEzIyPcfOSRH9OMAYJIk5nQAggbEi1+g+h9Rk\\/XpH0fXGAnGB\\/zQPxgYMgnnKYM8dYI84EeBxIzG2TmDIMgnIPEEWRAMn8CcxBGZ2yZODGSJGesgSRB3TGIJG3EyB7SRgGCZJGABJFeeUfP63xmMAZE8cDJ\\/zSDySSeAOJ8g8ijH\\/XPkEKzkkQQYkGRx5z4CMQZgEx+TIOQNsnESTujBIk52+CQMpH4JEQZTtJBE8AiZOSOsxmAgcQcEiACQBxiAYPjIJUSBEg4FzwYMk+ZVE8AiAZk7Y5GTMg+AM8En8kgGMxBAmTOBkxAkz0IxOcSIiCCoHgHJ\\/KVHx8TnoOkW3tf1NjfrcYzGACVZ+YwEycFIJxBM4EZgkQTyIGeYwJI5B\\/SYgJj8wOJgGOvbTwc+0GSCYg8wCeRORnnkgHoQiP04+YgcSYSTIJzBiCDhJAA62Nhztb0\\/P440Zi1vy+vTnHKcBIyZkpkGACnI3SCABMkT+o+cwAD74Cc+OTGVT4HzwmZjzmejeZgY5\\/qJEZjAAxgEjMEk+SfBMmcfH9QG0xAiTPugzI3GQPnrYE2GAEGQbyYJE9COm8e\\/rywXHkZO0\\/PEgyZEjMAhJJGYOZ6FxPiTBhKpwJ8HbuGT+oyIMzyOJjGP7T8GOYUYAJ8TBHJ6zt5IjIwCIEiJgfpxKQDkgSIgScjymffgQNtiLCLbi21\\/n5ieeAgYjJgfnMQcjHB\\/UAecwR1nwYMECRwCD4yDuSAowQPiMnCsgcRySDEEznn9ODyJmCAmCII6z8Aj+nEQDPgpBzyTKQZGYM5JoGpMHlHygjn85GAGbmCNuQMEQRJ9\\/lNpkzjGP2Gf0kSZ4MFUfJHgCMAmOvAEj+wjBPE4zmQZASJlWUznoQB85VJGcyCZAhQmAAMAnBMAjJFEngJjIj5G4\\/1AEkE8yJjGTke\\/Lnz90fXxg2wHY35eV7e79MBI4zHJgnkeMEY8eSckDwOsgeI8cwQFCFAbsAgHMyo\\/k9eGYwBiTzj9Ik5OceZVyesx\\/TgyJ8EcHBwCYkwIkjEzHQVJ1Dz5E8ribSOWNkg7CL\\/DabW3iN\\/M4CAQMhRxkQo5lIPMgmQYgj\\/Tr0E5nJMjwTkgD3ZyCR\\/SAY88jMz+n5gxBJkQogGQSTzxI4zPXvE\\/n4EkyPEpyZgwYM5GSSKCJ2jlBnl05fU3xrYyDPxG3ly9R8bWLKePGBGDyAcQYmPAGZzGeh7fzjiQPdk8AySfaciRk8jHWYmDETjAJiJEEEnIEZn5JgAHrIjMDJIJAJV4HI\\/HwTOBJIJ6y9hFz9c49fTGjvtHlgIH5jiOc8AYj3ckc5k\\/ucgfpI8gcSDyJMk5OAADJGOeehgTwIicbomDxEkc48DMgdejPxA+cGIgkzuJB5mB5nx1nqI8jjMYHOE+J4OcHOJABk\\/IwZyOvQCBBjifxJjblOQCODBkEGDM5I+R8YEZmDncZGVA8CR+ehRxPMQBAJmZPPu5ESSIBgRInI\\/L84+f8AXG5O3r8\\/7DGPjCj8QmUn4I+Dg4JniBJg4CTEHMjxMx4gGCInaIkEyD8dC5gTAG0yeRJ5BJMiCRAkGOhYHmZPAyfmPBBMRkxBEDmcAtbkPr0+d\\/XGSOY9w8ote\\/XngsiJEjJ\\/aRifzBEEmPmT1kA4Ef8ATBH6cSTmJPwR+SOhgDxJkJgA5HgyMgD+5GARyQM7TkYEGcBPuz+TBBBGQIJBkzjoQEk3HLcRNwOW3rM\\/E419XH1GCwPM7uDH4PJIkRyBiZzHPQgJAByMAe2M\\/wCgAOdoyMkGTg9Z2\\/sIE+Y9sjA4j5BzMeOM7Y8biRJHtyB+BPtJ8iYBI6zSTMcuVt7T5Wn9MZh0tN6u9jedqbPdLjaaiopXqCoettdUUDtRQ1AH3FFULpnWy\\/RvpSj1Kd3c0spQVplIPTaQMyBGIH6RAAmAJHED5MHA468AQeCUwYABkxHkE5kwTEZEfnO2OEk4yApMQAJTJHOJBzO7Bx0cFHSEhStAJISSSkExMDYHlI5zM40EpClKCQFKAClQJUBsCdyBcgGwm15wCTPjMZOeeTkkfgzmB+\\/WB+IxH7EfjOOQY55AiYJpQuEr2wFKKEqGApQBBEj2hcGdqlBRiQD0HbjgkTzA4AwJIyedscT+ATop23i0Qeke+23SZ53wLa0jfcXI2va\\/PBYz4BPPESMAGPJmOQPzPWYyPxAAGCdpgFQ3TyTJ+CJE46HEHPjj8n5wfcZIAE4yDCusgT8RAjj\\/AJZwcSJmZJzmQesjoOXlF4G0jaB0xr6+OAwPPBEiDE4OcnkQR8SRtJPXgDOMkAmc8STABMwCIJgE4n85I8gGTM\\/EjPBkyMSOATtn5z8E+IEeVESAPcZkDEnAPOQSQ3BB2i1+kgco6kid9+kYcY4jyQIgSBiDHnzA+ZgHBPQgf+Y5k+6YnnJGT\\/mnjmMgdez4yYPnOMwYjAPOTyJx1njwIGcjHkicwD7sEExkiTjrcQSQL335ncc7X8vhjW0e762\\/S\\/lgGJPAH4kgzBECCQTkJyCJg5jrMQBjBgZmDM\\/2z5JwqCYPkQBVxJlMiZIBMbgTjJxiI4gEwelNNSvVJ2NNrdUARCEhR2pElQEFUDMYOIkpyehJSpSghAKlEgJSkKUonwiABN97e\\/rjRUlAlRCUpAkqICUjqSqBHmbct7YJaWtrft2fzG1NlSm23CEKI3+n6iVems496Nq9pgEZ6LIM7uQTBGBPGDI8yZMTMQTOTlIKVbVQkiQeQT7oKcjKwSQfmI\\/cH5wAQVHgGJIiIkJM+eDP562QpPhII0kiCIg2kRyNhI5YCQCSoR4okgC4sbEQSCNgJm+NlfpY1x2P7f8AdO2376gO19R3Z7ft01yardL0moK3TlSKqptz9LQVrdTRuRUs0dU4irdpXwEOFCFtrDjKGXaT1xW2C46qv9bpa3\\/wrT9Vcqt60W8l5QpKB11TtPTIXUOu1K22kr9NDr7inVgAuJQoACMDIGcEQARJ8AjgjBTiPmc+QiFZ8gnBkEpMRIgjcTAIIyfM8r3MwW5QtUBaZCGl6w4EkOEDWUj8RQDqcJWsJC16UJUohCYbW8raazOozVLtSp+op2qVbKn3VUoS0SUrbp1L7ptw6ilbiEpUpNyTgIj94gHckEn4AxPH\\/L+ogyfOSJAEGDOZ2kjhX6T5TMk+I8Y6GBgk8BWcJmMCSd3gSfxGc9YAzODjIJ3HmZMGMEwY5HiMdNoTBBv5eojn+mHQRcEC3I3IsPjFxPoNsK6RamnUbCSEnIGPaYSIBgESRwcSCJ6kYHkjPkgHA\\/UIBBmfJkHCoMZMVQeOJMZAOMjAAkY8Akck5jqRU7vqNoO4EmFZThJ2iRmcA48EKI5BHTvlikjvGyYkhSRYXsDv+kWHPkFfI78pOw8zbf8AaIIthQEkCNsxgwOZT8SBnEk5mZweseYEEzE4IM5Ecc7oHJ3HzjoQIz+8\\/wDMrjH\\/AEyZxgdeyQFDEAY\\/I4n5I4ztGcxI6clEAHfzJAi8RF7859NsAJ58zv8ALlEbj65gSB8ZAHgE4MkkGIEx\\/VAJnIJ6FtOfacnaRBAI8ZBIA5J8T\\/ZPWd39hkEHge74nBiMfOFZz0MRhUj9PBIiMlWPcnJAJjwIx0WAFAoO1ietiPO0xty64Frm9t9pJ3jaOUjfzuMBAkeMATzPgmc85GSqIIPkHrMAcHkgGDkxMhUA8gRHMkiSY69gmR8iRhIAxEicgyqJ5wPAkUT\\/AHBjjyT+eQZ\\/aCAeZOAm3w+I6fXW2Azefrr+d8Fx8AzhJgyJwYKhmIPnzGc9ZCeCRBgmIg4VIidwJnnyYB4HRkH4BOJkE45IHyZ8DE4E89ejOM8kxORGYmPJHk\\/vCehJAmN9\\/iI38p2t8cZB+vKN\\/lgGYBE\\/0k8gnAyYOSScCCkkcwTI0ic4xxI\\/BTIyeYwRA+SSCOvAA5xBOYKkgTAkR+TzjGMHPQyQSkgJH\\/MnMkfAn2zAERKiSCSST0ehNpPI7dIi195Mje218DAFhB6GYixE7jYnz3tzxkCMweDMkiROZ5zwSoGPE5ytt7lG3W0i7gy8\\/QoqWFV1PTPppqmoo0PIXVU9NUutVDdNUP06FtM1DlPUIZWtDpZdSjYpFI8ADxgfMfkwCfaBxMiSCOsGDIGJmAfjHAgpOcAHnEgxHReopWNoBBg87iZ+fnYyIxipvv8AA+Rkn3QOnkMTruRX6FuWudT1vbKyXzTfb+putS7pGw6lujd71DbLIQgUtPebu0hpi4XAFLin6hppLKtwSgEJ3Kg0wMj2yBIgT4\\/ExmAczzOJBuGQZyRAjESOZ8kETAMAkfnot11LaSozJgRG6T+mBAOcZORtEZ84843CnCQEpEzIMC202PLrc2vGCYPhSTJGm5AvEATylVzIg\\/q23FYV\\/LGQBuWMGI4mIznPyOIB6ZihCeCcAgiFRHGfHI8ExIHPK1wlaipcZ90nEGIjyIxwB\\/rA6IO1JO5JA8ERuI4knIPIySBEHJ4i9QtTzi1WvGm0AiwA5yRhSkBIHxPP1wUWipC1JKShvbuCnUIUQs7U+m0pQW6QR7\\/TSr0wErdhBnoojMbo4IGRPkAAGMkARAIMyflS4yUrWE+6CIVk7gUzuSSElQJkSlMmJ46KKTM7gQPAnOROYMkf6AH4BHQVIKQdQg+nKReLeYt7+uMBm425WAPLeJ99\\/juSIAPESP8A45kAkmIJknwT4yOvAAjjOeI5JMA5jMHdHiCOjQOZHMQrEcHBTA+MDPH4xiD8GCBJiMDPgEwQSYOQJgeOi9xImPmPX6vjeCdkgTj4xxPIzklOBgSCJPkHB5wBx8fATwAAfjkYEyDjo8CJH7TtxGSRz\\/pM5JEgEycFPJBiTMgCTgY3D3cSfmZBPzoTA6wAZ90j5T7sb1QIvBgG3lBt57zbBOJkzOZHmAYIjj4A\\/IzPWNp55GDGeABI2yQRMCTIknPR8ZESADJwk8ceJmJiZPyMjr23AMYgR+P\\/AIkc8YP5hUQJw7j0N+m0eu1\\/z64YN+Z35Xtfpe\\/TBcAjzBP9zH4MjdnHEbYIjn0fjBB+R5nHMxJkyD5Px0KImYHGIPkg4kHjmYBB\\/MTgpJg488DH5BHERgniDIkE9BiJ6cvSRPw62I3E8sFz8Nv0sY9fXfmEiQIBBweZMjkQCcwYyFQDMkT1YWg9BK1Wi9X681y9Odv9HM0tVrTVxp1VQohWqWi0acsVIsoTe9a6nebdo9N2FtweopFVd7iuksVruFa0v7T9rrt3W1K\\/aKS4UWntP2K01eqdfa5vAWjTnb7RNrKTd9T3x0bSppreiitFqYUq5ajvtVQWG0su1lYkJdO6fcK0an\\/g+i+3turtO9odCOVaNGWWu2ovV8uVWltq89yNcFlS2qzXGrgy0upShX2mm7Oig0rZktUFCtVUc02mC89BQNJabE631+GSTEJYQCStRI1GEpCibIKitUuq\\/htEoe0obQ9XVCUhaMvYd\\/2tQMJNdV6VCkYIUEoSqqfR3SW23oTqvUw1FVU7FvtbOntM2dp2i0zpinf+6atFA4oLedra0oQu76huq0IqtRageQHbjWAMsN01upaChpomR4OIweZiZOIlPODJIyDOATtggiD4njAMmImYPECdskZAkgIE8\\/jAIM4IBJ5jOZzgicdAVqWSSfxQTAAiIACQLBIAhIH4RG\\/JahIQkJTqhIABUtS1G8kqWsqUtRN1KUSSo6iZwAjwZIE+TIzPOIIMCfAIxA6wRkgYIJI\\/JGBMZnJBj28A8jozb7QPwcHJIAkjIIkz5AJg8xHWI+f2jIHIjic8AniRHMHoIT1JIEW22jz8sDN4i+3LnAEee3TrvgrxPIx4kgiTiYIOeIMKMn8jiTmIyBA+TiARGQOZPEHmOhFO3IB4TwCYB4z\\/AO4QCROJyJkQTiTA5geYiNokQTBJwecjkdBKdo6gbXi0kxHSPfJvjQBJgc\\/X5xywVtk5HymfzIxEcTEnz4JnOCkyCeeJIOIkwDET4HI4yejCPJAAA4n44nBBOYH7ERGTkjxBHnbgf1fkERM\\/GPxnrAmSRfaNrSI2J3\\/aeWNi8+6OsyAIMW9P74KAkR5B5EQcf8v6pI+RJIBAJkiKfBGDwMSD\\/TMyZyR8c88kYABjniCIj55wZ25kcTxPPgMYzjJnE8yojPz5+Bjd1qIJHQfkAZtBAEW+QN8GCAPMC\\/XYT68vy6YAATOJJn2nyZyckERx7cQoYzPWIJnAJ5Jkn9JEH4zieePiOjCBkKjBExGZPOZg4OQTGFT46zE8n9p+DwMwMEZO4kpJx0HTdQ0+g3vbp0HX9sYDJjYgXHw9bbj6BwT8E5iJAE5AJykSBzzMkcmB17aeMTP6TIkAnlJMKEnBMkkfAyaEzM4H7YIgxMiMZx\\/yj4g+2zI4jOYHBkGP6ecGD4iAc6I5kb3Hra\\/nbY\\/PrsEG4+vr9x1wAAcyMCAcEnAJGVHCp3CSB7oOeswPxEwBxmFQMTJB8kweTPHQ4zxPgyY3QfwnxxzI\\/EjrA3ZPJkSfcMcwUxAgwkeDMnAPQCAOVuo5bfPyvt1JwIEgi\\/Tcxafrbz88eAnyRAnzmIOAR4MAD4nABMmbRBxEEHwIA244IkxODnnOJwmPyBmCQRMCCIBHt4nHmY4HRkGJkRx+CYER4JH5OYAPWJSBB98Hl\\/b0GD8YggQABgTMYORMY\\/cGeJ5MDr0D4ycnid2BMYzJgwQADyJHQwOIJ+JyJHj+8mACBjEHnr0EnII8RkgiRjImfCgckcx0PGY8lMnMAjBBJ8AgECJkTHIBIJExHSxspwrhWCBOTGTAxzmecEZPJSgH8zyIBAlJM8\\/PxGZOTx0obPu+BEjIBHAkQeIHIzGBjoaJBMbSCqdiB58renO+ArTqHn9c+XX3YWJROY8iIwMEgxlQEmeDzxAgdBqBDKgSJVtExiCYkAgkckH8QfE9KkAkYJEK\\/pG0SY\\/1En5IPgHHRNVG1scgqEgH3QAR7oESDwVYk84yv0HQTtIgbi5INz5\\/rcxglNlpIGxHUbEG3l67RYb4QJR\\/LMwY2pTESSYkgQZMSJniJI68hEmN2FwAYBVmDkH8CDBPHMcq3EbENpA5SpZBIJkDYPmfIxg5IJEyBsgq3ZwRxAiAMfjwZJwAMxgkuN6FpEXCUT5khJMk9CRvz9Rg9BmVHYm15gEAW6ix+O+xxt79M9ImtptUII2gJp4UU591M6J4H6dySABBncYUOtte0ujajV\\/chuqSmKfSFut1Bb1iFBN9uK6S12+IhAcpKqvqrgvaBtXbWlLlLHWsn0rhDdJrB90lLbLFNUOEZKGGmXXHFKCtoBDaXJ3ECPaAASrrqb9KGi3GbZTXWrYCaqsU9qavlORcLlTIp7YyrBJVS76mqSDgLClJSkKSrrrTs1yxOZ5XwlTlIUju3qp8EFQIp61QZTB3UHlKfTqVpUKVRAUBjz++03x2vgjLu0CtQU9\\/VmjyajCiCQMwy1LuYrQjTPjoWzSBRUAlysag+KDWP+IDrljQPZuz9vrU4mld1jWMUTjTaglbektHN0r7rcCNjVRXizsHdCXUIfQRtKx1RHaXt6rtz2c7Aaqr2FN3q4d79Ia6v6loCKimpe4CKjSlupn9wCgmgtF1srakKJKH3agEAlY6jffypX9Tn1gsaEoHl1OktOXBrSZdZUpTTVg0mKq661rmyCUpXcq5Ffb6d1Ih1Qt0n9BO8neywOO9mdeLt7Xp1VhsX+1VrbYTtFPVaMq6XU9A3ThIlIbVZwlgJCQkbcgCepSzT\\/5q4g4\\/4pCA5ScO0n+WuH9JKml1OU93mFctoAHSFVLLCULQkkt1KkgKKTNM01eezHs67C+zuudNHxB2gZk3x\\/x0lYCHm6fi0nJ8qpasHxp7rLa5TNQysp7upygKnxGLf+1hW0iNqYIMbgQkSMnn\\/wCxiPEwFjVMDyNwnHHuAE5Eef7wOc56W0bzN0o6G6UwSaa6UNLc6YpIKVMV9M1VslM4CfRfRETBGAMdOCGMgAD5OAnAJ\\/MckTiTnq+krQsBaDqSoakEGZQQCkg7xpgCIBAuAJjjmtq3adx2md8DrC1MuJ5pcaIQseULSqbEjrzwhbpgYxjztAgk55kgqmckQVAj5le3T8GNvBg4yIPiYMkwCPBJwOl7VOSD7RGAAQJzEAgRknwZHJzI6cGaQggkbjA\\/BERMiMRI+cgCYJPQVKB9Rv0233+hHTDC7mB5qM+fuF+sCMIGacmDGBAmFDiD8R5ycQBzM9OrFNJVIn9RE+TBGCcmQD\\/TnIHghW3TAcDIkQCDkRiImMZx8xjPTrSsNISp19aKdpKglb7pShhhJ3eo8+SClLVM16lS4s4ShpYO3BCJ91KZkwNydoAuSd9hf+kgIfbFulLaApa3FIbQhMla1uKShKEJFypSlaUwJkwL2PP36ydSNaP0fdtKsL2V\\/dXVtruVwaSolw6e0fYLDTvuentCUIrbzT22nSpK3FPLoKwFOAlOqn+Hz2kru731T0mqTTK\\/2X7O0zmqbtU7Fegu+VDdRbtMWvdwXVVTr9yJJU4WLQpX6SAmCfU93ac7i671Jq9O9uzUqE2HS1LBApNO2xx5uhUEDKKy9VKnbpUo\\/wCI3UVLrUrhJ67kfQL2Jp\\/p0+m+kvGr2U2zVmtmajuX3Afq0hl22Mv0prLbaahSlD0hY7Clht+nIStm4O1wWlClbU8iMPf5+7S3MzcSU8O8OO\\/xLURCC1ltTUuZehR8Ok1+ZOVFc4FJAVStOBckTj1z7T3qr7M\\/2ROFuzppsVfan2s+zZOaRpBdrk5txDQUNBmrbKAlb7ieH+HWqLIGO5UpKs1dpltpPfEGp\\/rr10mx2Wydube+E1eoFqu18QlQSpNopVo+zZWAoKSmrrG9xSVAKapyOFR1y8TnOScjECYIBExOP3kbQZPi1e83cWq7rdxtTa0qlKSxca5bdqpicUlmpf5Fsp0gGEfyEBxcgBTrjhMnKquKTjzg4II2gZIwOYHiDzIIGKA7TeK18X8W5lmYWpVG2v2PLUkyE0dKShCgOr6y4+owDLgB2x73\\/Ym+z8z9mz7OPZ\\/2cv0rLPErmXjiTjhxCUlVRxfn7bNXmrLi03dTlaBTZIwslQNPlrZSfESS8ATE4EjBgAgkH8TMRJJMRiOh7sZHBggAwn3DmMExB8R+Zz4+NpJxEBPkiQAYJ8CTiPBgx17HEHmP6jIJBBkkTtJgnk\\/nquFnzkRt0FufOYn++OuUNiOQtEx8vT+mBJiRnmBEiPMAeBJESCVfOBkRkjIAJIgnEkZHgkzxiJP56wBGRMkTI3BM4UP83gczj9jPQhxPggGSBHwJmTtmJMEgwTgdJyqTPTYdNvP1vH9C1IgiItH7zve31O2MSR+D7TKpic5A+MkwR8\\/Jc85kjMfndO2BIGTj8n4PRsEyDGCQBhUCCMiOSoQSMyRmD0WUTyCOMyfcZBBjGZMYJ\\/t0EGJ+uY9\\/Ll68sYlMTO\\/L9f0wSI3KHuEH8pT7RI\\/qBCeQTByPbnHWVH8T7hG2YO4g4MGYMBRwSPE8mbc\\/6\\/0yM8SYkkfnHP8AcJE+CqQJ8nPkjA8AgeIkH51M7R9Rz5\\/v5zgKyJHON7+lvlPvOC5\\/b9IjHEwQOJJBHiInHPQDJAglMT8wTEzJEz7jAkpMefIoicg\\/OcSSSZyPj3AA+CSegkGACB4SDAzmccyCFAzj8noMzG0noZEA78t+Xz54LMWmNxvA\\/P69+AK9pJgiYTxHjgk8Akj4MDIPkOOCARkgQR53QAYmDjP54jIyJmNvGRCT\\/lyZ4McDOc9FqIEiAI3AyAM+6OSkwcknxMc9DF7RP0PrlvvgoAEWN+fW5EeXXn5GJsUROTz8mEkkYzukz5BE8QMdBAJ8QRngCUkeQQE+J4JIjGQSYefdGQTAiZkfuSM88\\/Iiei4gCYmRGEjicTn9kx4jJGetQRBPMTuY+v6e4tYta8\\/EbSB6cxzmfUo4zJPkfuROIHkjHnGeiiJMGT4wM\\/GZEwQfJIOelK0xJiAk5mM+RHP6pI+OMzHRBABE8ZEZgZHgkjBkDMgeI60dxYH3SQbRyMe+OuEhSRJEgeXS3v8AffbYYJOcSIJGABAgmcZEAAAkZ8gdEqmMxgnI4SBgiCP2gng\\/PJUqjzgyMCBmcRzicBcn\\/wCP0Q5wTkgRgDkkEj2k+7PxymQQPOQLeW2\\/l+w9998FqkggAXEfkJ\\/X1wlVuIiRiByZABKYAIiciP8AsZkgOJgiN2YEjJTIIgg5wAPbkwZBgR\\/cgYnkFUHJzMzkj8cxPQVQMcEqEGQfBOTz88EAzEDoaRJ6fQ\\/vzE8hhGtOk9Lm3Tb5XwFRHJ\\/qkY5gH9oA\\/ECcZjkuJOPB3CQJ\\/EmDHg+B+ehKInyCSY55EQIgjzMCQJ5yAS1KweSoDIHImMQMhREc\\/PBiesAJMfL4D9tvdgk6YJ5WJPmNokR1n54Q1bgTvcUoBCJKiTISE5JIAgHbB8kEkc4NV3a4Kr6lRSYZRuS0gEnmDuMEEk5PEgAAnMmQalu5cUaCnWClJmoUCDuWD\\/wxAEwSN0c\\/iFEQuJEYGIk5EHhIAkwrGQCI8ETLvTtaEhREqOw235Rzjp57wcVHxXnRrak5fTLmmp1w+tOzzwIlG922yCCf5jO0YEgJPMiIjcRnhQIHM4MA+RE5BKpAnMQEkwec44MwOPknER5KdIVPIGT+kGcQAScSOSMEwATyOjHXEtJ93+X2zmTn2gx8TnkTPnpQtBIFztcb3tJN78\\/TEbSdKJVZCQecXMRE\\/v5mwwXUvhsFKSCqIPykcSokjPKh484I6bQmZJhXk4E8iSeST4k4O4iTyRKUpxRJODmM+TgCfIEhOfJzGOvAf3Vg4EkgRG0AA8j5jEwPICIAF4H7jpz6Rf1JwjXLytRE80joLC+1x5bD0wNIEGMQQSTAJA\\/12zOPGIEEHowDx\\/mxiRPIPOZH9sxjE9YgQOSZyI2x5II4JyYIMn4AjpQgDI\\/B92Yg+CByckzyfzAPW4vYfr0EdeVr778sDba1mwg+Qg7gCLeflyvjVKAAAIkgzyDwJ5BIH4J\\/UMSJ6MH9JmADO6dojPuMZMEgJnBGCJkdegY+TtkgeREE5P8AaMzPRoH4kE8wOCDM+JAJk\\/3E89HpGxkzG0yP7GP22xxehJG3QXO\\/L5RO0Sd\\/IJgREDgE5IE84G74wJxOUjjrITAmTJGSed0iSRtMEYzO4RIETI9oGeZKeSJAJ3RGACB5MkCTJE9C8zJJg4A5PPAImf8AMcERiZkXPf8Ap+uDQk+ccvcB+fU9cYHOBzAknGVEg8So+QSM5yASR4JORM+TlX4OR\\/8AOwCSAeJGkHE8EDz+QI92MAieTnnMDISZMZIJkCTifjBycc+CSY52DEj3W5QRt8OWDQn8hI32AsesRuIxgyMmDHwJgCcRxgySZBAMfk+KDHgGQCPckHIxiQpUzB8ifiehwMRgkAHhQABgxmDM4SUkxIgmOhBPwMjwMT7gTxJ3AkjBM\\/srrJjY7+Vx74+EHzscCCRPT0HyPKD+fTfHhEfgEeIkpifPyZzlUgSZ68E5\\/uAOADxEgGTkGZEmNvIjoQEQRGeIBgxkEg8RkeVERBnHQwASUmeRBHPJIyobjiSduNsRB61y9N7T+WNgT1sOcmYgHYec7fvjwSCZAgZB5IIkHHH5ISrIIHMdZjfCQTJI255yE8HkcYGI8njoYTCcn\\/KTkHz4BG4xiB\\/SMSZ6MpwlTyCRIQVLkDIKAcjOBMeMz4gRgA9JN\\/3xsjmAefnyHUef7dcefSC\\/CSIRsAEQQAEgiDumTkSI8CBwEASCcAYiIk4EkH9xtiJjaYwehEypSiYK9ygSTvkmYJ4niBAIJOY5zzMyBIIMAEYCZVJjPMyRniYPQjckwN5tz2FrCT+flgSU29Y5bA+o5ix6YwSTKgeTPEqnaqFGZ5gEZB3Z8deH8tJWRBJIgmYKZgAkf0\\/qGcASJiegiefECARxBTgiIknGJPOD15MrI\\/IMpGBMEKMEncSYyRB2456wGJNpgATty8xy\\/WTjCgna0ec9D023\\/QCZx4BM7jMxmTBkQTPiVHxujAIgT1mBzgcT4mIzA4gk7uYMEjB6Eox+qOAf0mPG4beJA8eZAOevDJIkhJVPOCZETBJhWSSAAMEyeg84\\/Q8\\/r1xsWAIB\\/WJG1ha08tz1kAIJEwBA5jKoInMzOMGBIkTPITgyNp9sQM8Ek8q8KwknIM+Yk1RJE4KU+34KonaJBnkcHJOecnEDkRIGcTPIIJVyTiYIgjyOdkgDfYSbehPwjePlgYk7iIvvOEym0qCkkBSYUFiBtWDghQMhUgkH+naqVRuxE36dVK8KYkemYcp1H3FbAITsWc71sGG1rMSlSFmfU6mRTJI3RKphOAoED5lROYAMpO3Px0311H91TkNx67UuMYglaQQtpSh4eT7IIML9NRB2JHRS06tKxum8f8kmCUxzkCRYwfXBDqCu4AC0kcplNiRMR+8QbC8PqABC4EuEN53KGfPBEEGOBBA5GCU7RsrpU1dEXIbIFXTrlS0OJBK9siSkgpPuwsK3DjCmpT6lMsoVJKZSPclZUmCJG3cCI2qkYKYVIklJbKjc6mQfUUna62DuNW2n+lKZCC+wQVsn9TqdzRlRSCFIbLobWIS8AErMSgkiFSegN07x57oX5EFMeEaognUAAdItMk\\/1BmChlLgV7sKMg7ykpWOCJyd07vyARnB6KSUyW1mMEIjBMEjAO4ZEyTOEgRnpdcKT7V3e0d7TxS4ytMFBSqCAkgSnEKQScpkDCSOkKiVRO4LnAmCZkgj4gkiZAPBJkdEOtqbcLaxCkeFQ\\/wCSYSQRuIIiCLX58yUqCwFoIIMXi8Wm5j5jAUH0nC24JbXAIE8EEJWPaMp5xEnBI6842WlFBAPBJE7SkgSUiIJ84JI\\/MjoSj6iQSf5iR4gbhgCIEgbuJByZmCOjG1BxBaVIWn\\/hEmCozG1R\\/AlJBkCfJABL06gUzECUespBTO99x5+mMKYteDyiYJIv6QIk7TO048w4WXEKkbcBSSdxhRjBMAwIgwc5kjmQJVICkwqQDuzJkSSlQGQScnMc+7nqNAQODPnjmIMj8f8AYEjiOnahdlPomJSD6Z\\/zg4M5JMRnAIBzBJBUUrkK7tWxAjfexggzvty3t5NGZU5UjvUjxN2MDcSLj0Ee73YcjESDI88yDzAgiIMQRzP9UnoRH5mCCBHOUjcB7j7Y+QMkyJM+SCfIEcEwkkRHjkzmDgkEcx0ICQcTAjgEfgwCCDkk4xEcnpxww4B+ZKgP3mf1RBlPBIOQQPwB1k\\/kExg4M5iCBPM8kGCAcEkdDjMczGOSc7pgxBEQcEjgcHrBHE8iYABG2DMgyUhWDwNwzEkic+vr5Y1z3v8A2J\\/T44CAJwI3AHbBI55PEmQPmM+Dj0cYz4wZzEgJIPn4gExxIkzaSBjEkfpzmIJgyTjMxIOTz1kD4AEZzB42n5I\\/ERz5MYzG8AEfkQMSFTBgEAkj3HG0jgz5AkUciPMg\\/wCmcwYwDH5EdDgf9Y4GZmR5En3TuIB9pPXgPjMicgkjgfgSPxjnrMasfd6+R\\/bGDHwTxmBhOczOCBMiZjxjrERHBmAQMwDzAOBAxyTOc9GBMCcGfnk5iSMeADnkjH5HtJHzPuAIkyOcR8YkEyRA4HQwNQi0gGI\\/+P0Z6+Vsna9uXXl1P1I95QBkwMg8kkjiRAhOMkTgHAwJ6GExIGfBMA4JEYjBGPkDxM9CHMz8jOOcETIAyMjBzEDoUZmDEiJAJ5SeJnaTx88E5HWBM87jlFrETfn8sAUoiQCDvymOUH63tAGACcHkgCc+6ASSCB7YBgHwDkiJ6EEk44\\/ER5EcSNwMkiOM8HoUfA5EkR5J5AHAmPzBnInrI+OBIwZ8QCOY\\/I5J\\/qz0LQBECI\\/T63G3ToEESJJ5XnyAPSPMzy58whMyYxJx7oPiYMCZ5SRnmfHXlcE4k4+DIAxtJwJwSMAHJ8kcY\\/sZ5yR5JJM5Jxg8T1jjwY3cQqZEcZOSDhOJBmdwA6GhKVb2IMR1JjrvECLcx0AxhIgATtc7GCZj9RfY4Kz8jMYjB9xyYMKMAKkE\\/sJnoYGSP3BMDJCiQTgiAYyDBmfnoQHAzmEiPODAEbhxwk\\/JggcjA\\/AAmYPEpzOSRMERt4IP4HQlAJgAfAR0HnyG3LrywHAIkZ\\/cYOdoHEn3ERHMnniB1mMzMiBgghMAmCQOZBJwoiQcxHQyDAMZMfgmdvzJkn5MEHnrMZg\\/sBwZ3QJSSEjPlPBxk9COmIGwEm5m5HmN\\/kY2wFJ8V9r2npHOPP3XjexYH4ByDkwOQcSCZBgCIBEZMEdZIjGTIIEbZPOSSTAznM5OBgEUf0nB8QCSCYgcTB8kHOccnr3gxJKuYBPugnMxkY2iOgeW\\/vjePdb59diBYBAMTztECR58CZI4Hn\\/485CfjzAPnn+kbk\\/iB\\/VMCIPQzJ\\/p4JBIkRkKiJEY8\\/p6yfkgnAwOcDOMgfIJMkeFRPQkpk+6SN+kTf3wemMN\\/l8hGCwJBmQCAD8ZkfpIBB84Ejxk58AeFYg+QeQTH+USQBmCZJJmTJ0YzHwPEyRxPIOBII\\/vjrxTxPtxAyQMQYgyDk8iDH7DoQSEm255newgR+UD9MZgIAkGZmBGTM\\/OIg\\/Px88deEzOcQCEhQIG7iCCCCAZzJ\\/Mx0M5ifkEcDJJ+TwfPuGIAg8+HIPmRgSADJgAGJIJMnkgEc84pM38v1F+u3Tp7sYLm88hv\\/Q2AtI\\/TAQODJA8SBO2RzOIOMAj52gmehACYA5g8SCCSOAY4MEAkwQf2EkD8CCBMAZHEZBOQFEgHzzyRgEjO0GCoYEZ4iRIVkxPPKiDHRYHSfqPWbkW9Pdn19fVsE7Tx+M4JyJE+4gjnwDtHBg9CgmRH5MyQYiCMxyk8mcTx0ZtGcDn4BJiORmTGCQRjEiM4CcCIgiQY4\\/V4wR+fx8noxKYM\\/XL+vyPpoGR1IsfXBfEQCSTiBI5ChGZEcAAwSAAT1mPiB585ggiUgkk5IGAJxHQwniAJgf8xicgcADI\\/V\\/frMAHwYkkg4Hk4EZJiDEAE5noQB9\\/ltyHr87k43gETHI+SOSI5Bkzx+nkmPOTnaTIBM5+TyM8xIjgR\\/1x0ZHHjxwPE\\/nCvg4OcGM9ejnGMkJgTBgeZMj5GDkDgEiAmYnz8tup\\/p84zAANpHHwRIkSD48JKsxMpMeB0IfkGDECJIkxgkmPaIKsJn4kdZiTjOfxmSoYBnziTkTxHHgAIkgiInGIABAHA5kZJETJ463Eiw\\/PymbcuQ2vvIM5Nx8fhE9OuD6apcpnCpCW3ELEPUz6C7TVDachuoZlBcTP6XG3G32idzLzLh3dS6k0w3qxt1WkQ49fGW3Kip0Y+4ly61DTCVLde0q6pY\\/2iZp0IC3LWlk6gpWi44Ke4UjDtd1DInkfPkAHJ+Rk8R4Mx5MiQtbTjbzLjrT1Ott9h9pxTTzLzSt7TzTrag6y82sBbTjakONrAUlSSEqG0LjwkakH8SdlJkiVIVB0q2kGUqtqBMEFrSo+JtZQ4nYxKFbHStIIKkmP5SlYsUqGxTKOVjMtrW2oHKgtBUlxKwoEpKVylaVGUrEKSFDHuJJkAkmMkZJkfkDnjIBM4jrYizXDRveZtOn+4N2tOgO65abpdLd2a8It+jNdViStNLp3vMWGw1Yr3XuKRTW3vBTsfaqeDNN3EpHqdf8AtVRUxqrSWpdC6ju2kNY2Wu0\\/qayVH21zs9wbSmoZUtPqMvtLQtbFZQVjSkVNtuVG7U2650TjNbb6moo32n17cb0ALSoLZKghLmkp0r0hXdPJiW3ACeqFQe7UtIKgmYrEuuqpnUdxWIR3hYUoFLzY0y\\/SuQE1DAUoJWpIDrCzofbaUpGuP85xGTJAkxAgSqCT+PzMk9eAESRkgzPHxnMmDxMbgYHycjBz5PHHJO4f0geD5mPBk9GQMEH4BAgA5EH9U7iZHMSMDx0GJTq+jsBvt9bbBSFRA5ctgdp6xzBkxzwXtGZMmTEQRwficDIgg\\/JM9ZCRgZ8GEngAg8GBicKyPAEdS3RVfpS031ms1rpq4at0+ikuSKix2vULml6x6tfoH27ZUJvCLZdyy3RXFVNU1FOKJZrGEOU4cZLnqCMrKVKUUp2JlRSgFSgkHITuISTAwJAMSFZOTQ0O6S53iCpS1JLQnWgAJIUq2mFAnTBJtJABE67w94pGhUBKCHJRoWVFQKUAKK9SYBVqSEwpOlSjqCSiBjAIg48HIHgTuIwRxOYmer4+nbv\\/AKj+m7uB\\/wCI+ltPaN1NeG7LdLNT0OubE1qC1UwuSWwbhS0ri2zT3KnLKU09ShcJbW604hbbqgKIPyYJEYxMRPBkgEzPO3ESOsQrkk5MQScAfq5TAORkY25GOTKOqqaCqaqqRwtVDRlDiQlRTqSUn8QKT4VEXFpkEGDhFmWWUOc5fWZVmlO3WZfXMqpqumcKgh9lyErbXoKVBJBghKkkcuRw43i6P3u63O71CWkVN1r6641KaZlFPTB+vqHKt4MNICW2WUuuqDTKRDbSUgJkSWsTIyZ5kx5ifj3DdBgmPMcnJG78iCCYEccSAZgzM4MnPPWYPPGSIPnjkgmSDgQPB+Oi3FLfcU64rUtay4tREFa1nUtRG3iJvFpmABbC1ltDDTbLSUobaQhtttIgNoQkIQgRNkpAA9JJvOPDOMDzB4kcTMkq4IAkHI5jrMDnOTGME8jM\\/wBXM\\/I+CYOQIEnP7ZERMZ4xJAJmMScyYByMETgcHiYkE8\\/IklRxwetaQNgJgxI\\/P69MGSJj65fuMBHEySY+ciRHmTAIM+T5Bz1iPiRx\\/ckqiBBI5wDAJxMdGp8jwJJkRBII3QSZBiSPiYyZ6wJMg\\/k5MxkcyPHAAzH+nQSkkzbc\\/ARAPr5C2ApiSB5EdYjb0Hn164CCRJiQMwMHiAf08yc\\/2JHPToy2WglZglSI2\\/1BJSIk4BMjByJkkAxLcUyPCeQP1AECY48kjgx++B0vDy1NAYgICSqf1ASZyoR4OCYB3E8AqaXS2tSlb6RpHmFAwYm1vhffbSwYAAsYBN7DlEefl5dMLfuEpURt3ASArcrJwTiB4nwBugZMSV9wsn2gRu4kg4gJ3R7YVjcR7iYJGOku7AIxgQTkzGY+DOATknBiZIwScxH5gCRIycGOfjIPu+OlBecJ3AvIgRA5fKJ9MF\\/W+Foekn2iJkxKjMp5iD+RhUk8iT0e24FAg48GSIIM7ckkEnKf3g\\/gN4KlEAAkkkDEDgn+0HH4kA5PRgMApEqMjJAggZ2gSOSTxj44AJjTywrUoiACCIvBAje3IjnywBQPIn0nmDIjbzmZ25m+F+48gyAM8DiDg7ckpIkjkjiD0ckSOQc5ViOJ+DzBAEnERwZRIWOCQY2gQIMeDJ4EBWQDmRMZKgEnbERKQJgnEzMZxjMxE8zJXtuJV4hOwETNyAR7oiN\\/ljY8MJO9\\/jO199z+2DoAgTGCDB5CSDyJjyB4MiOSesgTG4RgRgic8Yk8QSfMR++EpxzJJGZkT\\/mOCcgwP6oBB4I6HMAQRjEyZxkCcY8EyJJAgkiVA2kkA2jrJj9Z26DqMCBkQJ36dQIg7\\/XrgGIESCIP7xmCQN3gjJwJmZjoWZPtPPJMc7sE4kHzHMg+R15XEggcSBAJymJAOSciMASecSBRyOIggD8fsSZM+DIzH7mpkpi1jEnp1HmLR6YEmxBmAZG09Nh16X6+WBTOP2BnETEjOQAYMTgDzz0EmR8Rt4\\/qB4IyrIOeBMiCduczukgf1SD\\/AH\\/Vj3DIEAfMTmeil\\/5Zmf2H7T4kQTHgQc4lE8sCYmRvEcoMefpt88DUTsBzubwNt\\/KD9cgOOBMr5SkQTEkERGYlR2gZkjkjnpveWXFEmAn3BKYggZIJ+SYMGYjjGOj3ODMzJIBMQQFQT+qSEwJEjgcHpKrIz+5mASDE\\/mFf65gDPTY+6pxJbJISDMdYiJ6jr1B5YDEEFN\\/cdvgOYIPu54TTCsczBgGBySMzgkg5HBHzktUkiCCOJUop+JzzABM8QY8xJhExgmR42wB\\/b+3EmOJ6wtKQAAc7gcTlOSeAT8gclUgxPCEiRNxp38tjeP0PocDKoMfPeNptY7Qff5Ri+qXu7oprsPce0TvZzRdVqys1BSX9jvRULug7gUyW1IFRY2z9yLOmxfbtMUjVM3RIUpwv3B5xypWlKaAUBMzEpEZgk4BHME45GCTP46GEKMGYEGBJBVII\\/YEz4gEDJmIyUgzkHwRE5IEiFZ+Jj\\/Mcc9K6msdqwz3un7lpLTelITCABa3MxJvEydzhBSUNNRKqPZ0rSauoXUv94687L7gQFKSHVqDaCEABpoIbFylAkyTt4nn8ZIGMSfjmBxMjkAhgxMA+DBJzPyTM8QcyDnz0bBgSY88xmYzAyQeBBwD5wcQZx+AcTySfBJhRk\\/iIPPSIJAEHnEnkb7b+diOfnbDjgqD\\/AJfE54\\/OMD4GJMwRgDoI4M4+TMgf0mR5EyCJ\\/J+ejgkSP0\\/gDHOeMg5ISIJnIziQlMjBIIM+QSY9pMEiQT+mMz4GCG0QZm19omIBvfr5DpBjRP533kC3S5+HMTbcAk\\/BnOeBBgAHzmAME4iJ5yQTjgYzkfsSfEgkQCcH8iRZzgQIGQT555J4MkeBMx0EgSYkyNxERPMEDgAGBuM\\/3joJBSQDzHn+2NarxB5e6evT8vPeAmBJAiMyTkccZznB4KucQZlOhNC6r7maw09oPQ9ofvmqdU3Fq12a2trbYQt5wKceqa2tfUimttqt9K3UXG7XOtcborZbKWruFY43TUzhEVIn9KVLJ2oShKVLcWtRCUpQhAKnFqJCAEJK1qISlO4gHcXVfrfS729uHayiUmm+oTutp6nT3pu1O8n7\\/tB22vTLVdb+yFtqGYXQa51pRLpLx3fqWnRVWiyLtmgyUOVF\\/bBrTQXqcWfum4KgCApaiQENp1FMLJmSbJSlSvEQElnzfM3aP2WhoW0P5xminGsvZcBUywhoIVVZnWhJC\\/4flyFpW9oUhVQ+unoWlpfq2sQ3u3rPTOlNON\\/T52hu7F30LZboxdO5fcO3JcZ\\/8ce5Ft3NfxdhTqUVH\\/hhol5yptfbSzvbWq+KzW9dT\\/xG8UootckxEGCf3jJmABxkHJBIxzwThDQSkBMJAAAAAAAGAI+BAgIEBI4Ex0KADiZOTGTgiZOAYJ8cSZJz0J1SnFFRAEgBKUWShKYCUpFj4REXuolRuSSry2gay6mSw244+4pSnqqrfVqqa2rdIU\\/VVCrDvHVWS2gJap2UtU1OhqnZabRg+CQABAAGCMiRJOTmSQRjwNwJwRJ5ySD8gZyRIPwAn8xJIyRbc5BnjBHA5jEAQB8qifEyISTHgT4+II8\\/jngfA6JUm3K3Sd0kD846c5wvHn8OnlgBmR\\/SZ84JwM4kTMgA8+746CAABiZA8f2\\/TkQQJGc5ODHRoE8ATjcqJII4BMnHzOPE+ejUsoLZdK0BSfTCWVBZU7v3bthQktoS2ke5TjiSoKSlAJJT1gTM+QHxPxtO\\/wCnLYI89otzttf87+QwnAE8AnzAIHOTk5kEYg\\/uRHQQDzzExg55J8Rn\\/KByYBgGTomZCY4GP\\/bgGcfHIETtEDrxHAMg8EwcCZ5ng5IAM8YjPWFMbkenXb3z+492Tv6AHyAj06fW+CNpMnMEAAEf2kcyZPn44IgDxTPjiY\\/ccSIz8YgEnkK6P2j5A+M7Z455M+BkAnyTnrG2TyT4MDn4MAyJMCSccRxIcbBFrkefnaPQC\\/utzsnKf7SME45ED9WYk5OPicz0IAHGP3M5BkGJmRiSYkeejij\\/ALT+8EHgYPOTP5gdY2wf+xglRgkZgjPOIzkEzBOY3qAMjrz5WEkwNz1nrbCcpyJUTgcwcTtMAgZ8GCf+uRBPHyMRhP8A0+ZPwCeJOT0ZtJ4HgTjiSRAEHnByJBPQoA5H58nn9xEGMknEzHB60Ug7jAZvPUzv5\\/EYK9vOfMgc4AIgEkfvkESBwevETBzgEAZMmE+CJmJiMHjyCTIVHHA+cCI4nbIkx\\/USRBBx1gpjJ\\/I5IBIjEFWBOQZwOQY6ElA3sDy2nYWBmb25HzwaCIGw26c4JA+t\\/dgsiRJGMHExOR55iYjyAfnr0FUT+BBOVeQYzIJge4mBzAz0Zt4jJMYjKRyIG7weIBJMTM9YKZ\\/HJBM\\/qjkQfkhO0yoZzCusLZNrRsfltffn08uuwQCLxe3LaLfXI7EYCAd0gwk+IIkgyJI5mZAJmQDPjo4D4HIxugmSQYJkEHB8RJnk5LgnJ3HiQMeTOCIEeOZAmDM9GoTABwPbMGciUxzMQRlAyOPjogi5kXSfFJMSY5Wj8\\/O2D2ryOux5WjePLyvvjwBjMj9Oc4OQAeCc4Ijk4nrIAP5EQCQOAUgTBPtjzIVGSCM9ZMfgCZ4AyAP0mTAJO4SJn9wesxBOZ90GYzxklJE8fmcyOeg4MKSI6nb5fvj0Tk8wMR4JM88QTJXBPPJ4PbB3JmIIPtmJGFQEkHzKfmTPyOikgSRB4PwBBIiPkEzJnM+CZ6UtCc+0EKMgbYJBJkicgY8nAIkeRoTKgNvnzAPuM4wiATNhE2giw5e\\/5HDm3CUk42wCfAkfIH7nGZk\\/A6IUPUdaAG6UhRIGBBngkE5+CBkfME4xsVPMQkYj3QBtB+eSScTiRHR1OkYcMpKU7fAnAOREj3DknwIJyS9NN6+7RM+IKI\\/7Qegm2209bjCMqKAV9LD\\/AOQ\\/cdcIKkS8oCQlJDaZ\\/q9MD\\/KYkmYxJBzAkdFNJ5EYO4iEjCZAPHmOJIjiI6NUkb1EED3KVJA3GTgkZESTGeSRyQTlKYk44IxMAkfPHgx5OBgDpE4SpwkmCVHlEAFIjy23t054VNgaEjolJNwdgJ8juTz\\/ADncT6YHEKVfrO4lSRqRNLa2nEocXC1OodqQvaDtDdubeUTP9SyogbEnspdNW03ZX6ddd9wyWWK6ktNWnT6VJALt4qSnT2lqZKCrc6E16m6taUlR+1Q46Nw3AcvvowsL1cy5Wlokfe1lNSKUgpCquoTQ0XqgyApNJSrqwPb+pVQcBtRN0f4hWvat5Pa36d9KOLrKw\\/Y6mu9DTH3VV2urirHou1uemSoOFLlxuCmFoBm5UDyRCWyOt+Dqmp4b7N3M\\/UqHxlzdFkyQg96qrrFuCmKADKlIfq6pxIABU2BbfHmh21ZLRdqn2iuFOzZCAcraz1ziPjh1awadvIOG6elq80aeK9KGWq9mhy3LQ4SUoqahWqw0lH9BPb1blPrTu5c0OPVV2qVaTsT9R7luUtO61dNS3FtSpKjWXVVtoS7yXLZVoAgq66QVNjZvdsudkeQlxm82uvtLqVAKT6NzpH6BxKk7V7gpFQoKwqUg4MmIf2t0HR9udBaT0RRbS3pyzUtA++gJCa24e+pu1dAG2bjdqiurSYgJqG0e1KQnq16FCkONkJhSXEqmAE+w7wOAEkqHEyB7fJBufhDIhw7wtl2TOAKfFMXcxWZV3tfXEP16iSZcHfOLaSVX7ttAJtGOKu23tKc497UuJOL6Nw+xpzIU2QCdIayfJlCmyjQ0mzXessprXEAR31U6UgTin+x1U5c+znbR9\\/cupY0fZ7VV7wQoVtgpjp6sQsQFJWirtT4Ij9cgwRAttpgkgkE4xgxyoRBknnkeYBIM9Vr2epUW23690smUp0l3X7hW1hspKUpt15vX+2lpCEzAaVQ6saDIAQPSSAAQkFNzMs54gZkxEwTMRxEg5Px5J6kOTqK8qoCSSpFKwy4TuHWGww8Dc3S604k3O2+Idx24hririFxr\\/wAvVZrWZhSdDR5o6Myo1JFgEqpqtpaRYQRG0YJZpzAIAGOQCmZCgYxtxwJHjz5cW6c+EBJ8CDwTMRtjwPaCIAx8dKWWBtGQBIwd0SPBIJyDGP6ROYMdOLdPxjAIIwRkAkf088R4EyeOlThIKo8vQ7b\\/AFOK8eqSefS9vLp6Xg4StU0yIInBGYiAMCBECIkz4+R1FO6\\/31B2g7kV1tpV1dxOmbvabNRsLQ3VXC83K01JboaZx0pbbq36WGmVuOobQ44FLG0GbJZp1rKUpQXFLWlCUbSS44ohCEJwSVKWoJAgbpPMR1Lu4naa9al7dVenLC7R\\/wAb+2qH21VjzlPSVN3qAlJ3PJZeW20htS6dtwtHYwlCSgTuTHM9UtVBV07SnEPPUVWhC2kpW6kuU62kd2FkI7zWsKb1AplEEESMO3BXEmSZPx5wM\\/nldRUFCni\\/h6oqn8wUtNBT0NFmtHUVtTXqQlbgo2mkAPlCS53al92AtN+Ef0c\\/T1d+\\/f1E21vVOnrnQaG7R3BvVGuKO8W5+hRVXmiqFDS+lqimqENK3VlwZTXVTSklx212+qJATUhQ7C\\/Xf3ROie01Jou3VKWbz3GqnbcsNrCHmdNW37d68PQkyhqsdcpbcmDCkuPiVQQbl+mXsIx2H0AdNP11PdL7ervWal1TeUNBlqqvNwS2KhCFrHruUdvYaQw288St9CFVDqWCtTQ46\\/VV3XV3d7y6iu9HUqe0zp9atKaTSCfRFrtT7rb9ckJlAN0uBqq5ahlxC2QfagKHJnFpPZj2dLycVK3OI+K3HBVVC9IfQ0pCUPNAtgJLdDQ6aVJRoSKqpeebA7wge2v2PqP\\/APjr+3wx2lPITmvYt9m3LKbOMneLD6cszTP23inh1xLNSlC23s44nTU8QttPNl13JeGGaWonSFL12Sjd7gP8qf0gGAYgDJI\\/YgjJB+MkfPJAk5yZHEzMEcQTGSqT0LkSckAyRiM5\\/pkY4hMj5gx0XMeDBHmPgggSR+AIJ4yfHXKCzJiTO55CT1\\/OeX5fTYINwAOQjoLelzPr74wHaIIIxAjj8xk\\/if7456EIHgeUwAJ8QQByMDJJ+P38AP8A4ocgZM54xjiZkkAiRBzggQPgwrwJjPukEK5xBIwOY6KO3y+Nuf0cGeIDyid+pGw+G\\/n1x7xgjGTyVA4O2ZIGVcjj4PHWeYk4O3gEEnEk+T5mQBI6wFAEY93AAj5OSRGPblJz\\/pPWQYBxMxgTOTjyf2MSASPBwUoQY+v7fIXA54Bv+vLHjA54jB8nI8qBkn8EA4zA6ziB+kR4MeJMDAkE4wT5MkE9BzyPzInxgxHuEDyRPnHPWSQPIgwAAUjMkfHieRAgdBxoj3mCN43i+310wGAT88ATEiYwcx5+c\\/nyBQg5xwJKeVEnI25J+CJAGDzHQvmTBx7TgiAJxGefGVSPByA5MARyRIBkYGJHzODGTJgHoCrbGCSPfEc+Ub\\/RwUR5HlyIGw\\/WcBI5nKRjdEDMcbpH6SBP7eJ6BtwfJVmf9RnAJ4AAGMnxyZMEmBiQCnEkGQMHIUPJMSP9SyolJJxIkSOCZgeYKpgQQTggwc4AQALHz98xEX+XSxg4KUCbT0B8z6AEjlO3wvgCjB4MDn++2PChOBjgczM9FmSAZiSJnd+QZIGT4TECR\\/bo3BzJn8FXA2jAOSJBHuyY4PkswcYgg\\/ByPkZBycEY4iOOhpJHw5crD5Tf5YIXq5SI3I3HlBBHvg88EZg5yYgkEzgYiT5T7RxHJmJwTEDgiAPA8EQAZ5mc5546GeM4jHnGf3MSBnnkETJ6AQTPgZBJMmODzBgD8HmDMZEpWoC3nP7e7442NgDvF\\/l8fPl8cFK8ROeeMCFQDn2844J4kg9BUkEcDBJBIgESJ5McCRnATOZ6NIg8gA8SBkkzIABziATERkkHJZ4BMDgnjMARkpzk\\/twOT0UqYBmOtusbjlt+nPBJibGfr0H18AnI+c4zOZJgcAATKp88z+6dRT+AonB4ggnMFIBJI8AjAjJjpSoBWCCORmEzOAr3SZBAHnMCCc9EKGTAkE\\/ETtg4jmZJz+3I6ECCQJHnB22+PPp54TrEAkAjcbx6EH0uevK0wnUZzAEGQYIBIxkfJj4+MAx0STPiAozmSBhIiACTEwASTj\\/VQsfjkkiY+YxxyCDg8+RMdJV\\/kTkT5VPtBkmRAOCMcHJE9GJ6CJ3ny8P5Hra3mcInJJn3e\\/mel\\/ngozMzuMmIBwQVePbBk4SPj4z1G7\\/dxb6fY0tJqHUlLQJPtTBCnVeIHCT8yoSJl4uFazQ0ztQ8rahAgIkhS3CkhDaJGVKPmYSAT46qCsrHa6odqXzKlkhKQSQhJ\\/S0mCAAk4OJknd7pldTMhRKz+EX9VW\\/KfSZjEG4tz\\/2Fj+H0iv9bUo8SkkamGDYri0LXdLY5CVbCcJCoqJUokqWSSqZlSsFSiSRkZxyPg9GJScAEYPumTgAESc8eMp\\/pzk9eAkzx+8cEScRg+AROfyTJgO0FUyBJP5UQcfBiADJjnAx04CSbW3I25X9\\/wBTisGGQmyrzckkn3k+pufneMZVDackwZ5JJAzPnJ4x8YEjPTY6surkcAEAZKYPP4wTB4wYBJMdG1DvqHak4HP+UkK8icwTEiARGZMdEgTHzInyZnzkSeTMc\\/sCRExcESBB5Gbecm3O+025FPuBZKEjwJkGRAURHIi4sIM36YwkAftjIjGE+EkiMwJIEwOjBGB4mQM+7OB8E4PwByTmD6MzIn4MwPEEckzAAnif268ARkiZnbjnxImREyUpJSTM4zIQJn5fET8BfAG2yoiB6D0jy23nAgcePAECJMQMZOZ5mSBJ4PTXdbs1bWFLTtW8pKvRaJAClgEAkke1tJyrkGQQfhRW1bVDTreeMgTCQYUtcQEI8AiM+ODgdR6w6euetbmokKbpkLSaqo2kop2N0+k3iFPKB2p8lRKlRkjNQQtKR4iuBFuo6kc7jyuJwXUvVPtFNlOVtLqM3r1IbYabSFFoKUPvXBcIQm5JUQEpBUqBJNGJkkwMnMiT+AckE7SJOD8ZHRwG2B+oqEySQAmTn4ERIEEgkmfJLTnzlOAQkQcAf5QZPx7Y5mc9GpHxxnBJMHdnnEgYzJ8RB6MSbCxvPIQI+ud5nHGyLwIi45zaAbTtbljwyDBknwTJ3cjBxxgxiBAOY6ycgc7RxySARn9WATIG0Z85nAkp5xkEEDBk84BjOYJGARjHWQnngQQIAiMpMGQQcHncMx\\/c5IBOx5fmPle\\/QYUBAt+Z5TA5WtabTPnjKeM+ZAgn3ZkAAiCMwSCOMAQOsgCP1TkfIJIAAwfdIIEeCDEgz1k\\/PMzAxJwTMSciRJKsYMfOczODgiIGZPBwSQYjccHzmE9bvcxA6e8c+VvQCOUTgUcvT5xHLz\\/bz8PggAZI4x\\/T\\/mwBkED5BmTJEBgSciDEjyoQNpMAc7VEztiPk4HwDH4wkGc88qIJAGNpgxMYGI\\/SDxnOREpEyZxOUgCMxzyE3i0E9BAg2G\\/9jPI7jCCN4j3nboPPa+MgeYgCDESBx4AG4pE5E4IkRjoQEYJkyOBJkeYVB8cDAJEdYGQCRPtByQJGU4BwAeCZHIBTwehgETj94zBBA4B8QMgyQfOehIG\\/UQI98\\/p09\\/TakiBG37kDz39DtjMgcciJifP6QCE8K5PABnOY6G0AW3lgQVbW08SS57jHgyCYBMwJyI6L2kgFRGSkjAAlRPgx+JkKiOedwADCWwSAIVtxG6CAVAkZAH6iAIgAeSE6tRPpEbWgG5jmYPQc5k4GlsKSUyR4gTBm1j0G8bYNlMDb4ATumYiAqYMkAAEAJAAPJz1mCAEg8EYBOQIIwFZJEiIEkADI6DgGI8BOAmRhPBycQCSJ+fHQhgGSPjMHIJIJgf2GZMhQgE9YJgTyG0+hsJkzaxtykHbQEWN4sCdyBzO3OeQxjnIxifGBBVPmASJnJEAZOeh7QQQY8x4Bk+04kqyFCIE\\/nEY4MmJTzt8nmQCRJKiQCBGCOIkYTgkCJJkZEeSeVTtxwQSQZ89aTEyeQm2\\/QR7zjZv+nr9WO2+AxBMAiMRBBIOZEHyeNwn2mZx0L9MJ5kECB8KySYSfyUgkkkwfPQgn3EApJMCYEyQSE8Az4AHIVzJJOSAoj2iJIhQAgkGIE+ZMDyRAmDO9ovvBmBbaLn32nl7sAjVeIgwZ35n68+fXEDkHKjHyRuIJk7SQZx5IJgYz1jaJBiSMyQMTAEAjEHI\\/bbGJ6MiZBGOSecmDj9JMwTngSTz0EgESATI4A\\/sPBI5wQDzk4A6wDc\\/kRABNxvO0\\/nJBnBiU9PIz7gI939SY2LUncMiCYzABJGIUJzPPtg8kxOQ7dvBJHgzEQMZIAJgyAcZkfHR8CSYgYBgGTmYIPMmDukCfggbgkYOZmD7jkwBIKSYwMc8Dnk9CAJFiZ3IuBygTbl5+mNhETeb3i8GwPn1OIncqUU9QXUgBmpUpaZMbKnJdQce1Lgl1CQI3B1JgAAw2rZVT1JUn2IcVvacmFJcHMHBnd7k5hMpgxHVp1FO3UNuMvfoWORIUhSTKXEGT+kkEAySNwVKSR1Bq6lKg\\/Sve15ogTuxuj2OJAIJQtJCgkxKTkBQjpE8hRGlNz+JB5pMiRM+8bG5G4wkqWlABSTAtHUHmDJ53G3PcxgykfautM5SvFKagBbrDivapt0EKeGAEllaiH0kRsUt5tQwhRj5QpC1pUFNqStSVpWCSlaP1JmYJkmORwBPPQKZ99h5PuKXWlgoBAhK0SFIUSCQCCptQUBKTETnp1rA2+G6tpJQl4hLjY9wacRAImJyfkkKG0gyroZc9qp0lQHtNMAlZFtdPYCReVNq3JuUkXgYagO7ekWbdMqSI8DpgqNuSxeDcKBjcDDVB5BEQZkk4MfIk54+DJMqHXs7kqEzgyJISTM4PwTJPlJOOJOMTicEgifzPkTE5AwSf9egxHwRzkTMyPBBk4\\/JnHgdJDBG+8QfPlH54UqACd4IMDoSQPIgEn16XAGCgrcSVbgdwj2gFUj2\\/jmecZgHPR7alMuJWmRHu\\/B8KSSMEc5UTIPBHOXGNqUqSFrQoJMg7gCSBnCR4wZIgx+4kguJKVEyAQCSAmMkbo+DMmDzOQOtBKkLhVl2UOpFjYbEben5EKCVeBWxBB32IG5PmDccwPPEgaUHEBQMgiUkkAxAOYkZVztMySQTHuP5Aj3AYB5GI8kmJjJ4giQTI6aKB4SWlEAgHYBOFRCk8qJn3Hjgzx08Jgx4BJiYJIIiBtHKvBnwRPgvLKg6hJmDEK\\/8AcImAI5XnnJ6GIpWU5p3lIAEbo80n5\\/lf0wGDnEyZiMAz\\/VABPgFMQZA\\/PQokmT88TEmTnMgiDEDzmfIuZOI5nJAkZA93yDBB+R4kiiYKcmBnODgwnzz55zBkdCIghM3PL4bfKf1wkBJEkXE7c\\/Tf6GAAAgRk4IJmYJiBGZwJ5VuBEfIoIODJMyR8wfxMgcpH9JxEz1mDx+5MCDKfnxJz5mDHz0YBBE5B3CPdkyPbGZI9sGTJkExyLRtKt\\/hyAg+XIc77YwGevPzg2tYbXtP9iylWYA4yBMQcgDwYwCTxiQQesBPP6cHyARz\\/AHABzISPEiQI6P5HB4EDyQYMDMifIyITEE468AYAzwATkAQARM5OZgwBJhUGOhFH1by3iCbT9XOGBaOn5gfqPXngsJiSZV5GCfzt8TA4HwcH5xBJAETAG3jhRAIB4BUeBmQIBkDo4gkATk5EEzyDkAkx4BBE4A8zj9gYOYxJE5MTGZMp54kj29bCYEAwY3+v7ibEYCTtG1uXmCB08xfytOARkg4g5iDkHnAKiEzA4g8mTIFGJBEZB9sEEQeNuIJiRn8naSfR+xEYMgwIE8meNuCojJPyOhAQYAJn4MQN0zwfJjaZ5AyD1sIsL7ctp\\/D5Rvc3n3YBPl+\\/Lbfpb34xB8YjgSE+fMGJMY5SfjwcDn4BjzGRJAAkjIMxIIP4joY5g\\/vAwDhR4zM\\/0kc4MZkhAOAMgAAiScfiAJBwATkkGRmOhgE26xJ9YPWOVuuNYzHHjB+AcAJMpUTwY4zBM5InIBMggA\\/A8mVJA4B\\/pGRicjM7hCTxg5wDBMCQQQCSR5nBgYgR16Jj24xAEYjJEKEqHJiAPcIPJ6zYGxBsbnbY2v5DbaN+Q0RPp8+R69R77bYCBHnB+cwJBkhREgGSUhOCTByB0Lz4k85ESJOfaQn+kD5Hz0IpI8QcQMiYI2kQSCI2pJPABJmc+M5gkyP0wonGBEyDOPdg\\/jGdQN5nrbnb99+oxvAYJMCBjjMAA7edo3AYIByDJmT1mJmEjkjCYMGMRtxImAcGZJB6yR59oEkEbTkkxIAngn9QzBMyehxPMj\\/WJ9uOQT+I\\/wBPPQkpBmZ\\/T49b\\/P1jUAfl+X7DAADnBAgcSMwYhJ8cgmcnk9YgnwkHyJ3DMEQPgeSYjwYI6OABM5OY4lRBxInM+eZBkkZnoMeMnyOSRHJ8RH4J+YOZEAJ2NhvBF7R7+trnG\\/XAABnJEnnyOAccgSchIwODkSJI44Pt4ySfgjcceBjOTiJ6GYzO0TEQBODx8E45mYPPyIJnwQQDyCfABOSR+AU\\/MgZnrZB+ERJn4iL\\/AB9IxmCwnJBAOYxJweRtmPyfHunMdCAM4EkiIkwSASCDHB49oJBkTMyIick+ZyCNwBEJJOQZ\\/wC2eJ6EYBwJJEzHwRxkbiJOSPJkzyK3vE\\/OPn+3xASQY67bmNhe\\/wBHncwVtkYxjkk5iPMAlQMxiIH56yE\\/P58GFCRAAUST5GYOY8dDCRHjImJyY4EAgRORnHEc9Cj5ECRI4\\/Y7pgkj3DB8+ZncE8v3+GBTFrnlJ9Y3Hw9ReJBIIM8Af2PgAQBkKInmZ5BjbBEM8YkHHE\\/ODIzJ2ieeeR1kYI5OAoRnjOOFD8QTAmYkdejmCeRIGADHBnESPCtxiOJkOkn\\/ANojblBBv+QuPK8xhI26wRvuCIv62+hjEZwJMiCeCZiduNv6QEgZ+QDEjI5nAEZhWIxzIJkSSORPQgAclUCZBGJMgZzBOT5+ASB1kCQCQT7eDIwPgEHmY9p8wTB6GEzMEW8xbbfpYz7sBMCNwdrXI2MefT37G8FlPMjIyTJBEeYgx5AgzBgZx16IHg+AACeSZmcjEYJEfHHRm0DJyAcjdGZ8GB8RmTHODPWNp\\/EERGcAyTuBAEjGACZnxnowJEA7yBvPQW3jzjzHTGlHcRtBI3HKxta3Qxy54ABj\\/wCx9vnkmIk5A5jMkkDPQggD2xkkZmfgyIAKhgwM5MRMzkCZJkEAeDGMwQQAMk+2ccGQRJkHjMjxB\\/pjgfOOQQc\\/mOhJAVBFpjzNyJNres3jnONFRAgxynqLA7+R5yfcMF7BkGfnAIJx\\/UI4GScxEfnrG3OSQc4E8DMe4R+xImB5PJwyYOMkJ8Zg7QAqRkGAQc8EmD1mD5BBBA4Jj3eAUkEnJJxOJg5OFJBInkOloHoJn08uQxoKMGDIt7rcvLryvaJGCdvkiAARxJgeBE4zg8Azxgn22ADwRyRyQJn5IMnCvI889GwYGSSABlJAAI\\/MYJAEJJGRu8EiCQIKR+oeBnkSJR4Mk87h\\/r1rwm43O5Bjpby2\\/bz0VKO5taI\\/Xn5zJvy54I2JVIUNyIIKVTtIIKVBQ\\/TtKclIyoQASDm\\/NK9xdP6u09bu1vfJ+4VOlbTT\\/Zdue6FHTLuut+ziiofb270kk1utO0jjoLl20A\\/UiusSVPXfQdZb65NRaLpRUSBnwJgHJiM+ZBIjcCOBJz1mEyTHzgzuMSI44PBAnMHx0JsqbJKNJBTocQsS262SnUhxMglJIBlJC0KAW2pK0hWE1VSs1iEpe1pW0sO09Qyru6mkeSQUvUzoB7tYjSoEKaebKmH23WFrbVLO4fbjVHa+\\/t2HU7NC63cbfT3zTOpbHVpuuktcaXripNt1bo2+toap73YbiEuIZqUIaq6OqZftV2o7dd6Ost7EKAidu4q8zGRkkZxkxuAEgyOMdX5217n2Wj0+\\/wBpu7tsuGquy92rqm4UYtaKZ7W3aDU9dtS93D7U1FYpDSKh9SUK1loKrfY0z3DoEbKwUGoqa2XuniXdPtVeu1l1taKi52vVWjdW2837t13I0yp17SWv9OKdLH8RtTtQlFRb7vQPD7HU2lLmmmv+lbwh213ekSftqmr2pCYK2ge75oV4lsqOmyykEqR4vu3LBYMEJVulpa15t9OXZnoTWlKlUtShJRSZq00lJccaSZDFaiSarLyoqQEqqKZT1MVKZrA5HMRyDAB9xkAZKYUQMcCIg8hUJiczhXHOCROZjAyIVI6MOONxInJ+I4+JEAGciSSJ5wRHnOCf7RIOB8gAcASPEdBj6\\/XDqDBiDM8+pgEHymT8MBTJkgYnyMTBB3CYnJEEyQY+JxtiOY4OcckwSCcScYgweUyejU8j\\/rJATmZ\\/905gk5MY+RgSOMcSfcI5+cEcY44GOsgxPz8\\/r5eWNkkGY\\/FfzIgCJ5fv8yI8zzgAHJBiM4kmBkzBjGM+SmYIABic8k8ZP7kCRnGRjJ+3JgfP5nMEgecqHJO6JA4HWAAT5GYxwSM8gjgyCSBBAAng7ja9jz+E28p2xoKIAESdh6W5ACZi31IIwDHxznAIGZgg\\/JIkKgT5OQBnJIxIB8\\/hPwccHHg5yYB88+ORMjkQTuJGQZj8jPXsTHMgTgGOCcn\\/AEKeYIgxzhERBk2ny2v87Gb9MaBHOw9\\/UW5nl\\/XkQ\\/BiIBGeY8kZJHMDMR5Ez1nyT45zOTJzwZUYH4z5OSIJJwQCZMGTPGRtg8DEeZ8xkQgK4GRBTk\\/kyIjnOZMSDJGc0gpkWIuY3sRG0Hab+uMBIidvQXFp9doM+hwADEzKsbYkTIBAyD+JMyZPEdDSkpUBgwU\\/k8kHBBgyYIMwYmAJ6zEfp\\/6z\\/wAwERwTxmCDicx05V9xfuf2AeYoGv4dbKS1M\\/YUFLQl9ijcqFt1VcaZps11ydNS4mqudUXayqQ1ToW7sYaQgxEXjyuRuLTe5BG8CZk2G4yTP4UkSJMjwi3kbXg7XJI2OEEbjISYgyDhXg+PBmDxOTPgCiMHIJMEcSfjkGZg8E4HWIx\\/cRIOBiDgcCfMH+2esg8xnPx+BgjzmCIGYzM5HgOBzIOPke6ZI\\/MxBmYAyfwACB8AD4G0c\\/MADgf9cTPGSXPnwBBmAcADImYzBjjkxicyeAQJBgZJjjAIg5zuPzBEAzmMwpaWlKk+ruCcSUkBcQYKdxgiY3YhSRgyeloW24oqaQG0qA2oQSpIIAECSTk7uc5iIE9NAUVYBBwSkTBgDE45ykgcRnkHo9pxTfJBBUFQcK42ggxzg\\/v\\/AGye28UKE7G28C\\/Mjy3Pp78aInmfj5g\\/Hz\\/PDuDPGAIg7YESCRJMwCQAf\\/dHmDOTODjBHgTmIzzI5BjEEjKdtKyjdtVtlJkpBJAEA4z+kyFcRzBmVLbm1YWEIWEGSl3LaiCf1jhQxJEmYG4KEjp1bVqAKjANxO4293TnPrgIJ0mBJFhcXj66\\/nbBAAJVOBnIPAOPIOOBxH+hJJJkiTtBlOCTA4ESIkGDMxxjo4qj3EkiRIAEEkifaQEiSQOOJ\\/J6GhlbyVqTsIQCpSCsJXGCSEwEqEECSTkkSSAno1KxBSIJ5+lrfR52waCQkHqNpBAmCR+5Fr4TAnnjI5mTE5IxIgiRG0EmSZyUs5gZ8AmMkg8YOZO0EgCOjVEDzggkCTifcYGZknB\\/qGIMEElfGADiBAnzBImCRkxM5JHE9JXtgQDsSR0gDfeNwOg9+MsVQLAxtECwty+vPCZeSB8AjECII4EiCPEFWM8T0QsGVSBEmeOMCTKjuETmDJBJEQOlCieQYM7YJiRMgQRg4jnB8xEp3AD+PIMJwIgHP5k4AIBJ5wWpYIMm1zuLjmZ\\/eT57RjdhE2BBBN+s8rb7RPnO2CCBzjAmZBGBOACJk\\/HBmDGOiwJJkZwPOTPIAg4nByACOMdHH9QnBE\\/8s4GeSBOBI5BggSZLgCYUYngA4HzgACODEyQMzgklKZ1Db+a5jkBaY3tPlJ89kWvEAC9z0t5DeY6jGIIAIBAwcgcADAHgZgCI8yOsFAA8qUT+8niSMCfAGRtyBA6GByISABMjwAARMARGBBwJPWSJzwMfAJzjgGJJIHwcTxOKTEQB6esDc7322N\\/gCdptE3ET5dNjgopj2nMfjPtiJ5M5jJ\\/PPIIjPt\\/sATnJyInwDGRyQZybABkCeQo8pGSTujMDPGIzglR6AR4mPO2fOD8Gc+DkiBkCTmkSBt4ZMddueM1H1tANrbfM\\/RwGDuMAmJ8YPMECYjgTIHIIMZCR+ZBI+QYkAgxIH5gZwRAKuhxPjH5mSOAScziScRMEyefAGczJMxMmZJxMcQYPgEGT52EDz2+fM\\/02GNajPQxFvKNuvmcFlM8EEgSZ3EhIImMDn\\/WOcz0EIkAAA4zABA+Zydx\\/+eT+ejiJAMYBwB7QTPk4HBBIkyPMkTcHZbtaz3Q1RXov92c0t240VY6vXfdrW6Wg6NI9v7Q9TsVr9vYcITW6ov8AXVNFpbRFoBDt41ZeLZSoH27dWtostgqCUmZUkXNgZCRq2GnqZsN8E1dbT5fSVFbVr0U9M2XHFQVLVdIQ20kAqcecWUtssoCnHXVobbSpagMWh2atlF2S0QPqh1bQUdXqN263DTf0z6SuzLdRT6g7h2lKBe+7F1trqHBWaL7QOVFO\\/QMvhFHqDuGu22sqeatNwCdWq64XG8V9wu94uFbd7vdq6tul1utyqHKu4XS53J9ysuFwr6p0l2qra6reeqKp5wlbjri1qIKo6snvF3UrO8Gsv48izt6V0jYLTb9GdstA0b\\/r23t7250+lxrTemKV2Epqq4IW9ddTXn00v6h1PcLreakFdUhKKtKQcRJM8wcyCTjzkcCZkZEdGuFJSlCAUobEJ38bhA1vRsCojwyCUthKZnUS15JQVCF1ObZmNOb5poLrJUlYyugavRZO0pJUnTTIWp6scbUW6jMnql1JUx7OhssxM5\\/+KwcZx53ST8HHzz6OJxBjESZwJE4AIzKgTIBkxJkAYIMjEf8AQAkjEf5vIIkg4OQk8TjGCfAxmJn+nIk+eicP5V+h9djaY67+8eRQQf2\\/cHAERA+JgD4JJE56Htg4kkcxnwCfySZnJEiBkySaEmCAD8zn4ETHz4IiczGCfEZGCJkyE8ZIgTBKo4Gck8mesifS0+XTGio2N\\/32Pn8Jn43Kge2Y8AAAcczxjk+3PlIkR1nGDO6IHn++CcHI93zAHAkYT\\/fjkT5GTJ+IE4HPgnrwHmRnkxkfqO2IIIUQCSSRI4+daSNk7x5c+dvPe5k7Y2FRvvPQW28\\/679MAKPxjMiAcGMgmcDgSBySSBnrBTyQBBInA8A\\/AgHAEk42z+CbBBwZ+PnPJIEZEQD5+CAes7QCEwSQDiCQcHaJiT5wOOJwZzRcbi4IHIbDptYY1r9wmevu9T19MFx+eVQD7vBHPtPkEn4+IJ69HJEYEkHMwZiODtwMggCQZiOjthyrbiMzyCCnE8c4IHIyPyNuneeKwy046W23XVJQ2pRQ00guuukJSSlDbYUtale1CZUrHWFsAi8i+xkWAkdfq8Y2HBcmEgRckAdNzA+rYSkYJ9sREjMRJMAmPP6pnBxI6AUk+QMp4BzA8ESRBOODnndJ6cnrfWtUTFycpahu3VNZXW6lr1MuppKmvtrFHUXGjpn1pDT1Tb2rlbV1jbZWumTcKP10pNSyFIoORtj2\\/wBicftgCZiYgxz1hQIg3EC\\/KDG0jn+h8zgchVxHQxFiLEWJuDIPnguB5A8RE5PjBVEHJJJlOIkjOCMRE5EcyBGf1HiJnkSSRkdDIMSYmTjETyJBiMef1ZOYHXucRPjGZncBA92M8ciDgiOghMJImbGOXu3j6PXG5t8f0jlHKfOL8sA2ggkxzzjn8knOVRiBkHnnG3J4+MAcSOcnEQQSY\\/GJJmQB+IP5J\\/aBJmAc+BmCFHB8nmJxwYgGIPPBG6QofsMiCfDJ2tN7SABG\\/X3G3ljCf0\\/ID5wMAGDMg8Hg8g554AO0GDAnrxHnOSkYSRyBI\\/6iDMqiCcSRx+PzAyfBJyT+wOZ4PXsE5A4nAHIgjyf9YiJn8bxuR57AHbYRyIPSeXyuWInGM4BAySRJAyCCo+RuHPmQa2ASQBtGI\\/fj5IJySEkcjBJjrA8n9OY4EfERgHkyYOB8ROUjmMiCeJ8mCJg+RBEQOQDB6TqSNyCJJ6+UeZvF\\/LChskFPoBPmY8wCOpmOXpnJGMGQfKZKTAkpODMmOTj5ziMkgzHAJIkg4HumOckfMQfJkGTHM\\/t85iZPJkHyJxJPQI4wYgE4mBjEfEiIzAMzyCDTIAJmOnnyHkPT0jbC0ADYR9D9h9TjyQTAkg4OCRkYzAJnAjgHbBT0uZTPE5SAPaMzKYycjMlJHODGekiZJjk\\/gECRP4jGMzP5lQ6cadorBwMKBUAM4IJSARtJUSThQOPPBMp0FS0wCYiQBO4Te5j9z05Fu2TIMbDnz9Om4+pVJG5BUqU+xQ5IgjBO3\\/3DyQPzIkhbWUNqGCElWyIO4ySMbsxuKoAkAwc8nqSfTnZEyACnkSQqDxIIH\\/YfkhABUrISAR8QZmCZx5kjnHJx0\\/ae70lIuEXECT5c4I2G43FpwgHikkyNQgWt+Hn6H9Y2wUkSrglUQSJO4R8GPwfOPBwejUIVuIjeSuAmZJlSYCUjJUVEAQQTuj9RAHm0ScyPjABP6QPaB+oE5PMgSY5ujsRooax7l2WmqGlO2uxhzUd2AEpUxayhdFTOxkitui6KnWkhSFNF3wlUEZXlz+a5jRZdTj7+vqmqZBAnSXFpCnDBB0tIJccIIKUIUTABIIzrNqbIMlzTO6w6aXKsvqa52bFaaZlTiW0GCC48tKWWxupxaUiVKGOo\\/wBK2kKfQukKCpvDjdO3ZLWm4Xd50JaRTVNRSu3a8OvLUAkfw9ipepluKmEtKSo8Aa\\/\\/AE8Utf', '2025-09-21 11:30:56');

-- --------------------------------------------------------

--
-- Table structure for table `recruitee_ratings`
--

CREATE TABLE `recruitee_ratings` (
  `recruitee_user_id` int(11) NOT NULL,
  `recruiter_user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recruitee_ratings`
--

INSERT INTO `recruitee_ratings` (`recruitee_user_id`, `recruiter_user_id`, `rating`, `created_at`) VALUES
(2, 1, 5, '2025-09-07 04:17:21'),
(2, 5, 5, '2025-09-11 08:57:12'),
(2, 7, 1, '2025-09-11 08:58:27'),
(4, 3, 4, '2025-09-10 09:17:24'),
(6, 1, 3, '2025-09-05 21:05:36');

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_ratings`
--

CREATE TABLE `recruiter_ratings` (
  `recruiter_user_id` int(11) NOT NULL,
  `recruitee_user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recruiter_ratings`
--

INSERT INTO `recruiter_ratings` (`recruiter_user_id`, `recruitee_user_id`, `rating`, `created_at`) VALUES
(1, 2, 2, '2025-09-07 04:46:52'),
(5, 2, 4, '2025-09-14 05:47:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_type` enum('recruitee','recruiter','admin') NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `headline` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `skills_cache` text DEFAULT NULL COMMENT 'A comma-separated list of top skills for fast searching',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `name`, `email`, `password_hash`, `profile_image`, `headline`, `location`, `phone`, `skills_cache`, `is_active`, `created_at`) VALUES
(1, 'recruiter', 'Leapfrog', 'test3@gmail.com', '$2y$10$ytmC6OtLJtD9cv4374eBoe0i2K3f57Czm0oZRD12amAHdRSNuraRa', 'uploads/avatars/68b27eec0a126-ChatGPT_Image_Apr_16__2025__10_18_14_PM-removebg-preview.png', 'test', 'butwal', '9840403305', NULL, 1, '2025-08-29 21:37:46'),
(2, 'recruitee', 'Rajan', 'test2@gmail.com', '$2y$10$hhTDAdCapMmtLuiE6enE4up8UD4csCIKMhWbHm9u4y/shaI2GwWTq', 'uploads/avatars/68b1e96823fb2-ChatGPT Image Mar 30, 2025, 08_01_26 AM.png', 'lazy boy', 'butwal', '9840402206', 'html, js, php, Team Motivation, Problem-solving, Report Preparation, Sales Training, Customer Relationship Management, Market Trend Analysis, Presentation Skills, Communication, Negotiation, Performance Analysis, Sales Forecasting, Dealer Relationship Management', 1, '2025-08-29 23:39:48'),
(3, 'admin', 'test', 'test@gmail.com', '$2y$10$16oLIWydIHryMPZBpy7mseZpr9C8BH9gd1xO9ZOnkiFB2GXdp8mIa', 'uploads/avatars/68b33ff52f118-logo.png', NULL, 'butwal', NULL, NULL, 1, '2025-08-31 00:01:21'),
(4, 'recruitee', 'test1', 'test5@gmail.com', '$2y$10$bQ6Oruv/IcaRp6vncBiFD.pADT3AGQ/E3uIH17PocgCQYEPxcH6g2', 'uploads/avatars/68b340bf32af3-recruitment.jpg', NULL, 'butwal', NULL, 'Security best practices, DNS), Networking (TCP/IP, MySQL/PostgreSQL, Bash/Python scripting, GitHub Actions), CI/CD (Jenkins, Docker, Terraform, Kubernetes, Linux, AWS, react, figma, bootstrap', 1, '2025-08-31 00:04:43'),
(5, 'recruiter', 'Ramesh Pun Magar', 'test4@gmail.com', '$2y$10$UGKQnuRDHdrhcLHREEnce.cc4Li2rNkWQ72lKe1/OW8FcWg3gd/Z6', 'uploads/avatars/68b5a9728374c-people.jpg', 'Lazy fucker', 'butwal', '9840402206', 'html, js, php, Team Motivation, Problem-solving, Report Preparation, Sales Training, Customer Relationship Management, Market Trend Analysis, Presentation Skills, Communication, Negotiation, Performance Analysis, Sales Forecasting, Dealer Relationship Management', 1, '2025-09-01 19:55:58'),
(6, 'recruitee', 'bro', 'test6@gmail.com', '$2y$10$TmJs9YwuCzOhZuqovcKjpeqGhYiVhF6W4yXDFW/lkg9AQnvM8bU4S', NULL, NULL, 'butwal', NULL, 'html, js, php, Team Motivation, Problem-solving, Report Preparation, Sales Training, Customer Relationship Management, Market Trend Analysis, Presentation Skills, Communication, Negotiation, Performance Analysis, Sales Forecasting, Dealer Relationship Management', 1, '2025-09-01 21:41:50'),
(7, 'recruiter', 'dude', 'test11@gmail.com', '$2y$10$YM3wyl.D9WHJmvHb/t4FYezLIqFOhBgowTbJG3BVmt4gVSl8CWfya', 'uploads/avatars/68b5c2d39b5bf-parrot.png', NULL, 'kathmandu', NULL, 'html, js, php, Team Motivation, Problem-solving, Report Preparation, Sales Training, Customer Relationship Management, Market Trend Analysis, Presentation Skills, Communication, Negotiation, Performance Analysis, Sales Forecasting, Dealer Relationship Management', 1, '2025-09-01 21:44:15'),
(9, 'recruitee', 'Rajan GC', 'gc.rajan339@gmail.com', '$2y$10$4xcDYc5pakj4lpU2litXm.c90To7on.f2IC6tMgSTp5e5bo7Pnvzu', 'uploads/avatars/68ce2b0b84e7d-IMG_1522.PNG', NULL, NULL, NULL, NULL, 1, '2025-09-20 10:03:19'),
(10, 'recruitee', 'tester one', 'tester1@gmail.com', '$2y$10$x7e66kHKun.cw1zNi6rr3etaes4I74lsmTb7c2fuIyhDuAesmQIJW', 'uploads/avatars/68cef3ed2ceba-IMG_3173.jpg', 'wow lazy', 'Kathmandu', '9840403305', '', 1, '2025-09-21 00:05:45'),
(11, 'recruitee', 'tester two', 'tester2@gmail.com', '$2y$10$D/ByTO6woqlUFl/CXoLf3uk/q7q2kd9FDQYVNlk8e.qO.9y4NaA2u', NULL, NULL, NULL, NULL, NULL, 1, '2025-09-21 18:45:51'),
(12, 'recruiter', 'tester twelve', 'teser12@gmail.com', '$2y$10$ih5d6fqvesKHMPSJmu914e6gEjWYSrLeBffXLKWaP9SNXBVE0CBHO', NULL, NULL, NULL, NULL, NULL, 1, '2025-09-21 18:47:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_applicant` (`job_id`,`recruitee_user_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indexes for table `chat_threads`
--
ALTER TABLE `chat_threads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_users` (`user_one_id`,`user_two_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cv_certificates`
--
ALTER TABLE `cv_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cv_id` (`cv_id`);

--
-- Indexes for table `cv_education`
--
ALTER TABLE `cv_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cv_id` (`cv_id`);

--
-- Indexes for table `cv_experience`
--
ALTER TABLE `cv_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cv_id` (`cv_id`);

--
-- Indexes for table `cv_projects`
--
ALTER TABLE `cv_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cv_id` (`cv_id`);

--
-- Indexes for table `cv_skills`
--
ALTER TABLE `cv_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cv_id` (`cv_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recruiter_user_id` (`recruiter_user_id`);

--
-- Indexes for table `job_comments`
--
ALTER TABLE `job_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `job_likes`
--
ALTER TABLE `job_likes`
  ADD PRIMARY KEY (`job_id`,`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `pending_signups`
--
ALTER TABLE `pending_signups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `recruitee_ratings`
--
ALTER TABLE `recruitee_ratings`
  ADD PRIMARY KEY (`recruitee_user_id`,`recruiter_user_id`);

--
-- Indexes for table `recruiter_ratings`
--
ALTER TABLE `recruiter_ratings`
  ADD PRIMARY KEY (`recruiter_user_id`,`recruitee_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_threads`
--
ALTER TABLE `chat_threads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `cv_certificates`
--
ALTER TABLE `cv_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cv_education`
--
ALTER TABLE `cv_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `cv_experience`
--
ALTER TABLE `cv_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `cv_projects`
--
ALTER TABLE `cv_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cv_skills`
--
ALTER TABLE `cv_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=187;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `job_comments`
--
ALTER TABLE `job_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pending_signups`
--
ALTER TABLE `pending_signups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
