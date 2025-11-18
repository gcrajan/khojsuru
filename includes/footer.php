<?php // /includes/footer.php ?>
</main>

<footer class="footer">
    <span>&copy; <?php echo date('Y'); ?> Khojsuru. All Rights Reserved.</span>
    <a href="<?php echo BASE_URL; ?>privacy.php">Privacy</a>
</footer>

<!-- Toast Notification Container: This is where toasts will appear -->
<div id="toast-container"></div>

<style>
    /* --- Toast Notification Styles --- */
    #toast-container {
        position: fixed;
        top: 80px; /* Just below the header */
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .toast {
        background: var(--secondary-bg);
        color: var(--text-primary);
        padding: 1rem 1.5rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        border-left-width: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    }
    .toast.show {
        opacity: 1;
        transform: translateX(0);
    }
    .toast.success { border-left-color: var(--success-color); }
    .toast.error { border-left-color: var(--error-color); }

    .toast-icon { font-size: 1.5rem; }
    .toast.success .toast-icon { color: var(--success-color); }
    .toast.error .toast-icon { color: var(--error-color); }
    .footer{
        padding: 1rem;
        color: var(--text-secondary);
        display: inline-block;
        text-align: center;
        width: -webkit-fill-available;
    }
    .footer>a{
        text-decoration: none;
        color: var(--text-secondary);
        padding-left: 0.5rem;
    }
    /* Responsive for smaller screens */
    @media (max-width: 480px) {
        #toast-container {
            right: 10px;
            left: 10px;
            top: 70px;
        }
        .toast {
            min-width: unset;
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- THEME TOGGLE ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const body = document.body;
        const themeIcon = themeToggleBtn?.querySelector('i');

        const applyTheme = () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                body.classList.add('light-theme');
                themeIcon && (themeIcon.className = 'fas fa-moon');
            } else {
                body.classList.remove('light-theme');
                themeIcon && (themeIcon.className = 'fas fa-sun');
            }
        };

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                body.classList.toggle('light-theme');
                const newTheme = body.classList.contains('light-theme') ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                if (themeIcon) {
                    themeIcon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                }
            });
        }

        applyTheme();

        // --- TOAST FUNCTION ---
        window.showToast = function (message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            toast.innerHTML = `
                <div class="toast-icon"><i class="fas ${iconClass}"></i></div>
                <div class="toast-message">${message}</div>
            `;
            container.appendChild(toast);

            // Animate in
            setTimeout(() => toast.classList.add('show'), 100);

            // Animate out and remove
            setTimeout(() => {
                toast.classList.remove('show');
                toast.addEventListener('transitionend', () => toast.remove());
            }, duration);
        };

        // --- COUNTDOWN TIMER ---
        function initializeCountdowns() {
            const countdownElements = document.querySelectorAll('.countdown-timer');
            if (countdownElements.length === 0) return;

            setInterval(() => {
                countdownElements.forEach(el => {
                    const deadline = new Date(el.dataset.deadline + " UTC").getTime();
                    const now = new Date().getTime();
                    const distance = deadline - now;

                    if (distance < 0) {
                        el.textContent = "Application Closed";
                        el.style.color = '#94a3b8';
                        const card = el.closest('.feed-item') || el.closest('.job-view-container');
                        if (card) {
                            const applyBtn = card.querySelector('.apply-btn');
                            if (applyBtn) {
                                applyBtn.textContent = 'Deadline Passed';
                                applyBtn.disabled = true;
                            }
                        }
                    } else {
                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        el.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s left`;

                        // Turn red if less than 1 day
                        if (distance < 24 * 60 * 60 * 1000) {
                            el.style.color = '#ef4444';
                        } else {
                            el.style.color = '#94a3b8';
                        }
                    }
                });
            }, 1000);
        }

        initializeCountdowns(); // Init countdowns after DOM is ready
    });
</script>

</body>
</html>