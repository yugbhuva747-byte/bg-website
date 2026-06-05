<?php
// ============================================================
//  contact_submit.php — AJAX endpoint for Contact Form
//  PHP Role: Validate input → sanitize → insert to DB → JSON response
//  This file is called by fetch() in index.php JS, NOT directly by browser
// ============================================================
header('Content-Type: application/json');   // Tell browser: response is JSON

require_once 'includes/db.php';
require_once 'includes/auth.php';

// Only accept POST requests — reject GET, PUT, etc.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

// ── Read & sanitize POST data ─────────────────────────────────
// strip_tags() removes any HTML/PHP tags
// trim() removes whitespace
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

// ── Server-side validation ────────────────────────────────────
// NEVER rely only on client-side JS validation — always validate in PHP too
$errors = [];
if (empty($name))    $errors[] = 'Name is required.';
if (empty($email))   $errors[] = 'Email is required.';
if (empty($message)) $errors[] = 'Message is required.';

// filter_var with FILTER_VALIDATE_EMAIL checks proper email format
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}
if (strlen($name) > 255)    $errors[] = 'Name too long.';
if (strlen($message) > 5000) $errors[] = 'Message too long.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit();
}

// ── Prepared Statement — Prevents SQL Injection ───────────────
// ? placeholders are filled safely by bind_param
// mysqli_prepare() compiles the SQL query structure first
$stmt = mysqli_prepare($conn, "INSERT INTO contact_inquiries (name, email, message) VALUES (?, ?, ?)");
// 'sss' = three string parameters
mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $message);

if (mysqli_stmt_execute($stmt)) {
    // Optional: send email notification to admin
    // mail('admin@domain.com', 'New Inquiry from '.$name, $message);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Could not save your message. Try again.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
