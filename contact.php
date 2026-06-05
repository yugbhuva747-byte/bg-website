<?php
// ============================================================
//  contact.php — Contact Page & Inquiry Form
//  PHP Role: Renders the contact form and AJAX handler script.
// ============================================================
require_once 'includes/db.php';
require_once 'includes/auth.php';

$site_title  = get_setting('site_title', $conn, 'Bhavana Goparaju');
$meta_desc   = get_setting('meta_description', $conn, 'Get in touch for story strategy, systems design, and program inquiry.');

$page_title = 'Contact — ' . $site_title;
$nav_theme = 'dark'; // Dark theme for navbar on dark green background
$no_footer_markup = true; // Completely suppress footer block markup

// Pre-fill fields if URL param specifies inquiry type
$inquiry_type = $_GET['inquiry'] ?? '';
$default_message = '';
if ($inquiry_type === 'partner') {
    $default_message = "I am interested in partnering or supporting your work. Let's start a conversation.";
} elseif ($inquiry_type === 'book') {
    $default_message = "I would like to invite/book you for a speaking, teaching, or facilitation engagement.";
} elseif ($inquiry_type === 'services') {
    $default_message = "I would like to inquire about consulting, story strategy, or systems design services.";
} elseif (!empty($inquiry_type)) {
    $default_message = "I am reaching out regarding project inquiry: " . ucwords(str_replace('-', ' ', sanitize($inquiry_type)));
}

// Generate a simple CSRF token for security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

mysqli_close($conn);

include_once 'includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
     CONTACT FORM SECTION — Dark Forest Green & Outlined Form
═══════════════════════════════════════════════════════════ -->
<section class="min-h-[90vh] bg-[#223525] text-cream flex items-center py-12 px-6 md:px-12 flex-grow">
    <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left Side: Form (7/12 width) -->
        <div class="lg:col-span-7 flex flex-col justify-center pr-0 lg:pr-8">
            <!-- Headline -->
            <h1 class="gsap-slide-left font-display text-4xl md:text-5xl font-semibold tracking-tight text-white mb-6 leading-tight">
                Start a conversation.
            </h1>

            <!-- Description -->
            <p class="gsap-fade-in font-body text-sm md:text-base text-cream/80 leading-relaxed max-w-xl mb-10">
                For consulting, partnerships, support, speaking, teaching, producing, story strategy, cultural programming, or systems design inquiries.
            </p>

            <!-- Form Fields -->
            <div id="contactFormBox" class="gsap-fade-in max-w-xl space-y-6">
                <!-- CSRF Token -->
                <input type="hidden" id="csrfToken" value="<?= $_SESSION['csrf_token'] ?>">

                <!-- Name Input -->
                <div>
                    <input type="text" id="name" class="w-full px-5 py-4 border border-cream/20 rounded-2xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm transition-all duration-300 placeholder-cream/50" placeholder="Your name" required>
                </div>

                <!-- Email Input -->
                <div>
                    <input type="email" id="email" class="w-full px-5 py-4 border border-cream/20 rounded-2xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm transition-all duration-300 placeholder-cream/50" placeholder="Email address" required>
                </div>

                <!-- Message Input -->
                <div>
                    <textarea id="message" rows="6" class="w-full px-5 py-4 border border-cream/20 rounded-2xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm transition-all duration-300 placeholder-cream/50 resize-y" placeholder="Tell me what you're building or trying to understand.&#10;What are you reaching out about?" required><?= sanitize($default_message) ?></textarea>
                </div>

                <!-- Submit & Alerts -->
                <div class="flex flex-col gap-4 pt-2">
                    <button onclick="submitContactForm()" class="px-8 py-3.5 bg-[#dcae44] text-[#1b2b1d] hover:bg-[#c39736] font-body font-bold text-[10px] tracking-[0.2em] uppercase rounded-full transition-all duration-300 self-start shadow-md">
                        Send Inquiry
                    </button>
                    
                    <!-- Alert Banner -->
                    <div id="alertBanner" class="hidden px-5 py-3.5 rounded-2xl font-body text-xs leading-relaxed border transition-all duration-300"></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Film Set Crew Image (5/12 width) -->
        <div class="lg:col-span-5 flex justify-center lg:justify-end">
            <div class="w-full max-w-[420px] aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl bg-neutral-900 gsap-slide-right">
                <?php if (file_exists('assets/images/contact_shoot.png')): ?>
                    <img src="assets/images/contact_shoot.png" alt="Film Crew on Set" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-cream/20">
                        Visual Visual
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</section>

<!-- AJAX Form Handler -->
<script>
async function submitContactForm() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    const csrfToken = document.getElementById('csrfToken').value;
    const alertBanner = document.getElementById('alertBanner');

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const message = messageInput.value.trim();

    // Reset Alert
    alertBanner.className = 'hidden';
    alertBanner.textContent = '';

    // Frontend validation
    if (!name || !email || !message) {
        showAlert('Please fill in all required fields.', 'error');
        return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showAlert('Please enter a valid email address.', 'error');
        return;
    }

    // Build Form Payload
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('message', message);
    formData.append('csrf_token', csrfToken);

    try {
        const response = await fetch('contact_submit.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();

        if (result.success) {
            showAlert('Your inquiry has been successfully sent. Thank you!', 'success');
            // Clear inputs
            nameInput.value = '';
            emailInput.value = '';
            messageInput.value = '';
        } else {
            showAlert(result.error || 'There was an issue sending your message. Please try again.', 'error');
        }
    } catch (e) {
        showAlert('A network error occurred. Please verify your connection and try again.', 'error');
    }
}

function showAlert(text, type) {
    const alertBanner = document.getElementById('alertBanner');
    alertBanner.textContent = text;
    
    if (type === 'success') {
        alertBanner.className = 'px-5 py-3.5 rounded-xl font-body text-xs leading-relaxed border bg-green-900/30 text-green-200 border-green-700/50 block';
    } else {
        alertBanner.className = 'px-5 py-3.5 rounded-xl font-body text-xs leading-relaxed border bg-red-950/30 text-red-200 border-red-900/50 block';
    }
    
    alertBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<?php
include_once 'includes/footer.php';
?>
