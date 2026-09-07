<?php

declare(strict_types=1);

/**
 * Home: the starting point of the prototype.
 *
 * Doubles as the example every other screen is copied from: it shows the four
 * steps a page takes (load, handle the form, set the page, print markup) and
 * uses the components that are already in the design system.
 */

require __DIR__ . '/../bootstrap.php';

if (isPost()) {
    $name = input('name', '');

    if (!isValidCsrf()) {
        flash('Het formulier is verlopen. Probeer het opnieuw.', 'danger');
    } elseif ($name === '') {
        flash('Vul eerst een naam in.', 'danger');
    } else {
        flash(sprintf('Genoteerd, %s. Er is niets opgeslagen.', $name), 'success');
    }

    redirect('index');
}

$contacts = loadJson('contacts');

$skeleton = <<<'PHP'
<?php

declare(strict_types=1);

/**
 * One sentence saying what this screen is for.
 */

require __DIR__ . '/../bootstrap.php';

$contacts = loadJson('contacts');

page('Contacten');

?>
<section class="section">
    <h1>Contacten</h1>

    <?php foreach ($contacts as $contact): ?>
        <p><?= e($contact['name']) ?></p>
    <?php endforeach; ?>
</section>
PHP;

page('Start', ['description' => 'De opzet van het Draagvlak-prototype: een PHP-template met een eigen design system.']);

?>
<section class="hero">
    <p class="badge">Startpunt</p>

    <h1 class="hero__title">Een lege opzet die al werkt</h1>

    <p class="hero__intro">
        Dit is de basis van Draagvlak: gewone PHP-pagina's, een eigen design system en
        een layout die op een telefoon net zo goed staat als op een laptop. Er staat nog
        geen scherm in. Die bouw jij.
    </p>

    <div class="hero__actions">
        <a class="btn" href="#nieuw-scherm">Zo maak je een scherm</a>
        <a class="btn btn--ghost" href="#formulier">Een formulier dat werkt</a>
    </div>
</section>

<section class="section" id="nieuw-scherm">
    <div class="section__header">
        <h2>Een scherm is één bestand</h2>

        <p class="prose text-muted">
            Zet een bestand in <code>public/</code>, geef het een Nederlandse naam, en je
            kunt beginnen. Je hoeft niets te importeren: <code>bootstrap.php</code> laadt
            alles uit <code>src/</code> voor je in, en de layout eromheen wordt vanzelf
            geprint zodra de pagina klaar is.
        </p>
    </div>

    <pre class="code-sample"><code><?= e($skeleton) ?></code></pre>
</section>

<section class="section">
    <div class="section__header">
        <h2>Wat er al klaarstaat</h2>
    </div>

    <div class="card-grid">
        <article class="card">
            <h3 class="card__title">Geen imports</h3>

            <p class="card__body">
                <code>e()</code>, <code>page()</code>, <code>partial()</code>,
                <code>flash()</code>, <code>redirect()</code> en <code>loadJson()</code>
                bestaan op elke pagina. Nieuw bestand in <code>src/</code> erbij? Dan doet
                die het meteen, zonder <code>composer dump-autoload</code>.
            </p>
        </article>

        <article class="card">
            <h3 class="card__title">Eén plek voor de vormgeving</h3>

            <p class="card__body">
                Kleuren, maten, fonts en hoeken staan in <code>tokens.css</code>. Outfit
                voor koppen, Inter voor alles wat je leest. Beide staan in de repo, dus ze
                werken ook zonder internet.
            </p>
        </article>

        <article class="card">
            <h3 class="card__title">Mobiel eerst, licht en donker</h3>

            <p class="card__body">
                De layout is gebouwd voor een telefoon en groeit mee naar een laptop. Het
                thema volgt je systeem; onderaan zet je het zelf vast.
            </p>
        </article>

        <article class="card">
            <h3 class="card__title">Data uit JSON</h3>

            <p class="card__body">
                In plaats van een database staan er JSON-bestanden in <code>data/</code>.
                <code>contacts.json</code> bevat er nu
                <span class="numeric"><?= e(count($contacts)) ?></span>.
            </p>
        </article>
    </div>
</section>

<section class="section" id="formulier">
    <div class="section__header">
        <h2>Een formulier dat werkt</h2>

        <p class="prose text-muted">
            Versturen gaat met POST, het formulier is beveiligd met een token, en na
            afloop volgt een redirect met een melding. Zo levert vernieuwen nooit twee
            keer dezelfde inzending op.
        </p>
    </div>

    <form class="stack" method="post" action="<?= e(url('index')) ?>">
        <?= csrfField() ?>

        <div class="field">
            <label class="field__label" for="name">Je naam</label>
            <input class="field__input" type="text" id="name" name="name" autocomplete="name" required>
            <p class="field__hint">Wordt nergens bewaard. Je krijgt alleen een melding terug.</p>
        </div>

        <div>
            <button class="btn" type="submit">Versturen</button>
        </div>
    </form>
</section>
