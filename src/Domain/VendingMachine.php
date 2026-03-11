<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

use VendingMachine\Domain\Exception\InsufficientChange;
use VendingMachine\Domain\Exception\InsufficientFunds;
use VendingMachine\Domain\Exception\InvalidCoin;
use VendingMachine\Domain\Exception\InvalidSelector;
use VendingMachine\Domain\Exception\ItemOutOfStock;

/**
 * Core vending machine — Phase 11.
 *
 * All preconditions are validated before any state mutation:
 *  insertCoin  → InvalidCoin if denomination is not accepted
 *  selectItem  → InvalidSelector, InsufficientFunds, ItemOutOfStock, InsufficientChange
 */
final class VendingMachine implements VendingMachineInterface
{
    public function __construct(
        private readonly string                  $machineId,
        private readonly MachineConfig           $config,
        private MachineState                     $state,
        private readonly ChangeStrategyInterface $changeStrategy,
    ) {}

    public static function createDefault(string $machineId = 'default'): self
    {
        $config = MachineConfig::createDefault();
        return new self(
            $machineId,
            $config,
            MachineState::createDefault($config),
            new GreedyChangeStrategy(),
        );
    }

    public function id(): string { return $this->machineId; }

    public function insertCoin(int $cents): void
    {
        if (!$this->config->hasDenomination($cents)) {
            throw new InvalidCoin($cents);
        }
        $this->state->insertCoin($cents);
    }

    public function returnCoins(): array
    {
        return $this->state->ejectInserted();
    }

    public function selectItem(string $selector): VendResult
    {
        if (!$this->config->hasProduct($selector)) {
            throw new InvalidSelector($selector);
        }

        $price = $this->config->product($selector)->price();
        $paid  = $this->state->insertedTotal();

        if ($paid < $price) {
            throw new InsufficientFunds($price, $paid);
        }

        if ($this->state->itemCount($selector) === 0) {
            throw new ItemOutOfStock($selector);
        }

        // Compute change against the tentative inventory (current + inserted coins)
        // so we can verify feasibility before committing any state mutation.
        $tentative = $this->state->coinInventory();
        foreach ($this->state->insertedCoins() as $coin) {
            $tentative[$coin] = ($tentative[$coin] ?? 0) + 1;
        }

        $change = $this->changeStrategy->compute($paid - $price, $tentative);
        if ($change === null) {
            throw new InsufficientChange($paid - $price);
        }

        // All checks passed — commit the transaction.
        $this->state->absorb();

        foreach ($change as $denom) {
            $this->state->decrementCoin($denom);
        }

        $this->state->decrementItem($selector);

        return new VendResult($selector, $change);
    }

    public function service(array $coinsToAdd, array $itemsToAdd): void
    {
        foreach ($coinsToAdd as $denom => $count) {
            $this->state->addCoins($denom, $count);
        }
        foreach ($itemsToAdd as $selector => $count) {
            $this->state->addItems($selector, $count);
        }
    }

    public function snapshot(): MachineState
    {
        return $this->state;
    }
}
