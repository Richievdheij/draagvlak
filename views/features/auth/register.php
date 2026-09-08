<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Registration;
use Draagvlak\Features\Score\ScoreRules;

/**
 * The registration form. Three fields and no more: there is no second password
 * box, because repeating a password does not catch the typo that matters.
 *
 * Registration returns an English key per field that is wrong; the Dutch for
 * those keys is the list below.
 *
 * @var array{values: array<string, string>, errors: array<string, string>} $form
 */

$errorText = [
    'name_required' => 'Vul je naam in.',
    'name_long' => sprintf('Houd je naam onder de %d tekens.', Registration::MAX_NAME_LENGTH),
    'email_required' => 'Vul je e-mailadres in.',
    'email_invalid' => 'Dit lijkt geen geldig e-mailadres.',
    'email_taken' => 'Er bestaat al een account met dit adres.',
    'password_short' => sprintf('Kies een wachtwoord van minstens %d tekens.', Registration::MIN_PASSWORD_LENGTH),
    'password_long' => sprintf('Kies een wachtwoord van hoogstens %d tekens.', Registration::MAX_PASSWORD_LENGTH),
];

?>
<section class="auth-card">
    <h1 class="auth-card__title">Account maken</h1>

    <p class="auth-card__intro">
        Je krijgt een startcijfer van
        <span class="numeric"><?= $this->e(ScoreRules::START_SCORE) ?></span> en een eigen
        code. Wat je cijfer daarna doet, hangt van je contacten af.
    </p>

    <form class="auth-card__form stack" method="post" action="<?= $this->e($this->url('register')) ?>">
        <?= $this->csrfField() ?>

        <div class="field">
            <label class="field__label" for="name">Naam</label>
            <input
                class="field__input"
                type="text"
                id="name"
                name="name"
                value="<?= $this->e($form['values']['name'] ?? '') ?>"
                autocomplete="name"
                maxlength="<?= $this->e(Registration::MAX_NAME_LENGTH) ?>"
                required
                autofocus
                <?= isset($form['errors']['name']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['name'])): ?>
                <p class="field__error"><?= $this->e($errorText[$form['errors']['name']] ?? '') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="email">E-mailadres</label>
            <input
                class="field__input"
                type="email"
                id="email"
                name="email"
                value="<?= $this->e($form['values']['email'] ?? '') ?>"
                autocomplete="email"
                required
                <?= isset($form['errors']['email']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['email'])): ?>
                <p class="field__error"><?= $this->e($errorText[$form['errors']['email']] ?? '') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="password">Wachtwoord</label>
            <input
                class="field__input"
                type="password"
                id="password"
                name="password"
                autocomplete="new-password"
                required
                <?= isset($form['errors']['password']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['password'])): ?>
                <p class="field__error"><?= $this->e($errorText[$form['errors']['password']] ?? '') ?></p>
            <?php else: ?>
                <p class="field__hint">
                    Minstens <?= $this->e(Registration::MIN_PASSWORD_LENGTH) ?> tekens.
                </p>
            <?php endif; ?>
        </div>

        <button class="btn btn--block" type="submit">Account maken</button>
    </form>

    <p class="auth-card__switch">
        Heb je al een account? <a href="<?= $this->e($this->url('login')) ?>">Inloggen</a>.
    </p>
</section>
