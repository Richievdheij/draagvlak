<?php

declare(strict_types=1);

use Draagvlak\Core\View\Format;
use Draagvlak\Features\Contacts\Contact;
use Draagvlak\Features\Score\ScoreRules;

/**
 * One person on the list, with their number and the way out next to it.
 *
 * The way out is the quietest thing on the row. That is not sloppiness: taking
 * somebody off your list is the choice the app would rather you did not make.
 *
 * @var Contact $contact One person from the list.
 */

?>
<article class="contact-row<?= $contact->isQuiet() ? ' contact-row--quiet' : '' ?>">
    <span class="avatar<?= $contact->isQuiet() ? ' avatar--quiet' : '' ?>" aria-hidden="true">
        <?= $this->e(Format::initial($contact->name)) ?>
    </span>

    <span class="contact-row__who">
        <span class="contact-row__name"><?= $this->e($contact->name) ?></span>

        <span class="contact-row__meta">
            <?php if ($contact->relation !== ''): ?>
                <?= $this->e($contact->relation) ?>
            <?php elseif ($contact->isLinked()): ?>
                Toegevoegd met een code
            <?php else: ?>
                Geen relatie ingevuld
            <?php endif; ?>

            <?php if ($contact->isQuiet()): ?>
                · staat onder de <?= $this->e(ScoreRules::CONTACT_THRESHOLD) ?>
            <?php endif; ?>
        </span>
    </span>

    <span class="contact-row__score numeric">
        <?= $this->e($contact->score) ?>
        <small>draagvlak</small>
    </span>

    <form class="contact-row__remove" method="post" action="<?= $this->e($this->url('contacts')) ?>">
        <?= $this->csrfField() ?>
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="contactId" value="<?= $this->e($contact->id) ?>">

        <button class="btn btn--quiet btn--small" type="submit">Verwijderen</button>
    </form>
</article>
