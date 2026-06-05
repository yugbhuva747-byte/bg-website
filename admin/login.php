<?php
// ============================================================
//  admin/login.php — Secure Admin Login Page
//  PHP Role: Process login form → verify credentials → create session
// ============================================================
require_once '../includes/db.php';
require_once '../includes/auth.php';

// If already logged in, redirect to dashboard immediately
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// ── Handle form POST ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';   // Don't trim passwords

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // login_admin() in auth.php verifies hash & creates session
        if (login_admin($username, $password, $conn)) {
            header('Location: dashboard.php');
            exit();
        } else {
            // Generic error — don't reveal which field is wrong (security)
            $error = 'Invalid credentials. Please try again.';
            // Small delay to slow brute-force attacks
            sleep(1);
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — BG</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'DM Sans', sans-serif; background: #0a1628; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
      .login-card { background: #111f38; border: 1px solid rgba(201,168,76,0.2); border-radius: 8px; padding: 3rem 2.5rem; width: 100%; max-width: 420px; }
      .logo { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 900; color: #c9a84c; text-align: center; margin-bottom: 0.3rem; }
      .logo-sub { font-size: 0.75rem; letter-spacing: 0.15em; color: rgba(245,240,232,0.4); text-align: center; text-transform: uppercase; margin-bottom: 2.5rem; }
      .label { font-size: 0.75rem; letter-spacing: 0.1em; color: rgba(245,240,232,0.6); text-transform: uppercase; margin-bottom: 0.4rem; display: block; }
      .input { width: 100%; padding: 0.85rem 1.1rem; background: rgba(255,255,255,0.05); border: 1.5px solid rgba(201,168,76,0.2); border-radius: 4px; color: #f5f0e8; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; outline: none; transition: border-color 0.3s; margin-bottom: 1.2rem; }
      .input:focus { border-color: #c9a84c; }
      .btn { width: 100%; padding: 0.9rem; background: #c9a84c; color: #0a1628; font-weight: 600; font-size: 0.82rem; letter-spacing: 0.12em; text-transform: uppercase; border: none; border-radius: 4px; cursor: pointer; transition: opacity 0.3s; }
      .btn:hover { opacity: 0.88; }
      .error-box { background: rgba(139,58,42,0.25); border: 1px solid rgba(139,58,42,0.5); color: #ffb3a0; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.82rem; margin-bottom: 1.2rem; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo">BG</div>
    <div class="logo-sub">Admin Portal</div>

    <?php if ($error): ?>
        <!-- PHP outputs error only when $error variable is not empty -->
        <div class="error-box"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <!-- method="post" — credentials sent in request body, NOT URL -->
    <!-- action="" — submits to same file (login.php handles both GET and POST) -->
    <form method="post" action="">
        <label class="label" for="username">Username</label>
        <!-- autocomplete="username" — helps password managers -->
        <input class="input" type="text" id="username" name="username" autocomplete="username" required>

        <label class="label" for="password">Password</label>
        <input class="input" type="password" id="password" name="password" autocomplete="current-password" required>

        <button class="btn" type="submit">Sign In</button>
    </form>

    <p style="text-align:center;margin-top:1.5rem;">
        <a href="/" style="font-size:0.75rem;color:rgba(245,240,232,0.35);text-decoration:none;">← Back to Site</a>
    </p>
</div>
</body>
</html>
