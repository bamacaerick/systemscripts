<?php

namespace SystemScripts;

/**
 * Loads Vite assets: the dev server when a `hot` file exists, the built manifest otherwise.
 */
class Assets
{
    private const ENTRIES = [
        'css' => 'resources/css/app.css',
        'js'  => 'resources/js/app.js',
    ];

    private const EDITOR_STYLE = 'resources/css/editor-style.css';

    public static function boot(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue']);
        add_action('after_setup_theme', [self::class, 'editor_style']);
        add_action('admin_notices', [self::class, 'missing_build_notice']);
        add_filter('script_loader_tag', [self::class, 'module_tag'], 10, 3);
    }

    public static function enqueue(): void
    {
        if ($dev = self::dev_server()) {
            wp_enqueue_script('ss-vite-client', $dev . '/@vite/client', [], null, false);
            wp_enqueue_script('ss-app', $dev . '/' . self::ENTRIES['js'], [], null, true);
            wp_enqueue_style('ss-app', $dev . '/' . self::ENTRIES['css'], [], null);

            return;
        }

        $manifest = self::manifest();

        if (isset($manifest[self::ENTRIES['css']])) {
            $entry = $manifest[self::ENTRIES['css']];
            wp_enqueue_style('ss-app', SS_URI . '/dist/' . $entry['file'], [], null);
        }

        if (isset($manifest[self::ENTRIES['js']])) {
            $entry = $manifest[self::ENTRIES['js']];
            wp_enqueue_script('ss-app', SS_URI . '/dist/' . $entry['file'], [], null, true);
        }
    }

    public static function editor_style(): void
    {
        add_theme_support('editor-styles');

        if ($dev = self::dev_server()) {
            add_editor_style($dev . '/' . self::EDITOR_STYLE);

            return;
        }

        $manifest = self::manifest();

        if (isset($manifest[self::EDITOR_STYLE])) {
            add_editor_style('dist/' . $manifest[self::EDITOR_STYLE]['file']);
        }
    }

    /**
     * Vite emits ES modules, so scripts must be printed with type="module".
     */
    public static function module_tag(string $tag, string $handle, string $src): string
    {
        if (!in_array($handle, ['ss-app', 'ss-vite-client'], true)) {
            return $tag;
        }

        // Rewrites the tag of an already enqueued handle, so this is not a hardcoded script.
        return sprintf('<script type="module" src="%s"></script>' . "\n", esc_url($src)); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
    }

    public static function missing_build_notice(): void
    {
        if (!current_user_can('manage_options') || self::dev_server() || self::manifest()) {
            return;
        }

        printf(
            '<div class="notice notice-warning"><p>%s</p></div>',
            esc_html__('SystemScripts: assets are not built. Run "pnpm install && pnpm build" in the theme folder.', 'systemscripts')
        );
    }

    private static function dev_server(): ?string
    {
        $hot = SS_DIR . '/hot';

        if (!is_file($hot)) {
            return null;
        }

        $url = trim((string) file_get_contents($hot));

        return $url !== '' ? untrailingslashit($url) : null;
    }

    private static function manifest(): array
    {
        static $manifest = null;

        if ($manifest !== null) {
            return $manifest;
        }

        $manifest = [];

        // Vite 5+ writes to dist/.vite/manifest.json; older versions to dist/manifest.json.
        foreach (['/dist/.vite/manifest.json', '/dist/manifest.json'] as $path) {
            if (is_file(SS_DIR . $path)) {
                $manifest = json_decode((string) file_get_contents(SS_DIR . $path), true) ?: [];
                break;
            }
        }

        return $manifest;
    }
}
