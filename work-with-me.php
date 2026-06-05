<?php
// ============================================================
//  work-with-me.php — Services & Strategy Offerings
//  PHP Role: Renders the services and consultation page.
// ============================================================
require_once 'includes/db.php';
require_once 'includes/auth.php';

$site_title  = get_setting('site_title', $conn, 'Bhavana Goparaju');
$meta_desc   = get_setting('meta_description', $conn, 'Work with me to bring your stories, programs, systems, and creative leadership to life.');

$page_title = 'Work With Me — ' . $site_title;
$nav_theme = 'dark'; // Dark theme for navbar to match the deep dark page background

mysqli_close($conn);

include_once 'includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════
     WORK WITH ME SECTION — Deep Navy/Black Background
═══════════════════════════════════════════════════════════ -->
<section class="bg-[#08111e] text-cream py-16 px-6 md:py-24 md:px-12 flex-grow min-h-screen flex flex-col justify-center">
    <div class="max-w-6xl mx-auto w-full">
        <!-- Eyebrow -->
        <p class="gsap-slide-left font-body text-[11px] tracking-[0.2em] font-bold text-gold uppercase mb-4">
            Services &amp; Collaboration
        </p>

        <!-- Page Headline -->
        <h1 class="gsap-slide-left font-display text-4xl md:text-5xl lg:text-[54px] font-bold tracking-tight text-white mb-6 leading-tight">
            Bring a story, system, program, or question.
        </h1>

        <!-- Page Description -->
        <p class="gsap-fade-in font-body text-base md:text-lg text-cream/70 leading-relaxed max-w-3xl mb-16">
            Work with me when the pieces are important, complex, and not yet fully connected.
        </p>

        <!-- Services Cards Layout -->
        <!-- Grid: 3 columns on desktop, stacks on mobile. GSAP stagger animated -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 gsap-stagger-grid mb-16">
            
            <!-- Card 01 -->
            <div id="story" class="bg-[#111f32]/60 border border-white/5 p-8 rounded-[24px] shadow-lg hover:shadow-xl hover:border-gold/30 hover:bg-[#111f32] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[220px]">
                <div>
                    <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">01</span>
                    <h3 class="font-display text-xl font-bold text-white mb-3">Story &amp; Narrative Strategy</h3>
                    <p class="font-body text-xs md:text-sm text-cream/70 leading-relaxed">
                        Clarifying the narrative thread inside complex works. We translate data, experience, and knowledge into stories that connect.
                    </p>
                </div>
            </div>

            <!-- Card 02 -->
            <div id="program" class="bg-[#111f32]/60 border border-white/5 p-8 rounded-[24px] shadow-lg hover:shadow-xl hover:border-gold/30 hover:bg-[#111f32] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[220px]">
                <div>
                    <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">02</span>
                    <h3 class="font-display text-xl font-bold text-white mb-3">Program &amp; Community Infrastructure Design</h3>
                    <p class="font-body text-xs md:text-sm text-cream/70 leading-relaxed">
                        Building community-centered initiatives, regional partnerships, and sustainable resource distribution pathways.
                    </p>
                </div>
            </div>

            <!-- Card 03 -->
            <div id="system" class="bg-[#111f32]/60 border border-white/5 p-8 rounded-[24px] shadow-lg hover:shadow-xl hover:border-gold/30 hover:bg-[#111f32] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[220px]">
                <div>
                    <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">03</span>
                    <h3 class="font-display text-xl font-bold text-white mb-3">Data, Research &amp; Systems Strategy</h3>
                    <p class="font-body text-xs md:text-sm text-cream/70 leading-relaxed">
                        Helping teams leverage data, research, and collaborative evaluation to reveal connections and drive informed decisions.
                    </p>
                </div>
            </div>

            <!-- Card 04 (Centered/offset logic on desktop) -->
            <div class="bg-[#111f32]/60 border border-white/5 p-8 rounded-[24px] shadow-lg hover:shadow-xl hover:border-gold/30 hover:bg-[#111f32] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[220px] md:col-start-1 md:col-end-2 lg:col-start-1 lg:col-end-2">
                <div>
                    <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">04</span>
                    <h3 class="font-display text-xl font-bold text-white mb-3">Producing &amp; Creative Leadership</h3>
                    <p class="font-body text-xs md:text-sm text-cream/70 leading-relaxed">
                        Providing artistic vision and operational leadership to bring complex storytelling projects and independent media products to life.
                    </p>
                </div>
            </div>

            <!-- Card 05 -->
            <div class="bg-[#111f32]/60 border border-white/5 p-8 rounded-[24px] shadow-lg hover:shadow-xl hover:border-gold/30 hover:bg-[#111f32] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between min-h-[220px] md:col-start-2 md:col-end-3 lg:col-start-2 lg:col-end-3">
                <div>
                    <span class="font-body text-xs font-bold text-gold tracking-widest block mb-4">05</span>
                    <h3 class="font-display text-xl font-bold text-white mb-3">Speaking, Teaching &amp; Facilitation</h3>
                    <p class="font-body text-xs md:text-sm text-cream/70 leading-relaxed">
                        Designing and facilitating interactive workshops, teaching courses, and delivering keynotes at the intersection of media and systems.
                    </p>
                </div>
            </div>

        </div>

        <!-- Buttons centered underneath -->
        <div class="gsap-fade-in flex flex-wrap items-center justify-center gap-4 mt-8">
            <a href="contact.php?inquiry=services" class="px-8 py-3 border-2 border-gold bg-gold text-navy font-body font-bold text-xs tracking-[0.15em] uppercase rounded-full hover:bg-transparent hover:text-gold transition-all duration-300">
                Work With Me
            </a>
            <a href="contact.php?inquiry=book" class="px-8 py-3 border-2 border-gold text-cream font-body font-bold text-xs tracking-[0.15em] uppercase rounded-full hover:bg-gold hover:text-navy transition-all duration-300">
                Invite / Book
            </a>
        </div>
    </div>
</section>

<?php
include_once 'includes/footer.php';
?>
