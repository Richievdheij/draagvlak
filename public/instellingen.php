<?php

declare(strict_types=1);

/**
 * Settings: what you can switch off, and what only looks like you can.
 *
 * Empty on purpose. The screen and its stylesheet in
 * assets/css/pages/instellingen.css are ready; the content is yours to build.
 * The toggle that hides your badge does not work and is not available in your
 * region, and the opt-out sets you to zero. That is the scenario, not a bug.
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

page('Instellingen');

?>
<section class="section">
    <h1>Instellingen</h1>
</section>
