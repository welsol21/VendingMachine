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
