<?php

declare(strict_types=1);

/**
 * Project settings.
 *
 * These defaults match a standard Herd, Laragon or XAMPP install: MySQL on
 * localhost with the user root and no password. Works on a fresh checkout
 * without editing anything.
 *
 * Do you need different values on your machine? Copy this file to
 * config.local.php and change only the lines you need. That file is ignored by
 * Git, so your password never ends up in the repository:
 *
 *     return ['db' => ['user' => 'draagvlak', 'password' => 'secret']];
 */

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'draagvlak',
        'user' => 'root',
        'password' => '',
    ],
];
