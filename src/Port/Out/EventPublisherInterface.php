<?php

declare(strict_types=1);

namespace VendingMachine\Port\Out;

/**
 * Outbound port: domain event publishing.
 */
interface EventPublisherInterface
{
    public function publish(object $event): void;
}
