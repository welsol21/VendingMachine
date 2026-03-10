# Vending Machine: Architecture Idea and TODO
## Goal
Implement the vending machine challenge in PHP as a **single-machine domain model** that is **ready to scale to a network of machines**.
The implementation should solve the current task for one vending machine, but the code structure should make it easy to embed this machine into a service-oriented or microservice architecture later.
---
## Core Architectural Idea
We make a deliberate assumption:
- all vending machines are **functionally identical**
- all machines support the same money denominations
- all machines sell the same products at the same prices
- all machines use the same vending logic
- all machines use the same change-making logic
- the only thing that differs between machines is their **resource state**
So, machines do **not** differ by class or type.
They differ only by:
- `machineId`
- available item counts
- available change counts
- currently inserted money
- operational state if needed later
This lets us keep the domain clean and simple.
---
## Why This Is a Strong Design
This approach gives us the right balance between:
- solving the challenge cleanly
- avoiding unnecessary overengineering
- showing backend/system design maturity
- preparing for future multi-machine deployment
Instead of designing many vending machine classes, we design:
- one **stable machine contract**
- one **single implementation**
- one **shared static configuration**
- one **per-machine dynamic state**
- one **factory that reconstitutes a machine instance from config + state**
That means a "new vending machine in the network" is not a new class.
It is simply:
- a new `machineId`
- a new persisted state record
- the same domain logic
---
## Domain Assumptions
### Shared configuration across all machines
The following are identical for every vending machine:
- supported coins: `0.05`, `0.10`, `0.25`, `1.00`
- products:
  - Water = `0.65`
  - Juice = `1.00`
  - Soda = `1.50`
- change-making rules
- purchase flow
- return coin behavior
- service behavior
### Per-machine state
The following are unique for each machine:
- item inventory
- change inventory
- inserted money
- machine identifier
---
## Money Representation
All monetary values are stored and processed as **integers in cents** to avoid floating-point precision errors.
| Input  | Internal (cents) |
|--------|-----------------|
| `0.05` | `5`             |
| `0.10` | `10`            |
| `0.25` | `25`            |
| `1.00` | `100`           |
| `0.65` | `65`            |
| `1.50` | `150`           |
Conversion between user-facing decimal values and internal integer representation happens only at system boundaries (CLI input/output, DTOs, tests).
---
## Recommended Model
## 1. Machine Contract
Introduce a stable interface for the machine.
Example responsibilities:
- get machine id
- insert coin
- select item
- return inserted coins
- service/refill machine
- export state snapshot
The contract exists to:
- isolate domain logic
- support application services
- support future service boundaries
- improve testability
- avoid coupling infrastructure to implementation details
---
## 2. Machine Configuration
Create a shared configuration object that defines the static rules of the machine.
It should include:
- supported coin denominations
- product catalog
- product prices
This configuration is shared by all machine instances.
This is **not mutable runtime state**.
---
## 3. Machine State
Create a state object that stores the dynamic resources of a specific machine.
It should include:
- item counts
- change counts
- inserted coin counts
This is the part that changes during operation.
Every machine has its own state instance.
---
## 4. Vending Machine Aggregate
Create one concrete `VendingMachine` implementation.
It should receive:
- `machineId`
- shared `MachineConfig`
- current `MachineState`
The machine owns the business rules for:
- coin insertion
- purchase validation
- item vending
- returning inserted money
- giving change
- service updates
- resetting current inserted session after purchase/return
---
## 5. Machine Factory
Use a factory not to choose different machine classes, but to **build a machine instance from config + state**.
This is effectively an aggregate reconstitution factory.
It should:
- receive shared config
- receive machine id
- receive state
- return a working `VendingMachine`
This is useful because in a real system the application layer would:
1. load machine state from repository
2. call factory
3. get domain object
4. execute command
5. persist new state
---
## 6. Repository Interface
Create a repository abstraction for loading and saving machine state.
The repository should work by `machineId`.
At minimum it should support:
- load state by machine id
- save machine state
For the challenge, an in-memory repository is enough.
Later it can be replaced with:
- PostgreSQL
- Redis
- event store
- external machine state service
---
## 7. Application Service
Add an application service that coordinates the use case.
Responsibilities:
- load machine state from repository
- create machine via factory
- apply command/action
- save resulting state
- return result
This keeps transport/integration logic outside the domain model.
It also makes it easy to adapt the solution to:
- CLI
- HTTP API
- queue consumer
- fleet manager service
---
## 8. Change-Making Strategy
Extract the change algorithm behind an interface.
For the challenge, greedy strategy is acceptable because the denominations are simple.
But architecturally it is better to keep it replaceable.
Why:
- testability
- separation of concern
- future changes to rules
- future support for more complex denominations
---
## 9. Event-Readiness
Even if events are not required in the challenge, the design should be event-ready.
Possible future events:
- coin inserted
- item selected
- item vended
- change returned
- machine serviced
- insufficient change detected
- out of stock detected
This is useful if the machine later becomes part of a fleet platform.
For now, event publishing can be omitted or kept behind a no-op interface.
---
## Why This Fits Microservice Thinking
This design is good for microservice architecture because:
- the machine domain is isolated
- the machine has a stable contract
- persistence is abstracted
- configuration and state are separated
- machine instances are reconstituted from persistence
- the application layer can become a service boundary later
In a future system, one service could manage vending workflows while another service could consume machine events for monitoring, analytics, or replenishment planning.
---
## What We Explicitly Avoid
To keep the challenge solution clean, we do **not** introduce:
- multiple machine subclasses
- distributed locks
- real message brokers
- CQRS/event sourcing complexity
- fleet orchestration services
- infrastructure-heavy abstractions with no immediate value
The goal is to stay simple, but structurally correct.
---
## High-Level Class Structure
Suggested conceptual classes:
### Domain
- `VendingMachineInterface`
- `VendingMachine`
- `MachineConfig`
- `MachineState`
- `ProductCatalogItem` or `ProductDefinition`
- `VendResult`
- `ChangeStrategyInterface`
- `GreedyChangeStrategy`
### Application
- `VendingMachineService`
- command/request DTOs if desired
### Ports
- `MachineRepositoryInterface`
- optionally `EventPublisherInterface`
### Infrastructure
- `InMemoryMachineRepository`
- CLI adapter or simple runner
- Dockerfile / docker-compose
- PHPUnit tests
---
## Transaction Flow
A purchase should conceptually work like this:
1. Load machine state by `machineId`
2. Create machine from shared config + loaded state
3. Insert coin(s) or select item
4. Validate:
   - item exists
   - item is in stock
   - enough money inserted
   - exact change can be returned
