<?php

declare(strict_types=1);

/**
 * Header on every page: the brand and the menu.
 *
 * @var string $nav Navigation item to mark as current.
 */

?>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?= e(url()) ?>">
            <span class="brand__mark" aria-hidden="true">D</span>
            <span class="brand__name"><?= e(APP_NAME) ?></span>
        </a>

        <button
            class="icon-btn nav-toggle"
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

        <?php partial('site-nav', ['nav' => $nav]); ?>
    </div>
</header>
