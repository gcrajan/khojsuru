-- PostgreSQL compatible schema for RecruiterCV
-- Converted from MySQL to PostgreSQL

-- Enable UUID extension if needed (optional)
-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

SET TIME ZONE 'UTC';

--
-- Table structure for table `applications`
--

CREATE TABLE applications (
  id SERIAL PRIMARY KEY,
  job_id INTEGER NOT NULL,
  recruitee_user_id INTEGER NOT NULL,
  cv_id INTEGER DEFAULT NULL,
  uploaded_cv_path VARCHAR(255) DEFAULT NULL,
  application_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(20) NOT NULL DEFAULT 'submitted' CHECK (status IN ('submitted','viewed','interviewing','rejected','hired')),
  UNIQUE (job_id, recruitee_user_id)
);

--
-- Table structure for table `blog_posts`
--

CREATE TABLE blog_posts (
  id SERIAL PRIMARY KEY,
  author_user_id INTEGER NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content_html TEXT NOT NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'draft' CHECK (status IN ('draft','published')),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create trigger to automatically update updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_blog_posts_updated_at BEFORE UPDATE ON blog_posts FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

--
-- Table structure for table `chat_messages`
--

CREATE TABLE chat_messages (
  id SERIAL PRIMARY KEY,
  thread_id INTEGER NOT NULL,
  sender_user_id INTEGER NOT NULL,
  message_content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read SMALLINT NOT NULL DEFAULT 0
);

--
-- Table structure for table `chat_threads`
--

CREATE TABLE chat_threads (
  id SERIAL PRIMARY KEY,
  user_one_id INTEGER NOT NULL,
  user_two_id INTEGER NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (user_one_id, user_two_id)
);

--
-- Table structure for table `companies`
--

CREATE TABLE companies (
  id SERIAL PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  website VARCHAR(255) DEFAULT NULL,
  logo VARCHAR(255) DEFAULT NULL,
  about TEXT DEFAULT NULL,
  created_by_user_id INTEGER NOT NULL
);

--
-- Table structure for table `contact_messages`
--

CREATE TABLE contact_messages (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(255) DEFAULT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Table structure for table `cvs`
--

CREATE TABLE cvs (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  title VARCHAR(150) NOT NULL,
  target_role VARCHAR(255) DEFAULT NULL,
  is_public SMALLINT NOT NULL DEFAULT 0,
  template_name VARCHAR(50) NOT NULL DEFAULT 'modern',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  full_name VARCHAR(100) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  linkedin_url VARCHAR(255) DEFAULT NULL,
  github_url VARCHAR(255) DEFAULT NULL,
  summary TEXT DEFAULT NULL
);

CREATE TRIGGER update_cvs_updated_at BEFORE UPDATE ON cvs FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

--
-- Table structure for table `cv_certificates`
--

CREATE TABLE cv_certificates (
  id SERIAL PRIMARY KEY,
  cv_id INTEGER NOT NULL,
  certificate_name VARCHAR(150) NOT NULL,
  issuing_organization VARCHAR(100) DEFAULT NULL,
  issue_date VARCHAR(50) DEFAULT NULL,
  credential_url VARCHAR(255) DEFAULT NULL
);

--
-- Table structure for table `cv_education`
--

CREATE TABLE cv_education (
  id SERIAL PRIMARY KEY,
  cv_id INTEGER NOT NULL,
  degree VARCHAR(100) NOT NULL,
  institution VARCHAR(100) DEFAULT NULL,
  start_date VARCHAR(50) DEFAULT NULL,
  end_date VARCHAR(50) DEFAULT NULL
);

--
-- Table structure for table `cv_experience`
--

CREATE TABLE cv_experience (
  id SERIAL PRIMARY KEY,
  cv_id INTEGER NOT NULL,
  job_title VARCHAR(100) NOT NULL,
  company_name VARCHAR(100) DEFAULT NULL,
  start_date VARCHAR(50) DEFAULT NULL,
  end_date VARCHAR(50) DEFAULT NULL,
  description TEXT DEFAULT NULL
);

--
-- Table structure for table `cv_projects`
--

CREATE TABLE cv_projects (
  id SERIAL PRIMARY KEY,
  cv_id INTEGER NOT NULL,
  project_name VARCHAR(150) NOT NULL,
  project_url VARCHAR(255) DEFAULT NULL,
  description TEXT DEFAULT NULL
);

--
-- Table structure for table `cv_skills`
--

CREATE TABLE cv_skills (
  id SERIAL PRIMARY KEY,
  cv_id INTEGER NOT NULL,
  skill_name VARCHAR(100) NOT NULL
);

--
-- Table structure for table `jobs`
--

CREATE TABLE jobs (
  id SERIAL PRIMARY KEY,
  recruiter_user_id INTEGER NOT NULL,
  company_id INTEGER NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(150) DEFAULT NULL,
  job_type VARCHAR(20) NOT NULL DEFAULT 'Full-time' CHECK (job_type IN ('Full-time','Part-time','Contract','Internship')),
  is_remote SMALLINT NOT NULL DEFAULT 0,
  is_active SMALLINT NOT NULL DEFAULT 1,
  is_featured SMALLINT NOT NULL DEFAULT 0,
  posted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deadline TIMESTAMP NOT NULL
);

--
-- Table structure for table `job_comments`
--

CREATE TABLE job_comments (
  id SERIAL PRIMARY KEY,
  job_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  parent_comment_id INTEGER DEFAULT NULL,
  comment_text TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Table structure for table `job_likes`
--

CREATE TABLE job_likes (
  job_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (job_id, user_id)
);

--
-- Table structure for table `notifications`
--

CREATE TABLE notifications (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  type VARCHAR(20) NOT NULL CHECK (type IN ('new_applicant','status_change','new_comment','new_reply','new_rating')),
  message VARCHAR(255) NOT NULL,
  link VARCHAR(255) NOT NULL,
  is_read SMALLINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Table structure for table `password_resets`
--

CREATE TABLE password_resets (
  id SERIAL PRIMARY KEY,
  email VARCHAR(150) NOT NULL,
  token VARCHAR(64) NOT NULL,
  expires_at TIMESTAMP NOT NULL
);

--
-- Table structure for table `pending_signups`
--

CREATE TABLE pending_signups (
  id SERIAL PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  otp_hash VARCHAR(255) NOT NULL,
  form_data TEXT NOT NULL,
  expires_at TIMESTAMP NOT NULL
);

--
-- Table structure for table `recruitee_ratings`
--

CREATE TABLE recruitee_ratings (
  recruitee_user_id INTEGER NOT NULL,
  recruiter_user_id INTEGER NOT NULL,
  rating SMALLINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (recruitee_user_id, recruiter_user_id)
);

--
-- Table structure for table `recruiter_ratings`
--

CREATE TABLE recruiter_ratings (
  recruiter_user_id INTEGER NOT NULL,
  recruitee_user_id INTEGER NOT NULL,
  rating SMALLINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (recruiter_user_id, recruitee_user_id)
);

--
-- Table structure for table `users`
--

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  user_type VARCHAR(20) NOT NULL CHECK (user_type IN ('recruitee','recruiter','admin')),
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  profile_image VARCHAR(255) DEFAULT NULL,
  headline VARCHAR(250) DEFAULT NULL,
  location VARCHAR(100) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  skills_cache TEXT DEFAULT NULL,
  is_active SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

--
-- Create indexes for better performance
--

CREATE INDEX idx_applications_job_id ON applications(job_id);
CREATE INDEX idx_applications_recruitee_user_id ON applications(recruitee_user_id);
CREATE INDEX idx_applications_cv_id ON applications(cv_id);

CREATE INDEX idx_blog_posts_author ON blog_posts(author_user_id);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);
CREATE INDEX idx_blog_posts_slug ON blog_posts(slug);

CREATE INDEX idx_chat_messages_thread_id ON chat_messages(thread_id);
CREATE INDEX idx_chat_messages_sender ON chat_messages(sender_user_id);
CREATE INDEX idx_chat_messages_created_at ON chat_messages(created_at);

CREATE INDEX idx_companies_created_by ON companies(created_by_user_id);

CREATE INDEX idx_cvs_user_id ON cvs(user_id);
CREATE INDEX idx_cvs_is_public ON cvs(is_public);

CREATE INDEX idx_cv_certificates_cv_id ON cv_certificates(cv_id);
CREATE INDEX idx_cv_education_cv_id ON cv_education(cv_id);
CREATE INDEX idx_cv_experience_cv_id ON cv_experience(cv_id);
CREATE INDEX idx_cv_projects_cv_id ON cv_projects(cv_id);
CREATE INDEX idx_cv_skills_cv_id ON cv_skills(cv_id);

CREATE INDEX idx_jobs_recruiter_user_id ON jobs(recruiter_user_id);
CREATE INDEX idx_jobs_company_id ON jobs(company_id);
CREATE INDEX idx_jobs_is_active ON jobs(is_active);
CREATE INDEX idx_jobs_is_featured ON jobs(is_featured);
CREATE INDEX idx_jobs_deadline ON jobs(deadline);

CREATE INDEX idx_job_comments_job_id ON job_comments(job_id);
CREATE INDEX idx_job_comments_user_id ON job_comments(user_id);
CREATE INDEX idx_job_comments_parent_id ON job_comments(parent_comment_id);

CREATE INDEX idx_job_likes_user_id ON job_likes(user_id);

CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_is_read ON notifications(is_read);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);

CREATE INDEX idx_password_resets_email ON password_resets(email);
CREATE INDEX idx_password_resets_token ON password_resets(token);
CREATE INDEX idx_password_resets_expires_at ON password_resets(expires_at);

CREATE INDEX idx_pending_signups_expires_at ON pending_signups(expires_at);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_user_type ON users(user_type);
CREATE INDEX idx_users_is_active ON users(is_active);

--
-- Foreign key constraints
--

-- Applications table
ALTER TABLE applications ADD CONSTRAINT fk_applications_job_id FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE;
ALTER TABLE applications ADD CONSTRAINT fk_applications_recruitee_user_id FOREIGN KEY (recruitee_user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE applications ADD CONSTRAINT fk_applications_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE SET NULL;

-- Blog posts table
ALTER TABLE blog_posts ADD CONSTRAINT fk_blog_posts_author_user_id FOREIGN KEY (author_user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Chat tables
ALTER TABLE chat_messages ADD CONSTRAINT fk_chat_messages_thread_id FOREIGN KEY (thread_id) REFERENCES chat_threads(id) ON DELETE CASCADE;
ALTER TABLE chat_messages ADD CONSTRAINT fk_chat_messages_sender_user_id FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE chat_threads ADD CONSTRAINT fk_chat_threads_user_one_id FOREIGN KEY (user_one_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE chat_threads ADD CONSTRAINT fk_chat_threads_user_two_id FOREIGN KEY (user_two_id) REFERENCES users(id) ON DELETE CASCADE;

-- Companies table
ALTER TABLE companies ADD CONSTRAINT fk_companies_created_by_user_id FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE CASCADE;

-- CV tables
ALTER TABLE cvs ADD CONSTRAINT fk_cvs_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE cv_certificates ADD CONSTRAINT fk_cv_certificates_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE;
ALTER TABLE cv_education ADD CONSTRAINT fk_cv_education_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE;
ALTER TABLE cv_experience ADD CONSTRAINT fk_cv_experience_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE;
ALTER TABLE cv_projects ADD CONSTRAINT fk_cv_projects_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE;
ALTER TABLE cv_skills ADD CONSTRAINT fk_cv_skills_cv_id FOREIGN KEY (cv_id) REFERENCES cvs(id) ON DELETE CASCADE;

-- Jobs table
ALTER TABLE jobs ADD CONSTRAINT fk_jobs_recruiter_user_id FOREIGN KEY (recruiter_user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE jobs ADD CONSTRAINT fk_jobs_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE;

-- Job comments table
ALTER TABLE job_comments ADD CONSTRAINT fk_job_comments_job_id FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE;
ALTER TABLE job_comments ADD CONSTRAINT fk_job_comments_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE job_comments ADD CONSTRAINT fk_job_comments_parent_id FOREIGN KEY (parent_comment_id) REFERENCES job_comments(id) ON DELETE CASCADE;

-- Job likes table
ALTER TABLE job_likes ADD CONSTRAINT fk_job_likes_job_id FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE;
ALTER TABLE job_likes ADD CONSTRAINT fk_job_likes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Notifications table
ALTER TABLE notifications ADD CONSTRAINT fk_notifications_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Rating tables
ALTER TABLE recruitee_ratings ADD CONSTRAINT fk_recruitee_ratings_recruitee_user_id FOREIGN KEY (recruitee_user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE recruitee_ratings ADD CONSTRAINT fk_recruitee_ratings_recruiter_user_id FOREIGN KEY (recruiter_user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE recruiter_ratings ADD CONSTRAINT fk_recruiter_ratings_recruiter_user_id FOREIGN KEY (recruiter_user_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE recruiter_ratings ADD CONSTRAINT fk_recruiter_ratings_recruitee_user_id FOREIGN KEY (recruitee_user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Insert sample data (optional - same as your MySQL data but with PostgreSQL syntax)
-- Note: You would need to convert your INSERT statements to use single quotes and handle binary data differently

COMMENT ON TABLE applications IS 'Stores job applications from recruitees';
COMMENT ON COLUMN applications.status IS 'Application status: submitted, viewed, interviewing, rejected, hired';

COMMENT ON TABLE blog_posts IS 'Blog posts for career hub';
COMMENT ON COLUMN blog_posts.author_user_id IS 'Admin/Editor''s user ID';

COMMENT ON TABLE notifications IS 'User notifications system';
COMMENT ON COLUMN notifications.link IS 'The URL to go to when clicked';

COMMENT ON TABLE pending_signups IS 'Pending user registrations with OTP verification';
-- COMMENT ON COLUMN pending_signups.form_data IS 'JSON encoded data from the signup form';

COMMENT ON TABLE users IS 'System users table';
-- COMMENT ON COLUMN users.skills_cache IS 'A comma-separated list of top skills for fast searching';