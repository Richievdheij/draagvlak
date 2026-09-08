<?php

declare(strict_types=1);

namespace Draagvlak\Features\Settings\Pages;

use Draagvlak\Core\Page;

/**
 * Settings: what you can switch off, and what only looks like you can.
 *
 * Empty on purpose. The class, its template in
 * views/features/settings/settings.php and its stylesheet in
 * assets/css/features/settings/ are ready; the content is yours to build.
 * The toggle that hides your badge does not work and is not available in your
 * region, and the opt-out sets you to zero. That is the scenario, not a bug.
 */
final class SettingsPage extends Page
{
    protected function title(): string
    {
        return 'Instellingen';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['account' => $this->app->auth->user()];
    }
}
