<?php

declare(strict_types=1);

/**
 * Printing to the page: escaping, asset URLs and knowing which page you are on.
 *
 * These functions live in the global namespace on purpose. Every page loads
 * them through bootstrap.php, so a template can call e() without an import.
 */

/**
 * Escape a value before printing it inside HTML.
 *
 * Anything that did not come from a literal in the template has to go through
 * this function. Skipping it is how a name field turns into a script tag.
 *
 * @param string|int|float|bool|null $value Raw value, straight from data or input.
 * @return string Safe to place between tags or inside a quoted attribute.
 */
function e(string|int|float|bool|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Build the URL of a file in public/assets/.
 *
 * The modification time is appended as a query string so the browser fetches
 * the new version after an edit instead of serving the one it cached.
 *
 * @param string $path Path relative to public/assets/, for example 'css/base.css'.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $absolute = PUBLIC_PATH . '/assets/' . $path;
    $version = is_file($absolute) ? (string) filemtime($absolute) : '0';

    return '/assets/' . $path . '?v=' . $version;
}

/**
 * Build a link to a page in public/.
 *
 * Write url('contacten') rather than '/contacten.php', so the extension sits in
 * one place if the URLs ever change.
 *
 * @param string               $page  Page file name without extension. 'index' becomes '/'.
 * @param array<string, string|int> $query Optional query string parameters.
 */
function url(string $page = 'index', array $query = []): string
{
    $path = $page === 'index' ? '/' : '/' . trim($page, '/') . '.php';

    return $query === [] ? $path : $path . '?' . http_build_query($query);
}

/**
 * File name of the page being viewed, without extension.
 *
 * The home page reports 'index', which is what url() and the navigation use.
 */
function currentPage(): string
{
    $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $page = basename($path, '.php');

    return $page === '' || $page === '/' ? 'index' : $page;
}

/**
 * Whether a navigation item points at the page being viewed.
 *
 * @param string $page Page file name without extension, for example 'contacten'.
 */
function isCurrentPage(string $page): bool
{
    return currentPage() === $page;
}

/**
 * Turn an array into HTML attributes, escaped and ready to print.
 *
 * A value of true prints the attribute on its own (hidden), false and null drop
 * it entirely. Saves a template from building conditional attribute strings by
 * hand, which is where escaping usually gets forgotten.
 *
 * @param array<string, string|int|bool|null> $attributes Attribute name to value.
 */
function attributes(array $attributes): string
{
    $parts = [];

    foreach ($attributes as $name => $value) {
        if ($value === null || $value === false) {
            continue;
        }

        $parts[] = $value === true
            ? e($name)
            : sprintf('%s="%s"', e($name), e($value));
    }

    return implode(' ', $parts);
}
