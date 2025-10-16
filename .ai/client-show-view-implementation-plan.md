# Plan implementacji widoku: Podgląd Klienta (Client Show)

## 1. Przegląd
Celem tego widoku jest wyświetlenie szczegółowych informacji o pojedynczym kliencie oraz listy wszystkich pojazdów powiązanych z tym klientem. Widok będzie stanowił centralny punkt do przeglądania danych klienta i nawigowania do szczegółów jego pojazdów. Interfejs zostanie podzielony na zakładki, aby zachować czytelność i porządek.

## 2. Routing widoku
Widok będzie dostępny pod następującą ścieżką, wykorzystując route-model binding Laravela:
-   **Ścieżka URL:** `/clients/{client}`
-   **Nazwa trasy:** `clients.show`

## 3. Struktura komponentów
Komponenty zostaną zorganizowane w hierarchiczną strukturę, aby zapewnić reużywalność i czytelność kodu.

```
- ClientShowPage (`pages/clients/show.tsx`)
  - AuthenticatedLayout (`layouts/authenticated-layout.tsx`)
    - PageHeader (`components/page-header.tsx`)
    - Tabs (`@/components/ui/tabs`)
      - TabsList
        - TabsTrigger ("Dane klienta")
        - TabsTrigger ("Pojazdy")
      - TabsContent ("Dane klienta")
        - ClientDetailsCard (`components/clients/client-details-card.tsx`)
      - TabsContent ("Pojazdy")
        - ClientVehiclesTable (`components/clients/client-vehicles-table.tsx`)
```

## 4. Szczegóły komponentów

### `ClientShowPage`
-   **Opis komponentu:** Główny komponent strony, który otrzymuje dane z backendu poprzez propsy Inertia. Odpowiada za renderowanie ogólnego layoutu, w tym `PageHeader` oraz systemu zakładek (`Tabs`) do przełączania się między widokiem szczegółów klienta a listą jego pojazdów.
-   **Główne elementy:** Komponent `AuthenticatedLayout`, komponent `PageHeader`, komponent `Tabs` z `TabsList` i `TabsContent`.
-   **Obsługiwane interakcje:** Brak bezpośrednich interakcji; komponent deleguje obsługę zdarzeń i przekazuje dane do komponentów podrzędnych.
-   **Obsługiwana walidacja:** Brak.
-   **Typy:** `ClientShowPageProps`
-   **Propsy:** `{ client: App.Dto.Client.ClientData, vehicles: App.Dto.Vehicle.VehicleData[] }`

### `PageHeader`
-   **Opis komponentu:** Reużywalny komponent nagłówka strony. Wyświetli ścieżkę nawigacyjną (breadcrumbs), tytuł strony (imię i nazwisko klienta) oraz przyciski akcji (np. "Edytuj klienta").
-   **Główne elementy:** Elementy `div`, `h1`, komponenty do breadcrumbs, komponent `Button`.
-   **Obsługiwane interakcje:** Kliknięcie przycisku "Edytuj", które przeniesie użytkownika na stronę edycji klienta.
-   **Obsługiwana walidacja:** Brak.
-   **Typy:** `PageHeaderProps` (do zdefiniowania).
-   **Propsy:** `{ title: string, breadcrumbs: BreadcrumbItem[], actions: React.ReactNode }`

### `ClientDetailsCard`
-   **Opis komponentu:** Komponent wyświetlający wszystkie dane kontaktowe i adresowe klienta w estetycznej formie karty (`Card` z biblioteki shadcn/ui). Prezentuje informacje takie jak numer telefonu, e-mail oraz pełny adres.
-   **Główne elementy:** Komponent `Card`, `CardHeader`, `CardTitle`, `CardContent`. Dane zostaną przedstawione w formie listy definicji (`<dl>`, `<dt>`, `<dd>`) dla lepszej semantyki i dostępności.
-   **Obsługiwane interakcje:** Brak. Komponent jest tylko do odczytu.
-   **Obsługiwana walidacja:** Sprawdzanie, czy opcjonalne pola (np. `last_name`, `email`, części adresu) nie są `null`. Jeśli są, wyświetlany jest placeholder (np. "—").
-   **Typy:** `ClientDetailsCardProps`
-   **Propsy:** `{ client: App.Dto.Client.ClientData }`

