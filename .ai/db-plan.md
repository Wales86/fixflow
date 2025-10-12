# Plan Schematu Bazy Danych - FixFlow

## 1. Lista Tabel

### `users`
Przechowuje dane użytkowników z rolami `Właściciel` i `Biuro`. Uwierzytelnianie i role będą zarządzane przez pakiety Laravel.

| Nazwa Kolumny       | Typ Danych         | Ograniczenia                                     | Opis                               |
| ------------------- | ------------------ | ------------------------------------------------ | ---------------------------------- |
| `id`                | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator użytkownika |
| `workshop_id`       | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (workshops.id)`         | Klucz obcy warsztatu (tenanta)     |
| `name`              | `VARCHAR(255)`     | `NOT NULL`                                       | Imię i nazwisko użytkownika        |
| `email`             | `VARCHAR(255)`     | `NOT NULL`, `UNIQUE(workshop_id, email)`         | Adres email (unikalny w warsztacie)|
| `email_verified_at` | `TIMESTAMP`        | `NULLABLE`                                       | Data weryfikacji adresu email      |
| `password`          | `VARCHAR(255)`     | `NOT NULL`                                       | Hasło (zahashowane)                |
| `remember_token`    | `VARCHAR(100)`     | `NULLABLE`                                       | Token "zapamiętaj mnie"            |
| `created_at`        | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu            |
| `updated_at`        | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji        |
| `deleted_at`        | `TIMESTAMP`        | `NULLABLE`                                       | Data "miękkiego" usunięcia         |

### `mechanics`
Przechowuje listę mechaników przypisanych do konkretnego warsztatu.

| Nazwa Kolumny | Typ Danych         | Ograniczenia                                     | Opis                             |
| ------------- | ------------------ | ------------------------------------------------ | -------------------------------- |
| `id`          | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator mechanika |
| `workshop_id` | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (workshops.id)`         | Klucz obcy warsztatu (tenanta)   |
| `first_name`  | `VARCHAR(255)`     | `NOT NULL`                                       | Imię mechanika                   |
| `last_name`   | `VARCHAR(255)`     | `NOT NULL`                                       | Nazwisko mechanika               |
| `is_active`   | `BOOLEAN`          | `NOT NULL`, `DEFAULT TRUE`                       | Czy mechanik jest aktywny        |
| `created_at`  | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu          |
| `updated_at`  | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji      |
| `deleted_at`  | `TIMESTAMP`        | `NULLABLE`                                       | Data "miękkiego" usunięcia       |

### `clients`
Przechowuje dane klientów warsztatu.

