<?php

declare(strict_types=1);

namespace Draagvlak\Features\Score;

use Draagvlak\Core\View\Format;

/**
 * What a choice cost, on the way back to the screen that made it.
 *
 * The result is an English key, never a sentence. The screen hands that key to
 * a flash message and views/components/notices.php writes the Dutch around it,
 * which is how the app names the price only after you paid it.
 */
final readonly class Outcome
{
    /**
     * @param string $result     English key describing what happened.
     * @param string $contact    Name of the person it happened to, empty when it is nobody's.
     * @param int    $delta      Points the participant gained or lost.
     * @param int    $activeLeft Active contacts left, for the rule about the minimum.
     * @param int    $handled    Messages the app answered on the participant's behalf.
     * @param int    $priceCents What the subscription cost.
     */
    public function __construct(
        public string $result,
        public string $contact = '',
        public int $delta = 0,
        public int $activeLeft = 0,
        public int $handled = 0,
        public int $priceCents = 0,
    ) {}

    /**
     * The values a Dutch sentence can drop into its {placeholders}.
     *
     * @return array<string, string|int>
     */
    public function values(): array
    {
        return [
            'contact' => $this->contact,
            'delta' => abs($this->delta),
            'activeLeft' => $this->activeLeft,
            'handled' => $this->handled,
            'price' => Format::price($this->priceCents),
        ];
    }
}
