Jasne, oto podsumowanie kluczowych decyzji dotyczących architektury interfejsu użytkownika, które posłuży jako fundament do dalszych prac implementacyjnych.

### Podsumowanie Architektury UI dla FixFlow MVP

#### 1. Architektura Ogólna i Nawigacja

*   **Layout:** Elastyczna struktura oparta na starter kicie, domyślnie wykorzystująca **nawigację boczną (sidebar)**, z możliwością przełączenia na nawigację górną.
*   **Nawigacja Zależna od Roli:**
    *   **Właściciel:** Pełen dostęp do wszystkich modułów.
    *   **Biuro:** Dostęp do wszystkiego z wyjątkiem zarządzania użytkownikami.
    *   **Mechanik:** Dostęp wyłącznie do modułu Zleceń.
*   **Struktura Menu (Właściciel / Biuro):**
    1.  `Dashboard` (Panel główny)
    2.  `Zlecenia` (Repair Orders)
    3.  `Klienci` (Clients)
    4.  `Pojazdy` (Vehicles)
    5.  `Mechanicy` (Mechanics)
    6.  `Raporty` (Reports)
    7.  `Użytkownicy` (Users - *tylko Właściciel*)
    8.  `Ustawienia` (Settings - początkowo zarządzanie profilem)

#### 2. Kluczowe Widoki i Moduły

*   **Dashboard (`Pages/Dashboard/Index`):**
    *   Wyświetla 3 kluczowe wskaźniki (KPI) w formie kart (`StatCard`): liczba aktywnych zleceń, liczba zleceń gotowych do odbioru, suma zarejestrowanych godzin w bieżącym dniu.
*   **Listy Danych (Klienci, Pojazdy, Zlecenia itp.):**
    *   **Widok Desktop (Właściciel/Biuro):** Reużywalny komponent tabeli (`DataTable`) z wyszukiwaniem, sortowaniem i filtrowaniem. Stan filtrów będzie zapisywany w URL. Akcje dla wiersza (Szczegóły, Edycja) będą dostępne z menu kontekstowego (`DropdownMenu`).
    *   **Statusy Zleceń:** Wizualne rozróżnienie statusów za pomocą kolorowych plakietek (`Badge`).
*   **Widok Listy Zleceń dla Mechanika:**
    *   Zamiast tabeli, zostanie użyty dedykowany, responsywny **widok oparty na kartach (`Card`)**. Każda karta będzie zawierać kluczowe informacje o zleceniu i duży przycisk "Zarejestruj czas".
*   **Rejestracja Czasu Pracy (`Pages/TimeEntry/Create`):**
    *   Interfejs w stylu "kiosku", zoptymalizowany pod kątem **mobile-first**.
    *   Duże, dotykowe elementy, w tym predefiniowane przyciski czasu (30, 60, 90 min) oraz pole do ręcznego wprowadzania wartości.
*   **Formularze CRUD (np. Dodaj Klienta):**
    *   W pełni responsywne, z jednokolumnowym układem na urządzeniach mobilnych, aby umożliwić wygodne dodawanie danych w terenie.
*   **Dodawanie Zdjęć do Zlecenia:**
    *   Komponent "przeciągnij i upuść" (drag-and-drop) z podglądem miniaturek wybranych plików.

#### 3. Wzorce, Komponenty i UX

*   **Biblioteka Komponentów:** Interfejs będzie budowany w oparciu o **shadcn/ui** i **Tailwind CSS**.
*   **Reużywalne Komponenty:** Stworzone zostaną kluczowe, reużywalne komponenty, takie jak `PageHeader`, `EmptyState`, `StatCard` oraz `DataTable`.
*   **Formularze:** Stan formularzy, walidacja i obsługa błędów będą zarządzane przez hook **`useForm` z Inertia.js**.
*   **Okna Modalne:**
    *   **`Dialog` (Modal):** Używany do szybkich, kontekstowych akcji (np. dodanie notatki, zmiana statusu zlecenia), aby nie opuszczać bieżącego widoku.
    *   **`AlertDialog` (Potwierdzenie):** Używany przed wykonaniem operacji niszczących (np. usuwanie klienta), aby wymagać od użytkownika ostatecznego potwierdzenia.
*   **Powiadomienia:** Globalne powiadomienia "toast" będą informować o sukcesie lub błędzie operacji.
*   **Stany Ładowania i Puste:**
    *   Podczas ładowania danych będą wyświetlane komponenty **"skeleton"**.
    *   Dla pustych list będą wyświetlane dedykowane widoki **"empty state"** z wezwaniem do akcji (call to action).
*   **Obsługa Uprawnień w UI:** Elementy interfejsu (np. przycisk "Usuń") będą dynamicznie renderowane na podstawie flag `can.*` dostarczanych z backendu dla każdego zasobu.
*   **Obsługa Błędów:** Stworzone zostaną dedykowane strony dla błędów **403 (Brak dostępu)** i **404 (Nie znaleziono)**.