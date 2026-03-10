# Implementation Approach

## Purpose

This document defines the implementation approach for the Vending Machine project.

The goal is to implement the service using:

- a simple hexagonal architecture
- a domain-first design
- TDD as the primary development methodology
- an incremental workflow supported by an AI agent

The approach must remain practical, controlled, and easy to evolve without introducing unnecessary complexity.

---

## Core Implementation Principle

The implementation will follow a **TDD-driven, outside-in approach**.

This means we begin with a small number of high-level tests that describe the expected behavior of the system as a whole.

Those tests will initially fail.

Then we implement the smallest possible behavior needed to make them pass, even if the first green step is temporarily simplified or partially hardcoded.

Once the tests are green, we refactor toward the intended domain model and architectural structure.

This gives us:

- early control over end-to-end behavior
- fast validation of the direction of the design
- freedom to evolve internals safely
- a gradual path from behavior to architecture

---

## TDD Strategy

The project will use a combination of:

- **high-level controlling tests**
- **incremental minimal implementation**
- **progressive refactoring**
- **lower-level unit tests as the model emerges**

This is appropriate for the project because the vending machine challenge has a small number of very clear business scenarios that can define the system from the outside.

Examples include:

- buying Soda with exact change
- returning inserted coins
- buying Water and receiving change
- failing when funds are insufficient
- failing when the item is out of stock
- failing when exact change cannot be returned

These scenarios are ideal as the first tests that control the direction of the implementation.

---

## Outside-In Development Flow

The implementation will proceed in the following pattern:

1. write a high-level test for a system behavior
2. run it and confirm it fails
3. implement the smallest possible logic to make it pass
4. keep the first implementation intentionally minimal
5. refactor toward proper domain objects and boundaries
6. add more specific tests around the newly extracted logic
7. repeat

This allows the codebase to grow from observable behavior rather than from speculative structure.

---

## Temporary Hardcoding Policy

Temporary hardcoding is allowed only as a **transitional TDD step**.

This means:

- it is acceptable to use a simplified implementation to achieve the first green result
- it is not acceptable to leave the solution as a collection of scenario-specific hardcoded branches
- any temporary hardcoding must be replaced by generalized business logic during refactoring

In other words:

- **hardcoding is allowed as a ladder**
- **hardcoding is not allowed as the final architecture**

This distinction is important.

The goal is not to fake behavior, but to move quickly from a failing scenario to a controlled implementation that can then be generalized properly.

---

## Why This Approach Fits This Project

This project is a good fit for outside-in TDD because:

- the expected behaviors are concrete
- the scenarios are easy to express
- the domain rules are small enough to emerge iteratively
- the architecture must stay clean while still supporting future growth
- the service is intended to evolve into a reusable component in a wider microservice landscape

A behavior-first approach helps ensure that architecture emerges in service of actual use cases rather than abstract assumptions.

---

## Relationship Between TDD and Hexagonal Architecture

TDD does not conflict with hexagonal architecture.

In this project, TDD will help us discover and stabilize the boundaries of the hexagonal design.

The expected relationship is:

- high-level tests exercise the application through a use-case boundary
- domain logic is extracted as behavior becomes clearer
- ports are introduced when the application needs explicit boundaries
- adapters remain replaceable because behavior is already protected by tests

This means the architecture is not merely declared in folders.

It is built and verified through the test suite.

---

## Initial Test Layers

The implementation will use three test layers.

### 1. High-Level Controlling Tests

These are the first tests written.

They describe the system behavior from the outside and ensure the project is moving in the correct direction.

Examples:

- `it_vends_soda_with_exact_change`
- `it_returns_inserted_coins`
- `it_vends_water_and_returns_change`

These tests should initially fail and then become the first green targets.

### 2. Domain Rule Tests

Once the domain begins to emerge, we add more focused tests for business rules.

Examples:

- `it_fails_when_not_enough_money_was_inserted`
- `it_fails_when_item_is_out_of_stock`
- `it_fails_when_exact_change_cannot_be_returned`

These tests help us evolve from a scenario-driven implementation toward a real domain model.

### 3. Isolated Unit Tests

After the core objects are extracted, we add targeted tests for internal components.

Examples:

- `greedy_change_strategy_returns_expected_coins`
- `greedy_change_strategy_fails_when_inventory_is_insufficient`
- `service_updates_machine_resources`
- `inserted_money_is_reset_after_successful_purchase`
- `inserted_money_is_reset_after_return_coin`

These tests allow safe refactoring and clarify responsibilities inside the model.

---

## Initial High-Level Scenarios

The project should begin with a very small set of controlling tests.

The first recommended scenarios are:

