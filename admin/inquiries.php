<?php
// ============================================================
//  admin/inquiries.php — Contact Inquiries Viewer
// ============================================================
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_login();

// Mark as read when viewing full message
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $rid  = (int)$_GET['read'];
    $stmt = mysqli_prepare($conn, "UPDATE contact_inquiries SET is_read=1 WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $rid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Delete inquiry
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $did  = (int)($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn, "DELETE FROM contact_inquiries WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $did);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

$inquiries = mysqli_fetch_all(
    mysqli_query($conn, "SELECT * FROM contact_inquiries ORDER BY created_at DESC"),
    MYSQLI_ASSOC
);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries — BG Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'DM Sans', sans-serif; background: #0d1b2a; color: #e8e0d0; margin: 0; }
      .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 240px; background: #0a1628; border-right: 1px solid rgba(201,168,76,0.12); padding: 2rem 0; }
      .sidebar-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #c9a84c; padding: 0 1.8rem 2rem; border-bottom: 1px solid rgba(201,168,76,0.12); margin-bottom: 1.5rem; }
      .nav-item { display: block; padding: 0.7rem 1.8rem; font-size: 0.82rem; color: rgba(232,224,208,0.65); text-decoration: none; transition: 0.2s; border-left: 3px solid transparent; }
      .nav-item:hover,.nav-item.active { color: #c9a84c; background: rgba(201,168,76,0.07); border-left-color: #c9a84c; }
      .main { margin-left: 240px; padding: 2.5rem; }
      .page-title { font-family: 'Playfair Display', serif; font-size: 2rem; color: #e8e0d0; margin-bottom: 2rem; }
      .inq-card { background: #111f38; border: 1px solid rgba(201,168,76,0.1); border-radius: 6px; padding: 1.3rem 1.5rem; margin-bottom: 1rem; display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: start; }
      .inq-card.unread { border-left: 3px solid #c9a84c; }
      .inq-name { font-weight: 500; margin-bottom: 0.2rem; }
      .inq-email { font-size: 0.78rem; color: rgba(232,224,208,0.5); margin-bottom: 0.6rem; }
      .inq-msg { font-size: 0.83rem; color: rgba(232,224,208,0.7); line-height: 1.65; }
      .inq-date { font-size: 0.72rem; color: rgba(232,224,208,0.35); margin-top: 0.5rem; }
      .inq-actions { display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end; }
      .badge-unread { background: rgba(201,168,76,0.15); color: #c9a84c; font-size: 0.65rem; padding: 2px 8px; border-radius: 50px; }
      .btn-del { padding: 0.4rem 0.8rem; background: rgba(139,58,42,0.25); border: 1px solid rgba(139,58,42,0.4); color: #ffb3a0; border-radius: 4px; font-size: 0.72rem; cursor: pointer; font-family: 'DM Sans', sans-serif; }
      .btn-del:hover { background: rgba(139,58,42,0.45); }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">BG Admin</div>
    <a href="dashboard.php" class="nav-item">Dashboard</a>
    <a href="blog.php" class="nav-item">Blog Posts</a>
    <a href="inquiries.php" class="nav-item active">Inquiries</a>
    <a href="settings.php" class="nav-item">Site Settings</a>
</aside>
<main class="main">
    <h1 class="page-title">Contact Inquiries</h1>
    <?php if (empty($inquiries)): ?>
        <p style="color:rgba(232,224,208,0.4);">No inquiries yet.</p>
    <?php else: ?>
        <?php foreach ($inquiries as $inq): ?>
        <div class="inq-card <?= !$inq['is_read'] ? 'unread' : '' ?>">
            <div>
                <div class="inq-name">
                    <?= sanitize($inq['name']) ?>
                    <?php if (!$inq['is_read']): ?><span class="badge-unread">New</span><?php endif; ?>
                </div>
                <div class="inq-email"><?= sanitize($inq['email']) ?></div>
                <div class="inq-msg">
                    <?= nl2br(sanitize($inq['message'])) ?>
                </div>
                <div class="inq-date"><?= date('F j, Y — g:i a', strtotime($inq['created_at'])) ?></div>
            </div>
            <div class="inq-actions">
                <?php if (!$inq['is_read']): ?>
                    <a href="inquiries.php?read=<?= $inq['id'] ?>" style="font-size:0.72rem;color:#c9a84c;text-decoration:none;">Mark Read</a>
                <?php endif; ?>
                <form method="post" onsubmit="return confirm('Delete this inquiry?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                    <button type="submit" class="btn-del">Delete</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>
