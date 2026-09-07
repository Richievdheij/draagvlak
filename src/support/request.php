<?php

declare(strict_types=1);

/**
 * Reading the request and leaving the page.
 *
 * A page handles its form at the top, before any HTML, and ends that handling
 * with redirect(). Without the redirect a refresh replays the form.
 */

/**
 * HTTP method of this request, uppercase.
 */
function requestMethod(): string
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

/**
 * Whether this request is a form submit.
 */
function isPost(): bool
{
    return requestMethod() === 'POST';
}

/**
 * Read one submitted value, trimmed.
 *
 * POST wins over the query string, so a form field beats a leftover parameter
 * in the URL. Anything that is not a plain value (an array, a file) returns the
 * default, because a template has no business printing those.
 *
 * @param string      $key     Field or parameter name.
 * @param string|null $default Returned when the value is missing or not a string.
 */
function input(string $key, ?string $default = null): ?string
{
    $value = $_POST[$key] ?? $_GET[$key] ?? null;

    return is_string($value) ? trim($value) : $default;
}

/**
 * Read one submitted value as a whole number.
 *
 * @param string $key     Field or parameter name.
 * @param int    $default Returned when the value is missing or not numeric.
 */
function inputInt(string $key, int $default = 0): int
{
    $value = input($key);

    return $value !== null && $value !== '' && is_numeric($value) ? (int) $value : $default;
}

/**
 * Go to another page and stop this one.
 *
 * Pass a page name ('contacten') or a path ('/contacten.php?id=3'). Anything
 * already printed is thrown away, so a redirect halfway down a page still
 * results in a clean redirect instead of half a document.
 *
 * @param string                    $target Page name without extension, or a path starting with '/'.
 * @param array<string, string|int> $query  Query parameters, only used with a page name.
 */
function redirect(string $target = 'index', array $query = []): never
{
    $location = str_starts_with($target, '/') ? $target : url($target, $query);

    cancelPage();

    header('Location: ' . $location, true, 303);

    exit;
}
