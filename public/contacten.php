<?php

declare(strict_types=1);

/**
 * Contacts: everyone on your list, the slowest first and in the quietest state.
 *
 * Empty on purpose. The screen, its stylesheet in assets/css/pages/contacten.css
 * and its place in the menu are ready; the content is yours to build. Read the
 * people out of the database with activeContacts(currentUserId()).
 */

require __DIR__ . '/../bootstrap.php';

requireLogin();

page('Contacten');

?>
<section class="section">
    <h1>Contacten</h1>
</section>
