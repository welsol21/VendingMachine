<?php

declare(strict_types=1);

namespace VendingMachine\Tests;

use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\VendingMachine;

/**
 * High-level controlling tests.
 *
 * These tests describe the expected system behaviour from the outside.
 * All coins and prices are represented as integers in cents.
 *
 *   5  →  0.05
 *  10  →  0.10
 *  25  →  0.25
 * 100  →  1.00
 *  65  →  0.65  (WATER)
 * 150  →  1.50  (SODA)
 *
 * Phase 2 goal: tests exist and FAIL.
 * Phase 3 goal: tests exist and PASS.
 */
class VendingMachineTest extends TestCase
{
    private VendingMachine $machine;

    protected function setUp(): void
    {
        $this->machine = VendingMachine::createDefault();
    }

    /**
     * Scenario 1 — Buy SODA with exact change.
     *
     * Insert: 1.00 + 0.25 + 0.25 = 1.50
     * Select: SODA (costs 1.50)
     * Expect: item = SODA, change = []
     */
    public function test_it_vends_soda_with_exact_change(): void
    {
        $this->machine->insertCoin(100);
        $this->machine->insertCoin(25);
        $this->machine->insertCoin(25);

        $result = $this->machine->selectItem('SODA');

        $this->assertSame('SODA', $result->item());
        $this->assertSame([], $result->change());
    }

    /**
     * Scenario 2 — Return inserted coins.
     *
     * Insert: 0.10 + 0.10
     * Action: returnCoins()
     * Expect: [10, 10]
     */
    public function test_it_returns_inserted_coins(): void
    {
        $this->machine->insertCoin(10);
        $this->machine->insertCoin(10);

        $coins = $this->machine->returnCoins();

        $this->assertSame([10, 10], $coins);
    }

    /**
     * Scenario 3 — Buy WATER and receive change.
     *
     * Insert: 1.00
     * Select: WATER (costs 0.65)
     * Expect: item = WATER, change = [25, 10] (greedy: 25 + 10 = 35 cents)
     */
    public function test_it_vends_water_and_returns_change(): void
    {
        $this->machine->insertCoin(100);

        $result = $this->machine->selectItem('WATER');

        $this->assertSame('WATER', $result->item());
        $this->assertSame([25, 10], $result->change());
    }
}
