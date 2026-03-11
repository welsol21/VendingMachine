<?php

declare(strict_types=1);

namespace VendingMachine\Tests;

use PHPUnit\Framework\TestCase;
use VendingMachine\Adapter\Out\Event\NullEventPublisher;
use VendingMachine\Adapter\Out\Persistence\InMemoryMachineRepository;
use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\ReturnCoinCommand;
use VendingMachine\Application\Command\SelectItemCommand;
use VendingMachine\Application\Command\ServiceMachineCommand;
use VendingMachine\Application\MachineFactory;
use VendingMachine\Application\VendingMachineService;

/**
 * Integration tests for VendingMachineService wired with real adapters.
 */
class VendingMachineServiceTest extends TestCase
{
    private VendingMachineService $service;

    protected function setUp(): void
    {
        $factory        = MachineFactory::createDefault();
        $repository     = new InMemoryMachineRepository($factory);
        $eventPublisher = new NullEventPublisher();
        $this->service  = new VendingMachineService($repository, $eventPublisher);
    }

    public function test_service_loads_and_saves_machine_state_correctly(): void
    {
        $id = 'machine-A';

        $this->service->insertCoin(new InsertCoinCommand($id, 100));
        $this->service->insertCoin(new InsertCoinCommand($id, 25));
        $this->service->insertCoin(new InsertCoinCommand($id, 25));

        $result = $this->service->selectItem(new SelectItemCommand($id, 'SODA'));

        $this->assertSame('SODA', $result->item());
        $this->assertSame([], $result->change());
    }

    public function test_multiple_machine_ids_behave_independently(): void
    {
        // Insert coins into machine A only
        $this->service->insertCoin(new InsertCoinCommand('machine-A', 25));

        // Machine B has its own empty buffer
        $coinsB = $this->service->returnCoins(new ReturnCoinCommand('machine-B'));

        $this->assertSame([], $coinsB);

        // Machine A still has its coin
        $coinsA = $this->service->returnCoins(new ReturnCoinCommand('machine-A'));
        $this->assertSame([25], $coinsA);
    }

    public function test_service_machine_command_updates_inventory(): void
    {
        $id = 'machine-C';

        $this->service->serviceMachine(new ServiceMachineCommand($id, [100 => 10], ['JUICE' => 5]));

        $this->service->insertCoin(new InsertCoinCommand($id, 100));
        $result = $this->service->selectItem(new SelectItemCommand($id, 'JUICE'));

        $this->assertSame('JUICE', $result->item());
    }
}
