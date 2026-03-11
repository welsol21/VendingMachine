<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Mutable per-machine state.
 *
 * Tracks coin inventory (funds the machine holds), item inventory (stock),
 * and the coins currently inserted by a customer mid-transaction.
 *
 * All coin values are integers in cents.
 */
final class MachineState
{
    /**
     * @param array<int, int>    $coinInventory  Map of cent-value → count (machine's funds).
     * @param array<string, int> $itemInventory  Map of selector → stock count.
     * @param int[]              $insertedCoins  Coins currently in the slot, not yet spent.
     */
    public function __construct(
        private array $coinInventory,
        private array $itemInventory,
        private array $insertedCoins = [],
    ) {}

    /**
     * Build a fully stocked default state: 10 units/coins per denomination and product.
     */
    public static function createDefault(MachineConfig $config): self
    {
        $coinInventory = [];
        foreach ($config->denominations() as $denom) {
            $coinInventory[$denom] = 10;
        }

        $itemInventory = [];
        foreach ($config->catalog() as $selector => $_) {
            $itemInventory[$selector] = 10;
        }

        return new self($coinInventory, $itemInventory);
    }

    // ── Inserted-coins buffer ────────────────────────────────────────────────

    public function insertCoin(int $cents): void
    {
        $this->insertedCoins[] = $cents;
    }

    /** @return int[] Coins waiting to be spent or returned. */
    public function insertedCoins(): array { return $this->insertedCoins; }

    public function insertedTotal(): int { return array_sum($this->insertedCoins); }

    /**
     * Absorb inserted coins into the machine's coin inventory and clear the buffer.
     * Called after a successful purchase.
     */
    public function absorb(): void
    {
        foreach ($this->insertedCoins as $coin) {
            $this->coinInventory[$coin] = ($this->coinInventory[$coin] ?? 0) + 1;
        }
        $this->insertedCoins = [];
    }

    /**
     * Return all inserted coins to the customer and clear the buffer.
     *
     * @return int[] The coins that were in the slot.
     */
    public function ejectInserted(): array
    {
        $coins = $this->insertedCoins;
        $this->insertedCoins = [];
        return $coins;
    }

    // ── Coin inventory ───────────────────────────────────────────────────────

    public function coinCount(int $denom): int { return $this->coinInventory[$denom] ?? 0; }

    public function decrementCoin(int $denom): void { $this->coinInventory[$denom]--; }

    public function addCoins(int $denom, int $count): void
    {
        $this->coinInventory[$denom] = ($this->coinInventory[$denom] ?? 0) + $count;
    }

    /** @return array<int, int> */
    public function coinInventory(): array { return $this->coinInventory; }

    // ── Item inventory ───────────────────────────────────────────────────────

    public function itemCount(string $selector): int { return $this->itemInventory[$selector] ?? 0; }

    public function decrementItem(string $selector): void { $this->itemInventory[$selector]--; }

    public function addItems(string $selector, int $count): void
    {
        $this->itemInventory[$selector] = ($this->itemInventory[$selector] ?? 0) + $count;
    }

    /** @return array<string, int> */
    public function itemInventory(): array { return $this->itemInventory; }
}
