<?php

declare(strict_types=1);

namespace VendingMachine\Adapter\In\Cli;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\ReturnCoinCommand;
use VendingMachine\Application\Command\SelectItemCommand;
use VendingMachine\Port\In\VendingMachineUseCaseInterface;

/**
 * CLI demo runner — exercises the machine via the inbound use-case port.
 *
 * Not used by tests; intended as a runnable entry point for manual demos.
 */
final class DemoRunner
{
    public function __construct(
        private readonly VendingMachineUseCaseInterface $useCase,
        private readonly string                         $machineId = 'demo',
    ) {}

    public function run(): void
    {
        echo "=== Vending Machine Demo ===\n\n";

        // Scenario 1: buy SODA with exact change
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 100));
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 25));
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 25));
        $result = $this->useCase->selectItem(new SelectItemCommand($this->machineId, 'SODA'));
        echo "Bought : {$result->item()}\n";
        echo "Change : " . (empty($result->change()) ? 'none' : implode(', ', $result->change())) . "\n\n";

        // Scenario 2: insert coins then press return
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 10));
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 10));
        $coins = $this->useCase->returnCoins(new ReturnCoinCommand($this->machineId));
        echo "Returned: " . implode(', ', $coins) . "\n\n";

        // Scenario 3: buy WATER and receive change
        $this->useCase->insertCoin(new InsertCoinCommand($this->machineId, 100));
        $result = $this->useCase->selectItem(new SelectItemCommand($this->machineId, 'WATER'));
        echo "Bought : {$result->item()}\n";
        echo "Change : " . implode(', ', $result->change()) . "\n";
    }
}
