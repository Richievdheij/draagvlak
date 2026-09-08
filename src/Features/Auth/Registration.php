<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth;

/**
 * Checking the registration form. Returns an English key per field that is
 * wrong; the screen turns those into Dutch.
 *
 * There is no second password field. Repeating a password does not catch the
 * typo that matters and is one more thing between somebody and their account.
 */
final readonly class Registration
{
    public const int MIN_PASSWORD_LENGTH = 8;

    /** bcrypt stops reading at 72 bytes, so anything longer is refused. */
    public const int MAX_PASSWORD_LENGTH = 72;

    /** A name longer than the column can hold is refused rather than cut off. */
    public const int MAX_NAME_LENGTH = 80;

    public function __construct(private AccountRepository $accounts) {}

    /** @return array<string, string> Field name to error key. Empty when it is in order. */
    public function validate(string $name, string $email, string $password): array
    {
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'name_required';
        } elseif (mb_strlen($name) > self::MAX_NAME_LENGTH) {
            $errors['name'] = 'name_long';
        }

        if ($email === '') {
            $errors['email'] = 'email_required';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'email_invalid';
        } elseif ($this->accounts->emailExists($email)) {
            $errors['email'] = 'email_taken';
        }

        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $errors['password'] = 'password_short';
        } elseif (\strlen($password) > self::MAX_PASSWORD_LENGTH) {
            $errors['password'] = 'password_long';
        }

        return $errors;
    }
}
