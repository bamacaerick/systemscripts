<?php

namespace SystemScripts;

/**
 * Content authorship: profile fields (E-E-A-T), author data for templates and schema,
 * and the optional "disable author archives" switch.
 *
 * User meta: ss_job_title, ss_knows_about (comma separated), ss_same_as (one URL per line).
 */
class Author
{
    public static function boot(): void
    {
        add_action('show_user_profile', [self::class, 'profile_fields']);
        add_action('edit_user_profile', [self::class, 'profile_fields']);
        add_action('personal_options_update', [self::class, 'save_profile_fields']);
        add_action('edit_user_profile_update', [self::class, 'save_profile_fields']);

        if (Customizer::get('disable_author_archives')) {
            add_action('template_redirect', [self::class, 'redirect_archive']);
            add_filter('wp_sitemaps_add_provider', static fn($provider, $name) => $name === 'users' ? false : $provider, 10, 2);
        }
    }

    public static function archive_enabled(): bool
    {
        return !Customizer::get('disable_author_archives');
    }

    public static function redirect_archive(): void
    {
        if (is_author()) {
            wp_safe_redirect(home_url('/'), 301);
            exit;
        }
    }

    /**
     * @return array{id:int,name:string,url:string,description:string,job_title:string,knows_about:string[],same_as:string[]}
     */
    public static function data(int $user_id): array
    {
        $knows = array_filter(array_map('trim', explode(',', (string) get_user_meta($user_id, 'ss_knows_about', true))));
        $same  = array_filter(array_map('esc_url_raw', preg_split('/\R/', (string) get_user_meta($user_id, 'ss_same_as', true)) ?: []));

        return [
            'id'          => $user_id,
            'name'        => (string) get_the_author_meta('display_name', $user_id),
            'url'         => self::archive_enabled() ? get_author_posts_url($user_id) : '',
            'description' => (string) get_the_author_meta('description', $user_id),
            'job_title'   => (string) get_user_meta($user_id, 'ss_job_title', true),
            'knows_about' => array_values($knows),
            'same_as'     => array_values($same),
        ];
    }

    /**
     * Escaped author name, linked (rel="author") to the archive when archives are enabled.
     */
    public static function name_html(int $user_id): string
    {
        $name = esc_html((string) get_the_author_meta('display_name', $user_id));
        $url  = self::archive_enabled() ? get_author_posts_url($user_id) : '';

        return $url ? sprintf('<a href="%s" rel="author">%s</a>', esc_url($url), $name) : $name;
    }

    public static function profile_fields(\WP_User $user): void
    {
        ?>
        <h2><?php esc_html_e('Author details (SystemScripts)', 'systemscripts'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="ss_job_title"><?php esc_html_e('Job title', 'systemscripts'); ?></label></th>
                <td><input type="text" name="ss_job_title" id="ss_job_title" class="regular-text" value="<?php echo esc_attr((string) get_user_meta($user->ID, 'ss_job_title', true)); ?>"></td>
            </tr>
            <tr>
                <th><label for="ss_knows_about"><?php esc_html_e('Topics of expertise', 'systemscripts'); ?></label></th>
                <td>
                    <input type="text" name="ss_knows_about" id="ss_knows_about" class="regular-text" value="<?php echo esc_attr((string) get_user_meta($user->ID, 'ss_knows_about', true)); ?>">
                    <p class="description"><?php esc_html_e('Comma separated. Used in structured data (knowsAbout).', 'systemscripts'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="ss_same_as"><?php esc_html_e('Profile links', 'systemscripts'); ?></label></th>
                <td>
                    <textarea name="ss_same_as" id="ss_same_as" rows="4" class="large-text code"><?php echo esc_textarea((string) get_user_meta($user->ID, 'ss_same_as', true)); ?></textarea>
                    <p class="description"><?php esc_html_e('One URL per line (LinkedIn, GitHub, X...). Used for the author box and structured data (sameAs).', 'systemscripts'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_profile_fields(int $user_id): void
    {
        // Core verifies the profile form nonce before these hooks fire; still check capability.
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        // phpcs:disable WordPress.Security.NonceVerification
        if (isset($_POST['ss_job_title'])) {
            update_user_meta($user_id, 'ss_job_title', sanitize_text_field(wp_unslash($_POST['ss_job_title'])));
        }

        if (isset($_POST['ss_knows_about'])) {
            update_user_meta($user_id, 'ss_knows_about', sanitize_text_field(wp_unslash($_POST['ss_knows_about'])));
        }

        if (isset($_POST['ss_same_as'])) {
            update_user_meta($user_id, 'ss_same_as', Customizer::sanitize_urls(wp_unslash($_POST['ss_same_as'])));
        }
        // phpcs:enable
    }
}
