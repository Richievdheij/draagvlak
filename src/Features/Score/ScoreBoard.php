<?php

declare(strict_types=1);

namespace Draagvlak\Features\Score;

use Draagvlak\Core\Data\Database;
use Draagvlak\Core\Http\Session;

/**
 * The number of one participant: changing it, and keeping the trail. Every
 * change is written to score_events as well, so a researcher can read back a
 * whole session.
 */
final readonly class ScoreBoard
{
    public function __construct(
        private Database $database,
        private Session $session,
    ) {}

    /**
     * @param int $delta Points, may be negative. Zero is still recorded.
     * @param string $reason English key describing what caused the change.
     *
     * @return int The score after the change.
     */
    public function change(int $userId, int $delta, string $reason): int
    {
        $current = (int) $this->database->value('SELECT score FROM users WHERE id = ?', [$userId]);
        $next = ScoreRules::clamp($current + $delta);

        $this->database->run('UPDATE users SET score = ? WHERE id = ?', [$next, $userId]);

        $this->database->insert('score_events', [
            'user_id' => $userId,
            'delta' => $next - $current,
            'reason' => $reason,
        ]);

        return $next;
    }

    /** @return list<array{delta: int, reason: string, created_at: string}> Newest first. */
    public function history(int $userId, int $limit = 10): array
    {
        return $this->database->all(
            'SELECT delta, reason, created_at FROM score_events WHERE user_id = ? ORDER BY id DESC LIMIT ' . max(1, $limit),
            [$userId],
        );
    }

    /**
     * How long ago this visit started, for the offer that runs out. Kept in the
     * session: the countdown belongs to this visit, and the next participant
     * has to see the same offer again.
     */
    public function offerSecondsElapsed(): int
    {
        $startedAt = $this->session->getInt('offerStartedAt');

        if ($startedAt === 0) {
            $startedAt = time();
            $this->session->set('offerStartedAt', $startedAt);
        }

        return time() - $startedAt;
    }
}
