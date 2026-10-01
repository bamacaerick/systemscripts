<?php

namespace SystemScripts;

/**
 * Site-wide settings in the Customizer (no plugin required). Read them with Customizer::get().
 */
class Customizer
{
    private const PREFIX = 'ss_';

    /**
     * Defaults live apart from the labels so get() never calls __() before init.
     */
    private const DEFAULTS = [
        'org_type'                => 'Organization',
        'og_image'                => 0,
        'disable_author_archives' => false,
        'disable_comments'        => false,
    ];

    /**
     * key => [label, type, sanitize callback, default]
     * Sections are declared in sections() below.
     */
    private static function settings(): array
    {
        return [
            // Organization / schema.org
            'org_type'     => ['org', __('Publisher type', 'systemscripts'), 'select', [self::class, 'sanitize_org_type'], 'Organization'],
            'org_name'     => ['org', __('Organization name (blank = site title)', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_phone'    => ['org', __('Telephone', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_email'    => ['org', __('Contact email', 'systemscripts'), 'email', 'sanitize_email', ''],
            'org_street'   => ['org', __('Street address', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_city'     => ['org', __('City', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_region'   => ['org', __('State / region', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_postal'   => ['org', __('Postal code', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_country'  => ['org', __('Country (2-letter code)', 'systemscripts'), 'text', 'sanitize_text_field', ''],
            'org_social'   => ['org', __('Social profile URLs (one per line)', 'systemscripts'), 'textarea', [self::class, 'sanitize_urls'], ''],
            // SEO
            'og_image'     => ['seo', __('Default social sharing image', 'systemscripts'), 'media', 'absint', 0],
            'twitter_site' => ['seo', __('X / Twitter handle (e.g. @name)', 'systemscripts'), 'text', [self::class, 'sanitize_handle'], ''],
            'disable_author_archives' => ['seo', __('Disable author archives (single-author sites)', 'systemscripts'), 'checkbox', 'rest_sanitize_boolean', false],
            // Features
            'disable_comments' => ['features', __('Disable comments site-wide', 'systemscripts'), 'checkbox', 'rest_sanitize_boolean', false],
            'gtm_id'       => ['features', __('Google Tag Manager ID (GTM-XXXX)', 'systemscripts'), 'text', [self::class, 'sanitize_gtm'], ''],
            'ga_id'        => ['features', __('Google Analytics 4 ID (G-XXXX)', 'systemscripts'), 'text', [self::class, 'sanitize_ga'], ''],
        ];
    }

    private static function sections(): array
    {
        return [
            'org'      => __('Organization & Schema', 'systemscripts'),
            'seo'      => __('SEO & Social', 'systemscripts'),
            'features' => __('Features & Tracking', 'systemscripts'),
        ];
    }

    public static function boot(): void
    {
        add_action('customize_register', [self::class, 'register']);
    }

    public static function get(string $key, $default = null)
    {
        return get_theme_mod(self::PREFIX . $key, $default ?? self::DEFAULTS[$key] ?? '');
    }

    public static function register(\WP_Customize_Manager $wp_customize): void
    {
        $wp_customize->add_panel('ss_panel', [
            'title'    => __('SystemScripts', 'systemscripts'),
            'priority' => 30,
        ]);

        foreach (self::sections() as $id => $title) {
            $wp_customize->add_section('ss_' . $id, ['title' => $title, 'panel' => 'ss_panel']);
        }

        foreach (self::settings() as $key => [$section, $label, $type, $sanitize, $default]) {
            $id = self::PREFIX . $key;

            $wp_customize->add_setting($id, [
                'default'           => $default,
                'sanitize_callback' => $sanitize,
            ]);

            if ($type === 'media') {
                $wp_customize->add_control(new \WP_Customize_Media_Control($wp_customize, $id, [
                    'label'     => $label,
                    'section'   => 'ss_' . $section,
                    'mime_type' => 'image',
                ]));
                continue;
            }

            $args = ['label' => $label, 'section' => 'ss_' . $section, 'type' => $type];

            if ($type === 'select') {
                $args['choices'] = [
                    'Organization'  => __('Organization', 'systemscripts'),
                    'LocalBusiness' => __('Local business', 'systemscripts'),
                    'Person'        => __('Person', 'systemscripts'),
                ];
            }

            $wp_customize->add_control($id, $args);
        }
    }

    public static function sanitize_org_type($value): string
    {
        return in_array($value, ['Organization', 'LocalBusiness', 'Person'], true) ? $value : 'Organization';
    }

    public static function sanitize_urls($value): string
    {
        $urls = array_filter(array_map('esc_url_raw', preg_split('/\R/', (string) $value) ?: []));

        return implode("\n", $urls);
    }

    public static function sanitize_handle($value): string
    {
        $value = '@' . ltrim(sanitize_text_field((string) $value), '@');

        return preg_match('/^@\w{1,15}$/', $value) ? $value : '';
    }

    public static function sanitize_gtm($value): string
    {
        $value = strtoupper(trim((string) $value));

        return preg_match('/^GTM-[A-Z0-9]{4,10}$/', $value) ? $value : '';
    }

    public static function sanitize_ga($value): string
    {
        $value = strtoupper(trim((string) $value));

        return preg_match('/^G-[A-Z0-9]{4,12}$/', $value) ? $value : '';
    }
}
