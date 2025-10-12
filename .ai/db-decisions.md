<conversation_summary>
<decisions>
1.  **Architektura Multi-Tenancy**: Zostanie zaimplementowana przy użyciu dedykowanego pakietu **`spatie/laravel-multitenancy`**, który zapewni solidną i bezpieczną izolację danych między warsztatami.
2.  **Zarządzanie Plikami**: Obsługa załączników i plików graficznych zostanie zrealizowana za pomocą pakietu **`spatie/laravel-medialibrary`**, co ułatwi zarządzanie mediami i integrację z chmurowym storage'em.
3.  **Architektura Kodu**: W celu utrzymania czystej architektury i zapewnienia silnie typowanego przepływu danych między warstwami aplikacji (Request -> Service) zostanie wykorzystany pakiet **`spatie/laravel-data`**.
4.  **Role i Uprawnienia**: Do zarządzania rolami (`Właściciel`, `Biuro`) i uprawnieniami zostanie użyty pakiet `spatie/laravel-permission`, zintegrowany z wbudowanym w Laravel mechanizmem Gates/Policies.
5.  **Audyt Zmian**: Historia kluczowych zmian w danych będzie rejestrowana za pomocą pakietu `spatie/laravel-activitylog`.
6.  **Klucze Główne i Usuwanie Danych**: Stosowane będą auto-inkrementowane klucze główne, a kluczowe encje będą wykorzystywać mechanizm "miękkiego usuwania" (soft delete).
7.  **Struktura Danych**: Kolumny dla danych osobowych i adresowych będą rozdzielone. Rocznik pojazdu (`year`) będzie przechowywany jako `SMALLINT`.
8.  **Statusy Zleceń**: Będą zarządzane jako natywne Enumy PHP 8.1+.
9.  **Dane Deweloperskie**: Zostaną przygotowane "seedery" do wypełniania bazy danych przykładowymi danymi.
</decisions>

<matched_recommendations>
1.  **Multi-Tenancy**: Wykorzystanie pakietu `spatie/laravel-multitenancy` jest najlepszą praktyką zapewniającą solidną, bezpieczną i skalowalną izolację danych, automatyzującą wiele skomplikowanych aspektów tego procesu.
2.  **Zarządzanie Mediami**: Zastosowanie `spatie/laravel-medialibrary` abstrahuje logikę przechowywania plików, ułatwia generowanie konwersji (np. miniaturek) i upraszcza powiązanie mediów z modelami.
3.  **Obiekty DTO**: Użycie `spatie/laravel-data` znacząco poprawia czytelność, utrzymywalność i bezpieczeństwo typów w kodzie aplikacji, szczególnie przy przekazywaniu danych do warstwy serwisowej.
4.  **Audyt i Role**: Dedykowane pakiety Spatie (`laravel-activitylog`, `laravel-permission`) to sprawdzone w boju, standardowe rozwiązania, które oszczędzają czas i dostarczają gotową, niezawodną funkcjonalność.
5.  **Izolacja Danych (RLS)**: Pakiet `laravel-multitenancy` zajmie się implementacją zasad bezpieczeństwa na poziomie wierszy w sposób znacznie bardziej zaawansowany i bezpieczny niż ręczna implementacja.
</matched_recommendations>

<database_planning_summary>
    **a. Główne wymagania dotyczące schematu bazy danych**

    Schemat bazy danych dla FixFlow MVP zostanie zbudowany w oparciu o architekturę multi-tenant, której fundamentem będzie pakiet `spatie/laravel-multitenancy`. Zapewni to pełną izolację danych na poziomie zapytań, zadań w kolejce i innych procesów. Zarządzanie plikami i historią zmian zostanie powierzone dedykowanym pakietom (`laravel-medialibrary`, `laravel-activitylog`), co pozwoli skupić się na logice biznesowej. Schemat będzie wykorzystywał auto-inkrementowane klucze i mechanizm soft-deletes dla kluczowych encji.

    **b. Kluczowe encje i ich relacje**

    *   **Tabele z pakietów (zostaną utworzone jako pierwsze)**:
        *   `tenants`: Tabela z pakietu `laravel-multitenancy`, będzie mapowana na nasz model `Workshop` i przechowywać dane warsztatów.
        *   `media`: Tabela z pakietu `laravel-medialibrary`, będzie polimorficznie przechowywać informacje o wszystkich załącznikach.
        *   `activity_log`: Tabela z pakietu `laravel-activitylog`, będzie polimorficznie przechowywać historię zmian.
        *   `roles`, `permissions`, `model_has_roles`, etc.: Tabele z pakietu `laravel-permission`.

    *   **Tabele aplikacji (zależne od powyższych)**:
        *   `users`: Użytkownicy. Posiada relację **wiele-do-jednego** z `tenants` (workshops).
        *   `mechanics`: Mechanicy. Posiada relację **wiele-do-jednego** z `tenants` (workshops).
        *   `clients`: Klienci. Posiada relację **wiele-do-jednego** z `tenants` (workshops).
        *   `vehicles`: Pojazdy. Posiada relację **wiele-do-jednego** z `clients`.
        *   `repair_orders`: Zlecenia naprawy. Posiada relację **wiele-do-jednego** z `vehicles`. Będzie powiązana z mediami (`media`) i historią zmian (`activity_log`).
        *   `time_entries`: Wpisy czasu pracy. Posiada relację **wiele-do-jednego** z `repair_orders` oraz z `mechanics`.
        *   `internal_notes`: Notatki do zlecenia. Posiada relację **wiele-do-jednego** z `repair_orders` oraz **relację polimorficzną** z autorem (`users` lub `mechanics`).

    **c. Ważne kwestie dotyczące bezpieczeństwa i skalowalności**

    *   **Bezpieczeństwo**: Wykorzystanie pakietu `spatie/laravel-multitenancy` stanowi trzon strategii bezpieczeństwa, automatyzując izolację danych i minimalizując ryzyko ich wycieku. Scentralizowana autoryzacja za pomocą `laravel-permission` i Gates/Policies zapewni spójną kontrolę dostępu w całej aplikacji.
    *   **Skalowalność**: Architektura oparta na wyspecjalizowanych pakietach jest wysoce skalowalna. `laravel-medialibrary` umożliwia bezproblemowe przejście na rozproszony system plików (np. S3), a `laravel-multitenancy` posiada strategie pozwalające w przyszłości na skalowanie bazy danych (np. osobna baza per tenant) bez konieczności przepisywania logiki aplikacji. Czysta architektura z `laravel-data` ułatwi utrzymanie i rozbudowę projektu w przyszłości.

    **d. Wszelkie nierozwiązane kwestie lub obszary wymagające dalszego wyjaśnienia**

    Wszystkie kluczowe aspekty schematu bazy danych dla wersji MVP zostały omówione i uzgodnione. Nie ma obecnie nierozwiązanych kwestii blokujących przejście do następnego etapu, którym jest instalacja pakietów i tworzenie plików migracji dla bazy danych.
</database_planning_summary>

<unresolved_issues>
Brak.
</unresolved_issues>
</conversation_summary>