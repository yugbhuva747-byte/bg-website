# BG Website — Full PHP Project
## By: Yug Bhuva | Task Assignment: Social Amplifiers

---

## 📁 Project Structure

```
bg-website/
├── index.php               ← Homepage (public)
├── contact_submit.php      ← AJAX contact form handler
├── setup.sql               ← Run once to create DB tables
├── .htaccess               ← Apache security + routing
│
├── includes/
│   ├── db.php              ← MySQL connection
│   └── auth.php            ← Session, login, helper functions
│
├── admin/
│   ├── login.php           ← Admin login form
│   ├── logout.php          ← Session destroy
│   ├── dashboard.php       ← Stats overview
│   ├── blog.php            ← Blog CRUD (Create/Read/Update/Delete)
│   ├── settings.php        ← Site config with AJAX save
│   └── inquiries.php       ← Contact messages viewer
│
└── uploads/
    └── blog/               ← Blog image uploads stored here
```

---

## 🚀 Setup Instructions

### 1. Database
```bash
mysql -u root -p < setup.sql
```

### 2. Local Server
```bash
# Using XAMPP or WAMP — place folder in htdocs/
# OR use PHP built-in server:
cd bg-website
php -S localhost:8000
```

### 3. Admin Login
- URL: `http://localhost:8000/admin/login.php`
- Username: `admin`
- Password: `Admin@123`

### 4. For Render.com Deployment
- Add env vars: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
- Update `includes/db.php` to read from `$_ENV`
- Add `composer.json` if using composer packages

---

## 🧠 PHP Concepts Explained — Every Single One Used

### 1. `require_once` vs `include`
```php
require_once '../includes/db.php';
```
- `require` = if file missing, PHP STOPS (fatal error). Good for critical files.
- `include` = if file missing, PHP gives warning but continues.
- `_once` suffix = load the file only once, even if called multiple times.
- Use `require_once` for DB connections, auth helpers — they must exist.

---

### 2. `mysqli_connect()` — Database Connection
```php
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
```
- Opens a connection to MySQL server.
- Returns a connection object `$conn` used in ALL queries.
- Always close with `mysqli_close($conn)` when done.
- We use `mysqli_` (not deprecated `mysql_`) or PDO.

---

### 3. Prepared Statements — SQL Injection Prevention
```php
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
```
- `?` = placeholder. PHP fills it safely — user input NEVER goes directly into SQL.
- `bind_param('s', $var)` — 's' means string, 'i' = integer, 'd' = double.
- Without prepared statements: `WHERE username = '$username'` is vulnerable to:
  ```sql
  admin' OR '1'='1  ← This would log in without password!
  ```

---

### 4. `password_hash()` and `password_verify()`
```php
// When creating admin (in setup.sql comments):
$hash = password_hash('Admin@123', PASSWORD_BCRYPT);

// When verifying login:
if (password_verify($plain_password, $hash_from_db)) { ... }
```
- `PASSWORD_BCRYPT` generates a one-way hash. Cannot be reversed.
- `password_verify()` re-hashes and compares. Returns true/false.
- NEVER store plain text passwords.

---

### 5. PHP Sessions — How Admin Login Works
```php
session_start();                          // Must be first thing before any output
$_SESSION['admin_logged_in'] = true;      // Store in server-side session
session_regenerate_id(true);              // New session ID after login (security)
session_destroy();                        // On logout
```
- Session data lives on the SERVER in a temp file.
- Browser only gets a cookie: `PHPSESSID=abc123`
- Every request: browser sends cookie → PHP reads session file → checks vars.
- This is how "stay logged in" works.

---

### 6. `header('Location: ...')` — PHP Redirects
```php
header('Location: /admin/dashboard.php');
exit();  // ALWAYS call exit() after header redirect!
```
- Sends HTTP 302 redirect. Browser goes to new URL.
- `exit()` is critical — without it, PHP keeps running below the redirect.
- Must be called BEFORE any HTML output (or use output buffering).

---

### 7. `$_POST`, `$_GET`, `$_FILES` — Superglobals
```php
$name  = $_POST['name'] ?? '';     // Form field via POST
$id    = $_GET['edit']  ?? '';     // URL parameter: ?edit=5
$file  = $_FILES['image'];         // Uploaded file info
```
- `??` = null coalescing operator. If key doesn't exist, use default.
- `$_POST` — submitted form data (not visible in URL).
- `$_GET` — URL query parameters (`?key=value`).
- `$_FILES` — contains: name, tmp_name, size, type, error.

