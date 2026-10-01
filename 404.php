<?php
/**
 * 404 template.
 *
 * @package SystemScripts
 */

get_header();
?>

<div class="container py-16">
    <div class="mx-auto max-w-xl space-y-6">
        <p class="text-6xl font-semibold text-muted" aria-hidden="true">404</p>
        <h1 class="text-3xl font-semibold"><?php esc_html_e('Page not found', 'systemscripts'); ?></h1>
        <p class="text-lg text-muted"><?php esc_html_e('Sorry, the page you are looking for could not be found. Try searching, or head back to the home page.', 'systemscripts'); ?></p>
        <?php get_search_form(); ?>
        <p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ss-button"><?php esc_html_e('Go to the home page', 'systemscripts'); ?></a>
        </p>
    </div>
</div>

<?php
get_footer();
