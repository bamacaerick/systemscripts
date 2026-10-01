<?php
/**
 * Single post / custom post type content.
 *
 * @package SystemScripts
 */

use SystemScripts\Author;

$is_post    = 'post' === get_post_type();
$updated    = get_the_modified_date('Y-m-d') !== get_the_date('Y-m-d');
$categories = $is_post ? get_the_category_list(', ') : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="container py-8">
        <?php SystemScripts\Breadcrumbs::render('mb-6'); ?>

        <header class="mx-auto max-w-4xl text-center">
            <?php if ($categories): ?>
                <p class="text-sm font-semibold"><?php echo wp_kses_post($categories); ?></p>
            <?php endif; ?>

            <h1 class="mt-2 text-4xl font-medium tracking-tight text-balance md:text-5xl"><?php the_title(); ?></h1>

            <?php if ($is_post): ?>
                <p class="mt-4 text-sm text-muted">
                    <?php
                    printf(
                        /* translators: %s: author name */
                        esc_html__('By %s', 'systemscripts'),
                        Author::name_html((int) get_the_author_meta('ID')) // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
                    );
                    ?>
                    <span aria-hidden="true">&middot;</span>
                    <?php esc_html_e('Published', 'systemscripts'); ?>
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <?php if ($updated): ?>
                        <span aria-hidden="true">&middot;</span>
                        <?php esc_html_e('Updated', 'systemscripts'); ?>
                        <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>"><?php echo esc_html(get_the_modified_date()); ?></time>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
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
            'before' => '<nav class="page-links" aria-label="' . esc_attr__('Post pages', 'systemscripts') . '"><span>' . esc_html__('Pages:', 'systemscripts') . '</span>',
            'after'  => '</nav>',
        ]);
        ?>
    </div>

    <?php if ($is_post): ?>
        <footer class="container mx-auto mt-12 max-w-3xl space-y-10">
            <?php the_tags('<p class="text-sm"><span class="font-semibold">' . esc_html__('Tags:', 'systemscripts') . '</span> ', ', ', '</p>'); ?>
            <?php get_template_part('template-parts/author-box'); ?>
            <?php get_template_part('template-parts/related-posts'); ?>
        </footer>
    <?php endif; ?>
</article>
