<?php
// ============================================================
//  writing-photography.php — Blog & Creative Showcase
//  PHP Role: Renders the writing list or single blog post detail
// ============================================================
require_once 'includes/db.php';
require_once 'includes/auth.php';

$site_title  = get_setting('site_title', $conn, 'Bhavana Goparaju');
$meta_desc   = get_setting('meta_description', $conn, 'Essays, poetry, images, and process notes from the places where story, memory, land, and systems meet.');

// ── SINGLE POST READER MODE ──────────────────────────────────
$single_post = null;
if (isset($_GET['post'])) {
    $slug = trim($_GET['post']);
    $stmt = mysqli_prepare($conn, "SELECT title, content, image_path, created_at FROM blog_posts WHERE slug = ? AND status = 'published' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $single_post = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($single_post) {
        $page_title = sanitize($single_post['title']) . ' — ' . $site_title;
    } else {
        // Redirect back if post not found
        header('Location: writing-photography.php');
        exit();
    }
} else {
    $page_title = 'Writing & Photography — ' . $site_title;
}

// ── GET GENERAL BLOG POSTS LIST ──────────────────────────────
$blog_result = mysqli_query($conn, "SELECT title, slug, content, image_path, created_at FROM blog_posts WHERE status='published' ORDER BY created_at DESC");
$blog_posts  = mysqli_fetch_all($blog_result, MYSQLI_ASSOC);

mysqli_close($conn);

$nav_theme = 'light'; // Light navbar theme for cream backgrounds
include_once 'includes/header.php';
?>

<?php if ($single_post): ?>
<!-- ═══════════════════════════════════════════════════════════
     SINGLE BLOG ARTICLE READER
═══════════════════════════════════════════════════════════ -->
<article class="bg-cream py-12 px-6 md:py-20 flex-grow">
    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="writing-photography.php" class="inline-flex items-center gap-2 font-body text-xs font-bold tracking-widest text-rust uppercase hover:text-charcoal transition-colors duration-300 mb-8">
            <span class="text-sm">←</span> Back to Writing &amp; Photography
        </a>

        <!-- Publish Date -->
        <p class="font-body text-xs text-charcoal/50 uppercase tracking-widest mb-4">
            Published on <?= date('F j, Y', strtotime($single_post['created_at'])) ?>
        </p>

        <!-- Headline -->
        <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-charcoal mb-8 leading-tight">
            <?= sanitize($single_post['title']) ?>
        </h1>

        <!-- Optional Featured Image -->
        <?php if (!empty($single_post['image_path']) && file_exists($single_post['image_path'])): ?>
            <div class="rounded-3xl overflow-hidden shadow-md mb-10 w-full max-h-[450px]">
                <img src="<?= sanitize($single_post['image_path']) ?>" alt="<?= sanitize($single_post['title']) ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <!-- Rich Body Text -->
        <div class="font-body text-base md:text-lg text-charcoal/80 leading-relaxed space-y-6 prose prose-rust">
            <?= nl2br(sanitize($single_post['content'])) ?>
        </div>
    </div>
</article>

<?php else: ?>
<!-- ═══════════════════════════════════════════════════════════
     MAIN SPLIT VISUAL SECTION (Matches Image 5)
