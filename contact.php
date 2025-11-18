<?php
// /contact.php
$page_title = "Contact: Khojsuru";
// All logic must come before the header is included.
require_once __DIR__ . '/includes/session_handler.php'; // Defines $is_logged_in
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .contact-layout {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem 4rem;
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    .contact-info-panel, .contact-form-panel {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2.5rem;
    }
    .contact-header { margin-bottom: 2rem; }
    .contact-header h1 { font-size: 2.25rem; }
    .contact-header p { color: var(--text-secondary); }
    
    .info-item { display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem; }
    .info-item i { font-size: 1.25rem; color: var(--accent-color); margin-top: 0.25rem; }
    .info-item-content h4 { font-size: 1.1rem; margin: 0 0 0.25rem; }
    .info-item-content p { color: var(--text-secondary); margin: 0; }
    
    .map-container iframe { width: 100%; height: 250px; border-radius: 12px; }

    .faq-section { background: var(--secondary-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 2.5rem; margin-top: 2rem; }
    .faq-item { border-bottom: 1px solid var(--border-color); }
    .faq-item:last-child { border-bottom: none;}
    .faq-question { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 0; cursor: pointer; font-weight: 500; }
    .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; color: var(--text-secondary); }
    .faq-item.active .faq-answer { max-height: 200px; /* Adjust as needed */ }

    @media (min-width: 992px) {
        .contact-layout { grid-template-columns: 1fr 1.2fr; }
        .faq-section { grid-column: 1 / -1; } /* Make FAQ span full width */
    }
    @media (max-width: 480px) {
        .contact-layout {margin: 0rem; padding:0rem;}
        .contact-info-panel, .contact-form-panel {    padding: 1rem 0.5rem;}
        .contact-header h1 { font-size: 1.8rem; margin-top: 0rem;}
        .faq-section { padding: 1rem 0.5rem; margin-top: 0rem;}
    }
</style>

<div class="contact-layout">
    <div class="contact-info-panel">
        <div class="contact-header">
            <h1>Contact Information</h1>
            <p>Reach out to us through any of the channels below.</p>
        </div>
        <div class="info-item">
            <i class="fas fa-map-marker-alt fa-fw"></i>
            <div class="info-item-content">
                <h4>Our Office</h4><p>Shantinagar, Kathmandu, Nepal</p>
            </div>
        </div>
        <div class="info-item">
            <i class="fas fa-envelope fa-fw"></i>
            <div class="info-item-content">
                <h4>Email Us</h4><p>jhamghatltd@gmail.com</p>
            </div>
        </div>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1034.52483924341!2d85.34271724627925!3d27.68747418933513!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb199216093dbd%3A0x5cbfd3f63859846!2zTThRVisyOEMsIOCktuCkvuCkqOCljeCkpOCkv-CkqOCkl-CksCDgpK7gpL7gpLDgpY3gpJcsIEthdGhtYW5kdSA0NDYwMA!5e0!3m2!1sen!2snp!4v1755940547844!5m2!1sen!2snp" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
    <div class="contact-form-panel">
        <div class="contact-header">
            <h1>Send us a Message</h1>
            <p>Fill out the form and our team will get back to you shortly.</p>
        </div>
        <?php if ($is_logged_in): ?>
            <form id="contact-form" novalidate>
                <div class="form-group"><label for="subject">Subject</label><input type="text" name="subject" id="subject" class="form-input" required></div>
                <div class="form-group"><label for="message">Message</label><textarea name="message" id="message" class="form-input" rows="5" required></textarea></div>
                <button type="submit" id="contact-submit-btn" class="btn-submit">Send Message</button>
            </form>
        <?php else: ?>
            <div style="text-align:center; background:rgba(239, 68, 68, 0.1); padding: 2rem; border-radius: 8px;border: 1px solid var(--error-color);    color: var(--error-color);">
                <h4><i class="fas fa-lock"></i> Login Required</h4>
                <p>Please log in to use our contact form. This helps us prevent spam and provide better support.</p>
                <a href="login.php" class="btn-submit" style="display:inline-block; width:auto; text-decoration:none;">Login to Contact Us</a>
            </div>
        <?php endif; ?>
</div>
<div class="faq-section">
    <div class="contact-header" style="margin-bottom: 1.5rem;">
        <h2>Frequently Asked Questions</h2>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Is my CV created with Khojsuru ATS-friendly?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Yes, absolutely. We designed our templates with Applicant Tracking Systems (ATS) in mind. Our Classic and Modern templates use clean layouts, standard fonts, and clear headings to ensure they are parsed correctly by recruitment software. Our AI generation tool also helps you include role-specific keywords, which increases your chances of passing ATS scans. For best results, we recommend the Classic or Modern templates.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Can I tailor a CV for each job application?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Yes, and we encourage it! You can create multiple CVs for different applications. Use the "Create with AI" feature for each role to generate a tailored version based on the job description. You can then manage all these versions easily from your <strong>My CVs</strong> tab.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Should I use the AI-generated content directly?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>The AI provides a strong first draft, saving you hours of work. However, the best CVs include your unique achievements. We recommend reviewing and customizing the draft — for example, replace "Responsible for sales" with "Increased sales by 20%". Think of AI as your assistant, not a replacement.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>What's the difference between "Manual" and "AI" creation?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>The <strong>Manual</strong> option gives you full control to craft a CV from scratch inside our editor. The <strong>AI</strong> option auto-generates content based on a job description, which you can then refine. Many users start with AI and personalize afterwards.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Can I download my CV in different formats?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Currently, CVs are generated as high-quality PDFs. PDF ensures your design and formatting remain consistent across all devices and is widely accepted by recruiters and ATS. We may introduce DOCX or other formats in the future, but PDF is the most professional and reliable format today.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Is my data safe and private?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Yes. All CVs you create are private by default, visible only to you unless you choose to make them public. We do not sell your data to third parties. For full details, please read our <a href="<?php echo BASE_URL; ?>privacy">Privacy Policy</a>.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>Is Khojsuru free to use?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Yes, Khojsuru provides free access to core features like creating and downloading CVs. In the future, premium features may be introduced, but the basic CV builder will remain free for all users.</p>
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question"><span>I'm having trouble logging in. What should I do?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>If you cannot access your account, try resetting your password using the "Forgot Password" option. If the issue persists, please contact us at <a href="mailto:jhamghatltd@gmail.com">jhamghatltd@gmail.com</a> for assistance.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <div class="faq-question"><span>How can I post ads?</span><i class="fas fa-chevron-down"></i></div>
        <div class="faq-answer">
            <p>Advertisers can reach out to us directly at <a href="mailto:jhamghatltd@gmail.com">jhamghatltd@gmail.com</a>. We’ll guide you through the posting process.</p>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- FAQ Accordion ---
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const faqItem = button.parentElement;
            faqItem.classList.toggle('active');
        });
    });

    // --- Contact Form Submission (only if the form exists on the page) ---
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('contact-submit-btn');
            const formData = new FormData(this);
            
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;

            fetch('<?php echo BASE_URL; ?>api.php?action=submit_contact_form', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Message sent successfully!', 'success');
                    contactForm.reset();
                } else {
                    showToast(data.message || 'An error occurred.', 'error');
                }
            })
            .catch(() => showToast('A network error occurred. Please try again.', 'error'))
            .finally(() => {
                submitBtn.textContent = 'Send Message';
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>