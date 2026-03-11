<?php

declare(strict_types=1);

namespace VendingMachine\Adapter\Out\Event;

use VendingMachine\Port\Out\EventPublisherInterface;

/**
 * No-op publisher — silently discards all events.
 *
 * Used in testing and demo contexts where event side-effects are not needed.
 */
final class NullEventPublisher implements EventPublisherInterface
{
    public function publish(object $event): void {}
}
