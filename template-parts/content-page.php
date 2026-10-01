<?php
/**
 * Page content.
 *
 * @package SystemScripts
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="container py-8">
        <?php SystemScripts\Breadcrumbs::render('mb-6'); ?>

        <header class="mx-auto max-w-4xl <?php echo is_front_page() ? '' : 'text-center'; ?>">
            <h1 class="text-4xl font-medium tracking-tight text-balance md:text-5xl"><?php the_title(); ?></h1>
        </header>

        <?php if (has_post_thumbnail()): ?>
            <div class="mx-auto mt-10 max-w-4xl overflow-hidden rounded-lg bg-light">
                <?php the_post_thumbnail('large', ['class' => 'aspect-16/10 w-full object-cover', 'loading' => 'eager', 'fetchpriority' => 'high']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="entry-content mt-10">
        <?php
        the_content();

        wp_link_pages([
            'before' => '<nav class="page-links" aria-label="' . esc_attr__('Page sections', 'systemscripts') . '"><span>' . esc_html__('Pages:', 'systemscripts') . '</span>',
            'after'  => '</nav>',
        ]);
        ?>
    </div>
</article>
