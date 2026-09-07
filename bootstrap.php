<?php

declare(strict_types=1);

/**
 * Bootstrap for every page in public/.
 *
 * A page starts with one require of this file and can use every helper in src/
 * straight away, without a single import line. This file sets the paths, the
 * error handling, the timezone and the session, and it loads every PHP file in
 * src/ by itself, so adding a helper never means editing composer.json.
 */

/** Root of the project. Every other path is derived from this one. */
const BASE_PATH = __DIR__;

/** PHP helpers. Every file in here is loaded automatically, see below. */
const SRC_PATH = BASE_PATH . '/src';

/** Layouts and shared fragments. */
const VIEWS_PATH = BASE_PATH . '/views';

/** JSON files that stand in for a database. */
const DATA_PATH = BASE_PATH . '/data';

/** The only web-reachable folder. Used to resolve asset URLs. */
const PUBLIC_PATH = BASE_PATH . '/public';

/** Name of the product, printed in the title bar and the header. */
const APP_NAME = 'Draagvlak';

/** Language of everything a visitor reads. */
const APP_LOCALE = 'nl';

/**
 * Show every error while developing. Set this to false before you demo the
 * prototype to an audience, so a stack trace never lands on screen.
 */
const DEBUG = true;

error_reporting(DEBUG ? E_ALL : 0);
ini_set('display_errors', DEBUG ? '1' : '0');

date_default_timezone_set('Europe/Amsterdam');
mb_internal_encoding('UTF-8');

/*
 * Composer's autoloader, for dependencies and for any class you write in src/.
 * It is optional on purpose: the helpers below load without it, so a checkout
 * where someone forgot "composer install" still shows a page instead of a wall
 * of text.
 */
$autoloadPath = BASE_PATH . '/vendor/autoload.php';

if (is_file($autoloadPath)) {
    require $autoloadPath;
}

/*
 * Load every PHP file in src/, sorted, so the same files load in the same order
 * on every machine. This is what replaces the old list of imports: drop a file
 * in src/, and its functions exist on every page.
 *
 * A file in src/ therefore only declares things. Code that runs on its own does
 * not belong there, because it would run on every request of every page.
 */
(static function (): void {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(SRC_PATH, FilesystemIterator::SKIP_DOTS)
    );

    $paths = [];

    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $paths[] = $file->getPathname();
        }
    }

    sort($paths);

    foreach ($paths as $path) {
        require_once $path;
    }
})();

startSession();
