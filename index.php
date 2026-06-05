<?php
// ============================================================
//  index.php — Public Homepage (Vertical Scroll Portfolio)
//  PHP Role: Fetches Live settings & Blog content from DB.
// ============================================================
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Fetch dynamic site config
$site_title  = get_setting('site_title', $conn, 'Bhavana Goparaju');
$meta_desc   = get_setting('meta_description', $conn, 'Bridging lived experience, collective memory, and future possibility.');

$page_title = $site_title;
$nav_theme = 'dark'; // Initial theme is dark for Hero section

// Fetch published blog posts for our creative modal reader
$blog_result = mysqli_query($conn, "SELECT id, title, slug, content, image_path, created_at FROM blog_posts WHERE status='published' ORDER BY created_at DESC");
$blog_posts  = mysqli_fetch_all($blog_result, MYSQLI_ASSOC);

mysqli_close($conn);

include_once 'includes/header.php';
?>

<!-- 
   Custom styles for contact textarea placeholder line-height to prevent overlapping/collapsing
-->
<style>
textarea.form-input-custom {
    line-height: 1.65 !important;
}
textarea.form-input-custom::placeholder {
    line-height: 1.65 !important;
}
</style>

<!-- ═══════════════════════════════════════════════════════════
     VERTICAL SCROLL CONTAINER