5. Apply state changes atomically
6. Save updated machine state
7. Return vend result
---
## Important Design Rule
The domain model should not know anything about:
- HTTP
- CLI
- database implementation
- Docker
- queue systems
- monitoring tools
It should only know vending rules and machine state transitions.
---
# TODO for Implementation
## Phase 1 — Project Setup
- [ ] Create public repository
- [ ] Create initial commit immediately
- [ ] Add `README.md`
- [ ] Add PHP project structure
- [ ] Add `composer.json`
- [ ] Add PHPUnit configuration
- [ ] Add Dockerfile
- [ ] Optionally add `docker-compose.yml`
---
## Phase 2 — Define Core Domain Types
- [ ] Create value/object or model for product definition
- [ ] Create `MachineConfig`
- [ ] Create `MachineState`
- [ ] Create `VendResult`
- [ ] Define supported coin denominations in one place
- [ ] Define product catalog in one place
---
## Phase 3 — Define Machine Contract
- [ ] Create `VendingMachineInterface`
- [ ] Include methods for:
  - [ ] `id()`
  - [ ] `insertCoin(...)`
  - [ ] `selectItem(...)`
  - [ ] `returnCoins()`
  - [ ] `service(...)`
  - [ ] `snapshot()` or equivalent
---
## Phase 4 — Implement Change Strategy
- [ ] Create `ChangeStrategyInterface`
- [ ] Implement `GreedyChangeStrategy`
- [ ] Make sure it works with limited coin counts
- [ ] Return failure when exact change cannot be formed
- [ ] Add dedicated unit tests for change calculation
---
## Phase 5 — Implement Vending Machine Domain Logic
- [ ] Create concrete `VendingMachine`
- [ ] Inject:
  - [ ] machine id
  - [ ] machine config
  - [ ] machine state
  - [ ] change strategy
