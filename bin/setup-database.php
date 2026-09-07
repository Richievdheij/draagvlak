<?php

declare(strict_types=1);

/**
 * Create the database, its tables and the demo content.
 *
 *     composer db:setup     create what is missing and leave existing rows alone
 *     composer db:fresh     drop the whole database first and start over
 *
 * Run this once after cloning, and run db:fresh between two test sessions to
 * put every participant back on the same starting position.
 */

require __DIR__ . '/../bootstrap.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$fresh = in_array('--fresh', $argv, true);
$name = (string) config('db.name');

$server = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=utf8mb4', (string) config('db.host'), (int) config('db.port')),
    (string) config('db.user'),
    (string) config('db.password'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

if ($fresh) {
    $server->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $name));
    echo "Dropped database {$name}.\n";
}

$server->exec(sprintf(
    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
    $name
));

echo "Database {$name} is ready.\n";

foreach (readSqlStatements(BASE_PATH . '/database/schema.sql') as $statement) {
    db()->exec($statement);
}

echo "Tables are up to date.\n";

$email = 'sam@draagvlak.test';
$password = 'draagvlak';

$userId = (int) (dbValue('SELECT id FROM users WHERE email = ?', [$email]) ?? 0);

if ($userId === 0) {
    $userId = dbInsert('users', [
        'name' => 'Sam Vermeer',
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'score' => START_SCORE,
    ]);

    echo "Created the demo account {$email} with the password {$password}.\n";
} else {
    echo "The demo account {$email} already exists.\n";
}

if (activeContactCount($userId) > 0) {
    echo "Contacts and messages are already there. Use --fresh to start over.\n";
    exit;
}

$added = seedScenarioFor($userId);

if ($added === 0) {
    echo "data/scenario.json holds no contacts, so the app starts with an empty inbox.\n";
} else {
    echo "Added {$added} contacts with a message each, from data/scenario.json.\n";
}
echo "Open http://localhost:8000 and log in with {$email} / {$password}.\n";

/**
 * Split an SQL file into statements it can run one by one.
 *
 * Comment lines are dropped first, so a semicolon inside a comment cannot cut a
 * statement in half.
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
        static fn (string $line): bool => !str_starts_with(trim($line), '--')
    );

    $statements = array_map('trim', explode(';', implode("\n", $lines)));

    return array_values(array_filter($statements, static fn (string $sql): bool => $sql !== ''));
}
