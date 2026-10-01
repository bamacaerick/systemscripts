<?php
/**
 * Main template: blog index, archives and search results.
 *
 * @package SystemScripts
 */

get_header();
?>

<div class="container py-8">
    <?php SystemScripts\Breadcrumbs::render('mb-6'); ?>
    <?php get_template_part('template-parts/archive-header'); ?>
    <?php get_template_part('template-parts/loop'); ?>
</div>

<?php
get_footer();
