<?php
// ============================================================
//  work.php — Portfolio Page
//  PHP Role: Renders the portfolio page showcasing projects.
// ============================================================
require_once 'includes/db.php';
require_once 'includes/auth.php';

$site_title  = get_setting('site_title', $conn, 'Bhavana Goparaju');
$meta_desc   = get_setting('meta_description', $conn, 'Explore the portfolio pathways of story, systems, and cultural practice.');

$page_title = 'Explore My Work — ' . $site_title;
$nav_theme = 'light'; // Light navigation for light cream background

mysqli_close($conn);

include_once 'includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
     WORK SHOWCASE SECTION
═══════════════════════════════════════════════════════════ -->
<section class="bg-cream py-16 px-6 md:py-24 md:px-12 flex-grow">
    <div class="max-w-6xl mx-auto">
        <!-- Eyebrow -->
        <p class="gsap-slide-left font-body text-[11px] tracking-[0.2em] font-bold text-rust uppercase mb-4">
            Portfolio
        </p>

        <!-- Page Headline -->
        <h1 class="gsap-slide-left font-display text-4xl md:text-6xl font-semibold tracking-tight text-charcoal mb-4">
            Explore my work.
        </h1>

        <!-- Page Subtitle -->
        <p class="gsap-fade-in font-body text-base md:text-lg text-charcoal/70 leading-relaxed max-w-2xl mb-12">
            Three pathways hold the body of work: story, systems, and cultural practice.
        </p>

        <!-- Category Filters -->
        <div class="gsap-fade-in flex flex-wrap gap-3 mb-12 font-body text-xs font-bold tracking-widest uppercase">
            <button onclick="filterWork('all')" id="filter-all" class="filter-btn active px-5 py-2.5 rounded-full border border-rust bg-rust text-cream transition-all duration-300">
                All Work
            </button>
            <button onclick="filterWork('films')" id="filter-films" class="filter-btn px-5 py-2.5 rounded-full border border-charcoal/20 hover:border-rust hover:text-rust text-charcoal/70 transition-all duration-300">
                Films
            </button>
            <button onclick="filterWork('systems')" id="filter-systems" class="filter-btn px-5 py-2.5 rounded-full border border-charcoal/20 hover:border-rust hover:text-rust text-charcoal/70 transition-all duration-300">
                Systems &amp; Programs
            </button>
        </div>

        <!-- Portfolio Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8" id="workGrid">
            
            <!-- Card 1: Jeevi Films -->
            <div class="work-item-card flex flex-col rounded-[24px] overflow-hidden shadow-md bg-white hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5" data-category="films">
                <!-- Image Container with zoom effect -->
                <div class="zoom-hover h-[240px] relative bg-neutral-200">
                    <?php if (file_exists('assets/images/jeevi_films.png')): ?>
                        <img src="assets/images/jeevi_films.png" alt="Jeevi Films" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-charcoal/30">No Image Available</div>
                    <?php endif; ?>
                </div>
                <!-- Terracotta Bottom Banner -->
                <div class="bg-rust p-6 md:p-8 flex-grow flex flex-col justify-between text-cream">
                    <div>
                        <h3 class="font-display text-2xl font-bold mb-3">Jeevi Films</h3>
                        <p class="font-body text-xs md:text-sm text-cream/80 leading-relaxed">
                            Stories that witness, question, and connect. Focus on cinematic storytelling that uncovers cultural memory.
                        </p>
                    </div>
                    <div class="mt-6">
                        <a href="contact.php?inquiry=jeevi-films" class="inline-flex items-center gap-2 font-body text-[10px] tracking-widest uppercase hover:text-gold transition-colors duration-300 font-bold">
                            Inquire Project <span class="text-[12px]">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: MyReelium -->
            <div class="work-item-card flex flex-col rounded-[24px] overflow-hidden shadow-md bg-white hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5" data-category="films">
                <!-- Image Container with zoom effect -->
                <div class="zoom-hover h-[240px] relative bg-neutral-200">
                    <?php if (file_exists('assets/images/myreelium.png')): ?>
                        <img src="assets/images/myreelium.png" alt="MyReelium" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-charcoal/30">No Image Available</div>
                    <?php endif; ?>
                </div>
                <!-- Slate Blue Bottom Banner -->
                <div class="bg-deepslate p-6 md:p-8 flex-grow flex flex-col justify-between text-cream">
                    <div>
                        <h3 class="font-display text-2xl font-bold mb-3">MyReelium</h3>
                        <p class="font-body text-xs md:text-sm text-cream/80 leading-relaxed">
                            Distribution intelligence for independent films and community pathways. Connecting independent media with local audiences.
                        </p>
                    </div>
                    <div class="mt-6">
                        <a href="contact.php?inquiry=myreelium" class="inline-flex items-center gap-2 font-body text-[10px] tracking-widest uppercase hover:text-gold transition-colors duration-300 font-bold">
                            Inquire Project <span class="text-[12px]">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Jeevi Collective -->
            <div class="work-item-card flex flex-col rounded-[24px] overflow-hidden shadow-md bg-white hover:shadow-xl transition-all duration-500 hover:-translate-y-1.5" data-category="systems">
                <!-- Image Container with zoom effect -->
                <div class="zoom-hover h-[240px] relative bg-neutral-200">
                    <?php if (file_exists('assets/images/jeevi_collective.png')): ?>
                        <img src="assets/images/jeevi_collective.png" alt="Jeevi Collective" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-charcoal/30">No Image Available</div>
                    <?php endif; ?>
                </div>
                <!-- Forest Green Bottom Banner -->
                <div class="bg-olive p-6 md:p-8 flex-grow flex flex-col justify-between text-cream">
                    <div>
                        <h3 class="font-display text-2xl font-bold mb-3">Jeevi Collective</h3>
                        <p class="font-body text-xs md:text-sm text-cream/80 leading-relaxed">
                            Programs, dialogue, workshops, and cultural practice. Designing spaces for community enrichment and systems-level collaboration.
                        </p>
                    </div>
                    <div class="mt-6">
                        <a href="contact.php?inquiry=jeevi-collective" class="inline-flex items-center gap-2 font-body text-[10px] tracking-widest uppercase hover:text-gold transition-colors duration-300 font-bold">
                            Inquire Project <span class="text-[12px]">→</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
// Filter URL check
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const filterParam = urlParams.get('filter');
    if (filterParam) {
        filterWork(filterParam);
    }
});

function filterWork(category) {
    // 1. Update filter button styles
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-rust', 'text-cream', 'border-rust');
        btn.classList.add('border-charcoal/20', 'text-charcoal/70');
    });

    const activeBtn = document.getElementById('filter-' + category);
    if (activeBtn) {
        activeBtn.classList.remove('border-charcoal/20', 'text-charcoal/70');
        activeBtn.classList.add('active', 'bg-rust', 'text-cream', 'border-rust');
    }

    // 2. Animate items out and filter
    const cards = document.querySelectorAll('.work-item-card');
    
    gsap.to(cards, {
        opacity: 0,
        scale: 0.95,
        y: 15,
        duration: 0.3,
        onComplete: () => {
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all' || cardCat === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            // Animate filtered items back in
            const visibleCards = Array.from(cards).filter(c => c.style.display !== 'none');
            gsap.to(visibleCards, {
                opacity: 1,
                scale: 1,
                y: 0,
                duration: 0.5,
                stagger: 0.1,
                ease: 'power2.out'
            });
        }
    });
}
</script>

<?php
include_once 'includes/footer.php';
?>
