<?php

declare(strict_types=1);

namespace VendingMachine\Application;

use VendingMachine\Domain\ChangeStrategyInterface;
use VendingMachine\Domain\GreedyChangeStrategy;
use VendingMachine\Domain\MachineConfig;
use VendingMachine\Domain\MachineState;
use VendingMachine\Domain\VendingMachine;

/**
 * Creates and restores VendingMachine instances.
 *
 * Centralises wiring of MachineConfig and ChangeStrategy so that
 * the rest of the application does not need to know about construction details.
 */
final class MachineFactory
{
    public function __construct(
        private readonly MachineConfig           $config,
        private readonly ChangeStrategyInterface $changeStrategy,
    ) {}

    public static function createDefault(): self
    {
        return new self(MachineConfig::createDefault(), new GreedyChangeStrategy());
    }

    /** Create a new, fully stocked machine with the given ID. */
    public function create(string $machineId): VendingMachine
    {
        return new VendingMachine(
            $machineId,
            $this->config,
            MachineState::createDefault($this->config),
            $this->changeStrategy,
        );
    }

    /** Restore a machine from a previously persisted state snapshot. */
    public function restore(string $machineId, MachineState $state): VendingMachine
    {
        return new VendingMachine($machineId, $this->config, $state, $this->changeStrategy);
    }
}
