<?php

declare(strict_types=1);

namespace Draagvlak\Features\Scenario;

use Draagvlak\Core\Config;
use Draagvlak\Features\Auth\AccountRepository;

/**
 * The one account the team logs in with, and the scenario content behind it.
 *
 * It is a fixture and not a person: the password is written down in the README,
 * so it may only ever exist on a machine the team controls. app.demo is the
 * switch that keeps it off a server, and "composer db:setup" asks this class
 * before it writes anything.
 */
final readonly class DemoSeeder
{
    /**
     * example.com is reserved by IANA for exactly this, so this address can
     * never belong to a real person and a stray mail can never reach anybody.
     */
    public const string EMAIL = 'test@example.com';

    public const string PASSWORD = 'password';

    /** The participant of the scenario. See docs/01-project-en-scenario.md. */
    public const string NAME = 'Sam Vermeer';

    /**
     * Fixed instead of drawn, so it survives every db:fresh and the README can
     * name it. A participant never reads the word "test" in it, which is the
     * whole reason it is not something like SAM-TEST.
     */
    public const string CONTACT_CODE = 'SAM-2938';

    public function __construct(
        private Config $config,
        private AccountRepository $accounts,
        private Scenario $scenario,
    ) {}

    /** Whether this machine is allowed to have the demo account at all. */
    public function isAllowed(): bool
    {
        return $this->config->get('app.demo', true) === true;
    }

    /**
     * Create the account when it is missing and give it the scenario content.
     * Running it twice changes nothing.
     */
    public function seed(): SeedResult
    {
        $account = $this->accounts->findByEmail(self::EMAIL);
        $created = $account === null;

        if ($account === null) {
            $account = $this->accounts->create(self::NAME, self::EMAIL, self::PASSWORD, self::CONTACT_CODE);
        }

        return new SeedResult($account, $created, $this->scenario->seedFor($account->id));
    }
}
