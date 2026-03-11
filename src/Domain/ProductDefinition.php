<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Immutable descriptor for a product sold by the machine.
 *
 * Money is represented as integers in cents throughout.
 */
final class ProductDefinition
{
    /**
     * @param string $selector Short uppercase key used to select the product (e.g. 'SODA').
     * @param string $name     Human-readable display name.
     * @param int    $price    Price in cents.
     */
    public function __construct(
        private readonly string $selector,
        private readonly string $name,
        private readonly int    $price,
    ) {}

    public function selector(): string { return $this->selector; }

    public function name(): string { return $this->name; }

    /** Price in cents. */
    public function price(): int { return $this->price; }
}
