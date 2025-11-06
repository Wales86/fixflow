# Dokument Wymagań Produktu (PRD) - FixFlow

## 1. Przegląd Produktu

FixFlow to narzędzie do zarządzania warsztatem, zaprojektowane w celu precyzyjnego śledzenia czasu pracy poświęconego na zlecenia naprawy. Głównym celem aplikacji jest dostarczanie danych niezbędnych do analizy rentowności napraw i efektywności zespołu, eliminując tym samym potrzebę prowadzenia dokumentacji w formie papierowej. System ten będzie służył jako jedyne źródło prawdy dla wszystkich działań związanych z ewidencją czasu pracy w warsztacie, umożliwiając menedżerom podejmowanie decyzji opartych na danych w celu optymalizacji operacji. Wersja Minimum Viable Product (MVP) skupia się na podstawowych funkcjonalnościach zarządzania klientami i pojazdami, przetwarzania zleceń, rejestracji czasu pracy oraz raportowania, z uwzględnieniem architektury multi-tenancy, pozwalającej na obsługę wielu niezależnych warsztatów w ramach jednej aplikacji.

## 2. Problem Użytkownika

Obecnie warsztat opiera się na ręcznym, papierowym systemie śledzenia czasu, jaki mechanicy poświęcają na każde zlecenie naprawy. Proces ten jest nieefektywny, podatny na błędy i utrudnia gromadzenie użytecznych danych. Kluczowe problemy to:

*   _Niedokładna analiza rentowności_: Bez precyzyjnego śledzenia czasu trudno jest określić rzeczywisty koszt pracy przy każdym zleceniu, co utrudnia ocenę prawdziwej rentowności usług.
*   _Brak wglądu w wydajność_: Kierownictwo nie ma jasnego, obiektywnego sposobu na mierzenie efektywności poszczególnych mechaników ani całego zespołu.
*   _Słaba dostępność danych_: Odzyskiwanie historii napraw dla konkretnego pojazdu lub klienta jest uciążliwym i czasochłonnym procesem ręcznego wyszukiwania.

FixFlow ma na celu rozwiązanie tych problemów poprzez cyfryzację i automatyzację procesu śledzenia czasu, zapewniając natychmiastowy dostęp do dokładnych, zagregowanych danych.

## 3. Wymagania Funkcjonalne

Wersja MVP aplikacji FixFlow będzie zawierać następujące moduły i funkcje:

### 3.0. Zarządzanie Warsztatem (Multi-Tenancy)
*   _F-000_: System musi wspierać wiele warsztatów (tenantów), zapewniając pełną izolację danych (klienci, pojazdy, zlecenia, użytkownicy) między nimi.
*   _F-000.1_: Każdy warsztat posiada własną, odizolowaną konfigurację, w tym unikalną listę mechaników i ustawienia.
*   _F-000.2_: Dostęp do danych jest ściśle ograniczony do warsztatu, do którego zalogowany jest użytkownik.

### 3.1. Baza Danych Klientów i Pojazdów
*   _F-001_: Możliwość tworzenia, przeglądania, aktualizowania i usuwania profili klientów w cyfrowej bazie danych danego warsztatu.
*   _F-002_: Możliwość dodawania wielu pojazdów do jednego profilu klienta, w tym szczegółów takich jak Marka, Model, Rocznik, VIN i Numer Rejestracyjny.
*   _F-003_: Dostęp do pełnej historii napraw i serwisów dla każdego zarejestrowanego pojazdu.

### 3.2. Zarządzanie Zleceniami
*   _F-004_: Tworzenie nowych zleceń naprawy i powiązywanie ich z konkretnym klientem i pojazdem.
*   _F-005_: Dodawanie szczegółowego opisu usterki, notatek wewnętrznych dla mechaników oraz dołączanie plików graficznych (np. zdjęć uszkodzeń) do każdego zlecenia.
*   _F-006_: Zarządzanie i aktualizowanie statusu każdego zlecenia. Dostępne statusy to: *Nowe, Diagnoza, Wymaga kontaktu, Czeka na części, W naprawie, Gotowe do odbioru, Zamknięte*.

### 3.3. Rejestracja Czasu Pracy
*   _F-007_: System musi być dostępny zarówno z komputerów stacjonarnych w warsztacie, jak i z urządzeń mobilnych (za pośrednictwem responsywnego interfejsu webowego).
*   _F-008_: Mechanicy mogą wybrać swoje imię z listy pracowników przypisanych do ich warsztatu i aktywne zlecenie, aby zarejestrować czas pracy.
*   _F-009_: Dla każdego wpisu czasu, mechanik musi podać czas spędzony na zadaniu oraz krótki opis wykonanej czynności. Opis jest opcjonalny.
*   _F-010_: Aplikacja musi automatycznie agregować wszystkie wpisy czasu dla jednego zlecenia, aby pokazać całkowity czas pracy.

