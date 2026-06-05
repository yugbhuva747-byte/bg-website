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
$host = "sql206.infinityfree.com";
$user = "if0_42101782";
$pass = "YOUR_CPANEL_PASSWORD";  // jo password thi login karyo
$db   = "if0_42101782_bg_website";
$port = 3306;

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
