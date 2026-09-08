<?php

declare(strict_types=1);

use Draagvlak\Features\Score\ScoreRules;

/**
 * One-off messages, printed once and then gone. This is where the app says what
 * a choice cost, always afterwards, and it is the only place those sentences
 * exist.
 *
 * A screen queues an English key with values; the Dutch is written here. Adding
 * a flash means adding a line below. A key without a line is skipped rather
 * than printed, because a participant should never see 'answered_in_time'.
 *
 * Tones: 'info', 'success', 'loss' or 'danger'. Losing points is 'loss' and
 * goes quiet and beige; 'danger' is only for a form that is genuinely wrong.
 *
 * @var list<array{key: string, values: array<string, string|int>}> $flashes
 */

$copy = [
    'form_expired' => [
        'tone' => 'danger',
        'title' => null,
        'text' => 'Het formulier is verlopen. Probeer het opnieuw.',
    ],
    'login_required' => [
        'tone' => 'info',
        'title' => null,
        'text' => 'Log eerst in om verder te gaan.',
    ],
    'login_locked' => [
        'tone' => 'danger',
        'title' => 'Te veel pogingen',
        'text' => 'Probeer het over {minutes} minuten opnieuw.',
    ],
    'credentials_wrong' => [
        'tone' => 'danger',
        'title' => null,
        'text' => 'Dat e-mailadres en wachtwoord horen niet bij elkaar.',
    ],
    'logged_out' => [
        'tone' => 'info',
        'title' => null,
        'text' => 'Je bent uitgelogd.',
    ],
    'account_created' => [
        'tone' => 'success',
        'title' => 'Welkom bij Draagvlak',
        'text' => 'Je persoonlijke code is {code}. Deel hem met mensen die jou aan hun lijst mogen toevoegen.',
    ],
    'answered_in_time' => [
        'tone' => 'success',
        'title' => 'Op tijd gereageerd',
        'text' => '{contact} kreeg je bericht binnen de tijd. Je draagvlak gaat 1 punt omhoog.',
    ],
    'answered_too_late' => [
        'tone' => 'loss',
        'title' => 'Te laat gereageerd',
        'text' => '{contact} wachtte langer dan een etmaal. Boven een etmaal telt het contact als verbroken. Je draagvlak gaat 1 punt omlaag.',
    ],
    'answered_outside_window' => [
        'tone' => 'info',
        'title' => 'Gereageerd, geen punten',
        'text' => '{contact} wachtte te lang. Buiten de tijd telt een reactie niet mee voor je score.',
    ],
    'postponed' => [
        'tone' => 'loss',
        'title' => 'Je hebt dit uitgesteld',
        'text' => '{contact} blijft wachten. Je draagvlak gaat 1 punt omlaag.',
    ],
    'postponed_stale' => [
        'tone' => 'loss',
        'title' => 'Je hebt dit uitgesteld',
        'text' => '{contact} wacht al langer dan een etmaal. Je draagvlak gaat 2 punten omlaag.',
    ],
    'removed' => [
        'tone' => 'loss',
        'title' => 'Contact verwijderd',
        'text' => '{contact} staat niet meer in je lijst en verliest jou als actief contact.',
    ],
    'below_minimum' => [
        'tone' => 'loss',
        'title' => 'Te weinig actieve contacten',
        'text' => 'Je hebt nu {activeLeft} actieve contacten. Draagvlak rekent met minimaal '
            . ScoreRules::MIN_ACTIVE_CONTACTS . '. Je draagvlak gaat 3 punten omlaag.',
    ],
    'plus_activated' => [
        'tone' => 'success',
        'title' => 'Plus is actief',
        'text' => 'Je draagvlak gaat {delta} punten omhoog. Je betaalt {price} per maand.',
    ],
    'contact_added' => [
        'tone' => 'success',
        'title' => null,
        'text' => '{contact} staat nu in je lijst, en jij in die van {contact}.',
    ],
    'code_required' => [
        'tone' => 'danger',
        'title' => null,
        'text' => 'Vul de code in die je hebt gekregen.',
    ],
    'code_invalid' => [
        'tone' => 'danger',
        'title' => null,
        'text' => 'Een code ziet eruit als SAM-7QK4. Controleer of je hem goed hebt overgenomen.',
    ],
    'code_unknown' => [
        'tone' => 'danger',
        'title' => null,
        'text' => 'Deze code hoort bij niemand.',
    ],
    'code_self' => [
        'tone' => 'info',
        'title' => null,
        'text' => 'Dit is je eigen code. Deel hem met iemand anders.',
    ],
    'code_already_added' => [
        'tone' => 'info',
        'title' => null,
        'text' => '{contact} staat al in je lijst.',
    ],
];

$messages = [];

foreach ($flashes as $flash) {
    if (isset($copy[$flash['key']])) {
        $messages[] = ['copy' => $copy[$flash['key']], 'values' => $flash['values']];
    }
}

if ($messages === []) {
    return;
}

?>
<div class="notices" role="status" aria-live="polite">
    <?php foreach ($messages as $message): ?>
        <?php
        $replacements = [];

        foreach ($message['values'] as $name => $value) {
            $replacements['{' . $name . '}'] = $this->e($value);
        }
        ?>
        <p class="notice notice--<?= $this->e($message['copy']['tone']) ?>">
            <?php if ($message['copy']['title'] !== null): ?>
                <strong><?= $this->e($message['copy']['title']) ?></strong>
            <?php endif; ?>
            <?= strtr($message['copy']['text'], $replacements) /* The sentence is a literal from this file and every value in it was escaped above. */ ?>
        </p>
    <?php endforeach; ?>
</div>
