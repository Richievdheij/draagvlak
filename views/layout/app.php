<?php

declare(strict_types=1);

use Draagvlak\Features\Auth\Account;

/**
 * The document around every screen. Add something here and it appears on every
 * screen at once.
 *
 * @var string       $content     Everything the screen printed. Already HTML.
 * @var string       $title       Dutch page title.
 * @var string       $nav         Navigation item to mark as current.
 * @var string       $bodyClass   Extra class on the body, for styling one screen.
 * @var string|null  $description Meta description, or null to leave it out.
 * @var list<string> $stylesheets Every stylesheet this screen needs, in load order.
 * @var string       $appName     Name of the product.
 * @var string       $locale      Language of everything a visitor reads.
 * @var Account|null $account     The visitor, or null when nobody is logged in.
 * @var list<array{key: string, values: array<string, string|int>}> $flashes
 */

?>
<!DOCTYPE html>
<html lang="<?= $this->e($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= $this->e($title) ?> · <?= $this->e($appName) ?></title>
    <?php if ($description !== null): ?>
        <meta name="description" content="<?= $this->e($description) ?>">
    <?php endif; ?>

    <?php /* A meta tag cannot read a custom property, so these two values are
             the only place where a colour from tokens.css is repeated. */ ?>
    <meta name="theme-color" content="#f2f6f3" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f1613" media="(prefers-color-scheme: dark)">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <?php /* Without a version, so the browser reuses the exact file fonts.css
             asks for instead of downloading it twice. */ ?>
    <link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/outfit-latin.woff2" as="font" type="font/woff2" crossorigin>

    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= $this->e($stylesheet) ?>">
    <?php endforeach; ?>

    <script>
        // Runs before the first paint: applies the saved theme so the page never
        // flashes in the wrong one, and tells the CSS that JavaScript is here.
        (function () {
            document.documentElement.classList.add('has-js');

            try {
                var theme = localStorage.getItem('theme');

                if (theme === 'light' || theme === 'dark') {
                    document.documentElement.dataset.theme = theme;
                }
            } catch (error) {
                // Storage can be blocked. The system setting decides then.
            }
        })();
    </script>

    <script type="module" src="<?= $this->e($this->asset('js/app.js')) ?>"></script>
</head>
<body class="<?= $this->e($bodyClass) ?>">
<a class="skip-link" href="#main">Direct naar de inhoud</a>

<?php $this->partial('layout/site-header', ['nav' => $nav, 'account' => $account, 'appName' => $appName]); ?>

<main class="site-main" id="main" tabindex="-1">
    <div class="container">
        <?php $this->partial('components/notices', ['flashes' => $flashes]); ?>

        <?= $content /* Already HTML: the screen escaped its own values. */ ?>
    </div>
</main>

<?php $this->partial('layout/site-footer'); ?>
</body>
</html>
