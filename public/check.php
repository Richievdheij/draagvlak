<?php

declare(strict_types=1);

use Draagvlak\Features\Screening\Pages\CheckPage;

/**
 * The check tool: the screen a landlord sees about you.
 *
 * The URL, and nothing else. What it does is in src/Features/Screening/Pages/CheckPage.php
 * and what it looks like is in views/features/screening/check.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new CheckPage($app))->handle();
