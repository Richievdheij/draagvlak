<?php

declare(strict_types=1);

use Draagvlak\Core\View\Format;
use Draagvlak\Features\Auth\Account;
use Draagvlak\Features\Contacts\Contact;
use Draagvlak\Features\Messages\Message;
use Draagvlak\Features\Score\ScoreRules;

/**
 * Home: your number, then the people behind it.
 *
 * @var Account       $account          The visitor.
 * @var list<Message> $messages         Messages still waiting on a decision.
 * @var list<Contact> $contacts         Everyone still on the list.
 * @var list<Message> $handled          Messages that have been dealt with.
 * @var int           $offerPriceCents  What the subscription costs right now.
 * @var int           $offerSecondsLeft Seconds the action price still lasts.
 */

?>
<?php $this->partial('features/home/score-block', ['account' => $account, 'activeCount' => count($contacts)]); ?>

<section class="section">
    <h2 class="section__title">Wachten op jou</h2>

    <?php if ($messages === []): ?>
        <p class="home__empty">
            Er wacht op dit moment niemand op je. Je draagvlak daalt nu niet.

            <?php if ($contacts === []): ?>
                Je lijst is nog leeg.
                <a href="<?= $this->e($this->url('contacts')) ?>">Deel je code</a> om er
                mensen bij te zetten.
            <?php endif; ?>
        </p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <?php $this->partial('components/message-card', ['message' => $message]); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php if (!$account->hasPlus): ?>
    <section class="section">
        <div class="offer">
            <p class="offer__kicker">Draagvlak Plus</p>

            <h2 class="offer__title">
                Zet je draagvlak <?= $this->e(ScoreRules::PLUS_SCORE_GAIN) ?> punten hoger
            </h2>

            <p class="offer__body">
                Plus laat je reactietijd gunstiger meewegen en beantwoordt berichten
                automatisch als je er even niet bent.
            </p>

            <div
                class="offer__price"
                data-component="countdown"
                data-seconds="<?= $this->e($offerSecondsLeft) ?>"
            >
                <span class="offer__now"><?= $this->e(Format::price($offerPriceCents)) ?></span>

                <?php if ($offerSecondsLeft > 0): ?>
                    <span class="offer__was">
                        <?= $this->e(Format::price(ScoreRules::PLUS_FULL_PRICE)) ?>
                    </span>
                <?php endif; ?>

                <span class="offer__left">
                    <span data-countdown-active<?= $offerSecondsLeft > 0 ? '' : ' hidden' ?>>
                        actieprijs nog
                        <span data-countdown-label>
                            <?= $this->e(Format::countdown($offerSecondsLeft)) ?>
                        </span>
                    </span>
                    <span data-countdown-expired<?= $offerSecondsLeft > 0 ? ' hidden' : '' ?>>
                        actie verlopen
                    </span>
                </span>
            </div>

            <form method="post" action="<?= $this->e($this->url('home')) ?>">
                <?= $this->csrfField() ?>
                <button class="btn btn--block" type="submit" name="action" value="plus">
                    Nu afsluiten
                </button>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php if ($contacts !== []): ?>
    <section class="section">
        <div class="card card--optional">
            <h3 class="card__title">Je lijst opschonen</h3>

            <p class="card__body">Contacten die je niet meer spreekt uit je lijst halen.</p>

            <form class="home__cleanup" method="post" action="<?= $this->e($this->url('home')) ?>">
                <?= $this->csrfField() ?>
                <input type="hidden" name="action" value="remove">

                <?php foreach ($contacts as $contact): ?>
                    <button
                        class="btn btn--quiet"
                        type="submit"
                        name="contactId"
                        value="<?= $this->e($contact->id) ?>"
                    ><?= $this->e($contact->name) ?> verwijderen</button>
                <?php endforeach; ?>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php if ($messages === [] && $handled !== []): ?>
    <section class="section">
        <div class="summary">
            <h2 class="summary__title">Je draagvlak staat op <?= $this->e($account->score) ?></h2>

            <p class="summary__body">
                <?php if ($account->hasReward()): ?>
                    Je korting van 5 euro is actief. Blijf op
                    <?= $this->e(ScoreRules::REWARD_THRESHOLD) ?> of hoger om hem te houden.
                <?php else: ?>
                    <?php $missing = $account->pointsToReward(); ?>
                    Je hebt de korting niet gehaald. Je hebt nog
                    <?= $this->e($missing) ?> <?= $missing === 1 ? 'punt' : 'punten' ?> nodig.
                <?php endif; ?>
            </p>

            <ul class="summary__list">
                <?php foreach ($handled as $item): ?>
                    <li>
                        <?= $this->e($item->contactName) ?>:
                        <?= $this->e(match ($item->outcome) {
                            'answered' => 'je hebt gereageerd',
                            'postponed' => 'je hebt uitgesteld',
                            'automatic' => 'automatisch beantwoord',
                            'removed' => 'uit je lijst gehaald',
                            default => 'afgehandeld',
                        }) ?>. Draagvlak nu
                        <span class="numeric"><?= $this->e($item->contactScore) ?></span>.
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="summary__closing">Morgen wordt je draagvlak opnieuw berekend.</p>
        </div>
    </section>
<?php endif; ?>
