<?php

declare(strict_types=1);

/**
 * The database: one connection per request, and four ways to use it.
 *
 * Every query runs as a prepared statement with the values passed separately.
 * That is the whole defence against SQL injection, and it is not optional: a
 * value never goes into the SQL string, not even one you typed yourself.
 *
 *     dbAll('SELECT * FROM contacts WHERE user_id = ?', [$userId]);        // right
 *     dbAll("SELECT * FROM contacts WHERE user_id = $userId");             // wrong
 */

/**
 * The connection, opened the first time something asks for it.
 *
 * Errors throw instead of returning false, rows come back as associative
 * arrays, and prepared statements are real ones rather than strings glued
 * together by the driver.
 *
 * @throws RuntimeException When the database cannot be reached.
 */
function db(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        (string) config('db.host'),
        (int) config('db.port'),
        (string) config('db.name')
    );

    try {
        $connection = new PDO($dsn, (string) config('db.user'), (string) config('db.password'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
    } catch (PDOException $exception) {
        throw new RuntimeException(
            'Could not connect to the database. Check config.php and run "composer db:setup".',
            0,
            $exception
        );
    }

    return $connection;
}

/**
 * Run a statement and hand back the statement itself.
 *
 * Use this for INSERT, UPDATE and DELETE; for reading there are the three
 * functions below.
 *
 * @param string            $sql    Query with ? or :name placeholders.
 * @param array<int|string, mixed> $params Values for those placeholders.
 */
function dbRun(string $sql, array $params = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement;
}

/**
 * Every row of a query.
 *
 * @param array<int|string, mixed> $params
 * @return list<array<string, mixed>>
 */
function dbAll(string $sql, array $params = []): array
{
    return dbRun($sql, $params)->fetchAll();
}

/**
 * The first row of a query, or null when there is none.
 *
 * @param array<int|string, mixed> $params
 * @return array<string, mixed>|null
 */
function dbFirst(string $sql, array $params = []): ?array
{
    $row = dbRun($sql, $params)->fetch();

    return $row === false ? null : $row;
}

/**
 * The first column of the first row: a count, a name, an id.
 *
 * @param array<int|string, mixed> $params
 */
function dbValue(string $sql, array $params = []): mixed
{
    $value = dbRun($sql, $params)->fetchColumn();

    return $value === false ? null : $value;
}

/**
 * Insert one row and return its new id.
 *
 * The column names come from your own code and never from a form, so building
 * that part of the statement is safe; the values still go in as parameters.
 *
 * @param string               $table  Table name.
 * @param array<string, mixed> $values Column name to value.
 */
function dbInsert(string $table, array $values): int
{
    $columns = array_keys($values);
    $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );

    dbRun($sql, $values);

    return (int) db()->lastInsertId();
}
