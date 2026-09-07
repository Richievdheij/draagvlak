<?php

declare(strict_types=1);

/**
 * The session: per-visitor state, one-off messages and form protection.
 *
 * There is no database and no login. Everything a visitor does during one run
 * lives in the session, which is exactly what a test session needs: a fresh
 * browser is a fresh participant.
 */

/**
 * Start the session unless one is already running.
 *
 * Called by bootstrap.php, so a page never has to think about it.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/**
 * Read a value from the session.
 *
 * @param string $key     Key the value was stored under.
 * @param mixed  $default Returned when the key is absent.
 */
function sessionGet(string $key, mixed $default = null): mixed
{
    return $_SESSION[$key] ?? $default;
}

/**
 * Write a value to the session.
 */
function sessionSet(string $key, mixed $value): void
{
    $_SESSION[$key] = $value;
}

/**
 * Remove one value from the session.
 */
function sessionForget(string $key): void
{
    unset($_SESSION[$key]);
}

/**
 * Wipe the whole session and start a clean one.
 *
 * Use this for a reset link, so the next participant starts from scratch
 * without restarting the server.
 */
function sessionReset(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

/**
 * Queue a message to show on the next page load.
 *
 * Set it before a redirect, not before printing the page: the layout prints and
 * clears the queue, so a message set halfway down a page is already gone.
 *
 * @param string $message Dutch text, shown to the visitor as-is.
 * @param string $tone    'info', 'success' or 'danger'; drives the styling only.
 */
function flash(string $message, string $tone = 'info'): void
{
    $_SESSION['flashes'][] = ['message' => $message, 'tone' => $tone];
}

/**
 * Take every queued message and empty the queue.
 *
 * @return list<array{message: string, tone: string}>
 */
function takeFlashes(): array
{
    $flashes = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);

    return $flashes;
}

/**
 * Token that proves a form was submitted from this site.
 *
 * The same token is reused for the whole session, which is enough here: it
 * stops another site from posting to ours on a visitor's behalf.
 */
function csrfToken(): string
{
    if (!isset($_SESSION['csrfToken'])) {
        $_SESSION['csrfToken'] = bin2hex(random_bytes(16));
    }

    return (string) $_SESSION['csrfToken'];
}

/**
 * Hidden input carrying the token. Print this inside every POST form.
 */
function csrfField(): string
{
    return sprintf('<input type="hidden" name="csrfToken" value="%s">', e(csrfToken()));
}

/**
 * Whether the posted token matches the one in the session.
 *
 * @param string|null $token Token to check. Defaults to the posted one.
 */
function isValidCsrf(?string $token = null): bool
{
    $token ??= is_string($_POST['csrfToken'] ?? null) ? $_POST['csrfToken'] : '';

    return $token !== '' && hash_equals(csrfToken(), $token);
}
