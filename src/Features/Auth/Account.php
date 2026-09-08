<?php

declare(strict_types=1);

namespace Draagvlak\Features\Auth;

use Draagvlak\Features\Score\ScoreRules;

/**
 * One account, as the rest of the app sees it. The password hash is
 * deliberately not on it: nothing outside AccountRepository needs one.
 */
final readonly class Account
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public int $score,
        public bool $hasPlus,
        public string $contactCode,
    ) {}

    /** @param array<string, mixed> $row One row of the users table. */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['email'],
            (int) $row['score'],
            (bool) $row['has_plus'],
            (string) $row['contact_code'],
        );
    }

    /** Whether this score earns the discount from the health insurer. */
    public function hasReward(): bool
    {
        return ScoreRules::hasReward($this->score);
    }

    /** Points still missing before the discount starts. Zero once it is earned. */
    public function pointsToReward(): int
    {
        return max(0, ScoreRules::REWARD_THRESHOLD - $this->score);
    }
}
