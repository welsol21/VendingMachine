<?php

declare(strict_types=1);

namespace VendingMachine\Application\Command;

final class SelectItemCommand
{
    public function __construct(
        public readonly string $machineId,
        public readonly string $selector,
    ) {}
}
