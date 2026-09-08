<?php

declare(strict_types=1);

namespace Draagvlak\Core\Data;

use Draagvlak\Core\Config;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * One connection per request, and five ways to use it.
 *
 * Every query runs as a prepared statement with the values passed separately.
 * A value never goes into the SQL string, not even one you typed yourself.
 */
final class Database
{
    private ?PDO $connection = null;

    public function __construct(private readonly Config $config) {}

    /** @throws RuntimeException When the database cannot be reached. */
    public function connection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = \sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            (string) $this->config->get('db.host'),
            (int) $this->config->get('db.port'),
            (string) $this->config->get('db.name'),
        );

        try {
            $this->connection = new PDO(
                $dsn,
                (string) $this->config->get('db.user'),
                (string) $this->config->get('db.password'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ],
            );
        } catch (PDOException $exception) {
            throw new RuntimeException(
                \sprintf(
                    'Could not reach MySQL on %s:%d. Start it, check config.php or config.local.php, '
                    . 'and run "composer db:setup".',
                    (string) $this->config->get('db.host'),
                    (int) $this->config->get('db.port'),
                ),
                0,
                $exception,
            );
        }

        return $this->connection;
    }

    /**
     * For INSERT, UPDATE and DELETE. Reading has the three methods below.
     *
     * @param string                   $sql    Query with ? or :name placeholders.
     * @param array<int|string, mixed> $params Values for those placeholders.
     */
    public function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->connection()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /**
     * @param array<int|string, mixed> $params
     *
     * @return list<array<string, mixed>>
     */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * @param array<int|string, mixed> $params
     *
     * @return array<string, mixed>|null
     */
    public function first(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * The first column of the first row: a count, a name, an id.
     *
     * @param array<int|string, mixed> $params
     */
    public function value(string $sql, array $params = []): mixed
    {
        $value = $this->run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    /**
     * Insert one row and return its new id. The column names come from your own
     * code and never from a form; the values still go in as parameters.
     *
     * @param array<string, mixed> $values Column name to value.
     */
    public function insert(string $table, array $values): int
    {
        $columns = array_keys($values);
        $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);

        $sql = \sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders),
        );

        $this->run($sql, $values);

        return (int) $this->connection()->lastInsertId();
    }
}
