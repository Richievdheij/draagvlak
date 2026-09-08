<?php

declare(strict_types=1);

namespace Draagvlak\Core\View;

/**
 * Turning a value into HTML that cannot break the page. A template uses the
 * shorter View::e() and View::attributes(), which end up here.
 */
final class Html
{
    /**
     * Escape a value before printing it. Anything that is not a literal in the
     * template has to go through this.
     */
    public static function e(string|int|float|bool|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * HTML attributes, escaped. True prints the attribute on its own, false and
     * null drop it.
     *
     * @param array<string, string|int|bool|null> $attributes
     */
    public static function attributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            $parts[] = $value === true ? self::e($name) : \sprintf('%s="%s"', self::e($name), self::e($value));
        }

        return implode(' ', $parts);
    }
}
