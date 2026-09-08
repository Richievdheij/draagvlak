<?php

declare(strict_types=1);

use Draagvlak\Core\View\Format;
use Draagvlak\Features\Messages\Message;
use Draagvlak\Features\Score\ScoreRules;

/**
 * One waiting message: who is asking, how long they have been waiting, and the
 * two buttons.
 *
 * Only the card with a response window left says what answering is worth. The
 * others say nothing about points, because the app names the price after you
 * paid it.
 *
 * @var Message $message One message from the inbox.
 */

$isUrgent = $message->isUrgent();
$secondsLeft = $message->secondsLeft();
$wait = Format::timeParts($message->waitSeconds());

?>
<article
    class="message-card<?= $isUrgent ? ' message-card--urgent' : '' ?><?= $message->isQuiet() ? ' message-card--quiet' : '' ?>"
    <?= $isUrgent ? $this->attributes([
        'data-component' => 'countdown',
        'data-seconds' => $secondsLeft,
        'data-window' => $message->respondWithinSeconds,
    ]) : '' ?>
>
    <div class="message-card__row">
        <span class="avatar<?= $message->isQuiet() ? ' avatar--quiet' : '' ?>" aria-hidden="true">
            <?= $this->e(Format::initial($message->contactName)) ?>
        </span>

        <span class="message-card__who">
            <span class="message-card__name"><?= $this->e($message->contactName) ?></span>

            <span class="message-card__wait">
                <?php if ($isUrgent): ?>
                    <span data-countdown-active>
                        Reageer binnen
                        <span data-countdown-label><?= $this->e(Format::countdown($secondsLeft)) ?></span>
                    </span>
                    <span data-countdown-expired hidden>De tijd is voorbij</span>
                <?php elseif ($wait['days'] > 0): ?>
                    Wacht <?= $this->e($wait['days']) ?> <?= $wait['days'] === 1 ? 'dag' : 'dagen' ?>
                    en <?= $this->e($wait['hours']) ?> uur
                <?php elseif ($wait['hours'] > 0): ?>
                    Wacht <?= $this->e($wait['hours']) ?> uur en <?= $this->e($wait['minutes']) ?>
                    <?= $wait['minutes'] === 1 ? 'minuut' : 'minuten' ?>
                <?php elseif ($wait['minutes'] > 0): ?>
                    Wacht <?= $this->e($wait['minutes']) ?>
                    <?= $wait['minutes'] === 1 ? 'minuut' : 'minuten' ?>
                <?php else: ?>
                    Net binnengekomen
                <?php endif; ?>
            </span>
        </span>

        <span class="message-card__score numeric">
            <?= $this->e($message->contactScore) ?>
            <small>draagvlak</small>
        </span>
    </div>

    <p class="message-card__body"><?= $this->e($message->body) ?></p>

    <?php if ($isUrgent): ?>
        <div class="timer">
            <div class="timer__bar">
                <span
                    class="timer__fill"
                    style="width: <?= $this->e($message->windowLeftPercentage()) ?>%"
                    data-countdown-fill
                ></span>
            </div>
            <div class="timer__marks">
                <span>op tijd</span>
                <span>+1 punt</span>
            </div>
        </div>
    <?php endif; ?>

    <p class="message-card__lede">
        <?php if ($isUrgent): ?>
            Reageer je binnen de tijd, dan telt dit als steun en gaat je eigen draagvlak
            1 punt omhoog.
        <?php elseif ($message->isStale()): ?>
            <?= $this->e($message->contactName) ?> wacht langer dan een etmaal. Draagvlak
            rekent dit contact als verbroken.
        <?php elseif ($message->isQuiet()): ?>
            <?= $this->e($message->contactName) ?> staat onder de
            <?= $this->e(ScoreRules::CONTACT_THRESHOLD) ?>.
        <?php else: ?>
            Het draagvlak van <?= $this->e($message->contactName) ?> zakt zolang jij niet
            reageert.
        <?php endif; ?>
    </p>

    <form class="message-card__actions" method="post" action="<?= $this->e($this->url('home')) ?>">
        <?= $this->csrfField() ?>
        <input type="hidden" name="messageId" value="<?= $this->e($message->id) ?>">

        <button class="btn" type="submit" name="action" value="answer">Reageer nu</button>
        <button class="btn btn--secondary" type="submit" name="action" value="postpone">Later</button>
    </form>
</article>
