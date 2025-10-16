# Kompleksowy Plan Testów dla Aplikacji FixFlow

**Wersja:** 1.0
**Data:** 2025-10-16
**Autor:** Senior QA Engineer

---

## 1. Streszczenie (Executive Summary)

Niniejszy dokument przedstawia kompleksową strategię testowania dla aplikacji FixFlow, narzędzia do zarządzania czasem pracy w warsztacie samochodowym. Plan został opracowany na podstawie analizy Dokumentu Wymagań Produktu (PRD), Planu Architektury Systemu oraz Planów Widoków UI.

Główne cele testów to:
*   **Weryfikacja funkcjonalna:** Zapewnienie, że wszystkie funkcje aplikacji działają zgodnie z wymaganiami PRD.
*   **Zapewnienie integralności danych:** Potwierdzenie, że dane są poprawnie przetwarzane, przechowywane i izolowane w architekturze multi-tenancy.
*   **Weryfikacja ścieżek użytkownika:** Sprawdzenie, czy kluczowe procesy biznesowe (od rejestracji po raportowanie) są płynne, intuicyjne i wolne od błędów.
*   **Zapewnienie bezpieczeństwa i autoryzacji:** Potwierdzenie, że system ról i uprawnień działa poprawnie, uniemożliwiając nieautoryzowany dostęp do danych i funkcji.

Plan obejmuje dwa kluczowe filary: **Testy Funkcjonalne Backendu (Feature Tests)**, skupione na logice biznesowej i API, oraz **Testy End-to-End (E2E)**, symulujące rzeczywiste interakcje użytkowników z aplikacją.

---

## 2. Testy Funkcjonalne Backendu (Feature Tests)

### 2.1. Strategia

Testy funkcjonalne backendu będą realizowane jako testy automatyczne (Pest) na poziomie API. Każdy endpoint zdefiniowany w Planie Architektury będzie posiadał dedykowany zestaw testów. Główny nacisk zostanie położony na walidację danych wejściowych, poprawność logiki biznesowej, obsługę błędów oraz rygorystyczne testowanie mechanizmów multi-tenancy i kontroli dostępu opartej na rolach (Policies).

**Narzędzia:**
*   **Framework:** Pest
*   **Środowisko:** Dedykowana baza danych do testów (np. SQLite in-memory), odświeżana przed każdym testem (`RefreshDatabase`).

### 2.2. Zakres i Scenariusze Testowe

#### 2.2.1. Multi-Tenancy (Najwyższy Priorytet)
*   **Cel:** Weryfikacja pełnej izolacji danych pomiędzy różnymi warsztatami (tenantami).
*   **Scenariusz:**
    1.  Utwórz programowo dwa warsztaty (`Workshop A`, `Workshop B`).
    2.  W każdym warsztacie utwórz oddzielne zasoby: klientów, pojazdy, użytkowników.
    3.  Wykonaj żądanie API (np. `GET /clients`) jako użytkownik z `Workshop A`.
    4.  **Oczekiwany rezultat:** Odpowiedź API zawiera wyłącznie klientów należących do `Workshop A`. Dane z `Workshop B` nie są widoczne.
    5.  Wykonaj próbę nieautoryzowanego dostępu do zasobu z innego warsztatu (np. `GET /clients/{client_id_from_workshop_B}`) jako użytkownik z `Workshop A`.
    6.  **Oczekiwany rezultat:** Odpowiedź API zwraca błąd 404 (Not Found) lub 403 (Forbidden), zgodnie z implementacją `Policy`.

#### 2.2.2. Uwierzytelnianie i Autoryzacja (Spatie Permissions & Policies)
*   **Cel:** Weryfikacja mechanizmów logowania i systemu ról.
*   **Przypadki testowe:**
    *   **Rejestracja:** Pomyślne utworzenie warsztatu i użytkownika z rolą `Owner`. Testy walidacji (np. zduplikowany email, za krótkie hasło).
    *   **Logowanie:** Pomyślne logowanie dla każdej roli (`Owner`, `Office`, `Mechanic`). Nieudane logowanie przy błędnych danych.
    *   **Dostęp do endpointów:**
        *   Użytkownik z rolą `Office` próbuje uzyskać dostęp do `GET /users` -> Oczekiwany błąd 403.
        *   Użytkownik z rolą `Mechanic` próbuje utworzyć klienta (`POST /clients`) -> Oczekiwany błąd 403.
        *   Użytkownik z rolą `Owner` usuwa klienta (`DELETE /clients/{client}`) -> Oczekiwany sukces.

