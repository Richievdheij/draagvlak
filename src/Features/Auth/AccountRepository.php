<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth;

use Draagvlak\Core\Data\Database;
use Draagvlak\Features\Contacts\ContactCode;
use Draagvlak\Features\Score\ScoreRules;
use RuntimeException;

/**
 * Everything that reads or writes the users table. Passwords only exist in
 * here: they go in through password_hash() and come out as a yes or a no.
 */
final readonly class AccountRepository
{
    /**
     * Hash of nothing, used to spend the same time on an address that does not
     * exist as on one that does. Without it, a wrong address answers faster
     * than a wrong password and gives away which accounts exist.
     */
    private const string ABSENT_HASH = '$2y$12$usesomesillystringfsomeksdjfhasdfjkhasdfkjhasdfkjhasdfkjhas';

    /** Tries to find a free contact code before giving up. */
    private const int CODE_ATTEMPTS = 20;

    public function __construct(private Database $database) {}

    public function find(int $id): ?Account
    {
        $row = $this->database->first('SELECT * FROM users WHERE id = ?', [$id]);

        return $row === null ? null : Account::fromRow($row);
    }

    public function findByEmail(string $email): ?Account
    {
        $row = $this->database->first('SELECT * FROM users WHERE email = ?', [$email]);

        return $row === null ? null : Account::fromRow($row);
    }

    /** @param string $code Code as somebody typed it; normalised before the lookup. */
    public function findByContactCode(string $code): ?Account
    {
        $row = $this->database->first(
            'SELECT * FROM users WHERE contact_code = ?',
            [ContactCode::normalise($code)],
        );

        return $row === null ? null : Account::fromRow($row);
    }

    public function emailExists(string $email): bool
    {
        return $this->database->value('SELECT id FROM users WHERE email = ?', [$email]) !== null;
    }

    /**
     * Check the input with Registration first; this only guards the address,
     * because the database has the last word on whether it is free.
     *
     * @param string|null $contactCode A code to use instead of a drawn one. Only
     *                                 the demo seeder passes this, so its code
     *                                 stays the same after every db:fresh.
     *
     * @throws RuntimeException When the address or the code is taken.
     */
    public function create(string $name, string $email, string $password, ?string $contactCode = null): Account
    {
        if ($this->emailExists($email)) {
            throw new RuntimeException('That email address is already registered.');
        }

        if ($contactCode !== null && $this->findByContactCode($contactCode) !== null) {
            throw new RuntimeException(\sprintf('Contact code %s already belongs to somebody.', $contactCode));
        }

        $id = $this->database->insert('users', [
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'score' => ScoreRules::START_SCORE,
            'contact_code' => $contactCode ?? $this->freeContactCode($name, $email),
        ]);

        $account = $this->find($id);

        if ($account === null) {
            throw new RuntimeException('The account was created but could not be read back.');
        }

        return $account;
    }

    /**
     * The account behind an email and password, or null when they do not match.
     * A hash made with older settings is quietly replaced by a current one.
     */
    public function findByCredentials(string $email, string $password): ?Account
    {
        $row = $this->database->first('SELECT * FROM users WHERE email = ?', [$email]);

        if ($row === null) {
            password_verify($password, self::ABSENT_HASH);

            return null;
        }

        $hash = (string) $row['password_hash'];

        if (!password_verify($password, $hash)) {
            return null;
        }

        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->database->run('UPDATE users SET password_hash = ? WHERE id = ?', [
                password_hash($password, PASSWORD_DEFAULT),
                (int) $row['id'],
            ]);
        }

        return Account::fromRow($row);
    }

    /**
     * A contact code nobody else has yet.
     *
     * @throws RuntimeException When no free code turns up, which means the
     *                          alphabet is exhausted and ContactCode has to grow.
     */
    private function freeContactCode(string $name, string $email): string
    {
        for ($attempt = 0; $attempt < self::CODE_ATTEMPTS; $attempt++) {
            $code = ContactCode::generate($name, $email);

            if ($this->database->value('SELECT id FROM users WHERE contact_code = ?', [$code]) === null) {
                return $code;
            }
        }

        throw new RuntimeException('Could not find a free contact code. Make ContactCode longer.');
    }
}
