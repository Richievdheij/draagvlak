<?php

declare(strict_types=1);

/**
 * Home: your number, then the people behind it.
 *
 * The order on this screen is the argument of the whole prototype. You open the
 * app and the first thing you see is what you are losing, not who you know.
 *
 * Every choice on this page is a POST that ends in a redirect, so the app can
 * tell you afterwards what it cost. That is the only moment it explains a rule.
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

$userId = currentUserId();

if (isPost()) {
    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
        redirect('index');
    }

    $outcome = match (input('action')) {
        'answer' => answerMessage($userId, inputInt('messageId')),
        'postpone' => postponeMessage($userId, inputInt('messageId')),
        'remove' => removeContactFromList($userId, inputInt('contactId')),
        'plus' => takePlus($userId),
        default => null,
    };

    if ($outcome !== null) {
        $contact = (string) ($outcome['contact'] ?? '');

        [$title, $notice, $tone] = match ($outcome['result']) {
            'answered_in_time' => [
                'Op tijd gereageerd',
                sprintf('%s kreeg je bericht binnen de tijd. Je draagvlak gaat 1 punt omhoog.', $contact),
                'success',
            ],
            'answered_too_late' => [
                'Te laat gereageerd',
                sprintf('%s wachtte langer dan een etmaal. Boven een etmaal telt het contact als verbroken. Je draagvlak gaat 1 punt omlaag.', $contact),
                'loss',
            ],
            'answered_outside_window' => [
                'Gereageerd, geen punten',
                sprintf('%s wachtte te lang. Buiten de tijd telt een reactie niet mee voor je score.', $contact),
                'info',
            ],
            'postponed' => [
                'Je hebt dit uitgesteld',
                sprintf('%s blijft wachten. Je draagvlak gaat 1 punt omlaag.', $contact),
                'loss',
            ],
            'postponed_stale' => [
                'Je hebt dit uitgesteld',
                sprintf('%s wacht al langer dan een etmaal. Je draagvlak gaat 2 punten omlaag.', $contact),
                'loss',
            ],
            'removed' => [
                'Contact verwijderd',
                sprintf('%s staat niet meer in je lijst en verliest jou als actief contact.', $contact),
                'loss',
            ],
            'below_minimum' => [
                'Te weinig actieve contacten',
                sprintf(
                    'Je hebt nu %d actieve contacten. Draagvlak rekent met minimaal %d. Je draagvlak gaat 3 punten omlaag.',
                    $outcome['activeLeft'] ?? 0,
                    MIN_ACTIVE_CONTACTS
                ),
                'loss',
            ],
            'plus_activated' => [
                'Plus is actief',
                sprintf(
                    'Je draagvlak gaat %d punten omhoog. Je betaalt %s per maand.',
                    PLUS_SCORE_GAIN,
                    formatPrice($outcome['priceCents'])
                ),
                'success',
            ],
            default => [null, '', 'info'],
        };

        if ($notice !== '') {
            flash($notice, $tone, $title);
        }
    }

    redirect('index');
}

$user = currentUser();
$score = (int) $user['score'];
$hasPlus = (bool) $user['has_plus'];

$messages = markMessagesSeen(openMessages($userId));
$contacts = activeContacts($userId);
$handled = handledMessages($userId);

$offerElapsed = offerSecondsElapsed();
$offerSecondsLeft = max(0, PLUS_OFFER_SECONDS - $offerElapsed);

page('Jouw draagvlak', [
    'nav' => 'index',
    'description' => 'Je sociale steun in één cijfer.',
]);

?>
<?php partial('score-block', ['score' => $score, 'activeCount' => count($contacts)]); ?>

<section class="section">
    <h2 class="section__title">Wachten op jou</h2>

    <?php if ($messages === []): ?>
        <p class="home__empty">
            Er wacht op dit moment niemand op je. Je draagvlak daalt nu niet.
        </p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <?php partial('contact-card', ['message' => $message]); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php if (!$hasPlus): ?>
    <section class="section">
        <div class="offer">
            <p class="offer__kicker">Draagvlak Plus</p>

            <h2 class="offer__title">Zet je draagvlak <?= e(PLUS_SCORE_GAIN) ?> punten hoger</h2>

            <p class="offer__body">
                Plus laat je reactietijd gunstiger meewegen en beantwoordt berichten
                automatisch als je er even niet bent.
            </p>

            <div
                class="offer__price"
                data-component="countdown"
                data-seconds="<?= e($offerSecondsLeft) ?>"
            >
                <span class="offer__now"><?= e(formatPrice(plusPrice($offerElapsed))) ?></span>

                <?php if ($offerSecondsLeft > 0): ?>
                    <span class="offer__was"><?= e(formatPrice(PLUS_FULL_PRICE)) ?></span>
                <?php endif; ?>

                <span class="offer__left">
                    <span data-countdown-active<?= $offerSecondsLeft > 0 ? '' : ' hidden' ?>>
                        actieprijs nog
                        <span data-countdown-label><?= e(formatCountdown($offerSecondsLeft)) ?></span>
                    </span>
                    <span data-countdown-expired<?= $offerSecondsLeft > 0 ? ' hidden' : '' ?>>actie verlopen</span>
                </span>
            </div>

            <form method="post" action="<?= e(url('index')) ?>">
                <?= csrfField() ?>
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

            <form class="home__cleanup" method="post" action="<?= e(url('index')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="remove">

                <?php foreach ($contacts as $contact): ?>
                    <button
                        class="btn btn--quiet"
                        type="submit"
                        name="contactId"
                        value="<?= e((int) $contact['id']) ?>"
                    ><?= e($contact['name']) ?> verwijderen</button>
                <?php endforeach; ?>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php if ($messages === [] && $handled !== []): ?>
    <section class="section">
        <div class="summary">
            <h2 class="summary__title">Je draagvlak staat op <?= e($score) ?></h2>

            <p class="summary__body">
                <?php if (hasReward($score)): ?>
                    Je korting van 5 euro is actief. Blijf op <?= e(REWARD_THRESHOLD) ?> of
                    hoger om hem te houden.
                <?php else: ?>
                    <?php $missing = REWARD_THRESHOLD - $score; ?>
                    Je hebt de korting niet gehaald. Je hebt nog
                    <?= e($missing) ?> <?= $missing === 1 ? 'punt' : 'punten' ?> nodig.
                <?php endif; ?>
            </p>

            <ul class="summary__list">
                <?php foreach ($handled as $item): ?>
                    <li>
                        <?= e($item['contact_name']) ?>:
                        <?= e(match ($item['outcome']) {
                            'answered' => 'je hebt gereageerd',
                            'postponed' => 'je hebt uitgesteld',
                            'automatic' => 'automatisch beantwoord',
                            'removed' => 'uit je lijst gehaald',
                            default => 'afgehandeld',
                        }) ?>. Draagvlak nu <span class="numeric"><?= e((int) $item['contact_score']) ?></span>.
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="summary__closing">Morgen wordt je draagvlak opnieuw berekend.</p>
        </div>
    </section>
<?php endif; ?>
