<?php

declare(strict_types=1);

/**
 * The menu.
 *
 * The list below is the whole navigation: the key is the page file in public/
 * without its extension, the value is the Dutch label. Add a screen, add a line
 * here. The labels live in this partial and not in src/, because everything a
 * visitor reads is Dutch and src/ stays English.
 *
 * @var string $nav Navigation item to mark as current.
 */

$items = [
    'index' => 'Start',
];

?>
<nav class="site-nav" id="site-nav" aria-label="Hoofdmenu">
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
</nav>
