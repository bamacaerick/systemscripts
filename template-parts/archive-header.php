<?php
/**
 * Heading for the blog index, archives and search results. Always renders exactly one <h1>.
 *
 * @package SystemScripts
 */

$description = '';

if (is_search()) {
    /* translators: %s: search query */
    $title = sprintf(__('Search results for: %s', 'systemscripts'), get_search_query());
} elseif (is_home() && !is_front_page()) {
    $title = single_post_title('', false);
} elseif (is_home()) {
    $title = __('Latest posts', 'systemscripts');
} elseif (is_archive()) {
    $title       = wp_strip_all_tags(get_the_archive_title());
    $description = get_the_archive_description();
} else {
    $title = get_bloginfo('name');
}
?>
<header class="mb-8">
    <h1 class="text-3xl font-semibold md:text-4xl"><?php echo esc_html($title); ?></h1>
    <?php if ($description): ?>
        <div class="mt-3 max-w-2xl text-lg text-muted"><?php echo wp_kses_post($description); ?></div>
    <?php endif; ?>
</header>
