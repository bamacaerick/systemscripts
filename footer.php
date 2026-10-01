<?php
/**
 * Theme footer template.
 *
 * @package SystemScripts
 */

$social = array_filter(preg_split('/\R/', (string) SystemScripts\Customizer::get('org_social')) ?: []);
?>
        <?php do_action('ss_content_end'); ?>
    </main>

    <?php do_action('ss_content_after'); ?>

    <footer id="colophon" class="mt-12 bg-light">
        <div class="container py-12">
            <?php do_action('ss_footer'); ?>

            <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2')): ?>
                <div class="mb-8 grid gap-8 md:grid-cols-2">
                    <?php dynamic_sidebar('footer-1'); ?>
                    <?php dynamic_sidebar('footer-2'); ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-col gap-4 text-sm text-muted md:flex-row md:items-center md:justify-between">
                <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?></p>

                <?php if (has_nav_menu('footer')): ?>
                    <nav aria-label="<?php esc_attr_e('Footer', 'systemscripts'); ?>">
                        <?php
                        wp_nav_menu([
                            'container'      => false,
                            'menu_class'     => 'flex flex-wrap gap-x-6 gap-y-2',
                            'theme_location' => 'footer',
                            'depth'          => 1,
                            'link_class'     => 'inline-block py-1',
                            'fallback_cb'    => false,
                        ]);
                        ?>
                    </nav>
                <?php endif; ?>

                <?php if ($social): ?>
                    <ul class="flex flex-wrap gap-x-6 gap-y-2">
                        <?php foreach ($social as $url): ?>
                            <li>
                                <a class="inline-block py-1" href="<?php echo esc_url($url); ?>" rel="me noopener">
                                    <?php echo esc_html(preg_replace('#^www\.#', '', (string) wp_parse_url($url, PHP_URL_HOST))); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
