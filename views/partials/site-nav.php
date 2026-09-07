<?php

declare(strict_types=1);

/**
 * The menu, plus the account at the bottom of it.
 *
 * The list below is the whole navigation: the key is the page file in public/
 * without its extension, the value is the Dutch label. Add a screen, add a line
 * here. The labels live in this partial and not in src/, because everything a
 * visitor reads is Dutch and src/ stays English.
 *
 * @var string                   $nav  Navigation item to mark as current.
 * @var array<string, mixed>|null $user The visitor, or null when logged out.
 */

$items = [
    'index' => 'Start',
    'contacten' => 'Contacten',
    'noodcontacten' => 'Noodcontacten',
    'scorebureau' => 'Scorebureau',
    'instellingen' => 'Instellingen',
];

?>
<nav class="site-nav" id="site-nav" aria-label="Hoofdmenu">
    <?php if ($user !== null): ?>
        <ul class="site-nav__list">
            <?php foreach ($items as $page => $label): ?>
                <li>
                    <a
                        class="site-nav__link"
                        href="<?= e(url($page)) ?>"
                        <?= $page === $nav ? 'aria-current="page"' : '' ?>
                    ><?= e($label) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="site-nav__account">
            <span class="site-nav__name">Ingelogd als <?= e($user['name']) ?></span>
            <a class="site-nav__link" href="<?= e(url('uitloggen')) ?>">Uitloggen</a>
        </div>
    <?php else: ?>
        <ul class="site-nav__list">
            <li>
                <a
                    class="site-nav__link"
                    href="<?= e(url('inloggen')) ?>"
                    <?= $nav === 'inloggen' ? 'aria-current="page"' : '' ?>
                >Inloggen</a>
            </li>
            <li>
                <a
                    class="site-nav__link"
                    href="<?= e(url('registreren')) ?>"
                    <?= $nav === 'registreren' ? 'aria-current="page"' : '' ?>
                >Account maken</a>
            </li>
        </ul>
    <?php endif; ?>
</nav>
