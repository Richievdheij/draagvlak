<?php

declare(strict_types=1);

namespace Draagvlak\Core\Data;

use Draagvlak\Core\Paths;
use JsonException;
use RuntimeException;

/**
 * Reading the JSON files in data/. Those are the script of the prototype, not
 * storage: the team edits them between two sessions. Anything a visitor
 * produces goes in the database.
 */
final class JsonStore
{
    /** @var array<string, array<mixed>> */
    private array $cache = [];

    public function __construct(private readonly Paths $paths) {}

    /**
     * @param string $name File name without extension, for example 'scenario'.
     *
     * @return array<mixed>
     *
     * @throws RuntimeException When the file is missing or holds no array.
     * @throws JsonException When the file contains broken JSON.
     */
    public function load(string $name): array
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $file = $this->path($name);

        if (!is_file($file)) {
            throw new RuntimeException(\sprintf('Data file "%s" does not exist: %s', $name, $file));
        }

        $decoded = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        if (!\is_array($decoded)) {
            throw new RuntimeException(\sprintf('Data file "%s" does not contain a JSON object or array.', $name));
        }

        return $this->cache[$name] = $decoded;
    }

    /**
     * The name comes from code, but checking it keeps a future query parameter
     * from reaching outside data/.
     */
    private function path(string $name): string
    {
        if (preg_match('/^[a-z0-9-]+$/', $name) !== 1) {
            throw new RuntimeException(\sprintf('Invalid name for a data file: "%s".', $name));
        }

        return $this->paths->data . '/' . $name . '.json';
    }
}
