<?php

declare(strict_types=1);

/**
 * Make an account.
 *
 * A new account starts at the same score as everyone else, so two participants
 * are comparable. The password is hashed before it is stored and is never kept
 * to fill the form again.
 */

require __DIR__ . '/../bootstrap.php';

requireGuest();

if (isPost()) {
    $name = (string) input('name', '');
    $email = (string) input('email', '');
    $password = (string) input('password', '');
    $repeat = (string) input('passwordRepeat', '');

    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
        redirect('registreren');
    }

    $errors = validateRegistration($name, $email, $password, $repeat);

    if ($errors === []) {
        // Every participant starts from the same position, otherwise two test
        // sessions cannot be compared.
        seedScenarioFor(registerUser($name, $email, $password));
        attemptLogin($email, $password);

        flash('Je account staat klaar. Je begint op ' . START_SCORE . '.', 'success', 'Welkom bij Draagvlak');
        redirect('index');
    }

    rememberForm(['name' => $name, 'email' => $email], $errors);
    redirect('registreren');
}

$form = takeForm();

/*
 * The check itself returns a key, so the rules stay in src/ and the Dutch
 * sentence stays here.
 */
$errorText = [
    'name_required' => 'Vul je naam in.',
    'email_required' => 'Vul je e-mailadres in.',
    'email_invalid' => 'Dit lijkt geen geldig e-mailadres.',
    'email_taken' => 'Er bestaat al een account met dit adres.',
    'password_short' => sprintf('Kies een wachtwoord van minstens %d tekens.', MIN_PASSWORD_LENGTH),
    'password_mismatch' => 'De twee wachtwoorden zijn niet gelijk.',
];

page('Account maken', ['nav' => 'registreren']);

?>
<section class="auth-card">
    <h1 class="auth-card__title">Account maken</h1>

    <p class="auth-card__intro">
        Je krijgt een startcijfer van <span class="numeric"><?= e(START_SCORE) ?></span>.
        Wat het daarna doet, hangt van je contacten af.
    </p>

    <form class="auth-card__form stack" method="post" action="<?= e(url('registreren')) ?>">
        <?= csrfField() ?>

        <div class="field">
            <label class="field__label" for="name">Naam</label>
            <input
                class="field__input"
                type="text"
                id="name"
                name="name"
                value="<?= e($form['values']['name'] ?? '') ?>"
                autocomplete="name"
                required
                <?= isset($form['errors']['name']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['name'])): ?>
                <p class="field__error"><?= e($errorText[$form['errors']['name']] ?? '') ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="email">E-mailadres</label>
            <input
                class="field__input"
                type="email"
                id="email"
                name="email"
                value="<?= e($form['values']['email'] ?? '') ?>"
                autocomplete="email"
                required
                <?= isset($form['errors']['email']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['email'])): ?>
                <p class="field__error"><?= e($errorText[$form['errors']['email']] ?? '') ?></p>
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
                <p class="field__error"><?= e($errorText[$form['errors']['password']] ?? '') ?></p>
            <?php else: ?>
                <p class="field__hint">Minstens <?= e(MIN_PASSWORD_LENGTH) ?> tekens.</p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label class="field__label" for="passwordRepeat">Wachtwoord herhalen</label>
            <input
                class="field__input"
                type="password"
                id="passwordRepeat"
                name="passwordRepeat"
                autocomplete="new-password"
                required
                <?= isset($form['errors']['repeat']) ? 'aria-invalid="true"' : '' ?>
            >
            <?php if (isset($form['errors']['repeat'])): ?>
                <p class="field__error"><?= e($errorText[$form['errors']['repeat']] ?? '') ?></p>
            <?php endif; ?>
        </div>

        <button class="btn btn--block" type="submit">Account maken</button>
    </form>

    <p class="auth-card__switch">
        Heb je al een account? <a href="<?= e(url('inloggen')) ?>">Inloggen</a>.
    </p>
</section>