### `ClientVehiclesTable`
-   **Opis komponentu:** Tabela danych (`DataTable`) prezentująca listę pojazdów przypisanych do klienta. Będzie zawierać kluczowe informacje o każdym pojeździe oraz liczbę powiązanych z nim zleceń naprawy. Każdy wiersz tabeli będzie klikalny, prowadząc do strony szczegółów danego pojazdu.
-   **Główne elementy:** Komponent `DataTable` oparty na `Table` z shadcn/ui. Kolumny: Marka, Model, Rok, Nr. rejestracyjny, VIN, Liczba zleceń.
-   **Obsługiwane interakcje:** Kliknięcie wiersza tabeli w celu nawigacji do strony szczegółów pojazdu (`vehicles.show`).
-   **Obsługiwana walidacja:** Sprawdzanie, czy tablica `vehicles` jest pusta. Jeśli tak, wyświetlany jest komunikat "Brak pojazdów dla tego klienta" oraz przycisk "Dodaj pojazd".
-   **Typy:** `ClientVehiclesTableProps`.
-   **Propsy:** `{ vehicles: App.Dto.Vehicle.VehicleData[] }`

## 5. Typy
Do implementacji widoku wymagane będą następujące typy. Istniejące typy DTO zostaną zaimportowane z `resources/js/types/generated-dto.d.ts`.

-   **`ClientShowPageProps`**: Główny interfejs propsów dla strony.
    ```typescript
    import { App } from '@/types/generated-dto';

    export interface ClientShowPageProps {
      client: App.Dto.Client.ClientData;
      vehicles: App.Dto.Vehicle.VehicleData[];
    }
    ```

-   **`ClientDetailsCardProps`**: Propsy dla komponentu karty szczegółów klienta.
    ```typescript
    import { App } from '@/types/generated-dto';

    export interface ClientDetailsCardProps {
      client: App.Dto.Client.ClientData;
    }
    ```

-   **`ClientVehiclesTableProps`**: Propsy dla komponentu tabeli pojazdów.
    ```typescript
    import { App } from '@/types/generated-dto';

    export interface ClientVehiclesTableProps {
      vehicles: App.Dto.Vehicle.VehicleData[];
    }
    ```

## 6. Zarządzanie stanem
Stan w tym widoku jest prosty i opiera się głównie na danych otrzymanych z backendu przez Inertia.
-   **Stan globalny/propsy:** Dane `client` i `vehicles` są przekazywane jako propsy i traktowane jako niezmienne w obrębie tego widoku.
-   **Stan lokalny:** Komponent `Tabs` z biblioteki shadcn/ui zarządza swoim stanem wewnętrznie (która zakładka jest aktywna), więc nie ma potrzeby tworzenia dodatkowego stanu za pomocą `useState` do tego celu.

Nie ma potrzeby implementacji customowego hooka dla tego widoku.

## 7. Integracja z backendem
Integracja z backendem odbywa się w pełni poprzez adapter Inertia.js.
-   **Żądanie:** Kiedy użytkownik przechodzi na URL `/clients/{id}`, przeglądarka wysyła standardowe żądanie `GET`.
-   **Odpowiedź:** Kontroler `ClientController@show` zwraca odpowiedź Inertia, która zawiera komponent `clients/show` oraz obiekt `props` zgodny z typem `ClientShowPageProps`. Frontend nie musi wykonywać żadnych dodatkowych zapytań `fetch` ani `axios` do pobrania danych.

