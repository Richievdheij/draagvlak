<?php

declare(strict_types=1);

/**
 * Accounts: registering, logging in, logging out and keeping screens closed.
 *
 * Passwords are never stored, only a hash from password_hash(). Nothing here
 * writes a Dutch sentence: a check that fails returns a key, and the screen
 * decides what that key says to the visitor.
 */

/** A password shorter than this is refused. */
const MIN_PASSWORD_LENGTH = 8;

/** Wrong attempts allowed before the form locks, and for how many seconds. */
const MAX_LOGIN_ATTEMPTS = 5;
const LOGIN_LOCKOUT_SECONDS = 900;

/**
 * The visitor, or null when nobody is logged in.
 *
 * Read once per request: a page and three partials all ask for it.
 *
 * @return array<string, mixed>|null
 */
function currentUser(): ?array
{
    static $user = null;
    static $loaded = false;

    if ($loaded) {
        return $user;
    }

    $loaded = true;
    $userId = (int) sessionGet('userId', 0);

    if ($userId === 0) {
        return null;
    }

    $user = dbFirst('SELECT * FROM users WHERE id = ?', [$userId]);

    if ($user === null) {
        // The account was removed while the session was still open.
        sessionForget('userId');
    }

    return $user;
}

/** Whether somebody is logged in. */
function isLoggedIn(): bool
{
    return currentUser() !== null;
}

/**
 * Id of the visitor, for a query that needs it.
 *
 * @throws RuntimeException When nobody is logged in. Call requireLogin() first.
 */
function currentUserId(): int
{
    $user = currentUser();

    if ($user === null) {
        throw new RuntimeException('No one is logged in. Call requireLogin() at the top of the page.');
    }

    return (int) $user['id'];
}

/**
 * Close this screen for visitors who are not logged in.
 *
 * Put this on the first line of every screen that shows someone's own data. The
 * page they wanted is remembered, so logging in takes them there instead of to
 * the home screen.
 */
function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    sessionSet('intendedPage', currentPage());
    flash('Log eerst in om verder te gaan.', 'info');

    redirect('inloggen');
}

/**
 * Keep a logged-in visitor away from the login and registration screens.
 */
function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('index');
    }
}

/**
 * Check an email and password and log the visitor in when they match.
 *
 * The session id is replaced on success, so a session id someone else already
 * knew cannot be used to ride along after the login.
 */
function attemptLogin(string $email, string $password): bool
{
    $user = dbFirst('SELECT id, password_hash FROM users WHERE email = ?', [$email]);

    if ($user === null) {
        // Hash something anyway, so a wrong address does not answer faster than
        // a wrong password and give away which accounts exist.
        password_verify($password, '$2y$12$usesomesillystringfsomeksdjfhasdfjkhasdfkjhasdfkjhasdfkjhas');
        recordFailedLogin();

        return false;
    }

    if (!password_verify($password, (string) $user['password_hash'])) {
        recordFailedLogin();

        return false;
    }

    if (password_needs_rehash((string) $user['password_hash'], PASSWORD_DEFAULT)) {
        dbRun('UPDATE users SET password_hash = ? WHERE id = ?', [
            password_hash($password, PASSWORD_DEFAULT),
            (int) $user['id'],
        ]);
    }

    session_regenerate_id(true);
    sessionSet('userId', (int) $user['id']);
    clearLoginAttempts();

    return true;
}

/**
 * Log out and throw the whole session away, including the score of this run.
 */
function logOut(): void
{
    sessionReset();
}

/**
 * Create an account and return its id.
 *
 * Check the input with validateRegistration() first; this function assumes it
 * is correct and only guards the address, because the database has the last
 * word on whether it is free.
 *
 * @throws RuntimeException When the address is taken.
 */
function registerUser(string $name, string $email, string $password): int
{
    if (emailExists($email)) {
        throw new RuntimeException('That email address is already registered.');
    }

    return dbInsert('users', [
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'score' => START_SCORE,
    ]);
}

/** Whether an address already has an account. */
function emailExists(string $email): bool
{
    return dbValue('SELECT id FROM users WHERE email = ?', [$email]) !== null;
}

/**
 * Check the registration form.
 *
 * Returns a key per field that is wrong, for example ['email' => 'email_taken'].
 * The screen turns those keys into Dutch.
 *
 * @return array<string, string> Empty when everything is in order.
 */
function validateRegistration(string $name, string $email, string $password, string $repeat): array
{
    $errors = [];

    if ($name === '') {
        $errors['name'] = 'name_required';
    }

    if ($email === '') {
        $errors['email'] = 'email_required';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'email_invalid';
    } elseif (emailExists($email)) {
        $errors['email'] = 'email_taken';
    }

    if (mb_strlen($password) < MIN_PASSWORD_LENGTH) {
        $errors['password'] = 'password_short';
    } elseif ($password !== $repeat) {
        $errors['repeat'] = 'password_mismatch';
    }

    return $errors;
}

/**
 * Whether this browser has to wait before trying to log in again.
 */
function loginIsLocked(): bool
{
    return loginAttempts() >= MAX_LOGIN_ATTEMPTS;
}

/** Seconds until the login form opens up again. */
function loginLockSecondsLeft(): int
{
    $firstAttempt = (int) sessionGet('loginAttemptsStartedAt', 0);

    if ($firstAttempt === 0) {
        return 0;
    }

    return max(0, LOGIN_LOCKOUT_SECONDS - (time() - $firstAttempt));
}

/** Wrong attempts in the current window. */
function loginAttempts(): int
{
    if (loginLockSecondsLeft() === 0) {
        clearLoginAttempts();

        return 0;
    }

    return (int) sessionGet('loginAttempts', 0);
}

/** Count one wrong attempt, starting the window if this is the first. */
function recordFailedLogin(): void
{
    if ((int) sessionGet('loginAttemptsStartedAt', 0) === 0) {
        sessionSet('loginAttemptsStartedAt', time());
    }

    sessionSet('loginAttempts', loginAttempts() + 1);
}

/** Forget the wrong attempts, after a successful login or once time is up. */
function clearLoginAttempts(): void
{
    sessionForget('loginAttempts');
    sessionForget('loginAttemptsStartedAt');
}
