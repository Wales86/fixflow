# UI Architecture for FixFlow

## 1. UI Structure Overview

The FixFlow UI will be a responsive, role-based Single Page Application (SPA) built with React and Inertia.js, styled with Tailwind CSS and utilizing the shadcn/ui component library.

The architecture is centered around a persistent sidebar navigation layout for administrative roles (Owner, Office) and a simplified, task-focused interface for the Mechanic role. The core principle is to provide efficient data management for admins and an extremely streamlined, mobile-first time-logging experience for mechanics.

Data-heavy sections will use a reusable `DataTable` component that supports searching, sorting, and filtering, with state managed via URL query parameters for shareability and persistence. Complex views will use a tabbed interface to organize information logically. Quick actions will be handled through modals (`Dialogs`) to maintain context, while destructive actions will require confirmation (`AlertDialogs`). User feedback is provided via non-intrusive toast notifications.

## 2. View List

### Authentication Views

- **View Name:** Login
- **View Path:** `/login`
- **Primary Purpose:** To allow existing users to securely access the application.
- **Key Information:** Email, Password fields.
- **Key Components:** `Card`, `Input`, `Button`, `Form`.
- **UX/Accessibility/Security:** Standard login form with clear labels, password visibility toggle, and CSRF protection (handled by Laravel/Inertia).

- **View Name:** Register
- **View Path:** `/register`
- **Primary Purpose:** To allow a new workshop owner to register their workshop and create their owner account.
- **Key Information:** Workshop Name, Owner Name, Email, Password, Password Confirmation.
- **Key Components:** `Card`, `Input`, `Button`, `Form`.
- **UX/Accessibility/Security:** Multi-step form could be considered if more fields are added. Clear validation feedback.

### Dashboard

- **View Name:** Dashboard
- **View Path:** `/dashboard`
- **Primary Purpose:** To provide a high-level, at-a-glance overview of the workshop's current status.
- **Key Information:**
    - Count of active repair orders.
    - Count of orders ready for pickup.
    - Total hours logged today.
    - List of recently updated orders.
- **Key Components:** `PageHeader`, `StatCard`, `DataTable` (for recent orders).
- **UX/Accessibility/Security:** Data is read-only. Accessible to all authenticated users.

### Client Views

- **View Name:** Client List
- **View Path:** `/clients`
- **Primary Purpose:** To list, search, and filter all clients in the workshop.
- **Key Information:** Paginated list of clients (Name, Contact Info, Vehicle Count).
- **Key Components:** `PageHeader`, `DataTable` (with search/sort/filter), `Button` (for "Add Client"), `DropdownMenu` (for row actions).
- **UX/Accessibility/Security:** Access restricted to Owner/Office. Table should be keyboard navigable.

- **View Name:** Client Show
- **View Path:** `/clients/{client}`
- **Primary Purpose:** To display all details for a single client, including their associated vehicles.
- **Key Information:** Client's full contact details, list of their vehicles.
- **Key Components:** `PageHeader`, `Card`, `Tabs` (for Details/Vehicles), `DataTable` (for vehicles).
- **UX/Accessibility/Security:** Clear hierarchy between client details and their vehicle list.

- **View Name:** Client Create/Edit
- **View Path:** `/clients/create`, `/clients/{client}/edit`
- **Primary Purpose:** To create a new client or update an existing one.
- **Key Information:** Form fields for all client attributes.
- **Key Components:** `PageHeader`, `Card`, `Form`, `Input`, `Button`.
- **UX/Accessibility/Security:** Forms will be fully responsive for mobile use. `useForm` hook will handle validation and error display.

### Vehicle Views

- **View Name:** Vehicle List / Search
- **View Path:** `/vehicles`
- **Primary Purpose:** To search for any vehicle in the workshop by registration, VIN, or make/model.
- **Key Information:** Paginated list of vehicles (Make, Model, Reg Number, Owner).
- **Key Components:** `PageHeader`, `DataTable` (with a prominent global search bar).
- **UX/Accessibility/Security:** Search should be the primary focus.

