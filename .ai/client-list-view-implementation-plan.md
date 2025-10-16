# Plan implementacji widoku Lista Klientów

## 1. Przegląd
Widok "Lista Klientów" jest centralnym miejscem do zarządzania klientami warsztatu. Jego głównym celem jest umożliwienie użytkownikom (Właściciel, Biuro) przeglądania, wyszukiwania i filtrowania listy wszystkich klientów. Widok prezentuje kluczowe informacje w formie paginowanej tabeli danych i stanowi punkt wyjścia do dalszych akcji, takich jak dodawanie, edycja czy usuwanie klientów.

## 2. Routing widoku
Widok będzie dostępny pod następującą ścieżką:
-   **URL:** `/clients`
-   **Nazwa trasy (Laravel):** `clients.index`

## 3. Struktura komponentów
Hierarchia komponentów dla widoku listy klientów będzie wyglądać następująco. Zakłada się istnienie nadrzędnego komponentu `AuthenticatedLayout`, który zapewnia spójny layout dla zalogowanych użytkowników.

```
AuthenticatedLayout
└── ClientsPage (resources/js/pages/clients/index.tsx)
    ├── PageHeader
    │   ├── Tytuł ("Klienci")
    │   └── Przycisk ("Dodaj klienta")
    └── ClientsDataTable
        ├── Pasek narzędzi tabeli (wyszukiwanie, filtry)
        ├── Tabela danych (zbudowana z użyciem @tanstack/react-table)
        │   ├── Nagłówek tabeli (z obsługą sortowania)
        │   └── Ciało tabeli
        │       └── Wiersz klienta
        │           ├── Komórki z danymi
        │           └── Komórka z akcjami (DataTableRowActions)
        └── Paginacja tabeli (DataTablePagination)
```

## 4. Szczegóły komponentów
### ClientsPage
-   **Opis komponentu:** Główny komponent strony, renderowany przez Inertia. Odpowiada za orkiestrację i składanie całego widoku z mniejszych, reużywalnych komponentów. Otrzymuje dane (`clients`, `filters`) bezpośrednio z kontrolera Laravel.
-   **Główne elementy:** `PageHeader`, `ClientsDataTable`.
-   **Obsługiwane interakcje:** Brak bezpośrednich interakcji; przekazuje obsługę zdarzeń i dane do komponentów podrzędnych.
-   **Obsługiwana walidacja:** Brak.
-   **Typy:** `ClientsPageProps`.
-   **Propsy:**
    ```typescript
    interface ClientsPageProps {
      clients: PaginatedResponse<App.Dto.Client.ClientListItemData>;
      filters: App.Dto.Client.FiltersData;
    }
    ```

### PageHeader
-   **Opis komponentu:** Reużywalny komponent do wyświetlania nagłówka strony, zawierającego tytuł i główne przyciski akcji.
-   **Główne elementy:** `<h1>` dla tytułu, `Button` dla akcji.
-   **Obsługiwane interakcje:** Kliknięcie przycisku "Dodaj klienta" nawiguje do strony tworzenia nowego klienta (`/clients/create`).
-   **Obsługiwana walidacja:** Brak.
-   **Typy:** Brak specyficznych.
-   **Propsy:**
    ```typescript
    interface PageHeaderProps {
      title: string;
      children?: React.ReactNode; // Miejsce na przyciski akcji
    }
    ```

### ClientsDataTable
-   **Opis komponentu:** Sercem widoku jest tabela danych, która wyświetla listę klientów. Będzie ona zarządzać logiką sortowania, filtrowania (wyszukiwania) oraz akcjami dla poszczególnych wierszy. Zostanie zaimplementowana przy użyciu biblioteki `@tanstack/react-table` oraz komponentów UI z Shadcn.
-   **Główne elementy:** `Input` (dla wyszukiwania), `Table`, `TableHeader`, `TableRow`, `TableCell`, `DropdownMenu` (dla akcji), `DataTablePagination`.
-   **Obsługiwane interakcje:** Wpisywanie tekstu w polu wyszukiwania, klikanie nagłówków kolumn w celu sortowania, interakcja z menu akcji dla wiersza.
-   **Obsługiwana walidacja:** Brak po stronie frontendu; logika biznesowa i walidacja parametrów (sort, search) odbywa się na backendzie.
-   **Typy:** `PaginatedResponse<App.Dto.Client.ClientListItemData>`, `App.Dto.Client.FiltersData`.
-   **Propsy:**
    ```typescript
    interface ClientsDataTableProps {
      clients: PaginatedResponse<App.Dto.Client.ClientListItemData>;
      filters: App.Dto.Client.FiltersData;
    }
    ```

