<?php
/**
 * Empty state for list views.
 *
 * @package SystemScripts
 */
?>
<section class="space-y-4">
    <p class="text-lg text-muted">
        <?php
        echo esc_html(is_search()
            ? __('Nothing matched your search. Try different keywords.', 'systemscripts')
            : __('There is nothing to show here yet.', 'systemscripts'));
        ?>
    </p>
    <?php if (is_search()): ?>
        <?php get_search_form(); ?>
    <?php endif; ?>
</section>
