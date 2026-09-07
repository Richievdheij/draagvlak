<?php

declare(strict_types=1);

/**
 * The page template: one call at the top of a screen, the layout does the rest.
 *
 * A screen calls page('Titel') and then prints its own markup. Everything that
 * is the same on every screen (the document, the head, the header, the flash
 * messages, the footer, the scripts) is printed by the layout in views/layouts/,
 * which runs after the page is done. That is why a screen has no closing call
 * and no include of a header.
 */

/**
 * Start a page: capture everything it prints and hand it to the layout.
 *
 * @param string $title   Dutch page title, shown in the tab and as the heading.
 * @param array{
 *     layout?: string,
 *     nav?: string,
 *     description?: string,
 *     bodyClass?: string
 * } $options layout: file in views/layouts/ without extension, defaults to 'app'.
 *            nav: navigation item to mark as current, defaults to this page.
 *            description: meta description for this page.
 *            bodyClass: extra class on <body>, for styling one screen.
 */
function page(string $title, array $options = []): void
{
    pageState([
        'title' => $title,
        'layout' => $options['layout'] ?? 'app',
        'nav' => $options['nav'] ?? currentPage(),
        'description' => $options['description'] ?? null,
        'bodyClass' => $options['bodyClass'] ?? 'page-' . currentPage(),
        'open' => true,
        'bufferLevel' => ob_get_level() + 1,
    ]);

    ob_start();

    register_shutdown_function(finishPage(...));
}

/**
 * Print the layout around everything the page produced.
 *
 * Registered by page() and run when the script ends, so a screen never has to
 * call it. Calling it by hand renders the page early, which only makes sense if
 * something has to run after the HTML is out.
 */
function finishPage(): void
{
    $state = pageState();

    if ($state['open'] !== true) {
        return;
    }

    pageState(['open' => false]);

    $content = '';

    while (ob_get_level() >= $state['bufferLevel']) {
        $content = (string) ob_get_clean() . $content;
    }

    $file = VIEWS_PATH . '/layouts/' . $state['layout'] . '.php';

    if (!is_file($file)) {
        throw new RuntimeException(sprintf('Layout "%s" does not exist: %s', $state['layout'], $file));
    }

    $title = (string) $state['title'];
    $nav = (string) $state['nav'];
    $bodyClass = (string) $state['bodyClass'];
    $description = $state['description'] === null ? null : (string) $state['description'];

    require $file;
}

/**
 * Throw away the captured page and skip the layout.
 *
 * Used by redirect(): a browser that is being sent elsewhere should not receive
 * half a document first.
 */
function cancelPage(): void
{
    $state = pageState();

    if ($state['open'] !== true) {
        return;
    }

    pageState(['open' => false]);

    while (ob_get_level() >= $state['bufferLevel']) {
        ob_end_clean();
    }
}

/**
 * Include a shared fragment from views/partials/.
 *
 * Keys of $data become local variables inside the fragment, so
 * partial('site-nav', ['nav' => $nav]) exposes $nav there.
 *
 * @param string               $name Fragment file name without extension.
 * @param array<string, mixed> $data Variables the fragment expects.
 */
function partial(string $name, array $data = []): void
{
    $file = VIEWS_PATH . '/partials/' . $name . '.php';

    if (!is_file($file)) {
        throw new RuntimeException(sprintf('Partial "%s" does not exist: %s', $name, $file));
    }

    extract($data, EXTR_SKIP);

    require $file;
}

/**
 * Every stylesheet, in the order the browser has to load them.
 *
 * The folders mirror the code: base/ is the foundation, then the layout, then
 * the partials, then the components, and last the one file that belongs to this
 * page. Later files may override earlier ones, which is why the order is fixed
 * and not alphabetical across the whole set.
 *
 * Nothing has to be registered. Drop site-header.css in css/partials/ and it is
 * loaded; add css/pages/contacten.css and it is loaded on contacten.php only.
 *
 * @return list<string> Ready-to-print URLs, cache-busted by asset().
 */
function stylesheets(): array
{
    $files = [
        ...cssFilesIn('base', ['fonts.css', 'tokens.css', 'reset.css', 'elements.css', 'utilities.css']),
        ...cssFilesIn('layouts'),
        ...cssFilesIn('partials'),
        ...cssFilesIn('components'),
    ];

    $page = 'pages/' . currentPage() . '.css';

    if (is_file(PUBLIC_PATH . '/assets/css/' . $page)) {
        $files[] = $page;
    }

    return array_map(static fn (string $file): string => asset('css/' . $file), $files);
}

/**
 * The stylesheets in one folder of assets/css/, as paths relative to that
 * folder's parent.
 *
 * @param string       $folder Folder name, for example 'components'.
 * @param list<string> $first  File names that have to come first, in this order.
 * @return list<string>
 */
function cssFilesIn(string $folder, array $first = []): array
{
    $found = array_map('basename', glob(PUBLIC_PATH . '/assets/css/' . $folder . '/*.css') ?: []);
    sort($found);

    $ordered = [...array_intersect($first, $found), ...array_diff($found, $first)];

    return array_map(static fn (string $file): string => $folder . '/' . $file, $ordered);
}

/**
 * State the layout needs, kept between page() and finishPage().
 *
 * Internal: a screen sets this through page(), not directly.
 *
 * @param array<string, mixed> $changes Values to overwrite.
 * @return array<string, mixed>
 */
function pageState(array $changes = []): array
{
    static $state = [
        'title' => '',
        'layout' => 'app',
        'nav' => '',
        'description' => null,
        'bodyClass' => '',
        'open' => false,
        'bufferLevel' => 0,
    ];

    if ($changes !== []) {
        $state = [...$state, ...$changes];
    }

    return $state;
}
