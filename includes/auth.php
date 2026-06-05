<?php
// ============================================================
//  auth.php — Session & Authentication Helper
//  WHY: Centralises all session logic so admin pages just
//       call one function instead of repeating session checks
// ============================================================

// session_start() must be called BEFORE any output (echo/HTML)
// It reads the PHPSESSID cookie and loads session data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── is_logged_in() ──────────────────────────────────────────
// Returns TRUE only if the session has a valid admin flag.
// PHP sessions are server-side; the browser only holds a cookie ID.
function is_logged_in(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// ── require_login() ──────────────────────────────────────────
// Call this at the top of every protected admin page.
// header('Location: ...') sends HTTP redirect, exit() stops execution.
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit();
    }
}

// ── login_admin() ────────────────────────────────────────────
// Verifies credentials against DB and creates session if valid.
// password_verify() — compares plain password with stored bcrypt hash
function login_admin(string $username, string $password, $conn): bool {
    // Prepared statement — prevents SQL Injection completely
    // ? is a placeholder; real value bound separately via bind_param
    $stmt = mysqli_prepare($conn, "SELECT id, password FROM admin_users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);   // 's' = string type
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID after login — prevents Session Fixation attack
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']        = $user['id'];
        return true;
    }
    return false;
}

// ── logout_admin() ───────────────────────────────────────────
function logout_admin(): void {
    $_SESSION = [];                           // clear all session vars
    session_destroy();                        // destroy server-side session
    header('Location: /admin/login.php');
    exit();
}

// ── get_setting() ────────────────────────────────────────────
// Fetch a single site setting from DB with a fallback default
function get_setting(string $key, $conn, string $default = ''): string {
    $stmt = mysqli_prepare($conn, "SELECT setting_value FROM site_settings WHERE setting_key = ?");
    mysqli_stmt_bind_param($stmt, 's', $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $row ? $row['setting_value'] : $default;
}

// ── sanitize() ───────────────────────────────────────────────
// htmlspecialchars() converts <, >, &, " to HTML entities
// Prevents XSS (Cross-Site Scripting) when displaying user input
function sanitize(string $str): string {
    return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
}

// ── create_slug() ────────────────────────────────────────────
// Converts "My Blog Post!" → "my-blog-post"
// Used for clean, SEO-friendly URLs
function create_slug(string $title): string {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);   // remove special chars
    $slug = preg_replace('/[\s-]+/', '-', $slug);          // spaces → hyphens
    return $slug;
}
