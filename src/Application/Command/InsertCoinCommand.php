<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

final class InsertCoinCommand
{
    public function __construct(
        public readonly string $machineId,
        public readonly int    $cents,
    ) {}
}
