<?php

declare(strict_types=1);

namespace VendingMachine\Port\In;

use VendingMachine\Application\Command\InsertCoinCommand;
use VendingMachine\Application\Command\ReturnCoinCommand;
use VendingMachine\Application\Command\SelectItemCommand;
use VendingMachine\Application\Command\ServiceMachineCommand;
use VendingMachine\Domain\VendResult;

/**
 * Inbound port: what the application exposes to the outside world.
 */
interface VendingMachineUseCaseInterface
{
    public function insertCoin(InsertCoinCommand $command): void;

    public function selectItem(SelectItemCommand $command): VendResult;

    /** @return int[] */
    public function returnCoins(ReturnCoinCommand $command): array;

    public function serviceMachine(ServiceMachineCommand $command): void;
}
