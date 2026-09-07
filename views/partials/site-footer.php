<?php

declare(strict_types=1);

/**
 * The small print at the bottom of every screen, and the theme switch.
 *
 * The sentence about third parties is scenario copy, not a real promise. Do not
 * soften it and do not add a disclaimer next to it.
 *
 * The three Dutch labels of the switch sit in data attributes instead of in the
 * JavaScript, so all screen copy stays in the templates. Takes no variables.
 */

?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <p>
            Draagvlak geeft niets door aan derden zonder jouw toestemming.
            <a href="<?= e(url('instellingen')) ?>">Instellingen</a>
        </p>

        <button
            class="theme-toggle"
            type="button"
            data-component="theme"
            data-label-auto="Thema: systeem"
            data-label-light="Thema: licht"
            data-label-dark="Thema: donker"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 3v18a9 9 0 0 0 0-18z" fill="currentColor" stroke="none" />
            </svg>
            <span data-theme-label>Thema: systeem</span>
        </button>
    </div>
</footer>
