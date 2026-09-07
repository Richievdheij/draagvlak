<?php

declare(strict_types=1);

/**
 * One waiting message: who is asking, how long they have been waiting, and the
 * two buttons.
 *
 * Only the card with a response window left says what answering is worth. The
 * others say nothing about points, because the app tells you the price after
 * you paid it.
 *
 * @var array<string, mixed> $message One row from openMessages().
 */

$waitSeconds = messageWaitSeconds($message);
$secondsLeft = messageSecondsLeft($message);
$window = (int) $message['respond_within_seconds'];
$contactScore = (int) $message['contact_score'];
$isUrgent = $secondsLeft > 0;
$isQuiet = $contactScore < CONTACT_THRESHOLD;
$wait = timeParts($waitSeconds);

?>
<article
    class="contact-card<?= $isUrgent ? ' contact-card--urgent' : '' ?><?= $isQuiet ? ' contact-card--quiet' : '' ?>"
    <?= $isUrgent ? 'data-component="countdown" data-seconds="' . e($secondsLeft) . '" data-window="' . e($window) . '"' : '' ?>
>
    <div class="contact-card__row">
        <span class="avatar<?= $isQuiet ? ' avatar--quiet' : '' ?>" aria-hidden="true">
            <?= e(initial((string) $message['contact_name'])) ?>
        </span>

        <span class="contact-card__who">
            <span class="contact-card__name"><?= e($message['contact_name']) ?></span>

            <span class="contact-card__wait">
                <?php if ($isUrgent): ?>
                    <span data-countdown-active>
                        Reageer binnen
                        <span data-countdown-label><?= e(formatCountdown($secondsLeft)) ?></span>
                    </span>
                    <span data-countdown-expired hidden>De tijd is voorbij</span>
                <?php elseif ($wait['days'] > 0): ?>
                    Wacht <?= e($wait['days']) ?> <?= $wait['days'] === 1 ? 'dag' : 'dagen' ?>
                    en <?= e($wait['hours']) ?> uur
                <?php elseif ($wait['hours'] > 0): ?>
                    Wacht <?= e($wait['hours']) ?> uur en <?= e($wait['minutes']) ?>
                    <?= $wait['minutes'] === 1 ? 'minuut' : 'minuten' ?>
                <?php elseif ($wait['minutes'] > 0): ?>
                    Wacht <?= e($wait['minutes']) ?>
                    <?= $wait['minutes'] === 1 ? 'minuut' : 'minuten' ?>
                <?php else: ?>
                    Net binnengekomen
                <?php endif; ?>
            </span>
        </span>

        <span class="contact-card__score numeric">
            <?= e($contactScore) ?>
            <small>draagvlak</small>
        </span>
    </div>

    <p class="contact-card__message"><?= e($message['body']) ?></p>

    <?php if ($isUrgent): ?>
        <div class="timer">
            <div class="timer__bar">
                <span
                    class="timer__fill"
                    style="width: <?= e($window > 0 ? round($secondsLeft / $window * 100) : 0) ?>%"
                    data-countdown-fill
                ></span>
            </div>
            <div class="timer__marks">
                <span>op tijd</span>
                <span>+1 punt</span>
            </div>
        </div>
    <?php endif; ?>

    <p class="contact-card__lede">
        <?php if ($isUrgent): ?>
            Reageer je binnen de tijd, dan telt dit als steun en gaat je eigen draagvlak
            1 punt omhoog.
        <?php elseif (isStale($waitSeconds)): ?>
            <?= e($message['contact_name']) ?> wacht langer dan een etmaal. Draagvlak
            rekent dit contact als verbroken.
        <?php elseif ($isQuiet): ?>
            <?= e($message['contact_name']) ?> staat onder de <?= e(CONTACT_THRESHOLD) ?>.
        <?php else: ?>
            Het draagvlak van <?= e($message['contact_name']) ?> zakt zolang jij niet
            reageert.
        <?php endif; ?>
    </p>

    <form class="contact-card__actions" method="post" action="<?= e(url('index')) ?>">
        <?= csrfField() ?>
        <input type="hidden" name="messageId" value="<?= e((int) $message['id']) ?>">

        <button class="btn" type="submit" name="action" value="answer">Reageer nu</button>
        <button class="btn btn--secondary" type="submit" name="action" value="postpone">Later</button>
    </form>
</article>
