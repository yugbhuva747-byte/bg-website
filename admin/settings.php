<?php
// ============================================================
//  admin/settings.php — Site Configuration Panel
//  PHP Role: Read/update site_settings table via AJAX + regular form
// ============================================================
require_once '../includes/db.php';
require_once '../includes/auth.php';

require_login();

// ── AJAX handler — returns JSON ────────────────────────────────
// When JS sends fetch() to this file with action=save_setting
if (isset($_POST['action']) && $_POST['action'] === 'save_setting') {
    header('Content-Type: application/json');

    $key   = trim($_POST['key']   ?? '');
    $value = trim($_POST['value'] ?? '');

    // Whitelist of allowed keys — prevents arbitrary DB writes
    $allowed_keys = ['site_title','tagline','contact_email','contact_phone','dark_mode_default','meta_description'];

    if (!in_array($key, $allowed_keys)) {
        echo json_encode(['success' => false, 'error' => 'Invalid setting key.']);
        exit();
    }

    // INSERT OR UPDATE — if key exists, update value; else insert
    // ON DUPLICATE KEY UPDATE is efficient: one query handles both cases
    $stmt = mysqli_prepare($conn, "INSERT INTO site_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?");
    mysqli_stmt_bind_param($stmt, 'sss', $key, $value, $value);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB error.']);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
}