---

### 8. File Upload Security
```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
```
- `$_FILES['image']['type']` is set by the BROWSER — easy to fake!
- `finfo` checks the ACTUAL file bytes (magic numbers) — reliable.
- `move_uploaded_file()` — safely moves from PHP's temp dir to our uploads folder.
- Always: check MIME, check size, generate a unique filename.

---

### 9. `filter_var()` — Input Validation
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }
```
- PHP has built-in filters for common validations.
- `FILTER_VALIDATE_EMAIL` — checks proper email format.
- `FILTER_VALIDATE_INT` — checks if value is integer.
- `FILTER_SANITIZE_STRING` — removes dangerous chars (deprecated in PHP 8.1, use `htmlspecialchars`).

---

### 10. `htmlspecialchars()` — XSS Prevention
```php
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');
// or our helper:
function sanitize(string $str): string {
    return htmlspecialchars(strip_tags($str), ENT_QUOTES, 'UTF-8');
}
```
- XSS = Cross-Site Scripting: attacker injects `<script>alert('hacked')</script>` into your page.
- `htmlspecialchars()` converts `<` to `&lt;`, `>` to `&gt;`, `"` to `&quot;`.
- Browser displays these as text, not HTML — script never runs.
- ALWAYS sanitize before `echo`-ing user data.

---

### 11. `json_encode()` — AJAX Responses
```php
header('Content-Type: application/json');
echo json_encode(['success' => true]);
// or
echo json_encode(['success' => false, 'error' => 'Something went wrong']);
```
- For AJAX endpoints (called by JavaScript `fetch()`).
- `json_encode()` converts PHP array to JSON string.
- JS receives it and calls `response.json()` to parse back to object.

---

### 12. `ON DUPLICATE KEY UPDATE` — Upsert
```sql
INSERT INTO site_settings (setting_key, setting_value)
VALUES (?, ?)
ON DUPLICATE KEY UPDATE setting_value = ?
```
- If the row exists (duplicate primary key) → UPDATE it.
- If it doesn't exist → INSERT it.
- One query handles both cases — perfect for settings.

---

### 13. `mysqli_fetch_all()` vs `mysqli_fetch_assoc()`
```php
// Get ALL rows at once:
$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
// Returns: [['id'=>1,'title'=>'...'], ['id'=>2,'title'=>'...'], ...]

// Get ONE row (in a loop or for single result):
$row = mysqli_fetch_assoc($result);
// Returns: ['id'=>1, 'title'=>'...']
```

---

### 14. Type Casting for Security
```php
$id = (int)($_POST['id'] ?? 0);
```
- `(int)` casts to integer. `"5; DROP TABLE posts"` becomes `5`.
- Always cast numeric IDs before using in queries.
- Even with prepared statements — defense in depth.

---

### 15. `date()` and `strtotime()`
```php
echo date('M d, Y', strtotime($row['created_at']));
// "2024-01-15 14:30:00" → "Jan 15, 2024"

echo date('Y');  // Current year for footer copyright
```
- `strtotime()` converts date string to Unix timestamp.
- `date()` formats timestamp into human-readable string.

---

## 🎨 Frontend Stack Used
- **Tailwind CSS** (CDN) — utility classes
- **GSAP** (CDN) — timeline animations, ScrollTrigger
- **Google Fonts** — Playfair Display + DM Sans
- **Vanilla JS fetch()** — AJAX form submission, settings save

---

## 🔐 Security Checklist
- [x] Prepared statements (SQL Injection prevention)
- [x] password_hash / password_verify (no plain passwords)
- [x] session_regenerate_id (Session Fixation prevention)
- [x] htmlspecialchars on all echo (XSS prevention)
- [x] MIME-type file validation (not trusting browser)
- [x] .htaccess blocks /includes/ directory
- [x] Generic error messages (don't reveal which field failed)
- [x] Type casting for all numeric inputs
- [x] Setting key whitelist in AJAX handler

---

## 📦 Deployment on Render.com
1. Push to GitHub
2. Create new Web Service on Render → connect repo
3. Environment: PHP (select version 8.x)
4. Start command: `php -S 0.0.0.0:$PORT`
5. Add environment variables for DB credentials
6. For DB: use Railway.app free MySQL, or PlanetScale

Made for: Social Amplifiers | Assignee: Yug Bhuva
