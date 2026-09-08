<?php

declare(strict_types=1);

namespace Draagvlak\Features\Messages\Pages;

use Draagvlak\Core\Page;

/**
 * One conversation, with the clock running and the cost adding up.
 *
 * Empty on purpose. The class, its template in
 * views/features/messages/conversation.php and its stylesheet in
 * assets/css/features/messages/ are ready; the content is yours to build.
 * The message arrives as ?id=, which data() already looks up.
 */
final class ConversationPage extends Page
{
    protected function title(): string
    {
        return 'Gesprek';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return [
            'message' => $this->app->messages->find(
                $this->app->auth->id(),
                $this->app->request->inputInt('id'),
            ),
        ];
    }
}
