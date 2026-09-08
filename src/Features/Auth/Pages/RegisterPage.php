<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth\Pages;

use Draagvlak\Core\Http\Response;
use Draagvlak\Core\Page;

/**
 * Make an account.
 *
 * A new account starts on the same number as everybody else and with an empty
 * list. You get a code of your own and somebody who has it can add you, which
 * is why this screen sends you to your contacts.
 */
final class RegisterPage extends Page
{
    protected function title(): string
    {
        return 'Account maken';
    }

    protected function authorise(): void
    {
        $this->app->auth->requireGuest();
    }

    protected function submit(): void
    {
        $this->requireValidCsrf('register');

        $name = $this->app->request->text('name');
        $email = $this->app->request->text('email');
        $password = $this->app->request->text('password');

        $errors = $this->app->registration->validate($name, $email, $password);

        if ($errors !== []) {
            $this->app->session->rememberForm(['name' => $name, 'email' => $email], $errors);

            Response::redirect('register');
        }

        $account = $this->app->accounts->create($name, $email, $password);

        $this->app->auth->logIn($account);
        $this->app->session->flash('account_created', ['code' => $account->contactCode]);

        Response::redirect('contacts');
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['form' => $this->app->session->takeForm()];
    }
}