## 8. Interakcje użytkownika
-   **Przełączanie zakładek:** Użytkownik może swobodnie przełączać się między zakładką "Dane klienta" a "Pojazdy", klikając na odpowiednie nagłówki.
-   **Nawigacja do edycji klienta:** Kliknięcie przycisku "Edytuj" w `PageHeader` przekieruje użytkownika na stronę edycji (`/clients/{id}/edit`) za pomocą `router.get()`.
-   **Nawigacja do szczegółów pojazdu:** Kliknięcie dowolnego wiersza w tabeli pojazdów przekieruje użytkownika na stronę szczegółów danego pojazdu (`/vehicles/{vehicle_id}`) za pomocą `router.get()`.

## 9. Warunki i walidacja
Widok jest przeznaczony głównie do odczytu, więc walidacja dotyczy głównie poprawnego wyświetlania danych.
-   **Puste pola:** W komponencie `ClientDetailsCard`, opcjonalne pola (`last_name`, `email`, adres), które mają wartość `null`, powinny wyświetlać zdefiniowany placeholder (np. "—" lub "Brak danych"), aby uniknąć pustych miejsc w interfejsie.
-   **Brak pojazdów:** W komponencie `ClientVehiclesTable`, jeśli tablica `vehicles` jest pusta, tabela powinna wyświetlić stan "pusty" z czytelnym komunikatem i przyciskiem umożliwiającym dodanie nowego pojazdu.

## 10. Obsługa błędów
Obsługa błędów HTTP jest w większości zarządzana przez Laravela i Inertia.
-   **Błąd 404 (Not Found):** Jeśli klient o podanym ID nie istnieje, framework wyświetli standardową stronę błędu 404.
-   **Błąd 403 (Forbidden):** Jeśli zalogowany użytkownik nie ma uprawnień do przeglądania danych klienta, framework wyświetli stronę błędu 403.
-   **Błędy renderowania:** Komponenty powinny być napisane w taki sposób, aby poprawnie obsługiwać wartości `null` lub `undefined` w danych, zapobiegając błędom wykonania po stronie klienta.

## 11. Kroki implementacji
1.  **Utworzenie plików komponentów:** Stworzyć puste pliki dla nowych komponentów:
    -   `resources/js/pages/clients/show.tsx`
    -   `resources/js/components/clients/client-details-card.tsx`
    -   `resources/js/components/clients/client-vehicles-table.tsx`
2.  **Definicja typów:** Zdefiniować interfejsy `ClientShowPageProps` i propsy dla poszczególnych komponentów w dedykowanym pliku (np. `resources/js/types/pages.d.ts`), wykorzystując bezpośrednio typy z `generated-dto.d.ts`.
3.  **Implementacja `ClientDetailsCard`:** Zaimplementować komponent wyświetlający dane klienta, dbając o obsługę pustych pól.
4.  **Implementacja `ClientVehiclesTable`:**
    -   Skonfigurować kolumny dla `DataTable`.
    -   Zaimplementować logikę wyświetlania danych pojazdów.
    -   Dodać obsługę kliknięcia wiersza w celu nawigacji.
    -   Zaimplementować "pusty stan" tabeli.
5.  **Implementacja `ClientShowPage`:**
    -   Złożyć główny layout strony, używając `AuthenticatedLayout`.
    -   Dodać `PageHeader`, przekazując odpowiednie `propsy` (tytuł, breadcrumbs, przycisk edycji).
    -   Zintegrować komponent `Tabs` z shadcn/ui.
    -   Umieścić `ClientDetailsCard` i `ClientVehiclesTable` w odpowiednich zakładkach (`TabsContent`).
    -   Połączyć wszystko z `propsami` otrzymanymi od Inertia.
6.  **Stylowanie i testowanie responsywności:** Upewnić się, że widok wygląda poprawnie na różnych rozmiarach ekranu, ze szczególnym uwzględnieniem tabeli danych na urządzeniach mobilnych.
7.  **Weryfikacja końcowa:** Przetestować wszystkie interakcje użytkownika: przełączanie zakładek, działanie linków i przycisków, a także poprawne wyświetlanie danych dla różnych scenariuszy (klient z pojazdami i bez, klient z kompletnymi i niekompletnymi danymi).
