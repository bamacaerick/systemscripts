<?php
/**
 * Card grid block.
 *
 * @var array $attributes Block attributes.
 */

use SystemScripts\Blocks;

$items = array_filter((array) $attributes['items'], static fn($item) => !empty($item['title']));

if (!$items) {
    echo Blocks::placeholder(__('Card grid: add at least one card with a title.', 'systemscripts')); // phpcs:ignore WordPress.Security.EscapeOutput
    return;
}

// Full class names so Tailwind can find them.
$columns = [
    '2' => 'md:grid-cols-2',
    '3' => 'md:grid-cols-2 lg:grid-cols-3',
    '4' => 'md:grid-cols-2 lg:grid-cols-4',
][$attributes['columns']] ?? 'md:grid-cols-2 lg:grid-cols-3';

$has_heading = $attributes['heading'] !== '';
$tag         = Blocks::heading_tag($attributes['level']);
$card_tag    = $has_heading ? Blocks::child_heading_tag($tag) : 'h2';
$title_id    = wp_unique_id('ss-cards-title-');
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'ss-cards py-12']); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo $has_heading ? 'aria-labelledby="' . esc_attr($title_id) . '"' : ''; ?>>
    <div class="mx-auto max-w-(--breakpoint-xl) space-y-8 in-[.alignfull]:px-6">
        <?php if ($has_heading || $attributes['intro'] !== ''): ?>
            <div class="max-w-3xl space-y-3">
                <?php if ($has_heading): ?>
                    <<?php echo tag_escape($tag); ?> id="<?php echo esc_attr($title_id); ?>" class="text-3xl font-semibold md:text-4xl"><?php echo esc_html($attributes['heading']); ?></<?php echo tag_escape($tag); ?>>
                <?php endif; ?>
                <?php if ($attributes['intro'] !== ''): ?>
                    <div class="text-lg text-muted"><?php echo Blocks::paragraphs($attributes['intro']); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <ul class="grid list-none gap-6 p-0 <?php echo esc_attr($columns); ?>">
            <?php foreach ($items as $item): ?>
                <?php
                $url   = (string) ($item['url'] ?? '');
                $image = Blocks::image((int) ($item['image'] ?? 0), 'mt-0 aspect-16/10 w-full object-cover', 'medium_large');
                ?>
                <li class="relative flex flex-col overflow-hidden p-0 rounded-lg border border-zinc-200 bg-white <?php echo $url ? 'focus-within:outline-3 focus-within:outline-primary hover:shadow-md' : ''; ?>">
                    <?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <div class="mt-0 space-y-2 p-6">
                        <<?php echo tag_escape($card_tag); ?> class="text-xl font-semibold">
                            <?php if ($url): ?>
                                <a class="text-dark no-underline after:absolute after:inset-0 focus-visible:outline-none" href="<?php echo esc_url($url); ?>"><?php echo esc_html($item['title']); ?></a>
                            <?php else: ?>
                                <?php echo esc_html($item['title']); ?>
                            <?php endif; ?>
                        </<?php echo tag_escape($card_tag); ?>>
                        <?php if (!empty($item['text'])): ?>
                            <div class="text-muted"><?php echo Blocks::paragraphs((string) $item['text']); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
