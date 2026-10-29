<?php

namespace SystemScripts;

/**
 * Native, server-rendered custom blocks (no ACF needed).
 *
 * Each block lives in blocks/<slug>/ with block.json and render.php. The `ss.fields` list in
 * block.json drives both the block attributes and the editor controls (see blocks/editor.js).
 *
 * Field types: text, textarea, url, select, toggle, image, repeater (with nested `fields`).
 */
class Blocks
{
    public const CATEGORY = 'ss';

    /** @var array<string, array> Editor field definitions keyed by block name. */
    private static array $editor_fields = [];

    public static function boot(): void
    {
        add_filter('block_categories_all', [self::class, 'category']);
        add_action('init', [self::class, 'register']);
        add_action('init', [self::class, 'patterns']);
        add_action('enqueue_block_editor_assets', [self::class, 'editor_assets']);
    }

    public static function category(array $categories): array
    {
        array_unshift($categories, [
            'slug'  => self::CATEGORY,
            'title' => __('SystemScripts', 'systemscripts'),
        ]);

        return $categories;
    }

    public static function register(): void
    {
        foreach (glob(SS_DIR . '/blocks/*/block.json') ?: [] as $file) {
            $meta   = json_decode((string) file_get_contents($file), true);
            $fields = is_array($meta) ? ($meta['ss']['fields'] ?? []) : [];
            $type   = register_block_type(dirname($file), ['attributes' => self::attributes($fields)]);

            if ($type) {
                self::$editor_fields[$type->name] = ['fields' => $fields];
            }
        }
    }

    public static function editor_assets(): void
    {
        $file = SS_DIR . '/blocks/editor.js';

        if (!is_file($file) || !self::$editor_fields) {
            return;
        }

        wp_enqueue_script(
            'ss-blocks-editor',
            SS_URI . '/blocks/editor.js',
            ['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-data', 'wp-i18n'],
            (string) filemtime($file),
            true
        );

        wp_add_inline_script(
            'ss-blocks-editor',
            'window.ssBlocks = ' . wp_json_encode(self::$editor_fields) . ';',
            'before'
        );
    }

    /**
     * Turns a `fields` list into block attribute definitions.
     */
    private static function attributes(array $fields): array
    {
        $attributes = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? '';

            if ($name === '') {
                continue;
            }

            switch ($field['type'] ?? 'text') {
                case 'toggle':
                    $attributes[$name] = ['type' => 'boolean', 'default' => (bool) ($field['default'] ?? false)];
                    break;
                case 'image':
                    $attributes[$name] = ['type' => 'integer', 'default' => 0];
                    break;
                case 'repeater':
                    $attributes[$name] = ['type' => 'array', 'default' => $field['default'] ?? []];
                    break;
                default:
                    $attributes[$name] = ['type' => 'string', 'default' => (string) ($field['default'] ?? '')];
            }
        }

