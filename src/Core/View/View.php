<?php

declare(strict_types=1);

namespace Draagvlak\Core\View;

use Draagvlak\Core\Http\Csrf;
use Draagvlak\Core\Paths;
use RuntimeException;
use Throwable;

/**
 * Printing a template from views/.
 *
 * A template is required from inside this class, so $this in a template is this
 * object. That is where e(), url(), asset() and partial() come from, and it is
 * why a template needs no imports and no global functions.
 */
final readonly class View
{
    public function __construct(
        private Paths $paths,
        private Assets $assets,
        private Csrf $csrf,
    ) {}

    /**
     * Render a template and hand back what it printed. Keys of $data become
     * local variables inside it.
     *
     * @param string $template Path under views/ without extension.
     * @param array<string, mixed> $data Variables the template expects.
     *
     * @throws RuntimeException When the template does not exist.
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->file($template);

        ob_start();

        try {
            (function () use ($file, $data): void {
                extract($data, EXTR_SKIP);

                require $file;
            })();
        } catch (Throwable $error) {
            ob_end_clean();

            throw $error;
        }

        return (string) ob_get_clean();
    }

    /**
     * Print another template inside this one. The path is written out in full,
     * so you can always see which file you are looking at.
     *
     *     $this->partial('components/notices', ['flashes' => $flashes]);
     *     $this->partial('features/home/score-block', ['account' => $account]);
     *
     * @param array<string, mixed> $data
     */
    public function partial(string $template, array $data = []): void
    {
        echo $this->render($template, $data);
    }

    /** Escape a value before printing it. */
    public function e(string|int|float|bool|null $value): string
    {
        return Html::e($value);
    }

    /** @param array<string, string|int|bool|null> $attributes */
    public function attributes(array $attributes): string
    {
        return Html::attributes($attributes);
    }

    /** @param array<string, string|int> $query */
    public function url(string $screen = 'home', array $query = []): string
    {
        return Url::to($screen, $query);
    }

    /** URL of a file in public/assets/, with a version on it. */
    public function asset(string $path): string
    {
        return $this->assets->url($path);
    }

    /** Hidden input with the form token. Print this inside every POST form. */
    public function csrfField(): string
    {
        return $this->csrf->field();
    }

    /** @throws RuntimeException When the template does not exist. */
    private function file(string $template): string
    {
        $file = $this->paths->views . '/' . trim($template, '/') . '.php';

        if (!is_file($file)) {
            throw new RuntimeException(\sprintf('Template "%s" does not exist: %s', $template, $file));
        }

        return $file;
    }
}
