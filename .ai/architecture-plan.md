# Application Architecture Plan (Laravel + Inertia + React)

## 1. Models, Controllers, and Relationships

### 1.1 Workshop (Tenant)
- **Source Table:** `workshops` (managed by spatie/laravel-multitenancy)
- **Proposed Model:** `Workshop` (extends Tenant)
- **Proposed Controller:** N/A (created during registration, managed through package)
- **Key Fields:** name
- **Relationships:**
  - hasMany: User, Mechanic, Client, Vehicle, RepairOrder

### 1.2 User
- **Source Table:** `users`
- **Proposed Model:** `User`
- **Proposed Controller:** `UserController`
- **Relationships:**
  - belongsTo: Workshop
  - morphMany: InternalNote (as author)
- **Roles:** Owner, Office, Mechanic (via spatie/laravel-permission)

### 1.3 Mechanic
- **Source Table:** `mechanics`
- **Proposed Model:** `Mechanic`
- **Proposed Controller:** `MechanicController`
- **Relationships:**
  - belongsTo: Workshop
  - hasMany: TimeEntry
  - morphMany: InternalNote (as author)

### 1.4 Client
- **Source Table:** `clients`
- **Proposed Model:** `Client`
- **Proposed Controller:** `ClientController`
- **Relationships:**
  - belongsTo: Workshop
  - hasMany: Vehicle
  - hasManyThrough: RepairOrder (through Vehicle)

### 1.5 Vehicle
- **Source Table:** `vehicles`
- **Proposed Model:** `Vehicle`
- **Proposed Controller:** `VehicleController`
- **Relationships:**
  - belongsTo: Workshop
  - belongsTo: Client
  - hasMany: RepairOrder
  - hasOneThrough: Client (for reverse access)

### 1.6 RepairOrder
- **Source Table:** `repair_orders`
- **Proposed Model:** `RepairOrder`
- **Proposed Controller:** `RepairOrderController`
- **Relationships:**
  - belongsTo: Workshop
  - belongsTo: Vehicle
  - hasMany: TimeEntry
  - hasMany: InternalNote
  - hasOneThrough: Client (through Vehicle)
  - morphMany: Media (via spatie/laravel-medialibrary)
  - morphMany: Activity (via spatie/laravel-activitylog)

### 1.7 TimeEntry
- **Source Table:** `time_entries`
- **Proposed Model:** `TimeEntry`
- **Proposed Controller:** `TimeEntryController`
- **Relationships:**
  - belongsTo: RepairOrder
  - belongsTo: Mechanic
  - hasOneThrough: Workshop (through RepairOrder)
  - morphMany: Activity (via spatie/laravel-activitylog)

### 1.8 InternalNote
- **Source Table:** `internal_notes`
- **Proposed Model:** `InternalNote`
- **Proposed Controller:** `InternalNoteController`
- **Relationships:**
  - belongsTo: RepairOrder
  - morphTo: author (User or Mechanic)
  - hasOneThrough: Workshop (through RepairOrder)

### 1.9 Additional Controllers

**DashboardController**
- Purpose: Display workshop overview and statistics

**ReportController**
- Purpose: Generate mechanic and team performance reports

---

## 2. Routes and Controller Actions

### 2.1 Authentication & Registration Routes

#### Registration
- `GET /register` → Show registration form (Laravel Fortify)
  - **Description:** Display workshop registration form
  - **Proposed Authorization:** Guest only
  - **Proposed React Component:** `Pages/Auth/Register`
  - **Component Props:** `{}`
  - **Form Fields:** workshop_name, owner_name, email, password, password_confirmation

- `POST /register` → Process registration (Laravel Fortify + custom logic)
  - **Description:** Create new workshop and owner account simultaneously
  - **Proposed Authorization:** Guest only
  - **Business Logic:**
    1. Create Workshop record
    2. Create User record with Owner role
    3. Associate user with workshop (set workshop_id)
    4. Auto-login the new owner
  - **Proposed React Component:** N/A (redirects to `dashboard`)

#### Login/Logout
- `GET /login` → Show login form (Laravel Fortify)
  - **Proposed React Component:** `Pages/Auth/Login`
