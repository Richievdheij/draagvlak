<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Account;

/**
 * The bar on top of every screen. The app says who it is, then what you are
 * worth; that order is deliberate.
 *
 * @var string       $nav     Navigation item to mark as current.
 * @var Account|null $account The visitor, or null when nobody is logged in.
 * @var string       $appName Name of the product.
 */

?>
<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="<?= $this->e($this->url()) ?>"><?= $this->e($appName) ?></a>

        <?php if ($account !== null): ?>
            <a class="brand-score" href="<?= $this->e($this->url()) ?>">
                <span class="brand-score__value numeric"><?= $this->e($account->score) ?></span>
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

        <?php $this->partial('layout/site-nav', ['nav' => $nav, 'account' => $account]); ?>
    </div>
</header>
