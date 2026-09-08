<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Pages\RegisterPage;

/**
 * Make an account.
 *
 * The URL, and nothing else. What it does is in src/Features/Auth/Pages/RegisterPage.php
 * and what it looks like is in views/features/auth/register.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new RegisterPage($app))->handle();
