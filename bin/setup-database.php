<?php

declare(strict_types=1);

use Draagvlak\Core\App;
use Draagvlak\Features\Scenario\DemoSeeder;

/**
 * Create the database, its tables and the demo content.
 *
 *     composer db:setup     create what is missing and leave existing rows alone
 *     composer db:fresh     drop the whole database first and start over
 *
 * The demo account and the scenario behind it come from DemoSeeder, which also
 * decides whether this machine is allowed to have them at all. Somebody who
 * registers starts with an empty list and their own code, the way the app works.
 */

/** @var App $app */
$app = require __DIR__ . '/../bootstrap.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$fresh = in_array('--fresh', $argv, true);
$name = (string) $app->config->get('db.name');

$server = new PDO(
    sprintf(
        'mysql:host=%s;port=%d;charset=utf8mb4',
        (string) $app->config->get('db.host'),
        (int) $app->config->get('db.port'),
    ),
    (string) $app->config->get('db.user'),
    (string) $app->config->get('db.password'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);

if ($fresh) {
    $server->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $name));
    echo "Dropped database {$name}.\n";
}

$server->exec(sprintf(
    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
    $name,
));

echo "Database {$name} is ready.\n";

foreach (readSqlStatements(BASE_PATH . '/database/schema.sql') as $statement) {
    $app->database->connection()->exec($statement);
}

echo "Tables are up to date.\n";

if (!$app->demo->isAllowed()) {
    echo "app.demo is off, so no demo account and no scenario content were written.\n";
    exit;
}

$seeded = $app->demo->seed();

echo $seeded->created
    ? sprintf("Created the demo account %s with the password %s.\n", DemoSeeder::EMAIL, DemoSeeder::PASSWORD)
    : sprintf("The demo account %s was already there.\n", DemoSeeder::EMAIL);

echo sprintf("Its contact code is %s, and it stays that way.\n", $seeded->account->contactCode);

echo $seeded->contactsAdded === 0
    ? "It already has contacts, or data/scenario.json holds none. Use --fresh to start over.\n"
    : sprintf("Added %d contacts with a message each, from data/scenario.json.\n", $seeded->contactsAdded);

echo sprintf("Log in with %s / %s.\n", DemoSeeder::EMAIL, DemoSeeder::PASSWORD);

/**
 * Split an SQL file into statements. Comment lines are dropped first, so a
 * semicolon inside a comment cannot cut a statement in half.
 *
 * @return list<string>
 */
function readSqlStatements(string $file): array
{
    if (!is_file($file)) {
        throw new RuntimeException(sprintf('SQL file is missing: %s', $file));
    }

    $lines = array_filter(
        explode("\n", (string) file_get_contents($file)),
        static fn (string $line): bool => !str_starts_with(trim($line), '--'),
    );

    $statements = array_map('trim', explode(';', implode("\n", $lines)));

    return array_values(array_filter($statements, static fn (string $sql): bool => $sql !== ''));
}
