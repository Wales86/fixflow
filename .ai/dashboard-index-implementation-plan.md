# Controller Action Implementation Plan: DashboardController@index

## 1. Endpoint Overview

This action displays the main dashboard view for authenticated users, providing a high-level overview of the workshop's current operational status. The dashboard serves as the landing page after login, showing key metrics including the count of active repair orders, orders ready for pickup, total time logged today, and a list of recently updated orders. This view is read-only and accessible to all authenticated users within their respective workshop (multi-tenant aware).
We do not generate react component yet, backend only now

## 2. Request Details

- **Route:** `GET /dashboard`
- **Named Route:** `dashboard`
- **Controller Action:** `DashboardController@index`
- **Route Parameters:** None
- **Query Parameters:** None
- **Request Body:** None (GET request)
- **Validation:** Not required (no user input)

## 3. Used Types

### Service Class
- **`App\Services\DashboardService`** (to be created) - Encapsulates all business logic for fetching dashboard statistics and data

### Data Transfer Objects (Optional)
While not strictly necessary for this simple action, a DTO could be created for better type safety:
- **`App\Data\DashboardData`** (optional) - Structures the dashboard response data

### Eloquent Models Used
- **`App\Models\RepairOrder`** - For querying repair orders statistics
- **`App\Models\TimeEntry`** - For calculating today's total time entries
- **`App\Models\Vehicle`** - For eager loading vehicle information
- **`App\Models\Client`** - For eager loading client information
- **`App\Models\User`** - For accessing the authenticated user and their workshop

### Enums
- **`App\Enums\RepairOrderStatus`** - For filtering orders by status

## 4. Response Details

### Success Response
- **Type:** `Inertia::render()`
- **Component:** `Pages/Dashboard/Index`
- **HTTP Status:** `200 OK`

### Component Props Structure

```typescript
{
  activeOrdersCount: number;        // Count of orders where status != 'closed'
  pendingOrdersCount: number;       // Count of orders with status = 'ready_for_pickup'
  todayTimeEntriesTotal: number;    // Sum of duration_minutes for today's time entries
  recentOrders: Array<{
    id: number;                     // Repair order ID
    vehicle: string;                // Formatted as "Make Model Year" (e.g., "Toyota Corolla 2015")
    client: string;                 // Formatted as "FirstName LastName"
    status: string;                 // Localized status label (e.g., "W naprawie")
    created_at: string;             // ISO 8601 formatted date
  }>;                               // Limited to 10 most recently updated orders
}
```

### Error Response
Not applicable - this is a simple GET request with no user input. Errors would be handled by:
- **Authentication failure:** Handled by `auth` middleware (redirect to login)
- **Server errors:** Standard Laravel 500 error page

## 5. Data Flow

### Step-by-Step Request Processing

1. **Authentication Check**
   - The `auth` middleware verifies the user is logged in
   - If not authenticated, redirect to `/login`

2. **Workshop Context**
   - Retrieve authenticated user via `auth()->user()`
   - Access user's `workshop_id` for multi-tenancy scoping

3. **Fetch Dashboard Data** (via `DashboardService`)
   - **Active Orders Count:**
     - Query: `RepairOrder::where('workshop_id', $workshopId)->where('status', '!=', RepairOrderStatus::Closed)->count()`

   - **Pending Orders Count:**
     - Query: `RepairOrder::where('workshop_id', $workshopId)->where('status', RepairOrderStatus::ReadyForPickup)->count()`

   - **Today's Time Entries Total:**
     - Query: `TimeEntry::whereHas('repairOrder', fn($q) => $q->where('workshop_id', $workshopId))->whereDate('created_at', today())->sum('duration_minutes')`

   - **Recent Orders:**
     - Query: `RepairOrder::where('workshop_id', $workshopId)->with(['vehicle', 'client'])->latest('updated_at')->limit(10)->get()`
     - Transform each order to the required prop structure

4. **Data Transformation**
   - Convert recent orders to the required array format
   - Format vehicle as: `"{$vehicle->make} {$vehicle->model} {$vehicle->year}"`
   - Format client as: `"{$client->first_name} {$client->last_name}"`
   - Get localized status label: `$order->status->label()`
   - Format created_at: `$order->created_at->toISOString()`

5. **Return Inertia Response**
   - Call `Inertia::render('Dashboard/Index', $props)`
   - Inertia will render the React component with the provided props

### Service Method Signatures

```php
// App\Services\DashboardService

public function getActiveOrdersCount(int $workshopId): int;
public function getPendingOrdersCount(int $workshopId): int;
public function getTodayTimeEntriesTotal(int $workshopId): int;
public function getRecentOrders(int $workshopId, int $limit = 10): Collection;
```

