<?php

declare(strict_types=1);

namespace VendingMachine\Application;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\ReturnCoinCommand;
use VendingMachine\Application\Command\SelectItemCommand;
use VendingMachine\Application\Command\ServiceMachineCommand;
use VendingMachine\Domain\VendResult;
use VendingMachine\Port\In\VendingMachineUseCaseInterface;
use VendingMachine\Port\Out\EventPublisherInterface;
use VendingMachine\Port\Out\MachineRepositoryInterface;

/**
 * Application service — Phase 7.
 *
 * Implements the inbound use-case port and depends only on outbound port
 * contracts (repository + event publisher), never on concrete adapters.
 */
final class VendingMachineService implements VendingMachineUseCaseInterface
{
    public function __construct(
        private readonly MachineRepositoryInterface $repository,
        private readonly EventPublisherInterface    $eventPublisher,
    ) {}

    public function insertCoin(InsertCoinCommand $command): void
    {
        $machine = $this->repository->findById($command->machineId);
        $machine->insertCoin($command->cents);
        $this->repository->save($machine);
    }

    public function selectItem(SelectItemCommand $command): VendResult
    {
        $machine = $this->repository->findById($command->machineId);
        $result  = $machine->selectItem($command->selector);
        $this->repository->save($machine);
        return $result;
    }

    public function returnCoins(ReturnCoinCommand $command): array
    {
        $machine = $this->repository->findById($command->machineId);
        $coins   = $machine->returnCoins();
        $this->repository->save($machine);
        return $coins;
    }

    public function serviceMachine(ServiceMachineCommand $command): void
    {
        $machine = $this->repository->findById($command->machineId);
        $machine->service($command->coinsToAdd, $command->itemsToAdd);
        $this->repository->save($machine);
    }
}
