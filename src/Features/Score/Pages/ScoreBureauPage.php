<?php

declare(strict_types=1);

namespace Draagvlak\Features\Score\Pages;

use Draagvlak\Core\Page;

/**
 * The score bureau: paid maintenance of your number.
 *
 * Empty on purpose. The class, its template in
 * views/features/score/score-bureau.php and its stylesheet in
 * assets/css/features/score/ are ready; the content is yours to build.
 * Whoever needs it most has the least money, which is the point of the screen.
 */
final class ScoreBureauPage extends Page
{
    protected function title(): string
    {
        return 'Scorebureau';
    }

    /** @return array<string, mixed> */
    protected function data(): array
    {
        return ['account' => $this->app->auth->user()];
    }
}
