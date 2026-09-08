<?php

declare(strict_types=1);

use Draagvlak\Features\Contacts\Pages\EmergencyContactsPage;

/**
 * Emergency contacts: who has you on their list.
 *
 * The URL, and nothing else. What it does is in src/Features/Contacts/Pages/EmergencyContactsPage.php
 * and what it looks like is in views/features/contacts/emergency-contacts.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new EmergencyContactsPage($app))->handle();
