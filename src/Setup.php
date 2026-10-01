<?php

namespace SystemScripts;

/**
 * Core theme setup: supports, menus, widget areas, <head> cleanup and menu markup helpers.
 */
class Setup
{
    public static function boot(): void
    {
        add_action('after_setup_theme', [self::class, 'supports']);
        add_action('widgets_init', [self::class, 'widgets']);
        add_action('init', [self::class, 'cleanup_head']);
        add_filter('nav_menu_css_class', [self::class, 'menu_item_class'], 10, 3);
        add_filter('nav_menu_link_attributes', [self::class, 'menu_link_class'], 10, 3);
        add_filter('wp_headers', [self::class, 'remove_pingback_header']);
    }

    public static function supports(): void
    {
        load_theme_textdomain('systemscripts', SS_DIR . '/languages');

        add_theme_support('title-tag');
        add_theme_support('custom-logo', [
            'height'      => 80,
            'width'       => 240,
            'flex-height' => true,
            'flex-width'  => true,
        ]);
        add_theme_support('post-thumbnails');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

        register_nav_menus([
            'primary' => __('Primary Menu', 'systemscripts'),
            'footer'  => __('Footer Menu', 'systemscripts'),
        ]);
    }

    public static function widgets(): void
    {
        foreach ([1 => __('Footer 1', 'systemscripts'), 2 => __('Footer 2', 'systemscripts')] as $n => $name) {
            register_sidebar([
                'id'            => 'footer-' . $n,
                'name'          => $name,
                'before_widget' => '<section id="%1$s" class="widget %2$s space-y-2">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title text-base font-semibold">',
                'after_title'   => '</h2>',
            ]);
        }
    }

    /**
     * Remove noise from <head>. Feeds and the REST link are left alone on purpose.
     */
    public static function cleanup_head(): void
    {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'feed_links_extra', 3);
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

        // Emoji scripts and styles.
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }

    public static function remove_pingback_header(array $headers): array
    {
        unset($headers['X-Pingback']);

        return $headers;
    }

    /**
     * Supports the `li_class` argument of wp_nav_menu().
     */
    public static function menu_item_class(array $classes, $item, $args): array
    {
        if (!empty($args->li_class)) {
            $classes[] = $args->li_class;
        }

        return $classes;
    }

    /**
     * Supports the `link_class` argument of wp_nav_menu().
     */
    public static function menu_link_class(array $atts, $item, $args): array
    {
        if (!empty($args->link_class)) {
            $atts['class'] = trim(($atts['class'] ?? '') . ' ' . $args->link_class);
        }

        return $atts;
    }
}
