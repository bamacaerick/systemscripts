<?php
/**
 * Call to action block.
 *
 * @var array $attributes Block attributes.
 */

use SystemScripts\Blocks;

if ($attributes['heading'] === '') {
    echo Blocks::placeholder(__('Call to action: add a heading in the block settings.', 'systemscripts')); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

[$surface, $dark] = Blocks::surface($attributes['style']);
$tag      = Blocks::heading_tag($attributes['level']);
$title_id = wp_unique_id('ss-cta-title-');
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'ss-cta ' . $surface . ' py-12']); // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <div class="mx-auto flex max-w-3xl flex-col items-center gap-4 px-6 text-center">
        <<?php echo $tag; ?> id="<?php echo esc_attr($title_id); ?>" class="text-3xl font-semibold text-balance md:text-4xl"><?php echo esc_html($attributes['heading']); ?></<?php echo $tag; ?>>
        <?php if ($attributes['text'] !== ''): ?>
            <div class="text-lg"><?php echo Blocks::paragraphs($attributes['text']); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
        <?php endif; ?>
        <?php echo Blocks::button($attributes['buttonLabel'], $attributes['buttonUrl'], false, $dark); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </div>
</section>
