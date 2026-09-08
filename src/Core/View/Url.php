<?php

declare(strict_types=1);

namespace Draagvlak\Core\View;

/**
 * Links to the screens in public/. There is no router: a screen is a file and
 * its name is its URL, so this class is the only place that knows about the
 * extension.
 */
final class Url
{
    /** The one screen whose file name is not its URL, because a web server opens it by default. */
    private const string HOME = 'home';

    /**
     * @param string $screen Screen name, for example 'contacts'.
     * @param array<string, string|int> $query Optional query string parameters.
     */
    public static function to(string $screen = self::HOME, array $query = []): string
    {
        $path = $screen === self::HOME ? '/' : '/' . trim($screen, '/') . '.php';

        return $query === [] ? $path : $path . '?' . http_build_query($query);
    }
}
