<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class InsufficientChange extends \RuntimeException
{
    public function __construct(int $amount)
    {
        parent::__construct("Cannot return {$amount}¢ in change");
    }
}
