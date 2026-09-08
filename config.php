<?php

declare(strict_types=1);

/**
 * Project settings.
 *
 * The defaults below work as they are on Laravel Herd, Homebrew MySQL, Laragon
 * and XAMPP: MySQL over TCP on 127.0.0.1:3306 with the user root and no
 * password. Nothing here depends on where you cloned the project or what you
 * called the folder; every path is derived from the file it sits in.
 *
 * Do you need different values on your machine? Copy this file to
 * config.local.php and change only the lines you need. That file is ignored by
 * Git, so your password never ends up in the repository:
 *
 *     return ['db' => ['user' => 'draagvlak', 'password' => 'secret']];
 */

return [
    'app' => [
        'name' => 'Draagvlak',
        'locale' => 'nl',
        'timezone' => 'Europe/Amsterdam',

        /*
         * Show every error while developing. Set this to false in
         * config.local.php before you demo the prototype to an audience, so a
         * stack trace never lands on screen while somebody is watching.
         */
        'debug' => true,

        /*
         * Whether "composer db:setup" creates the demo account and writes the
         * scenario content. Its password is in the README, so on any server
         * that other people can reach this has to be false.
         */
        'demo' => true,
    ],

    'db' => [
        /*
         * Always TCP, never a socket. A socket path differs per machine and per
         * installer; 127.0.0.1 is the same everywhere, and it is the same
         * server phpMyAdmin talks to.
         */
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'draagvlak',
        'user' => 'root',
        'password' => '',
    ],
];
