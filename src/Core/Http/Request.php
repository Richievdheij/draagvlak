<?php

declare(strict_types=1);

namespace Draagvlak\Core\Http;

/**
 * What the browser asked for. The superglobals are read once, here, and nowhere
 * else.
 */
final readonly class Request
{
    /**
     * @param array<string, mixed> $post
     * @param array<string, mixed> $get
     * @param array<string, mixed> $server
     */
    public function __construct(
        private array $post,
        private array $get,
        private array $server,
    ) {}

    public static function fromGlobals(): self
    {
        return new self($_POST, $_GET, $_SERVER);
    }

    /** HTTP method, uppercase. */
    public function method(): string
    {
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * One submitted value, trimmed. POST wins over the query string, and
     * anything that is not a plain value returns the default.
     *
     * @param string|null $default Returned when the value is missing or is not a string.
     */
    public function input(string $key, ?string $default = null): ?string
    {
        $value = $this->post[$key] ?? $this->get[$key] ?? null;

        return \is_string($value) ? trim($value) : $default;
    }

    /** One submitted value as text, never null. */
    public function text(string $key): string
    {
        return (string) $this->input($key, '');
    }

    /** One submitted value as a whole number. */
    public function inputInt(string $key, int $default = 0): int
    {
        $value = $this->input($key);

        return $value !== null && $value !== '' && is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Name of the screen being viewed: the file in public/ without its
     * extension, and 'home' for the root.
     */
    public function screen(): string
    {
        $path = (string) parse_url((string) ($this->server['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $screen = basename($path, '.php');

        return $screen === '' || $screen === '/' || $screen === 'index' ? 'home' : $screen;
    }
}