#### 2.2.3. Zarządzanie Klientami (`ClientController`)
*   **Cel:** Weryfikacja operacji CRUD na klientach.
*   **Przypadki testowe:**
    *   `POST /clients`: Test walidacji (np. brak `first_name`, niepoprawny format `email`). Pomyślne utworzenie klienta.
    *   `GET /clients`: Test paginacji, wyszukiwania i sortowania.
    *   `GET /clients/{client}`: Poprawne pobranie danych klienta.
    *   `PUT /clients/{client}`: Poprawna aktualizacja danych. Test walidacji przy aktualizacji.
    *   `DELETE /clients/{client}`: Pomyślne usunięcie (soft delete). Test logiki biznesowej (nie można usunąć klienta z przypisanymi pojazdami).

#### 2.2.4. Zarządzanie Pojazdami (`VehicleController`)
*   **Cel:** Weryfikacja operacji CRUD na pojazdach.
*   **Przypadki testowe:**
    *   `POST /vehicles`: Test walidacji (unikalność `vin` w obrębie warsztatu, poprawny `client_id`, zakres `year`).
    *   `GET /vehicles`: Test wyszukiwania po numerze rejestracyjnym, VIN, marce.
    *   `DELETE /vehicles/{vehicle}`: Test logiki biznesowej (nie można usunąć pojazdu z aktywnymi zleceniami).

#### 2.2.5. Zarządzanie Zleceniami Naprawy (`RepairOrderController`)
*   **Cel:** Weryfikacja logiki zarządzania zleceniami.
*   **Przypadki testowe:**
    *   `POST /repair-orders`: Test walidacji, pomyślne dodanie zlecenia z załącznikami (obrazami).
    *   `GET /repair-orders`: Test filtrowania po statusie.
    *   `PATCH /repair-orders/{order}/status`: Test zmiany statusu na dozwolony. Weryfikacja, czy zmiana została zarejestrowana w `activity_log`.
    *   `GET /repair-orders/{order}`: Weryfikacja poprawności agregacji danych (np. `total_time_minutes`).

#### 2.2.6. Ewidencja Czasu Pracy (`TimeEntryController`)
*   **Cel:** Weryfikacja dodawania i edycji wpisów czasu.
*   **Przypadki testowe:**
    *   `POST /time-entry`: Test walidacji (`duration_minutes` w zakresie 1-1440, istnienie `repair_order_id` i `mechanic_id`).
    *   `PUT /time-entries/{entry}`: Weryfikacja uprawnień (właściciel wpisu lub `Owner`/`Office` może edytować).
    *   `DELETE /time-entries/{entry}`: Weryfikacja uprawnień (`Owner`/`Office`).

### 2.3. Testy Integracyjne

Testy integracyjne będą naturalną częścią testów funkcjonalnych, koncentrując się na interakcjach między modelami i usługami.
*   **Zlecenie -> Wpisy Czasu:** Utworzenie zlecenia, dodanie kilku wpisów czasu, a następnie pobranie zlecenia i weryfikacja, czy pole `total_time_minutes` jest poprawnie obliczone.
*   **Klient -> Pojazd -> Zlecenie:** Sprawdzenie, czy po usunięciu (soft delete) klienta, jego pojazdy i zlecenia pozostają w bazie danych, ale nie są dostępne przez standardowe endpointy.
*   **Activity Log:** Weryfikacja, czy kluczowe operacje (zmiana statusu zlecenia, edycja wpisu czasu) generują odpowiednie wpisy w `spatie/laravel-activitylog`.

---

## 3. Testy End-to-End (E2E)

### 3.1. Strategia

Testy E2E będą symulować pełne, realistyczne scenariusze użytkowania aplikacji z perspektywy różnych ról. Celem jest weryfikacja integralności całego stosu technologicznego - od interakcji w UI (React), przez komunikację z API (Inertia), logikę backendu (Laravel), aż po zapis i odczyt z bazy danych. Testy te będą miały wyższy poziom abstrakcji niż testy backendowe i skupią się na krytycznych ścieżkach biznesowych.

**Narzędzia:**
*   **Framework:** Pest (z wykorzystaniem wbudowanego browser testing) lub Playwright/Cypress do bardziej zaawansowanych scenariuszy.

### 3.2. Krytyczne Ścieżki Użytkownika i Scenariusze

