<?php

namespace SystemScripts;

/**
 * Comment form arguments and the optional global "disable comments" switch.
 */
class Comments
{
    public static function boot(): void
    {
        add_filter('comment_form_default_fields', [self::class, 'autocomplete']);

        if (Customizer::get('disable_comments')) {
            add_action('init', [self::class, 'disable'], 100);
            add_filter('comments_open', '__return_false', 20);
            add_filter('pings_open', '__return_false', 20);
            add_filter('comments_array', '__return_empty_array', 20);
            add_action('admin_menu', static fn() => remove_menu_page('edit-comments.php'));
            add_action('wp_before_admin_bar_render', static function (): void {
                $GLOBALS['wp_admin_bar']->remove_menu('comments');
            });
        }
    }

    public static function enabled(): bool
    {
        return !Customizer::get('disable_comments');
    }

    public static function disable(): void
    {
        foreach (get_post_types() as $type) {
            if (post_type_supports($type, 'comments')) {
                remove_post_type_support($type, 'comments');
                remove_post_type_support($type, 'trackbacks');
            }
        }
    }

    /**
     * WCAG 1.3.5: identify the purpose of common inputs.
     */
    public static function autocomplete(array $fields): array
    {
        $map = ['author' => 'name', 'email' => 'email', 'url' => 'url'];

        foreach ($map as $key => $token) {
            if (isset($fields[$key]) && !str_contains($fields[$key], 'autocomplete=')) {
                $fields[$key] = preg_replace('/<input /', '<input autocomplete="' . $token . '" ', $fields[$key], 1);
            }
        }

        return $fields;
    }

    public static function form_args(): array
    {
        return [
            'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title mb-4 text-2xl font-semibold">',
            'title_reply_after'    => '</h2>',
            'class_form'           => 'comment-form space-y-4',
            'class_submit'         => 'ss-button',
            'comment_notes_before' => '<p class="comment-notes text-sm text-muted">' . esc_html__('Your email address will not be published. Required fields are marked with *.', 'systemscripts') . '</p>',
            'logged_in_as'         => '<p class="logged-in-as text-sm">' . esc_html__('Logged in. You can leave a comment below.', 'systemscripts') . '</p>',
        ];
    }
}
