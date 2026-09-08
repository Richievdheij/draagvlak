<?php

declare(strict_types=1);

namespace Draagvlak\Features\Contacts;

use Draagvlak\Core\Data\Database;
use Draagvlak\Features\Auth\Account;
use Draagvlak\Features\Auth\AccountRepository;
use Draagvlak\Features\Score\Outcome;
use Draagvlak\Features\Score\ScoreBoard;
use Draagvlak\Features\Score\ScoreRules;

/**
 * The people behind the number: reading the list, adding somebody by their
 * code, and taking somebody off it.
 *
 * A removed contact keeps a removed_at date instead of being deleted, so a
 * researcher can see what a participant did and adding the same person again
 * picks the old row back up.
 */
final readonly class ContactRepository
{
    /**
     * A linked contact shows the account's name and number; the copies in the
     * contacts table are only used by the scenario, which belongs to nobody.
     */
    private const string SELECT = 'SELECT c.id, c.user_id, c.contact_user_id, c.relation,
                   c.is_emergency_contact, c.lists_you, c.note, c.removed_at,
                   COALESCE(u.name, c.name) AS name,
                   COALESCE(u.score, c.score) AS score
            FROM contacts c
            LEFT JOIN users u ON u.id = c.contact_user_id';

    public function __construct(
        private Database $database,
        private AccountRepository $accounts,
        private ScoreBoard $scores,
    ) {}

    /**
     * Everyone still on the list, slowest first. The order is the argument of
     * the screen: the person who answers least is the first thing you see.
     *
     * @return list<Contact>
     */
    public function active(int $userId): array
    {
        $rows = $this->database->all(
            self::SELECT . ' WHERE c.user_id = ? AND c.removed_at IS NULL ORDER BY score ASC, name ASC',
            [$userId],
        );

        return array_map(Contact::fromRow(...), $rows);
    }

    /** @return list<Contact> The people who put you on their emergency list. */
    public function emergency(int $userId): array
    {
        $rows = $this->database->all(
            self::SELECT . ' WHERE c.user_id = ? AND c.removed_at IS NULL AND c.is_emergency_contact = TRUE
             ORDER BY score ASC, name ASC',
            [$userId],
        );

        return array_map(Contact::fromRow(...), $rows);
    }

    public function activeCount(int $userId): int
    {
        return (int) $this->database->value(
            'SELECT COUNT(*) FROM contacts WHERE user_id = ? AND removed_at IS NULL',
            [$userId],
        );
    }

    /** Null when the contact is not this participant's. */
    public function find(int $userId, int $contactId): ?Contact
    {
        $row = $this->database->first(
            self::SELECT . ' WHERE c.id = ? AND c.user_id = ? AND c.removed_at IS NULL',
            [$contactId, $userId],
        );

        return $row === null ? null : Contact::fromRow($row);
    }

    /**
     * Change what a contact is worth.
     *
     * A contact with an account of their own really moves: the change goes to
     * their score and to score_events, the same way their own choices would. A
     * scenario contact only has the copy in the contacts table.
     *
     * @param string $reason English key, written to score_events for a linked account.
     */
    public function changeScore(Contact $contact, int $delta, string $reason): void
    {
        if ($contact->accountId !== null) {
            $this->scores->change($contact->accountId, $delta, $reason);

            return;
        }

        $this->database->run(
            'UPDATE contacts SET score = GREATEST(?, LEAST(?, score + ?)) WHERE id = ?',
            [ScoreRules::SCORE_MIN, ScoreRules::SCORE_MAX, $delta, $contact->id],
        );
    }

    /**
     * Add somebody with the code they shared. The link is made both ways: you
     * appear on their list at the same moment they appear on yours.
     *
     * @param string $code Code as it was typed; cleaned up before the lookup.
     */
    public function addByCode(Account $account, string $code): Outcome
    {
        $normalised = ContactCode::normalise($code);

        if ($normalised === '') {
            return new Outcome('code_required');
        }

        if (!ContactCode::isValid($normalised)) {
            return new Outcome('code_invalid');
        }

        if ($normalised === $account->contactCode) {
            return new Outcome('code_self');
        }

        $other = $this->accounts->findByContactCode($normalised);

        if ($other === null) {
            return new Outcome('code_unknown');
        }

        if ($this->linkExists($account->id, $other->id)) {
            return new Outcome('code_already_added', $other->name);
        }

        $this->link($account->id, $other);
        $this->link($other->id, $account);

        return new Outcome('contact_added', $other->name);
    }

    /**
     * Take a contact off the list. Removing costs nothing by itself, but the
     * number of active contacts is part of the calculation, so dropping below
     * the minimum does.
     *
     * @return Outcome|null Null when the contact is not this participant's.
     */
    public function remove(int $userId, int $contactId): ?Outcome
    {
        $contact = $this->find($userId, $contactId);

        if ($contact === null) {
            return null;
        }

        $this->database->run('UPDATE contacts SET removed_at = NOW() WHERE id = ?', [$contact->id]);
        $this->database->run(
            'UPDATE messages SET handled_at = NOW(), outcome = ? WHERE contact_id = ? AND handled_at IS NULL',
            ['removed', $contact->id],
        );

        // The other side of a real link goes too: two people who are no longer
        // contacts should not each see a different list.
        if ($contact->accountId !== null) {
            $this->database->run(
                'UPDATE contacts SET removed_at = NOW() WHERE user_id = ? AND contact_user_id = ? AND removed_at IS NULL',
                [$contact->accountId, $userId],
            );
        }

        $this->changeScore($contact, -ScoreRules::CONTACT_LOSS_ON_REMOVAL, 'removed_from_a_list');

        $activeLeft = $this->activeCount($userId);
        $delta = ScoreRules::forRemovingContact($activeLeft);
        $result = $delta === 0 ? 'removed' : 'below_minimum';

        $this->scores->change($userId, $delta, $result);

        return new Outcome($result, $contact->name, $delta, $activeLeft);
    }

    private function linkExists(int $userId, int $accountId): bool
    {
        return $this->database->value(
            'SELECT id FROM contacts WHERE user_id = ? AND contact_user_id = ? AND removed_at IS NULL',
            [$userId, $accountId],
        ) !== null;
    }

    /**
     * A link that was removed earlier is brought back rather than inserted
     * again: the unique key keeps one row per pair, and the old row still
     * carries what the two of you did before.
     */
    private function link(int $userId, Account $other): void
    {
        $existing = $this->database->value(
            'SELECT id FROM contacts WHERE user_id = ? AND contact_user_id = ?',
            [$userId, $other->id],
        );

        if ($existing !== null) {
            $this->database->run(
                'UPDATE contacts SET removed_at = NULL, name = ? WHERE id = ?',
                [$other->name, (int) $existing],
            );

            return;
        }

        $this->database->insert('contacts', [
            'user_id' => $userId,
            'contact_user_id' => $other->id,
            'name' => $other->name,
            'score' => $other->score,
            'lists_you' => 1,
        ]);
    }
}
