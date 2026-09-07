<?php

declare(strict_types=1);

/**
 * The score of one participant: reading it, changing it, and keeping the trail.
 *
 * Every change is written to score_events as well as to the number on the user,
 * so a screen can show what happened and a researcher can read back an entire
 * session afterwards.
 */

/**
 * Change a score by a number of points and write down why.
 *
 * The reason is an English key ('answered_in_time', 'postponed'), not a
 * sentence: the Dutch wording belongs on the screen that prints it.
 *
 * @param int    $delta  Points, may be negative. Zero is still recorded.
 * @param string $reason Key describing what caused the change.
 * @return int The score after the change.
 */
function changeScore(int $userId, int $delta, string $reason): int
{
    $current = (int) dbValue('SELECT score FROM users WHERE id = ?', [$userId]);
    $next = max(SCORE_MIN, min(SCORE_MAX, $current + $delta));

    dbRun('UPDATE users SET score = ? WHERE id = ?', [$next, $userId]);

    dbInsert('score_events', [
        'user_id' => $userId,
        'delta' => $next - $current,
        'reason' => $reason,
    ]);

    return $next;
}

/**
 * The most recent changes, newest first.
 *
 * @return list<array{delta: int, reason: string, created_at: string}>
 */
function scoreHistory(int $userId, int $limit = 10): array
{
    return dbAll(
        'SELECT delta, reason, created_at FROM score_events WHERE user_id = ? ORDER BY id DESC LIMIT ' . max(1, $limit),
        [$userId]
    );
}

/**
 * When the current visit started, used for the offer that runs out.
 *
 * Kept in the session and not in the database: the countdown belongs to this
 * visit, and the next participant has to see the same offer again.
 */
function offerSecondsElapsed(): int
{
    $startedAt = (int) sessionGet('offerStartedAt', 0);

    if ($startedAt === 0) {
        $startedAt = time();
        sessionSet('offerStartedAt', $startedAt);
    }

    return time() - $startedAt;
}
