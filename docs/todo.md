# TODO

This document tracks the implementation progress of the Vending Machine project.

It follows the phased approach defined in `docs/implementation-approach.md` and the structural rules defined in `docs/hexagonal-architecture.md`.

**Measurable result for every phase: all high-level controlling tests are green.**

---

## Phase 1 — Project Setup

- [x] Create public repository
- [x] Create initial commit
- [x] Add `README.md`
- [x] Add `.gitignore`
- [x] Add `docs/architecture-notes.md`
- [x] Add `docs/hexagonal-architecture.md`
- [x] Add `docs/implementation-approach.md`
- [ ] Add `composer.json`
- [ ] Add PHPUnit configuration
- [ ] Add `src/` directory structure
- [ ] Add Dockerfile
- [ ] Optionally add `docker-compose.yml`

> ✅ Phase complete when: project runs `composer install` and `vendor/bin/phpunit` without errors.

---

## Phase 2 — Write First High-Level Controlling Tests

Start with three failing behavior tests that control the direction of the implementation.
See `docs/implementation-approach.md` for the TDD strategy.

- [ ] Set up PHPUnit test bootstrap
- [ ] Write `it_vends_soda_with_exact_change`
- [ ] Write `it_returns_inserted_coins`
- [ ] Write `it_vends_water_and_returns_change`

> ✅ Phase complete when: all three tests exist and **fail**.

---

## Phase 3 — Minimal Implementation to Pass First Tests

Implement the smallest possible logic to reach green.
Temporary hardcoding is allowed at this stage.

- [ ] Create minimal entry point for tests to run against
- [ ] Make `it_vends_soda_with_exact_change` pass
- [ ] Make `it_returns_inserted_coins` pass
- [ ] Make `it_vends_water_and_returns_change` pass

> ✅ Phase complete when: **all three high-level tests are green**.

---

## Phase 4 — Extract Core Domain Model

Replace temporary logic with proper domain structures.

- [ ] Create `Domain/ProductDefinition.php`
- [ ] Create `Domain/MachineConfig.php` — shared config (denominations, catalog, prices)
- [ ] Create `Domain/MachineState.php` — per-machine mutable state
- [ ] Create `Domain/VendResult.php`
- [ ] Create `Domain/VendingMachineInterface.php` with:
  - [ ] `id()`
  - [ ] `insertCoin(...)`
  - [ ] `selectItem(...)`
  - [ ] `returnCoins()`
  - [ ] `service(...)`
  - [ ] `snapshot()` or equivalent
- [ ] Create `Domain/VendingMachine.php` — inject machineId, MachineConfig, MachineState
- [ ] Implement coin insertion
- [ ] Implement product selection with validation
- [ ] Implement return coin
- [ ] Implement service/refill operation
- [ ] Ensure inserted money resets after purchase
- [ ] Ensure inserted money resets after return
- [ ] Ensure inserted coins become part of machine funds on successful purchase

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 5 — Introduce Change Strategy

Extract change-making logic behind an interface.

- [ ] Create `Domain/ChangeStrategyInterface.php`
- [ ] Create `Domain/GreedyChangeStrategy.php`
- [ ] Ensure strategy works with limited coin counts
- [ ] Return failure when exact change cannot be formed
- [ ] Inject strategy into `VendingMachine`

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 6 — Introduce Application Coordination

Add the orchestration layer.

- [ ] Create `Application/MachineFactory.php` — builds VendingMachine from config + state
- [ ] Create `Application/Command/InsertCoinCommand.php`
- [ ] Create `Application/Command/SelectItemCommand.php`
- [ ] Create `Application/Command/ReturnCoinCommand.php`
- [ ] Create `Application/Command/ServiceMachineCommand.php`
- [ ] Create `Application/VendingMachineService.php`:
  - [ ] load machine state from repository
  - [ ] reconstitute machine via factory
  - [ ] invoke domain behavior
  - [ ] save updated state
  - [ ] return result

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 7 — Introduce Ports

Formalize the input and output boundaries.

- [ ] Create `Port/In/VendingMachineUseCaseInterface.php`
- [ ] Create `Port/Out/MachineRepositoryInterface.php`:
  - [ ] `findById(machineId)`
  - [ ] `save(machineState)`
- [ ] Create `Port/Out/EventPublisherInterface.php`
- [ ] Make `VendingMachineService` depend on ports, not adapters

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 8 — Introduce Adapters

Add concrete implementations for all ports.

- [ ] Create `Adapter/Out/Persistence/InMemoryMachineRepository.php`
  - [ ] Seed sample machine state
  - [ ] Support multiple machines by id
- [ ] Create `Adapter/Out/Event/NullEventPublisher.php`
- [ ] Create `Adapter/In/Cli/DemoRunner.php`:
  - [ ] Support example scenarios from the challenge
  - [ ] Print output in challenge-friendly format
  - [ ] Keep runner separate from domain

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 9 — Add Domain Rule Tests

Expand test coverage for business rules.

- [ ] `it_fails_when_not_enough_money_was_inserted`
- [ ] `it_fails_when_item_is_out_of_stock`
- [ ] `it_fails_when_exact_change_cannot_be_returned`
- [ ] `it_resets_inserted_money_after_successful_purchase`
- [ ] `it_resets_inserted_money_after_return`
- [ ] `it_updates_resources_after_service`

> ✅ Phase complete when: **all high-level and domain rule tests are green**.

---

## Phase 10 — Add Isolated Unit Tests

Add focused tests for internal components.

- [ ] `greedy_change_strategy_returns_expected_coins`
- [ ] `greedy_change_strategy_fails_when_inventory_is_insufficient`
- [ ] `service_loads_and_saves_machine_state_correctly`
- [ ] `multiple_machine_ids_behave_independently`

> ✅ Phase complete when: **all tests across all layers are green**.

---

## Phase 11 — Add Error Handling

- [ ] Create `Domain/Exception/InvalidCoin.php`
- [ ] Create `Domain/Exception/InvalidSelector.php`
- [ ] Create `Domain/Exception/ItemOutOfStock.php`
- [ ] Create `Domain/Exception/InsufficientFunds.php`
- [ ] Create `Domain/Exception/InsufficientChange.php`
- [ ] Create `Domain/Exception/MachineNotFound.php`
- [ ] Make error messages clear and deterministic

> ✅ Phase complete when: **all tests across all layers are green**.

---

## Phase 12 — Docker Support

- [ ] Add `Dockerfile`
- [ ] Optionally add `docker-compose.yml`
- [ ] Verify `composer install` works inside container
- [ ] Verify tests pass inside container
- [ ] Verify demo runner works inside container

> ✅ Phase complete when: **all tests pass inside the container**.

---

## Phase 13 — Finalize Documentation

- [ ] Update `README.md` with final run instructions
- [ ] Update `README.md` with Docker instructions
- [ ] Update `docs/architecture-notes.md` if implementation diverged from plan
- [ ] Review all docs for consistency with final implementation

> ✅ Phase complete when: README reflects the actual state of the project.

---

## Suggested Commit Flow

```
chore: initialize php project structure
test: add first high-level controlling tests
feat: minimal implementation to pass controlling tests
feat: extract core domain model
feat: implement greedy change strategy
feat: add machine factory and application service
feat: introduce port contracts
feat: add in-memory repository and null event publisher
feat: add cli demo runner
test: expand domain rule and unit test coverage
feat: add domain exceptions
build: add docker support
docs: finalize readme and usage examples
```
