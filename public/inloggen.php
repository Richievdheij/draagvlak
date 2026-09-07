<?php

declare(strict_types=1);

/**
 * Log in.
 *
 * Everything a participant does is tied to their account, so this is the first
 * screen of a session. Wrong attempts are counted; after five the form closes
 * for a while, which stops someone from trying an endless list of passwords.
 */

require __DIR__ . '/../bootstrap.php';

requireGuest();

if (isPost()) {
    $email = (string) input('email', '');
    $password = (string) input('password', '');

    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
        redirect('inloggen');
    }

    if (loginIsLocked()) {
        flash(
            sprintf(
                'Probeer het over %d minuten opnieuw.',
                max(1, (int) ceil(loginLockSecondsLeft() / 60))
            ),
            'danger',
            'Te veel pogingen'
        );

        redirect('inloggen');
    }

    if (attemptLogin($email, $password)) {
        $intended = (string) sessionGet('intendedPage', 'index');
        sessionForget('intendedPage');

        redirect($intended);
    }

    rememberForm(['email' => $email], ['password' => 'credentials_wrong']);

    flash('Dat e-mailadres en wachtwoord horen niet bij elkaar.', 'danger');
    redirect('inloggen');
}

$form = takeForm();

page('Inloggen', ['nav' => 'inloggen']);

?>
<section class="auth-card">
    <h1 class="auth-card__title">Inloggen</h1>

    <p class="auth-card__intro">
        Je draagvlak hoort bij je account. Log in om te zien waar je vandaag staat.
    </p>

    <form class="auth-card__form stack" method="post" action="<?= e(url('inloggen')) ?>">
        <?= csrfField() ?>

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
                autofocus
            >
        </div>

        <div class="field">
            <label class="field__label" for="password">Wachtwoord</label>
            <input
                class="field__input"
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
                <?= isset($form['errors']['password']) ? 'aria-invalid="true"' : '' ?>
            >
        </div>

        <button class="btn btn--block" type="submit">Inloggen</button>
    </form>

    <p class="auth-card__hint">
        Testaccount: <code>sam@draagvlak.test</code> met wachtwoord <code>draagvlak</code>.
    </p>

    <p class="auth-card__switch">
        Nog geen account? <a href="<?= e(url('registreren')) ?>">Maak er een aan</a>.
    </p>
</section>
