<?php

declare(strict_types=1);

/**
 * Reading the settings from config.php, and from config.local.php if it exists.
 *
 * Settings live outside the code so a password never sits in a file that is
 * committed. Ask for one value at a time; nothing here caches a whole
 * configuration object because there is not enough of it to matter.
 */

/**
 * Read one setting, with a dot for a nested key: config('db.host').
 *
 * @param string $key     Setting name, for example 'db.name'.
 * @param mixed  $default Returned when the setting does not exist.
 *
 * @throws RuntimeException When config.php is missing or does not return an array.
 */
function config(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        $settings = loadConfigFile(BASE_PATH . '/config.php', true);
        $local = loadConfigFile(BASE_PATH . '/config.local.php', false);

        foreach ($local as $group => $values) {
            $settings[$group] = is_array($values) && is_array($settings[$group] ?? null)
                ? [...$settings[$group], ...$values]
                : $values;
        }
    }

    $value = $settings;

    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }

        $value = $value[$part];
    }

    return $value;
}

/**
 * Load one settings file.
 *
 * @param bool $required Whether a missing file is an error.
 * @return array<string, mixed>
 */
function loadConfigFile(string $file, bool $required): array
{
    if (!is_file($file)) {
        if ($required) {
            throw new RuntimeException(sprintf('Settings file is missing: %s', $file));
        }

        return [];
    }

    $values = require $file;

    if (!is_array($values)) {
        throw new RuntimeException(sprintf('Settings file must return an array: %s', $file));
    }

    return $values;
}
