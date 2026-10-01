<?php
/**
 * Search form.
 *
 * @package SystemScripts
 */

$id = wp_unique_id('search-');
?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative">
    <label for="<?php echo esc_attr($id); ?>" class="sr-only"><?php esc_html_e('Search for:', 'systemscripts'); ?></label>
    <input type="search" id="<?php echo esc_attr($id); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>" class="rounded-full py-2.5 pr-12 text-sm" autocomplete="off">
    <button type="submit" class="absolute right-0 top-0 inline-flex size-11 items-center justify-center rounded-full">
        <span class="sr-only"><?php esc_html_e('Search', 'systemscripts'); ?></span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-5" aria-hidden="true" focusable="false">
            <path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" />
        </svg>
    </button>
</form>
