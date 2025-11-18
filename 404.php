<?php
http_response_code(404);

$page_title = "Page Not Found: Khojsuru";
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 4rem 1rem;
        min-height: 60vh;
    }
    .error-code {
        font-size: 4rem;
        font-weight: 700;
        color: var(--accent-color);
        margin: 0;
    }
    .error-title {
        font-size: 1.5rem;
        color: var(--text-secondary);
        margin: 1rem 0;
    }
    .btn-home {
        background: var(--accent-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
    }
    .lottie-container {
        width: 300px;
        height: 300px;
        margin-bottom: 2rem;
    }
    .pagenotfoundimg{
        height:20rem;
        width: auto;
        margin-bottom:2rem;
    }
</style>

<div class="error-container">
    <h1 class="error-code">Are you Lost?</h1>
    <p class="error-title">
        Sorry, the page you are looking for doesn't exist or has been moved. Let's get you back to our home.
    </p>
    <img src="<?php echo BASE_URL; ?>assets/images/pagenotfound.svg" alt="404" class="pagenotfoundimg">
    <a href="<?php echo BASE_URL; ?>" class="btn-home">
        <i class="fas fa-home"></i> Go to Homepage
    </a>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
