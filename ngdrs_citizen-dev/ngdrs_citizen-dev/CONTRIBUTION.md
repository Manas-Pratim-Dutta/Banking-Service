# Contributing to NGDRS 2.0

Thank you for your interest in contributing to this project! This document outlines the guidelines and procedures for contributing to our Laravel and TypeScript codebase.

## Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** or **yarn**
- **PostgreSQL** >= 14
- **Git**

## Coding Standards

#### Laravel Best Practices

- **Naming Conventions:**
  - Controllers: `PascalCase` with `Controller` suffix (e.g., `UserController`)
  - Models: Singular `PascalCase` (e.g., `User`, `OrderItem`)
  - Database tables: Plural snake_case (e.g., `users`, `order_items`)
  - Migrations: Descriptive with timestamp (e.g., `2024_01_01_create_users_table`)
  - Routes: Kebab-case (e.g., `/user-profile`, `/order-history`)
  - Variables/Methods: camelCase (e.g., `$userName`, `getUserById()`)

- **File Organization:**
  - Keep controllers thin - move business logic to Services or Actions
  - Use Form Requests for validation
  - Use Resources/Transformers for API responses
  - Utilize Eloquent relationships properly

- **Code Structure:**
  ```php
  // Good
  class UserService
  {
      public function createUser(array $data): User
      {
          return DB::transaction(function () use ($data) {
              $user = User::create($data);
              $user->profile()->create($data['profile']);
              return $user;
          });
      }
  }

  // Avoid heavy logic in controllers
  class UserController extends Controller
  {
      public function store(StoreUserRequest $request, UserService $userService)
      {
          $user = $userService->createUser($request->validated());
          return new UserResource($user);
      }
  }
  ```

#### Documentation
- Add PHPDoc blocks for all classes and public methods
- Include `@param`, `@return`, and `@throws` annotations
- Example:
  ```php
  /**
   * Create a new user with the given data.
   *
   * @param array<string, mixed> $data The user data
   * @return User The created user instance
   * @throws \InvalidArgumentException If email is invalid
   */
  public function createUser(array $data): User
  {
      // Implementation
  }
  ```

### TypeScript Standards

#### Type Safety
- **Strict Mode:** Always use strict TypeScript configuration
- **No `any` Type:** Avoid using `any` - use `unknown` or proper types
- **Explicit Return Types:** Always specify return types for functions

#### Naming Conventions
- **Interfaces:** PascalCase with `I` prefix or descriptive name (e.g., `IUser` or `UserData`)
- **Types:** PascalCase (e.g., `UserRole`, `ApiResponse`)
- **Enums:** PascalCase (e.g., `UserStatus`)
- **Variables/Functions:** camelCase (e.g., `userName`, `fetchUserData`)
- **Constants:** UPPER_SNAKE_CASE (e.g., `API_BASE_URL`)

#### Code Style

```typescript
// Good - Explicit types and clear structure
interface UserData {
  id: number;
  name: string;
  email: string;
  role: UserRole;
}

enum UserRole {
  Admin = 'admin',
  User = 'user',
  Guest = 'guest'
}

async function fetchUser(userId: number): Promise<UserData> {
  const response = await fetch(`/api/users/${userId}`);
  if (!response.ok) {
    throw new Error('Failed to fetch user');
  }
  return response.json();
}

// Bad - Using any and unclear types
async function fetchUser(userId: any): Promise<any> {
  const response = await fetch(`/api/users/${userId}`);
  return response.json();
}
```

#### Modern JavaScript/TypeScript Features
- Use `async/await` over callbacks
- Use destructuring where appropriate
- Use optional chaining (`?.`) and nullish coalescing (`??`)
- Prefer `const` and `let` over `var`

#### Formatting
- Use Prettier for consistent formatting:
  ```bash
  npm run format
  ```
- Run ESLint for code quality:
  ```bash
  npm run lint
  ```

---

# Daily Development Workflow

Follow these steps **every day before you start working** on the project.

---

## 1. Switch to `main` and pull latest changes

Always begin by updating your local `main` branch.

```bash
git checkout main
git pull origin main
```

This ensures you are starting with the most recent and stable code.

---

## 2. Create or switch to your feature branch

**If starting a new task:**

```bash
git checkout -b <name>/<type>/<feature>
```

**If continuing existing work:**

```bash
git checkout <your-branch>
```

> **⚠️ Never work directly on main.**

---

## 3. Start coding

- Follow existing project structure
- Keep changes focused on a single task
- Make small, logical changes

---

## 4. Commit changes frequently

Commit small, meaningful units of work.

```bash
git add .
git commit -m "Short and clear description of change"
```

> **Avoid large, unclear commits.**

---

## 5. Sync with `main` before pushing

Before pushing your work or opening a PR, bring in the latest changes from `main`.

```bash
git checkout main
git pull origin main
git checkout <your-branch>
git merge main
```

Resolve conflicts if any and commit the resolution.

---

## 6. Push your branch

```bash
git push origin <your-branch>
```

---

## 7. Open or update Pull Request

- Open a PR if work is ready
- Update existing PR if continuing work
- Ensure PR is up to date with `main`
- Address review comments promptly

---

## Important Rules

- ❌ Do not push directly to `main`
- ✅ One task per branch
- ✅ One PR per task
- ✅ Always pull before starting work

---

This keeps daily work:

- **Predictable**
- **Conflict-free**
- **Easy to review**
- **Scalable for new team members**

## PR Template

When creating a pull request, include the following information:

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Related Issues
Closes #<issue-number>

## Changes Made
- Change 1
- Change 2
- Change 3

## Screenshots (if applicable)
Add screenshots for UI changes

## Testing
- [ ] Unit tests added/updated
- [ ] Manual testing completed
- [ ] Edge cases considered

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings generated
- [ ] Tests pass locally
```
---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [TypeScript Documentation](https://www.typescriptlang.org/docs)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

**Thank you for contributing! 🎉**
