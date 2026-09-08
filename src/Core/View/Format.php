<?php

declare(strict_types=1);

namespace Draagvlak\Core\View;

/**
 * Numbers, written the way a screen prints them. Digits only: the Dutch
 * sentence around them belongs in the template.
 */
final class Format
{
    /**
     * The screen decides which parts it shows.
     *
     * @return array{days: int, hours: int, minutes: int, seconds: int}
     */
    public static function timeParts(int $seconds): array
    {
        $seconds = max(0, $seconds);

        return [
            'days' => intdiv($seconds, 86400),
            'hours' => intdiv($seconds % 86400, 3600),
            'minutes' => intdiv($seconds % 3600, 60),
            'seconds' => $seconds % 60,
        ];
    }

    /** A countdown as mm:ss, for example '00:07'. */
    public static function countdown(int $seconds): string
    {
        $seconds = max(0, $seconds);

        return \sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    /** Euro cents as a Dutch price, for example '4,99'. */
    public static function price(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.');
    }

    /** First letter of a name, for the placeholder avatar. */
    public static function initial(string $name): string
    {
        return mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8');
    }
}