## 6. Security Considerations

### Authentication
- **Required:** Yes
- **Middleware:** `auth` (applied to route)
- **Implementation:** User must be logged in to access dashboard
- **Failure Behavior:** Redirect to `/login` if unauthenticated

### Authorization
- **Required:** No explicit authorization check needed
- **Rationale:** All authenticated users can view their workshop's dashboard
- **Multi-Tenancy:** Critical - all queries MUST be scoped to `workshop_id` from authenticated user

### Multi-Tenancy Data Isolation
This is the most critical security consideration for this action:

1. **Workshop Scoping:**
   - ALL database queries MUST filter by the authenticated user's `workshop_id`
   - Never trust workshop_id from request parameters
   - Always use: `auth()->user()->workshop_id`

2. **Query Scoping Checklist:**
   - ✅ Active orders count scoped to workshop
   - ✅ Pending orders count scoped to workshop
   - ✅ Time entries scoped to workshop (via repair order relationship)
   - ✅ Recent orders scoped to workshop

3. **Potential Security Risks:**
   - ❌ **Data Leakage:** Forgetting to scope queries could expose other workshops' data
   - ❌ **N+1 Queries:** Missing eager loading could be exploited for DoS
   - ✅ **Mitigation:** Comprehensive testing with multiple workshop tenants

### Additional Security Measures
- **Rate Limiting:** Consider adding rate limiting if dashboard becomes a performance bottleneck
- **CSRF Protection:** Not applicable (GET request, no state modification)
- **XSS Protection:** Handled by React and Inertia automatically

## 7. Error Handling

### Authentication Errors
- **Scenario:** User is not logged in
- **Handler:** `auth` middleware
- **Response:** Redirect to `/login` with intended URL stored in session
- **HTTP Status:** `302 Found`

### Database/Server Errors
- **Scenario:** Database connection failure, query timeout, or unexpected exception
- **Handler:** Laravel's global exception handler
- **Response:** Standard Laravel 500 error page (or custom error page if configured)
- **HTTP Status:** `500 Internal Server Error`
- **Logging:** Errors should be logged to Laravel's log files for monitoring

### No Data Scenarios
These are NOT errors, but expected edge cases:
- **Zero active orders:** Display count as `0`
- **Zero pending orders:** Display count as `0`
- **Zero time entries today:** Display total as `0`
- **No recent orders:** Display empty array `[]`
- **UX Consideration:** React component should handle empty states gracefully with appropriate messaging

### Soft Delete Considerations
- Queries should automatically exclude soft-deleted records for:
  - `repair_orders` (has `deleted_at`)
  - `vehicles` (has `deleted_at`)
  - `clients` (has `deleted_at`)
- If `withTrashed()` relationships exist, ensure they're NOT used in dashboard queries

## 8. Performance Considerations

### Database Query Optimization

1. **Eager Loading for Recent Orders**
   - **Problem:** N+1 query issue when accessing `vehicle` and `client` for each order
   - **Solution:** Use `->with(['vehicle.client'])` on RepairOrder query
   - **Impact:** Reduces queries from (1 + 10 + 10 = 21) to (1 + 1 = 2)

