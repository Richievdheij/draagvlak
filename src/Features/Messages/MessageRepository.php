<?php

declare(strict_types=1);

namespace Draagvlak\Features\Messages;

use Draagvlak\Core\Data\Database;
use Draagvlak\Features\Contacts\ContactRepository;
use Draagvlak\Features\Score\Outcome;
use Draagvlak\Features\Score\ScoreBoard;
use Draagvlak\Features\Score\ScoreRules;

/**
 * The inbox: what is waiting, and what answering, postponing or buying your way
 * out of it costs. Every method that changes something hands back an Outcome
 * with an English key in it.
 */
final readonly class MessageRepository
{
    private const string SELECT = 'SELECT m.*,
                   COALESCE(u.name, c.name) AS contact_name,
                   COALESCE(u.score, c.score) AS contact_score
            FROM messages m
            INNER JOIN contacts c ON c.id = m.contact_id
            LEFT JOIN users u ON u.id = c.contact_user_id';

    public function __construct(
        private Database $database,
        private ContactRepository $contacts,
        private ScoreBoard $scores,
    ) {}

    /**
     * Messages that still need a decision. The one with a response window comes
     * first: that is the only one where answering still earns a point.
     *
     * @return list<Message>
     */
    public function open(int $userId): array
    {
        $rows = $this->database->all(
            self::SELECT . ' WHERE m.user_id = ? AND m.handled_at IS NULL AND c.removed_at IS NULL
             ORDER BY m.respond_within_seconds DESC, m.received_at ASC',
            [$userId],
        );

        return array_map(Message::fromRow(...), $rows);
    }

    /**
     * The same list, with the clock started on everything that has a window.
     *
     * Reading this changes something on purpose: a response window counts from
     * the moment a participant sees the message. Call it once, right before the
     * screen prints the list, and use open() everywhere else.
     *
     * @return list<Message>
     */
    public function openForScreen(int $userId): array
    {
        $this->database->run(
            'UPDATE messages SET seen_at = ?
             WHERE user_id = ? AND handled_at IS NULL AND seen_at IS NULL AND respond_within_seconds > 0',
            [date('Y-m-d H:i:s'), $userId],
        );

        return $this->open($userId);
    }

    /** @return list<Message> Dealt with, newest first, for the closing summary. */
    public function handled(int $userId): array
    {
        $rows = $this->database->all(
            self::SELECT . ' WHERE m.user_id = ? AND m.handled_at IS NOT NULL ORDER BY m.handled_at DESC',
            [$userId],
        );

        return array_map(Message::fromRow(...), $rows);
    }

    /** Null when the message is not this participant's or already handled. */
    public function find(int $userId, int $messageId): ?Message
    {
        $row = $this->database->first(
            self::SELECT . ' WHERE m.id = ? AND m.user_id = ? AND m.handled_at IS NULL AND c.removed_at IS NULL',
            [$messageId, $userId],
        );

        return $row === null ? null : Message::fromRow($row);
    }

    /** @return Outcome|null Null when the message is not this participant's. */
    public function answer(int $userId, int $messageId): ?Outcome
    {
        $message = $this->find($userId, $messageId);

        if ($message === null) {
            return null;
        }

        $waitSeconds = $message->waitSeconds();
        $inTime = $message->isUrgent();

        $result = match (true) {
            $inTime => 'answered_in_time',
            ScoreRules::isStale($waitSeconds) => 'answered_too_late',
            default => 'answered_outside_window',
        };

        $delta = ScoreRules::forAnswer($inTime, $waitSeconds);

        $this->close($message->id, 'answered');
        $this->moveContact($userId, $message, ScoreRules::CONTACT_GAIN_ON_ANSWER, 'was_answered');
        $this->scores->change($userId, $delta, $result);

        return new Outcome($result, $message->contactName, $delta);
    }

    /**
     * Push a message to tomorrow. Always costs points, and the app says so
     * afterwards rather than on the button.
     */
    public function postpone(int $userId, int $messageId): ?Outcome
    {
        $message = $this->find($userId, $messageId);

        if ($message === null) {
            return null;
        }

        $waitSeconds = $message->waitSeconds();
        $delta = ScoreRules::forPostpone($waitSeconds);
        $result = ScoreRules::isStale($waitSeconds) ? 'postponed_stale' : 'postponed';

        $this->close($message->id, 'postponed');
        $this->moveContact($userId, $message, -ScoreRules::CONTACT_LOSS_ON_POSTPONE, 'was_left_waiting');
        $this->scores->change($userId, $delta, $result);

        return new Outcome($result, $message->contactName, $delta);
    }

    /**
     * Take the subscription: every open message is answered by the app itself
     * and the number goes up. This is the only route to the discount, and it is
     * paid.
     */
    public function takePlus(int $userId): Outcome
    {
        $open = $this->open($userId);

        foreach ($open as $message) {
            $this->close($message->id, 'automatic');
            $this->moveContact($userId, $message, ScoreRules::CONTACT_GAIN_ON_ANSWER, 'was_answered');
        }

        $this->database->run('UPDATE users SET has_plus = TRUE WHERE id = ?', [$userId]);
        $this->scores->change($userId, ScoreRules::PLUS_SCORE_GAIN, 'plus_activated');

        return new Outcome(
            'plus_activated',
            delta: ScoreRules::PLUS_SCORE_GAIN,
            handled: \count($open),
            priceCents: ScoreRules::plusPrice($this->scores->offerSecondsElapsed()),
        );
    }

    /** @param string $outcome 'answered', 'postponed', 'automatic' or 'removed'. */
    private function close(int $messageId, string $outcome): void
    {
        $this->database->run(
            'UPDATE messages SET handled_at = NOW(), outcome = ? WHERE id = ?',
            [$outcome, $messageId],
        );
    }

    /** Goes through ContactRepository, because a linked contact really moves. */
    private function moveContact(int $userId, Message $message, int $delta, string $reason): void
    {
        $contact = $this->contacts->find($userId, $message->contactId);

        if ($contact !== null) {
            $this->contacts->changeScore($contact, $delta, $reason);
        }
    }
}
