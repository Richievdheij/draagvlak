<?php

declare(strict_types=1);

namespace Draagvlak\Core\View;

use Draagvlak\Core\Paths;

/**
 * The files in public/assets/: their URL, and which stylesheets a screen gets.
 *
 * Nothing has to be registered. Drop a file in the right folder and it is
 * printed; a file under features/<name>/ is printed on that feature's screens
 * only.
 */
final readonly class Assets
{
    /** Always loaded, in this order. Later folders may override earlier ones. */
    private const array SHARED_FOLDERS = ['base', 'layout', 'components'];

    /** base/ has an order of its own: the foundation before anything uses it. */
    private const array BASE_ORDER = ['fonts.css', 'tokens.css', 'reset.css', 'elements.css', 'utilities.css'];

    public function __construct(private Paths $paths) {}

    /**
     * URL of a file in public/assets/, with its modification time appended so
     * the browser fetches the new version after an edit.
     *
     * @param string $path Path relative to public/assets/, for example 'js/app.js'.
     */
    public function url(string $path): string
    {
        $path = ltrim($path, '/');
        $absolute = $this->paths->public . '/assets/' . $path;
        $version = is_file($absolute) ? (string) filemtime($absolute) : '0';

        return '/assets/' . $path . '?v=' . $version;
    }

    /**
     * Every stylesheet this screen needs, in load order.
     *
     * @param string $feature Feature the screen belongs to, for features/<feature>/.
     *
     * @return list<string> Ready-to-print URLs.
     */
    public function stylesheets(string $feature): array
    {
        $files = [];

        foreach (self::SHARED_FOLDERS as $folder) {
            $files = [...$files, ...$this->cssFilesIn($folder, $folder === 'base' ? self::BASE_ORDER : [])];
        }

        $files = [...$files, ...$this->cssFilesIn('features/' . $feature)];

        return array_map(fn (string $file): string => $this->url('css/' . $file), $files);
    }

    /**
     * @param list<string> $first File names that have to come first, in this order.
     *
     * @return list<string> Paths relative to assets/css/.
     */
    private function cssFilesIn(string $folder, array $first = []): array
    {
        $found = array_map('basename', glob($this->paths->public . '/assets/css/' . $folder . '/*.css') ?: []);
        sort($found);

        $ordered = [...array_intersect($first, $found), ...array_diff($found, $first)];

        return array_map(static fn (string $file): string => $folder . '/' . $file, $ordered);
    }
}
