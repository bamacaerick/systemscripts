<?php
/**
 * Theme header template.
 *
 * @package SystemScripts
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('antialiased'); ?>>
<?php wp_body_open(); ?>
<?php do_action('ss_site_before'); ?>

<a class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:shadow-md" href="#main">
    <?php esc_html_e('Skip to content', 'systemscripts'); ?>
</a>

<div id="page" class="min-h-screen flex flex-col">
    <?php do_action('ss_header'); ?>

    <header id="masthead" class="container py-6">
        <div class="md:flex md:justify-between md:items-center">
            <div class="flex justify-between items-center">
                <div class="site-branding">
                    <?php if (has_custom_logo()): ?>
                        <?php the_custom_logo(); ?>
                    <?php else: ?>
                        <p class="flex items-center gap-2 text-lg font-medium">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="no-underline" rel="home"><?php bloginfo('name'); ?></a>
                            <?php if ($description = get_bloginfo('description')): ?>
                                <span class="text-sm font-light text-muted" aria-hidden="true">|</span>
                                <span class="text-sm font-light text-muted"><?php echo esc_html($description); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (has_nav_menu('primary')): ?>
                    <div class="md:hidden">
                        <button type="button" id="primary-menu-toggle" class="inline-flex size-11 items-center justify-center rounded-md" aria-expanded="false" aria-controls="primary-navigation">
                            <span class="sr-only"><?php esc_html_e('Menu', 'systemscripts'); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" aria-hidden="true" focusable="false">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div id="primary-navigation" class="max-md:hidden md:flex gap-6 items-center rounded-xl border border-zinc-200 p-4 max-md:mt-4 md:border-none md:p-0">
                <?php if (has_nav_menu('primary')): ?>
                    <nav aria-label="<?php esc_attr_e('Primary', 'systemscripts'); ?>">
                        <?php
                        wp_nav_menu([
                            'container'      => false,
                            'menu_class'     => 'flex flex-col md:flex-row md:gap-6',
                            'theme_location' => 'primary',
                            'link_class'     => 'block py-2 text-dark no-underline hover:underline',
                            'fallback_cb'    => false,
                        ]);
                        ?>
                    </nav>
                <?php elseif (current_user_can('edit_theme_options')): ?>
                    <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="text-sm"><?php esc_html_e('Set up the primary menu', 'systemscripts'); ?></a>
                <?php endif; ?>

                <div class="mt-4 md:mt-0"><?php get_search_form(); ?></div>
            </div>
            <noscript><style>#primary-navigation{display:flex!important}#primary-menu-toggle{display:none}</style></noscript>
        </div>
    </header>

    <main id="main" class="site-main grow" tabindex="-1">
        <?php do_action('ss_content_start'); ?>
