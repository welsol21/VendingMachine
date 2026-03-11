# TODO

This document tracks the implementation progress of the Vending Machine project.

It follows the phased approach defined in `docs/implementation-approach.md` and the structural rules defined in `docs/hexagonal-architecture.md`.

**Measurable result for every phase: all high-level controlling tests are green.**

---

## Time Tracking

| Phase | Started | Completed | Duration |
|-------|---------|-----------|----------|
| Phase 1 — Project Setup | 2026-03-10 | 2026-03-11 | ~2h |
| Phase 2 — First Controlling Tests | 2026-03-11 | 2026-03-11 | ~25m |
| Phase 3 — Minimal Implementation | 2026-03-11 | 2026-03-11 | ~20m |
| Phase 4 — Extract Core Domain Model | 2026-03-11 | 2026-03-11 | ~30m |
| Phase 5 — Change Strategy | — | — | — |
| Phase 6 — Application Coordination | — | — | — |
| Phase 7 — Ports | — | — | — |
| Phase 8 — Adapters | — | — | — |
| Phase 9 — Domain Rule Tests | — | — | — |
| Phase 10 — Isolated Unit Tests | — | — | — |
| Phase 11 — Error Handling | — | — | — |
| Phase 12 — Docker Support | — | — | — |
| Phase 13 — Finalize Documentation | — | — | — |
| **Total** | 2026-03-10 | — | — |

---

## Phase 1 — Project Setup

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Repository & initial commit | 2026-03-10 | 2026-03-10 | ~1h |
| README + .gitignore | 2026-03-10 | 2026-03-10 | ~30m |
| docs/architecture-notes.md | 2026-03-10 | 2026-03-10 | ~30m |
| docs/hexagonal-architecture.md | 2026-03-10 | 2026-03-10 | ~20m |
| docs/implementation-approach.md | 2026-03-10 | 2026-03-10 | ~20m |
| docs/todo.md | 2026-03-10 | 2026-03-10 | ~20m |
| composer.json | 2026-03-10 | 2026-03-10 | ~20m |
| PHPUnit configuration | 2026-03-11 | 2026-03-11 | ~10m |
| src/ directory structure | 2026-03-11 | 2026-03-11 | ~10m |
| Dockerfile | 2026-03-11 | 2026-03-11 | ~10m |

- [x] Create public repository
- [x] Create initial commit
- [x] Add `README.md`
- [x] Add `.gitignore`
- [x] Add `docs/architecture-notes.md`
- [x] Add `docs/hexagonal-architecture.md`
- [x] Add `docs/implementation-approach.md`
- [x] Add `composer.json`
- [x] Add PHPUnit configuration
- [x] Add `src/` directory structure
- [x] Add Dockerfile
- [ ] Optionally add `docker-compose.yml`

> ✅ Phase complete when: project runs `composer install` and `vendor/bin/phpunit` without errors.

---

## Phase 2 — Write First High-Level Controlling Tests

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| PHPUnit test bootstrap | 2026-03-11 | 2026-03-11 | ~5m |
| it_vends_soda_with_exact_change | 2026-03-11 | 2026-03-11 | ~10m |
| it_returns_inserted_coins | 2026-03-11 | 2026-03-11 | ~5m |
| it_vends_water_and_returns_change | 2026-03-11 | 2026-03-11 | ~5m |

- [x] Set up PHPUnit test bootstrap
- [x] Write `it_vends_soda_with_exact_change`
- [x] Write `it_returns_inserted_coins`
- [x] Write `it_vends_water_and_returns_change`

> ✅ Phase complete when: all three tests exist and **fail**.

---

## Phase 3 — Minimal Implementation to Pass First Tests

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Minimal entry point | 2026-03-11 | 2026-03-11 | ~10m |
| it_vends_soda_with_exact_change green | 2026-03-11 | 2026-03-11 | — |
| it_returns_inserted_coins green | 2026-03-11 | 2026-03-11 | — |
| it_vends_water_and_returns_change green | 2026-03-11 | 2026-03-11 | — |

- [x] Create minimal entry point for tests to run against
- [x] Make `it_vends_soda_with_exact_change` pass
- [x] Make `it_returns_inserted_coins` pass
- [x] Make `it_vends_water_and_returns_change` pass

> ✅ Phase complete when: **all three high-level tests are green**.

---

## Phase 4 — Extract Core Domain Model

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| ProductDefinition | 2026-03-11 | 2026-03-11 | ~5m |
| MachineConfig | 2026-03-11 | 2026-03-11 | ~5m |
| MachineState | 2026-03-11 | 2026-03-11 | ~10m |
| VendResult | 2026-03-11 | 2026-03-11 | done |
| VendingMachineInterface | 2026-03-11 | 2026-03-11 | ~5m |
| VendingMachine | 2026-03-11 | 2026-03-11 | ~10m |
| Business rules implementation | 2026-03-11 | 2026-03-11 | done |

