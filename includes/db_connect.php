
<?php
// recruitercv/includes/db_connect.php

require_once 'config.php';

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // For development, showing the error is helpful.
    // In a live production environment, you should log the error and show a generic message.
    die("Database Connection Error: " . $e->getMessage());
}
?>