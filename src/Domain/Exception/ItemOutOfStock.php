<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class ItemOutOfStock extends \RuntimeException
{
    public function __construct(string $selector)
    {
        parent::__construct("Item out of stock: {$selector}");
    }
}
