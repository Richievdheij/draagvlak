<?php

declare(strict_types=1);

namespace Draagvlak\Core;

use Draagvlak\Core\Http\Csrf;
use Draagvlak\Core\Http\Response;

/**
 * One screen: who may see it, what it does with a form, and what it prints.
 *
 * handle() always runs the same four steps in the same order, which is why a
 * template never has to work anything out:
 *
 *     authorise()  who is allowed to open this screen
 *     submit()     handle a POST, and end in a redirect
 *     data()       everything the template needs
 *     render       the template of the screen, inside the layout
 *
 * A subclass says almost nothing. Its feature comes from its namespace and its
 * name comes from its class name, so LoginPage in Features\Auth prints
 * views/features/auth/login.php at /login.php with the stylesheets in
 * assets/css/features/auth/.
 */
abstract class Page
{
    public function __construct(protected readonly App $app) {}

    /** Run the screen. A file in public/ calls this and nothing else. */
    final public function handle(): void
    {
        $this->authorise();

        if ($this->app->request->isPost()) {
            $this->submit();
        }

        echo $this->renderScreen();
    }

    /** Dutch title of this screen, shown in the tab and as the heading. */
    abstract protected function title(): string;

    /**
     * Every screen that shows somebody's own data leaves this as it is. The
     * login and registration screens turn it around with requireGuest().
     */
    protected function authorise(): void
    {
        $this->app->auth->requireLogin();
    }

    /** Handle a form submit. Always ends in Response::redirect(). */
    protected function submit(): void {}

    /** @return array<string, mixed> Everything the template needs. */
    protected function data(): array
    {
        return [];
    }

    /** Which navigation item to mark as current. Override for a sub screen. */
    protected function nav(): string
    {
        return $this->screen();
    }

    /** Meta description, or null to leave it out. */
    protected function description(): ?string
    {
        return null;
    }

    /**
     * Feature this screen belongs to, taken from its namespace:
     * Features\Auth\Pages\LoginPage becomes 'auth'.
     */
    final protected function feature(): string
    {
        $parts = explode('\\', static::class);
        $index = array_search('Features', $parts, true);

        return $index === false ? 'core' : self::kebab($parts[$index + 1] ?? 'core');
    }

    /**
     * Name of this screen, taken from its class name: LoginPage becomes
     * 'login'. It is the file in public/ and the template in views/ at once.
     */
    final protected function screen(): string
    {
        $name = basename(str_replace('\\', '/', static::class));

        return self::kebab(preg_replace('/Page$/', '', $name) ?? $name);
    }

    /**
     * Check the form token before anything changes.
     *
     * @param string $back Screen to return to when the token is wrong.
     */
    protected function requireValidCsrf(string $back): void
    {
        if ($this->app->csrf->isValid($this->app->request->input(Csrf::FIELD))) {
            return;
        }

        $this->app->session->flash('form_expired');

        Response::redirect($back);
    }

    /** 'EmergencyContacts' becomes 'emergency-contacts'. */
    private static function kebab(string $name): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $name));
    }

    private function renderScreen(): string
    {
        $feature = $this->feature();

        $content = $this->app->view->render(
            \sprintf('features/%s/%s', $feature, $this->screen()),
            $this->data(),
        );

        return $this->app->view->render('layout/app', [
            'content' => $content,
            'title' => $this->title(),
            'nav' => $this->nav(),
            'description' => $this->description(),
            'bodyClass' => 'screen-' . $this->screen(),
            'stylesheets' => $this->app->assets->stylesheets($feature),
            'appName' => $this->app->name(),
            'locale' => $this->app->locale(),
            'account' => $this->app->auth->account(),
            'flashes' => $this->app->session->takeFlashes(),
        ]);
    }
}