═══════════════════════════════════════════════════════════ -->
<div class="w-full flex flex-col min-h-screen">

    <!-- ─────────────────────────────────────────────────────────
         SECTION 1: SPLIT HERO (Navy/Green, Centered Portrait)
         Reference: Image 1 — Animated Canvas Networks
         ───────────────────────────────────────────────────────── -->
    <section id="hero-section" class="portfolio-section w-full h-screen flex flex-col justify-center bg-[#0a1628] relative overflow-hidden">
        
        <!-- Desktop Split Screen Background with Animated Canvas -->
        <div class="absolute inset-0 hidden lg:flex w-full h-full">
            <!-- Left Side: Navy + Animated Cyan Circuit Network -->
            <div class="w-1/2 h-full bg-[#0a1628] relative overflow-hidden">
                <canvas id="circuitCanvas" class="absolute inset-0 w-full h-full"></canvas>
            </div>
            <!-- Right Side: Dark Green + Animated Golden Root Network -->
            <div class="w-1/2 h-full bg-[#0c1813] relative overflow-hidden">
                <canvas id="rootCanvas" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        <!-- Mobile Background (Vertical Split with simpler canvas) -->
        <div class="absolute inset-0 lg:hidden w-full h-full flex flex-col">
            <div class="h-1/2 w-full bg-[#0a1628]"></div>
            <div class="h-1/2 w-full bg-[#0c1813]"></div>
        </div>

        <!-- Main Hero Content -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-6 h-screen flex flex-col items-center justify-center pt-8">
            <!-- Portrait: Square photo -->
            <div class="hero-anim flex-shrink-0 relative mb-8 lg:mb-10 flex items-center justify-center">
                <div class="relative w-52 h-52 md:w-60 md:h-60 lg:w-[290px] lg:h-[290px] rounded-[16px] overflow-hidden shadow-2xl">
                    <?php if (file_exists('assets/images/hero_avatar.png')): ?>
                        <img src="assets/images/hero_avatar.png" alt="Bhavana Goparaju" class="w-full h-full object-cover object-center">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-tr from-rust to-gold flex items-center justify-center text-white text-4xl font-display font-bold">BG</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Title -->
            <h2 class="hero-anim text-center font-display text-2xl md:text-4xl lg:text-[52px] lg:leading-[62px] font-bold text-cream max-w-4xl tracking-tight leading-snug px-4 mb-8 lg:mb-10">
                Bridging lived experience, <br class="hidden md:block">
                collective memory, <br class="hidden md:block">
                and future possibility.
            </h2>

            <!-- CTA Buttons -->
            <div class="hero-anim flex flex-wrap items-center justify-center gap-3 max-w-4xl px-4">
                <a href="#services-section" class="px-6 py-3 border-2 border-gold bg-gold text-[#0a1628] font-body font-bold text-xs tracking-widest uppercase rounded-full hover:bg-transparent hover:text-gold transition-all duration-300">
                    Work With Me
                </a>
                <a href="#contact-section" class="px-6 py-3 border-2 border-gold text-white font-body font-bold text-xs tracking-widest uppercase rounded-full hover:bg-gold hover:text-[#0a1628] transition-all duration-300">
                    Partner / Support
                </a>
                <a href="#contact-section" class="px-6 py-3 border-2 border-gold text-white font-body font-bold text-xs tracking-widest uppercase rounded-full hover:bg-gold hover:text-[#0a1628] transition-all duration-300">
                    Invite / Book
                </a>
                <a href="#work-section" class="px-6 py-3 border-2 border-gold text-white font-body font-bold text-xs tracking-widest uppercase rounded-full hover:bg-gold hover:text-[#0a1628] transition-all duration-300">
                    Explore My Work
                </a>
            </div>
        </div>

        <div class="absolute bottom-6 right-8 z-10 text-[9px] font-body tracking-widest text-white/30 uppercase">
            Homepage Concept
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════
         HERO BACKGROUND ANIMATION ENGINE
         Left: Cyan circuit network with IC chips, floating nodes, pulsing glow
         Right: Golden branching root system with drifting particles
    ═══════════════════════════════════════════════════════════ -->
    <script>
    (function() {
        // ── UTILITY ───────────────────────────────────────────────
        function rand(min, max) { return Math.random() * (max - min) + min; }

        // ── LEFT CANVAS: CYAN CIRCUIT NETWORK ─────────────────────
        const cCanvas = document.getElementById('circuitCanvas');
        if (!cCanvas) return;
        const cCtx = cCanvas.getContext('2d');

        function resizeCanvas(canvas) {
            canvas.width = canvas.parentElement.offsetWidth;
            canvas.height = canvas.parentElement.offsetHeight;
        }

        // IC Chip drawing helper
        function drawChip(ctx, x, y, w, h, color) {
            ctx.strokeStyle = color;
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.roundRect(x, y, w, h, 6);
            ctx.stroke();
            // Pins left (4 pins)
            var pinGap = h / 5;
            for (var i = 1; i <= 4; i++) {
                var py = y + pinGap * i;
                ctx.beginPath(); ctx.moveTo(x - 10, py); ctx.lineTo(x, py); ctx.stroke();
                ctx.beginPath(); ctx.moveTo(x + w, py); ctx.lineTo(x + w + 10, py); ctx.stroke();
            }
        }

        // Circuit node particles
        var cNodes = [];
        function initCircuitNodes(w, h) {
            cNodes = [];
            // IC chip positions (fixed, top area)
            var chips = [
                { x: w * 0.08, y: h * 0.08 },
                { x: w * 0.25, y: h * 0.08 },
                { x: w * 0.42, y: h * 0.08 },
                { x: w * 0.60, y: h * 0.08 }
            ];
            // Network nodes (floating)
            var numNodes = 28;
            for (var i = 0; i < numNodes; i++) {
                cNodes.push({
                    x: rand(30, w - 30),
                    y: rand(h * 0.20, h - 30),
                    baseX: 0, baseY: 0,
                    vx: rand(-0.15, 0.15),
                    vy: rand(-0.15, 0.15),
                    r: rand(2.5, 5),
                    pulse: rand(0, Math.PI * 2),
                    pulseSpeed: rand(0.008, 0.025)
                });
                cNodes[i].baseX = cNodes[i].x;
                cNodes[i].baseY = cNodes[i].y;
            }
            return chips;
        }

        var cChips = [];
        resizeCanvas(cCanvas);
        cChips = initCircuitNodes(cCanvas.width, cCanvas.height);

        function drawCircuit(time) {
            var w = cCanvas.width, h = cCanvas.height;
            cCtx.clearRect(0, 0, w, h);

            var cyan = '#00e5ff';

            // Draw IC chips
            var chipW = 70, chipH = 36;
            cChips.forEach(function(chip) {
                drawChip(cCtx, chip.x, chip.y, chipW, chipH, cyan);
            });

            // Draw connecting wires from chips downward
            cCtx.strokeStyle = cyan;
            cCtx.lineWidth = 1;
            cCtx.globalAlpha = 0.35;
            cChips.forEach(function(chip) {
                var cx = chip.x + chipW / 2;
                var cy = chip.y + chipH;
                // Find 2 closest network nodes and draw a line
                var sorted = cNodes.slice().sort(function(a, b) {
                    var da = Math.hypot(a.x - cx, a.y - cy);
                    var db = Math.hypot(b.x - cx, b.y - cy);
                    return da - db;
                });
                for (var j = 0; j < 2; j++) {
                    cCtx.beginPath();
                    cCtx.moveTo(cx, cy);
                    cCtx.lineTo(sorted[j].x, sorted[j].y);
                    cCtx.stroke();
                }
            });
            cCtx.globalAlpha = 1;

            // Update and draw network nodes
            cNodes.forEach(function(n, i) {
                n.pulse += n.pulseSpeed;
                n.x = n.baseX + Math.sin(n.pulse) * 8;
                n.y = n.baseY + Math.cos(n.pulse * 0.7) * 6;

                // Boundary wrap
                if (n.x < 10) n.baseX += 2;
                if (n.x > w - 10) n.baseX -= 2;
                if (n.y < h * 0.18) n.baseY += 2;
                if (n.y > h - 10) n.baseY -= 2;

                // Draw glow
                var glowR = n.r + 4 + Math.sin(n.pulse) * 2;
                var grad = cCtx.createRadialGradient(n.x, n.y, 0, n.x, n.y, glowR);
                grad.addColorStop(0, 'rgba(0,229,255,0.35)');
                grad.addColorStop(1, 'rgba(0,229,255,0)');
                cCtx.fillStyle = grad;
                cCtx.beginPath();
                cCtx.arc(n.x, n.y, glowR, 0, Math.PI * 2);
                cCtx.fill();

                // Draw node
                cCtx.fillStyle = cyan;
                cCtx.globalAlpha = 0.7 + Math.sin(n.pulse) * 0.3;
                cCtx.beginPath();
                cCtx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                cCtx.fill();
                cCtx.globalAlpha = 1;
            });

            // Draw connecting lines between nearby nodes
            var maxDist = 160;
            cCtx.lineWidth = 1;
            for (var i = 0; i < cNodes.length; i++) {
                for (var j = i + 1; j < cNodes.length; j++) {
                    var dx = cNodes[i].x - cNodes[j].x;
                    var dy = cNodes[i].y - cNodes[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < maxDist) {
                        var alpha = (1 - dist / maxDist) * 0.5;
                        cCtx.strokeStyle = 'rgba(0,229,255,' + alpha + ')';
                        cCtx.beginPath();
                        cCtx.moveTo(cNodes[i].x, cNodes[i].y);
                        cCtx.lineTo(cNodes[j].x, cNodes[j].y);
                        cCtx.stroke();
                    }
                }
            }
        }

        // ── RIGHT CANVAS: GOLDEN ROOT NETWORK ─────────────────────
        const rCanvas = document.getElementById('rootCanvas');
        const rCtx = rCanvas.getContext('2d');
        resizeCanvas(rCanvas);

        var rNodes = [];
        function initRootNodes(w, h) {
            rNodes = [];
            // Build a tree-like structure from top center downward
            var gold = '#dcae44';
            // Root apex
            var apex = { x: w * 0.5, y: h * 0.4 };
            // Generate branching nodes
            var levels = 5;
            var branches = [apex];
            rNodes.push({
                x: apex.x, y: apex.y,
                baseX: apex.x, baseY: apex.y,
                r: 5, pulse: 0, pulseSpeed: rand(0.01, 0.02),
                parent: -1
            });

            for (var lvl = 1; lvl <= levels; lvl++) {
                var newBranches = [];
                branches.forEach(function(parent) {
                    var numChildren = lvl < 3 ? 3 : 2;
                    for (var c = 0; c < numChildren; c++) {
                        var spreadX = rand(-50, 50) * (lvl * 0.45);
                        var spreadY = rand(30, 55);
                        var nx = parent.x + spreadX;
                        var ny = parent.y + spreadY;
                        // Clamp to canvas
                        nx = Math.max(40, Math.min(w - 40, nx));
                        ny = Math.min(h - 40, ny);

                        // Find parent index
                        var pIdx = rNodes.findIndex(function(n) { return n.baseX === parent.x && n.baseY === parent.y; });

                        rNodes.push({
                            x: nx, y: ny,
                            baseX: nx, baseY: ny,
                            r: Math.max(2, 5 - lvl * 0.4),
                            pulse: rand(0, Math.PI * 2),
                            pulseSpeed: rand(0.008, 0.02),
                            parent: pIdx
                        });
                        newBranches.push({ x: nx, y: ny });
                    }
                });
                branches = newBranches;
            }
        }

        initRootNodes(rCanvas.width, rCanvas.height);

        function drawRoots(time) {
            var w = rCanvas.width, h = rCanvas.height;
            rCtx.clearRect(0, 0, w, h);
            var gold = '#dcae44';

            // Update positions with gentle drift
            rNodes.forEach(function(n) {
                n.pulse += n.pulseSpeed;
                n.x = n.baseX + Math.sin(n.pulse) * 4;
                n.y = n.baseY + Math.cos(n.pulse * 0.6) * 3;
            });

            // Draw branch lines (parent to child)
            rCtx.lineWidth = 1.3;
            rCtx.lineCap = 'round';
            rNodes.forEach(function(n) {
                if (n.parent >= 0 && n.parent < rNodes.length) {
                    var p = rNodes[n.parent];
                    var dist = Math.hypot(n.x - p.x, n.y - p.y);
                    var alpha = Math.max(0.15, 0.55 - dist / 600);
                    rCtx.strokeStyle = 'rgba(220,174,68,' + alpha + ')';
                    rCtx.beginPath();
                    rCtx.moveTo(p.x, p.y);
                    // Gentle curve
                    var mx = (p.x + n.x) / 2 + Math.sin(n.pulse) * 3;
                    var my = (p.y + n.y) / 2;
                    rCtx.quadraticCurveTo(mx, my, n.x, n.y);
                    rCtx.stroke();
                }
            });

            // Draw nodes with glow
            rNodes.forEach(function(n) {
                // Glow
                var glowR = n.r + 5 + Math.sin(n.pulse) * 2;
                var grad = rCtx.createRadialGradient(n.x, n.y, 0, n.x, n.y, glowR);
                grad.addColorStop(0, 'rgba(220,174,68,0.3)');
                grad.addColorStop(1, 'rgba(220,174,68,0)');
                rCtx.fillStyle = grad;
                rCtx.beginPath();
                rCtx.arc(n.x, n.y, glowR, 0, Math.PI * 2);
                rCtx.fill();

                // Solid node
                rCtx.fillStyle = gold;
                rCtx.globalAlpha = 0.65 + Math.sin(n.pulse) * 0.35;
                rCtx.beginPath();
                rCtx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                rCtx.fill();
                rCtx.globalAlpha = 1;
            });
        }

        // ── ANIMATION LOOP ────────────────────────────────────────
        function animate(time) {
            drawCircuit(time);
            drawRoots(time);
            requestAnimationFrame(animate);
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            resizeCanvas(cCanvas);
            cChips = initCircuitNodes(cCanvas.width, cCanvas.height);
            resizeCanvas(rCanvas);
            initRootNodes(rCanvas.width, rCanvas.height);
        });

        // Start animation
        requestAnimationFrame(animate);
    })();
    </script>

    <!-- ─────────────────────────────────────────────────────────
         SECTION 2: ABOUT / INTRODUCTION (Cream, tight content height cards)
         Reference: Image 2
         ───────────────────────────────────────────────────────── -->
    <section id="about-section" class="portfolio-section w-full lg:h-screen min-h-screen flex flex-col justify-center bg-cream relative py-16 lg:py-0 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto w-full">
            <!-- <p class="slide-anim font-body text-[11px] tracking-[0.2em] font-bold text-rust uppercase mb-4">
                What I Do
            </p> -->
            <h2 class="slide-anim font-display text-3xl md:text-5xl lg:text-[54px] font-semibold tracking-tight text-charcoal leading-tight max-w-4xl mb-6">
                Stories carry memory. <br>
                <span class="text-rust">Systems shape possibility.</span>
            </h2>
            <p class="slide-anim font-body text-sm md:text-base text-charcoal/80 leading-relaxed max-w-3xl mb-12">
                I help people and organizations make sense of complexity - translating research, data, lived experience, and cultural knowledge into stories, systems, and programs that strengthen relationships, deepen understanding, and create lasting impact.
            </p>

            <!-- Small Size Cards (photo box height matched) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="slide-anim rounded-[24px] bg-rust text-cream p-6 flex flex-col justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-500">
                    <div>
                        <span class="font-body text-xs text-cream/60 font-bold block mb-2">01</span>
                        <h3 class="font-display text-xl font-bold mb-3">Shape a Story</h3>
                        <p class="font-body text-xs md:text-sm text-cream/90 leading-relaxed">
                            For filmmakers, artists, founders, and organizations clarifying the narrative thread inside complex work.
                        </p>
                    </div>
                    <a href="#services-section" class="inline-flex items-center gap-1 font-body text-[9px] font-bold tracking-widest uppercase mt-6 hover:text-gold transition-colors">
                        Learn More <span>→</span>
                    </a>
                </div>

                <div class="slide-anim rounded-[24px] bg-olive text-cream p-6 flex flex-col justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-500">
                    <div>
                        <span class="font-body text-xs text-cream/60 font-bold block mb-2">02</span>
                        <h3 class="font-display text-xl font-bold mb-3">Design a Program</h3>
                        <p class="font-body text-xs md:text-sm text-cream/90 leading-relaxed">
                            For organizations building community-centered programs, partnerships, and funding pathways.
                        </p>
                    </div>
                    <a href="#services-section" class="inline-flex items-center gap-1 font-body text-[9px] font-bold tracking-widest uppercase mt-6 hover:text-gold transition-colors">
                        Learn More <span>→</span>
                    </a>
                </div>

                <div class="slide-anim rounded-[24px] bg-deepslate text-cream p-6 flex flex-col justify-between shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-500">
                    <div>
                        <span class="font-body text-xs text-cream/60 font-bold block mb-2">03</span>
                        <h3 class="font-display text-xl font-bold mb-3">Map a System</h3>
                        <p class="font-body text-xs md:text-sm text-cream/90 leading-relaxed">
                            For teams using data, AI, research, and evaluation to understand relationships and make decisions.
                        </p>
                    </div>
                    <a href="#services-section" class="inline-flex items-center gap-1 font-body text-[9px] font-bold tracking-widest uppercase mt-6 hover:text-gold transition-colors">
                        Learn More <span>→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────────────────────
         SECTION 3: MY WORK / PROJECTS (Cream, 3 cards side-by-side)
         Reference: Image 3
         ───────────────────────────────────────────────────────── -->
    <section id="work-section" class="portfolio-section w-full lg:h-screen min-h-screen flex flex-col justify-center bg-cream relative py-16 lg:py-0 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto w-full">
            <!-- <p class="slide-anim font-body text-[11px] tracking-[0.2em] font-bold text-rust uppercase mb-4">
                Portfolio
            </p> -->
            <h2 class="slide-anim font-display text-3xl md:text-5xl font-semibold tracking-tight text-charcoal mb-3">
                Explore my work.
            </h2>
            <p class="slide-anim font-body text-xs md:text-sm text-charcoal/70 max-w-xl mb-12">
                Three pathways hold the body of work: story, systems, and cultural practice.
            </p>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="slide-anim flex flex-col rounded-[20px] overflow-hidden shadow-sm bg-white hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="zoom-hover h-[180px] md:h-[220px] relative bg-neutral-200">
                        <?php if (file_exists('assets/images/jeevi_films.png')): ?>
                            <img src="assets/images/jeevi_films.png" alt="Jeevi Films" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="bg-rust p-6 flex-grow text-cream">
                        <h3 class="font-display text-xl font-bold mb-2">Jeevi Films</h3>
                        <p class="font-body text-xs text-cream/80 leading-relaxed">
                            Stories that witness, question, and connect. Focus on cinematic storytelling.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="slide-anim flex flex-col rounded-[20px] overflow-hidden shadow-sm bg-white hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="zoom-hover h-[180px] md:h-[220px] relative bg-neutral-200">
                        <?php if (file_exists('assets/images/myreelium.png')): ?>
                            <img src="assets/images/myreelium.png" alt="MyReelium" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="bg-deepslate p-6 flex-grow text-cream">
                        <h3 class="font-display text-xl font-bold mb-2">MyReelium</h3>
                        <p class="font-body text-xs text-cream/80 leading-relaxed">
                            Distribution intelligence for independent films and community pathways.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="slide-anim flex flex-col rounded-[20px] overflow-hidden shadow-sm bg-white hover:shadow-lg transition-all duration-500 hover:-translate-y-1">
                    <div class="zoom-hover h-[180px] md:h-[220px] relative bg-neutral-200">
                        <?php if (file_exists('assets/images/jeevi_collective.png')): ?>
                            <img src="assets/images/jeevi_collective.png" alt="Jeevi Collective" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <div class="bg-olive p-6 flex-grow text-cream">
                        <h3 class="font-display text-xl font-bold mb-2">Jeevi Collective</h3>
                        <p class="font-body text-xs text-cream/80 leading-relaxed">
                            Programs, dialogue, workshops, and cultural practice. Designing creative spaces.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────────────────────
         SECTION 4: WORK WITH ME (Dark Navy, 5 Offerings)
         Reference: Image 4
         ───────────────────────────────────────────────────────── -->
    <section id="services-section" class="portfolio-section w-full lg:h-screen min-h-screen flex flex-col justify-center bg-[#08111e] text-cream relative py-16 lg:py-0 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto w-full">
            <!-- <p class="slide-anim font-body text-[11px] tracking-[0.2em] font-bold text-gold uppercase mb-4">
                Services &amp; Collaboration
            </p> -->
            <h2 class="slide-anim font-display text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-4 leading-tight">
                Bring a story, system, program, or question.
            </h2>
            <p class="slide-anim font-body text-sm md:text-base text-cream/70 leading-relaxed max-w-3xl mb-12">
                Work with me when the pieces are important, complex, and not yet fully connected.
            </p>

            <!-- Offering blocks -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                <!-- Box 1 -->
                <div class="slide-anim bg-[#0c1a2c] border border-white/10 p-8 rounded-[20px] shadow-sm hover:border-gold/30 hover:bg-[#112239] transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">01</span>
                        <h3 class="font-display text-xl md:text-2xl lg:text-[26px] lg:leading-[34px] font-semibold text-white">Story &amp; Narrative Strategy</h3>
                    </div>
                </div>
                <!-- Box 2 -->
                <div class="slide-anim bg-[#0c1a2c] border border-white/10 p-8 rounded-[20px] shadow-sm hover:border-gold/30 hover:bg-[#112239] transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">02</span>
                        <h3 class="font-display text-xl md:text-2xl lg:text-[26px] lg:leading-[34px] font-semibold text-white">Program &amp; Community Infrastructure Design</h3>
                    </div>
                </div>
                <!-- Box 3 -->
                <div class="slide-anim bg-[#0c1a2c] border border-white/10 p-8 rounded-[20px] shadow-sm hover:border-gold/30 hover:bg-[#112239] transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">03</span>
                        <h3 class="font-display text-xl md:text-2xl lg:text-[26px] lg:leading-[34px] font-semibold text-white">Data, Research &amp; Systems Strategy</h3>
                    </div>
                </div>
                <!-- Box 4 -->
                <div class="slide-anim bg-[#0c1a2c] border border-white/10 p-8 rounded-[20px] shadow-sm hover:border-gold/30 hover:bg-[#112239] transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">04</span>
                        <h3 class="font-display text-xl md:text-2xl lg:text-[26px] lg:leading-[34px] font-semibold text-white">Producing &amp; Creative Leadership</h3>
                    </div>
                </div>
                <!-- Box 5 -->
                <div class="slide-anim bg-[#0c1a2c] border border-white/10 p-8 rounded-[20px] shadow-sm hover:border-gold/30 hover:bg-[#112239] transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">05</span>
                        <h3 class="font-display text-xl md:text-2xl lg:text-[26px] lg:leading-[34px] font-semibold text-white">Speaking, Teaching &amp; Facilitation</h3>
                    </div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="slide-anim flex flex-wrap items-center gap-3">
                <a href="#contact-section" class="px-6 py-2.5 border-2 border-gold bg-gold text-navy font-body font-bold text-[10px] tracking-widest uppercase rounded-full hover:bg-transparent hover:text-gold transition-all duration-300">
                    Work With Me
                </a>
                <a href="#contact-section" class="px-6 py-2.5 border-2 border-gold text-cream font-body font-bold text-[10px] tracking-widest uppercase rounded-full hover:bg-gold hover:text-navy transition-all duration-300">
                    Invite / Book
                </a>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────────────────────
         SECTION 5: WRITING & PHOTOGRAPHY (Split Layout Visual)
         Reference: Image 5
         ───────────────────────────────────────────────────────── -->
    <section id="writing-section" class="portfolio-section w-full lg:h-screen min-h-screen flex flex-col bg-cream relative overflow-hidden">
        <div class="w-full lg:h-full flex-grow grid grid-cols-1 lg:grid-cols-2">
            <!-- Left Side Content -->
            <div class="pl-8 md:pl-16 lg:pl-24 pr-6 md:pr-8 lg:pr-8 py-16 flex flex-col justify-center bg-cream h-full">
                <!-- Double grid images -->
                <div class="grid grid-cols-2 gap-6 mb-8 w-full max-w-2xl">
                    <div class="aspect-square rounded-xl overflow-hidden shadow-sm bg-neutral-200">
                        <?php if (file_exists('assets/images/writing_bricks.png')): ?>
                            <img src="assets/images/writing_bricks.png" alt="Bricks Workers" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        <?php endif; ?>
                    </div>
                    <div class="aspect-square rounded-xl overflow-hidden shadow-sm bg-neutral-200">
                        <?php if (file_exists('assets/images/writing_camera.png')): ?>
                            <img src="assets/images/writing_camera.png" alt="Smiling Photographer" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Text -->
                <h2 class="slide-anim font-display text-4xl md:text-5xl lg:text-[56px] lg:leading-[64px] font-bold text-charcoal mb-6 tracking-tight">
                    Writing, photography, <br class="hidden md:block">and visual inquiry.
                </h2>
                <p class="slide-anim font-body text-sm md:text-lg text-charcoal/80 leading-relaxed max-w-xl mb-8">
                    Essays, poetry, images, and process notes from the places where story, memory, land, and systems meet.
                </p>

                <!-- Button triggers sleek full-screen blog list modal -->
                <div class="slide-anim">
                    <button onclick="openBlogModal()" class="px-8 py-3.5 bg-rust text-white font-body font-bold text-xs tracking-widest uppercase rounded-full hover:bg-charcoal transition-all duration-300 shadow-md">
                        Read / View
                    </button>
                </div>
            </div>

            <!-- Right Side Landscape Image -->
            <div class="relative h-[50vh] lg:h-auto min-h-[320px] lg:min-h-0 bg-neutral-200">
                <?php if (file_exists('assets/images/writing_forest.png')): ?>
                    <img src="assets/images/writing_forest.png" alt="Boy in Forest" class="absolute inset-0 w-full h-full object-cover">
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ─────────────────────────────────────────────────────────
         SECTION 6: CONTACT (Dark Forest Green, outlined form & photo)
         Reference: Image 6
         ───────────────────────────────────────────────────────── -->
    <section id="contact-section" class="portfolio-section w-full lg:h-screen min-h-screen flex flex-col justify-center bg-[#223525] text-cream relative py-16 lg:py-0 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6 items-center">
            
            <!-- Form -->
            <div class="lg:col-span-6 pr-0 lg:pr-8">
                <h2 class="slide-anim font-display text-4xl md:text-5xl font-semibold tracking-tight text-white mb-4 leading-tight">
                    Start a conversation.
                </h2>
                <p class="slide-anim font-body text-sm md:text-base text-cream/80 leading-relaxed max-w-lg mb-8">
                    For consulting, partnerships, support, speaking, teaching, producing, story strategy, cultural programming, or systems design inquiries.
                </p>

                <div id="contactFormBox" class="slide-anim space-y-4 max-w-xl">
                    <input type="hidden" id="csrfToken" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    
                    <div>
                        <input type="text" id="name" class="w-full px-4 py-3 border border-cream/20 rounded-xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm md:text-base transition-all duration-300 placeholder-cream/50" placeholder="Your name" required>
                    </div>
                    <div>
                        <input type="email" id="email" class="w-full px-4 py-3 border border-cream/20 rounded-xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm md:text-base transition-all duration-300 placeholder-cream/50" placeholder="Email address" required>
                    </div>
                    <div>
                        <!-- Textarea leading-relaxed class ensures placeholder lines never overlap -->
                        <textarea id="message" rows="4" class="form-input-custom w-full px-4 py-3 border border-cream/20 rounded-xl bg-black/10 focus:bg-black/25 focus:border-gold/60 focus:outline-none text-cream text-sm md:text-base transition-all duration-300 placeholder-cream/50 resize-y" placeholder="Tell me what you're building, trying to understand, or reaching out about." required></textarea>
                    </div>

                    <div class="flex flex-col gap-3 pt-2">
                        <button onclick="submitContactForm()" class="px-6 py-3 bg-[#dcae44] text-[#1b2b1d] hover:bg-[#c39736] font-body font-bold text-xs tracking-[0.2em] uppercase rounded-full transition-all duration-300 self-start shadow-md">
                            Send Inquiry
                        </button>
                        <div id="alertBanner" class="hidden px-4 py-2.5 rounded-xl font-body text-xs md:text-sm leading-relaxed border transition-all duration-300"></div>
                    </div>
                </div>
            </div>

            <!-- Vertical Crew Image Visual -->
            <div class="lg:col-span-6 flex justify-center lg:justify-start">
                <div class="w-full max-w-[480px] aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl bg-neutral-900 slide-anim">
                    <?php if (file_exists('assets/images/contact_shoot.png')): ?>
                        <img src="assets/images/contact_shoot.png" alt="Film Shoot Team" class="w-full h-full object-cover">
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

