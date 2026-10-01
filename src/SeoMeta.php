<?php

namespace SystemScripts;

/**
 * Per-post SEO fields: title override, meta description, noindex.
 * Not registered when a dedicated SEO plugin is active.
 */
class SeoMeta
{
    public const TITLE       = '_ss_title';
    public const DESCRIPTION = '_ss_description';
    public const NOINDEX     = '_ss_noindex';

    private const NONCE = 'ss_seo_meta';

    public static function boot(): void
    {
        if (Seo::external_active()) {
            return;
        }

        add_action('add_meta_boxes', [self::class, 'register']);
        add_action('save_post', [self::class, 'save']);
    }

    public static function register(): void
    {
        $types = get_post_types(['public' => true]);
        unset($types['attachment']);

        foreach ($types as $type) {
            add_meta_box('ss_seo', __('SEO', 'systemscripts'), [self::class, 'render'], $type, 'normal', 'low');
        }
    }

    public static function render(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE, self::NONCE . '_nonce');
        $title       = (string) get_post_meta($post->ID, self::TITLE, true);
        $description = (string) get_post_meta($post->ID, self::DESCRIPTION, true);
        $noindex     = (bool) get_post_meta($post->ID, self::NOINDEX, true);
        ?>
        <p>
            <label for="ss_title"><strong><?php esc_html_e('SEO title', 'systemscripts'); ?></strong></label><br>
            <input type="text" id="ss_title" name="ss_title" class="widefat" value="<?php echo esc_attr($title); ?>">
            <span class="description"><?php esc_html_e('Leave empty to use the post title and site name.', 'systemscripts'); ?></span>
        </p>
        <p>
            <label for="ss_description"><strong><?php esc_html_e('Meta description', 'systemscripts'); ?></strong></label><br>
            <textarea id="ss_description" name="ss_description" class="widefat" rows="3" maxlength="320"><?php echo esc_textarea($description); ?></textarea>
            <span class="description"><?php esc_html_e('Around 155 characters. Leave empty to use the excerpt.', 'systemscripts'); ?></span>
        </p>
        <p>
            <label>
                <input type="checkbox" name="ss_noindex" value="1" <?php checked($noindex); ?>>
                <?php esc_html_e('Ask search engines not to index this page (noindex)', 'systemscripts'); ?>
            </label>
        </p>
        <?php
    }

    public static function save(int $post_id): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $nonce = isset($_POST[self::NONCE . '_nonce']) ? sanitize_text_field(wp_unslash($_POST[self::NONCE . '_nonce'])) : '';

        if (!wp_verify_nonce($nonce, self::NONCE) || !current_user_can('edit_post', $post_id)) {
            return;
        }

        $title       = isset($_POST['ss_title']) ? sanitize_text_field(wp_unslash($_POST['ss_title'])) : '';
        $description = isset($_POST['ss_description']) ? sanitize_textarea_field(wp_unslash($_POST['ss_description'])) : '';

        foreach ([self::TITLE => $title, self::DESCRIPTION => $description] as $key => $value) {
            $value !== '' ? update_post_meta($post_id, $key, $value) : delete_post_meta($post_id, $key);
        }

        empty($_POST['ss_noindex']) ? delete_post_meta($post_id, self::NOINDEX) : update_post_meta($post_id, self::NOINDEX, 1);
    }
}
