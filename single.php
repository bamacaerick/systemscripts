<?php
/**
 * Single post (and custom post type) template.
 *
 * @package SystemScripts
 */

get_header();
?>

<?php while (have_posts()): the_post(); ?>
    <?php get_template_part('template-parts/content', 'single'); ?>

    <?php if (SystemScripts\Comments::enabled() && (comments_open() || get_comments_number())): ?>
        <div class="container">
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>
<?php endwhile; ?>

<?php
get_footer();
