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
<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'bg_website';
$port = (int)(getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

// If connection fails, stop everything & show a safe message
if (!$conn) {
    // die() halts PHP execution immediately
    die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
}

// UTF-8 so emojis, Hindi, multilingual text work fine
mysqli_set_charset($conn, 'utf8mb4');
