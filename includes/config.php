<?php
// recruitercv/includes/config.php

// --- DATABASE CREDENTIALS ---
define('DB_HOST', 'dpg-d4elucre5dus73fiim6g-a.oregon-postgres.render.com');
define('DB_PORT', '5432');
define('DB_NAME', 'test_db_bl77');
define('DB_USER', 'test_db_user');
define('DB_PASS', '6IldFifDcr8AnVD2aZYYDoqTaF84cmZK');

// define('DB_HOST', 'sql113.infinityfree.com');
// define('DB_PORT', '3306');
// define('DB_NAME', 'if0_40452872_khojsuru');
// define('DB_USER', 'if0_40452872');
// define('DB_PASS', '5qPXk9revbCYh');
// --- BASE URL ---
// Change this after deployment:
define('BASE_URL', 'https://khojsuru-9dt0.onrender.com/');
// define('BASE_URL', 'https://localhost/');

// --- ROOT PATH ---
define('ROOT_PATH', __DIR__ . '/../');

// --- ADD THIS ENTIRE SECTION FOR API KEYS & SECRETS ---

// --- GOOGLE GEMINI API KEYS (with rotation) ---
// The system will try the first key, and if it fails due to a rate limit, it will try the next.
define('GEMINI_API_KEYS', [
    'AIzaSyB_jjxf6xOdezlbc-hxMiIFhbF9NY-hohA', // Your primary key
    // 'AIzaSyC_ANOTHERKEYHEREeXampLe-APIKeY123',  // Add a second key here if you have one
    // 'AIzaSyD_ANDANOTHERKEYHEREjKlmNoPqRstUvw'   // Add a third key here if you have one
]);

// --- SMTP (EMAIL) CREDENTIALS for Brevo ---
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'recruitercvnp@gmail.com');
define('SMTP_PASS', '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');
define('SMTP_FROM_EMAIL', 'khojsuru@gmail.com');
define('SMTP_FROM_NAME', 'Khojsuru');
?>
