<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class InsufficientFunds extends \RuntimeException
{
    public function __construct(int $required, int $paid)
    {
        parent::__construct("Insufficient funds: need {$required}¢, got {$paid}¢");
    }
}
