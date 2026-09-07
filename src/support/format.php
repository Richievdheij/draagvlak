<?php

declare(strict_types=1);

/**
 * Turning numbers into something a screen can print.
 *
 * These functions return digits and never words: the Dutch sentence around them
 * is written in the page or the partial, because that is where screen copy
 * belongs.
 */

/**
 * Split a number of seconds into days, hours, minutes and seconds.
 *
 * The screen decides which parts it shows. Waiting three days does not need the
 * seconds, and waiting eleven seconds does not need the days.
 *
 * @return array{days: int, hours: int, minutes: int, seconds: int}
 */
function timeParts(int $seconds): array
{
    $seconds = max(0, $seconds);

    return [
        'days' => intdiv($seconds, 86400),
        'hours' => intdiv($seconds % 86400, 3600),
        'minutes' => intdiv($seconds % 3600, 60),
        'seconds' => $seconds % 60,
    ];
}

/**
 * A countdown as mm:ss, for example '00:07'.
 */
function formatCountdown(int $seconds): string
{
    $seconds = max(0, $seconds);

    return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
}

/**
 * An amount in euro cents as a Dutch price, for example '4,99'.
 *
 * The comma is a notation and not a word, so it can live here.
 */
function formatPrice(int $cents): string
{
    return number_format($cents / 100, 2, ',', '.');
}

/**
 * First letter of a name, for the placeholder avatar.
 *
 * Needs mbstring so a name starting with an accented character is not cut in
 * half; the extension is declared in composer.json.
 */
function initial(string $name): string
{
    return mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8');
}
