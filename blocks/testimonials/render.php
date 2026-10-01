<?php
/**
 * Testimonials block.
 *
 * @var array $attributes Block attributes.
 */

use SystemScripts\Blocks;

$items = array_filter((array) $attributes['items'], static fn($item) => !empty($item['quote']));

if (!$items) {
    echo Blocks::placeholder(__('Testimonials: add at least one quote.', 'systemscripts')); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

$has_heading = $attributes['heading'] !== '';
$tag         = Blocks::heading_tag($attributes['level']);
$title_id    = wp_unique_id('ss-testimonials-title-');
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'ss-testimonials py-12']); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo $has_heading ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
    <div class="mx-auto max-w-(--breakpoint-xl) space-y-8 px-6">
        <?php if ($has_heading): ?>
            <<?php echo $tag; ?> id="<?php echo esc_attr($title_id); ?>" class="text-3xl font-semibold md:text-4xl"><?php echo esc_html($attributes['heading']); ?></<?php echo $tag; ?>>
        <?php endif; ?>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($items as $item): ?>
                <figure class="flex flex-col justify-between gap-4 rounded-lg bg-light p-6">
                    <blockquote class="text-lg">
                        <?php echo Blocks::paragraphs((string) $item['quote']); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </blockquote>
                    <?php if (!empty($item['name'])): ?>
                        <figcaption class="text-sm">
                            <span class="font-semibold"><?php echo esc_html($item['name']); ?></span>
                            <?php if (!empty($item['role'])): ?>
                                <span class="block text-muted"><?php echo esc_html($item['role']); ?></span>
                            <?php endif; ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
