<?php

declare(strict_types=1);

namespace Draagvlak\Core;

use RuntimeException;

/**
 * Settings from config.php, with config.local.php layered on top so a password
 * never has to be committed.
 */
final class Config
{
    /** @var array<string, mixed> */
    private array $settings;

    public function __construct(Paths $paths)
    {
        $this->settings = $this->load($paths->root . '/config.php', true);

        foreach ($this->load($paths->root . '/config.local.php', false) as $group => $values) {
            $this->settings[$group] = \is_array($values) && \is_array($this->settings[$group] ?? null)
                ? [...$this->settings[$group], ...$values]
                : $values;
        }
    }

    /**
     * One setting, with a dot for a nested key: get('db.host').
     *
     * @param mixed $default Returned when the setting does not exist.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->settings;

        foreach (explode('.', $key) as $part) {
            if (!\is_array($value) || !\array_key_exists($part, $value)) {
                return $default;
            }

            $value = $value[$part];
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException When a required file is missing or returns something else.
     */
    private function load(string $file, bool $required): array
    {
        if (!is_file($file)) {
            if ($required) {
                throw new RuntimeException(\sprintf('Settings file is missing: %s', $file));
            }

            return [];
        }

        $values = require $file;

        if (!\is_array($values)) {
            throw new RuntimeException(\sprintf('Settings file must return an array: %s', $file));
        }

        return $values;
    }
}
