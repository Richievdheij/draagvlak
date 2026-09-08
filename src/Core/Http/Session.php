<?php

declare(strict_types=1);

namespace Draagvlak\Core\Http;

/**
 * State that belongs to one visit. Everything that has to survive a refresh
 * lives in the database instead.
 *
 * A flash is stored as an English key with values, never as a sentence: the
 * Dutch lives in views/components/notices.php.
 */
final class Session
{
    /** Started by App::boot(), so a screen never has to think about it. */
    public function start(): void
    {
        // A command line script has no visitor and no cookies to hang a session on.
        if (PHP_SAPI === 'cli' || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => ($_SERVER['HTTPS'] ?? '') !== '',
        ]);

        session_start();
    }

    /** @param mixed $default Returned when the key is absent. */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->get($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Wipe the visit and start a clean one. This is what logging out does. */
    public function reset(): void
    {
        $_SESSION = [];

        if (PHP_SAPI !== 'cli') {
            session_regenerate_id(true);
        }
    }

    /**
     * Give this visit a new session id, so an id somebody else already knew
     * cannot be used to ride along after a login.
     */
    public function regenerate(): void
    {
        if (PHP_SAPI !== 'cli') {
            session_regenerate_id(true);
        }
    }

    /**
     * Queue a message for the next page load. Set it before a redirect: the
     * layout prints and clears the queue.
     *
     * @param string $key Looked up in views/components/notices.php.
     * @param array<string, string|int> $values Values for the {placeholders} in that sentence.
     */
    public function flash(string $key, array $values = []): void
    {
        $_SESSION['flashes'][] = ['key' => $key, 'values' => $values];
    }

    /** @return list<array{key: string, values: array<string, string|int>}> */
    public function takeFlashes(): array
    {
        $flashes = $_SESSION['flashes'] ?? [];
        unset($_SESSION['flashes']);

        return \is_array($flashes) ? array_values($flashes) : [];
    }

    /**
     * Keep what somebody typed and what was wrong with it, so a form that fails
     * can redirect instead of printing itself again.
     *
     * @param array<string, string> $values Field name to value. Never a password.
     * @param array<string, string> $errors Field name to English error key.
     */
    public function rememberForm(array $values, array $errors = []): void
    {
        $_SESSION['form'] = ['values' => $values, 'errors' => $errors];
    }

    /** @return array{values: array<string, string>, errors: array<string, string>} */
    public function takeForm(): array
    {
        $form = $_SESSION['form'] ?? [];
        unset($_SESSION['form']);

        return [
            'values' => \is_array($form['values'] ?? null) ? $form['values'] : [],
            'errors' => \is_array($form['errors'] ?? null) ? $form['errors'] : [],
        ];
    }
}