### 3.4. Raportowanie
*   _F-011_: Generowanie raportu wydajności dla wybranego pracownika w określonym okresie (dzień, tydzień, miesiąc), zawierającego listę wszystkich wykonanych zadań i czasu na nie poświęconego.
*   _F-012_: Generowanie raportu wydajności dla całego zespołu, umożliwiającego porównanie zarejestrowanego czasu pracy poszczególnych mechaników w wybranym okresie.

### 3.5. Zarządzanie Użytkownikami i Role
*   _F-013_: System musi wspierać trzy role użytkowników w ramach każdego warsztatu: Właściciel, Biuro, Mechanik, z predefiniowanymi uprawnieniami.

### 3.6. Historia Zmian (Audit Log)
*   _F-014_: System musi rejestrować kluczowe zmiany, takie jak edycje wpisów czasu i zmiany statusów zleceń, w celu zapewnienia przejrzystości i możliwości audytu.

## 4. Granice Produktu

### W Zakresie MVP
*   Wszystkie funkcje wymienione w sekcji Wymagania Funkcjonalne (3).
*   Podstawowa architektura multi-tenancy zapewniająca izolację danych między warsztatami.
*   Uwierzytelnianie użytkowników i kontrola dostępu oparta na rolach (Właściciel, Biuro, Mechanik).
*   Responsywny interfejs webowy, który działa na standardowych przeglądarkach desktopowych i mobilnych.

### Poza Zakresem MVP
*   Moduły finansowe, w tym kosztorysowanie, fakturowanie i rozliczenia.
*   Zarządzanie magazynem i częściami zamiennymi.
*   Portale lub powiadomienia skierowane bezpośrednio do klientów.
*   Harmonogramowanie wizyt.
*   Integracja z zewnętrznymi systemami księgowymi lub systemami zarządzania salonem dealerskim.

Te funkcje mogą być rozważone w przyszłych wersjach produktu na podstawie opinii użytkowników i priorytetów biznesowych.

## 5. Historyjki Użytkowników

### Uwierzytelnianie i Dostęp
*   _ID_: US-001
*   _Tytuł_: Logowanie użytkownika
*   _Opis_: Jako zarejestrowany użytkownik (Właściciel lub pracownik Biura), chcę mieć możliwość bezpiecznego zalogowania się do aplikacji, aby uzyskać dostęp do jej funkcji zgodnie z moją rolą w danym warsztatem. Mechanicy będą korzystać z jednego wspólnego konta bez konieczności logowania - wybierają swoje imię z listy przypisanej do ich warsztatu przy rejestracji czasu.
*   _Kryteria akceptacji_:
    *   Będąc na stronie logowania, widzę pola na nazwę użytkownika i hasło.
    *   Gdy wpiszę poprawne dane uwierzytelniające i prześlę formularz, zostaję przekierowany do głównego panelu mojego warsztatu.
    *   Gdy wpiszę nieprawidłowe dane, widzę komunikat o błędzie i pozostaję na stronie logowania.
    *   Gdy jestem zalogowany, widzę opcję wylogowania.

### Zarządzanie Klientami
*   _ID_: US-002
*   _Tytuł_: Tworzenie nowego klienta
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę dodać nowego klienta do systemu mojego warsztatu, podając jego dane (w tym imię, nazwisko, telefon, email, adres), abym mógł tworzyć dla niego zlecenia naprawy.
*   _Kryteria akceptacji_:
    *   Będąc zalogowanym jako Właściciel lub pracownik Biura, mogę przejść do formularza "Utwórz klienta".
    *   Gdy wypełnię wymagane pola (np. Imię, Numer telefonu) i zapiszę, nowy klient zostanie dodany do bazy danych.
    *   Gdy próbuję zapisać bez wypełnienia wymaganych pól, widzę komunikat o błędzie walidacji.

*   _ID_: US-003
*   _Tytuł_: Przeglądanie listy klientów
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę przeglądać listę wszystkich klientów mojego warsztatu, abym mógł szybko znaleźć i zarządzać ich informacjami.
*   _Kryteria akceptacji_:
    *   Będąc zalogowanym jako Właściciel lub pracownik Biura, mam dostęp do strony, która wyświetla przeszukiwalną i sortowalną listę wszystkich klientów mojego warsztatu.
    *   Każdy wpis na liście wyświetla kluczowe informacje, takie jak imię i dane kontaktowe.
    *   Kliknięcie na klienta na liście przenosi mnie do strony jego szczegółowego profilu.

