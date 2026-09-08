<?php

declare(strict_types=1);

use Draagvlak\Features\Messages\Pages\ConversationPage;

/**
 * One conversation, with the clock running.
 *
 * The URL, and nothing else. What it does is in src/Features/Messages/Pages/ConversationPage.php
 * and what it looks like is in views/features/messages/conversation.php.
 */

$app = require __DIR__ . '/../bootstrap.php';

(new ConversationPage($app))->handle();
