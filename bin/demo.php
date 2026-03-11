<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use VendingMachine\Adapter\In\Cli\DemoRunner;
use VendingMachine\Adapter\Out\Event\NullEventPublisher;
use VendingMachine\Adapter\Out\Persistence\InMemoryMachineRepository;
use VendingMachine\Application\MachineFactory;
use VendingMachine\Application\VendingMachineService;

$factory   = MachineFactory::createDefault();
$repo      = new InMemoryMachineRepository($factory);
$publisher = new NullEventPublisher();
$service   = new VendingMachineService($repo, $publisher);

(new DemoRunner($service))->run();