- `POST /login` → Process login (Laravel Fortify)
- `POST /logout` → Logout user (Laravel Fortify)

#### Password Reset
- `GET /forgot-password` → Show password reset form (Laravel Fortify)
  - **Proposed React Component:** `Pages/Auth/ForgotPassword`
- `POST /forgot-password` → Send reset link (Laravel Fortify)
- `GET /reset-password/{token}` → Show reset password form (Laravel Fortify)
  - **Proposed React Component:** `Pages/Auth/ResetPassword`
- `POST /reset-password` → Process password reset (Laravel Fortify)

### 2.2 DashboardController

#### Dashboard Index
- **Route:** `GET /dashboard` → `dashboard` → `DashboardController@index`
- **Description:** Display workshop overview with active orders count and statistics
- **Proposed Authorization:** Authenticated user (any role)
- **Proposed React Component:** `Pages/Dashboard/Index`
- **Component Props:**
  ```typescript
  {
    activeOrdersCount: number;
    pendingOrdersCount: number;
    todayTimeEntriesTotal: number;
    recentOrders: Array<{
      id: number;
      vehicle: string;
      client: string;
      status: string;
      created_at: string;
    }>;
  }
  ```

---

### 2.3 ClientController

#### List Clients
- **Route:** `GET /clients` → `clients.index` → `ClientController@index`
- **Description:** List all clients (paginated, searchable)
- **Proposed Authorization:** `ClientPolicy::viewAny` (Owner, Office)
- **Proposed React Component:** `Pages/Clients/Index`
- **Component Props:**
  ```typescript
  {
    clients: PaginatedResponse<{
      id: number;
      first_name: string;
      last_name: string | null;
      phone_number: string;
      email: string | null;
      vehicles_count: number;
    }>;
    filters: {
      search?: string;
      sort?: string;
      direction?: 'asc' | 'desc';
    };
  }
  ```

#### Create Client Form
- **Route:** `GET /clients/create` → `clients.create` → `ClientController@create`
- **Description:** Show create client form
- **Proposed Authorization:** `ClientPolicy::create` (Owner, Office)
- **Proposed React Component:** `Pages/Clients/Create`
- **Component Props:** `{}`

#### Store Client
- **Route:** `POST /clients` → `clients.store` → `ClientController@store`
- **Description:** Store new client
- **Proposed Authorization:** `ClientPolicy::create` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `clients.show`)

#### Show Client
- **Route:** `GET /clients/{client}` → `clients.show` → `ClientController@show`
- **Description:** Show client details with vehicles list
- **Proposed Authorization:** `ClientPolicy::view` (Owner, Office)
- **Proposed React Component:** `Pages/Clients/Show`
- **Component Props:**
  ```typescript
  {
    client: {
      id: number;
      first_name: string;
      last_name: string | null;
      phone_number: string;
      email: string | null;
      address_street: string | null;
      address_city: string | null;
      address_postal_code: string | null;
      address_country: string | null;
      created_at: string;
    };
    vehicles: Array<{
      id: number;
      make: string;
      model: string;
      year: number;
      registration_number: string;
      vin: string;
      repair_orders_count: number;
    }>;
  }
  ```

#### Edit Client Form
- **Route:** `GET /clients/{client}/edit` → `clients.edit` → `ClientController@edit`
- **Description:** Show edit client form
- **Proposed Authorization:** `ClientPolicy::update` (Owner, Office)
- **Proposed React Component:** `Pages/Clients/Edit`
- **Component Props:**
  ```typescript
  {
    client: {
      id: number;
      first_name: string;
      last_name: string | null;
      phone_number: string;
      email: string | null;
      address_street: string | null;
      address_city: string | null;
      address_postal_code: string | null;
      address_country: string | null;
    };
  }
  ```

#### Update Client
- **Route:** `PUT/PATCH /clients/{client}` → `clients.update` → `ClientController@update`
- **Description:** Update client
- **Proposed Authorization:** `ClientPolicy::update` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `clients.show`)

