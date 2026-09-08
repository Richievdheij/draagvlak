<?php

declare(strict_types=1);

use Draagvlak\Features\Settings\Pages\SettingsPage;

/**
 * Settings: what you can switch off, and what only looks like you can.
 *
 * The URL, and nothing else. What it does is in src/Features/Settings/Pages/SettingsPage.php
 * and what it looks like is in views/features/settings/settings.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new SettingsPage($app))->handle();