### Scenario 1 — Buy Soda with Exact Change

Input:
```text
1, 0.25, 0.25, GET-SODA
```
Expected output:
```text
SODA
```

### Scenario 2 — Return Inserted Money

Input:
```text
0.10, 0.10, RETURN-COIN
```
Expected output:
```text
0.10, 0.10
```

### Scenario 3 — Buy Water and Receive Change

Input:
```text
1, GET-WATER
```
Expected output:
```text
WATER, 0.25, 0.10
```

These three scenarios are enough to bootstrap the first implementation cycle.

---

## Recommended Development Sequence

### Phase 1 — Control the System from the Outside

Start with a small number of high-level tests that verify the challenge examples.

At this stage, the purpose is not to perfect the architecture, but to control the direction of the implementation.

Target:

- get the first three scenarios under test
- make them fail
- achieve the first green state with minimal implementation

### Phase 2 — Extract the Core Domain Model

Once the first scenarios are green, begin replacing temporary logic with proper domain structures.

Expected emerging elements:

- `VendingMachine`
- `MachineState`
- `MachineConfig`
- `ProductDefinition`
- `VendResult`

At this stage, move business rules into the domain and remove scenario-specific logic.

### Phase 3 — Introduce the Change Strategy

As soon as change-making becomes a visible concern, extract it behind an interface.

Expected elements:

- `ChangeStrategyInterface`
- `GreedyChangeStrategy`

This allows the change logic to be tested independently and kept separate from the vending machine aggregate.

### Phase 4 — Introduce Application Coordination

Once the domain object exists, introduce the orchestration layer.

Expected elements:

- `MachineFactory`
- `VendingMachineService`

This layer should handle:

- loading machine state
- reconstituting the machine
- invoking behavior
- saving state
- returning results

### Phase 5 — Introduce Ports and Adapters

After the behavior and orchestration are stable, formalize the boundaries.

Expected ports:

- `Port/In/VendingMachineUseCaseInterface.php`
- `Port/Out/MachineRepositoryInterface.php`
- `Port/Out/EventPublisherInterface.php`

Expected adapters:

- `Adapter/In/Cli/DemoRunner.php`
- `Adapter/Out/Persistence/InMemoryMachineRepository.php`
- `Adapter/Out/Event/NullEventPublisher.php`

This is the point where the project becomes structurally hexagonal in a clear and verifiable way.

### Phase 6 — Expand the Test Suite

After the first implementation passes the basic scenarios, add broader rule coverage.

Recommended additions:

- insufficient funds
- out of stock
- insufficient change
- inserted money reset behavior
- service updates
- multi-machine independence through repository state

These tests protect both correctness and future refactoring.

---

## Final Structural Target

The implementation is expected to converge toward the following structure:

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

## Role of the AI Agent

The project will be implemented with the support of an AI agent.

The AI agent is not a substitute for the architecture or the development methodology.

Its role is to assist with implementation while remaining constrained by:

- the architecture notes
- this implementation approach
- the TDD workflow
- the hexagonal boundaries of the project

The first AI agent task should therefore be to generate code only after being given:

- the architecture notes
- the hexagonal structure decision
- this implementation approach
- the first target test cases

This ensures the generated code follows the intended design rather than inventing uncontrolled structure.

---

## Practical TDD Rule Set

The project should follow these rules during implementation:

1. do not start from random class generation
2. start from a failing behavior test
3. make the smallest possible change to reach green
4. refactor immediately after green
5. extract business rules into the domain
6. introduce ports only when the boundary becomes real
7. keep adapters thin
8. keep the domain pure
9. never leave temporary hardcoding as final logic
10. let architecture emerge under the protection of tests

---

## What We Explicitly Avoid

To keep the implementation proportional and maintainable, we avoid:

- writing a large amount of structure before behavior is tested
- introducing adapters before ports are needed
- inventing abstractions without a test or behavioral reason
- keeping scenario-specific hardcoded logic in the final solution
- building fake complexity under the label of microservices
- adding buses, CQRS layers, or event sourcing infrastructure prematurely

The implementation should remain disciplined, small, and behavior-driven.

---

## Final Implementation Philosophy

The implementation should move in this order:

**behavior first → minimal green → refactor → extract domain → define ports → add adapters → expand tests**

This approach gives us the right balance between:

- TDD discipline
- architectural correctness
- controlled complexity
- future scalability
- practical delivery

---

## One-Sentence Summary

**The Vending Machine service will be implemented using outside-in TDD: starting from a small set of high-level failing behavior tests, reaching green through minimal implementation, and then progressively refactoring toward a clean hexagonal architecture with a pure domain core and explicit ports and adapters.**
