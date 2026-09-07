<?php

declare(strict_types=1);

/**
 * The document around every screen.
 *
 * Printed by finishPage() once the screen is done, which is why a screen file
 * contains nothing but its own markup. Add something here and it appears on
 * every page at once.
 *
 * @var string      $content     Everything the screen printed. Already HTML.
 * @var string      $title       Dutch page title.
 * @var string      $nav         Navigation item to mark as current.
 * @var string      $bodyClass   Extra class on the body, for styling one screen.
 * @var string|null $description Meta description, or null to leave it out.
 */

?>
<!DOCTYPE html>
<html lang="<?= e(APP_LOCALE) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($title) ?> · <?= e(APP_NAME) ?></title>
    <?php if ($description !== null): ?>
        <meta name="description" content="<?= e($description) ?>">
    <?php endif; ?>

    <?php /* The browser chrome on a phone follows the theme. A meta tag cannot
             read a custom property, so these two values are the only place
             where a colour from tokens.css is repeated by hand. */ ?>
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0b1418" media="(prefers-color-scheme: dark)">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <?php /* Without a version in the URL, so the browser reuses the exact file
             fonts.css asks for instead of downloading it twice. */ ?>
    <link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/outfit-latin.woff2" as="font" type="font/woff2" crossorigin>

    <?php foreach (stylesheets() as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e($stylesheet) ?>">
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

    <script type="module" src="<?= e(asset('js/app.js')) ?>"></script>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Direct naar de inhoud</a>

<?php partial('site-header', ['nav' => $nav]); ?>

<main class="site-main" id="main" tabindex="-1">
    <div class="container">
        <?php partial('flashes'); ?>

        <?= $content /* Already HTML: the screen escaped its own values. */ ?>
    </div>
</main>

<?php partial('site-footer'); ?>
</body>
</html>
