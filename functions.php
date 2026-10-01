<?php
/**
 * SystemScripts theme bootstrap.
 *
 * @package SystemScripts
 */

defined('ABSPATH') || exit;

define('SS_VERSION', wp_get_theme()->get('Version'));
define('SS_DIR', get_template_directory());
define('SS_URI', get_template_directory_uri());

// Minimal PSR-4 autoloader (SystemScripts\ => src/), so Composer is not needed in production.
spl_autoload_register(static function (string $class): void {
    $prefix = 'SystemScripts\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $file = SS_DIR . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

SystemScripts\Setup::boot();
SystemScripts\Assets::boot();
SystemScripts\Customizer::boot();
SystemScripts\Security::boot();
SystemScripts\Comments::boot();
SystemScripts\Author::boot();
SystemScripts\Analytics::boot();