| Nazwa Kolumny         | Typ Danych         | Ograniczenia                                     | Opis                             |
| --------------------- | ------------------ | ------------------------------------------------ | -------------------------------- |
| `id`                  | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator klienta   |
| `workshop_id`         | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (workshops.id)`         | Klucz obcy warsztatu (tenanta)   |
| `first_name`          | `VARCHAR(255)`     | `NOT NULL`                                       | Imię klienta                     |
| `last_name`           | `VARCHAR(255)`     | `NULLABLE`                                       | Nazwisko klienta                 |
| `phone_number`        | `VARCHAR(50)`      | `NOT NULL`                                       | Numer telefonu                   |
| `email`               | `VARCHAR(255)`     | `NULLABLE`                                       | Adres email klienta              |
| `address_street`      | `VARCHAR(255)`     | `NULLABLE`                                       | Ulica i numer                    |
| `address_city`        | `VARCHAR(255)`     | `NULLABLE`                                       | Miasto                           |
| `address_postal_code` | `VARCHAR(20)`      | `NULLABLE`                                       | Kod pocztowy                     |
| `address_country`     | `VARCHAR(100)`     | `NULLABLE`                                       | Kraj                             |
| `created_at`          | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu          |
| `updated_at`          | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji      |
| `deleted_at`          | `TIMESTAMP`        | `NULLABLE`                                       | Data "miękkiego" usunięcia       |

### `vehicles`
Przechowuje dane pojazdów powiązanych z klientami.

| Nazwa Kolumny   | Typ Danych         | Ograniczenia                                     | Opis                                        |
| --------------- | ------------------ | ------------------------------------------------ | ------------------------------------------- |
| `id`            | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator pojazdu              |
| `workshop_id`   | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (workshops.id)`         | Klucz obcy warsztatu (dla unikalności VIN)  |
| `client_id`     | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (clients.id)`           | Klucz obcy klienta, do którego należy pojazd|
| `make`          | `VARCHAR(255)`     | `NOT NULL`                                       | Marka pojazdu                               |
| `model`         | `VARCHAR(255)`     | `NOT NULL`                                       | Model pojazdu                               |
| `year`          | `SMALLINT UNSIGNED`| `NOT NULL`                                       | Rocznik produkcji                           |
| `vin`                  | `VARCHAR(17)`      | `NOT NULL`, `UNIQUE(workshop_id, vin)`           | Numer VIN (unikalny w obrębie warsztatu)    |
| `registration_number`  | `VARCHAR(20)`      | `NOT NULL`                                       | Numer rejestracyjny                         |
| `created_at`    | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu                     |
| `updated_at`    | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji                 |
| `deleted_at`    | `TIMESTAMP`        | `NULLABLE`                                       | Data "miękkiego" usunięcia                  |

### `repair_orders`
Główna tabela przechowująca zlecenia naprawy.

| Nazwa Kolumny         | Typ Danych         | Ograniczenia                                     | Opis                             |
| --------------------- | ------------------ | ------------------------------------------------ | -------------------------------- |
| `id`                  | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator zlecenia  |
| `workshop_id`         | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (workshops.id)`         | Klucz obcy warsztatu (tenanta)   |
| `vehicle_id`          | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (vehicles.id)`          | Klucz obcy pojazdu               |
| `status`              | `VARCHAR(50)`      | `NOT NULL`, `DEFAULT 'Nowe'`                     | Status zlecenia (np. Nowe, W naprawie) |
| `problem_description` | `TEXT`             | `NOT NULL`                                       | Opis usterki zgłoszonej przez klienta |
| `started_at`          | `TIMESTAMP`        | `NULLABLE`                                       | Data rozpoczęcia naprawy           |
| `finished_at`         | `TIMESTAMP`        | `NULLABLE`                                       | Data zakończenia naprawy           |
| `created_at`          | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu          |
| `updated_at`          | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji      |
| `deleted_at`          | `TIMESTAMP`        | `NULLABLE`                                       | Data "miękkiego" usunięcia       |

### `time_entries`
Przechowuje wpisy czasu pracy mechaników dla konkretnych zleceń.

| Nazwa Kolumny     | Typ Danych         | Ograniczenia                                     | Opis                               |
| ----------------- | ------------------ | ------------------------------------------------ | ---------------------------------- |
| `id`              | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator wpisu       |
| `repair_order_id` | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (repair_orders.id)`     | Klucz obcy zlecenia naprawy        |
| `mechanic_id`     | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (mechanics.id)`         | Klucz obcy mechanika               |
| `duration_minutes`| `INT UNSIGNED`     | `NOT NULL`                                       | Czas pracy w minutach              |
| `description`     | `TEXT`             | `NULLABLE`                                       | Opis wykonanych czynności          |
| `created_at`      | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu            |
| `updated_at`      | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji        |

### `internal_notes`
Notatki wewnętrzne do zleceń, tworzone przez użytkowników lub mechaników.

| Nazwa Kolumny     | Typ Danych         | Ograniczenia                                     | Opis                                   |
| ----------------- | ------------------ | ------------------------------------------------ | -------------------------------------- |
| `id`              | `BIGINT UNSIGNED`  | `PRIMARY KEY`, `AUTO_INCREMENT`                  | Unikalny identyfikator notatki         |
| `repair_order_id` | `BIGINT UNSIGNED`  | `NOT NULL`, `FOREIGN KEY (repair_orders.id)`     | Klucz obcy zlecenia naprawy            |
| `content`         | `TEXT`             | `NOT NULL`                                       | Treść notatki                          |
| `author_id`       | `BIGINT UNSIGNED`  | `NOT NULL`                                       | ID autora (polimorficzne)              |
| `author_type`     | `VARCHAR(255)`     | `NOT NULL`                                       | Typ autora (np. 'App\Models\User')     |
| `created_at`      | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP`          | Data utworzenia rekordu                |
| `updated_at`      | `TIMESTAMP`        | `NOT NULL`, `DEFAULT CURRENT_TIMESTAMP ON UPDATE`| Data ostatniej aktualizacji            |

---

## 2. Relacje Między Tabelami

