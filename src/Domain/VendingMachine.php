<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Minimal vending machine implementation — Phase 3.
 *
 * Intentionally kept as simple as possible to pass the three high-level
 * controlling tests. Prices, denominations, inventory, and change logic
 * live here temporarily; they will be extracted into MachineConfig,
 * MachineState, and ChangeStrategy during Phase 4 and 5.
 *
 * Money is represented as integers in cents throughout.
 *
 * Denomination order matters for the greedy change algorithm:
 * largest first → [100, 25, 10, 5].
 */
final class VendingMachine
{
    /** @var int[] Coin denominations accepted, ordered largest-first. */
    private const DENOMINATIONS = [100, 25, 10, 5];

    /** @var array<string, int> Product prices in cents. */
    private const PRICES = [
        'SODA'  => 150,
        'JUICE' => 100,
        'WATER' => 65,
    ];

    /** @var int[] Coins currently inserted by the customer (not yet spent). */
    private array $insertedCoins = [];

    /**
     * @param array<int, int> $changeInventory  Map of cent-value → available count.
     * @param array<string, int> $itemInventory  Map of selector → stock count.
     */
    private function __construct(
        private array $changeInventory,
        private array $itemInventory,
    ) {}

    /**
     * Factory for a fully stocked machine ready for testing and demo use.
     *
     * Default stock: 10 units of every product.
     * Default change: 10 coins of every denomination.
     */
    public static function createDefault(): self
    {
        return new self(
            changeInventory: [100 => 10, 25 => 10, 10 => 10, 5 => 10],
            itemInventory:   ['SODA' => 10, 'JUICE' => 10, 'WATER' => 10],
        );
    }

    /**
     * Accept a coin from the customer.
     *
     * @param int $cents Coin value in cents (e.g. 25 for a quarter).
     */
    public function insertCoin(int $cents): void
    {
        $this->insertedCoins[] = $cents;
    }

    /**
     * Return all inserted coins to the customer without completing a purchase.
     *
     * @return int[] The exact coins that were inserted, in insertion order.
     */
    public function returnCoins(): array
    {
        $coins = $this->insertedCoins;
        $this->insertedCoins = [];

        return $coins;
    }

    /**
     * Vend the requested item.
     *
     * Merges inserted coins into the machine's change inventory, deducts the
     * price, computes change with a greedy algorithm, and decrements stock.
     *
     * @param string $selector Product selector (e.g. 'SODA').
     */
    public function selectItem(string $selector): VendResult
    {
        $price = self::PRICES[$selector];
        $paid  = array_sum($this->insertedCoins);

        // Inserted coins become part of the machine's funds.
        foreach ($this->insertedCoins as $coin) {
            $this->changeInventory[$coin]++;
        }
        $this->insertedCoins = [];

        $change = $this->makeChange($paid - $price);
        $this->itemInventory[$selector]--;

        return new VendResult($selector, $change);
    }

    /**
     * Greedy change algorithm — largest denomination first.
     *
     * @param  int   $amount Remaining amount to return in cents.
     * @return int[]         Coins to give back, largest first.
     */
    private function makeChange(int $amount): array
    {
        if ($amount === 0) {
            return [];
        }

        $change = [];

        foreach (self::DENOMINATIONS as $denom) {
            while ($amount >= $denom && $this->changeInventory[$denom] > 0) {
                $change[] = $denom;
                $this->changeInventory[$denom]--;
                $amount -= $denom;
            }
        }

        return $change;
    }
}
