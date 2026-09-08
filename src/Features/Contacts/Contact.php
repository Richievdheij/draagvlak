<?php

declare(strict_types=1);

namespace Draagvlak\Features\Contacts;

use Draagvlak\Features\Score\ScoreRules;

/**
 * One person on somebody's list.
 *
 * Either linked to a real account, because the two of you exchanged a code, or
 * part of the starting content of the scenario and standing on its own.
 * accountId says which; the name and the number come from the linked account
 * when there is one, so both sides see the same number.
 */
final readonly class Contact
{
    public function __construct(
        public int $id,
        public int $userId,
        public ?int $accountId,
        public string $name,
        public string $relation,
        public int $score,
        public bool $isEmergencyContact,
        public bool $listsYou,
        public string $note,
    ) {}

    /** @param array<string, mixed> $row One row of ContactRepository's queries. */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            $row['contact_user_id'] === null ? null : (int) $row['contact_user_id'],
            (string) $row['name'],
            (string) $row['relation'],
            (int) $row['score'],
            (bool) $row['is_emergency_contact'],
            (bool) $row['lists_you'],
            (string) $row['note'],
        );
    }

    /** Whether this contact is somebody with their own account. */
    public function isLinked(): bool
    {
        return $this->accountId !== null;
    }

    /** Under the point where a contact loses their own benefits. */
    public function isQuiet(): bool
    {
        return $this->score < ScoreRules::CONTACT_THRESHOLD;
    }
}
