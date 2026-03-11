# Vending Machine

PHP implementation of a vending machine domain model using **hexagonal architecture**, designed for a single machine instance and ready to scale to a network of identical machines.

## Goal

Implement the vending machine challenge in PHP while keeping the domain model clean, testable, and ready for future evolution into a service-oriented system.

The design solves the challenge for one machine instance, but assumes multiple machines can exist in a wider network and differ only by their persisted resource state.

## Core Assumption

All vending machines are treated as functionally identical. They share:
- supported coin denominations
- product catalog and prices
- vending rules and change-making strategy
- service behaviour

Machines differ only by runtime state: `machineId`, item inventory, available change, and currently inserted money.

## Architecture Overview

The project follows **hexagonal architecture** with four explicit zones.

```
Domain/
  VendingMachineInterface      ← contract for the machine
  VendingMachine               ← core implementation
  MachineConfig                ← shared, immutable config
  MachineState                 ← mutable per-machine state
  ProductDefinition
  VendResult
  ChangeStrategyInterface
  GreedyChangeStrategy
  Exception/                   ← InsufficientFunds, ItemOutOfStock, …

Application/
  VendingMachineService        ← orchestrates use cases
  MachineFactory
  Command/                     ← InsertCoin, SelectItem, ReturnCoin, ServiceMachine

Port/In/
  VendingMachineUseCaseInterface

Port/Out/
  MachineRepositoryInterface
  EventPublisherInterface

Adapter/In/Cli/
  DemoRunner

Adapter/Out/Persistence/
  InMemoryMachineRepository

Adapter/Out/Event/
  NullEventPublisher
```

See `docs/hexagonal-architecture.md` for the full architectural decision record.

## How It Works

A request flows through four layers before any state changes.
Example: `insertCoin('machine-1', 100)` followed by `selectItem('machine-1', 'WATER')`:

```
CLI / Test
    │  SelectItemCommand('machine-1', 'WATER')
    ▼
VendingMachineService          application layer — no business logic
    │  repository->findById('machine-1')
    │  machine->selectItem('WATER')
    │  repository->save(machine)
    ▼
VendingMachine                 domain — all rules live here
    │  config->hasProduct()    → valid selector?
    │  state->insertedTotal()  → enough money?
    │  state->itemCount()      → in stock?
    │  changeStrategy->compute(35¢, tentative inventory)  → change possible?
    │  state->absorb()         → accept inserted coins into the till
    │  state->decrementCoin()  → dispense change
    │  state->decrementItem()  → dispense item
    └─ return VendResult('WATER', [25, 10])
```

`MachineConfig` and `GreedyChangeStrategy` are stateless and shared across all machine instances.
`MachineState` is per-machine — each machine owns its own item inventory, coin inventory, and inserted coins.

Multiple machines are simply multiple `VendingMachine` objects keyed by ID inside the repository:

```
InMemoryMachineRepository.machines = [
    'machine-1'  => VendingMachine { state: { inserted: [25],  coins: […], items: […] } }
    'machine-2'  => VendingMachine { state: { inserted: [],    coins: […], items: […] } }
    'machine-42' => VendingMachine { state: { inserted: [100], coins: […], items: […] } }
]
```

## Current Bottlenecks and Future Upgrades

| Bottleneck | Impact | Upgrade path |
|---|---|---|
| `InMemoryMachineRepository` — state lives in process memory | All machine states are lost on restart | Swap in a `DoctrineRepository` or `RedisRepository` that implements `MachineRepositoryInterface` — no other code changes required |
| No concurrency control | Two simultaneous requests to the same machine can produce a lost update (both threads read stock=1, both vend, stock goes to 0 twice) | Add optimistic locking: `findById` returns a versioned snapshot; `save` throws `ConcurrencyException` if the version is stale |
| `NullEventPublisher` — domain events are discarded | Restocking alerts, audit logs, and billing systems cannot react to machine activity | Replace with a `KafkaEventPublisher` or `RabbitMQEventPublisher` implementing `EventPublisherInterface`; publish `ItemVendedEvent`, `CoinsReturnedEvent`, etc. |
| `MachineConfig::createDefault()` — one config for all machines | Cannot give individual machines different products, prices, or accepted denominations | Load config per `machineId` from a `ConfigRepository`; `MachineFactory::create()` already accepts an injected `MachineConfig` |
| Single entry point (`DemoRunner` CLI) | No way to drive the machine over HTTP or gRPC | Add an `Adapter\In\Http\MachineController` that maps HTTP requests to the same `VendingMachineUseCaseInterface` port |

The boundary that protects all of the above: `VendingMachineService` depends only on `MachineRepositoryInterface` and `EventPublisherInterface`. Swapping any adapter never touches the domain or application layers.

## Supported Behaviour

Accepted coin denominations (in cents): `5`, `10`, `25`, `100`

Products:

| Selector | Name  | Price  |
|----------|-------|--------|
| WATER    | Water | $0.65  |
| JUICE    | Juice | $1.00  |
| SODA     | Soda  | $1.50  |

## Example Scenarios

### Buy Soda with exact change
```
INSERT 1.00, INSERT 0.25, INSERT 0.25, SELECT SODA
→ SODA, change: none
```

### Return inserted money
```
INSERT 0.10, INSERT 0.10, RETURN-COIN
→ 0.10, 0.10
```

### Buy Water and receive change
```
INSERT 1.00, SELECT WATER
→ WATER, change: 0.25, 0.10
```

## Requirements

- PHP 8.2+
- Composer

Optional:
- Docker 20+
- docker-compose

## Running Locally

```bash
composer install
vendor/bin/phpunit
```

## Running with Docker

Run tests in the container:
```bash
docker-compose run --rm tests
```

Run the CLI demo:
```bash
docker-compose run --rm demo
```

Or build and run directly:
```bash
docker build -t vending-machine .
docker run --rm vending-machine
```

## Test Coverage

| Suite | Tests | Assertions |
|-------|-------|-----------|
| Controlling (high-level) | 3 | 5 |
| Domain rule & error handling | 8 | 8 |
| Isolated unit & integration | 6 | 10 |
| **Total** | **17** | **23** |

All 17 tests pass locally and inside the Docker container.

## Documentation

- `docs/architecture-notes.md` — core assumptions and implementation direction
- `docs/hexagonal-architecture.md` — layer structure and port/adapter boundaries
- `docs/implementation-approach.md` — TDD strategy and phased plan
- `docs/todo.md` — implementation progress tracker
