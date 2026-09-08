<?php

declare(strict_types=1);

use Draagvlak\Core\App;

/**
 * Bootstrap for every entry point in public/ and bin/.
 *
 * It registers the autoloader for the Draagvlak namespace and hands back the
 * one App object that holds every service, so a screen file is four lines:
 *
 *     $app = require __DIR__ . '/../bootstrap.php';
 *
 *     (new HomePage($app))->handle();
 *
 * The autoloader is written out here instead of taken from Composer, so a
 * checkout where nobody ran "composer install" still shows a page. Composer's
 * autoloader is used when it happens to be there, because an editor reads
 * composer.json to find the same classes.
 *
 * @return App
 */

/** Root of the project. The only path that cannot be derived from another. */
const BASE_PATH = __DIR__;

spl_autoload_register(static function (string $class): void {
    $prefix = 'Draagvlak\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $file = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$composerAutoload = BASE_PATH . '/vendor/autoload.php';

if (is_file($composerAutoload)) {
    require $composerAutoload;
}

return App::boot(BASE_PATH);
