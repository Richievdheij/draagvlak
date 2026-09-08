<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Account;

/**
 * The menu, plus the account at the bottom of it.
 *
 * The list below is the whole navigation: the key is the screen name, the value
 * is the Dutch label. Add a screen, add a line here.
 *
 * @var string       $nav     Navigation item to mark as current.
 * @var Account|null $account The visitor, or null when nobody is logged in.
 */

$items = $account !== null
    ? [
        'home' => 'Start',
        'contacts' => 'Contacten',
        'emergency-contacts' => 'Noodcontacten',
        'score-bureau' => 'Scorebureau',
        'settings' => 'Instellingen',
    ]
    : [
        'login' => 'Inloggen',
        'register' => 'Account maken',
    ];

?>
<nav class="site-nav" id="site-nav" aria-label="Hoofdmenu">
    <ul class="site-nav__list">
        <?php foreach ($items as $screen => $label): ?>
            <li>
                <a
                    class="site-nav__link"
                    href="<?= $this->e($this->url($screen)) ?>"
                    <?= $screen === $nav ? 'aria-current="page"' : '' ?>
                ><?= $this->e($label) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($account !== null): ?>
        <div class="site-nav__account">
            <span class="site-nav__name">Ingelogd als <?= $this->e($account->name) ?></span>
            <a class="site-nav__link" href="<?= $this->e($this->url('logout')) ?>">Uitloggen</a>
        </div>
    <?php endif; ?>
</nav>