</div>

<!-- ═══════════════════════════════════════════════════════════
     SLEEK FULL-SCREEN BLOG FEED MODAL (Blur background overlay)
═══════════════════════════════════════════════════════════ -->
<div id="blogModal" class="fixed inset-0 z-50 hidden bg-navy/60 backdrop-blur-xl transition-all duration-500 flex items-center justify-center p-4 md:p-8">
    
    <!-- Modal Dialog -->
    <div class="bg-cream rounded-[32px] w-full max-w-4xl h-[85vh] shadow-2xl flex flex-col overflow-hidden border border-black/5 relative transform scale-95 opacity-0 transition-all duration-500" id="blogModalContent">
        
        <!-- Modal Header -->
        <div class="p-6 md:px-10 border-b border-black/5 flex items-center justify-between bg-cream/50 backdrop-blur-md">
            <h2 class="font-display text-xl md:text-2xl font-bold text-charcoal" id="modalHeadingTitle">Creative Portfolio Writing</h2>
            <button onclick="closeBlogModal()" class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-charcoal hover:bg-rust hover:text-white transition-colors duration-300 text-lg font-bold">✕</button>
        </div>

        <!-- Modal Body (Dynamic Scroll Area) -->
        <div class="p-6 md:p-10 flex-grow overflow-y-auto" id="modalBodyContainer">
            
            <!-- VIEW A: ARTICLES LIST -->
            <div id="modalArticleList" class="space-y-10">
                <?php if (empty($blog_posts)): ?>
                    <div class="text-center py-16 text-charcoal/40 font-body text-xs bg-black/5 rounded-2xl p-6 border border-dashed border-charcoal/10">
                        <p class="mb-2 font-bold">No published essays available.</p>
                        <p>Publish blogs from the Admin Dashboard to read them here.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <?php foreach ($blog_posts as $post): ?>
                            <article class="flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                                <?php if (!empty($post['image_path']) && file_exists($post['image_path'])): ?>
                                    <div class="h-44 bg-neutral-100 overflow-hidden relative">
                                        <img src="<?= sanitize($post['image_path']) ?>" alt="<?= sanitize($post['title']) ?>" class="w-full h-full object-cover">
                                    </div>
                                <?php endif; ?>
                                <div class="p-6 flex-grow flex flex-col justify-between">
                                    <div>
                                        <span class="text-[10px] text-charcoal/50 uppercase tracking-widest font-bold block mb-2"><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                                        <h3 class="font-display text-lg font-bold text-charcoal mb-3"><?= sanitize($post['title']) ?></h3>
                                        <p class="font-body text-xs text-charcoal/70 leading-relaxed line-clamp-3 mb-4">
                                            <?= strip_tags(substr($post['content'], 0, 180)) ?>...
                                        </p>
                                    </div>
                                    <button onclick="readSingleArticle(<?= htmlspecialchars(json_encode($post)) ?>)" class="text-left font-body text-[10px] font-bold tracking-widest text-rust uppercase hover:text-charcoal transition-colors">
                                        Read Post →
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- VIEW B: SINGLE READER (Hidden initially) -->
            <div id="modalArticleReader" class="hidden max-w-2xl mx-auto py-4">
                <button onclick="backToModalList()" class="inline-flex items-center gap-1 font-body text-[10px] font-bold tracking-widest text-rust uppercase hover:text-charcoal transition-colors mb-6">
                    ← Back to Writing List
                </button>
                <p class="font-body text-[10px] text-charcoal/50 uppercase tracking-widest block mb-2" id="readerDate"></p>
                <h1 class="font-display text-2xl md:text-4xl font-bold text-charcoal mb-6 leading-tight" id="readerTitle"></h1>
                <div class="rounded-xl overflow-hidden shadow-sm mb-6 max-h-[300px] w-full hidden" id="readerImgContainer">
                    <img src="" id="readerImg" class="w-full h-full object-cover">
                </div>
                <div class="font-body text-xs md:text-sm text-charcoal/80 leading-relaxed space-y-4 prose" id="readerContent"></div>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     JS: VERTICAL OBSERVER & THEME SWITCHER
