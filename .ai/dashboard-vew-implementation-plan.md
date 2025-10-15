# Plan implementacji widoku pulpitu nawigacyjnego (Dashboard)

## 1. Przegląd
Pulpit nawigacyjny (`Dashboard`) jest głównym ekranem aplikacji po zalogowaniu. Jego celem jest dostarczenie użytkownikowi (Właścicielowi, pracownikowi Biura lub Mechanikowi) szybkiego, wysokopoziomowego przeglądu bieżącego statusu warsztatu. Widok jest przeznaczony tylko do odczytu i prezentuje kluczowe wskaźniki, takie jak liczba aktywnych zleceń, zleceń oczekujących na odbiór, łączny czas pracy zarejestrowany w danym dniu oraz listę ostatnio aktualizowanych zleceń.

## 2. Routing widoku
Widok pulpitu nawigacyjnego będzie dostępny pod następującą ścieżką:
- **URL:** `/dashboard`
- **Nazwa trasy (Laravel):** `dashboard`

## 3. Struktura komponentów
Komponenty zostaną zorganizowane w hierarchiczną strukturę, aby zapewnić czytelność i reużywalność kodu.

```
/resources/js/pages/dashboard/index.tsx (DashboardPage)
└── AppLayout
    ├── Head (tytuł strony)
    ├── PageHeader (nagłówek z tytułem i nawigacją)
    └── Główna treść
        ├── Sekcja statystyk (grid)
        │   ├── StatCard (dla aktywnych zleceń)
        │   ├── StatCard (dla zleceń do odbioru)
        │   └── StatCard (dla dzisiejszego czasu pracy)
        └── RecentOrdersTable (tabela ostatnich zleceń)
```

## 4. Szczegóły komponentów

### `DashboardPage`
- **Opis komponentu:** Główny kontener strony, który otrzymuje dane z `DashboardController` poprzez propsy Inertia. Odpowiada za renderowanie ogólnego layoutu strony (`AppLayout`) oraz przekazywanie danych do komponentów podrzędnych.
- **Główne elementy:** `AppLayout`, `Head` z `@inertiajs/react`, kontener `div` dla siatki statystyk oraz komponent `RecentOrdersTable`.
- **Obsługiwane interakcje:** Brak.
- **Obsługiwana walidacja:** Brak.
- **Typy:** `App.Dto.DashboardData`.
- **Propsy:** Komponent otrzymuje cały obiekt `DashboardData` bezpośrednio od Inertia.
  ```typescript
  // Propsy dostarczone przez Inertia do komponentu strony
  interface DashboardPageProps extends App.Dto.DashboardData {}
  ```

### `StatCard` (komponent do stworzenia)
- **Opis komponentu:** Reużywalny komponent do wyświetlania pojedynczej statystyki. Będzie składał się z tytułu, wartości oraz ikony. Zostanie użyty trzykrotnie do wyświetlenia kluczowych metryk. Zbudowany w oparciu o komponent `Card` z biblioteki Shadcn UI.
- **Główne elementy:** `Card`, `CardHeader`, `CardTitle`, `CardContent` z `@/components/ui/card`, oraz ikona z `lucide-react`.
- **Obsługiwane interakcje:** Brak.
- **Obsługiwana walidacja:** Brak.
- **Typy:** `StatCardProps`.
- **Propsy:**
  ```typescript
  interface StatCardProps {
    title: string;
    value: string | number;
    description?: string;
    icon: React.ElementType; // Komponent ikony, np. Timer, PackageCheck
  }
  ```

### `RecentOrdersTable` (komponent do stworzenia)
- **Opis komponentu:** Komponent wyświetlający listę ostatnio aktualizowanych zleceń w formie tabeli. Powinien zawierać kolumny: Pojazd, Klient, Status i Data utworzenia. Zbudowany w oparciu o komponent `Table` z biblioteki Shadcn UI.
- **Główne elementy:** `Card`, `CardHeader`, `CardTitle`, `CardContent` oraz `Table`, `TableHeader`, `TableBody`, `TableRow`, `TableCell` z `@/components/ui/table`.
- **Obsługiwane interakcje:** W przyszłości wiersze tabeli mogą stać się klikalne, aby nawigować do szczegółów zlecenia. W obecnej implementacji interakcje nie są wymagane.
- **Obsługiwana walidacja:** Komponent powinien obsłużyć przypadek, gdy lista zleceń jest pusta, wyświetlając odpowiedni komunikat.
- **Typy:** `RecentOrdersTableProps`, `App.Dto.RecentOrderData`.
- **Propsy:**
  ```typescript
  interface RecentOrdersTableProps {
    orders: App.Dto.RecentOrderData[];
  }
  ```

### `StatusBadge` (komponent do stworzenia)
- **Opis komponentu:** Mały, reużywalny komponent do wyświetlania statusu zlecenia w formie kolorowej etykiety (`Badge`). Ułatwi to szybkie wizualne rozróżnienie statusów.
- **Główne elementy:** Komponent `Badge` z `@/components/ui/badge`.
- **Obsługiwane interakcje:** Brak.
- **Obsługiwana walidacja:** Brak.
- **Typy:** `StatusBadgeProps`, `App.Enums.RepairOrderStatus`.
- **Propsy:**
  ```typescript
  interface StatusBadgeProps {
    status: App.Enums.RepairOrderStatus;
  }
  ```

## 5. Typy
Implementacja będzie opierać się na typach generowanych automatycznie z backendu, dostępnych w `resources/types/generated.d.ts`.

