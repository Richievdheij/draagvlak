<?php

declare(strict_types=1);

/**
 * The code somebody shares so another account can add them.
 *
 * The copy button is printed hidden and shown by its module, so a browser
 * without JavaScript never offers a button that would do nothing.
 *
 * @var string $code The visitor's own contact code.
 */

?>
<div class="code-card">
    <p class="code-card__label">Jouw code</p>

    <p class="code-card__value numeric" data-component="copy" data-copy-value="<?= $this->e($code) ?>">
        <span data-copy-text><?= $this->e($code) ?></span>

        <button
            class="code-card__copy"
            type="button"
            data-copy-button
            data-copied-label="Gekopieerd"
            hidden
        >
            <span data-copy-label>Kopieer</span>
        </button>
    </p>

    <p class="code-card__note">
        Geef deze code aan iemand die jou aan zijn lijst mag toevoegen. Zodra hij hem
        invult, staan jullie bij elkaar in de lijst en zien jullie elkaars draagvlak.
    </p>
</div>
