<?php

declare(strict_types=1);

namespace Draagvlak\Features\Reset\Pages;

use Draagvlak\Core\Page;

/**
 * Start over between two participants.
 *
 * Empty on purpose. The class, its template in views/features/reset/reset.php
 * and its stylesheet in assets/css/features/reset/ are ready; the content is
 * yours to build.
 *
 * Two things already exist to start over: logout.php clears the session, and
 * "composer db:fresh" puts everybody back on the starting position.
 */
final class ResetPage extends Page
{
    protected function title(): string
    {
        return 'Opnieuw beginnen';
    }
}
