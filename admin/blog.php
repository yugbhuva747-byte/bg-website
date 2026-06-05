<?php
// ============================================================
//  admin/blog.php — Blog CRUD Manager
//  PHP Role: Handle Create / Read / Update / Delete for blog_posts
//  All actions go through POST with an 'action' field
// ============================================================
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

$msg   = '';
$msgType = 'success';
$editPost = null;   // Holds post data when in Edit mode

// ── HANDLE POST ACTIONS ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── CREATE new post ──────────────────────────────────────
    if ($action === 'create') {
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status  = in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft';
        $slug    = create_slug($title);   // auto-generate slug from title

        // Handle image upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $image_path = handle_upload($_FILES['image']);
        }

        if (empty($title) || empty($content)) {
            $msg = 'Title and content are required.';
            $msgType = 'error';
        } else {
            // Check slug uniqueness — append timestamp if duplicate
            $check = mysqli_prepare($conn, "SELECT id FROM blog_posts WHERE slug = ?");
            mysqli_stmt_bind_param($check, 's', $slug);
            mysqli_stmt_execute($check);
            if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
                $slug .= '-' . time();
            }
            mysqli_stmt_close($check);

            $stmt = mysqli_prepare($conn, "INSERT INTO blog_posts (title, slug, content, image_path, status) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssss', $title, $slug, $content, $image_path, $status);
            if (mysqli_stmt_execute($stmt)) {
                $msg = 'Post created successfully!';
            } else {
                $msg = 'Error creating post.'; $msgType = 'error';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // ── UPDATE existing post ─────────────────────────────────
    elseif ($action === 'update') {
        $id      = (int)($_POST['id'] ?? 0);   // cast to int — prevents injection
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status  = in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft';

        $image_path = $_POST['existing_image'] ?? null;
        if (!empty($_FILES['image']['name'])) {
            $image_path = handle_upload($_FILES['image']);
        }

        if ($id > 0 && !empty($title) && !empty($content)) {
            $stmt = mysqli_prepare($conn, "UPDATE blog_posts SET title=?, content=?, image_path=?, status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssssi', $title, $content, $image_path, $status, $id);
            if (mysqli_stmt_execute($stmt)) {
                $msg = 'Post updated successfully!';
            } else {
                $msg = 'Error updating post.'; $msgType = 'error';
            }
            mysqli_stmt_close($stmt);
        } else {
            $msg = 'Missing required fields.'; $msgType = 'error';
        }
    }

    // ── DELETE post ──────────────────────────────────────────
    elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM blog_posts WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $msg = 'Post deleted.';
        }
    }
}

// ── Load post for EDITING (GET request with ?edit=ID) ─────────
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM blog_posts WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $editPost = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// ── Fetch all posts for listing ────────────────────────────────
$posts = mysqli_fetch_all(
    mysqli_query($conn, "SELECT id, title, slug, status, created_at FROM blog_posts ORDER BY created_at DESC"),
    MYSQLI_ASSOC
);

