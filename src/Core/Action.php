<?php

declare(strict_types=1);

namespace Draagvlak\Core;

/**
 * A file in public/ that does something and sends you on, without a screen.
 * Logging out is the one that exists today.
 */
abstract class Action
{
    public function __construct(protected readonly App $app) {}

    /** Do the work and leave. Always ends in Response::redirect(). */
    abstract public function handle(): never;
}