#### Delete Client
- **Route:** `DELETE /clients/{client}` → `clients.destroy` → `ClientController@destroy`
- **Description:** Delete client (soft delete)
- **Proposed Authorization:** `ClientPolicy::delete` (Owner only)
- **Proposed React Component:** N/A (redirects to `clients.index`)

---

### 2.4 VehicleController

#### List/Search Vehicles
- **Route:** `GET /vehicles` → `vehicles.index` → `VehicleController@index`
- **Description:** List/search all vehicles (searchable by registration, VIN, make, model)
- **Proposed Authorization:** `VehiclePolicy::viewAny` (Owner, Office)
- **Proposed React Component:** `Pages/Vehicles/Index`
- **Component Props:**
  ```typescript
  {
    vehicles: PaginatedResponse<{
      id: number;
      make: string;
      model: string;
      year: number;
      registration_number: string;
      vin: string;
      client: {
        id: number;
        first_name: string;
        last_name: string | null;
      };
      repair_orders_count: number;
    }>;
    filters: {
      search?: string;
      sort?: string;
      direction?: 'asc' | 'desc';
    };
  }
  ```

#### Create Vehicle Form
- **Route:** `GET /vehicles/create` → `vehicles.create` → `VehicleController@create`
- **Description:** Show create vehicle form
- **Proposed Authorization:** `VehiclePolicy::create` (Owner, Office)
- **Proposed React Component:** `Pages/Vehicles/Create`
- **Component Props:**
  ```typescript
  {
    clients: Array<{ id: number; name: string }>;
    preselected_client_id?: number;
  }
  ```

#### Store Vehicle
- **Route:** `POST /vehicles` → `vehicles.store` → `VehicleController@store`
- **Description:** Store new vehicle
- **Proposed Authorization:** `VehiclePolicy::create` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `vehicles.show`)

#### Show Vehicle
- **Route:** `GET /vehicles/{vehicle}` → `vehicles.show` → `VehicleController@show`
- **Description:** Show vehicle details with full repair history
- **Proposed Authorization:** `VehiclePolicy::view` (Owner, Office, Mechanic)
- **Proposed React Component:** `Pages/Vehicles/Show`
- **Component Props:**
  ```typescript
  {
    vehicle: {
      id: number;
      make: string;
      model: string;
      year: number;
      registration_number: string;
      vin: string;
      client: {
        id: number;
        first_name: string;
        last_name: string | null;
        phone_number: string;
      };
    };
    repair_orders: PaginatedResponse<{
      id: number;
      status: string;
      problem_description: string;
      total_time_minutes: number;
      started_at: string | null;
      finished_at: string | null;
      created_at: string;
    }>;
  }
  ```

#### Edit Vehicle Form
- **Route:** `GET /vehicles/{vehicle}/edit` → `vehicles.edit` → `VehicleController@edit`
- **Description:** Show edit vehicle form
- **Proposed Authorization:** `VehiclePolicy::update` (Owner, Office)
- **Proposed React Component:** `Pages/Vehicles/Edit`
- **Component Props:**
  ```typescript
  {
    vehicle: {
      id: number;
      client_id: number;
      make: string;
      model: string;
      year: number;
      registration_number: string;
      vin: string;
    };
    clients: Array<{ id: number; name: string }>;
  }
  ```

#### Update Vehicle
- **Route:** `PUT/PATCH /vehicles/{vehicle}` → `vehicles.update` → `VehicleController@update`
- **Description:** Update vehicle
- **Proposed Authorization:** `VehiclePolicy::update` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `vehicles.show`)

#### Delete Vehicle
- **Route:** `DELETE /vehicles/{vehicle}` → `vehicles.destroy` → `VehicleController@destroy`
- **Description:** Delete vehicle (soft delete)
- **Proposed Authorization:** `VehiclePolicy::delete` (Owner only)
- **Proposed React Component:** N/A (redirects to `clients.show`)

---

### 2.5 RepairOrderController

