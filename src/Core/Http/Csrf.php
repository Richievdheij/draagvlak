<?php

declare(strict_types=1);

namespace Draagvlak\Core\Http;

use Draagvlak\Core\View\Html;
use Random\RandomException;

/**
 * Proof that a form was submitted from this site. One token per visit is
 * enough here: it stops another site from posting on a visitor's behalf.
 */
final readonly class Csrf
{
    /** Name of the hidden field, and of the value a screen reads back. */
    public const string FIELD = 'csrfToken';

    public function __construct(private Session $session) {}

    /** @throws RandomException When the system has no source of randomness. */
    public function token(): string
    {
        $token = $this->session->get(self::FIELD);

        if (!\is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(16));
            $this->session->set(self::FIELD, $token);
        }

        return $token;
    }

    /** Hidden input carrying the token. Print this inside every POST form. */
    public function field(): string
    {
        return \sprintf('<input type="hidden" name="%s" value="%s">', self::FIELD, Html::e($this->token()));
    }

    /** @param string|null $token The submitted value, straight from the request. */
    public function isValid(?string $token): bool
    {
        return $token !== null && $token !== '' && hash_equals($this->token(), $token);
    }
}
