---

### **Agenda Spotkania: Aplikacja dla Warsztatu Samochodowego**

| **Klient:** | Pan [Imię i Nazwisko Klienta] |
| :--- | :--- |
| **Data:** | [Data Spotkania] |
| **Cel:** | Zebranie wymagań do budowy MVP, zrozumienie procesów i ustalenie modelu współpracy. |

---

#### **I. Wprowadzenie i Zrozumienie Głównego Problemu**
*Cel: Zrozumieć obecną sytuację i największe bolączki.*

* [ ] **Jak wygląda typowy dzień w warsztacie?** (Proces od telefonu klienta do odbioru auta)
* [ ] **Główny problem: "Przeciekające pieniądze"** - Poproś o konkretny, niedawny przykład. Co się dokładnie stało?
* [ ] **Aktualny proces:** Jak wygląda obsługa zlecenia przy użyciu zeszytu? Jakie informacje są zapisywane?
* [ ] **Zespół:** Kto pracuje w warsztacie i jakie ma zadania? (Kto przyjmuje zlecenia, kto naprawia, kto zamawia części?)

---

#### **II. Wymagania Funkcjonalne dla Aplikacji (MVP)**
*Cel: Zmapować kluczowe procesy, które muszą znaleźć się w pierwszej wersji aplikacji.*

**[ ] 1. Klienci i Pojazdy**
* **Dane Klienta:** Jakie informacje są kluczowe? (Imię, Nazwisko, Nr telefonu, E-mail?)
* **Dane Pojazdu:** Jakie informacje są potrzebne? (Marka, Model, Rejestracja, VIN?)
* **Powiązania:** Czy klient może mieć wiele aut? Czy potrzebna jest historia napraw dla każdego pojazdu?

**[ ] 2. Zlecenia Napraw (Najważniejszy Moduł)**
* **Przyjęcie zlecenia:** Jakie informacje są zbierane na starcie? (Opis usterki, ustalenia z klientem).
* **Statusy prac:** Jak śledzić postęp? (np. *Nowe, Diagnoza, Czeka na części, W naprawie, Gotowe, Zakończone*).
* **Rejestracja pracy (Czas):**
    * Czy pracownicy mają rejestrować czas na konkretne zadania?
    * Jak liczona jest stawka za roboczogodzinę? Czy jest stała?
* **Rejestracja materiałów (Części):**
    * Jak dodawać zużyte części do zlecenia?
    * Czy wystarczy ręczne wpisanie nazwy i ceny, czy potrzebny jest prosty magazyn? (**Sugestia na MVP: ręczne wpisywanie**).
* **Finalizacja i cena:**
    * Co składa się na ostateczną cenę? (Robocizna + Części = Suma).
    * Jakie dokumenty są generowane? (Wydruk z podsumowaniem, Faktura, Paragon?).

**[ ] 3. Pracownicy i Uprawnienia**
* **Użytkownicy:** Kto będzie korzystał z aplikacji? (Właściciel, mechanicy?).
* **Role:** Jakie powinny być poziomy dostępu? (np. **Admin** - pełny dostęp; **Mechanik** - dostęp tylko do swoich zleceń, dodawanie czasu/części).

**[ ] 4. Raporty i Statystyki**
* Jaki jest **jeden, najważniejszy raport**, który chciałby Pan widzieć na koniec miesiąca? (np. Przychód, Zysk, Liczba napraw).
* Jakie dane pomogą podejmować lepsze decyzje? (**Sugestia na MVP: prosty raport przychodów i kosztów**).

---

#### **III. Model Współpracy (Aspekty Finansowe)**
*Cel: Znaleźć rozwiązanie korzystne dla obu stron.*

**[ ] Opcja 1: Oprogramowanie Dedykowane**
* **Opis:** Jednorazowa, wyższa opłata za stworzenie aplikacji na własność.
* **Pytanie:** "Czy woli Pan zainwestować większą kwotę z góry, aby aplikacja była w pełni Pana własnością?"

**[ ] Opcja 2: Abonament (SaaS)**
* **Opis:** Niska, stała opłata miesięczna/roczna za dostęp do aplikacji, jej utrzymanie i aktualizacje.
* **Pytanie:** "Czy preferuje Pan niższe koszty na start i stałą, przewidywalną opłatę abonamentową, która gwarantuje rozwój i wsparcie?"

**[ ] Opcja 3 (Sugerowana): Model Hybrydowy**
* **Opis:** Jednorazowa opłata wdrożeniowa (pokrycie kosztów MVP) + niski miesięczny abonament za utrzymanie i rozwój.
* **Pytanie:** "Proponuję rozwiązanie partnerskie: jednorazowa opłata za wdrożenie systemu, a następnie niewielki abonament zapewniający wsparcie i dostęp do wszystkich przyszłych aktualizacji. Co Pan o tym sądzi?"

---

#### **IV. Podsumowanie i Następne Kroki**

* [ ] **Podsumowanie kluczowych wymagań:** Krótko powtórz, co ustaliliście jako zakres MVP.
* [ ] **Ustalenie dalszych kroków:** "Na podstawie naszej rozmowy, w ciągu X dni przygotuję podsumowanie i wstępną ofertę."
* [ ] **Potwierdzenie zrozumienia:** "Czy wszystko, co omówiliśmy, jest jasne i zgadza się z Pana wizją?"

---

#### **Miejsce na Notatki:**