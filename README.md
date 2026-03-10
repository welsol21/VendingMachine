# Vending Machine
PHP implementation of a vending machine domain model using **hexagonal architecture**, designed for a single machine instance and ready to scale to a network of identical machines.
## Goal
The goal of this project is to implement the vending machine challenge in PHP while keeping the domain model clean, testable, and ready for future evolution into a service-oriented or microservice-based system.
The implementation solves the challenge for one vending machine instance, but the design assumes that multiple machines can exist in a wider network and differ only by their persisted resource state.
## Core Assumption
All vending machines are treated as functionally identical.
They share the same:
- supported money denominations
- product catalog
- product prices
- vending rules
- change-making rules
- service behavior
Machines differ only by runtime resource state, such as:
- `machineId`
- item inventory
- available change
- currently inserted money
This allows the codebase to stay simple while remaining ready for multi-machine evolution.
## Design Direction
Instead of introducing multiple vending machine subclasses, this project uses:
- one stable machine contract
- one machine implementation
- one shared machine configuration
- one per-machine mutable state
- one factory for machine reconstitution
- one repository abstraction
- one application service
- one replaceable change-making strategy
This means a new machine in the system is not a new class. It is simply a new machine identifier and a new persisted state record using the same domain logic.
## Working Approach
The project will be developed with the help of an **AI agent**.
The first implementation step is to prepare a high-quality AI agent prompt based on the architectural decisions captured in:
- `docs/architecture-notes.md`
This document serves as the main architecture and design note for the project. It captures the core assumptions, boundaries, abstractions, and implementation direction that will guide the coding process.
## Planned Development Flow
1. Create and refine the AI agent prompt based on `docs/architecture-notes.md`
2. Generate the initial project structure
3. Implement the core domain model
4. Implement the change-making strategy
5. Implement repository and application service layers
6. Add tests
7. Add Docker support
8. Finalize documentation and usage examples
## Architecture Overview
The project follows a **hexagonal architecture** with four explicit zones: `Domain`, `Application`, `Port`, `Adapter`.
See `docs/hexagonal-architecture.md` for the full architectural decision.
### Domain
- `VendingMachineInterface`
- `VendingMachine`
- `MachineConfig`
- `MachineState`
- `ProductDefinition`
- `VendResult`
- `ChangeStrategyInterface`
- `GreedyChangeStrategy`
- `Exception/` — domain exceptions
### Application
- `VendingMachineService`
- `MachineFactory`
- `Command/` — InsertCoin, SelectItem, ReturnCoin, ServiceMachine
### Port/In
- `VendingMachineUseCaseInterface`
### Port/Out
- `MachineRepositoryInterface`
- `EventPublisherInterface`
### Adapter/In
- `Cli/DemoRunner`
### Adapter/Out
- `Persistence/InMemoryMachineRepository`
- `Event/NullEventPublisher`
## Transaction Flow
A purchase flow is expected to work like this:
1. Load machine state by `machineId`
2. Reconstitute machine from shared config and persisted state
3. Apply user action
4. Validate:
   - item exists
   - item is in stock
   - enough money has been inserted
   - exact change can be returned
5. Apply state changes atomically
6. Save updated machine state
7. Return vend result
## Supported Challenge Behavior
The machine accepts:
- `0.05`
- `0.10`
- `0.25`
- `1.00`
The machine provides at least these products:
- Water = `0.65`
- Juice = `1.00`
- Soda = `1.50`
Supported actions:
- insert money
- select item
- return inserted money
- service/refill machine
## Example Scenarios
### Buy Soda with exact change
~~~text
1, 0.25, 0.25, GET-SODA
-> SODA
~~~
### Return inserted money
~~~text
0.10, 0.10, RETURN-COIN
-> 0.10, 0.10
~~~
### Buy Water and receive change
~~~text
1, GET-WATER
-> WATER, 0.25, 0.10
~~~
## Why This Design
This design aims to balance:
- simplicity
- correctness
- testability
- extensibility
- readiness for multi-machine deployment
It avoids unnecessary complexity such as:
- multiple machine subclasses
- distributed locks
- real message brokers
- full CQRS/event sourcing setup
- fleet orchestration concerns inside the core domain
## Requirements
Planned runtime requirements:
- PHP 8.x
- Composer
- PHPUnit
Optional:
- Docker
- docker-compose
## Running the Project
Instructions will be finalized as implementation progresses.
Expected local flow:
~~~bash
composer install
php bin/demo.php
~~~
## Running Tests
Expected test flow:
~~~bash
vendor/bin/phpunit
~~~
## Documentation
Project design notes are maintained in:
- `docs/architecture-notes.md` — core assumptions, boundaries, implementation direction, and TODO phases
- `docs/hexagonal-architecture.md` — hexagonal architecture decision: layer structure, port/adapter boundaries, and final project layout
- `docs/implementation-approach.md` — TDD strategy, outside-in development flow, and phased implementation plan
## Status
This repository follows an implementation-first workflow guided by architecture notes and AI agent-assisted development.
The design is intentionally prepared for future growth from a single vending machine model to a reusable domain component for multiple machine instances.
