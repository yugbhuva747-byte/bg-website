<?php
// ============================================================
//  db.php — Database Connection
//  WHY: Single place to change DB credentials.
//       Every other file just does: require_once 'includes/db.php';
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // change for production
define('DB_PASS', '');           // change for production
define('DB_NAME', 'bg_website');

// mysqli_connect() — procedural style, simple & lightweight
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// If connection fails, stop everything & show a safe message
if (!$conn) {
    // die() halts PHP execution immediately
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// UTF-8 so emojis, Hindi, multilingual text work fine
mysqli_set_charset($conn, 'utf8mb4');
