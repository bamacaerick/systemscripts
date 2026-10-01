<?php

namespace SystemScripts;

/**
 * Optional GTM / GA4 snippets from the Customizer IDs.
 *
 * Nothing is printed unless an ID is set. Gate it behind a consent tool with the
 * `ss_analytics_enabled` filter (return false until the visitor consents).
 */
class Analytics
{
    public static function boot(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue']);
        add_action('wp_head', [self::class, 'head'], 1);
        add_action('wp_body_open', [self::class, 'body']);
    }

    /**
     * GA4 (only when no GTM container is set): the loader is a normal async script.
     */
    public static function enqueue(): void
    {
        $ga = Customizer::get('ga_id');

        if (!$ga || Customizer::get('gtm_id') || !self::enabled()) {
            return;
        }

        wp_enqueue_script('ss-ga', 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode($ga), [], null, ['strategy' => 'async']);
        wp_add_inline_script(
            'ss-ga',
            'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode($ga) . ');'
        );
    }

    public static function head(): void
    {
        $gtm = Customizer::get('gtm_id');

        if (!$gtm || !self::enabled()) {
            return;
        }

        wp_print_inline_script_tag(
            "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer'," . wp_json_encode($gtm) . ');'
        );
    }

    public static function body(): void
    {
        $gtm = Customizer::get('gtm_id');

        if ($gtm && self::enabled()) {
            printf(
                '<noscript><iframe src="%s" height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>' . "\n",
                esc_url('https://www.googletagmanager.com/ns.html?id=' . rawurlencode($gtm))
            );
        }
    }

    private static function enabled(): bool
    {
        return !is_admin() && !is_customize_preview() && apply_filters('ss_analytics_enabled', true);
    }
}
