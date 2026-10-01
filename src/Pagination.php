<?php

namespace SystemScripts;

use WP_Query;

/**
 * Accessible archive pagination built on paginate_links().
 * Core marks the current page with aria-current="page".
 */
class Pagination
{
    // SVG rather than a text arrow: single-character text nodes are skipped by contrast checkers.
    private const CHEVRON_PREV = '<svg class="size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M9.78 4.22a.75.75 0 0 1 0 1.06L7.06 8l2.72 2.72a.75.75 0 1 1-1.06 1.06L5.47 8.53a.75.75 0 0 1 0-1.06l3.25-3.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>';
    private const CHEVRON_NEXT = '<svg class="size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>';

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
            'prev_text' => self::label(__('Previous page', 'systemscripts'), self::CHEVRON_PREV),
            'next_text' => self::label(__('Next page', 'systemscripts'), self::CHEVRON_NEXT),
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

    private static function label(string $text, string $icon): string
    {
        return $icon . sprintf('<span class="sr-only">%s</span>', esc_html($text));
    }
}
