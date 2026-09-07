<?php

declare(strict_types=1);

/**
 * Reset between two participants.
 *
 * Empty on purpose. The screen and its stylesheet in assets/css/pages/reset.css
 * are ready; the content is yours to build.
 *
 * Two things already exist to start over: uitloggen.php clears the session, and
 * "composer db:fresh" puts the messages and the scores back to their starting
 * position for everybody.
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

page('Opnieuw beginnen');

?>
<section class="section">
    <h1>Opnieuw beginnen</h1>
</section>
