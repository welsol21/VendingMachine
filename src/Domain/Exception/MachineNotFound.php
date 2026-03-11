<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class MachineNotFound extends \RuntimeException
{
    public function __construct(string $machineId)
    {
        parent::__construct("Machine not found: '{$machineId}'");
    }
}
