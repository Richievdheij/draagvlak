<?php

declare(strict_types=1);

use Draagvlak\Features\Contacts\Pages\ContactsPage;

/**
 * Contacts: your own code, and everybody on your list.
 *
 * The URL, and nothing else. What it does is in src/Features/Contacts/Pages/ContactsPage.php
 * and what it looks like is in views/features/contacts/contacts.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new ContactsPage($app))->handle();