*   _ID_: US-004
*   _Tytuł_: Edycja danych klienta
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę edytować dane istniejącego klienta, aby jego informacje były zawsze aktualne.
*   _Kryteria akceptacji_:
    *   Przeglądając profil klienta, widzę opcję edycji jego informacji.
    *   Gdy zmienię dane i zapiszę, rekord klienta w bazie danych zostanie zaktualizowany.

### Zarządzanie Pojazdami
*   _ID_: US-005
*   _Tytuł_: Dodawanie pojazdu do klienta
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę dodać nowy pojazd do profilu klienta, w tym jego Markę, Model, rocznik, VIN i numer rejestracyjny, abym mógł śledzić jego historię serwisową w ramach mojego warsztatu.
*   _Kryteria akceptacji_:
    *   Będąc na stronie profilu klienta, mam opcję "Dodaj pojazd".
    *   Gdy wypełnię dane pojazdu i zapiszę, pojazd zostanie powiązany z tym klientem.
    *   System powinien uniemożliwić dodanie pojazdu z numerem VIN, który już istnieje w bazie danych danego warsztatu.

*   _ID_: US-006
*   _Tytuł_: Przeglądanie historii napraw pojazdu
*   _Opis_: Jako użytkownik, chcę przeglądać pełną historię napraw dla konkretnego pojazdu, abym mógł zrozumieć jego przeszłe problemy i usługi.
*   _Kryteria akceptacji_:
    *   Przeglądając szczegóły pojazdu, widzę chronologiczną listę wszystkich przeszłych i obecnych zleceń naprawy z nim związanych.
    *   Każdy wpis w historii prowadzi do strony ze szczegółami odpowiedniego zlecenia.

*   _ID_: US-006.1
*   _Tytuł_: Wyszukiwanie pojazdu
*   _Opis_: Jako użytkownik, chcę mieć możliwość szybkiego wyszukania pojazdu w moim warsztacie po numerze rejestracyjnym, VIN, marce lub modelu, aby sprawnie odnaleźć jego dane i historię.
*   _Kryteria akceptacji_:
    *   W systemie istnieje pole wyszukiwania, które pozwala na wpisanie numeru rejestracyjnego, VIN, marki lub modelu.
    *   Po wprowadzeniu zapytania i zatwierdzeniu, system wyświetla listę pasujących pojazdów.
    *   Kliknięcie na pojazd z listy przenosi mnie do jego szczegółowego profilu.

### Zarządzanie Zleceniami
*   _ID_: US-007
*   _Tytuł_: Tworzenie nowego zlecenia naprawy
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę utworzyć nowe zlecenie naprawy dla konkretnego pojazdu, aby praca mogła być śledzona.
*   _Kryteria akceptacji_:
    *   Będąc na stronie szczegółów pojazdu, mogę zainicjować tworzenie nowego zlecenia.
    *   Formularz tworzenia zlecenia pozwala mi dodać opis usterki, notatki wewnętrzne i załączyć zdjęcia.
    *   Po utworzeniu, zlecenie otrzymuje unikalny identyfikator, a jego status jest ustawiany na "Nowe".

*   _ID_: US-008 
*   _Tytuł_: Przeglądanie wszystkich zleceń
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę widzieć listę wszystkich zleceń naprawy w moim warsztacie
*   _Kryteria akceptacji_:
    *   Będąc zalogowanym, mogę przejść do panelu, który pokazuje listę wszystkich zleceń w moim warsztacie
    *   Lista jest przeszukiwalna i może być filtrowana według statusu.
    *   Każdy element listy pokazuje kluczowe informacje, takie jak ID zlecenia, Imię klienta, Pojazd i obecny Status.

*   _ID_: US-009
*   _Tytuł_: Aktualizacja statusu zlecenia
*   _Opis_: Jako Właściciel lub pracownik Biura, chcę zmieniać status zlecenia naprawy (*Nowe, Diagnoza, Wymaga kontaktu, Czeka na części, W naprawie, Gotowe do odbioru, Zamknięte*), aby odzwierciedlał jego obecny etap w przepływie pracy.
*   _Kryteria akceptacji_:
    *   Przeglądając szczegóły zlecenia, mogę zmienić jego status z predefiniowanej listy opcji.
    *   Gdy status zostanie zmieniony, aktualizacja jest natychmiast widoczna w szczegółach zlecenia i na głównej liście zleceń.

