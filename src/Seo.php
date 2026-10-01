<?php

namespace SystemScripts;

/**
 * Meta description, canonical, robots, Open Graph and Twitter cards.
 * Backs off completely when a dedicated SEO plugin is active.
 */
class Seo
{
    public static function boot(): void
    {
        if (self::external_active()) {
            return;
        }

        remove_action('wp_head', 'rel_canonical');
        add_action('wp_head', [self::class, 'output'], 2);
        add_filter('pre_get_document_title', [self::class, 'custom_title']);
        add_filter('wp_robots', [self::class, 'robots']);
    }

    /**
     * True when Yoast, Rank Math, SEOPress, AIOSEO or The SEO Framework is active.
     * Filter `ss_seo_external_active` to force either way.
     */
    public static function external_active(): bool
    {
        $active = defined('WPSEO_VERSION')
            || defined('RANK_MATH_VERSION')
            || defined('SEOPRESS_VERSION')
            || defined('AIOSEO_VERSION')
            || defined('THE_SEO_FRAMEWORK_VERSION');

        return (bool) apply_filters('ss_seo_external_active', $active);
    }

    public static function custom_title($title)
    {
        if (is_singular() && ($custom = (string) get_post_meta((int) get_queried_object_id(), SeoMeta::TITLE, true))) {
            return $custom;
        }

        return $title;
    }

    public static function robots(array $robots): array
    {
        if (is_singular() && get_post_meta((int) get_queried_object_id(), SeoMeta::NOINDEX, true)) {
            unset($robots['index']);
            $robots['noindex'] = true;
            $robots['follow']  = true;
        }

        return $robots;
    }

    public static function description(): string
    {
        $object = get_queried_object();
        $text   = '';

        if ($object instanceof \WP_Post) {
            $text = (string) get_post_meta($object->ID, SeoMeta::DESCRIPTION, true);

            if ($text === '') {
                $text = $object->post_excerpt ?: excerpt_remove_blocks(strip_shortcodes($object->post_content));
            }
        } elseif ($object instanceof \WP_Term) {
            $text = term_description($object->term_id, $object->taxonomy);
        } elseif ($object instanceof \WP_User) {
            $text = (string) get_the_author_meta('description', $object->ID);
        }

        if ($text === '' && !is_search() && !is_404()) {
            $text = get_bloginfo('description');
        }

        $text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags((string) $text)));

        return wp_trim_words($text, 30, '…');
    }

    public static function canonical(): string
    {
        if (is_search() || is_404()) {
            return '';
        }

        if (is_front_page()) {
            return home_url('/');
        }

        if (is_singular()) {
            return (string) (wp_get_canonical_url() ?: get_permalink());
        }

        return get_pagenum_link(max(1, (int) get_query_var('paged')));
    }

    /**
     * @return array{url:string,width:int,height:int,alt:string}|null
     */
    public static function image(): ?array
    {
        $id = 0;

        if (is_singular() && has_post_thumbnail()) {
            $id = (int) get_post_thumbnail_id();
        }

        if (!$id) {
            $id = (int) Customizer::get('og_image');
        }

        if (!$id) {
            return null;
        }

        $src = wp_get_attachment_image_src($id, 'full');

        if (!$src) {
            return null;
        }

        return [
            'url'    => $src[0],
            'width'  => (int) $src[1],
            'height' => (int) $src[2],
            'alt'    => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
        ];
    }

    public static function output(): void
    {
        $title       = html_entity_decode(wp_get_document_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $description = self::description();
        $canonical   = self::canonical();
        $image       = self::image();
        $is_article  = is_singular('post');
        $tags        = [];

        if ($description !== '') {
            $tags[] = ['name', 'description', $description];
        }

        $tags[] = ['property', 'og:locale', get_locale()];
        $tags[] = ['property', 'og:type', $is_article ? 'article' : (is_author() ? 'profile' : 'website')];
        $tags[] = ['property', 'og:title', $title];

        if ($description !== '') {
            $tags[] = ['property', 'og:description', $description];
        }

        if ($canonical !== '') {
            $tags[] = ['property', 'og:url', $canonical];
        }

        $tags[] = ['property', 'og:site_name', get_bloginfo('name')];

        if ($image) {
            $tags[] = ['property', 'og:image', $image['url']];
            $tags[] = ['property', 'og:image:width', (string) $image['width']];
            $tags[] = ['property', 'og:image:height', (string) $image['height']];

            if ($image['alt'] !== '') {
                $tags[] = ['property', 'og:image:alt', $image['alt']];
            }
        }

        if ($is_article) {
            $post_id = (int) get_queried_object_id();
            $tags[]  = ['property', 'article:published_time', get_the_date('c', $post_id)];
            $tags[]  = ['property', 'article:modified_time', get_the_modified_date('c', $post_id)];

            if ($author_url = Author::data((int) get_post_field('post_author', $post_id))['url']) {
                $tags[] = ['property', 'article:author', $author_url];
            }
        }

        $tags[] = ['name', 'twitter:card', $image ? 'summary_large_image' : 'summary'];

        if ($handle = Customizer::get('twitter_site')) {
            $tags[] = ['name', 'twitter:site', $handle];
        }

        if ($canonical !== '') {
            printf('<link rel="canonical" href="%s">' . "\n", esc_url($canonical));
        }

        if ($is_article && ($author = Author::data((int) get_post_field('post_author', (int) get_queried_object_id())))['url']) {
            printf('<link rel="author" href="%s">' . "\n", esc_url($author['url']));
        }

        foreach ($tags as [$attr, $key, $value]) {
            printf('<meta %s="%s" content="%s">' . "\n", esc_attr($attr), esc_attr($key), esc_attr($value));
        }
    }
}
