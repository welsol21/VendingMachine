<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Strategy for computing change from available coin inventory.
 */
interface ChangeStrategyInterface
{
    /**
     * Compute the coins to return for the given amount.
     *
     * @param  int              $amount    Amount in cents to return.
     * @param  array<int, int>  $available Map of denomination → count available.
     * @return int[]|null                  Coins to dispense (largest first), or null if impossible.
     */
    public function compute(int $amount, array $available): ?array;
}
