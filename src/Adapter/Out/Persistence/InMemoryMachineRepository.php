<?php

declare(strict_types=1);

namespace VendingMachine\Adapter\Out\Persistence;

use VendingMachine\Application\MachineFactory;
use VendingMachine\Domain\VendingMachineInterface;
use VendingMachine\Port\Out\MachineRepositoryInterface;

/**
 * In-memory repository — creates machines on first access, holds them
 * in a keyed array for the lifetime of the process.
 *
 * Used for testing and demo contexts; no serialisation needed.
 */
final class InMemoryMachineRepository implements MachineRepositoryInterface
{
    /** @var array<string, VendingMachineInterface> */
    private array $machines = [];

    public function __construct(private readonly MachineFactory $factory) {}

    public function findById(string $machineId): VendingMachineInterface
    {
        if (!isset($this->machines[$machineId])) {
            $this->machines[$machineId] = $this->factory->create($machineId);
        }
        return $this->machines[$machineId];
    }

    public function save(VendingMachineInterface $machine): void
    {
        $this->machines[$machine->id()] = $machine;
    }
}
