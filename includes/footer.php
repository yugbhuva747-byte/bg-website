<?php
// ============================================================
//  includes/footer.php — Shared Footer (Scripts Only)
//  PHP Role: Central script loading and scroll animations setup.
// ============================================================
?>

<!-- ═══════════════════════════════════════════════════════════
     GLOBAL JAVASCRIPT
═══════════════════════════════════════════════════════════ -->
<script>
// ── MOBILE NAVBAR DRAWER TOGGLE ──────────────────────────────
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileDrawer = document.getElementById('mobileDrawer');
let menuOpen = false;

if (mobileMenuBtn && mobileDrawer) {
    mobileMenuBtn.addEventListener('click', () => {
        menuOpen = !menuOpen;
        if (menuOpen) {
            mobileDrawer.classList.remove('translate-x-full');
            // Animate lines to form 'X'
            mobileMenuBtn.children[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            mobileMenuBtn.children[1].style.opacity = '0';
            mobileMenuBtn.children[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
        } else {
            mobileDrawer.classList.add('translate-x-full');
            // Restore hamburger lines
            mobileMenuBtn.children[0].style.transform = 'none';
            mobileMenuBtn.children[1].style.opacity = '1';
            mobileMenuBtn.children[2].style.transform = 'none';
        }
    });
}

// Close drawer on click of any drawer link
const drawerLinks = mobileDrawer ? mobileDrawer.querySelectorAll('a') : [];
drawerLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (menuOpen) {
            mobileMenuBtn.click();
        }
    });
});

// ── GSAP GLOBAL ENTRANCE & SCROLL TRIGGER ANIMATIONS ─────────
window.addEventListener('load', () => {
    // Check if GSAP is loaded before attempting scripts
    if (typeof gsap === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);

    // 1. Generic Fade In Up animations for components
    const fadeElements = gsap.utils.toArray('.gsap-fade-in');
    fadeElements.forEach((el) => {
        gsap.to(el, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true
            }
        });
    });

    // 2. Generic Slide in from Left animations
    const slideLeftElements = gsap.utils.toArray('.gsap-slide-left');
    slideLeftElements.forEach((el) => {
        gsap.to(el, {
            opacity: 1,
            x: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true
            }
        });
    });

    // 3. Generic Slide in from Right animations
    const slideRightElements = gsap.utils.toArray('.gsap-slide-right');
    slideRightElements.forEach((el) => {
        gsap.to(el, {
            opacity: 1,
            x: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true
            }
        });
    });

    // 4. Staggered reveal for services/portfolio grids
    const staggerGrids = gsap.utils.toArray('.gsap-stagger-grid');
    staggerGrids.forEach((grid) => {
        const items = grid.children;
        gsap.to(items, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: grid,
                start: 'top 80%',
                once: true
            }
        });
        // Ensure default styling is overridden nicely
        gsap.set(items, { opacity: 0, y: 30 });
    });
});
</script>
</body>
</html>
