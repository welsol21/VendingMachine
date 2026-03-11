<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Core vending machine — Phase 4.
 *
 * Delegates configuration to MachineConfig and mutable state to MachineState.
 * Change logic lives here temporarily; it will be extracted into a
 * ChangeStrategyInterface in Phase 5.
 */
final class VendingMachine implements VendingMachineInterface
{
    public function __construct(
        private readonly string       $machineId,
        private readonly MachineConfig $config,
        private MachineState           $state,
    ) {}

    /**
     * Factory for a fully stocked machine ready for testing and demo use.
     */
    public static function createDefault(string $machineId = 'default'): self
    {
        $config = MachineConfig::createDefault();
        return new self($machineId, $config, MachineState::createDefault($config));
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

        $change = $this->makeChange($paid - $price);
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

    /** @return int[] Change coins, largest denomination first. */
    private function makeChange(int $amount): array
    {
        if ($amount === 0) {
            return [];
        }

        $change = [];

        foreach ($this->config->denominations() as $denom) {
            while ($amount >= $denom && $this->state->coinCount($denom) > 0) {
                $change[] = $denom;
                $this->state->decrementCoin($denom);
                $amount -= $denom;
            }
        }

        return $change;
    }
}
