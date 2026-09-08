<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Account;
use Draagvlak\Features\Contacts\Contact;
use Draagvlak\Features\Contacts\ContactCode;
use Draagvlak\Features\Score\ScoreRules;

/**
 * Contacts: your own code, the field to add somebody, and your list.
 *
 * The code comes first because that is where a list starts. Nobody is added for
 * you: somebody has to type your code.
 *
 * @var Account       $account  The visitor, for the code under their name.
 * @var list<Contact> $contacts Everyone still on the list, slowest first.
 */

?>
<section class="section">
    <?php $this->partial('features/contacts/code-card', ['code' => $account->contactCode]); ?>
</section>

<section class="section">
    <h2 class="section__title">Iemand toevoegen</h2>

    <div class="card">
        <form class="stack" method="post" action="<?= $this->e($this->url('contacts')) ?>">
            <?= $this->csrfField() ?>
            <input type="hidden" name="action" value="add">

            <div class="field">
                <label class="field__label" for="code">Code van de ander</label>
                <input
                    class="field__input field__input--code numeric"
                    type="text"
                    id="code"
                    name="code"
                    placeholder="SAM-7QK4"
                    maxlength="<?= $this->e(ContactCode::LENGTH) ?>"
                    autocomplete="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    required
                >
                <p class="field__hint">Hoofdletters of kleine letters maakt niet uit.</p>
            </div>

            <button class="btn btn--block" type="submit">Toevoegen</button>
        </form>
    </div>
</section>

<section class="section">
    <h2 class="section__title">Je lijst</h2>

    <?php if ($contacts === []): ?>
        <p class="contacts__empty">
            Er staat nog niemand in je lijst. Draagvlak rekent met minimaal
            <span class="numeric"><?= $this->e(ScoreRules::MIN_ACTIVE_CONTACTS) ?></span>
            actieve contacten.
        </p>
    <?php else: ?>
        <div class="contacts__list">
            <?php foreach ($contacts as $contact): ?>
                <?php $this->partial('features/contacts/contact-row', ['contact' => $contact]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
