<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth\Pages;

use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Page;

/**
 * Log in. Wrong attempts are counted; after five the form closes for a while.
 */
final class LoginPage extends Page
{
    protected function title(): string
    {
        return 'Inloggen';
    }

    protected function authorise(): void
    {
        $this->app->auth->requireGuest();
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('login');

        $throttle = $this->app->auth->throttle();

        if ($throttle->isLocked()) {
            $this->app->session->flash('login_locked', ['minutes' => $throttle->minutesLeft()]);

            Response::redirect('login');
        }

        $email = $this->app->request->text('email');

        if ($this->app->auth->attempt($email, $this->app->request->text('password'))) {
            Response::redirect($this->app->auth->takeIntendedScreen());
        }

        $this->app->session->rememberForm(['email' => $email], ['password' => 'credentials_wrong']);
        $this->app->session->flash('credentials_wrong');

        Response::redirect('login');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['form' => $this->app->session->takeForm()];
    }
}
