Jesteś doświadczonym architektem oprogramowania, którego zadaniem jest stworzenie szczegółowego planu implementacji dla akcji kontrolera w aplikacji Laravel + Inertia. Twój plan poprowadzi zespół programistów w skutecznym i poprawnym wdrożeniu tej funkcjonalności.

Zanim zaczniemy, zapoznaj się z poniższymi informacjami:

1.  **Specyfikacja trasy (Route) i akcji kontrolera:**
    {{route-spec}}

2.  **Stack technologiczny:**
    @tech-stack.md

3. Dokumentacja projektowa:
@prd.md

Twoim zadaniem jest stworzenie kompleksowego planu implementacji dla tej konkretnej akcji. Przed dostarczeniem ostatecznego planu użyj znaczników `<analysis>`, aby przeanalizować informacje i nakreślić swoje podejście. W tej analizie upewnij się, że:

1.  Podsumujesz kluczowe punkty specyfikacji akcji (jaki kontroler, jaka metoda, co ma robić).
2.  Wymienisz wymagane i opcjonalne parametry przyjmowane przez akcję (z trasy lub z requestu).
3.  Zidentyfikujesz potrzebne klasy `FormRequest` (do walidacji) lub DTO (jeśli logika jest złożona).
4.  Zastanowisz się, jak wyodrębnić logikę biznesową do odpowiedniej klasy usługi (Service Class) - istniejącej lub nowej.
5.  Zaplanujesz walidację danych wejściowych zgodnie ze specyfikacją, zasobami bazy danych i zasadami implementacji.
6.  Określisz, jakie dane (props) zostaną przekazane do komponentu React.
7.  Zidentyfikujesz potencjalne zagrożenia bezpieczeństwa (np. brak autoryzacji) i jak im zapobiec za pomocą `Policies` lub `Gates`.
8.  Nakreślisz potencjalne scenariusze błędów i sposób ich obsługi (np. błąd walidacji, brak zasobu, błąd serwera).

Po przeprowadzeniu analizy utwórz szczegółowy plan implementacji w formacie markdown. Plan powinien zawierać następujące sekcje:

1.  Przegląd akcji kontrolera
2.  Szczegóły żądania (Request)
3.  Szczegóły odpowiedzi (Response)
4.  Przepływ danych
5.  Kwestie bezpieczeństwa (Autoryzacja)
6.  Obsługa błędów
7.  Wydajność
8.  Kroki implementacji


W całym planie upewnij się, że:
- Używasz prawidłowej logiki odpowiedzi dla Inertia:
  - `Inertia::render()` dla żądań `GET`.
  - `Redirect` z komunikatami sesji dla żądań `POST`/`PUT`/`DELETE`.
  - Zgłaszasz odpowiednie wyjątki Laravela dla błędów (np. `AuthorizationException`, `ModelNotFoundException`).
- Dostosowujesz się do dostarczonego stacku technologicznego.
- Postępujesz zgodnie z podanymi zasadami implementacji z plików `.mdc`.

Końcowym wynikiem powinien być dobrze zorganizowany plan wdrożenia w formacie markdown. Oto przykład struktury:

````markdown
# Controller Action Implementation Plan: [NazwaAkcjiKontrolera]

## 1. Endpoint Overview
[Krótki opis celu i funkcjonalności akcji, np. "Wyświetla formularz edycji dla danego posta" lub "Przetwarza dane z formularza tworzenia nowego użytkownika".]

## 2. Request Details
- **Route:** [np. `GET /posts/{post}/edit`]
- **Controller Action:** [np. `PostController@edit`]
- **Route Parameters:**
  - Required: [np. `{post}` - ID posta]
- **Request Body / Form Data:** [Struktura danych wysyłanych z formularza React, jeśli dotyczy]
- **Validation:** [Nazwa dedykowanej klasy FormRequest, np. `StorePostRequest`]

## 3. Used Types
[Wymień klasy FormRequest, DTO lub inne struktury danych niezbędne do implementacji.]

## 4. Response Details
- **Success Response:** [Opis odpowiedzi w przypadku sukcesu. Np. "Zwraca odpowiedź `Inertia::render('Posts/Edit', [...])` z danymi posta" lub "Przekierowuje na stronę `posts.show` z komunikatem sukcesu w sesji."]
- **Component Props:** [Struktura danych (props) przekazywanych do komponentu React.]
- **Error Response:** [Opis odpowiedzi w przypadku błędu walidacji. Np. "Automatyczne przekierowanie przez Laravela z błędami walidacji."]

## 5. Data Flow
[Opis krok po kroku, co dzieje się po trafieniu żądania do akcji. Np. "1. Autoryzacja przez `PostPolicy`. 2. Walidacja przez `UpdatePostRequest`. 3. Wywołanie `PostService->update()`. 4. Przekierowanie..."]

## 6. Security Considerations
- **Authentication:** [Wymagane, np. "Użytkownik musi być zalogowany."]
- **Authorization:** [Szczegóły autoryzacji, np. "Sprawdzenie za pomocą `PostPolicy::update` czy użytkownik może edytować ten post."]

## 7. Error Handling
- **Not Found:** [Jak obsłużony jest brak zasobu, np. "Route Model Binding automatycznie zwróci 404, jeśli post nie zostanie znaleziony."]
- **Validation Failed:** [Jak obsłużony jest błąd walidacji, np. "Automatyczna obsługa przez Laravela i Inertia."]
- **Authorization Failed:** [Jak obsłużony jest błąd autoryzacji, np. "Laravel zgłosi `AuthorizationException` i zwróci 403."]
- **Server Error:** [Ogólna obsługa błędów 500.]

## 8. Performance Considerations
[Potencjalne wąskie gardła i strategie optymalizacji, np. "Użycie Eager Loading (`->with('comments')`) w celu uniknięcia problemu N+1."]

## 9. Implementation Steps
1. Create `UpdatePostRequest` with validation rules.
2. Create `PostPolicy` and implement the `update` method.
3. Add the `update` method to `PostController`.
4. Implement the business logic, potentially calling a `PostService`.
5. Ensure the correct redirect response is returned on success.
6. Genrate typescript types for the frontend with spatie/typescript-transformer
...
````

Uwagi:
- podkatalogi i nazwy komponentów frontendu są zawsze z małych liter, np nie Inertia::render('Pages/Clients/Create') tylko Inertia::render('pages/clients/create'). Nie Create.tsx tylko create.tsx
- dto umieszczamy w katalogu Dto/{katalog_domenowy}/

Końcowe wyniki powinny składać się wyłącznie z planu wdrożenia w formacie markdown i nie powinny powielać ani powtarzać żadnej pracy wykonanej w sekcji analizy.

Pamiętaj, aby zapisać swój plan wdrożenia jako `.ai/{route}-route-implementation-plan.md`. Upewnij się, że plan jest szczegółowy, przejrzysty i zapewnia kompleksowe wskazówki dla zespołu programistów.