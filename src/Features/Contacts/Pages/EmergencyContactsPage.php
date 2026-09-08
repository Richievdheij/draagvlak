<?php

declare(strict_types=1);

namespace Draagvlak\Features\Contacts\Pages;

use Draagvlak\Core\Page;

/**
 * Emergency contacts: who has you on their list, and what that is worth.
 *
 * Empty on purpose. The class, its template in
 * views/features/contacts/emergency-contacts.php and its stylesheet in
 * assets/css/features/contacts/ are ready; the content is yours to build.
 * A Contact carries isEmergencyContact and listsYou for this screen.
 */
final class EmergencyContactsPage extends Page
{
    protected function title(): string
    {
        return 'Noodcontacten';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['contacts' => $this->app->contacts->emergency($this->app->auth->id())];
    }
}
