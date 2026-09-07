<?php

declare(strict_types=1);

/**
 * Reading and writing the JSON files in data/, which stand in for a database.
 *
 * Content lives in data/ so it can be changed between sessions without touching
 * code. Nothing in here knows what the files contain: that belongs to the
 * helper that asks for them.
 */

/**
 * Read a JSON file from data/ as an array.
 *
 * The result is kept in memory for the rest of the request, because several
 * parts of one page tend to ask for the same file.
 *
 * @param string $name File name without extension, for example 'contacts'.
 * @return array<mixed>
 *
 * @throws RuntimeException When the file is missing or does not contain JSON.
 */
function loadJson(string $name): array
{
    static $cache = [];

    if (isset($cache[$name])) {
        return $cache[$name];
    }

    $file = jsonPath($name);

    if (!is_file($file)) {
        throw new RuntimeException(sprintf('Data file "%s" does not exist: %s', $name, $file));
    }

    $decoded = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

    if (!is_array($decoded)) {
        throw new RuntimeException(sprintf('Data file "%s" does not contain a JSON object or array.', $name));
    }

    return $cache[$name] = $decoded;
}

/**
 * Write an array back to a JSON file in data/.
 *
 * Only for content the team maintains, never for what a visitor submits: these
 * files are the script of the prototype, and rewriting one during a session
 * changes what the next participant sees. Keep per-visitor state in the session.
 *
 * @param string       $name File name without extension.
 * @param array<mixed> $data Anything json_encode() can turn into an object or array.
 *
 * @throws RuntimeException When the file cannot be written.
 */
function saveJson(string $name, array $data): void
{
    $file = jsonPath($name);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    if (file_put_contents($file, $json . "\n", LOCK_EX) === false) {
        throw new RuntimeException(sprintf('Data file "%s" cannot be written: %s', $name, $file));
    }
}

/**
 * Full path of a data file, with the name checked.
 *
 * The name comes from code, but checking it keeps a future query parameter from
 * reaching outside data/.
 */
function jsonPath(string $name): string
{
    if (preg_match('/^[a-z0-9-]+$/', $name) !== 1) {
        throw new RuntimeException(sprintf('Invalid name for a data file: "%s".', $name));
    }

    return DATA_PATH . '/' . $name . '.json';
}
