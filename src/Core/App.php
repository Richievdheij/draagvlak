<?php

declare(strict_types=1);

namespace Draagvlak\Core;

use Draagvlak\Core\Data\Database;
use Draagvlak\Core\Data\JsonStore;
use Draagvlak\Core\Http\Csrf;
use Draagvlak\Core\Http\Request;
use Draagvlak\Core\Http\Session;
use Draagvlak\Core\View\Assets;
use Draagvlak\Core\View\View;
use Draagvlak\Features\Auth\AccountRepository;
use Draagvlak\Features\Auth\Guard;
use Draagvlak\Features\Auth\LoginThrottle;
use Draagvlak\Features\Auth\Registration;
use Draagvlak\Features\Contacts\ContactRepository;
use Draagvlak\Features\Messages\MessageRepository;
use Draagvlak\Features\Scenario\DemoSeeder;
use Draagvlak\Features\Scenario\Scenario;
use Draagvlak\Features\Score\ScoreBoard;
use Throwable;

/**
 * Everything the application is made of, wired together once.
 *
 * This is the only place where a class learns about the classes it needs, so
 * nothing reaches for a global and nothing builds its own dependency halfway
 * down a method. Add a service by adding a property and a line below.
 */
final class App
{
    public readonly Paths $paths;

    public readonly Config $config;

    public readonly Session $session;

    public readonly Request $request;

    public readonly Csrf $csrf;

    public readonly Assets $assets;

    public readonly View $view;

    public readonly Database $database;

    public readonly JsonStore $json;

    public readonly AccountRepository $accounts;

    public readonly Registration $registration;

    public readonly ContactRepository $contacts;

    public readonly MessageRepository $messages;

    public readonly ScoreBoard $scores;

    public readonly Scenario $scenario;

    public readonly DemoSeeder $demo;

    public readonly Guard $auth;

    private function __construct(string $root)
    {
        $this->paths = new Paths($root);
        $this->config = new Config($this->paths);

        $this->session = new Session();
        $this->request = Request::fromGlobals();
        $this->csrf = new Csrf($this->session);
        $this->assets = new Assets($this->paths);
        $this->view = new View($this->paths, $this->assets, $this->csrf);

        $this->database = new Database($this->config);
        $this->json = new JsonStore($this->paths);

        $this->scores = new ScoreBoard($this->database, $this->session);
        $this->accounts = new AccountRepository($this->database);
        $this->registration = new Registration($this->accounts);
        $this->contacts = new ContactRepository($this->database, $this->accounts, $this->scores);
        $this->messages = new MessageRepository($this->database, $this->contacts, $this->scores);
        $this->scenario = new Scenario($this->database, $this->json, $this->contacts);
        $this->demo = new DemoSeeder($this->config, $this->accounts, $this->scenario);

        $this->auth = new Guard(
            $this->accounts,
            $this->session,
            $this->request,
            new LoginThrottle($this->session),
        );
    }

    /**
     * Build the application and put the request in a state a screen can trust:
     * error handling set, timezone fixed, session started.
     *
     * @param string $root Project root, the folder bootstrap.php sits in.
     */
    public static function boot(string $root): self
    {
        $app = new self($root);
        $debug = (bool) $app->config->get('app.debug', false);

        error_reporting($debug ? E_ALL : 0);
        ini_set('display_errors', $debug ? '1' : '0');

        if (!$debug) {
            set_exception_handler($app->showFailure(...));
        }

        date_default_timezone_set((string) $app->config->get('app.timezone', 'Europe/Amsterdam'));
        mb_internal_encoding('UTF-8');

        $app->session->start();

        return $app;
    }

    /** Name of the product, printed in the title bar and the header. */
    public function name(): string
    {
        return (string) $this->config->get('app.name', 'Draagvlak');
    }

    /** Language of everything a visitor reads. */
    public function locale(): string
    {
        return (string) $this->config->get('app.locale', 'nl');
    }

    /**
     * With debug off, an error becomes a plain screen instead of a blank page.
     * A participant should never be left staring at nothing halfway a session.
     */
    private function showFailure(Throwable $error): void
    {
        error_log((string) $error);

        if (!headers_sent()) {
            http_response_code(500);
        }

        echo $this->view->render('layout/failure', [
            'appName' => $this->name(),
            'locale' => $this->locale(),
            'stylesheets' => $this->assets->stylesheets('core'),
        ]);
    }
}
