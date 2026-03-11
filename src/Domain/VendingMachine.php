<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Core vending machine — Phase 5.
 *
 * Change computation is delegated to an injected ChangeStrategyInterface.
 */
final class VendingMachine implements VendingMachineInterface
{
    public function __construct(
        private readonly string                  $machineId,
        private readonly MachineConfig           $config,
        private MachineState                     $state,
        private readonly ChangeStrategyInterface $changeStrategy,
    ) {}

    /**
     * Factory for a fully stocked machine using the greedy change strategy.
     */
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
        $this->state->insertCoin($cents);
    }

    public function returnCoins(): array
    {
        return $this->state->ejectInserted();
    }

    public function selectItem(string $selector): VendResult
    {
        $price = $this->config->product($selector)->price();
        $paid  = $this->state->insertedTotal();

        $this->state->absorb();

        $change = $this->changeStrategy->compute($paid - $price, $this->state->coinInventory()) ?? [];

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
