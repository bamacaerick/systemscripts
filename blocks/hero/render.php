<?php
/**
 * Hero block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 */

use SystemScripts\Blocks;

if ($attributes['heading'] === '') {
    echo Blocks::placeholder(__('Hero: add a heading in the block settings.', 'systemscripts')); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

[$surface, $dark] = Blocks::surface($attributes['style']);
$tag      = Blocks::heading_tag($attributes['level']);
$title_id = wp_unique_id('ss-hero-title-');
$image    = Blocks::image((int) $attributes['image'], 'w-full rounded-lg object-cover');
$reverse  = $attributes['imageSide'] === 'left' ? 'md:flex-row-reverse' : 'md:flex-row';
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'ss-hero ' . $surface . ' py-12 md:py-20']); // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <div class="mx-auto flex max-w-(--breakpoint-xl) flex-col gap-10 px-6 <?php echo $image ? esc_attr($reverse . ' md:items-center') : ''; ?>">
        <div class="space-y-6 <?php echo $image ? 'md:w-1/2' : 'max-w-3xl'; ?>">
            <?php if ($attributes['eyebrow'] !== ''): ?>
                <p class="text-sm font-semibold uppercase tracking-wide"><?php echo esc_html($attributes['eyebrow']); ?></p>
            <?php endif; ?>

            <<?php echo tag_escape($tag); ?> id="<?php echo esc_attr($title_id); ?>" class="text-4xl font-semibold tracking-tight text-balance md:text-6xl"><?php echo esc_html($attributes['heading']); ?></<?php echo tag_escape($tag); ?>>

            <?php if ($attributes['text'] !== ''): ?>
                <div class="space-y-4 text-lg md:text-xl"><?php echo Blocks::paragraphs($attributes['text']); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            <?php endif; ?>

            <?php if ($attributes['primaryLabel'] !== '' || $attributes['secondaryLabel'] !== ''): ?>
                <div class="flex flex-wrap gap-3">
                    <?php echo Blocks::button($attributes['primaryLabel'], $attributes['primaryUrl'], false, $dark); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <?php echo Blocks::button($attributes['secondaryLabel'], $attributes['secondaryUrl'], true, $dark); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($image): ?>
            <div class="md:w-1/2"><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
        <?php endif; ?>
    </div>
</section>
