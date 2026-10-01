<?php
/**
 * Author archive template.
 *
 * @package SystemScripts
 */

get_header();
?>

<div class="container py-8">
    <?php SystemScripts\Breadcrumbs::render('mb-6'); ?>
    <?php get_template_part('template-parts/author-header'); ?>
    <?php get_template_part('template-parts/loop'); ?>
</div>

<?php
get_footer();
