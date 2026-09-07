<?php

declare(strict_types=1);

/**
 * The bar on top of every screen.
 *
 * Shows the name of the system and, once someone is logged in, their number
 * right next to it. That order is deliberate: the app says who it is, then what
 * you are worth.
 *
 * @var string $nav Navigation item to mark as current.
 */

$headerUser = currentUser();

?>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?= e(url()) ?>"><?= e(APP_NAME) ?></a>

        <?php if ($headerUser !== null): ?>
            <a class="brand-score" href="<?= e(url()) ?>">
                <span class="brand-score__value numeric"><?= e((int) $headerUser['score']) ?></span>
                <span>draagvlak</span>
            </a>
        <?php endif; ?>

        <button
            class="nav-toggle"
            type="button"
            data-component="nav-toggle"
            aria-controls="site-nav"
            aria-expanded="false"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
            </svg>
            <span class="visually-hidden">Menu</span>
        </button>

        <?php partial('site-nav', ['nav' => $nav, 'user' => $headerUser]); ?>
    </div>
</header>
