<?php
/**
 * Theme footer template.
 *
 * @package SystemScripts
 */
?>
        </main>

        <?php do_action('ss_content_end'); ?>
    </div>

    <?php do_action('ss_content_after'); ?>

    <footer id="colophon" class="bg-light/50 mt-12" role="contentinfo">
        <div class="container mx-auto py-12">
            <?php do_action('ss_footer'); ?>
            <div class="text-sm text-zinc-700">
                &copy; <?php echo esc_html(date_i18n('Y')); ?> - <?php bloginfo('name'); ?>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