- [x] Create `Domain/ProductDefinition.php`
- [x] Create `Domain/MachineConfig.php` — shared config (denominations, catalog, prices)
- [x] Create `Domain/MachineState.php` — per-machine mutable state
- [x] Create `Domain/VendResult.php`
- [x] Create `Domain/VendingMachineInterface.php` with:
  - [x] `id()`
  - [x] `insertCoin(...)`
  - [x] `selectItem(...)`
  - [x] `returnCoins()`
  - [x] `service(...)`
  - [x] `snapshot()` or equivalent
- [x] Create `Domain/VendingMachine.php` — inject machineId, MachineConfig, MachineState
- [x] Implement coin insertion
- [x] Implement product selection with validation
- [x] Implement return coin
- [x] Implement service/refill operation
- [x] Ensure inserted money resets after purchase
- [x] Ensure inserted money resets after return
- [x] Ensure inserted coins become part of machine funds on successful purchase

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 5 — Introduce Change Strategy

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| ChangeStrategyInterface | — | — | — |
| GreedyChangeStrategy | — | — | — |
| Inject into VendingMachine | — | — | — |

- [ ] Create `Domain/ChangeStrategyInterface.php`
- [ ] Create `Domain/GreedyChangeStrategy.php`
- [ ] Ensure strategy works with limited coin counts
- [ ] Return failure when exact change cannot be formed
- [ ] Inject strategy into `VendingMachine`

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 6 — Introduce Application Coordination

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| MachineFactory | — | — | — |
| Command objects | — | — | — |
| VendingMachineService | — | — | — |

- [ ] Create `Application/MachineFactory.php`
- [ ] Create `Application/Command/InsertCoinCommand.php`
- [ ] Create `Application/Command/SelectItemCommand.php`
- [ ] Create `Application/Command/ReturnCoinCommand.php`
- [ ] Create `Application/Command/ServiceMachineCommand.php`
- [ ] Create `Application/VendingMachineService.php`

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 7 — Introduce Ports

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Port/In/VendingMachineUseCaseInterface | — | — | — |
| Port/Out/MachineRepositoryInterface | — | — | — |
| Port/Out/EventPublisherInterface | — | — | — |

- [ ] Create `Port/In/VendingMachineUseCaseInterface.php`
- [ ] Create `Port/Out/MachineRepositoryInterface.php`
- [ ] Create `Port/Out/EventPublisherInterface.php`
- [ ] Make `VendingMachineService` depend on ports, not adapters

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 8 — Introduce Adapters

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| InMemoryMachineRepository | — | — | — |
| NullEventPublisher | — | — | — |
| Cli/DemoRunner | — | — | — |

- [ ] Create `Adapter/Out/Persistence/InMemoryMachineRepository.php`
- [ ] Create `Adapter/Out/Event/NullEventPublisher.php`
- [ ] Create `Adapter/In/Cli/DemoRunner.php`

> ✅ Phase complete when: **all three high-level tests are still green**.

---

## Phase 9 — Add Domain Rule Tests

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Failure scenario tests | — | — | — |
| State reset tests | — | — | — |
| Service update test | — | — | — |

- [ ] `it_fails_when_not_enough_money_was_inserted`
- [ ] `it_fails_when_item_is_out_of_stock`
- [ ] `it_fails_when_exact_change_cannot_be_returned`
- [ ] `it_resets_inserted_money_after_successful_purchase`
- [ ] `it_resets_inserted_money_after_return`
- [ ] `it_updates_resources_after_service`

> ✅ Phase complete when: **all high-level and domain rule tests are green**.

---

## Phase 10 — Add Isolated Unit Tests

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Change strategy unit tests | — | — | — |
| Service integration tests | — | — | — |
| Multi-machine test | — | — | — |

- [ ] `greedy_change_strategy_returns_expected_coins`
- [ ] `greedy_change_strategy_fails_when_inventory_is_insufficient`
- [ ] `service_loads_and_saves_machine_state_correctly`
- [ ] `multiple_machine_ids_behave_independently`

> ✅ Phase complete when: **all tests across all layers are green**.

---

## Phase 11 — Add Error Handling

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Domain exceptions | — | — | — |

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

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| Dockerfile | — | — | — |
| docker-compose.yml | — | — | — |
| Verify container | — | — | — |

- [ ] Add `Dockerfile`
- [ ] Optionally add `docker-compose.yml`
- [ ] Verify `composer install` works inside container
- [ ] Verify tests pass inside container
- [ ] Verify demo runner works inside container

> ✅ Phase complete when: **all tests pass inside the container**.

---

## Phase 13 — Finalize Documentation

| Task | Started | Completed | Duration |
|------|---------|-----------|----------|
| README final update | — | — | — |
| Docs consistency review | — | — | — |

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
