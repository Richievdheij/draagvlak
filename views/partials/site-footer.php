<?php

declare(strict_types=1);

/**
 * Footer on every page: what this is, and the theme switch.
 *
 * The three Dutch labels of the switch sit in data attributes instead of in the
 * JavaScript, so all screen copy stays in the templates. Takes no variables.
 */

?>
<footer class="site-footer">
    <div class="container site-footer__inner">
        <p><?= e(APP_NAME) ?> — prototype, geen dienst die bestaat.</p>

        <button
            class="btn btn--ghost btn--small theme-toggle"
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
