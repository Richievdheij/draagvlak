<?php

declare(strict_types=1);

/**
 * The check tool: the screen a landlord sees about you.
 *
 * Empty on purpose. The screen and its stylesheet in
 * assets/css/pages/check.css are ready; the content is yours to build.
 * Candidate 3 of 12, a number, a threshold and no reason. Illness and caring
 * for someone are invisible to whoever is judging.
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

page('Check');

?>
<section class="section">
    <h1>Check</h1>
</section>
