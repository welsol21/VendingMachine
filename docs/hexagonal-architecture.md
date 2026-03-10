# Hexagonal Architecture Decision for the Vending Machine Service

## Purpose

This document defines the architectural direction for the Vending Machine project.

The service must be implemented as a **simple hexagonal architecture**, with **clear boundaries** and **no overengineering**, while remaining ready to participate in a **data-driven microservice architecture**.

The goal is to keep the codebase open for growth, easy to reason about, and easy to extend without introducing unnecessary structural complexity.

---

## Architectural Position

The project will use a **hexagonal architecture**.

This is a deliberate choice and not just a naming preference.

Because the service is intended to evolve as part of a **data-driven microservice environment**, the internal structure must reflect:

- a pure business core
- explicit input and output boundaries
- replaceable external integrations
- independence from transport, persistence, and messaging details

For this reason, the project must use the terminology and structure of **hexagonal architecture**, not a generic layered or infrastructure-first layout.

---

## Core Principle

The service is built around four explicit zones:

- `Domain`
- `Application`
- `Port`
- `Adapter`

These names are intentional and must be preserved.

They express the actual roles of the code inside the service and make the architecture understandable both for implementation and for future scaling.

---

## Why `Infrastructure` Is Not the Right Term Here

The folder name `Infrastructure` is common in layered architecture and clean architecture, but it is less precise for a strict hexagonal design.

In hexagonal architecture, what matters is not simply "technical code outside the domain," but the exact role that external code plays in relation to the core.

External code must be understood as one of two things:

- an **input adapter**
- an **output adapter**

The term `Infrastructure` hides this distinction.

It does not tell us:

- whether the code enters the system or leaves it
- which port it implements
- how it connects to the application boundary

Because of this, `Infrastructure` is too broad and too vague for a project that is explicitly based on hexagonal architecture.

If we want the architecture to be correct and self-explanatory, we should use `Adapter`, and we should separate adapters into `In` and `Out`.

---

## Final Structural Rule

The project must not use a general `Infrastructure` folder as the main external layer.

Instead, the codebase must use:

- `Port/In`
- `Port/Out`
- `Adapter/In`
- `Adapter/Out`

This makes the architecture explicit and keeps the boundaries clear.

---

## Meaning of Each Layer

## 1. Domain

The `Domain` layer contains the business core of the vending machine service.

It defines the rules, state, behavior, and invariants of the vending machine itself.

Typical contents include:

- vending machine entity or aggregate
- machine state
- machine configuration
- product definition
- vend result
- change-making strategy
- domain exceptions
- domain-level interfaces

The `Domain` layer must remain pure.

It must not know anything about:

- CLI
- HTTP
- databases
- Docker
- queues
- event brokers
- framework code
- repository implementations

The domain is responsible only for vending machine logic and state transitions.

---

## 2. Application

The `Application` layer coordinates use cases.

It does not own low-level infrastructure concerns, and it should not become a place for random business rules that belong in the domain.

Its role is to orchestrate the flow:

1. load machine state
2. reconstitute the machine
3. invoke domain behavior
4. save the updated state
5. publish events if needed
6. return the result

Typical contents include:

- application services
- command objects
- machine factory
- request/response DTOs if needed

The `Application` layer depends on ports, not on adapters.

---

## 3. Port

The `Port` layer defines the boundaries of the service.

Ports are contracts. They describe how the application can be used from the outside, and what external dependencies the application needs in order to do its work.

Ports must be split into:

- `Port/In`
- `Port/Out`

### `Port/In`

Input ports define the use cases that the service exposes to the outside world.

They describe what the application can do.

For example:

- insert coin
- select item
- return coin
- service machine

These are not transport-level handlers. They are application-level capabilities.

### `Port/Out`

Output ports define what the application needs from the outside world.

For example:

- load/save machine state
- publish domain events

Typical output ports include:

- `MachineRepositoryInterface`
- `EventPublisherInterface`

---

## 4. Adapter

The `Adapter` layer contains concrete implementations that connect the service to the outside world.

