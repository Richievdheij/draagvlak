<?php

declare(strict_types=1);

use Draagvlak\Features\Reset\Pages\ResetPage;

/**
 * Start over between two participants.
 *
 * The URL, and nothing else. What it does is in src/Features/Reset/Pages/ResetPage.php
 * and what it looks like is in views/features/reset/reset.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new ResetPage($app))->handle();
