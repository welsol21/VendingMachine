<?php

declare(strict_types=1);

namespace VendingMachine\Tests;

use PHPUnit\Framework\TestCase;
use VendingMachine\Domain\GreedyChangeStrategy;

/**
 * Isolated unit tests for GreedyChangeStrategy.
 */
class GreedyChangeStrategyTest extends TestCase
{
    private GreedyChangeStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new GreedyChangeStrategy();
    }

    public function test_greedy_change_strategy_returns_expected_coins(): void
    {
        $available = [100 => 2, 25 => 5, 10 => 5, 5 => 5];

        $this->assertSame([25, 10], $this->strategy->compute(35, $available));
        $this->assertSame([25, 25], $this->strategy->compute(50, $available));
        $this->assertSame([],       $this->strategy->compute(0,  $available));
    }

    public function test_greedy_change_strategy_fails_when_inventory_is_insufficient(): void
    {
        // Only dollar coins — cannot make 35¢
        $available = [100 => 5, 25 => 0, 10 => 0, 5 => 0];

        $this->assertNull($this->strategy->compute(35, $available));
    }

    public function test_greedy_change_strategy_respects_coin_count_limits(): void
    {
        // Only one 25¢ coin available; need 50¢ — can't do it with just one 25¢
        $available = [25 => 1, 10 => 0, 5 => 0];

        $this->assertNull($this->strategy->compute(50, $available));
    }
}
