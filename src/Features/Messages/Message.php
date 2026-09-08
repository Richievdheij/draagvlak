<?php

declare(strict_types=1);

namespace Draagvlak\Features\Messages;

use Draagvlak\Features\Score\ScoreRules;

/**
 * One message from a contact, and everything the screen has to know about the
 * clock on it.
 *
 * A message with a response window is the one where answering still earns a
 * point. That window counts from the moment the participant saw it, not from
 * the moment it arrived, so the pressure is real in every session.
 */
final readonly class Message
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $contactId,
        public string $contactName,
        public int $contactScore,
        public string $body,
        public string $receivedAt,
        public int $respondWithinSeconds,
        public ?string $seenAt,
        public ?string $outcome,
    ) {}

    /** @param array<string, mixed> $row One row of MessageRepository's queries. */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            (int) $row['contact_id'],
            (string) $row['contact_name'],
            (int) $row['contact_score'],
            (string) $row['body'],
            (string) $row['received_at'],
            (int) $row['respond_within_seconds'],
            $row['seen_at'] === null ? null : (string) $row['seen_at'],
            $row['outcome'] === null ? null : (string) $row['outcome'],
        );
    }

    /** Seconds between the message arriving and now. */
    public function waitSeconds(): int
    {
        return max(0, time() - (int) strtotime($this->receivedAt));
    }

    /** Seconds left to answer in time, or zero when there is no window left. */
    public function secondsLeft(): int
    {
        if ($this->respondWithinSeconds === 0 || $this->seenAt === null) {
            return 0;
        }

        return max(0, $this->respondWithinSeconds - (time() - (int) strtotime($this->seenAt)));
    }

    /** The window is still open, so answering earns a point. */
    public function isUrgent(): bool
    {
        return $this->secondsLeft() > 0;
    }

    /** This contact has waited long enough to count as broken. */
    public function isStale(): bool
    {
        return ScoreRules::isStale($this->waitSeconds());
    }

    /** The contact is under the point where they lose their own benefits. */
    public function isQuiet(): bool
    {
        return $this->contactScore < ScoreRules::CONTACT_THRESHOLD;
    }

    /** How full the timer bar is, as a percentage of the whole window. */
    public function windowLeftPercentage(): int
    {
        if ($this->respondWithinSeconds === 0) {
            return 0;
        }

        return (int) round($this->secondsLeft() / $this->respondWithinSeconds * 100);
    }
}