// ── Load all settings for display ─────────────────────────────
$settings_result = mysqli_query($conn, "SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = mysqli_fetch_assoc($settings_result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

mysqli_close($conn);

// Helper to safely get a setting with fallback
function s(array $settings, string $key, string $default = ''): string {
    return htmlspecialchars($settings[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings — BG Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body { font-family: 'DM Sans', sans-serif; background: #0d1b2a; color: #e8e0d0; margin: 0; }
      .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 240px; background: #0a1628; border-right: 1px solid rgba(201,168,76,0.12); padding: 2rem 0; }
      .sidebar-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #c9a84c; padding: 0 1.8rem 2rem; border-bottom: 1px solid rgba(201,168,76,0.12); margin-bottom: 1.5rem; }
      .nav-item { display: block; padding: 0.7rem 1.8rem; font-size: 0.82rem; color: rgba(232,224,208,0.65); text-decoration: none; transition: 0.2s; border-left: 3px solid transparent; }
      .nav-item:hover, .nav-item.active { color: #c9a84c; background: rgba(201,168,76,0.07); border-left-color: #c9a84c; }
      .main { margin-left: 240px; padding: 2.5rem; max-width: 900px; }
      .page-title { font-family: 'Playfair Display', serif; font-size: 2rem; color: #e8e0d0; margin-bottom: 2rem; }
      .settings-group { background: #111f38; border: 1px solid rgba(201,168,76,0.1); border-radius: 6px; margin-bottom: 1.5rem; overflow: hidden; }
      .group-title { padding: 1rem 1.5rem; background: rgba(201,168,76,0.06); border-bottom: 1px solid rgba(201,168,76,0.1); font-size: 0.75rem; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(232,224,208,0.55); }
      .setting-row { display: flex; align-items: center; gap: 1.5rem; padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.04); }
      .setting-row:last-child { border-bottom: none; }
      .setting-label { font-size: 0.82rem; color: rgba(232,224,208,0.7); min-width: 180px; }
      .setting-label small { display: block; font-size: 0.7rem; color: rgba(232,224,208,0.35); margin-top: 2px; }
      .setting-input { flex: 1; padding: 0.6rem 0.9rem; background: rgba(255,255,255,0.05); border: 1.5px solid rgba(201,168,76,0.15); border-radius: 4px; color: #e8e0d0; font-family: 'DM Sans', sans-serif; font-size: 0.85rem; outline: none; transition: border-color 0.2s; }
      .setting-input:focus { border-color: #c9a84c; }
      .save-btn { padding: 0.55rem 1.1rem; background: rgba(201,168,76,0.15); border: 1px solid rgba(201,168,76,0.3); color: #c9a84c; border-radius: 4px; font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; cursor: pointer; transition: background 0.2s; white-space: nowrap; font-family: 'DM Sans', sans-serif; }
      .save-btn:hover { background: rgba(201,168,76,0.28); }
      .save-btn.saved { background: rgba(42,122,80,0.25); border-color: rgba(42,122,80,0.5); color: #7dffb3; }
      /* Toggle switch for dark mode */
      .toggle { position: relative; width: 44px; height: 24px; }
      .toggle input { opacity: 0; width: 0; height: 0; }
      .slider { position: absolute; inset: 0; background: rgba(255,255,255,0.1); border-radius: 24px; cursor: pointer; transition: 0.3s; border: 1px solid rgba(201,168,76,0.2); }
      .slider:before { content:''; position: absolute; width: 18px; height: 18px; left: 3px; top: 2px; background: rgba(232,224,208,0.6); border-radius: 50%; transition: 0.3s; }
      input:checked + .slider { background: rgba(201,168,76,0.3); border-color: #c9a84c; }
      input:checked + .slider:before { transform: translateX(20px); background: #c9a84c; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-logo">BG Admin</div>
    <a href="dashboard.php" class="nav-item">Dashboard</a>
    <a href="blog.php" class="nav-item">Blog Posts</a>
    <a href="inquiries.php" class="nav-item">Inquiries</a>
    <a href="settings.php" class="nav-item active">Site Settings</a>
</aside>

<main class="main">
    <h1 class="page-title">Site Settings</h1>
    <p style="font-size:0.83rem;color:rgba(232,224,208,0.45);margin-top:-1.5rem;margin-bottom:2rem;">Changes save instantly via AJAX — no page reload needed.</p>

    <!-- IDENTITY -->
    <div class="settings-group">
        <div class="group-title">Identity</div>

        <div class="setting-row">
            <div class="setting-label">Site Title <small>Appears in browser tab & nav</small></div>
            <input class="setting-input" id="site_title" value="<?= s($settings, 'site_title') ?>">
            <button class="save-btn" onclick="saveSetting('site_title')">Save</button>
        </div>

        <div class="setting-row">
            <div class="setting-label">Tagline <small>Subtitle under name</small></div>
            <input class="setting-input" id="tagline" value="<?= s($settings, 'tagline') ?>">
            <button class="save-btn" onclick="saveSetting('tagline')">Save</button>
        </div>

        <div class="setting-row">
            <div class="setting-label">Meta Description <small>SEO description</small></div>
            <input class="setting-input" id="meta_description" value="<?= s($settings, 'meta_description') ?>">
            <button class="save-btn" onclick="saveSetting('meta_description')">Save</button>
        </div>
    </div>

    <!-- CONTACT -->
    <div class="settings-group">
        <div class="group-title">Contact Information</div>

        <div class="setting-row">
            <div class="setting-label">Contact Email</div>
            <input class="setting-input" id="contact_email" type="email" value="<?= s($settings, 'contact_email') ?>">
            <button class="save-btn" onclick="saveSetting('contact_email')">Save</button>
        </div>

        <div class="setting-row">
            <div class="setting-label">Contact Phone</div>
            <input class="setting-input" id="contact_phone" value="<?= s($settings, 'contact_phone') ?>">
            <button class="save-btn" onclick="saveSetting('contact_phone')">Save</button>
        </div>
    </div>

    <!-- APPEARANCE -->
    <div class="settings-group">
        <div class="group-title">Appearance</div>

        <div class="setting-row">
            <div class="setting-label">Dark Mode Default <small>Toggle site-wide default</small></div>
            <label class="toggle">
                <input type="checkbox" id="dark_mode_default"
                    <?= ($settings['dark_mode_default'] ?? '0') === '1' ? 'checked' : '' ?>
                    onchange="saveSetting('dark_mode_default', this.checked ? '1' : '0')">
                <span class="slider"></span>
            </label>
        </div>
    </div>
</main>

<script>
// ── AJAX Save Individual Setting ──────────────────────────────
// Each field has its own Save button — no full form submit needed
// This is UX-friendly: change one thing, save one thing
async function saveSetting(key, overrideValue) {
    // overrideValue used for toggle switches that don't have input[id]
    const inputEl = document.getElementById(key);
    const value   = overrideValue !== undefined ? overrideValue : (inputEl ? inputEl.value : '');

    // Find the Save button for this setting (next sibling of input)
    const btn = inputEl ? inputEl.nextElementSibling : null;

    const fd = new FormData();
    fd.append('action', 'save_setting');
    fd.append('key',    key);
    fd.append('value',  value);

    try {
        // POST to same page — PHP detects action=save_setting and returns JSON early
        const res  = await fetch('settings.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success && btn) {
            // Visual feedback: button turns green momentarily
            btn.textContent = '✓ Saved';
            btn.classList.add('saved');
            setTimeout(() => {
                btn.textContent = 'Save';
                btn.classList.remove('saved');
            }, 2000);
        }
    } catch(e) {
        console.error('Save failed', e);
    }
}
</script>
</body>
</html>
