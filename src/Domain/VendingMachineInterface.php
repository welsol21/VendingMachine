<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Contract for a vending machine.
 *
 * All coin values are integers in cents.
 */
interface VendingMachineInterface
{
    public function id(): string;

    public function insertCoin(int $cents): void;

    public function selectItem(string $selector): VendResult;

    /** @return int[] Coins returned to the customer. */
    public function returnCoins(): array;

    /**
     * Technician refill operation.
     *
     * @param array<int, int>    $coinsToAdd  denom → count to add to coin inventory.
     * @param array<string, int> $itemsToAdd  selector → count to add to item inventory.
     */
    public function service(array $coinsToAdd, array $itemsToAdd): void;

    /** Read-only snapshot of current machine state (for persistence). */
    public function snapshot(): MachineState;
}
