<?php

declare(strict_types=1);

namespace Draagvlak\Features\Contacts;

use Random\RandomException;

/**
 * The code somebody shares so another account can add them, as SAM-7QK4.
 *
 * Three characters from the account itself so the code still looks like it
 * belongs to a person, and four random ones that make it unique. The alphabet
 * leaves out I, O, 0 and 1: a code gets read out loud and typed over, and those
 * four are the ones people get wrong.
 */
final class ContactCode
{
    public const string ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public const int PREFIX_LENGTH = 3;

    public const int SUFFIX_LENGTH = 4;

    /** Characters a whole code takes up, dash included. For a maxlength. */
    public const int LENGTH = self::PREFIX_LENGTH + self::SUFFIX_LENGTH + 1;

    /** Filler for an account whose name and address hold no usable letters. */
    private const string FILLER = 'X';

    /**
     * A new code. It is not guaranteed to be free; only the database knows, so
     * that is AccountRepository's job.
     *
     * @param string $email Fallback when the name has no usable characters.
     *
     * @throws RandomException When the system has no source of randomness.
     */
    public static function generate(string $name, string $email): string
    {
        return self::prefixFor($name, $email) . '-' . self::randomPart(self::SUFFIX_LENGTH);
    }

    /**
     * Clean up a code somebody typed. Spaces, lower case and a missing or
     * doubled dash all lead to the same code, because that is how people copy
     * one over from a screen.
     */
    public static function normalise(string $code): string
    {
        $bare = preg_replace('/[^A-Z0-9]/', '', mb_strtoupper(trim($code), 'UTF-8')) ?? '';

        if (mb_strlen($bare) <= self::PREFIX_LENGTH) {
            return $bare;
        }

        return mb_substr($bare, 0, self::PREFIX_LENGTH) . '-' . mb_substr($bare, self::PREFIX_LENGTH);
    }

    /** Whether a normalised code has the shape of a code at all. */
    public static function isValid(string $code): bool
    {
        $pattern = \sprintf('/^[A-Z0-9]{%d}-[A-Z0-9]{%d}$/', self::PREFIX_LENGTH, self::SUFFIX_LENGTH);

        return preg_match($pattern, $code) === 1;
    }

    /** The name, the address when the name gives nothing, filler when neither does. */
    private static function prefixFor(string $name, string $email): string
    {
        $usable = self::usableCharacters($name);

        if (mb_strlen($usable) < self::PREFIX_LENGTH) {
            $usable .= self::usableCharacters(strstr($email, '@', true) ?: $email);
        }

        $usable .= str_repeat(self::FILLER, self::PREFIX_LENGTH);

        return mb_substr($usable, 0, self::PREFIX_LENGTH);
    }

    private static function usableCharacters(string $text): string
    {
        $upper = mb_strtoupper(trim($text), 'UTF-8');

        return preg_replace('/[^' . preg_quote(self::ALPHABET, '/') . ']/', '', $upper) ?? '';
    }

    /**
     * random_int() and not rand(): a code that can be guessed is a code that
     * adds you to somebody else's list.
     *
     * @throws RandomException When the system has no source of randomness.
     */
    private static function randomPart(int $length): string
    {
        $last = \strlen(self::ALPHABET) - 1;
        $code = '';

        for ($index = 0; $index < $length; $index++) {
            $code .= self::ALPHABET[random_int(0, $last)];
        }

        return $code;
    }
}
