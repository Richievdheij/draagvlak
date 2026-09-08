<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Pages\LoginPage;

/**
 * Log in.
 *
 * The URL, and nothing else. What it does is in src/Features/Auth/Pages/LoginPage.php
 * and what it looks like is in views/features/auth/login.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new LoginPage($app))->handle();