        return $attributes;
    }

    // -- Helpers used by blocks/*/render.php ---------------------------------

    /**
     * True while the block editor is rendering a preview (ServerSideRender is a REST request).
     */
    public static function is_editor(): bool
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }

    public static function placeholder(string $message): string
    {
        return self::is_editor()
            ? '<p class="rounded-md border border-dashed border-border p-6 text-center text-muted">' . esc_html($message) . '</p>'
            : '';
    }

    public static function heading_tag(string $level, string $fallback = 'h2'): string
    {
        return in_array($level, ['h1', 'h2', 'h3', 'h4'], true) ? $level : $fallback;
    }

    /**
     * Tag for items nested under a block heading (one level lower, never below h6).
     */
    public static function child_heading_tag(string $parent): string
    {
        return 'h' . min(6, (int) substr($parent, 1) + 1);
    }

    /**
     * Plain textarea content to safe paragraphs.
     */
    public static function paragraphs(string $text): string
    {
        return $text === '' ? '' : wpautop(esc_html($text));
    }

    public static function button(string $label, string $url, bool $secondary = false, bool $inverse = false, array $options = []): string
    {
        if ($label === '' || $url === '') {
            return '';
        }

        $variant = $options['variant'] ?? ($secondary ? 'secondary' : 'primary');
        if (!in_array($variant, ['primary', 'secondary', 'link'], true)) {
            $variant = 'primary';
        }

        $class = $variant === 'link'
            ? 'ss-link-button'
            : 'ss-button' . ($variant === 'secondary' ? ' ss-button--secondary' : '') . ($inverse ? ' ss-button--inverse' : '');

        $target = !empty($options['new_tab']) ? '_blank' : '_self';
        $rel    = $target === '_blank' ? 'noopener noreferrer' : '';
        $url    = self::button_url($url, (string) ($options['whatsapp_text'] ?? ''));
        $icon   = self::button_icon((string) ($options['icon'] ?? ''));
        $side   = ($options['icon_position'] ?? 'after') === 'before' ? 'before' : 'after';
        $iconed = $icon === '' ? esc_html($label) : ($side === 'before'
            ? $icon . '<span>' . esc_html($label) . '</span>'
            : '<span>' . esc_html($label) . '</span>' . $icon);

        return sprintf(
            '<a class="%s" href="%s" target="%s"%s>%s</a>',
            esc_attr($class),
            esc_url($url),
            esc_attr($target),
            $rel === '' ? '' : ' rel="' . esc_attr($rel) . '"',
            $iconed
        );
    }

    private static function button_url(string $url, string $whatsapp_text): string
    {
        if ($whatsapp_text === '') {
            return $url;
        }

        $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
        if (!in_array($host, ['wa.me', 'api.whatsapp.com', 'web.whatsapp.com', 'www.whatsapp.com', 'whatsapp.com'], true)) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $message_parameter = $host === 'wa.me' ? 'text' : 'text';

        return $url . $separator . $message_parameter . '=' . rawurlencode($whatsapp_text);
    }

    private static function button_icon(string $icon): string
    {
        if (!in_array($icon, ['arrow-left', 'arrow-right', 'external', 'whatsapp'], true)) {
            return '';
        }

        $paths = [
            'arrow-left' => '<path d="M19 12H5m6 6-6-6 6-6"/>',
            'arrow-right' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
            'external' => '<path d="M14 5h5v5M10 14 19 5M19 14v5H5V5h5"/>',
            'whatsapp' => '<path d="M21 12a9 9 0 0 1-13.5 7.8L3 21l1.2-4.3A9 9 0 1 1 21 12Z"/><path d="M8.5 8.5c.3 3.3 3.7 6.7 7 7l1-1.5-2.3-1-1 1c-1.4-.7-2.2-1.5-2.9-2.9l1-1-1-2.3Z"/>',
        ];

        return '<svg class="ss-button__icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths[$icon] . '</svg>';
    }

    /**
     * Surface colors that keep AA contrast. Returns [classes, is_dark].
     *
     * @return array{0:string,1:bool}
     */
    public static function surface(string $style): array
    {
        $map = [
            'light'   => ['bg-light text-dark', false],
            'dark'    => ['bg-dark text-white [&_a:not(.ss-button)]:text-white', true],
            'primary' => ['bg-primary text-white [&_a:not(.ss-button)]:text-white', true],
            'none'    => ['', false],
        ];

        return $map[$style] ?? $map['light'];
    }

    public static function image(int $id, string $class = '', string $size = 'large'): string
    {
        if (!$id) {
            return '';
        }

        // Empty alt text in the media library renders alt="" (decorative), which is correct.
        return wp_get_attachment_image($id, $size, false, ['class' => $class]);
    }

    // -- Patterns -------------------------------------------------------------

    public static function patterns(): void
    {
        register_block_pattern_category(self::CATEGORY, ['label' => __('SystemScripts', 'systemscripts')]);

        $blocks = [
            ['ss/hero', [
                'level'        => 'h2',
                'eyebrow'      => 'Welcome',
                'heading'      => 'A clear headline that says what you do',
                'text'         => 'One or two sentences that explain the value you offer and who it is for.',
                'primaryLabel' => 'Get started',
                'primaryUrl'   => '#',
                'align'        => 'full',
            ]],
            ['ss/cards', [
                'heading' => 'What we offer',
                'columns' => '3',
                'align'   => 'wide',
                'items'   => [
                    ['title' => 'First service', 'text' => 'Short description of the first service.'],
                    ['title' => 'Second service', 'text' => 'Short description of the second service.'],
                    ['title' => 'Third service', 'text' => 'Short description of the third service.'],
                ],
            ]],
            ['ss/faq', [
                'heading' => 'Frequently asked questions',
                'items'   => [
                    ['question' => 'How does it work?', 'answer' => 'Explain the process in a few sentences.'],
                    ['question' => 'How much does it cost?', 'answer' => 'Describe pricing or how to request a quote.'],
                ],
            ]],
            ['ss/cta', [
                'heading'     => 'Ready to start?',
                'text'        => 'Tell visitors what to do next.',
                'buttonLabel' => 'Contact us',
                'buttonUrl'   => '#',
                'align'       => 'full',
            ]],
        ];

        $content = '';

        foreach ($blocks as [$name, $attrs]) {
            $content .= sprintf("<!-- wp:%s %s /-->\n\n", $name, wp_json_encode($attrs, JSON_UNESCAPED_SLASHES));
        }

        register_block_pattern('ss/landing-page', [
            'title'       => __('Landing page', 'systemscripts'),
            'description' => __('Hero, cards, FAQ and call to action.', 'systemscripts'),
            'categories'  => [self::CATEGORY],
            'content'     => $content,
        ]);
    }
}
