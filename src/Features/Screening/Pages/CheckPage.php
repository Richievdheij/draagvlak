<?php

declare(strict_types=1);

namespace Draagvlak\Features\Screening\Pages;

use Draagvlak\Core\Page;

/**
 * The check tool: the screen a landlord sees about you.
 *
 * Empty on purpose. The class, its template in
 * views/features/screening/check.php and its stylesheet in
 * assets/css/features/screening/ are ready; the content is yours to build.
 * Candidate 3 of 12, a number, a threshold and no reason. Illness and caring
 * for somebody are invisible to whoever is judging.
 */
final class CheckPage extends Page
{
    protected function title(): string
    {
        return 'Check';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['account' => $this->app->auth->user()];
    }
}