#### 3.2.1. Ścieżka 1: Rejestracja i Konfiguracja Nowego Warsztatu (Priorytet: Krytyczny)
*   **Rola:** Nowy Właściciel Warsztatu
*   **Cel:** Weryfikacja procesu onboardingu, od rejestracji po dodanie pierwszych pracowników.
*   **Kroki scenariusza:**
    1.  Wejdź na stronę `/register`.
    2.  Wypełnij formularz rejestracyjny poprawnymi danymi warsztatu i właściciela.
    3.  **Oczekiwany rezultat:** Użytkownik jest zalogowany i przekierowany na `/dashboard`. Widoczny jest komunikat powitalny.
    4.  Przejdź do sekcji "Mechanicy" (`/mechanics`) przez menu boczne.
    5.  Kliknij "Dodaj mechanika" i wypełnij formularz.
    6.  **Oczekiwany rezultat:** Nowy mechanik pojawia się na liście.
    7.  Przejdź do sekcji "Użytkownicy" (`/users`).
    8.  Kliknij "Dodaj użytkownika", wypełnij formularz, przypisując rolę `Office`.
    9.  **Oczekiwany rezultat:** Nowy użytkownik z poprawną rolą pojawia się na liście.

#### 3.2.2. Ścieżka 2: Pełen Cykl Obsługi Zlecenia dla Nowego Klienta (Priorytet: Krytyczny)
*   **Rola:** Pracownik Biura (`Office`)
*   **Cel:** Weryfikacja podstawowego przepływu pracy w aplikacji.
*   **Kroki scenariusza:**
    1.  Zaloguj się jako użytkownik z rolą `Office`.
    2.  Przejdź do `/clients` i utwórz nowego klienta.
    3.  **Oczekiwany rezultat:** Po zapisie następuje przekierowanie na stronę szczegółów nowego klienta.
    4.  Na stronie klienta dodaj nowy pojazd.
    5.  **Oczekiwany rezultat:** Po zapisie następuje przekierowanie na stronę szczegółów pojazdu.
    6.  Na stronie pojazdu utwórz nowe zlecenie naprawy, dodając opis usterki i załączając zdjęcie.
    7.  **Oczekiwany rezultat:** Po zapisie następuje przekierowanie na stronę zlecenia. Zlecenie ma status "Nowe".
    8.  Na stronie zlecenia, zmień jego status na "W naprawie".
    9.  **Oczekiwany rezultat:** Status zostaje zaktualizowany bez przeładowania strony, a zmiana jest widoczna w logu aktywności.

#### 3.2.3. Ścieżka 3: Rejestracja Czasu Pracy przez Mechanika (Priorytet: Krytyczny)
*   **Rola:** Mechanik
*   **Cel:** Weryfikacja kluczowej funkcjonalności dla mechaników - prostego i szybkiego ewidencjonowania czasu.
*   **Kroki scenariusza:**
    1.  Zaloguj się na wspólne konto mechanika.
    2.  **Oczekiwany rezultat:** Użytkownik ląduje na widoku zleceń w formie kart (`/repair-orders`).
    3.  Odszukaj zlecenie utworzone w Ścieżce 2.
    4.  Kliknij przycisk "Rejestruj czas".
    5.  W formularzu/modalu wybierz swoje imię z listy, wprowadź czas pracy (np. "1h 30m") i opcjonalny opis.
    6.  Zatwierdź formularz.
    7.  **Oczekiwany rezultat:** Widoczny jest komunikat o sukcesie (`Toast`), a formularz jest gotowy do ponownego użycia.
    8.  (Krok weryfikacyjny) Zaloguj się jako `Owner`/`Office`, przejdź do szczegółów zlecenia.
    9.  **Oczekiwany rezultat:** Nowy wpis czasu jest widoczny na liście, a całkowity czas pracy nad zleceniem został poprawnie zaktualizowany.

#### 3.2.4. Ścieżka 4: Przeglądanie Raportów przez Właściciela (Priorytet: Wysoki)
*   **Rola:** Właściciel Warsztatu (`Owner`)
*   **Cel:** Weryfikacja poprawności generowania i wyświetlania raportów wydajności.
*   **Kroki scenariusza:**
    1.  Zaloguj się jako `Owner`.
    2.  Przejdź do `/reports/team`.
    3.  Wybierz zakres dat obejmujący wpis czasu ze Ścieżki 3.
    4.  **Oczekiwany rezultat:** Raport jest generowany. Mechanik, który zarejestrował czas, jest widoczny na liście z poprawną sumą zarejestrowanych godzin.
    5.  Przejdź do `/reports/mechanic`.
    6.  Wybierz tego samego mechanika i zakres dat.
    7.  **Oczekiwany rezultat:** Widoczna jest szczegółowa lista wpisów czasu dla danego mechanika, zawierająca wpis ze Ścieżki 3.