2. **Indexed Columns**
   - Ensure the following indexes exist (they do based on schema):
     - `repair_orders.workshop_id` (composite indexes exist)
     - `repair_orders.status` (✅ index exists)
     - `repair_orders.updated_at` (for sorting recent orders - **missing, should add**)
     - `time_entries.created_at` (for today's filter - **missing, should add**)
   - **Recommendation:** Add migration to create missing indexes:
     ```php
     $table->index('updated_at');  // on repair_orders table
     $table->index('created_at');  // on time_entries table
     ```
     ```

3. **Limit Recent Orders**
   - Always use `->limit(10)` to prevent fetching excessive data
   - Make limit configurable via config file: `config('dashboard.recent_orders_limit', 10)`


## 9. Implementation Steps

### Step 1: Create DashboardService
```bash
sail artisan make:class Services/DashboardService
```

**File:** `app/Services/DashboardService.php`

**Methods to implement:**
- `getActiveOrdersCount(int $workshopId): int`
- `getPendingOrdersCount(int $workshopId): int`
- `getTodayTimeEntriesTotal(int $workshopId): int`
- `getRecentOrders(int $workshopId, int $limit = 10): Collection`

**Key Implementation Details:**
- Inject no dependencies in constructor (service uses only Eloquent models)
- Ensure all queries are scoped to `$workshopId`
- Use eager loading in `getRecentOrders()`: `->with(['vehicle.client'])`
- Consider adding caching logic within each method

### Step 2: Create DashboardController
```bash
sail artisan make:controller DashboardController
```

**File:** `app/Http/Controllers/DashboardController.php`

**Implementation:**
```php
public function __construct(
    private readonly DashboardService $dashboardService
) {}

public function index(): Response
{
    $workshopId = auth()->user()->workshop_id;

    $recentOrders = $this->dashboardService
        ->getRecentOrders($workshopId)
        ->map(fn($order) => [
            'id' => $order->id,
            'vehicle' => "{$order->vehicle->make} {$order->vehicle->model} {$order->vehicle->year}",
            'client' => "{$order->client->first_name} {$order->client->last_name}",
            'status' => $order->status->label(),
            'created_at' => $order->created_at->toISOString(),
        ]);

    return Inertia::render('Dashboard/Index', [
        'activeOrdersCount' => $this->dashboardService->getActiveOrdersCount($workshopId),
        'pendingOrdersCount' => $this->dashboardService->getPendingOrdersCount($workshopId),
        'todayTimeEntriesTotal' => $this->dashboardService->getTodayTimeEntriesTotal($workshopId),
        'recentOrders' => $recentOrders,
    ]);
}
```

### Step 3: Register Route
**File:** `routes/web.php`

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

**Notes:**
- Apply `auth` middleware to ensure user is authenticated
- Use named route `dashboard` for easier URL generation

### Step 5: Write Feature Tests
```bash
sail artisan make:test --pest Dashboard/DashboardIndexTest
```

**File:** `tests/Feature/Dashboard/DashboardIndexTest.php`

**Test cases to implement:**

1. **Authentication Tests:**
   - ✅ Unauthenticated user is redirected to login
   - ✅ Authenticated user can access dashboard

2. **Multi-Tenancy Tests:**
   - ✅ User only sees data from their own workshop
   - ✅ User cannot see data from other workshops

3. **Data Display Tests:**
   - ✅ Active orders count is correct (excludes closed orders)
   - ✅ Pending orders count is correct (only ready_for_pickup status)
   - ✅ Today's time entries total is correct
   - ✅ Recent orders list is limited to 10 items
   - ✅ Recent orders are sorted by updated_at DESC

4. **Empty State Tests:**
   - ✅ Dashboard displays zeros when no data exists
   - ✅ Recent orders array is empty when no orders exist

5. **Eager Loading Test:**
   - ✅ Verify no N+1 queries (use Laravel Debugbar or query log)

**Example test structure:**
```php
use App\Models\User;
use App\Models\Workshop;
use App\Models\RepairOrder;
use App\Enums\RepairOrderStatus;

it('displays correct active orders count for authenticated user', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->for($workshop)->create();

    RepairOrder::factory()->for($workshop)->count(5)->create(['status' => RepairOrderStatus::InProgress]);
    RepairOrder::factory()->for($workshop)->count(2)->create(['status' => RepairOrderStatus::Closed]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) =>
        $page->component('Dashboard/Index')
            ->where('activeOrdersCount', 5)
    );
});
```

### Step 7: Run Tests
```bash
sail artisan test --filter=DashboardIndexTest
```

**Validation:**
- All tests should pass
- Verify no N+1 query warnings
- Check test coverage includes all scenarios

### Step 8: Code Quality Checks
```bash
# Format code with Pint
sail artisan pint

# Run static analysis with Larastan (if configured)
sail artisan analyse
```

### Step 9: Manual Testing Checklist
- [ ] Dashboard loads successfully for authenticated user
- [ ] Stat cards display correct counts
- [ ] Recent orders table displays correctly with proper formatting
- [ ] Empty states work when no data exists
- [ ] Multi-tenancy works (create two workshops and verify isolation)
- [ ] Page loads in < 200ms (use browser DevTools Network tab)


## 10. Testing Strategy


### Feature Tests (Required)
As outlined in Step 5, create comprehensive feature tests that:
- Test the full request/response cycle
- Verify authentication and authorization
- Validate multi-tenancy isolation
- Check data accuracy and formatting
- Test edge cases (empty states, etc.)


---

## Summary

This implementation plan provides a comprehensive roadmap for implementing the `DashboardController@index` action. The key priorities are:

1. **Multi-tenancy security** - Ensure all queries are properly scoped
2. **Performance** - Use eager loading and consider caching
3. **Code quality** - Follow Laravel best practices and project conventions
4. **Testing** - Comprehensive test coverage for confidence

The implementation should be straightforward, with the main complexity residing in the service layer query logic and ensuring proper multi-tenant data isolation.
