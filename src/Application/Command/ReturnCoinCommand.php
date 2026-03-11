<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

final class ReturnCoinCommand
{
    public function __construct(
        public readonly string $machineId,
    ) {}
}