## 5. Typy
Do implementacji widoku wymagane są następujące struktury typów w TypeScript.

Do implementacji widoku wykorzystane zostaną następujące, w większości auto-generowane, struktury typów:

1.  **`App.Dto.Client.ClientListItemData`** (z `generated-dto.d.ts`): Podstawowy typ dla pojedynczego klienta na liście.
2.  **`App.Dto.Client.FiltersData`** (z `generated-dto.d.ts`): Typ dla obiektu filtrów.
3.  **`PaginatedResponse<T>`** (z `laravel.d.ts`): Ręcznie zdefiniowany, generyczny typ opakowujący paginowane odpowiedzi z API.

Dzięki temu podejściu unikamy duplikacji i utrzymujemy spójność z definicjami DTO na backendzie. Właściwość `full_name` będzie tworzona dynamicznie w komponencie React.

## 6. Zarządzanie stanem
Zarządzanie stanem filtrów (wyszukiwanie, sortowanie) oraz obsługa ponownego pobierania danych zostanie zamknięta w dedykowanym customowym hooku `useDataTableFilters`.

-   **Cel hooka:** Abstrakcja logiki związanej z interakcjami z tabelą, które wymagają ponownego zapytania do serwera.
-   **Działanie:**
    1.  Inicjalizuje swój wewnętrzny stan na podstawie `filters` otrzymanych z propsów.
    2.  Udostępnia funkcje do modyfikacji stanu (np. `handleSearch`, `handleSort`).
    3.  Używa `useEffect` do obserwacji zmian w swoim stanie.
    4.  Po wykryciu zmiany, wywołuje `router.get()` z Inertia, aby pobrać nowe dane od serwera, przekazując zaktualizowane parametry filtrów. Używa opcji `{ preserveState: true, replace: true }` dla płynnego odświeżania danych bez przeładowania strony.
    5.  Dla pola wyszukiwania zostanie zastosowany debouncing, aby uniknąć nadmiernych zapytań do API podczas pisania.

## 7. Integracja API
Integracja z backendem odbywa się w całości za pośrednictwem Inertia.js.

-   **Endpoint:** `GET /clients`
-   **Kontroler/Akcja:** `ClientController@index`
-   **Żądanie:** Parametry żądania są przekazywane jako query string w URL.
    -   `page`: numer strony (obsługiwany przez paginator Laravela)
    -   `search`: ciąg znaków do wyszukiwania
    -   `sort`: nazwa kolumny do sortowania (np. `last_name`)
    -   `direction`: kierunek sortowania (`asc` lub `desc`)
-   **Odpowiedź:** Kontroler zwraca komponent `clients/index` z propsami, których typy na frontendzie będą wyglądać następująco:
    -   `clients`: `PaginatedResponse<App.Dto.Client.ClientListItemData>`
    -   `filters`: `App.Dto.Client.FiltersData`

## 8. Interakcje użytkownika
-   **Wyszukiwanie klienta:** Użytkownik wpisuje frazę w polu wyszukiwania. Po krótkim opóźnieniu (debounce), lista klientów jest automatycznie odświeżana, aby pokazać pasujące wyniki.
-   **Sortowanie listy:** Użytkownik klika na nagłówek kolumny (np. "Imię i nazwisko"). Lista jest odświeżana i sortowana według tej kolumny. Ponowne kliknięcie zmienia kierunek sortowania.
-   **Zmiana strony:** Użytkownik klika na przycisk paginacji (np. "Następna" lub numer strony). Tabela ładuje odpowiedni zestaw danych.
-   **Dodawanie klienta:** Użytkownik klika przycisk "Dodaj klienta", co powoduje przejście do formularza tworzenia nowego klienta.
-   **Akcje na wierszu:** Użytkownik klika menu akcji na danym wierszu, aby wyświetlić opcje takie jak "Edytuj" lub "Usuń", które prowadzą do odpowiednich akcji/widoków.

