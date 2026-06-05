<?php
// ============================================================
//  admin/dashboard.php — Admin Dashboard (Protected)
//  PHP Role: require_login() gate → fetch stats → render panel
// ============================================================
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();   // 🔐 Redirects to login.php if not authenticated

// ── Fetch dashboard stats ─────────────────────────────────────
// mysqli_fetch_assoc() returns one row as an associative array
$total_posts    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM blog_posts"))['c'];
$published      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM blog_posts WHERE status='published'"))['c'];
$total_inquiries= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contact_inquiries"))['c'];
$unread         = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM contact_inquiries WHERE is_read=0"))['c'];

// Recent inquiries — LIMIT 5 for preview
$inquiries = mysqli_fetch_all(
    mysqli_query($conn, "SELECT name, email, message, created_at, is_read FROM contact_inquiries ORDER BY created_at DESC LIMIT 5"),
    MYSQLI_ASSOC
);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — BG Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'DM Sans', sans-serif; background: #0d1b2a; color: #e8e0d0; margin: 0; }
      /* ── Sidebar ── */
      .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 240px; background: #0a1628; border-right: 1px solid rgba(201,168,76,0.12); padding: 2rem 0; display: flex; flex-direction: column; }
      .sidebar-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #c9a84c; padding: 0 1.8rem 2rem; border-bottom: 1px solid rgba(201,168,76,0.12); margin-bottom: 1.5rem; }
      .nav-item { display: block; padding: 0.7rem 1.8rem; font-size: 0.82rem; letter-spacing: 0.08em; color: rgba(232,224,208,0.65); text-decoration: none; transition: color 0.2s, background 0.2s; border-left: 3px solid transparent; }
      .nav-item:hover, .nav-item.active { color: #c9a84c; background: rgba(201,168,76,0.07); border-left-color: #c9a84c; }
      .nav-item .badge { background: #c9a84c; color: #0a1628; font-size: 0.65rem; padding: 1px 6px; border-radius: 50px; margin-left: 0.5rem; font-weight: 700; }
      .sidebar-bottom { margin-top: auto; padding: 0 1.8rem; }
      .logout-btn { display: block; width: 100%; padding: 0.7rem 1rem; background: rgba(139,58,42,0.2); border: 1px solid rgba(139,58,42,0.35); color: #ffb3a0; font-size: 0.78rem; letter-spacing: 0.1em; text-align: center; text-decoration: none; border-radius: 4px; transition: background 0.2s; }
      .logout-btn:hover { background: rgba(139,58,42,0.4); }
      /* ── Main Content ── */
      .main { margin-left: 240px; padding: 2.5rem; }
      .page-title { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; color: #e8e0d0; margin-bottom: 0.3rem; }
      .page-sub { font-size: 0.82rem; color: rgba(232,224,208,0.45); margin-bottom: 2.5rem; }
      /* Stat cards */
      .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.2rem; margin-bottom: 2.5rem; }
      .stat-card { background: #111f38; border: 1px solid rgba(201,168,76,0.1); border-radius: 6px; padding: 1.5rem; }
      .stat-label { font-size: 0.72rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(232,224,208,0.45); margin-bottom: 0.5rem; }
      .stat-num { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 700; color: #c9a84c; }
      /* Table */
      .card { background: #111f38; border: 1px solid rgba(201,168,76,0.1); border-radius: 6px; overflow: hidden; }
      .card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(201,168,76,0.1); font-size: 0.82rem; letter-spacing: 0.1em; color: rgba(232,224,208,0.6); text-transform: uppercase; display: flex; justify-content: space-between; align-items: center; }
      table { width: 100%; border-collapse: collapse; }
      th { padding: 0.75rem 1.2rem; font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(232,224,208,0.4); text-align: left; border-bottom: 1px solid rgba(201,168,76,0.08); }
      td { padding: 0.9rem 1.2rem; font-size: 0.82rem; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: top; }
      tr:last-child td { border-bottom: none; }
      .badge-unread { background: rgba(201,168,76,0.15); color: #c9a84c; font-size: 0.65rem; padding: 2px 8px; border-radius: 50px; }
      .badge-read   { background: rgba(255,255,255,0.05); color: rgba(232,224,208,0.4); font-size: 0.65rem; padding: 2px 8px; border-radius: 50px; }
      @media(max-width:900px){ .stats-grid{grid-template-columns:1fr 1fr;} }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">BG Admin</div>
    <a href="dashboard.php" class="nav-item active">Dashboard</a>
    <a href="blog.php" class="nav-item">Blog Posts</a>
    <a href="inquiries.php" class="nav-item">
        Inquiries
        <?php if ($unread > 0): ?>
            <span class="badge"><?= $unread ?></span>
        <?php endif; ?>
    </a>
    <a href="settings.php" class="nav-item">Site Settings</a>
    <div class="sidebar-bottom">
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-sub">Welcome back. Here's an overview of your site.</p>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Posts</div>
            <div class="stat-num"><?= $total_posts ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Published</div>
            <div class="stat-num"><?= $published ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Inquiries</div>
            <div class="stat-num"><?= $total_inquiries ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unread</div>
            <div class="stat-num" style="color:<?= $unread > 0 ? '#c9a84c' : 'rgba(232,224,208,0.3)' ?>"><?= $unread ?></div>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="card">
        <div class="card-header">
            Recent Inquiries
            <a href="inquiries.php" style="font-size:0.75rem;color:#c9a84c;text-decoration:none;">View All →</a>
        </div>
        <?php if (empty($inquiries)): ?>
            <p style="padding:2rem;color:rgba(232,224,208,0.4);font-size:0.85rem;">No inquiries yet.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Preview</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php foreach ($inquiries as $inq): ?>
                <tr>
                    <td><?= sanitize($inq['name']) ?></td>
                    <td style="color:rgba(232,224,208,0.6)"><?= sanitize($inq['email']) ?></td>
                    <td style="color:rgba(232,224,208,0.5);max-width:200px;"><?= sanitize(substr($inq['message'], 0, 60)) ?>...</td>
                    <td style="color:rgba(232,224,208,0.4);font-size:0.75rem;"><?= date('M d, Y', strtotime($inq['created_at'])) ?></td>
                    <td>
                        <?php if (!$inq['is_read']): ?>
                            <span class="badge-unread">Unread</span>
                        <?php else: ?>
                            <span class="badge-read">Read</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
