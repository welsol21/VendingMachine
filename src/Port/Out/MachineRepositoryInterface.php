<?php

declare(strict_types=1);

namespace VendingMachine\Port\Out;

use VendingMachine\Domain\VendingMachineInterface;

/**
 * Outbound port: machine persistence.
 */
interface MachineRepositoryInterface
{
    public function findById(string $machineId): VendingMachineInterface;

    public function save(VendingMachineInterface $machine): void;
}
