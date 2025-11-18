<?php
// recruitercv/includes/session_handler.php

// Use secure session settings
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
// For a live site with HTTPS, uncomment the next line
// ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically to prevent session fixation attacks
if (!isset($_SESSION['last_regen'])) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
} else if (time() - $_SESSION['last_regen'] > 1800) { // Regenerate every 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

function protect_page() {
    // Array of pages accessible to logged-out users
    $public_pages = [
        'login.php',
        'signup.php',
        'privacy.php',
        'contact.php',
        'blog.php',
        'article.php',
        '404.php'
    ];

    // Get the name of the current script
    $current_page = basename($_SERVER['PHP_SELF']);

    // If the user is NOT logged in AND the current page is NOT in our public list
    if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
        // Redirect them to the login page
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}
/**
 * Ensures that only a logged-in user with the 'admin' role can access a page.
 */
function require_admin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        // Redirect non-admins to the main homepage or a 404 page
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}