═══════════════════════════════════════════════════════════ -->
<script>
// Slide Theme Map
const slideThemes = {
    'hero-section': 'dark',
    'about-section': 'light',
    'work-section': 'light',
    'services-section': 'dark',
    'writing-section': 'light',
    'contact-section': 'dark'
};

// Scroll observer to toggle Navbar Theme dynamically
window.addEventListener('load', () => {
    
    // Intersection Observer setup
    const observerOptions = {
        root: null, // viewport
        threshold: 0.35 // triggers when 35% of section is visible
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = entry.target.id;
                const theme = slideThemes[sectionId];
                if (window.updateNavbarTheme) {
                    window.updateNavbarTheme(theme, sectionId);
                }
                
                // Trigger entry transitions for elements inside slide
                gsap.to(entry.target.querySelectorAll('.slide-anim, .hero-anim'), {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    stagger: 0.12,
                    ease: 'power2.out'
                });
            }
        });
    }, observerOptions);

    // Track all slides
    document.querySelectorAll('.portfolio-section').forEach(section => {
        // Set initial hidden anim state
        gsap.set(section.querySelectorAll('.slide-anim, .hero-anim'), { opacity: 0, y: 25 });
        observer.observe(section);
    });
});

// Dynamic Navbar Theme Switcher (Called by Observer)
window.updateNavbarTheme = function(theme, sectionId) {
    const nav = document.querySelector('nav');
    if (!nav) return;
    
    if (sectionId === 'hero-section') {
        nav.classList.add('opacity-0', 'pointer-events-none');
        nav.classList.remove('opacity-100');
        return;
    } else {
        nav.classList.remove('opacity-0', 'pointer-events-none');
        nav.classList.add('opacity-100');
    }
    
    const logo = nav.querySelector('.logo-circle');
    const navLinks = nav.querySelectorAll('.hidden.md\\:flex a');
    const contactBtn = nav.querySelector('.nav-contact');
    const hamburgerSpans = document.querySelectorAll('#mobileMenuBtn span');

    if (theme === 'dark') {
        nav.className = 'fixed top-0 left-0 w-full z-50 px-6 py-4 md:px-12 flex items-center justify-between transition-all duration-300 bg-transparent border-b border-white/10';
        if (logo) logo.className = 'w-10 h-10 rounded-full border-2 border-cream text-cream flex items-center justify-center font-display font-bold text-sm tracking-wider transition-transform duration-300 hover:scale-105';
        navLinks.forEach(link => {
            if (!link.classList.contains('nav-contact')) {
                link.className = 'text-cream/80 hover:text-gold transition-colors duration-300';
            }
        });
        if (contactBtn) {
            contactBtn.className = 'nav-contact ml-4 px-5 py-2 border rounded-full border-cream/40 text-cream hover:bg-cream hover:text-navy transition-all duration-300 font-bold';
        }
        hamburgerSpans.forEach(span => {
            span.className = 'w-full h-[2px] bg-cream transition-all duration-300 origin-left';
        });
    } else {
        nav.className = 'fixed top-0 left-0 w-full z-50 px-6 py-4 md:px-12 flex items-center justify-between transition-all duration-300 bg-transparent border-b border-black/5';
        if (logo) logo.className = 'w-10 h-10 rounded-full border-2 border-charcoal text-charcoal flex items-center justify-center font-display font-bold text-sm tracking-wider transition-transform duration-300 hover:scale-105';
        navLinks.forEach(link => {
            if (!link.classList.contains('nav-contact')) {
                link.className = 'text-charcoal/80 hover:text-rust transition-colors duration-300';
            }
        });
        if (contactBtn) {
            contactBtn.className = 'nav-contact ml-4 px-5 py-2 border rounded-full border-charcoal/40 text-charcoal hover:bg-charcoal hover:text-cream transition-all duration-300 font-bold';
        }
        hamburgerSpans.forEach(span => {
            span.className = 'w-full h-[2px] bg-charcoal transition-all duration-300 origin-left';
        });
    }
};

