<?php

declare(strict_types=1);

namespace VendingMachine\Domain\Exception;

final class InvalidSelector extends \InvalidArgumentException
{
    public function __construct(string $selector)
    {
        parent::__construct("Unknown product selector: '{$selector}'");
    }
}
