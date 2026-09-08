<?php

declare(strict_types=1);

namespace Draagvlak\Core;

/**
 * Where the parts of the project sit on disk.
 */
final readonly class Paths
{
    public string $src;

    public string $views;

    public string $data;

    public string $public;

    public function __construct(public string $root)
    {
        $this->src = $root . '/src';
        $this->views = $root . '/views';
        $this->data = $root . '/data';
        $this->public = $root . '/public';
    }
}
