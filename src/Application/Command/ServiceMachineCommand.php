<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

final class ServiceMachineCommand
{
    /**
     * @param array<int, int>    $coinsToAdd  denom → count to add.
     * @param array<string, int> $itemsToAdd  selector → count to add.
     */
    public function __construct(
        public readonly string $machineId,
        public readonly array  $coinsToAdd,
        public readonly array  $itemsToAdd,
    ) {}
}
