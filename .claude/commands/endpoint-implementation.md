Twoim zadaniem jest zaimplementowanie akcji kontrolera w aplikacji Laravel + Inertia, bazując na dostarczonym planie. Twoim celem jest stworzenie solidnej i dobrze zorganizowanej implementacji, która zawiera odpowiednią walidację, obsługę błędów i podąża za wszystkimi logicznymi krokami opisanymi w planie.

Najpierw dokładnie przejrzyj dostarczony plan implementacji:

{{route-plan}}

Dokumentacja projektowa:
@.ai/prd.md 
@.ai/tech-stack.md 

**Zasady pracy:** Zrealizuj **maksymalnie 3 pierwsze kroki** z sekcji "Implementation Steps" w planie. Po ich wykonaniu, podsumuj krótko co zrobiłeś, opisz plan na 3 kolejne działania i **zatrzymaj pracę, oczekując na mój feedback.**

Teraz wykonaj następujące kroki, aby zaimplementować akcję kontrolera:

1.  **Przeanalizuj plan implementacji:**
    *   Określ metodę HTTP i URI dla trasy (route).
    *   Zidentyfikuj, która akcja w którym kontrolerze ma być zaimplementowana.
    *   Zrozum wymaganą logikę biznesową i etapy przetwarzania danych.
    *   Zwróć szczególną uwagę na wymagania dotyczące walidacji (`FormRequest`) i autoryzacji (`Policy`).

2.  **Rozpocznij implementację (pierwsze 3 kroki z planu):**
    *   Rozpocznij od zdefiniowania odpowiednich plików (np. `FormRequest`, `Policy`, metoda w kontrolerze).
    *   Zaimplementuj logikę walidacji w dedykowanej klasie `FormRequest`.
    *   Zaimplementuj logikę autoryzacji w metodzie odpowiedniej klasy `Policy`.
    *   Postępuj zgodnie z logicznymi krokami opisanymi w planie implementacji.
    *   W metodzie kontrolera użyj wstrzykiwania zależności, aby otrzymać `FormRequest` i skorzystaj z Route Model Binding.

3.  **Walidacja i obsługa błędów:**
    *   Upewnij się, że walidacja jest w pełni zaimplementowana w klasie `FormRequest`.
    *   Polegaj na wbudowanych mechanizmach Laravela do obsługi błędów (wyjątki `AuthorizationException`, `ModelNotFoundException` i automatyczne przekierowanie przy błędzie walidacji).
    *   Zapewnij, że komunikaty o błędach (jeśli są niestandardowe) są jasne i informacyjne.

4.  **Rozważania dotyczące testowania:**
    *   Podczas implementacji myśl o przypadkach brzegowych, które będą musiały zostać przetestowane (np. co się stanie, gdy dane wejściowe będą nieprawidłowe, użytkownik nie będzie miał uprawnień, lub zasób nie będzie istniał).

5. Wygnerowanie typów DTO dla typescript dzięki spatie/typescript-transformer

Uwagi:
- Nie używaj komentarzy w kodzie, chyba że trzeba wyjaśnić coś nieoczywistego.
- DTO umieszczaj w katalogu Dto/{KatalogDomenowy}/
- generując pusty komponent frontendowy na potrzeby testów używaj małych liter dla podkatalogów

Po zakończeniu implementacji pierwszych 3 kroków, przedstaw wygenerowany kod. Upewnij się, że zawiera on wszystkie niezbędne importy (`use` statements) i jest czysty, czytelny oraz dobrze zorganizowany.

Jeśli musisz przyjąć jakieś założenia lub masz pytania dotyczące planu, przedstaw je przed pisaniem kodu.