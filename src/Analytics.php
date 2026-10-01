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
        add_action('wp_head', [self::class, 'head'], 1);
        add_action('wp_body_open', [self::class, 'body']);
    }

    public static function head(): void
    {
        if (!self::enabled()) {
            return;
        }

        if ($gtm = Customizer::get('gtm_id')) {
            printf(
                "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',%s);</script>\n",
                wp_json_encode($gtm)
            );
        } elseif ($ga = Customizer::get('ga_id')) {
            printf(
                "<script async src=\"%s\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',%s);</script>\n",
                esc_url('https://www.googletagmanager.com/gtag/js?id=' . rawurlencode($ga)),
                wp_json_encode($ga)
            );
        }
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