// ── File Upload Helper Function ───────────────────────────────
function handle_upload(array $file): ?string {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    // finfo checks ACTUAL file type, not just extension (security!)
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mime    = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;  // 5MB max

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('blog_', true) . '.' . $ext;   // unique filename
    $dest     = '../uploads/blog/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return 'uploads/blog/' . $filename;
    }
    return null;
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Manager — BG Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'DM Sans', sans-serif; background: #0d1b2a; color: #e8e0d0; margin: 0; }
      .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 240px; background: #0a1628; border-right: 1px solid rgba(201,168,76,0.12); padding: 2rem 0; }
      .sidebar-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #c9a84c; padding: 0 1.8rem 2rem; border-bottom: 1px solid rgba(201,168,76,0.12); margin-bottom: 1.5rem; }
      .nav-item { display: block; padding: 0.7rem 1.8rem; font-size: 0.82rem; color: rgba(232,224,208,0.65); text-decoration: none; transition: color 0.2s; border-left: 3px solid transparent; }
      .nav-item:hover, .nav-item.active { color: #c9a84c; background: rgba(201,168,76,0.07); border-left-color: #c9a84c; }
      .main { margin-left: 240px; padding: 2.5rem; }
      .page-title { font-family: 'Playfair Display', serif; font-size: 2rem; color: #e8e0d0; margin-bottom: 2rem; }
      .card { background: #111f38; border: 1px solid rgba(201,168,76,0.1); border-radius: 6px; overflow: hidden; margin-bottom: 2rem; }
      .card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(201,168,76,0.1); font-size: 0.82rem; letter-spacing: 0.1em; color: rgba(232,224,208,0.6); text-transform: uppercase; }
      .card-body { padding: 1.5rem; }
      .form-label { display: block; font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(232,224,208,0.5); margin-bottom: 0.4rem; }
      .form-input { width: 100%; padding: 0.75rem 1rem; background: rgba(255,255,255,0.05); border: 1.5px solid rgba(201,168,76,0.18); border-radius: 4px; color: #e8e0d0; font-family: 'DM Sans', sans-serif; font-size: 0.87rem; outline: none; margin-bottom: 1.2rem; transition: border-color 0.2s; }
      .form-input:focus { border-color: #c9a84c; }
      textarea.form-input { height: 200px; resize: vertical; }
      select.form-input { cursor: pointer; }
      .btn { padding: 0.65rem 1.4rem; border-radius: 4px; font-size: 0.78rem; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; transition: opacity 0.2s; }
      .btn-gold { background: #c9a84c; color: #0a1628; font-weight: 600; }
      .btn-gold:hover { opacity: 0.88; }
      .btn-danger { background: rgba(139,58,42,0.3); color: #ffb3a0; border: 1px solid rgba(139,58,42,0.4); }
      .btn-danger:hover { background: rgba(139,58,42,0.5); }
      .btn-edit { background: rgba(201,168,76,0.12); color: #c9a84c; border: 1px solid rgba(201,168,76,0.25); font-size: 0.72rem; padding: 0.4rem 0.9rem; }
      table { width: 100%; border-collapse: collapse; }
      th { padding: 0.75rem 1.2rem; font-size: 0.7rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(232,224,208,0.4); text-align: left; border-bottom: 1px solid rgba(201,168,76,0.08); }
      td { padding: 0.85rem 1.2rem; font-size: 0.82rem; border-bottom: 1px solid rgba(255,255,255,0.04); }
      tr:last-child td { border-bottom: none; }
      .status-pub   { color: #7dffb3; font-size: 0.72rem; }
      .status-draft { color: rgba(232,224,208,0.4); font-size: 0.72rem; }
      .msg-box { padding: 0.8rem 1.2rem; border-radius: 4px; font-size: 0.83rem; margin-bottom: 1.5rem; }
      .msg-success { background: rgba(42,122,80,0.2); border: 1px solid rgba(42,122,80,0.4); color: #7dffb3; }
      .msg-error   { background: rgba(139,58,42,0.2); border: 1px solid rgba(139,58,42,0.4); color: #ffb3a0; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">BG Admin</div>
    <a href="dashboard.php" class="nav-item">Dashboard</a>
    <a href="blog.php" class="nav-item active">Blog Posts</a>
    <a href="inquiries.php" class="nav-item">Inquiries</a>
    <a href="settings.php" class="nav-item">Site Settings</a>
</aside>

<main class="main">
    <h1 class="page-title"><?= $editPost ? 'Edit Post' : 'Blog Posts' ?></h1>

    <?php if ($msg): ?>
        <div class="msg-box msg-<?= $msgType ?>"><?= sanitize($msg) ?></div>
    <?php endif; ?>

    <!-- ── CREATE / EDIT FORM ─────────────────────────────── -->
    <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
    <div class="card">
        <div class="card-header"><?= $editPost ? 'Edit Post #'.$editPost['id'] : 'New Post' ?></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <!-- Hidden field tells PHP which action to run -->
                <input type="hidden" name="action" value="<?= $editPost ? 'update' : 'create' ?>">
                <?php if ($editPost): ?>
                    <input type="hidden" name="id" value="<?= $editPost['id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= sanitize($editPost['image_path'] ?? '') ?>">
                <?php endif; ?>

                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-input" value="<?= sanitize($editPost['title'] ?? '') ?>" required>

                <label class="form-label">Content *</label>
                <textarea name="content" class="form-input"><?= sanitize($editPost['content'] ?? '') ?></textarea>

                <label class="form-label">Featured Image</label>
                <input type="file" name="image" class="form-input" accept="image/*" style="padding:0.5rem;">
                <?php if (!empty($editPost['image_path'])): ?>
                    <p style="font-size:0.75rem;color:rgba(232,224,208,0.4);margin-top:-0.8rem;margin-bottom:1rem;">Current: <?= sanitize($editPost['image_path']) ?></p>
                <?php endif; ?>

                <label class="form-label">Status</label>
                <select name="status" class="form-input" style="max-width:200px;">
                    <option value="draft"     <?= ($editPost['status'] ?? '') === 'draft'     ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($editPost['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                </select>

                <div style="display:flex;gap:1rem;align-items:center;">
                    <button type="submit" class="btn btn-gold"><?= $editPost ? 'Update Post' : 'Create Post' ?></button>
                    <?php if ($editPost): ?>
                        <a href="blog.php" style="font-size:0.78rem;color:rgba(232,224,208,0.45);text-decoration:none;">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ── POSTS LIST ─────────────────────────────────────── -->
    <div class="card">
        <div class="card-header">All Posts (<?= count($posts) ?>)</div>
        <?php if (empty($posts)): ?>
            <p style="padding:2rem;color:rgba(232,224,208,0.35);font-size:0.85rem;">No posts yet. Create your first post above.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Title</th><th>Slug</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td style="color:rgba(232,224,208,0.35)"><?= $post['id'] ?></td>
                <td><?= sanitize($post['title']) ?></td>
                <td style="color:rgba(232,224,208,0.45);font-size:0.75rem;"><?= sanitize($post['slug']) ?></td>
                <td class="status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></td>
                <td style="color:rgba(232,224,208,0.4);font-size:0.75rem;"><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                <td>
                    <a href="blog.php?edit=<?= $post['id'] ?>" class="btn btn-edit">Edit</a>
                    &nbsp;
                    <!-- Inline delete form — one per row -->
                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this post permanently?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
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
