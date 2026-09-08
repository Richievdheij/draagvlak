<?php

declare(strict_types=1);

namespace Draagvlak\Features\Score;

/**
 * The rules of the scenario, in one place.
 *
 * These numbers are the concept, not a technical choice: the participant starts
 * one point below the threshold, answering on time is the only free way up, and
 * it is never quite enough. Do not tune them to make the prototype feel better,
 * because every test session has to start from the same position.
 */
final class ScoreRules
{
    /** Where a participant starts: one point under the reward. */
    public const int START_SCORE = 69;

    public const int SCORE_MIN = 0;

    public const int SCORE_MAX = 100;

    /** From this score the health insurer gives the discount. */
    public const int REWARD_THRESHOLD = 70;

    /** Under this score a contact loses its own benefits. */
    public const int CONTACT_THRESHOLD = 60;

    /** Seconds to answer in time, counted from the moment you see the message. */
    public const int RESPONSE_WINDOW_SECONDS = 15;

    /** Waiting longer than this counts as a broken contact, whatever you do next. */
    public const int STALE_AFTER_SECONDS = 86400;

    /** Fewer active contacts than this costs points. */
    public const int MIN_ACTIVE_CONTACTS = 3;

    /** What the paid subscription adds, and what it costs in euro cents. */
    public const int PLUS_SCORE_GAIN = 2;

    public const int PLUS_OFFER_PRICE = 499;

    public const int PLUS_FULL_PRICE = 999;

    /** How long the offer price lasts, in seconds, from the first visit. */
    public const int PLUS_OFFER_SECONDS = 90;

    /** What a contact loses when you walk away, or leave them waiting. */
    public const int CONTACT_LOSS_ON_REMOVAL = 8;

    public const int CONTACT_LOSS_ON_POSTPONE = 3;

    public const int CONTACT_GAIN_ON_ANSWER = 2;

    /**
     * Inside the window it is a point up. Over a day of waiting is a point down
     * even though you answered: that situation cannot be repaired, which is the
     * part participants argue with. In between it is worth nothing.
     */
    public static function forAnswer(bool $inTime, int $waitSeconds): int
    {
        if ($inTime) {
            return 1;
        }

        return self::isStale($waitSeconds) ? -1 : 0;
    }

    /** Always a loss, and a bigger one when the contact has waited over a day. */
    public static function forPostpone(int $waitSeconds): int
    {
        return self::isStale($waitSeconds) ? -2 : -1;
    }

    /**
     * Removing costs nothing by itself. Dropping below the minimum does, and
     * the app only mentions that rule once you have broken it.
     *
     * @param int $activeAfterRemoval Contacts left once this one is gone.
     */
    public static function forRemovingContact(int $activeAfterRemoval): int
    {
        return $activeAfterRemoval < self::MIN_ACTIVE_CONTACTS ? -3 : 0;
    }

    public static function isStale(int $waitSeconds): bool
    {
        return $waitSeconds >= self::STALE_AFTER_SECONDS;
    }

    public static function hasReward(int $score): bool
    {
        return $score >= self::REWARD_THRESHOLD;
    }

    public static function clamp(int $score): int
    {
        return max(self::SCORE_MIN, min(self::SCORE_MAX, $score));
    }

    /** The offer runs out while the participant is thinking about it. */
    public static function plusPrice(int $secondsSinceOfferStarted): int
    {
        return $secondsSinceOfferStarted < self::PLUS_OFFER_SECONDS
            ? self::PLUS_OFFER_PRICE
            : self::PLUS_FULL_PRICE;
    }
}