═══════════════════════════════════════════════════════════ -->
<section class="grid grid-cols-1 lg:grid-cols-2 min-h-[90vh]">
    <!-- Left Column: Image Grid + Narrative -->
    <div class="bg-cream p-8 md:p-16 lg:p-24 flex flex-col justify-center">
        <!-- Double Square Image Row -->
        <div class="grid grid-cols-2 gap-4 mb-10 gsap-slide-left">
            <div class="aspect-square rounded-2xl overflow-hidden bg-neutral-200 shadow-md">
                <?php if (file_exists('assets/images/writing_bricks.png')): ?>
                    <img src="assets/images/writing_bricks.png" alt="Brick Workers" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                <?php endif; ?>
            </div>
            <div class="aspect-square rounded-2xl overflow-hidden bg-neutral-200 shadow-md">
                <?php if (file_exists('assets/images/writing_camera.png')): ?>
                    <img src="assets/images/writing_camera.png" alt="Photographer near Sea" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                <?php endif; ?>
            </div>
        </div>

        <!-- Section Title & Description -->
        <p class="gsap-fade-in font-body text-[11px] tracking-[0.2em] font-bold text-rust uppercase mb-4">
            Creative Practice
        </p>
        <h1 class="gsap-fade-in font-display text-4xl md:text-5xl font-semibold tracking-tight text-charcoal mb-6 leading-tight">
            Writing, photography, <br class="hidden md:block">and visual inquiry.
        </h1>
        <p class="gsap-fade-in font-body text-base text-charcoal/70 leading-relaxed max-w-lg mb-10">
            Essays, poetry, images, and process notes from the places where story, memory, land, and systems meet.
        </p>

        <!-- CTA Button scroll anchor -->
        <div class="gsap-fade-in">
            <a href="#blog-feed" class="px-8 py-3 bg-rust text-cream font-body font-bold text-xs tracking-[0.15em] uppercase rounded-full hover:bg-charcoal transition-all duration-300 inline-block shadow-md">
                Read / View
            </a>
        </div>
    </div>

    <!-- Right Column: Full-Height Image -->
    <div class="relative h-[60vh] lg:h-auto min-h-[450px]">
        <?php if (file_exists('assets/images/writing_forest.png')): ?>
            <img src="assets/images/writing_forest.png" alt="Forest Path with Bicycle" class="absolute inset-0 w-full h-full object-cover">
        <?php else: ?>
            <div class="absolute inset-0 bg-neutral-300 flex items-center justify-center text-charcoal/30">Forest Path Visual</div>
        <?php endif; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     DYNAMIC BLOG LIST SECTION (Connected to SQL)
═══════════════════════════════════════════════════════════ -->
<section id="blog-feed" class="bg-white py-20 px-6 md:py-28 md:px-12 scroll-mt-20">
    <div class="max-w-4xl mx-auto">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-charcoal mb-12 border-b border-neutral-100 pb-6">
            Published Essays &amp; Notes
        </h2>

        <?php if (empty($blog_posts)): ?>
            <!-- Fallback display if database is empty -->
            <div class="text-center py-12 text-charcoal/40 font-body text-sm bg-cream/30 rounded-3xl p-8 border border-dashed border-charcoal/10">
                <p class="mb-4">No published writing available at this moment.</p>
                <p class="text-xs">Publish articles through the Admin Dashboard to see them populate here.</p>
            </div>
        <?php else: ?>
            <!-- Loop through blog posts from DB -->
            <div class="space-y-16">
                <?php foreach ($blog_posts as $post): ?>
                    <article class="gsap-fade-in flex flex-col md:flex-row gap-8 items-start">
                        
                        <!-- Post Thumb -->
                        <?php if (!empty($post['image_path']) && file_exists($post['image_path'])): ?>
                            <div class="w-full md:w-1/3 aspect-[4/3] md:aspect-square rounded-2xl overflow-hidden bg-neutral-100 flex-shrink-0 shadow-sm">
                                <a href="writing-photography.php?post=<?= urlencode($post['slug']) ?>">
                                    <img src="<?= sanitize($post['image_path']) ?>" alt="<?= sanitize($post['title']) ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Post Text details -->
                        <div class="flex-grow">
                            <span class="font-body text-[10px] text-charcoal/50 uppercase tracking-widest block mb-2">
                                <?= date('M d, Y', strtotime($post['created_at'])) ?>
                            </span>
                            <h3 class="font-display text-2xl font-bold text-charcoal mb-4 hover:text-rust transition-colors duration-300">
                                <a href="writing-photography.php?post=<?= urlencode($post['slug']) ?>">
                                    <?= sanitize($post['title']) ?>
                                </a>
                            </h3>
                            <p class="font-body text-sm text-charcoal/70 leading-relaxed mb-6 line-clamp-3">
                                <?= strip_tags(substr($post['content'], 0, 260)) ?>...
                            </p>
                            <a href="writing-photography.php?post=<?= urlencode($post['slug']) ?>" class="inline-flex items-center gap-2 font-body text-[10px] font-bold tracking-widest text-rust uppercase hover:text-charcoal transition-colors duration-300">
                                Read Post <span class="text-[12px]">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php
include_once 'includes/footer.php';
?>