- **`App.Dto.DashboardData`**: Główny obiekt danych dla widoku.
  ```typescript
  // Zgodnie z generated.d.ts
  type DashboardData = {
    activeOrdersCount: number;
    pendingOrdersCount: number;
    todayTimeEntriesTotal: number;
    recentOrders: Array<App.Dto.RecentOrderData>;
  };
  ```
- **`App.Dto.RecentOrderData`**: Obiekt reprezentujący pojedyncze zlecenie na liście ostatnich zleceń.
  ```typescript
  // Zgodnie z generated.d.ts
  type RecentOrderData = {
    id: number;
    vehicle: string;
    client: string;
    status: App.Enums.RepairOrderStatus; // Należy traktować jako enum
    created_at: string; // Format ISO 8601
  };
  ```

## 6. Zarządzanie stanem
Widok jest przeznaczony wyłącznie do odczytu i wszystkie niezbędne dane są dostarczane z serwera podczas ładowania strony. W związku z tym, nie ma potrzeby implementacji lokalnego stanu za pomocą `useState` ani bardziej złożonych narzędzi do zarządzania stanem. Dane są przekazywane jako niezmienne propsy.

## 7. Integracja API
Integracja z API odbywa się w pełni po stronie serwera za pomocą Inertia.js.
- **Endpoint:** `GET /dashboard`
- **Akcja:** Nawigacja do ścieżki `/dashboard` powoduje wywołanie metody `DashboardController@index`.
- **Odpowiedź:** Kontroler renderuje komponent `resources/js/pages/dashboard/index.tsx`, przekazując mu obiekt `DashboardData` jako propsy. Frontend nie wykonuje żadnych bezpośrednich wywołań `fetch` ani `axios`.

## 8. Interakcje użytkownika
- **Nawigacja do pulpitu:** Użytkownik, przechodząc pod adres `/dashboard`, zobaczy w pełni załadowany widok z danymi statystycznymi i tabelą.
- **Brak interakcji z danymi:** Wszystkie elementy na stronie są statyczne i służą jedynie do prezentacji danych. Kliknięcie na statystyki lub wiersze tabeli nie wywołuje żadnej akcji.

## 9. Warunki i walidacja
- **Uwierzytelnienie:** Dostęp do widoku jest chroniony przez middleware `auth` w Laravelu. Użytkownik musi być zalogowany.
- **Pusta lista zleceń:** Komponent `RecentOrdersTable` musi sprawdzić, czy props `orders` jest pustą tablicą. Jeśli tak, zamiast tabeli powinien wyświetlić komunikat, np. "Brak ostatnich zleceń do wyświetlenia".

## 10. Obsługa błędów
- **Błąd serwera (5xx):** W przypadku błędu po stronie serwera podczas pobierania danych, Inertia.js wyświetli domyślny modal błędu. Nie jest wymagana dodatkowa obsługa po stronie komponentu.
- **Brakujące dane:** Komponenty powinny być odporne na ewentualny brak danych (np. `null` lub `undefined`), chociaż TypeScript i DTO po stronie backendu minimalizują to ryzyko. Wartości liczbowe można domyślnie ustawić na `0`, a stringi na pusty ciąg znaków.
  - Przykład w `StatCard`: `<span>{value ?? 0}</span>`

## 11. Kroki implementacji
1.  **Weryfikacja i instalacja komponentów Shadcn UI:** Sprawdź, czy komponenty `Card` i `Table` są dostępne w `resources/js/components/ui`. Jeśli nie, zainstaluj je za pomocą polecenia `npx shadcn-ui@latest add card table badge`.
2.  **Stworzenie komponentu `StatusBadge`:** Utwórz nowy plik `resources/js/components/status-badge.tsx`. Zaimplementuj logikę mapowania wartości enuma `RepairOrderStatus` na odpowiedni tekst i wariant kolorystyczny komponentu `Badge`.
3.  **Stworzenie komponentu `StatCard`:** Utwórz nowy plik `resources/js/components/stat-card.tsx`. Zaimplementuj komponent zgodnie z opisem w sekcji 4, używając komponentu `Card` i przyjmując propsy zdefiniowane w `StatCardProps`.
4.  **Stworzenie komponentu `RecentOrdersTable`:** Utwórz nowy plik `resources/js/components/recent-orders-table.tsx`. Zaimplementuj tabelę przy użyciu komponentu `Table` z Shadcn UI. Tabela powinna renderować dane przekazane w propsie `orders`. Pamiętaj o obsłudze pustej tablicy oraz formatowaniu daty `created_at` (np. przy użyciu biblioteki `date-fns`).
5.  **Aktualizacja `DashboardPage`:** Zmodyfikuj istniejący plik `resources/js/pages/dashboard/index.tsx`.
    -   Zaimportuj nowo utworzone komponenty `StatCard` i `RecentOrdersTable`.
    -   Zdefiniuj propsy strony, używając typu `App.Dto.DashboardData`.
    -   Usuń istniejące elementy zastępcze (`PlaceholderPattern`).
    -   Zaimplementuj siatkę (grid) dla trzech komponentów `StatCard`, przekazując im odpowiednie dane: `activeOrdersCount`, `pendingOrdersCount`, `todayTimeEntriesTotal`. Dobierz odpowiednie ikony z `lucide-react`.
    -   Pod siatką statystyk umieść komponent `RecentOrdersTable` i przekaż mu `recentOrders`.
6.  **Stylowanie i weryfikacja:** Upewnij się, że wszystkie komponenty są poprawnie ostylowane zgodnie z Tailwind CSS i wyglądają spójnie z resztą aplikacji. Sprawdź responsywność widoku.