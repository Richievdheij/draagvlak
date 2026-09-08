<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth;

use Draagvlak\Core\Http\Session;

/**
 * How often this browser may guess a password before the form closes.
 *
 * The count lives in the session, so it is per browser and not per account:
 * enough to stop somebody working through a list, and it never locks a
 * participant out of their own account from another device.
 */
final readonly class LoginThrottle
{
    public const int MAX_ATTEMPTS = 5;

    public const int LOCKOUT_SECONDS = 900;

    private const string ATTEMPTS = 'loginAttempts';

    private const string STARTED_AT = 'loginAttemptsStartedAt';

    public function __construct(private Session $session) {}

    public function isLocked(): bool
    {
        return $this->attempts() >= self::MAX_ATTEMPTS;
    }

    public function secondsLeft(): int
    {
        $firstAttempt = $this->session->getInt(self::STARTED_AT);

        if ($firstAttempt === 0) {
            return 0;
        }

        return max(0, self::LOCKOUT_SECONDS - (time() - $firstAttempt));
    }

    /** Whole minutes until the form opens up again, at least one. */
    public function minutesLeft(): int
    {
        return max(1, (int) ceil($this->secondsLeft() / 60));
    }

    /** Wrong attempts in the current window. */
    public function attempts(): int
    {
        if ($this->secondsLeft() === 0) {
            $this->clear();

            return 0;
        }

        return $this->session->getInt(self::ATTEMPTS);
    }

    public function recordFailure(): void
    {
        if ($this->session->getInt(self::STARTED_AT) === 0) {
            $this->session->set(self::STARTED_AT, time());
        }

        $this->session->set(self::ATTEMPTS, $this->attempts() + 1);
    }

    public function clear(): void
    {
        $this->session->forget(self::ATTEMPTS);
        $this->session->forget(self::STARTED_AT);
    }
}
