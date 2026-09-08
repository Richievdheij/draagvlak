<?php

declare(strict_types=1);

use Draagvlak\Features\Home\Pages\HomePage;

/**
 * Home: your number, then the people behind it.
 *
 * The URL, and nothing else. What it does is in src/Features/Home/Pages/HomePage.php
 * and what it looks like is in views/features/home/home.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new HomePage($app))->handle();
