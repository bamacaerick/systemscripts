<?php
/**
 * The loop for list views, with pagination and an empty state.
 *
 * @package SystemScripts
 */

if (have_posts()): ?>
    <div class="space-y-10">
        <?php while (have_posts()): the_post(); ?>
            <?php get_template_part('template-parts/content'); ?>
        <?php endwhile; ?>
    </div>

    <?php SystemScripts\Pagination::render(); ?>
<?php else: ?>
    <?php get_template_part('template-parts/content', 'none'); ?>
<?php endif; ?>
