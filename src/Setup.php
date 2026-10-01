<?php

namespace SystemScripts;

/**
 * Core theme setup: supports, menus and menu markup helpers.
 */
class Setup
{
    public static function boot(): void
    {
        add_action('after_setup_theme', [self::class, 'supports']);
        add_filter('nav_menu_css_class', [self::class, 'menu_item_class'], 10, 3);
        add_filter('nav_menu_link_attributes', [self::class, 'menu_link_class'], 10, 3);
    }

    public static function supports(): void
    {
        load_theme_textdomain('systemscripts', SS_DIR . '/languages');

        add_theme_support('title-tag');
        add_theme_support('custom-logo');
        add_theme_support('post-thumbnails');
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

        register_nav_menus([
            'primary' => __('Primary Menu', 'systemscripts'),
        ]);
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