-   **Warsztat (Tenant) -> Reszta**: Tabela `workshops` (zarządzana przez pakiet `spatie/laravel-multitenancy` jako `tenants`) ma relację **jeden-do-wielu** z:
    -   `users`
    -   `mechanics`
    -   `clients`
    -   `vehicles`
    -   `repair_orders`
-   **Client -> Vehicle**: `clients` ma relację **jeden-do-wielu** z `vehicles`. Jeden klient może mieć wiele pojazdów.
-   **Vehicle -> Repair Order**: `vehicles` ma relację **jeden-do-wielu** z `repair_orders`. Jeden pojazd może mieć wiele zleceń naprawy.
-   **Repair Order -> Time Entry / Internal Note**: `repair_orders` ma relację **jeden-do-wielu** z:
    -   `time_entries`
    -   `internal_notes`
-   **Mechanic -> Time Entry**: `mechanics` ma relację **jeden-do-wielu** z `time_entries`. Jeden mechanik może mieć wiele wpisów czasu.
-   **Author -> Internal Note (Polimorficzna)**: `users` i `mechanics` mają polimorficzną relację **jeden-do-wielu** z `internal_notes`. Notatka może być stworzona przez użytkownika systemu lub mechanika.
-   **Relacje z pakietów zewnętrznych**:
    -   `repair_orders` będzie miał polimorficzną relację z tabelą `media` (z `spatie/laravel-medialibrary`) do obsługi załączników.
    -   Różne modele (np. `repair_orders`, `time_entries`) będą miały polimorficzną relację z tabelą `activity_log` (z `spatie/laravel-activitylog`) do śledzenia historii zmian.
    -   Model `User` będzie powiązany z tabelami `roles` i `permissions` (z `spatie/laravel-permission`).

---

## 3. Indeksy

W celu optymalizacji wydajności zapytań, następujące kolumny powinny zostać zindeksowane:

-   **Klucze obce**: Wszystkie kolumny będące kluczami obcymi (np. `users.workshop_id`, `vehicles.client_id`, etc.).
-   **`users`**:
    -   `users_workshop_id_email_unique` (`workshop_id`, `email`)
-   **`clients`**:
    -   `clients_phone_number_index` (`phone_number`)
-   **`vehicles`**:
    -   `vehicles_workshop_id_vin_unique` (`workshop_id`, `vin`)
    -   `vehicles_registration_number_index` (`registration_number`)
-   **`repair_orders`**:
    -   `repair_orders_status_index` (`status`)
-   **`internal_notes`**:
    -   `internal_notes_author_index` (`author_id`, `author_type`)

---

## 4. Zasady MySQL (Row-Level Security)

Zabezpieczenia na poziomie wiersza (RLS) nie będą implementowane bezpośrednio w MySQL. Izolacja danych między warsztatami (tenantami) zostanie w całości zrealizowana na poziomie aplikacji przez pakiet **`spatie/laravel-multitenancy`**. Pakiet ten automatycznie dodaje warunek `WHERE workshop_id = ?` do wszystkich zapytań Eloquent, co zapewnia solidną i bezpieczną separację danych bez konieczności definiowania skomplikowanych polityk RLS w bazie danych.

---

## 5. Dodatkowe Uwagi

-   **Miękkie Usuwanie (Soft Deletes)**: Kluczowe encje (`users`, `mechanics`, `clients`, `vehicles`, `repair_orders`) posiadają kolumnę `deleted_at`. Umożliwia to "miękkie usuwanie" rekordów, co jest kluczowe dla zachowania integralności historycznych danych i możliwości ich ewentualnego przywrócenia.
-   **Statusy Zleceń**: Kolumna `status` w tabeli `repair_orders` będzie przechowywać wartości tekstowe. Zarządzanie dozwolonymi statusami będzie realizowane w aplikacji za pomocą natywnych `Enumów` PHP, co zapewnia czytelność w bazie danych i bezpieczeństwo typów w kodzie.
-   **Przechowywanie Czasu Pracy**: Czas pracy w `time_entries` jest przechowywany jako liczba całkowita (`duration_minutes`), co eliminuje problemy z precyzją i ułatwia agregację oraz obliczenia.
-   **Pakiety Zewnętrzne**: Schemat celowo nie definiuje tabel dla ról, mediów, logów aktywności i tenantów, ponieważ zostaną one utworzone i zarządzane przez dedykowane, sprawdzone pakiety Spatie, zgodnie z decyzjami projektowymi.
