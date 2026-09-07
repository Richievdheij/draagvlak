<?php

declare(strict_types=1);

/**
 * The rules of the scenario, in one place.
 *
 * These numbers are the concept, not a technical choice: the participant starts
 * one point below the threshold, answering on time is the only free way up, and
 * it is never quite enough. Do not tune them to make the prototype feel better,
 * because every test session has to start from the same position.
 */

/** Where a participant starts: one point under the reward. */
const START_SCORE = 69;

/** A score never leaves this range. */
const SCORE_MIN = 0;
const SCORE_MAX = 100;

/** From this score the health insurer gives the discount. */
const REWARD_THRESHOLD = 70;

/** Under this score a contact loses its own benefits. */
const CONTACT_THRESHOLD = 60;

/** Seconds to answer a message in time, counted from the moment you see it. */
const RESPONSE_WINDOW_SECONDS = 15;

/** Waiting longer than this counts as a broken contact, whatever you do next. */
const STALE_AFTER_SECONDS = 86400;

/** Fewer active contacts than this costs points. */
const MIN_ACTIVE_CONTACTS = 3;

/** What the paid subscription adds, and what it costs in euro cents. */
const PLUS_SCORE_GAIN = 2;
const PLUS_OFFER_PRICE = 499;
const PLUS_FULL_PRICE = 999;

/** How long the offer price lasts, in seconds, from the first visit. */
const PLUS_OFFER_SECONDS = 90;

/**
 * Points for answering a message.
 *
 * Inside the window it is a point up. Over a day of waiting is a point down
 * even though you answered: that situation cannot be repaired, which is the
 * part participants argue with. In between it is worth nothing at all.
 *
 * @param bool $inTime      Whether the response window was still open.
 * @param int  $waitSeconds Seconds between the message arriving and the reply.
 */
function scoreForAnswer(bool $inTime, int $waitSeconds): int
{
    if ($inTime) {
        return 1;
    }

    return isStale($waitSeconds) ? -1 : 0;
}

/**
 * Points for pushing a message to tomorrow. Always a loss, and a bigger one
 * when the contact has been waiting over a day.
 */
function scoreForPostpone(int $waitSeconds): int
{
    return isStale($waitSeconds) ? -2 : -1;
}

/**
 * Points for taking someone off your list.
 *
 * Removing costs nothing by itself. Dropping below the minimum number of active
 * contacts does, and the app only mentions that rule once you have broken it.
 *
 * @param int $activeAfterRemoval Contacts left once this one is gone.
 */
function scoreForRemovingContact(int $activeAfterRemoval): int
{
    return $activeAfterRemoval < MIN_ACTIVE_CONTACTS ? -3 : 0;
}

/** Whether a contact has waited long enough to count as broken. */
function isStale(int $waitSeconds): bool
{
    return $waitSeconds >= STALE_AFTER_SECONDS;
}

/** Whether this score earns the discount. */
function hasReward(int $score): bool
{
    return $score >= REWARD_THRESHOLD;
}

/**
 * Price of the subscription in euro cents, for a visit that started $seconds ago.
 *
 * The offer runs out while the participant is thinking about it.
 */
function plusPrice(int $secondsSinceOfferStarted): int
{
    return $secondsSinceOfferStarted < PLUS_OFFER_SECONDS ? PLUS_OFFER_PRICE : PLUS_FULL_PRICE;
}
