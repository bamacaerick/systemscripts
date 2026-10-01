<?php

namespace SystemScripts;

use WP_Query;

/**
 * Accessible archive pagination built on paginate_links().
 * Core marks the current page with aria-current="page".
 */
class Pagination
{
    public static function render(?WP_Query $query = null): void
    {
        $query = $query ?? $GLOBALS['wp_query'];
        $total = (int) $query->max_num_pages;

        if ($total <= 1) {
            return;
        }

        $current = max(1, (int) $query->get('paged'), (int) get_query_var('paged'));

        $links = paginate_links([
            'total'     => $total,
            'current'   => $current,
            'type'      => 'array',
            'mid_size'  => 2,
            'prev_text' => self::label(__('Previous page', 'systemscripts'), '&larr;'),
            'next_text' => self::label(__('Next page', 'systemscripts'), '&rarr;'),
        ]);

        if (!$links) {
            return;
        }

        printf(
            '<nav class="mt-12 border-t border-zinc-200 pt-6" aria-label="%s"><ul class="flex flex-wrap items-center justify-center gap-2">',
            esc_attr__('Pagination', 'systemscripts')
        );

        foreach ($links as $link) {
            echo '<li>' . $link . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built by core.
        }

        echo '</ul></nav>';
    }

    private static function label(string $text, string $arrow): string
    {
        return sprintf('<span aria-hidden="true">%s</span><span class="sr-only">%s</span>', $arrow, esc_html($text));
    }
}
