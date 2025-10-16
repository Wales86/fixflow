# Controller Action Implementation Plan: ClientController@show

## 1. Endpoint Overview
This action is responsible for displaying the details of a specific client, along with a list of all vehicles associated with that client. It will fetch the necessary data from the database and render the corresponding Inertia React component.

## 2. Request Details
- **Route:** `GET /clients/{client}`
- **Route Name:** `clients.show`
- **Controller Action:** `ClientController@show`
- **Route Parameters:**
  - Required: `{client}` - The `Client` model instance, resolved automatically via Route Model Binding.
- **Request Body / Form Data:** None.
- **Validation:** Not applicable for this `GET` request.

## 3. Used Types
To ensure type safety and a clear data contract between the backend and frontend, the following DTOs (Data Transfer Objects) from `spatie/laravel-data` will be created. They will be configured for automatic TypeScript transformation.

- **`App\Data\Client\ClientData`**: Will represent the structure of a single client object.
  ```php
  class ClientData extends Data
  {
      public function __construct(
          public int $id,
          public string $first_name,
          public ?string $last_name,
          public string $phone_number,
          public ?string $email,
          public ?string $address_street,
          public ?string $address_city,
          public ?string $address_postal_code,
          public ?string $address_country,
          public string $created_at,
      ) {}
  }
  ```
- **`App\Data\Vehicle\VehicleData`**: Will represent the structure of a single vehicle object within the vehicles list.
  ```php
  class VehicleData extends Data
  {
      public function __construct(
          public int $id,
          public string $make,
          public string $model,
          public int $year,
          public string $registration_number,
          public string $vin,
          public int $repair_orders_count,
      ) {}
  }
  ```

## 4. Response Details
- **Success Response:** Returns an Inertia response that renders the `pages/clients/show` React component.
- **Component Props:**
  ```typescript
  {
    client: ClientData;
    vehicles: DataCollection<VehicleData>;
  }
  ```
- **Error Response:**
  - `404 Not Found`: If the client with the specified ID does not exist.
  - `403 Forbidden`: If the authenticated user is not authorized to view the client's details.

## 5. Data Flow
1.  A `GET` request is made to the `/clients/{client}` endpoint.
2.  Laravel's Route Model Binding attempts to find the `Client` model by its ID. If not found, it throws a `ModelNotFoundException` (404).
3.  The `ClientController@show` method is invoked with the fetched `Client` instance.
4.  Authorization is performed using `$this->authorize('view', $client)`, which calls the `view` method on `ClientPolicy`. If authorization fails, it throws an `AuthorizationException` (403).
5.  The controller calls a dedicated service, `ClientService`, to fetch and prepare the data.
6.  The `ClientService` loads the client's vehicles with a count of their associated repair orders (`$client->load(['vehicles' => fn ($query) => $query->withCount('repairOrders')])`).
7.  The service transforms the `Client` model and the collection of `Vehicle` models into their respective DTOs (`ClientData` and `VehicleData`).
8.  The controller receives the DTOs from the service.
9.  The controller returns `Inertia::render('pages/clients/show', ...)` with the DTOs as props.

## 6. Security Considerations
- **Authentication:** The user must be authenticated. This is handled by the `auth` middleware group in the route definition.
- **Authorization:** Access is restricted by the `ClientPolicy`. The `view` method within this policy must verify that the authenticated user has the 'Owner' or 'Office' role.

## 7. Error Handling
- **Not Found:** Handled automatically by Route Model Binding, which will result in a 404 page.
- **Validation Failed:** Not applicable.
- **Authorization Failed:** The `authorize` method in the controller will throw an `AuthorizationException`, resulting in a 403 page.
- **Server Error:** Any other exceptions will be caught by Laravel's default exception handler, resulting in a 500 error page.

## 8. Performance Considerations
- To prevent the N+1 query problem, the `vehicles` relationship will be eager-loaded.
- The count of repair orders for each vehicle (`repair_orders_count`) will be calculated efficiently in a single query using Eloquent's `withCount()` method.

## 9. Implementation Steps
1.  **Create Policy:** If it doesn't already exist, create the `ClientPolicy` using `sail artisan make:policy ClientPolicy --model=Client`.
2.  **Implement Policy Logic:** Implement the `view` method in `ClientPolicy` to check if the user has the required 'Owner' or 'Office' role.
3.  **Create DTOs:**
    - Create the `App\Dto\Client\ClientData` DTO (`sail artisan make:data Dto/Client/ClientData`).
    - Create the `App\Dto\Vehicle\VehicleData` DTO (`sail artisan make:data Dto/Vehicle/VehicleData`).
    - Annotate both DTOs with `#[TypeScript]` to enable automatic type generation.
4.  **Create Service:** If it doesn't exist, create the `ClientService` class (`sail artisan make:class Services/ClientService`).
5.  **Implement Service Logic:** Add a method to `ClientService` (e.g., `prepareClientShowData`) that accepts a `Client` object, performs the eager loading with `withCount`, and returns the data structured with the `ClientData` and `VehicleData` DTOs.
6.  **Implement Controller Action:**
    - Add the `show(Client $client)` method to `ClientController`.
    - Inject `ClientService` into the controller's constructor.
    - Call `$this->authorize('view', $client);` at the beginning of the method.
    - Call the service method to get the prepared data.
    - Return `Inertia::render('pages/clients/show', ['client' => $clientData, 'vehicles' => $vehicleDataCollection]);`.
7.  **Generate TypeScript Types:** Run `sail artisan typescript:transform` to generate the corresponding types for the frontend.
8.  **Create React Component:** Create the `resources/js/pages/clients/show.tsx` component to display the client and vehicle data passed in the props.
9.  **Create Feature Test:**
    - Update Client/ClientControllerTest.php
    - Write tests to cover:
        - A successful response (200 OK) for an authorized user, asserting the component and props structure.
        - A 403 Forbidden response for an unauthorized user.
        - A 404 Not Found response for a non-existent client ID.
10. **Code Formatting:** Run `sail pint --dirty` to ensure the new PHP code adheres to the project's coding style.
