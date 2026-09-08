<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth\Pages;

use Draagvlak\Core\Action;
use Draagvlak\Core\Http\Response;

/**
 * Log out and throw the visit away. Between two participants this is what
 * gives the next one a clean start.
 */
final class LogoutPage extends Action
{
    public function handle(): never
    {
        $this->app->auth->logOut();
        $this->app->session->flash('logged_out');

        Response::redirect('login');
    }
}
