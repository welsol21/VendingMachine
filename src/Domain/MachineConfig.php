<?php

declare(strict_types=1);

namespace VendingMachine\Domain;

/**
 * Shared, immutable configuration for a vending machine.
 *
 * Holds the accepted coin denominations and the product catalog.
 * A single MachineConfig instance can be shared across multiple machines.
 */
final class MachineConfig
{
    /** @var array<string, ProductDefinition> */
    private readonly array $catalog;

    /**
     * @param int[]               $denominations Accepted coin values in cents, ordered largest-first.
     * @param ProductDefinition[] $products
     */
    public function __construct(
        private readonly array $denominations,
        array $products,
    ) {
        $catalog = [];
        foreach ($products as $product) {
            $catalog[$product->selector()] = $product;
        }
        $this->catalog = $catalog;
    }

    public static function createDefault(): self
    {
        return new self(
            denominations: [100, 25, 10, 5],
            products: [
                new ProductDefinition('SODA',  'Soda',  150),
                new ProductDefinition('JUICE', 'Juice', 100),
                new ProductDefinition('WATER', 'Water',  65),
            ],
        );
    }

    /** @return int[] Coin denominations ordered largest-first. */
    public function denominations(): array { return $this->denominations; }

    public function product(string $selector): ProductDefinition
    {
        return $this->catalog[$selector];
    }

    public function hasProduct(string $selector): bool
    {
        return isset($this->catalog[$selector]);
    }

    public function hasDenomination(int $cents): bool
    {
        return in_array($cents, $this->denominations, true);
    }

    /** @return array<string, ProductDefinition> */
    public function catalog(): array { return $this->catalog; }
}
