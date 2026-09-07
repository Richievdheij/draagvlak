<?php

declare(strict_types=1);

/**
 * The people behind the number: reading the list and taking someone off it.
 *
 * A removed contact is kept with a removed_at date instead of being deleted, so
 * a researcher can still see what a participant did during the session.
 */

/** How many points a contact loses when the participant walks away from them. */
const CONTACT_LOSS_ON_REMOVAL = 8;
const CONTACT_LOSS_ON_POSTPONE = 3;
const CONTACT_GAIN_ON_ANSWER = 2;

/**
 * Everyone still on the list, slowest first.
 *
 * The order is the argument of the screen: the person who answers least is the
 * first thing you see, which is exactly how the app wants you to read people.
 *
 * @return list<array<string, mixed>>
 */
function activeContacts(int $userId): array
{
    return dbAll(
        'SELECT * FROM contacts WHERE user_id = ? AND removed_at IS NULL ORDER BY score ASC, name ASC',
        [$userId]
    );
}

/** How many contacts still count as active. */
function activeContactCount(int $userId): int
{
    return (int) dbValue(
        'SELECT COUNT(*) FROM contacts WHERE user_id = ? AND removed_at IS NULL',
        [$userId]
    );
}

/**
 * One contact of this participant, or null when it is not theirs.
 *
 * Always look a contact up together with the user id. Without that a changed
 * number in the URL reaches someone else's data.
 *
 * @return array<string, mixed>|null
 */
function findContact(int $userId, int $contactId): ?array
{
    return dbFirst('SELECT * FROM contacts WHERE id = ? AND user_id = ?', [$contactId, $userId]);
}

/**
 * Change the score of a contact, within the same range as a participant's.
 */
function changeContactScore(int $contactId, int $delta): void
{
    dbRun(
        'UPDATE contacts SET score = GREATEST(?, LEAST(?, score + ?)) WHERE id = ?',
        [SCORE_MIN, SCORE_MAX, $delta, $contactId]
    );
}

/**
 * Take a contact off the list.
 *
 * Removing costs nothing by itself, but the number of active contacts is part
 * of the calculation, so dropping below the minimum does. The rule is only
 * mentioned once the participant has broken it, which is the point.
 *
 * @return array{result: string, contact: string, delta: int, activeLeft: int}|null
 *         Null when the contact does not belong to this participant.
 */
function removeContactFromList(int $userId, int $contactId): ?array
{
    $contact = findContact($userId, $contactId);

    if ($contact === null || $contact['removed_at'] !== null) {
        return null;
    }

    dbRun('UPDATE contacts SET removed_at = NOW() WHERE id = ?', [$contactId]);
    dbRun(
        'UPDATE messages SET handled_at = NOW(), outcome = ? WHERE contact_id = ? AND handled_at IS NULL',
        ['removed', $contactId]
    );

    changeContactScore($contactId, -CONTACT_LOSS_ON_REMOVAL);

    $activeLeft = activeContactCount($userId);
    $delta = scoreForRemovingContact($activeLeft);
    $result = $delta === 0 ? 'removed' : 'below_minimum';

    changeScore($userId, $delta, $result);

    return [
        'result' => $result,
        'contact' => (string) $contact['name'],
        'delta' => $delta,
        'activeLeft' => $activeLeft,
    ];
}
