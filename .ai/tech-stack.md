# Stos Technologiczny Projektu

Data wygenerowania: 2025-10-11
Autor: @Wales86

Ten dokument opisuje wybrany stos technologiczny dla projektu aplikacji do zarządzania czasem pracy w warsztacie. Wybór został dokonany na podstawie analizy wymagań MVP, potencjalnej skalowalności oraz doświadczenia deweloperskiego.

## Podsumowanie

Wybrano architekturę "nowoczesnego monolitu" opartą o framework **Laravel** po stronie serwera oraz bibliotekę **React** po stronie klienta, połączone za pomocą **Inertia.js**. Takie podejście łączy szybkość i prostotę rozwoju typową dla aplikacji monolitycznych z bogatym i reaktywnym interfejsem użytkownika charakterystycznym dla aplikacji SPA (Single Page Application).

---

## Szczegóły Technologii

### 1. Backend: Laravel (PHP)

*   **Framework:** Laravel (najnowsza wersja LTS)
*   **Język:** PHP
*   **Kluczowe cechy:**
    *   **Szybkość rozwoju (Rapid Development):** Laravel dostarcza gotowe do użycia komponenty takie jak system autentykacji, ORM Eloquent, routing i system kolejek, co drastycznie przyspiesza prace nad funkcjonalnościami CRUD, stanowiącymi rdzeń MVP.
    *   **Bezpieczeństwo:** Wbudowane mechanizmy ochrony przed atakami XSS, CSRF i SQL Injection. Sprawdzony i regularnie aktualizowany framework z dużą społecznością dbającą o bezpieczeństwo.
    *   **Skalowalność:** Architektura Laravela jest gotowa na skalowanie horyzontalne. System kolejek pozwala na asynchroniczne przetwarzanie cięższych zadań (np. generowanie raportów) bez obciążania głównego wątku aplikacji.
    *   **Ekosystem:** Ogromna liczba dostępnych pakietów pozwala na łatwą rozbudowę systemu o nowe funkcjonalności w przyszłości.

### 2. Frontend: React (JavaScript)

*   **Biblioteka:** React
*   **Język:** JavaScript/TypeScript
*   **Kluczowe cechy:**
    *   **Bogaty interfejs użytkownika:** React pozwala na tworzenie złożonych, interaktywnych i szybkich komponentów UI, co jest kluczowe dla zapewnienia dobrego UX, zwłaszcza na urządzeniach mobilnych.
    *   **Architektura komponentowa:** Ułatwia zarządzanie kodem i jego ponowne wykorzystanie w miarę rozrastania się aplikacji.
    *   **Doświadczenie dewelopera:** Wybór podyktowany wieloletnim doświadczeniem i znajomością biblioteki, co minimalizuje czas potrzebny na wdrożenie.

### 3. "Klej": Inertia.js

*   **Technologia:** Inertia.js
*   **Rola:** Łącznik między backendem a frontendem.
*   **Kluczowe cechy:**
    *   **Eliminacja potrzeby budowy API:** Inertia pozwala kontrolerom Laravela zwracać bezpośrednio komponenty React wraz z danymi, eliminując konieczność tworzenia i utrzymywania osobnego API REST/GraphQL dla frontendu.
    *   **Prostota "monolitu", moc SPA:** Zachowujemy prosty cykl "request-response" znany z tradycyjnych aplikacji webowych, jednocześnie oferując użytkownikowi płynne przejścia i reaktywność typową dla SPA.
    *   **Redukcja złożoności:** Mniej "ruchomych części" w architekturze (brak osobnego zarządzania stanem API, podwójnego routingu itp.) sprawia, że projekt jest łatwiejszy w utrzymaniu przez jednego dewelopera.

### 4. Baza Danych

*   **System:** MySQL lub PostgreSQL (do wyboru na etapie konfiguracji środowiska).
*   **Uzasadnienie:** Standardowe, wydajne i niezawodne systemy relacyjnych baz danych, w pełni wspierane przez Laravela.

### 5. Infrastruktura i Deployment

*   **Platforma:** Laravel Cloud
*   **Uzasadnienie:** Wybór platformy typu PaaS (Platform as a Service) zoptymalizowanej pod Laravela ma na celu zminimalizowanie czasu poświęconego na zadania DevOps. Laravel Cloud automatyzuje procesy deploymentu, zarządzania serwerem, bazą danych, kolejkami i certyfikatami SSL, co pozwala skupić się w 100% na pisaniu kodu aplikacji.