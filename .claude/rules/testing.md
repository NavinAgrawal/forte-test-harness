# Testing Requirements

Applies to: Claude Code AND Codex. Code projects only (skip for docs/content projects).

## Minimum Coverage: 80%

| Test Type | Scope | Tools |
|-----------|-------|-------|
| **Unit** | Functions, utilities, components | Jest, pytest, XCTest |
| **Integration** | API endpoints, DB operations | Supertest, pytest-asyncio |
| **E2E** | Critical user flows | Playwright |

## Test-Driven Development (MANDATORY for code projects)

1. **RED**: Write test first - it MUST fail
2. **GREEN**: Write minimal implementation to pass
3. **REFACTOR**: Clean up, verify tests still pass
4. **COVERAGE**: Verify 80%+ coverage

## Test Quality Standards

- **Naming**: `test_should_[expected]_when_[condition]` or `it('should [expected] when [condition]')`
- **Structure**: Arrange-Act-Assert (AAA) pattern
- **Isolation**: Each test independent, no shared mutable state
- **No superficial tests**: Every test must have meaningful assertions (not just `assert True`)
- **Mock discipline**: Only mock external dependencies, never mock the thing being tested
- **Error paths**: Test failure cases, edge cases, boundary conditions - not just happy path

## When Tests Fail

1. **Fix implementation, not tests** (unless tests are wrong)
2. Use **tdd-guide** agent for complex test scenarios
3. Check test isolation (shared state is #1 cause of flaky tests)
4. Verify mocks match real interfaces

## Agent Support

- **tdd-guide** - Use PROACTIVELY for new features (enforces write-tests-first)
- **e2e-runner** - Playwright E2E testing specialist
- **build-error-resolver** - When test infrastructure breaks
