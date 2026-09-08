<?php

declare(strict_types=1);

namespace Draagvlak\Features\Home\Pages;

use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Page;
use Draagvlak\Features\Score\Outcome;
use Draagvlak\Features\Score\ScoreRules;

/**
 * Home: your number, then the people behind it.
 *
 * The order on this screen is the argument of the whole prototype. You open the
 * app and the first thing you see is what you are losing, not who you know.
 * Every choice is a POST that ends in a redirect, so the app can tell you
 * afterwards what it cost.
 */
final class HomePage extends Page
{
    protected function title(): string
    {
        return 'Jouw draagvlak';
    }

    protected function description(): ?string
    {
        return 'Je sociale steun in één cijfer.';
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('home');

        $userId = $this->app->auth->id();

        $outcome = match ($this->app->request->input('action')) {
            'answer' => $this->app->messages->answer($userId, $this->app->request->inputInt('messageId')),
            'postpone' => $this->app->messages->postpone($userId, $this->app->request->inputInt('messageId')),
            'remove' => $this->app->contacts->remove($userId, $this->app->request->inputInt('contactId')),
            'plus' => $this->app->messages->takePlus($userId),
            default => null,
        };

        if ($outcome instanceof Outcome) {
            $this->app->session->flash($outcome->result, $outcome->values());
        }

        Response::redirect('home');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        $account = $this->app->auth->user();

        // Reading the inbox starts the clock on anything with a response
        // window, so this line is the moment the pressure begins.
        $messages = $this->app->messages->openForScreen($account->id);
        $offerElapsed = $this->app->scores->offerSecondsElapsed();

        return [
            'account' => $account,
            'messages' => $messages,
            'contacts' => $this->app->contacts->active($account->id),
            'handled' => $this->app->messages->handled($account->id),
            'offerPriceCents' => ScoreRules::plusPrice($offerElapsed),
            'offerSecondsLeft' => max(0, ScoreRules::PLUS_OFFER_SECONDS - $offerElapsed),
        ];
    }
}