- **View Name:** Vehicle Show
- **View Path:** `/vehicles/{vehicle}`
- **Primary Purpose:** To display vehicle details and its complete repair history.
- **Key Information:** Vehicle details (Make, Model, VIN, etc.), Client info, paginated list of all associated repair orders.
- **Key Components:** `PageHeader`, `Card`, `DataTable` (for repair history).
- **UX/Accessibility/Security:** Provides a single source of truth for a vehicle's history.

- **View Name:** Vehicle Create/Edit
- **View Path:** `/vehicles/create`, `/vehicles/{vehicle}/edit`
- **Primary Purpose:** To add a new vehicle to a client or update an existing one.
- **Key Information:** Form fields for vehicle attributes, a searchable select dropdown for the client.
- **Key Components:** `PageHeader`, `Card`, `Form`, `Input`, `Button`, `Combobox` (for client selection).
- **UX/Accessibility/Security:** When creating from a client's page, the client should be pre-selected.

### Repair Order Views

- **View Name:** Repair Order List (Admin)
- **View Path:** `/repair-orders`
- **Primary Purpose:** For Owner/Office to view and manage all active repair orders.
- **Key Information:** Paginated list of orders (ID, Vehicle, Client, Status, Time Logged).
- **Key Components:** `PageHeader`, `DataTable` (with status filter).
- **UX/Accessibility/Security:** Role-based actions (e.g., only Owner can delete).

- **View Name:** Repair Order List (Mechanic)
- **View Path:** `/repair-orders` (rendered differently for Mechanic role)
- **Primary Purpose:** For Mechanics to see active orders and quickly log time.
- **Key Information:** Key order details (Vehicle, Problem).
- **Key Components:** `PageHeader`, `RepairOrderCard` (replaces `DataTable`). Each card has a prominent "Log Time" button.
- **UX/Accessibility/Security:** Mobile-first, card-based layout optimized for quick identification and action.

- **View Name:** Repair Order Show
- **View Path:** `/repair-orders/{order}`
- **Primary Purpose:** To be the central hub for a single repair order, showing all related information.
- **Key Information:** Order details, vehicle/client info, images, time entries, internal notes, activity log.
- **Key Components:** `PageHeader`, `Card`, `Tabs`, `StatusBadge`, `Dialog` (for adding notes/updating status).
- **UX/Accessibility/Security:** Uses tabs to prevent information overload. `can.*` flags from the API will control visibility of edit/delete buttons for notes and time entries.

- **View Name:** Repair Order Create/Edit
- **View Path:** `/repair-orders/create`, `/repair-orders/{order}/edit`
- **Primary Purpose:** To create or update a repair order.
- **Key Information:** Form with vehicle selection, problem description, status, and image uploader.
- **Key Components:** `PageHeader`, `Form`, `Textarea`, `ImageUploader`, `Combobox` (for vehicle).
- **UX/Accessibility/Security:** Image uploader will support drag-and-drop and show previews.

### Time Entry Views

- **View Name:** Time Entry Create
- **View Path:** `/time-entry` (or as a modal)
- **Primary Purpose:** To provide a fast, "kiosk-style" interface for mechanics to log work time.
- **Key Information:** Mechanic selection, Active Order selection, Duration, Description (optional).
- **Key Components:** `Form`, `Select`, `Button` (with preset times like "30m", "1h"), `Input` (for custom time).
- **UX/Accessibility/Security:** Mobile-first, large touch targets, minimal typing required. This is the primary interface for mechanics.

### Settings & Management Views

- **View Name:** Mechanic List
- **View Path:** `/mechanics`
- **Primary Purpose:** For Owner/Office to manage the list of mechanics available for time logging.
- **Key Components:** `PageHeader`, `DataTable`.

- **View Name:** User List
- **View Path:** `/users`
- **Primary Purpose:** For the Workshop Owner to manage user accounts and roles (Owner, Office).
- **Key Components:** `PageHeader`, `DataTable`.
- **UX/Accessibility/Security:** Access strictly limited to Owner role. Owner cannot delete their own account.

### Report Views

