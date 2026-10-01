<?php
/**
 * Author box shown under posts.
 *
 * @package SystemScripts
 */

use SystemScripts\Author;

$author = Author::data((int) get_the_author_meta('ID'));

if (!apply_filters('ss_show_author_box', $author['description'] !== '' || $author['job_title'] !== '', $author)) {
    return;
}
?>
<section aria-labelledby="author-box-title" class="flex gap-4 rounded-lg bg-light p-6">
    <div class="shrink-0">
        <?php echo get_avatar($author['id'], 64, '', '', ['class' => 'size-16 rounded-full']); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
    <div class="space-y-2">
        <h2 id="author-box-title" class="text-lg font-semibold">
            <span class="text-sm font-normal text-muted"><?php esc_html_e('Written by', 'systemscripts'); ?></span><br>
            <?php echo Author::name_html($author['id']); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>
        </h2>
        <?php if ($author['job_title']): ?>
            <p class="text-sm text-muted"><?php echo esc_html($author['job_title']); ?></p>
        <?php endif; ?>
        <?php if ($author['description']): ?>
            <p><?php echo esc_html($author['description']); ?></p>
        <?php endif; ?>
        <?php if ($author['same_as']): ?>
            <ul class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <?php foreach ($author['same_as'] as $url): ?>
                    <li><a href="<?php echo esc_url($url); ?>" rel="me noopener"><?php echo esc_html(preg_replace('#^www\.#', '', (string) wp_parse_url($url, PHP_URL_HOST))); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
