<?php

declare(strict_types=1);

namespace Draagvlak\Core\Http;

use Draagvlak\Core\View\Url;

/**
 * Leaving a screen without printing it. A screen that changes something ends
 * here, so refreshing the result never submits the form again.
 */
final class Response
{
    /**
     * @param string $target Screen name, or a path starting with '/'.
     * @param array<string, string|int> $query Only used with a screen name.
     */
    public static function redirect(string $target = 'home', array $query = []): never
    {
        $location = str_starts_with($target, '/') ? $target : Url::to($target, $query);

        header('Location: ' . $location, true, 303);

        exit;
    }
}
