<?php

declare(strict_types=1);

/**
 * The login form.
 *
 * Nothing here says which accounts exist: a wrong address and a wrong password
 * give the same answer, and the demo account is written down in the README
 * instead of on the screen a participant sees.
 *
 * @var array{values: array<string, string>, errors: array<string, string>} $form
 */

?>
<section class="auth-card">
    <h1 class="auth-card__title">Inloggen</h1>

    <p class="auth-card__intro">
        Je draagvlak hoort bij je account. Log in om te zien waar je vandaag staat.
    </p>

    <form class="auth-card__form stack" method="post" action="<?= $this->e($this->url('login')) ?>">
        <?= $this->csrfField() ?>

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

    <p class="auth-card__switch">
        Nog geen account? <a href="<?= $this->e($this->url('register')) ?>">Maak er een aan</a>.
    </p>
</section>
