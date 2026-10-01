<?php
/**
 * Related posts: same categories, newest first.
 *
 * @package SystemScripts
 */

if (!apply_filters('ss_show_related_posts', true)) {
    return;
}

$categories = wp_get_post_categories(get_the_ID());

if (!$categories) {
    return;
}

$related = new WP_Query([
    'category__in'        => $categories,
    'post__not_in'        => [get_the_ID()],
    'posts_per_page'      => 3,
    'no_found_rows'       => true,
    'ignore_sticky_posts' => true,
]);

if (!$related->have_posts()) {
    return;
}
?>
<section aria-labelledby="related-posts-title" class="border-t border-zinc-200 pt-8">
    <h2 id="related-posts-title" class="mb-4 text-2xl font-semibold"><?php esc_html_e('Related posts', 'systemscripts'); ?></h2>
    <ul class="space-y-3">
        <?php while ($related->have_posts()): $related->the_post(); ?>
            <li>
                <a href="<?php the_permalink(); ?>" class="font-semibold"><?php the_title(); ?></a>
                <time class="block text-sm text-muted" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
            </li>
        <?php endwhile; ?>
    </ul>
</section>
<?php wp_reset_postdata(); ?>
