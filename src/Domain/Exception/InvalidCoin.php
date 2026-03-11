<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class InvalidCoin extends \InvalidArgumentException
{
    public function __construct(int $cents)
    {
        parent::__construct("Invalid coin: {$cents}¢ is not an accepted denomination");
    }
}
