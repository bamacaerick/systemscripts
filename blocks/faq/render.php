<?php
/**
 * FAQ block. Uses native <details>/<summary>: keyboard and screen reader accessible without JS.
 * FAQPage structured data is generated from the same attributes by SystemScripts\Schema.
 *
 * @var array $attributes Block attributes.
 */

use SystemScripts\Blocks;

$items = array_filter((array) $attributes['items'], static fn($item) => !empty($item['question']) && !empty($item['answer']));

if (!$items) {
    echo Blocks::placeholder(__('FAQ: add at least one question and answer.', 'systemscripts')); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

$has_heading = $attributes['heading'] !== '';
$tag         = Blocks::heading_tag($attributes['level']);
$title_id    = wp_unique_id('ss-faq-title-');
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'ss-faq py-8']); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo $has_heading ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
    <?php if ($has_heading): ?>
        <<?php echo tag_escape($tag); ?> id="<?php echo esc_attr($title_id); ?>" class="mb-4 text-2xl font-semibold"><?php echo esc_html($attributes['heading']); ?></<?php echo tag_escape($tag); ?>>
    <?php endif; ?>

    <div class="divide-y divide-zinc-200 border-y border-zinc-200">
        <?php foreach ($items as $item): ?>
            <details class="group py-1">
                <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 py-3 font-semibold marker:hidden [&::-webkit-details-marker]:hidden">
                    <span><?php echo esc_html($item['question']); ?></span>
                    <svg class="size-5 shrink-0 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </summary>
                <div class="space-y-3 pb-4 text-muted"><?php echo Blocks::paragraphs((string) $item['answer']); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
