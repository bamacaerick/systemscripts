<?php
/**
 * Page template.
 *
 * @package SystemScripts
 */

get_header();
?>

<?php while (have_posts()): the_post(); ?>
    <?php get_template_part('template-parts/content', 'page'); ?>

    <?php if (comments_open()): ?>
        <div class="container">
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>
<?php endwhile; ?>

<?php
get_footer();