## 9. Warunki i walidacja
Cała walidacja parametrów wejściowych (takich jak `sort`, `direction`) jest przeprowadzana po stronie backendu przez `IndexClientRequest`. Frontend jest odpowiedzialny jedynie za dostarczenie poprawnego interfejsu do tworzenia tych parametrów:
-   **Wyszukiwanie:** Pole tekstowe.
-   **Sortowanie:** Kliknięcie w predefiniowane nagłówki kolumn generuje poprawne i dozwolone wartości dla parametrów `sort` i `direction`.

## 10. Obsługa błędów
-   **Błąd ładowania danych:** W przypadku błędu serwera podczas pobierania listy klientów (np. błąd 500), globalny system obsługi błędów Inertia powinien wyświetlić stosowne powiadomienie (np. Toast). Tabela powinna wyświetlić stan błędu, informując użytkownika o problemie.
-   **Pusta lista:** Jeśli nie zostaną znalezieni żadni klienci (lub nie ma ich w ogóle w systemie), tabela wyświetli komunikat "Nie znaleziono klientów", zachęcając do dodania pierwszego.
-   **Brak wyników wyszukiwania:** Jeśli aktywne filtry nie zwrócą żadnych wyników, tabela wyświetli komunikat "Nie znaleziono klientów pasujących do Twoich kryteriów".

## 11. Kroki implementacji
1.  **Struktura plików:** Utworzenie pliku komponentu `resources/js/pages/clients/index.tsx` oraz ewentualnych podkomponentów w `resources/js/components/clients/`.
2.  **Typy:** Upewnienie się, że plik `resources/js/types/laravel.d.ts` istnieje i zawiera generyczny typ `PaginatedResponse<T>`.
3.  **Komponent główny (`ClientsPage`):** Stworzenie szkieletu strony, który przyjmuje propsy `clients` i `filters` zgodnie ze zaktualizowanymi typami (`PaginatedResponse<App.Dto.Client.ClientListItemData>` i `App.Dto.Client.FiltersData`) i renderuje komponenty `PageHeader` i `ClientsDataTable`.
4.  **Komponent `ClientsDataTable`:**
    -   Implementacja definicji kolumn dla `@tanstack/react-table`, określając nagłówki, sposób renderowania komórek (w tym dynamiczne tworzenie `full_name`) i flagi sortowania.
    -   Dodanie paska narzędzi z polem do wyszukiwania.
    -   Zintegrowanie komponentu paginacji.
5.  **Custom Hook (`useDataTableFilters`):**
    -   Implementacja logiki do zarządzania stanem filtrów.
    -   Dodanie funkcji obsługujących zmiany (wyszukiwanie z debouncingiem, sortowanie).
    -   Zintegrowanie z `router` z Inertia w celu odświeżania danych.
6.  **Połączenie logiki:** Wykorzystanie hooka `useDataTableFilters` w komponencie `ClientsDataTable` do połączenia interakcji użytkownika (wpisywanie tekstu, klikanie) z ponownym pobieraniem danych.
7.  **Akcje dla wiersza:** Stworzenie komponentu `DataTableRowActions` z `DropdownMenu`, zawierającego linki do edycji i przycisk do usuwania klienta (z modalem potwierdzającym).
8.  **Stylowanie:** Dopracowanie wyglądu wszystkich komponentów przy użyciu Tailwind CSS, zgodnie z systemem designu aplikacji.
9.  **Testowanie:** Manualne przetestowanie wszystkich interakcji: wyszukiwania, sortowania (w obu kierunkach), paginacji i akcji dla wierszy. Sprawdzenie obsługi pustych stanów i błędów.