#### List Active Orders
- **Route:** `GET /repair-orders` → `repair-orders.index` → `RepairOrderController@index`
- **Description:** List all active orders (filterable by status)
- **Proposed Authorization:** `RepairOrderPolicy::viewAny` (all authenticated users)
- **Proposed React Component:** `Pages/RepairOrders/Index`
- **Component Props:**
  ```typescript
  {
    orders: PaginatedResponse<{
      id: number;
      status: string;
      vehicle: {
        id: number;
        make: string;
        model: string;
        registration_number: string;
      };
      client: {
        id: number;
        first_name: string;
        last_name: string | null;
      };
      total_time_minutes: number;
      created_at: string;
    }>;
    filters: {
      status?: string;
      search?: string;
      sort?: string;
      direction?: 'asc' | 'desc';
    };
    statuses: Array<string>;
  }
  ```

#### Create Repair Order Form
- **Route:** `GET /repair-orders/create` → `repair-orders.create` → `RepairOrderController@create`
- **Description:** Show create repair order form
- **Proposed Authorization:** `RepairOrderPolicy::create` (Owner, Office)
- **Proposed React Component:** `Pages/RepairOrders/Create`
- **Component Props:**
  ```typescript
  {
    vehicles: Array<{
      id: number;
      label: string;
      client_name: string;
    }>;
    preselected_vehicle_id?: number;
    statuses: Array<string>;
  }
  ```

#### Store Repair Order
- **Route:** `POST /repair-orders` → `repair-orders.store` → `RepairOrderController@store`
- **Description:** Store new repair order with attachments
- **Proposed Authorization:** `RepairOrderPolicy::create` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `repair-orders.show`)

#### Show Repair Order
- **Route:** `GET /repair-orders/{repairOrder}` → `repair-orders.show` → `RepairOrderController@show`
- **Description:** Show order details with time entries, notes, attachments, activity log
- **Proposed Authorization:** `RepairOrderPolicy::view` (all authenticated users)
- **Proposed React Component:** `Pages/RepairOrders/Show`
- **Component Props:**
  ```typescript
  {
    order: {
      id: number;
      status: string;
      problem_description: string;
      started_at: string | null;
      finished_at: string | null;
      created_at: string;
      vehicle: {
        id: number;
        make: string;
        model: string;
        year: number;
        registration_number: string;
        vin: string;
      };
      client: {
        id: number;
        first_name: string;
        last_name: string | null;
        phone_number: string;
        email: string | null;
      };
      total_time_minutes: number;
      images: Array<{
        id: number;
        url: string;
        name: string;
      }>;
    };
    time_entries: Array<{
      id: number;
      mechanic: {
        id: number;
        first_name: string;
        last_name: string;
      };
      duration_minutes: number;
      description: string | null;
      created_at: string;
      can_edit: boolean;
    }>;
    internal_notes: Array<{
      id: number;
      content: string;
      author: {
        id: number;
        name: string;
        type: 'User' | 'Mechanic';
      };
      created_at: string;
      can_edit: boolean;
    }>;
    activity_log: Array<{
      description: string;
      subject_type: string;
      subject_id: number;
      causer: {
        name: string;
      } | null;
      properties: {
        old?: any;
        attributes?: any;
      };
      created_at: string;
    }>;
    can_edit: boolean;
    can_delete: boolean;
  }
  ```

#### Edit Repair Order Form
- **Route:** `GET /repair-orders/{repairOrder}/edit` → `repair-orders.edit` → `RepairOrderController@edit`
- **Description:** Show edit repair order form
- **Proposed Authorization:** `RepairOrderPolicy::update` (Owner, Office)
- **Proposed React Component:** `Pages/RepairOrders/Edit`
- **Component Props:**
  ```typescript
  {
    order: {
      id: number;
      vehicle_id: number;
      status: string;
      problem_description: string;
      started_at: string | null;
      finished_at: string | null;
      images: Array<{
        id: number;
        url: string;
        name: string;
      }>;
    };
    vehicles: Array<{
      id: number;
      label: string;
    }>;
    statuses: Array<string>;
  }
  ```

#### Update Repair Order
- **Route:** `PUT/PATCH /repair-orders/{repairOrder}` → `repair-orders.update` → `RepairOrderController@update`
- **Description:** Update repair order
- **Proposed Authorization:** `RepairOrderPolicy::update` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `repair-orders.show`)

