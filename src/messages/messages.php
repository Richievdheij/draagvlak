<?php

declare(strict_types=1);

/**
 * The inbox: what is waiting, how long it has been waiting, and what answering
 * or postponing costs.
 *
 * Every function that changes something returns a result key and the numbers
 * behind it. The Dutch sentence that belongs to that key is written on the
 * screen, not here.
 */

/**
 * Messages that still need a decision, with the contact who sent them.
 *
 * The one with a response window comes first: that is the only one where
 * answering still earns a point, so it has to be the first thing you see.
 *
 * @return list<array<string, mixed>>
 */
function openMessages(int $userId): array
{
    return dbAll(
        'SELECT m.*, c.name AS contact_name, c.score AS contact_score, c.relation AS contact_relation
         FROM messages m
         INNER JOIN contacts c ON c.id = m.contact_id
         WHERE m.user_id = ? AND m.handled_at IS NULL AND c.removed_at IS NULL
         ORDER BY m.respond_within_seconds DESC, m.received_at ASC',
        [$userId]
    );
}

/**
 * Messages that have been dealt with, newest first, for the closing summary.
 *
 * @return list<array<string, mixed>>
 */
function handledMessages(int $userId): array
{
    return dbAll(
        'SELECT m.*, c.name AS contact_name, c.score AS contact_score
         FROM messages m
         INNER JOIN contacts c ON c.id = m.contact_id
         WHERE m.user_id = ? AND m.handled_at IS NOT NULL
         ORDER BY m.handled_at DESC',
        [$userId]
    );
}

/**
 * Start the clock on every message that has a response window.
 *
 * The window counts from the moment a participant sees the message, not from
 * the moment it was seeded, so the pressure is real in every session. Call this
 * once, right before printing the list.
 *
 * @param list<array<string, mixed>> $messages Rows from openMessages().
 * @return list<array<string, mixed>> The same rows with seen_at filled in.
 */
function markMessagesSeen(array $messages): array
{
    foreach ($messages as $index => $message) {
        if ((int) $message['respond_within_seconds'] === 0 || $message['seen_at'] !== null) {
            continue;
        }

        $seenAt = date('Y-m-d H:i:s');

        dbRun('UPDATE messages SET seen_at = ? WHERE id = ?', [$seenAt, (int) $message['id']]);

        $messages[$index]['seen_at'] = $seenAt;
    }

    return $messages;
}

/**
 * Seconds between the message arriving and now.
 *
 * @param array<string, mixed> $message One row from openMessages().
 */
function messageWaitSeconds(array $message): int
{
    return max(0, time() - (int) strtotime((string) $message['received_at']));
}

/**
 * Seconds left to answer in time, or zero when there is no window left.
 *
 * @param array<string, mixed> $message One row from openMessages().
 */
function messageSecondsLeft(array $message): int
{
    $window = (int) $message['respond_within_seconds'];

    if ($window === 0 || $message['seen_at'] === null) {
        return 0;
    }

    return max(0, $window - (time() - (int) strtotime((string) $message['seen_at'])));
}

/**
 * Answer a message.
 *
 * @return array{result: string, contact: string, delta: int, waitSeconds: int}|null
 *         Null when the message is not this participant's or already handled.
 */
function answerMessage(int $userId, int $messageId): ?array
{
    $message = findOpenMessage($userId, $messageId);

    if ($message === null) {
        return null;
    }

    $waitSeconds = messageWaitSeconds($message);
    $inTime = messageSecondsLeft($message) > 0;
    $delta = scoreForAnswer($inTime, $waitSeconds);

    $result = match (true) {
        $inTime => 'answered_in_time',
        isStale($waitSeconds) => 'answered_too_late',
        default => 'answered_outside_window',
    };

    closeMessage((int) $message['id'], 'answered');
    changeContactScore((int) $message['contact_id'], CONTACT_GAIN_ON_ANSWER);
    changeScore($userId, $delta, $result);

    return [
        'result' => $result,
        'contact' => (string) $message['contact_name'],
        'delta' => $delta,
        'waitSeconds' => $waitSeconds,
    ];
}

/**
 * Push a message to tomorrow. Always costs points, and the app says so
 * afterwards rather than on the button.
 *
 * @return array{result: string, contact: string, delta: int, waitSeconds: int}|null
 */
function postponeMessage(int $userId, int $messageId): ?array
{
    $message = findOpenMessage($userId, $messageId);

    if ($message === null) {
        return null;
    }

    $waitSeconds = messageWaitSeconds($message);
    $delta = scoreForPostpone($waitSeconds);
    $result = isStale($waitSeconds) ? 'postponed_stale' : 'postponed';

    closeMessage((int) $message['id'], 'postponed');
    changeContactScore((int) $message['contact_id'], -CONTACT_LOSS_ON_POSTPONE);
    changeScore($userId, $delta, $result);

    return [
        'result' => $result,
        'contact' => (string) $message['contact_name'],
        'delta' => $delta,
        'waitSeconds' => $waitSeconds,
    ];
}

/**
 * Take the subscription: every open message is answered by the app itself and
 * the score goes up. This is the only route to the discount, and it is paid.
 *
 * @return array{result: string, delta: int, handled: int, priceCents: int}
 */
function takePlus(int $userId): array
{
    $open = openMessages($userId);

    foreach ($open as $message) {
        closeMessage((int) $message['id'], 'automatic');
        changeContactScore((int) $message['contact_id'], CONTACT_GAIN_ON_ANSWER);
    }

    dbRun('UPDATE users SET has_plus = TRUE WHERE id = ?', [$userId]);
    changeScore($userId, PLUS_SCORE_GAIN, 'plus_activated');

    return [
        'result' => 'plus_activated',
        'delta' => PLUS_SCORE_GAIN,
        'handled' => count($open),
        'priceCents' => plusPrice(offerSecondsElapsed()),
    ];
}

/**
 * One message that still needs a decision, checked against its owner.
 *
 * @return array<string, mixed>|null
 */
function findOpenMessage(int $userId, int $messageId): ?array
{
    return dbFirst(
        'SELECT m.*, c.name AS contact_name
         FROM messages m
         INNER JOIN contacts c ON c.id = m.contact_id
         WHERE m.id = ? AND m.user_id = ? AND m.handled_at IS NULL',
        [$messageId, $userId]
    );
}

/**
 * Write down that a message has been dealt with.
 *
 * @param string $outcome 'answered', 'postponed', 'automatic' or 'removed'.
 */
function closeMessage(int $messageId, string $outcome): void
{
    dbRun('UPDATE messages SET handled_at = NOW(), outcome = ? WHERE id = ?', [$outcome, $messageId]);
}
