<?php

declare(strict_types=1);

namespace VendingMachine\Application;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\ReturnCoinCommand;
use VendingMachine\Application\Command\SelectItemCommand;
use VendingMachine\Application\Command\ServiceMachineCommand;
use VendingMachine\Domain\VendingMachineInterface;
use VendingMachine\Domain\VendResult;

/**
 * Application service — Phase 6.
 *
 * Holds machines in memory keyed by ID. Phase 7 will replace
 * the internal map with a proper MachineRepositoryInterface.
 */
final class VendingMachineService
{
    /** @var array<string, VendingMachineInterface> */
    private array $machines = [];

    public function __construct(private readonly MachineFactory $factory) {}

    public function insertCoin(InsertCoinCommand $command): void
    {
        $this->get($command->machineId)->insertCoin($command->cents);
    }

    public function selectItem(SelectItemCommand $command): VendResult
    {
        return $this->get($command->machineId)->selectItem($command->selector);
    }

    /** @return int[] */
    public function returnCoins(ReturnCoinCommand $command): array
    {
        return $this->get($command->machineId)->returnCoins();
    }

    public function serviceMachine(ServiceMachineCommand $command): void
    {
        $this->get($command->machineId)->service($command->coinsToAdd, $command->itemsToAdd);
    }

    private function get(string $machineId): VendingMachineInterface
    {
        if (!isset($this->machines[$machineId])) {
            $this->machines[$machineId] = $this->factory->create($machineId);
        }
        return $this->machines[$machineId];
    }
}
