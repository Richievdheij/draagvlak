<?php

declare(strict_types=1);

namespace Draagvlak\Features\Contacts\Pages;

use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Page;
use Draagvlak\Features\Score\Outcome;

/**
 * Contacts: your own code, and everybody on your list.
 *
 * This is where a list starts. You share the code under your name, somebody
 * types it in on their own screen, and from that moment the two of you are on
 * each other's list. Nobody is added for you.
 */
final class ContactsPage extends Page
{
    protected function title(): string
    {
        return 'Contacten';
    }

    protected function description(): ?string
    {
        return 'De mensen achter je cijfer.';
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('contacts');

        $account = $this->app->auth->user();

        $outcome = match ($this->app->request->input('action')) {
            'add' => $this->app->contacts->addByCode($account, $this->app->request->text('code')),
            'remove' => $this->app->contacts->remove($account->id, $this->app->request->inputInt('contactId')),
            default => null,
        };

        if ($outcome instanceof Outcome) {
            $this->app->session->flash($outcome->result, $outcome->values());
        }

        Response::redirect('contacts');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        $account = $this->app->auth->user();

        return [
            'account' => $account,
            'contacts' => $this->app->contacts->active($account->id),
        ];
    }
}
