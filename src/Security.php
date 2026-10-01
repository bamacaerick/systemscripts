<?php

namespace SystemScripts;

/**
 * Small hardening defaults. Each can be turned off with a filter.
 */
class Security
{
    public static function boot(): void
    {
        if (apply_filters('ss_disable_xmlrpc', true)) {
            add_filter('xmlrpc_enabled', '__return_false');
        }

        if (apply_filters('ss_block_user_enumeration', true)) {
            add_action('template_redirect', [self::class, 'block_author_query']);
            add_filter('rest_endpoints', [self::class, 'restrict_users_endpoint']);
        }
    }

    /**
     * Redirect /?author=N (user ID enumeration) for visitors.
     */
    public static function block_author_query(): void
    {
        if (is_user_logged_in() || is_admin()) {
            return;
        }

        if (isset($_GET['author']) && is_numeric(wp_unslash($_GET['author']))) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_safe_redirect(home_url('/'), 301);
            exit;
        }
    }

    public static function restrict_users_endpoint(array $endpoints): array
    {
        if (!is_user_logged_in()) {
            unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        }

        return $endpoints;
    }
}
