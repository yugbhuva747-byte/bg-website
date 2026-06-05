<?php
// ============================================================
//  includes/header.php — Shared Header
//  PHP Role: Renders HTML head, CSS styling, and responsive nav.
//  Expects: $page_title, $meta_desc, and optionally $nav_theme ('light' or 'dark')
// ============================================================
$nav_theme = $nav_theme ?? 'light';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($meta_desc) ? sanitize($meta_desc) : '' ?>">
    <title><?= isset($page_title) ? sanitize($page_title) : 'Bhavana Goparaju' ?></title>

    <!-- Google Fonts: Playfair Display (serif) + DM Sans (body sans-serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400;1,700&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- GSAP + ScrollTrigger for modern animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>

    <script>
      // Custom Tailwind theme styling matching PDF exactly
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              navy: '#0c1a2c',       // Left Split Dark Navy
              forest: '#112217',     // Right Split Dark Green
              cream: '#f6f0e5',      // Main Page Background Warm Cream
              rust: '#a34d32',       // Terracotta accent
              olive: '#4d6b46',      // Olive green accent
              deepslate: '#152e3c',  // Map a System Card background
              gold: '#c9a84c',       // Button background / highlight yellow
              charcoal: '#1a1a1a',   // Dark body text
            },
            fontFamily: {
              display: ['"Playfair Display"', 'serif'],
              body: ['"DM Sans"', 'sans-serif'],
            }
          }
        }
      }
    </script>

    <style>
      /* ── Custom Micro-styles & Base Rules ────────────────── */
      *, *::before, *::after { box-sizing: border-box; }
      html { scroll-behavior: smooth; }
      body {
        font-family: 'DM Sans', sans-serif;
        background-color: #f6f0e5;
        color: #1a1a1a;
        overflow-x: hidden;
      }
      
      /* Glassmorphism utility */
      .glass-nav {
        background: rgba(246, 240, 229, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
      }
      .glass-nav-dark {
        background: rgba(12, 26, 44, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
      }

      /* Image zoom animation effect */
      .zoom-hover {
        overflow: hidden;
      }
      .zoom-hover img {
        transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
      }
      .zoom-hover:hover img {
        transform: scale(1.05);
      }

      /* custom scrollbar */
      ::-webkit-scrollbar {
        width: 8px;
      }
      ::-webkit-scrollbar-track {
        background: #f6f0e5;
      }
      ::-webkit-scrollbar-thumb {
        background: #c9a84c;
        border-radius: 4px;
      }
      ::-webkit-scrollbar-thumb:hover {
        background: #a34d32;
      }

      /* GSAP hidden states to avoid flash of unstyled content */
      .gsap-fade-in { opacity: 0; transform: translateY(30px); }
      .gsap-slide-left { opacity: 0; transform: translateX(-40px); }
      .gsap-slide-right { opacity: 0; transform: translateX(40px); }
    </style>
</head>
<body class="flex flex-col min-h-screen">

<!-- ═══════════════════════════════════════════════════════════
     NAVIGATION HEADER
═══════════════════════════════════════════════════════════ -->
<?php
$is_dark = ($nav_theme === 'dark');
$navbar_bg = $is_dark ? 'glass-nav-dark border-b border-white/10' : 'glass-nav border-b border-black/5';
$text_color = $is_dark ? 'text-cream/80 hover:text-gold' : 'text-charcoal/80 hover:text-rust';
$logo_border = $is_dark ? 'border-cream text-cream' : 'border-charcoal text-charcoal';
$contact_border = $is_dark ? 'border-cream/40 text-cream hover:bg-cream hover:text-navy' : 'border-charcoal/40 text-charcoal hover:bg-charcoal hover:text-cream';
$hamburger_color = $is_dark ? 'bg-cream' : 'bg-charcoal';
?>
<nav class="fixed top-0 left-0 w-full z-50 px-6 py-4 md:px-12 flex items-center justify-between transition-all duration-500 opacity-0 pointer-events-none <?= $navbar_bg ?>">
    <!-- Logo -->
    <a href="index.php" class="flex items-center">
        <div class="w-10 h-10 rounded-full border-2 <?= $logo_border ?> flex items-center justify-center font-display font-bold text-sm tracking-wider transition-transform duration-300 hover:scale-105">
            BG
        </div>
    </a>

    <!-- Desktop Navigation Links -->
    <div class="hidden md:flex items-center gap-8 font-body font-medium text-[11px] tracking-[0.15em] uppercase">
        <a href="index.php#services-section" class="<?= $text_color ?> transition-colors duration-300">Work With Me</a>
        <a href="index.php#work-section" class="<?= $text_color ?> transition-colors duration-300">My Work</a>
        <a href="index.php#work-section" onclick="if(window.filterWork) { filterWork('films'); }" class="<?= $text_color ?> transition-colors duration-300">Films</a>
        <a href="index.php#writing-section" class="<?= $text_color ?> transition-colors duration-300">Writing & Photography</a>
        <a href="index.php#about-section" class="<?= $text_color ?> transition-colors duration-300">About</a>
        <a href="index.php#contact-section" class="nav-contact ml-4 px-5 py-2 border rounded-full <?= $contact_border ?> transition-all duration-300 font-bold">Contact</a>
    </div>

    <!-- Mobile Navigation Toggle -->
    <button id="mobileMenuBtn" class="md:hidden flex flex-col justify-between w-6 h-4 focus:outline-none z-50" aria-label="Toggle Menu">
        <span class="w-full h-[2px] <?= $hamburger_color ?> transition-all duration-300 origin-left"></span>
        <span class="w-full h-[2px] <?= $hamburger_color ?> transition-all duration-300"></span>
        <span class="w-full h-[2px] <?= $hamburger_color ?> transition-all duration-300 origin-left"></span>
    </button>

    <!-- Mobile Drawer Menu -->
    <div id="mobileDrawer" class="fixed inset-0 bg-navy z-40 transform translate-x-full transition-transform duration-500 ease-in-out flex flex-col justify-center px-12 md:hidden">
        <div class="flex flex-col gap-8 font-display text-2xl text-cream font-semibold">
            <a href="index.php#services-section" class="hover:text-gold transition-colors duration-300">Work With Me</a>
            <a href="index.php#work-section" class="hover:text-gold transition-colors duration-300">My Work</a>
            <a href="index.php#work-section" class="hover:text-gold transition-colors duration-300">Films</a>
            <a href="index.php#writing-section" class="hover:text-gold transition-colors duration-300">Writing & Photography</a>
            <a href="index.php#about-section" class="hover:text-gold transition-colors duration-300">About</a>
            <a href="index.php#contact-section" class="inline-block mt-4 text-center py-3 border border-cream/35 rounded-full hover:bg-cream hover:text-navy transition-colors duration-300 font-body text-[14px] uppercase tracking-widest font-bold">Contact</a>
        </div>
    </div>
</nav>