Adapters are split into:

- `Adapter/In`
- `Adapter/Out`

### `Adapter/In`

Input adapters translate external interaction into calls to input ports.

Examples include:

- CLI runner
- HTTP controller
- queue consumer
- gRPC handler

For the current version of the project, the initial input adapter can be a CLI demo runner.

### `Adapter/Out`

Output adapters implement the output ports declared by the application.

Examples include:

- in-memory repository
- PostgreSQL repository
- Redis repository
- null event publisher
- Kafka publisher

For the current version of the project, the initial output adapters can be:

- `InMemoryMachineRepository`
- `NullEventPublisher`

---

## Why This Structure Fits a Data-Driven Microservice Architecture

The service is intended to be part of a larger data-driven architecture.

That does not mean we should introduce big-data tooling or distributed systems complexity inside this project right now.

It means the service should be prepared to:

- receive commands from different channels
- manage state explicitly
- persist state through a replaceable boundary
- publish events through a replaceable boundary
- remain independent from the implementation details of storage and transport

A simple hexagonal structure gives us exactly that.

It keeps the service small and understandable, while making it ready for future integration into an event-driven or data-driven ecosystem.

---

## What We Explicitly Avoid

To keep the architecture clean and proportional to the problem, the project must avoid premature complexity.

We do not introduce, at this stage:

- generic `Infrastructure` catch-all folders
- command bus
- query bus
- shared kernel
- projection engine
- event sourcing infrastructure
- distributed lock handling
- messaging platform-specific abstractions
- unnecessary base classes
- vague utility layers such as `Helpers` or `Utils`

The architecture must stay simple, explicit, and intentional.

---

## Final Project Structure

The project structure for version 1 must be:

```text
src/
├─ Domain/
│  ├─ VendingMachine.php
│  ├─ VendingMachineInterface.php
│  ├─ MachineConfig.php
│  ├─ MachineState.php
│  ├─ ProductDefinition.php
│  ├─ VendResult.php
│  ├─ ChangeStrategyInterface.php
│  ├─ GreedyChangeStrategy.php
│  └─ Exception/
│     ├─ InvalidCoin.php
│     ├─ InvalidSelector.php
│     ├─ ItemOutOfStock.php
│     ├─ InsufficientFunds.php
│     └─ InsufficientChange.php
├─ Application/
│  ├─ MachineFactory.php
│  ├─ VendingMachineService.php
│  └─ Command/
│     ├─ InsertCoinCommand.php
│     ├─ SelectItemCommand.php
│     ├─ ReturnCoinCommand.php
│     └─ ServiceMachineCommand.php
├─ Port/
│  ├─ In/
│  │  └─ VendingMachineUseCaseInterface.php
│  └─ Out/
│     ├─ MachineRepositoryInterface.php
│     └─ EventPublisherInterface.php
└─ Adapter/
   ├─ In/
   │  └─ Cli/
   │     └─ DemoRunner.php
   └─ Out/
      ├─ Persistence/
      │  └─ InMemoryMachineRepository.php
      └─ Event/
         └─ NullEventPublisher.php
```

---

## Architectural Interpretation of This Structure

- `Domain` — the business core
- `Application` — orchestrates use cases
- `Port/In` — defines what the service offers
- `Port/Out` — defines what the service needs
- `Adapter/In` — connects external inputs to the service
- `Adapter/Out` — connects the service to external dependencies

---

## Design Conclusion

The Vending Machine project will be implemented as a simple hexagonal PHP service.

The architecture must remain:

- explicit
- modular
- boundary-driven
- easy to evolve
- free from unnecessary complexity

The correct top-level service structure is:

- `Domain`
- `Application`
- `Port`
- `Adapter`

The project must not rely on a vague `Infrastructure` layer as its main external boundary, because that would weaken the clarity of the hexagonal design.

---

## One-Sentence Summary

**The Vending Machine service uses a simple hexagonal architecture with a pure domain core, explicit input and output ports, and replaceable adapters, making it ready for a data-driven microservice environment without unnecessary complexity.**
