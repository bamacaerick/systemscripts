<?php
/**
 * Post card used in list views.
 *
 * @package SystemScripts
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('border-t border-zinc-200 pt-8'); ?>>
    <div class="md:flex md:gap-8">
        <?php if (has_post_thumbnail()): ?>
            <div class="mb-4 shrink-0 md:mb-0 md:w-1/3">
                <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                    <?php the_post_thumbnail('medium_large', ['class' => 'aspect-16/10 w-full rounded-lg object-cover', 'alt' => '']); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="space-y-3">
            <h2 class="text-2xl font-semibold">
                <a href="<?php the_permalink(); ?>" class="text-dark no-underline hover:underline"><?php the_title(); ?></a>
            </h2>

            <?php if ('post' === get_post_type()): ?>
                <p class="text-sm text-muted">
                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <span aria-hidden="true">&middot;</span>
                    <span class="sr-only"><?php esc_html_e('by', 'systemscripts'); ?></span>
                    <?php echo SystemScripts\Author::name_html((int) get_the_author_meta('ID')); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
                </p>
            <?php endif; ?>

            <div class="max-w-2xl text-base text-muted"><?php the_excerpt(); ?></div>

            <p>
                <a class="font-semibold" href="<?php the_permalink(); ?>">
                    <?php esc_html_e('Read more', 'systemscripts'); ?><span class="sr-only">: <?php echo esc_html(get_the_title()); ?></span>
                </a>
            </p>
        </div>
    </div>
</article>
