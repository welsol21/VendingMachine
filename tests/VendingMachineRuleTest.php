<?php

declare(strict_types=1);

namespace VendingMachine\Tests;

use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\Exception\InsufficientChange;
use VendingMachine\Domain\Exception\InsufficientFunds;
use VendingMachine\Domain\Exception\InvalidCoin;
use VendingMachine\Domain\Exception\InvalidSelector;
use VendingMachine\Domain\Exception\ItemOutOfStock;
use VendingMachine\Domain\GreedyChangeStrategy;
use VendingMachine\Domain\MachineConfig;
use VendingMachine\Domain\MachineState;
use VendingMachine\Domain\VendingMachine;

/**
 * Domain rule tests — failure scenarios and state invariants.
 *
 * Phase 9 goal: all six tests green.
 */
class VendingMachineRuleTest extends TestCase
{
    private VendingMachine $machine;

    protected function setUp(): void
    {
        $this->machine = VendingMachine::createDefault();
    }

    /**
     * Inserting less than the item price must throw.
     */
    public function test_it_fails_when_not_enough_money_was_inserted(): void
    {
        $this->machine->insertCoin(25);  // SODA costs 150¢

        $this->expectException(InsufficientFunds::class);
        $this->machine->selectItem('SODA');
    }

    /**
     * Selecting a sold-out item must throw.
     */
    public function test_it_fails_when_item_is_out_of_stock(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->machine->insertCoin(100);
            $this->machine->insertCoin(25);
            $this->machine->insertCoin(25);
            $this->machine->selectItem('SODA');
        }

        $this->machine->insertCoin(100);
        $this->machine->insertCoin(25);
        $this->machine->insertCoin(25);

        $this->expectException(ItemOutOfStock::class);
        $this->machine->selectItem('SODA');
    }

    /**
     * When the machine cannot form exact change, the transaction must be refused.
     *
     * Machine loaded with only $1 coins cannot return 35¢ change for WATER.
     */
    public function test_it_fails_when_exact_change_cannot_be_returned(): void
    {
        $config  = MachineConfig::createDefault();
        $state   = new MachineState(
            coinInventory: [100 => 5, 25 => 0, 10 => 0, 5 => 0],
            itemInventory: ['WATER' => 5],
        );
        $machine = new VendingMachine('no-change', $config, $state, new GreedyChangeStrategy());

        $machine->insertCoin(100);  // WATER = 65¢, change needed = 35¢ — impossible

        $this->expectException(InsufficientChange::class);
        $machine->selectItem('WATER');
    }

    /**
     * After a successful purchase the inserted-coin buffer must be empty.
     */
    public function test_it_resets_inserted_money_after_successful_purchase(): void
    {
        $this->machine->insertCoin(100);
        $this->machine->insertCoin(100);    // Over-pay for SODA (150¢); change = 50¢ returned
        $this->machine->selectItem('SODA');

        $this->assertSame([], $this->machine->returnCoins());
    }

    /**
     * After returnCoins() the buffer must be empty on the next call.
     */
    public function test_it_resets_inserted_money_after_return(): void
    {
        $this->machine->insertCoin(100);
        $this->machine->returnCoins();

        $this->assertSame([], $this->machine->returnCoins());
    }

    /**
     * service() must increase both coin and item inventories.
     */
    public function test_it_updates_resources_after_service(): void
    {
        // Drain all SODA
        for ($i = 0; $i < 10; $i++) {
            $this->machine->insertCoin(100);
            $this->machine->insertCoin(25);
            $this->machine->insertCoin(25);
            $this->machine->selectItem('SODA');
        }

        // Refill via service
        $this->machine->service([100 => 5], ['SODA' => 5]);

        $this->machine->insertCoin(100);
        $this->machine->insertCoin(25);
        $this->machine->insertCoin(25);
        $result = $this->machine->selectItem('SODA');

        $this->assertSame('SODA', $result->item());
    }

    public function test_it_rejects_an_unaccepted_coin_denomination(): void
    {
        $this->expectException(InvalidCoin::class);
        $this->machine->insertCoin(3);  // 3¢ is not a valid denomination
    }

    public function test_it_rejects_an_unknown_product_selector(): void
    {
        $this->machine->insertCoin(100);

        $this->expectException(InvalidSelector::class);
        $this->machine->selectItem('COFFEE');
    }
}
