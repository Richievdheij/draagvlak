<?php

declare(strict_types=1);

namespace Draagvlak\Features\Scenario;

use Draagvlak\Features\Auth\Account;

/**
 * What DemoSeeder did, so the command line script can say it out loud.
 */
final readonly class SeedResult
{
    /**
     * @param bool $created        False when the account was already there.
     * @param int  $contactsAdded  Zero when it already had contacts.
     */
    public function __construct(
        public Account $account,
        public bool $created,
        public int $contactsAdded,
    ) {}
}