- **View Name:** Mechanic & Team Reports
- **View Path:** `/reports/mechanic`, `/reports/team`
- **Primary Purpose:** To generate and display performance reports.
- **Key Information:** Date range pickers, mechanic selector (for individual report), table of results (hours logged, etc.).
- **Key Components:** `PageHeader`, `DatePicker`, `Select`, `DataTable`.
- **UX/Accessibility/Security:** Read-only data visualization.

## 3. User Journey Map

**Primary Flow: New Repair Order from a New Client**

1.  **Login:** User (Owner/Office) logs in at `/login`.
2.  **Dashboard:** Lands at `/dashboard`.
3.  **Navigate to Clients:** Clicks "Clients" in the sidebar, navigates to `/clients`.
4.  **Create Client:** Clicks "Add Client", navigates to `/clients/create`. Submits form.
5.  **View Client:** Redirected to `/clients/{id}` for the new client.
6.  **Create Vehicle:** On the client's page, clicks "Add Vehicle", navigates to `/vehicles/create` (client is pre-selected). Submits form.
7.  **View Vehicle:** Redirected to `/vehicles/{id}` for the new vehicle.
8.  **Create Repair Order:** On the vehicle's page, clicks "Create Repair Order", navigates to `/repair-orders/create` (vehicle is pre-selected). Submits form.
9.  **View Order:** Redirected to `/repair-orders/{id}`. The order is now active.

**Secondary Flow: Mechanic Logs Time**

1.  **Login:** Mechanic logs in with a shared account at `/login`.
2.  **View Active Orders:** Lands at `/repair-orders` (card view).
3.  **Select Order:** Finds the correct `RepairOrderCard` for their task.
4.  **Log Time:** Clicks the "Log Time" button, which opens the `TimeEntryCreate` view/modal.
5.  **Submit Entry:** Selects their name, chooses a duration, adds a note, and saves.
6.  **Return:** Is returned to the `/repair-orders` list, with a success toast notification.

## 4. Layout and Navigation Structure

- **Main Layout:** A two-column layout featuring a persistent `Sidebar` on the left and the main content area on the right.
- **Sidebar:**
    - Contains links to all primary modules (`Dashboard`, `Repair Orders`, `Clients`, etc.).
    - Links are dynamically rendered based on the user's role.
- **Header:**
    - Sits above the main content area.
    - Contains a `PageHeader` component displaying the current page title, breadcrumbs for context, and primary action buttons (e.g., "Add New Client").
    - Includes a user profile dropdown for accessing settings and logging out.
- **Mobile Layout:** On smaller screens, the sidebar will collapse into a hamburger menu to maximize content visibility. Forms and tables will reflow into a single-column layout. The mechanic's card-based view is already mobile-first.

## 5. Key Components

- **`Layout`:** The main application shell managing the sidebar and header.
- **`DataTable`:** A highly reusable component for displaying lists of data. It will encapsulate logic for sorting, filtering (via a dedicated filter bar), pagination, and row-level actions within a `DropdownMenu`.
- **`PageHeader`:** Provides consistent page titles, breadcrumbs, and a slot for action buttons.
- **`StatCard`:** A simple card for displaying a single metric (e.g., "Active Orders") on the Dashboard.
- **`RepairOrderCard`:** A specialized card for the mechanic's order list, optimized for touch and quick actions.
- **`TimeEntryForm`:** A "kiosk-style" form with large controls designed for the time logging process.
- **`ImageUploader`:** A drag-and-drop component with image previews for attaching files to repair orders.
- **`StatusBadge`:** A color-coded badge to visually represent the status of a repair order.
- **`EmptyState`:** A placeholder shown when a data table is empty, with a call-to-action button.
- **`Skeleton`:** Used as a loading state placeholder to improve perceived performance.
- **`Dialog` & `AlertDialog`:** Modal components from shadcn/ui used for quick actions and confirmations.
- **`Toast`:** Used for displaying global success or error notifications after an action.
- **`Form` (Inertia's `useForm`):** The underlying logic for all data submission, providing state management, validation, and error handling.
