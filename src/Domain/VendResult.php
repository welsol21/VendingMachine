<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Result of a successful vend operation.
 *
 * Carries the dispensed item selector and the list of change coins returned
 * to the customer. All coin values are integers in cents.
 */
final class VendResult
{
    /**
     * @param string $item   Selector of the dispensed item (e.g. 'SODA').
     * @param int[]  $change Coins returned as change, largest denomination first.
     */
    public function __construct(
        private readonly string $item,
        private readonly array  $change,
    ) {}

    public function item(): string
    {
        return $this->item;
    }

    /** @return int[] */
    public function change(): array
    {
        return $this->change;
    }
}
