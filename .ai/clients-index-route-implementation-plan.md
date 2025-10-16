# Controller Action Implementation Plan: ClientController@index

## 1. Endpoint Overview
This controller action is responsible for displaying a list of all clients associated with the workshop. The list is paginated, searchable by a text query, and sortable by key columns. It serves as the main data source for the client management interface.

## 2. Request Details
- **Route:** `GET /clients`
- **Route Name:** `clients.index`
- **Controller Action:** `ClientController@index`
- **Route Parameters:** None
- **Query Parameters:**
  - `search` (optional, string): A search term to filter clients by name, email, etc.
  - `sort` (optional, string): The column to sort by. Allowed values: `first_name`, `last_name`, `email`.
  - `direction` (optional, string): The sort direction. Allowed values: `asc`, `desc`.
  - `page` (optional, integer): The page number for pagination.
- **Validation:** All query parameters will be validated by the `App\Http\Requests\Client\IndexClientRequest` form request class.

## 3. Used Types
- **`App\Http\Requests\Client\IndexClientRequest`**: A dedicated `FormRequest` class to handle validation of the query parameters (`search`, `sort`, `direction`).
- **`App\Data\Client\ClientListItemData`**: A DTO class (using `spatie/laravel-data`) representing a single client in the paginated list. This will be annotated for TypeScript transformation.
- **`App\Data\Page\ClientIndexPageProps`**: A DTO class that structures all the props passed to the React component, including the paginated clients and filter values. This will also be annotated for TypeScript transformation.

## 4. Response Details
- **Success Response:** On success, the action returns an Inertia response: `Inertia::render('Clients/Index', ClientIndexPageProps::from($props))`.
- **Component Props (`ClientIndexPageProps`):**
  ```typescript
  {
    clients: PaginatedData<ClientListItemData>;
    filters: {
      search?: string;
      sort?: string;
      direction?: 'asc' | 'desc';
    };
  }
  ```
  Where `ClientListItemData` is:
  ```typescript
  {
    id: number;
    first_name: string;
    last_name: string | null;
    phone_number: string;
    email: string | null;
    vehicles_count: number;
  }
  ```
- **Error Response:** In case of validation failure, Laravel's `FormRequest` automatically handles the redirection back to the previous page with validation errors, which are then made available to the Inertia component.

## 5. Data Flow
1.  An HTTP `GET` request is made to the `/clients` endpoint.
2.  The `ClientController@index` action is invoked.
3.  The `IndexClientRequest` class is resolved by the service container, validating the incoming query parameters.
4.  Authorization is checked via `$this->authorize('viewAny', Client::class)`, which triggers the `viewAny` method in `ClientPolicy`.
5.  The controller calls `ClientService::list($request->validated())`, passing the validated filter and sorting criteria.
6.  The `ClientService` builds a database query using the `Client` model. It applies the search filter (e.g., using a `WHERE` clause on multiple columns), the sorting order (`orderBy`), and calculates the number of associated vehicles using `withCount('vehicles')`.
7.  The service executes the query with pagination (`->paginate()`) and transforms the results into a paginated collection of `ClientListItemData` DTOs.
8.  The controller receives the paginated data, combines it with the filter values into the `ClientIndexPageProps` DTO.
9.  The final `props` object is passed to `Inertia::render('Clients/Index', ...)` to render the React component.

## 6. Security Considerations
- **Authentication:** The user must be authenticated. This is enforced by the `auth` middleware group in `routes/web.php`.
- **Authorization:** Access is controlled by `ClientPolicy::viewAny`. This policy must ensure that only users with the 'Owner' or 'Office' roles can view the list of clients, preventing unauthorized data access.

## 7. Error Handling
- **Not Found:** This route is an index, so a 404 is not a primary concern unless the route itself is not defined.
- **Validation Failed:** Handled automatically by `IndexClientRequest`. Inertia will receive the errors and expose them to the frontend.
- **Authorization Failed:** The `$this->authorize` method will throw an `Illuminate\Auth\Access\AuthorizationException`, which Laravel will handle by rendering a 403 Forbidden response page.
- **Server Error:** Any unhandled exceptions will result in a standard Laravel 500 Internal Server Error response.

## 8. Performance Considerations
- **N+1 Problem:** The number of vehicles per client (`vehicles_count`) must be fetched efficiently. This will be solved by using `withCount('vehicles')` in the Eloquent query, which performs a single aggregate query instead of one query per client.
- **Database Indexing:** The columns used for searching (`first_name`, `last_name`, `email`) and sorting should have database indexes to ensure fast query performance, especially as the number of clients grows.
- **Pagination:** Using Laravel's built-in pagination is crucial to avoid loading the entire clients table into memory, ensuring the application remains fast and scalable.

## 9. Implementation Steps
1.  **Model & Policy:**
    -   Ensure the `Client` model and its migration exist.
    -   Create `ClientPolicy` using `sail artisan make:policy ClientPolicy --model=Client`.
    -   Implement the `viewAny(User $user)` method in `ClientPolicy` to check for 'Owner' or 'Office' roles.
    -   Register the policy in `AuthServiceProvider` if not auto-discovered.
2.  **DTOs (Data Transfer Objects):**
    -   Create `App\Data\Client\ClientListItemData` DTO using `sail artisan make:data ClientListItemData`. Add properties and annotate with `#[TypeScript]` and `#[DataCollectionOf(ClientListItemData::class)]` for collections.
    -   Create `App\Data\Page\ClientIndexPageProps` DTO similarly.
3.  **Form Request:**
    -   Create `App\Http\Requests\Client\IndexClientRequest` using `sail artisan make:request Client/IndexClientRequest`.
    -   Set `authorize()` to return `true` (authorization is handled in the controller).
    -   Define the validation `rules()` for `search`, `sort`, and `direction`.
4.  **Service Class:**
    -   Create `App\Services\ClientService`.
    -   Implement a public method `list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator`.
    -   This method will contain the Eloquent query logic: applying `when()` for optional search, `orderBy()` for sorting, `withCount('vehicles')`, and finally `paginate()->through(fn ($client) => ClientListItemData::from($client))`.
5.  **Controller:**
    -   Create `ClientController` using `sail artisan make:controller ClientController`.
    -   Implement the `index(IndexClientRequest $request): \Inertia\Response` method.
    -   Inside `index`, call the authorization logic, then the `ClientService`, construct the `ClientIndexPageProps` DTO, and return the `Inertia::render` response.
6.  **Routing:**
    -   Add the route to `routes/web.php`: `Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');` within the appropriate middleware group.
7.  **Generate TypeScript Types:**
    -   Run `sail artisan typescript:transform` to generate the `.d.ts` files from the DTOs created in step 2.
8.  **Frontend Component:**
    -   Create the React component `resources/js/Pages/Clients/Index.tsx`.
    -   The component will accept `ClientIndexPageProps` and render the table of clients, filter inputs, and pagination links.
9.  **Testing:**
    -   Create a feature test for the endpoint using `sail artisan make:test Http/Controllers/ClientControllerTest --pest`.
    -   Write tests to verify:
        -   Unauthorized users are blocked (403).
        -   Authorized users receive a 200 response.
        -   The correct component (`Clients/Index`) is rendered.
        -   The `clients` prop has the correct paginated structure.
        -   Search functionality works as expected.
        -   Sorting functionality works as expected.
