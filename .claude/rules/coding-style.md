# Coding Style

Applies to: Claude Code AND Codex. All languages.

## Immutability (CRITICAL)

ALWAYS create new objects, NEVER mutate:

```javascript
// WRONG: user.name = name (MUTATION!)
// CORRECT:
const updated = { ...user, name }
```

```python
# WRONG: users.append(new_user) (MUTATION!)
# CORRECT:
updated = [*users, new_user]
```

## File Organization

MANY SMALL FILES > FEW LARGE FILES:
- High cohesion, low coupling
- 200-400 lines typical, **800 max**
- Extract utilities from large components
- Organize by feature/domain, not by type
- Functions: **50 lines max**
- Nesting: **4 levels max**

## Error Handling

```typescript
// TypeScript/JavaScript
try {
  const result = await riskyOperation()
  return result
} catch (error) {
  logger.error('Operation failed:', { error, context })
  throw new AppError('User-friendly message', { cause: error })
}
```

```python
# Python
try:
    result = await risky_operation()
    return result
except SpecificError as e:
    logger.error("Operation failed: %s", e, exc_info=True)
    raise AppError("User-friendly message") from e
# NEVER use bare except: or except Exception:
```

## Input Validation

Validate at system boundaries (API endpoints, user input, external data):

```typescript
// TypeScript - Zod
const schema = z.object({
  email: z.string().email(),
  age: z.number().int().min(0).max(150)
})
```

```python
# Python - Pydantic
class UserInput(BaseModel):
    email: EmailStr
    age: int = Field(ge=0, le=150)
```

## Naming Conventions

| Language | Variables/Functions | Classes | Constants | Files |
|----------|-------------------|---------|-----------|-------|
| TypeScript/JS | camelCase | PascalCase | UPPER_SNAKE | kebab-case |
| Python | snake_case | PascalCase | UPPER_SNAKE | snake_case |
| Swift | camelCase | PascalCase | camelCase | PascalCase |

## Code Quality Checklist

Before marking work complete:
- [ ] Code is readable and well-named
- [ ] Functions are small (<50 lines)
- [ ] Files are focused (<800 lines)
- [ ] No deep nesting (>4 levels)
- [ ] Proper error handling (no bare except/catch)
- [ ] No console.log/print statements in production
- [ ] No hardcoded values (use env vars/config)
- [ ] No mutation (immutable patterns used)
- [ ] No TODO/FIXME left without linked issue
