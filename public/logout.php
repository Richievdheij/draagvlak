<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Pages\LogoutPage;

/**
 * Log out and throw the visit away.
 *
 * The URL, and nothing else. There is no screen and no template: this one
 * clears the session and sends the visitor back to the login form.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new LogoutPage($app))->handle();
