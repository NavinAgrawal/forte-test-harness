# Git Workflow

Applies to: Claude Code AND Codex. Code projects with git repos.

## Commit Message Format

```
<type>: <description>

<optional body explaining WHY, not what>
```

**Types**: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `perf`, `ci`

Examples:
- `feat: add Canadian EFT routing number validation`
- `fix: prevent NSF transactions from retrying after R01 return`
- `refactor: extract payment processor into separate module`

## Branch Naming

```
<type>/<short-description>
```
Examples: `feat/canadian-eft`, `fix/routing-validation`, `refactor/payment-module`

## Pull Request Workflow

1. Analyze **full commit history** (not just latest commit)
2. Use `git diff [base-branch]...HEAD` to see all changes
3. Draft comprehensive PR summary with test plan
4. Push with `-u` flag for new branches

## Feature Implementation Flow

1. **Plan** - Use planner/architect agent for complex features
2. **TDD** - Write tests first (RED → GREEN → REFACTOR)
3. **Review** - Use code-reviewer agent after writing code
4. **Violations** - Run `/violations` before committing
5. **Commit** - Conventional commit format, detailed messages
6. **PR** - Include summary, test plan, and linked issues

## Safety Rules

- NEVER force-push to main/master
- NEVER use `--no-verify` to skip hooks
- NEVER amend published commits
- Always create NEW commits after hook failures (don't amend)
- Stage specific files (not `git add -A`) to avoid committing secrets
