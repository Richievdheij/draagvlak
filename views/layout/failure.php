<?php

declare(strict_types=1);

/**
 * What a visitor sees when something breaks and debug is off.
 *
 * A demo with a blank white page is worse than one that says something went
 * wrong, so this screen exists. It stays plain on purpose: it must not need the
 * database, the session or anything else that may be the thing that failed.
 *
 * @var string       $appName     Name of the product.
 * @var string       $locale      Language of everything a visitor reads.
 * @var list<string> $stylesheets Base, layout and components. No feature.
 */

?>
<!DOCTYPE html>
<html lang="<?= $this->e($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Er ging iets mis · <?= $this->e($appName) ?></title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= $this->e($stylesheet) ?>">
    <?php endforeach; ?>
</head>
<body class="screen-failure">
<main class="site-main" id="main">
    <div class="container">
        <div class="card">
            <h1 class="card__title">Er ging iets mis</h1>

            <p class="card__body">
                Draagvlak kon dit scherm niet laden. Probeer het zo nog een keer.
            </p>

            <p class="card__body">
                <a href="<?= $this->e($this->url()) ?>">Terug naar start</a>
            </p>
        </div>
    </div>
</main>
</body>
</html>