- [ ] Implement coin insertion
- [ ] Implement product selection
- [ ] Implement return coin
- [ ] Implement service/refill operation
- [ ] Ensure machine state is updated correctly
- [ ] Ensure inserted money resets when appropriate
- [ ] Ensure change is taken from machine inventory correctly
- [ ] Ensure inserted coins become part of machine funds on successful purchase
---
## Phase 6 — Add Factory
- [ ] Create `MachineFactoryInterface`
- [ ] Create concrete `MachineFactory`
- [ ] Pass shared config through constructor
- [ ] Add `create(machineId, state)` method
- [ ] Return a ready-to-use `VendingMachine`
---
## Phase 7 — Add Repository Port
- [ ] Create `MachineRepositoryInterface`
- [ ] Add method to load state by machine id
- [ ] Add method to save machine state
- [ ] Keep repository contract storage-agnostic
---
## Phase 8 — Add In-Memory Repository
- [ ] Implement `InMemoryMachineRepository`
- [ ] Seed sample machine state
- [ ] Make it easy to create multiple machines by id
- [ ] Use this repository for tests and demo execution
---
## Phase 9 — Add Application Service
- [ ] Create `VendingMachineService`
- [ ] Inject repository
- [ ] Inject factory
- [ ] Add methods/use cases for:
  - [ ] insert coin
  - [ ] select item
  - [ ] return coin
  - [ ] service machine
- [ ] Ensure service loads machine, executes action, saves state, returns result
---
## Phase 10 — Add Error Handling
- [ ] Create domain exceptions for:
  - [ ] invalid coin
  - [ ] invalid selector
  - [ ] item out of stock
  - [ ] insufficient funds
  - [ ] insufficient change
  - [ ] machine not found
- [ ] Make error messages clear and deterministic
---
## Phase 11 — Add Tests
### Domain tests
- [ ] buy Soda with exact change
- [ ] return inserted coins
- [ ] buy Water and receive correct change
- [ ] fail when not enough money
- [ ] fail when item is out of stock
- [ ] fail when exact change cannot be returned
- [ ] verify inserted coins reset after return
- [ ] verify inserted coins reset after successful purchase
- [ ] verify service updates resources
### Application tests
- [ ] service loads and saves machine state correctly
- [ ] multiple machine ids behave independently
### Strategy tests
- [ ] change algorithm returns correct coin combination
- [ ] change algorithm fails correctly when inventory is insufficient
---
## Phase 12 — Add Demo Runner
- [ ] Add simple CLI script or runner
- [ ] Support executing example scenarios from the challenge
- [ ] Print outputs in challenge-friendly format
- [ ] Keep runner separate from domain
---
## Phase 13 — README
- [ ] Explain architecture briefly
- [ ] Explain the "identical machines, different state only" assumption
- [ ] Explain how to run locally
- [ ] Explain how to run with Docker
- [ ] Explain how to run tests
- [ ] Show example challenge scenarios
- [ ] Mention why the design is ready for multi-machine/network evolution
---
## Phase 14 — Git Hygiene
- [ ] Commit from the beginning
- [ ] Commit per logical step
- [ ] Use readable commit messages
- [ ] Keep git history reviewable
Suggested commit flow:
1. `chore: initialize php project structure`
2. `feat: add machine config and state models`
3. `feat: add vending machine contract`
4. `feat: implement greedy change strategy`
5. `feat: implement vending machine domain logic`
6. `feat: add machine factory`
7. `feat: add repository contract and in-memory implementation`
8. `feat: add application service`
9. `test: add vending machine unit tests`
10. `docs: add readme and usage examples`
11. `build: add docker support`
---
# Final Implementation Direction
The best implementation direction is:
- one machine contract
- one machine implementation
- one shared machine configuration
- one per-machine mutable state
- one factory for reconstitution
- one repository port
- one application service
- one replaceable change strategy
This solves the coding challenge cleanly while also showing that the codebase is ready to evolve into a fleet-aware backend design.
---
# One-Sentence Summary
**Implement one vending machine model, but design it as a reusable domain component for many machine instances that differ only by persisted resource state.**
