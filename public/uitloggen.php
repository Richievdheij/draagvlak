<?php

declare(strict_types=1);

/**
 * Log out and throw the session away.
 *
 * There is nothing to see here: the screen only clears the session and sends
 * the visitor back to the login form. Between two participants this is what
 * gives the next one a clean start.
 */

require __DIR__ . '/../bootstrap.php';

logOut();

flash('Je bent uitgelogd.', 'info');

redirect('inloggen');
