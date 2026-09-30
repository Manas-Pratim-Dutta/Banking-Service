# TypeScript Folder Structure Guide (Laravel + Vite)

## Folder Structure

### `app.ts`
- Entry point loaded on every page
- Calls global and layout initializers
- Imports `bootstrap.ts`

### `bootstrap.ts`
- Global setup (axios, headers, common configs)
- Runs before any other application code

### `layout/`
- TypeScript for layout parts used on all pages
- Examples: header toggle, sidebar open/close, footer logic
- Imported and executed from `app.ts`

### `pages/`
- Page-specific logic
- One file per Blade page
- Loaded in Blade using `@vite('resources/js/pages/xyz.ts')`

### `components/`
- Reusable UI behaviour
- Examples: modal, dropdown, tabs, table sorting
- Imported by `pages/` or `app.ts` when needed

### `utils/`
- Small reusable helper functions
- No DOM or API calls
- Examples: date format, validation, querySelector helpers

### `services/`
- Backend/API communication and shared business logic
- Central place for axios/fetch calls
- Reused across multiple pages

### `types/`
- Shared TypeScript interfaces and types
- Ensures consistent data structures across project

## Usage Rules

| Scenario | Folder |
|----------|--------|
| Needed on every page | `layout/` and call from `app.ts` |
| Used only on one page | `pages/` |
| Reusable UI behaviour | `components/` |
| Reusable pure logic/helper | `utils/` |
| API/data access | `services/` |
| Shared data shapes | `types/` |

## Execution Flow

1. Layout loads `app.ts`
2. `app.ts` initializes layout/global features
3. Each Blade page optionally loads its own file from `pages/`
4. Page files import and use `components/`, `utils/`, `services/`, and `types/` as needed

## Example Structure

```
resources/js/
├── app.ts
├── bootstrap.ts
├── layout/
│   ├── header.ts
│   ├── sidebar.ts
│   └── footer.ts
├── pages/
│   ├── dashboard.ts
│   ├── users/
│   │   ├── index.ts
│   │   └── edit.ts
│   └── products.ts
├── components/
│   ├── modal.ts
│   ├── dropdown.ts
│   └── table-sort.ts
├── utils/
│   ├── date-formatter.ts
│   ├── validators.ts
│   └── dom-helpers.ts
├── services/
│   ├── api.ts
│   ├── user-service.ts
│   └── product-service.ts
└── types/
    ├── user.ts
    ├── product.ts
    └── api-response.ts
```

## Quick Tips

- Keep `app.ts` lightweight - it should only initialize, not contain heavy logic
- Use `bootstrap.ts` for third-party library configurations
- Page files should orchestrate, not implement - use components and services
- Keep components focused on a single UI pattern
- Utils should be pure functions with no side effects
- Services handle all external communication
- Types ensure type safety across the entire application