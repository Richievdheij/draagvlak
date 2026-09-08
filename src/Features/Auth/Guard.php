<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth;

use Draagvlak\Core\Http\Request;
use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Http\Session;
use RuntimeException;

/**
 * Who is looking at this screen, and whether they are allowed to. Both
 * require methods end the request when the answer is no, so everything after
 * them can assume it was yes.
 */
final class Guard
{
    private ?Account $account = null;

    private bool $loaded = false;

    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly Session $session,
        private readonly Request $request,
        private readonly LoginThrottle $throttle,
    ) {}

    /** The visitor, or null when nobody is logged in. Read once per request. */
    public function account(): ?Account
    {
        if ($this->loaded) {
            return $this->account;
        }

        $this->loaded = true;
        $userId = $this->session->getInt('userId');

        if ($userId === 0) {
            return null;
        }

        $this->account = $this->accounts->find($userId);

        if ($this->account === null) {
            // The account was removed while the session was still open.
            $this->session->forget('userId');
        }

        return $this->account;
    }

    public function isLoggedIn(): bool
    {
        return $this->account() !== null;
    }

    /** @throws RuntimeException When nobody is logged in. Guard the screen first. */
    public function user(): Account
    {
        $account = $this->account();

        if ($account === null) {
            throw new RuntimeException('No one is logged in. Guard the screen with requireLogin().');
        }

        return $account;
    }

    public function id(): int
    {
        return $this->user()->id;
    }

    /**
     * Close this screen for visitors who are not logged in. The screen they
     * wanted is remembered, so logging in takes them there.
     */
    public function requireLogin(): void
    {
        if ($this->isLoggedIn()) {
            return;
        }

        $this->session->set('intendedScreen', $this->request->screen());
        $this->session->flash('login_required');

        Response::redirect('login');
    }

    /** Keep a logged-in visitor away from the login and registration screens. */
    public function requireGuest(): void
    {
        if ($this->isLoggedIn()) {
            Response::redirect('home');
        }
    }

    /** The lock on the login form, for the screen that has to explain it. */
    public function throttle(): LoginThrottle
    {
        return $this->throttle;
    }

    public function attempt(string $email, string $password): bool
    {
        $account = $this->accounts->findByCredentials($email, $password);

        if ($account === null) {
            $this->throttle->recordFailure();

            return false;
        }

        $this->logIn($account);

        return true;
    }

    /**
     * Put an account in the session without asking for a password, so somebody
     * who just registered is not asked to type it again.
     */
    public function logIn(Account $account): void
    {
        $this->session->regenerate();
        $this->session->set('userId', $account->id);
        $this->throttle->clear();

        $this->account = $account;
        $this->loaded = true;
    }

    /** Log out and throw the whole visit away, including the offer countdown. */
    public function logOut(): void
    {
        $this->session->reset();

        $this->account = null;
        $this->loaded = true;
    }

    /** The screen somebody wanted before they were sent to the login form. */
    public function takeIntendedScreen(): string
    {
        $screen = $this->session->get('intendedScreen');
        $this->session->forget('intendedScreen');

        return \is_string($screen) && $screen !== '' ? $screen : 'home';
    }
}
