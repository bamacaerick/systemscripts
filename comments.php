<?php
/**
 * Comments template.
 *
 * @package SystemScripts
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area mx-auto my-10 max-w-3xl sm:my-16">
    <?php if (have_comments()): ?>
        <h2 class="comments-title mb-8 text-3xl font-medium">
            <?php
            printf(
                esc_html(_nx(
                    'One comment',
                    '%1$s comments',
                    get_comments_number(),
                    'comments title',
                    'systemscripts'
                )),
                esc_html(number_format_i18n(get_comments_number()))
            );
            ?>
        </h2>

        <ol class="comment-list mb-12 [&_.children]:ml-8 md:[&_.children]:ml-16 [&_.children>li]:mt-8 space-y-8">
            <?php
            wp_list_comments([
                'format'      => 'html5',
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 56,
                'walker'      => new \SystemScripts\Walkers\CommentWalker(),
            ]);
            ?>
        </ol>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
            <nav class="comment-navigation mb-12 flex justify-between" aria-label="<?php esc_attr_e('Comment navigation', 'systemscripts'); ?>">
                <div class="nav-previous"><?php previous_comments_link(esc_html__('Older comments', 'systemscripts')); ?></div>
                <div class="nav-next"><?php next_comments_link(esc_html__('Newer comments', 'systemscripts')); ?></div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
        <p class="no-comments text-muted"><?php esc_html_e('Comments are closed.', 'systemscripts'); ?></p>
    <?php endif; ?>

    <?php comment_form(SystemScripts\Comments::form_args()); ?>
</div>