#### Update Order Status
- **Route:** `PATCH /repair-orders/{repairOrder}/status` → `repair-orders.update-status` → `RepairOrderController@updateStatus`
- **Description:** Update order status only
- **Proposed Authorization:** `RepairOrderPolicy::updateStatus` (Owner, Office)
- **Proposed React Component:** N/A (redirects back)

#### Delete Repair Order
- **Route:** `DELETE /repair-orders/{repairOrder}` → `repair-orders.destroy` → `RepairOrderController@destroy`
- **Description:** Delete repair order (soft delete)
- **Proposed Authorization:** `RepairOrderPolicy::delete` (Owner only)
- **Proposed React Component:** N/A (redirects to `repair-orders.index`)

---

### 2.6 TimeEntryController

#### Create Time Entry Form
- **Route:** `GET /time-entry` → `time-entry.create` → `TimeEntryController@create`
- **Description:** Show time entry form (mobile optimized, for mechanics)
- **Proposed Authorization:** Authenticated user with Mechanic role
- **Proposed React Component:** `Pages/TimeEntry/Create`
- **Component Props:**
  ```typescript
  {
    active_orders: Array<{
      id: number;
      label: string;
      status: string;
    }>;
    mechanics: Array<{
      id: number;
      first_name: string;
      last_name: string;
    }>;
  }
  ```

#### Store Time Entry
- **Route:** `POST /time-entry` → `time-entry.store` → `TimeEntryController@store`
- **Description:** Store new time entry
- **Proposed Authorization:** Authenticated user with Mechanic role
- **Proposed React Component:** N/A (redirects back to `time-entry.create`)

