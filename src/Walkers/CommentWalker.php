<?php

namespace SystemScripts\Walkers;

/**
 * Accessible, Tailwind-styled comment markup.
 */
class CommentWalker extends \Walker_Comment
{
    protected function html5_comment($comment, $depth, $args)
    {
        $tag                = ('div' === $args['style']) ? 'div' : 'li';
        $commenter          = wp_get_current_commenter();
        $show_pending_links = !empty($commenter['comment_author']);
        $is_pending         = '0' === $comment->comment_approved;

        $moderation_note = $commenter['comment_author_email']
            ? __('Your comment is awaiting moderation.', 'systemscripts')
            : __('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.', 'systemscripts');

        $author = ($is_pending && !$show_pending_links)
            ? esc_html(get_comment_author($comment))
            : get_comment_author_link($comment);
        ?>
        <<?php echo esc_html($tag); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body flex gap-4 md:gap-6">
                <?php if (0 !== $args['avatar_size']): ?>
                    <div class="shrink-0">
                        <?php
                        // Decorative: the author name is announced next to it.
                        echo get_avatar($comment, (int) $args['avatar_size'], '', '', ['class' => 'size-12 rounded-full object-cover']); // phpcs:ignore WordPress.Security.EscapeOutput
                        ?>
                    </div>
                <?php endif; ?>

                <div class="min-w-0 grow">
                    <footer class="comment-meta flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="font-semibold"><?php echo $author; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
                        <span class="flex gap-4 text-sm text-muted">
                            <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                                <time datetime="<?php echo esc_attr(get_comment_time('c')); ?>">
                                    <?php
                                    printf(
                                        /* translators: 1: comment date, 2: comment time */
                                        esc_html__('%1$s at %2$s', 'systemscripts'),
                                        esc_html(get_comment_date('', $comment)),
                                        esc_html(get_comment_time())
                                    );
                                    ?>
                                </time>
                            </a>
                            <?php edit_comment_link(esc_html__('Edit', 'systemscripts'), '<span class="edit-link">', '</span>'); ?>
                        </span>
                    </footer>

                    <?php if ($is_pending): ?>
                        <p class="comment-awaiting-moderation mt-2 text-sm italic text-muted"><?php echo esc_html($moderation_note); ?></p>
                    <?php endif; ?>

                    <div class="comment-content my-4"><?php comment_text(); ?></div>

                    <?php
                    if ('1' === $comment->comment_approved || $show_pending_links) {
                        comment_reply_link(array_merge($args, [
                            'add_below' => 'div-comment',
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'before'    => '<div class="reply text-sm font-semibold">',
                            'after'     => '</div>',
                        ]));
                    }
                    ?>
                </div>
            </article>
        <?php
    }
}
