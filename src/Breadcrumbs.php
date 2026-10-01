<?php

namespace SystemScripts;

/**
 * Breadcrumb trail. One source for the visible navigation and the BreadcrumbList schema.
 */
class Breadcrumbs
{
    /**
     * @return array<int, array{label: string, url: string}> The last item is the current page (empty url).
     */
    public static function trail(): array
    {
        if (is_front_page()) {
            return [];
        }

        $trail = [self::item(__('Home', 'systemscripts'), home_url('/'))];

        if (is_home()) {
            $trail[] = self::item(single_post_title('', false) ?: __('Blog', 'systemscripts'));
        } elseif (is_singular()) {
            $post = get_queried_object();
            $trail = array_merge($trail, self::singular_parents($post));
            $trail[] = self::item(get_the_title($post));
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();

            if ($term instanceof \WP_Term) {
                foreach (array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy')) as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, $term->taxonomy);

                    if ($ancestor instanceof \WP_Term) {
                        $trail[] = self::item($ancestor->name, get_term_link($ancestor));
                    }
                }

                $trail[] = self::item($term->name);
            }
        } elseif (is_post_type_archive()) {
            $trail[] = self::item(post_type_archive_title('', false));
        } elseif (is_author()) {
            $trail[] = self::item(get_the_author_meta('display_name', (int) get_queried_object_id()));
        } elseif (is_year()) {
            $trail[] = self::item(get_the_date('Y'));
        } elseif (is_month()) {
            $trail[] = self::item(get_the_date('F Y'));
        } elseif (is_day()) {
            $trail[] = self::item(get_the_date());
        } elseif (is_search()) {
            /* translators: %s: search query */
            $trail[] = self::item(sprintf(__('Search results for: %s', 'systemscripts'), get_search_query()));
        } elseif (is_404()) {
            $trail[] = self::item(__('Page not found', 'systemscripts'));
        }

        /**
         * Filters the breadcrumb trail.
         *
         * @param array $trail List of ['label' => string, 'url' => string]; the last item has an empty url.
         */
        return apply_filters('ss_breadcrumbs', $trail);
    }

    public static function render(string $class = ''): void
    {
        $trail = self::trail();

        if (count($trail) < 2) {
            return;
        }

        $last = count($trail) - 1;

        printf(
            '<nav aria-label="%s" class="%s"><ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted">',
            esc_attr__('Breadcrumb', 'systemscripts'),
            esc_attr($class)
        );

        foreach ($trail as $i => $crumb) {
            echo '<li class="flex items-center gap-2">';

            if ($i === $last || $crumb['url'] === '') {
                printf('<span aria-current="page">%s</span>', esc_html($crumb['label']));
            } else {
                printf('<a href="%s">%s</a>', esc_url($crumb['url']), esc_html($crumb['label']));
                echo '<span aria-hidden="true">/</span>';
            }

            echo '</li>';
        }

        echo '</ol></nav>';
    }

    private static function singular_parents(\WP_Post $post): array
    {
        $parents = [];

        if ($post->post_type === 'post') {
            $blog_page = (int) get_option('page_for_posts');

            if ($blog_page) {
                $parents[] = self::item(get_the_title($blog_page), get_permalink($blog_page));
            }

            $categories = get_the_category($post->ID);

            if ($categories) {
                $category = $categories[0];

                foreach (array_reverse(get_ancestors($category->term_id, 'category', 'taxonomy')) as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, 'category');

                    if ($ancestor instanceof \WP_Term) {
                        $parents[] = self::item($ancestor->name, get_term_link($ancestor));
                    }
                }

                $parents[] = self::item($category->name, get_term_link($category));
            }
        } elseif (is_post_type_hierarchical($post->post_type)) {
            foreach (array_reverse(get_post_ancestors($post)) as $ancestor_id) {
                $parents[] = self::item(get_the_title($ancestor_id), get_permalink($ancestor_id));
            }
        } else {
            $type = get_post_type_object($post->post_type);

            if ($type && $type->has_archive) {
                $parents[] = self::item($type->labels->name, get_post_type_archive_link($post->post_type));
            }
        }

        return $parents;
    }

    private static function item(string $label, $url = ''): array
    {
        return [
            'label' => wp_strip_all_tags($label),
            'url'   => is_string($url) ? $url : '',
        ];
    }
}