### Ewidencja Czasu Pracy
*   _ID_: US-010
*   _Tytuł_: Rejestrowanie czasu pracy
*   _Opis_: Jako Mechanik, chcę rejestrować czas, który spędzam na konkretnym zadaniu w ramach zlecenia naprawy, aby moja praca była rozliczona.
*   _Kryteria akceptacji_:
    *   Będąc na ekranie rejestracji czasu, mogę wybrać aktywne zlecenie naprawy z listy zleceń mojego warsztatu.
    *   Następnie mogę wybrać swoje imię z listy pracowników mojego warsztatu.
    *   Mogę wprowadzić czas spędzony (np. w godzinach i minutach) oraz opcjonalny opis wykonanej pracy.
    *   Gdy zapiszę wpis, zostanie on dodany do dziennika pracy zlecenia.
    *   Mogę edytować swój ostatni wpis czasu w razie pomyłki.

*   _ID_: US-011
*   _Tytuł_: Przeglądanie zagregowanego czasu w zleceniu
*   _Opis_: Jako użytkownik, chcę widzieć całkowity czas spędzony na zleceniu naprawy, zagregowany ze wszystkich indywidualnych wpisów mechaników, abym mógł zrozumieć całkowity wkład pracy.
*   _Kryteria akceptacji_:
    *   Przeglądając szczegóły zlecenia naprawy, widzę bieżącą sumę całego zarejestrowanego czasu.
    *   Mogę również zobaczyć podział wszystkich indywidualnych wpisów czasu, pokazujący, który mechanik zarejestrował czas, ile czasu spędził i jakie zadanie wykonał.

### Raportowanie
*   _ID_: US-012
*   _Tytuł_: Generowanie raportu wydajności pracownika
*   _Opis_: Jako Właściciel, chcę wygenerować raport dla konkretnego mechanika w moim warsztacie w danym zakresie dat (dzień, tydzień, miesiąc), aby zobaczyć wszystkie zadania, nad którymi pracował, i całkowity czas, który zarejestrował.
*   _Kryteria akceptacji_:
    *   Będąc na stronie raportowania, mogę wybrać pracownika z mojego warsztatu i zakres dat.
    *   Gdy generuję raport, wyświetla on całkowitą liczbę godzin przepracowanych przez pracownika w tym okresie.
    *   Raport zawiera również szczegółową listę każdego wpisu czasu zarejestrowanego przez tego pracownika, pogrupowaną według zlecenia naprawy.

*   _ID_: US-013
*   _Tytuł_: Generowanie raportu wydajności zespołu
*   _Opis_: Jako Właściciel, chcę wygenerować raport dla całego zespołu mojego warsztatu w danym zakresie dat, aby porównać ich czasy pracy i zobaczyć ogólną produktywność.
*   _Kryteria akceptacji_:
    *   Będąc na stronie raportowania, mogę wybrać opcję raportu dla całego zespołu i wybrać zakres dat.
    *   Raport wyświetla tabelę z listą mechaników mojego warsztatu i sumą ich zarejestrowanego czasu w danym okresie.
    *   Mogę łatwo porównać wyniki poszczególnych pracowników.

*   _ID_: US-014
*   _Tytuł_: Przeglądanie historii zmian
*   _Opis_: Jako Właściciel, chcę widzieć podstawową informację o tym, kto i kiedy edytował zlecenie, aby mieć ogólną świadomość zmian.
*   _Kryteria akceptacji_:
    *   Przeglądając szczegóły zlecenia, widzę informację o tym, kto i kiedy dokonał ostatniej edycji.
    *   Szczegółowy log zmian z poprzednimi i nowymi wartościami nie jest wymagany w wersji MVP.

## 6. Metryki Sukcesu

Sukces wersji MVP FixFlow będzie mierzony za pomocą następujących kluczowych wskaźników:

*   _Czas na zarejestrowanie zadania_: Średni czas, jaki zajmuje mechanikowi wybranie zlecenia i przesłanie wpisu czasu. Cel: Mniej niż 60 sekund.
*   _Satysfakcja użytkownika_: Jakościowe opinie zbierane od mechaników i menedżerów dotyczące łatwości obsługi i użyteczności aplikacji. Mierzone poprzez dwutygodniowe spotkania kontrolne przez pierwsze dwa miesiące.

## 7. Stos Technologiczny
*   **Backend:** Laravel (framework PHP)
*   **Frontend:** React + Inertia
*   **Baza danych:** MySql
