<?php
/**
 * Heading for author archives (the profile page).
 *
 * @package SystemScripts
 */

use SystemScripts\Author;

$author = Author::data((int) get_queried_object_id());
?>
<header class="mb-8 flex gap-6">
    <div class="shrink-0">
        <?php echo get_avatar($author['id'], 96, '', '', ['class' => 'size-24 rounded-full']); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
    <div class="space-y-2">
        <h1 class="text-3xl font-semibold md:text-4xl"><?php echo esc_html($author['name']); ?></h1>
        <?php if ($author['job_title']): ?>
            <p class="text-muted"><?php echo esc_html($author['job_title']); ?></p>
        <?php endif; ?>
        <?php if ($author['description']): ?>
            <p class="max-w-2xl text-lg"><?php echo esc_html($author['description']); ?></p>
        <?php endif; ?>
        <?php if ($author['same_as']): ?>
            <ul class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                <?php foreach ($author['same_as'] as $url): ?>
                    <li><a href="<?php echo esc_url($url); ?>" rel="me noopener"><?php echo esc_html(preg_replace('#^www\.#', '', (string) wp_parse_url($url, PHP_URL_HOST))); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</header>