// Smooth Scrolling Navigation click handler
function scrollToSection(id) {
    const target = document.getElementById(id);
    if (!target) return;
    target.scrollIntoView({ behavior: 'smooth' });
}

// Intercept Navbar Anchor Click Events
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', (e) => {
        const href = link.getAttribute('href');
        if (href.startsWith('index.php#') || href.startsWith('#')) {
            const id = href.substring(href.indexOf('#') + 1);
            const target = document.getElementById(id);
            if (target) {
                e.preventDefault();
                scrollToSection(id);
                // Close mobile drawer if active
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                if (mobileMenuBtn && window.menuOpen) {
                    mobileMenuBtn.click();
                }
            }
        }
    });
});

// ── SLEEK MODAL CONTROLLER ────────────────────────────────────
function openBlogModal() {
    const modal = document.getElementById('blogModal');
    const content = document.getElementById('blogModalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    backToModalList(); // start in list view
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 50);
}

function closeBlogModal() {
    const modal = document.getElementById('blogModal');
    const content = document.getElementById('blogModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }, 500);
}

function readSingleArticle(post) {
    document.getElementById('modalArticleList').classList.add('hidden');
    document.getElementById('modalArticleReader').classList.remove('hidden');
    
    // Set reader content
    document.getElementById('modalHeadingTitle').textContent = 'Viewing Article';
    document.getElementById('readerTitle').textContent = post.title;
    document.getElementById('readerDate').textContent = 'Published: ' + new Date(post.created_at).toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'});
    
    const contentBox = document.getElementById('readerContent');
    contentBox.innerHTML = post.content.replace(/\n/g, '<br>');

    // Optional image loading
    const imgContainer = document.getElementById('readerImgContainer');
    const img = document.getElementById('readerImg');
    if (post.image_path) {
        img.src = post.image_path;
        imgContainer.classList.remove('hidden');
    } else {
        imgContainer.classList.add('hidden');
    }
    
    // Scroll modal container to top
    document.getElementById('modalBodyContainer').scrollTop = 0;
}

function backToModalList() {
    document.getElementById('modalArticleReader').classList.add('hidden');
    document.getElementById('modalArticleList').classList.remove('hidden');
    document.getElementById('modalHeadingTitle').textContent = 'Creative Portfolio Writing';
}

// Close modal on background click
document.getElementById('blogModal').addEventListener('click', (e) => {
    if (e.target.id === 'blogModal') {
        closeBlogModal();
    }
});

// ── AJAX CONTACT FORM SUBMISSION ──────────────────────────────
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

    if (!name || !email || !message) {
        showAlert('Please fill in all required fields.', 'error');
        return;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showAlert('Please enter a valid email address.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('message', message);
    formData.append('csrf_token', csrfToken);

    try {
        const response = await fetch('contact_submit.php', { method: 'POST', body: formData });
        const result = await response.json();

        if (result.success) {
            showAlert('Your inquiry has been successfully sent. Thank you!', 'success');
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
        alertBanner.className = 'px-4 py-2.5 rounded-xl font-body text-[11px] leading-relaxed border bg-green-900/30 text-green-200 border-green-700/50 block';
    } else {
        alertBanner.className = 'px-4 py-2.5 rounded-xl font-body text-[11px] leading-relaxed border bg-red-950/30 text-red-200 border-red-900/50 block';
    }
}
</script>

<?php
include_once 'includes/footer.php';
?>
