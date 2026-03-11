<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Greedy change strategy — always picks the largest denomination that fits.
 *
 * Returns null when the exact amount cannot be formed from available coins.
 */
final class GreedyChangeStrategy implements ChangeStrategyInterface
{
    public function compute(int $amount, array $available): ?array
    {
        if ($amount === 0) {
            return [];
        }

        $change    = [];
        $remaining = $amount;

        // Sort denominations descending so the greedy pick is always largest-first.
        $denoms = array_keys($available);
        rsort($denoms);

        foreach ($denoms as $denom) {
            $canUse = min((int) floor($remaining / $denom), $available[$denom]);
            for ($i = 0; $i < $canUse; $i++) {
                $change[]  = $denom;
                $remaining -= $denom;
            }
            if ($remaining === 0) {
                break;
            }
        }

        return $remaining === 0 ? $change : null;
    }
}
