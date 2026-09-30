# Controller Structure Guide for NGDRS 2.0


## Core Principle (Most Important)

**One controller should have one responsibility.**

❌ Do NOT create one huge controller just because everything belongs to "Application"

✅ Instead:
- **Application** is the domain
- **Controllers** are organized by responsibility

## Types of Controllers in NGDRS 2.0

You will have three kinds of controllers:

1. **Web Controllers** - Return full pages
2. **API Controllers** - Return JSON data
3. **Fragment Controllers** - Return HTML snippets

Each has a very clear role and should **never be mixed**.

## Recommended Controller Folder Structure

```
app/Http/Controllers/
├── Web/
│   └── Application/
│       ├── GeneralController.php
│       ├── PropertyController.php
│       ├── PartyController.php
│       ├── WitnessController.php
│       ├── IdentifierController.php
│       ├── FeeController.php
│       ├── EdeedController.php
│       ├── DocumentController.php
│       └── AppointmentController.php
├── Api/
│   └── Application/
│       ├── GeneralController.php
│       ├── PropertyController.php
│       ├── PartyController.php
│       ├── WitnessController.php
│       ├── IdentifierController.php
│       ├── FeeController.php
│       ├── EdeedController.php
│       ├── DocumentController.php
│       └── SubmissionController.php
└── Fragment/
    └── Application/
        ├── PartyController.php
        ├── WitnessController.php
        └── CommonController.php
```

## Web Controllers (Page Routes)

### Purpose
- Return full Blade pages
- Handle navigation between steps
- No database queries
- No calculations
- No validation logic

### Responsibilities
Each step gets its own controller.

**Example:** `PartyController` only returns the party page view.

Typical responsibilities:
- `return view()`
- Pass minimal flags or state
- Enforce step order via middleware (not logic)

### Key Rule
**Web controllers should stay very small.**

### Example

```php
<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function index(): View
    {
        return view('application.party');
    }
}
```

## API Controllers (AJAX / JSON)

### Purpose
- Fetch step data
- Save step data
- Perform calculations (fee, edeeds, etc.)
- Handle submission and transitions

### Responsibilities
Each capability gets its own controller.

Examples of capabilities:
- Party
- Witness
- Fee
- Document upload
- Edeed generation

### Rules
- One controller per capability
- One method per action
- Validation must use Form Requests
- Business logic must NOT be in controllers

### What Controllers Should Do
Controllers should only:
1. Accept request
2. Call a service
3. Return JSON

### Example

```php
<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\PartyRequest;
use App\Services\Application\PartyService;
use Illuminate\Http\JsonResponse;

class PartyController extends Controller
{
    public function __construct(
        private PartyService $partyService
    ) {}

    public function store(PartyRequest $request): JsonResponse
    {
        $party = $this->partyService->save($request->validated());
        
        return response()->json([
            'success' => true,
            'data' => $party
        ]);
    }

    public function index(): JsonResponse
    {
        $parties = $this->partyService->getAll();
        
        return response()->json($parties);
    }
}
```

## Fragment Controllers (HTML Snippets)

### Purpose
- Return small chunks of HTML
- Used when frontend needs server-rendered blocks
- Replacement for CakePHP-style partial rendering

### Examples
- Add another party row
- Add another witness row
- Dynamic form sections

### Rules
- Return Blade partials only
- No DB writes
- Minimal logic

**These controllers keep frontend JS simple and consistent.**

### Example

```php
<?php

namespace App\Http\Controllers\Fragment\Application;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function addRow(): View
    {
        return view('fragments.application.party-row');
    }
}
```

## Where Business Logic Goes (Very Important)

**Business logic must NOT go into controllers.**

It belongs in **Services**.

### Recommended Structure

```
app/Services/Application/
├── GeneralService.php
├── PropertyService.php
├── PartyService.php
├── WitnessService.php
├── FeeService.php
└── EdeedService.php
```

### Remember
- **Controllers** only coordinate
- **Services** decide what happens

### Example Service

```php
<?php

namespace App\Services\Application;

use App\Models\Party;

class PartyService
{
    public function save(array $data): Party
    {
        // Business logic here
        // Validation, calculations, database operations
        
        return Party::create($data);
    }

    public function getAll(): Collection
    {
        return Party::with('identifiers')->get();
    }
}
```

## Validation Rules

**Never validate inside controllers.**

Use **Form Request** classes:
- `PartyRequest`
- `PropertyRequest`
- `FeeRequest`

### Controller Implementation

```php
public function store(PartyRequest $request): JsonResponse
{
    // Validation already happened
    $validated = $request->validated();
    
    // Just call the service
    $party = $this->partyService->save($validated);
    
    return response()->json($party);
}
```

## Common Mistakes to Avoid

❌ **Don't do these:**
- One giant `ApplicationController`
- Mixing Web and API logic in same controller
- Writing database queries in controllers
- Writing calculation logic in controllers
- Controllers growing beyond a few hundred lines

✅ **If you see these, refactor immediately.**

## Simple Decision Rule

Ask yourself one question:

**What does this endpoint return?**

| Returns | Controller Type |
|---------|----------------|
| Full page (Blade view) | Web controller |
| JSON | API controller |
| HTML snippet | Fragment controller |

**That single rule solves 90% of confusion.**

## Quick Reference Table

| Controller Type | Returns | Contains | Example Use Case |
|----------------|---------|----------|------------------|
| **Web** | Blade view | `return view()` | Show party step page |
| **API** | JSON | `return response()->json()` | Save party data via AJAX |
| **Fragment** | HTML snippet | `return view('partial')` | Add new party row dynamically |

## Final Takeaway

**Application** is the business domain, not a file.

**Controllers** are grouped by responsibility, not by name.

### If you follow this structure:
✅ Routes stay readable  
✅ Controllers stay small  
✅ Logic stays testable  
✅ NGDRS 2.0 remains maintainable even as it grows

---

## Step-by-Step Workflow Example

```
User Journey: general → property → party → witness → fee → edeeds → submission → appointment

Web Controllers (show pages):
├── GeneralController → return view('application.general')
├── PropertyController → return view('application.property')
├── PartyController → return view('application.party')
└── ...

API Controllers (handle data):
├── GeneralController → save/fetch general info
├── PropertyController → save/fetch property info
├── PartyController → save/fetch party info
└── ...

Fragment Controllers (render snippets):
├── PartyController → return party row HTML
├── WitnessController → return witness row HTML
└── CommonController → return common UI components
```

Each step has its own Web controller (page) and API controller (data), keeping concerns completely separated.