#### Edit Time Entry Form
- **Route:** `GET /time-entries/{timeEntry}/edit` → `time-entries.edit` → `TimeEntryController@edit`
- **Description:** Show edit time entry form
- **Proposed Authorization:** `TimeEntryPolicy::update` (Owner, Office, or mechanic's own entry)
- **Proposed React Component:** `Pages/TimeEntry/Edit`
- **Component Props:**
  ```typescript
  {
    time_entry: {
      id: number;
      repair_order_id: number;
      mechanic_id: number;
      duration_minutes: number;
      description: string | null;
      repair_order_label: string;
    };
    mechanics: Array<{
      id: number;
      first_name: string;
      last_name: string;
    }>;
  }
  ```

#### Update Time Entry
- **Route:** `PUT/PATCH /time-entries/{timeEntry}` → `time-entries.update` → `TimeEntryController@update`
- **Description:** Update time entry
- **Proposed Authorization:** `TimeEntryPolicy::update` (Owner, Office, or mechanic's own entry)
- **Proposed React Component:** N/A (redirects to related `repair-orders.show`)

#### Delete Time Entry
- **Route:** `DELETE /time-entries/{timeEntry}` → `time-entries.destroy` → `TimeEntryController@destroy`
- **Description:** Delete time entry
- **Proposed Authorization:** `TimeEntryPolicy::delete` (Owner, Office)
- **Proposed React Component:** N/A (redirects back)

---

### 2.7 InternalNoteController

#### Store Internal Note
- **Route:** `POST /internal-notes` → `internal-notes.store` → `InternalNoteController@store`
- **Description:** Add note to repair order
- **Proposed Authorization:** `InternalNotePolicy::create` (Owner, Office)
- **Proposed React Component:** N/A (redirects back to `repair-orders.show`)

#### Update Internal Note
- **Route:** `PUT/PATCH /internal-notes/{internalNote}` → `internal-notes.update` → `InternalNoteController@update`
- **Description:** Update note
- **Proposed Authorization:** `InternalNotePolicy::update` (Owner, or author)
- **Proposed React Component:** N/A (redirects back)

#### Delete Internal Note
- **Route:** `DELETE /internal-notes/{internalNote}` → `internal-notes.destroy` → `InternalNoteController@destroy`
- **Description:** Delete note
- **Proposed Authorization:** `InternalNotePolicy::delete` (Owner, or author)
- **Proposed React Component:** N/A (redirects back)

---

### 2.8 MechanicController

#### List Mechanics
- **Route:** `GET /mechanics` → `mechanics.index` → `MechanicController@index`
- **Description:** List all mechanics
- **Proposed Authorization:** `MechanicPolicy::viewAny` (Owner, Office)
- **Proposed React Component:** `Pages/Mechanics/Index`
- **Component Props:**
  ```typescript
  {
    mechanics: Array<{
      id: number;
      first_name: string;
      last_name: string;
      is_active: boolean;
      total_time_entries_count: number;
      created_at: string;
    }>;
    filters: {
      active?: boolean;
    };
  }
  ```

#### Create Mechanic Form
- **Route:** `GET /mechanics/create` → `mechanics.create` → `MechanicController@create`
- **Description:** Show create mechanic form
- **Proposed Authorization:** `MechanicPolicy::create` (Owner, Office)
- **Proposed React Component:** `Pages/Mechanics/Create`
- **Component Props:** `{}`

#### Store Mechanic
- **Route:** `POST /mechanics` → `mechanics.store` → `MechanicController@store`
- **Description:** Store new mechanic
- **Proposed Authorization:** `MechanicPolicy::create` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `mechanics.index`)

#### Edit Mechanic Form
- **Route:** `GET /mechanics/{mechanic}/edit` → `mechanics.edit` → `MechanicController@edit`
- **Description:** Show edit mechanic form
- **Proposed Authorization:** `MechanicPolicy::update` (Owner, Office)
- **Proposed React Component:** `Pages/Mechanics/Edit`
- **Component Props:**
  ```typescript
  {
    mechanic: {
      id: number;
      first_name: string;
      last_name: string;
      is_active: boolean;
    };
  }
  ```

#### Update Mechanic
- **Route:** `PUT/PATCH /mechanics/{mechanic}` → `mechanics.update` → `MechanicController@update`
- **Description:** Update mechanic (including is_active status)
- **Proposed Authorization:** `MechanicPolicy::update` (Owner, Office)
- **Proposed React Component:** N/A (redirects to `mechanics.index`)

#### Delete Mechanic
- **Route:** `DELETE /mechanics/{mechanic}` → `mechanics.destroy` → `MechanicController@destroy`
- **Description:** Delete mechanic (soft delete)
- **Proposed Authorization:** `MechanicPolicy::delete` (Owner only)
- **Proposed React Component:** N/A (redirects to `mechanics.index`)

---

### 2.9 UserController

#### List Users
- **Route:** `GET /users` → `users.index` → `UserController@index`
- **Description:** List all workshop users with roles
- **Proposed Authorization:** `UserPolicy::viewAny` (Owner only)
- **Proposed React Component:** `Pages/Users/Index`
- **Component Props:**
  ```typescript
  {
    users: Array<{
      id: number;
      name: string;
      email: string;
      roles: Array<string>;
      created_at: string;
    }>;
  }
  ```

#### Create User Form
- **Route:** `GET /users/create` → `users.create` → `UserController@create`
- **Description:** Show create user form
- **Proposed Authorization:** `UserPolicy::create` (Owner only)
- **Proposed React Component:** `Pages/Users/Create`
- **Component Props:**
  ```typescript
  {
    roles: Array<string>;
  }
  ```

#### Store User
- **Route:** `POST /users` → `users.store` → `UserController@store`
- **Description:** Store new user with role assignment
- **Proposed Authorization:** `UserPolicy::create` (Owner only)
- **Proposed React Component:** N/A (redirects to `users.index`)

#### Edit User Form
- **Route:** `GET /users/{user}/edit` → `users.edit` → `UserController@edit`
- **Description:** Show edit user form
- **Proposed Authorization:** `UserPolicy::update` (Owner only)
- **Proposed React Component:** `Pages/Users/Edit`
- **Component Props:**
  ```typescript
  {
    user: {
      id: number;
      name: string;
      email: string;
      roles: Array<string>;
    };
    available_roles: Array<string>;
  }
  ```

#### Update User
- **Route:** `PUT/PATCH /users/{user}` → `users.update` → `UserController@update`
- **Description:** Update user and role
- **Proposed Authorization:** `UserPolicy::update` (Owner only)
- **Proposed React Component:** N/A (redirects to `users.index`)

#### Delete User
- **Route:** `DELETE /users/{user}` → `users.destroy` → `UserController@destroy`
- **Description:** Delete user (soft delete)
- **Proposed Authorization:** `UserPolicy::delete` (Owner only, cannot delete self)
- **Proposed React Component:** N/A (redirects to `users.index`)

---

### 2.10 ReportController

#### Mechanic Performance Report
- **Route:** `GET /reports/mechanic` → `reports.mechanic` → `ReportController@mechanic`
- **Description:** Generate mechanic performance report (query params: mechanic_id, start_date, end_date)
- **Proposed Authorization:** `ReportPolicy::viewMechanic` (Owner only)
- **Proposed React Component:** `Pages/Reports/Mechanic`
- **Component Props:**
  ```typescript
  {
    mechanic: {
      id: number;
      first_name: string;
      last_name: string;
    };
    period: {
      start_date: string;
      end_date: string;
    };
    total_minutes: number;
    total_hours: string;
    time_entries: Array<{
      id: number;
      repair_order: {
        id: number;
        vehicle: string;
        client: string;
      };
      duration_minutes: number;
      description: string | null;
      created_at: string;
    }>;
    mechanics: Array<{ id: number; name: string }>;
  }
  ```

#### Team Performance Report
- **Route:** `GET /reports/team` → `reports.team` → `ReportController@team`
- **Description:** Generate team performance report (query params: start_date, end_date)
- **Proposed Authorization:** `ReportPolicy::viewTeam` (Owner only)
- **Proposed React Component:** `Pages/Reports/Team`
- **Component Props:**
  ```typescript
  {
    period: {
      start_date: string;
      end_date: string;
    };
    mechanics: Array<{
      id: number;
      first_name: string;
      last_name: string;
      total_minutes: number;
      total_hours: string;
      entries_count: number;
      orders_worked_on: number;
    }>;
    total_team_minutes: number;
    total_team_hours: string;
  }
  ```

---

## Notes

### Workshop Registration & Onboarding

**Registration Flow:**
1. Visitor goes to `/register`
2. Fills out form with:
   - Workshop name (e.g., "Auto-Serwis Kowalski")
   - Owner name
   - Email
   - Password
3. System creates:
   - New Workshop record
   - New User record with Owner role
   - Associates user with workshop via `workshop_id`
4. Owner is automatically logged in and redirected to dashboard
5. Owner can now:
   - Create additional users (Office role)
   - Create mechanics (for the `mechanics` table)
   - Create shared "Mechanic" user account for time entry

### Tenant Identification & Mechanic Access

**Solution:** Session-based multitenancy + Shared Mechanic Account

**Implementation:**
- All workshops use single domain (e.g., `fixflow.app`)
- Tenant identification via authenticated user's `workshop_id`
- When user logs in, their `workshop_id` determines which workshop data they access
- Owner creates a shared "Mechanic" user account with Mechanic role for their workshop
- At shift start, mechanics login once with shared credentials
- Session persists throughout work shift
- All queries automatically scoped to `auth()->user()->workshop_id` via spatie/laravel-multitenancy
- Time entry interface shows only:
  - Active orders from their workshop (filtered by tenant)
  - Mechanics list from their workshop (filtered by tenant)

**User Roles:**
- **Owner:** Full access to all features
- **Office:** Can manage clients, vehicles, orders; limited user/mechanic management
- **Mechanic:** Can only access time entry interface and view related orders

**Security:**
- No cross-workshop data access possible
- Workshop determined by authenticated user, not URL
- Tenant scoping applied automatically to all Eloquent queries

### Soft Deletes
The following models use soft deletes (deleted_at column):
- User, Mechanic, Client, Vehicle, RepairOrder

### Status Values
RepairOrder statuses (enum): `Nowe`, `Diagnoza`, `Wymaga kontaktu`, `Czeka na części`, `W naprawie`, `Gotowe do odbioru`, `Zamknięte`

---

**Document Version:** 2.2 (Added Workshop Registration)
**Created:** 2025-10-12
**Status:** Ready for Implementation
