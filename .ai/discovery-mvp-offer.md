# System Zarządzania Warsztatem
## Oferta wdrożenia MVP

**Data:** 07.10.2025
**Dla:** Puzon-Auto
---

## 1. Problem i rozwiązanie

### Twój główny problem:
Tracisz pieniądze bo nie wiesz dokładnie ile czasu Twoi mechanicy poświęcają na konkretne naprawy. Pytani po czasie nie potrafią precyzyjnie odpowiedzieć, przez co:
- Nie wiesz ile faktycznie skasować od klienta
- Nie masz kontroli nad efektywnością zespołu
- Ciężko jest Ci oszacować czy naprawa była opłacalna

### Co Ci to da:
**System, w którym:**
- Każdy mechanik po wykonanej czynności dopisze swój czas (z laptopa w warsztacie lub z telefonu)
- W każdej chwili zobaczysz ile czasu sumarycznie poszło na konkretny samochód
- Zobaczysz ile każdy mechanik przepracował dzisiaj, w tym tygodniu, w tym miesiącu
- Będziesz mieć pełną historię napraw każdego auta
- Przestaniesz zgadywać i zaczniesz dokładnie rozliczać

---

## 2. Co będzie w systemie (MVP)

### 2.1 Kartoteka klientów

**Co będziesz mógł robić:**
- Dodać nowego klienta (imię, nazwisko, telefon, email, adres)
- Zobaczyć listę wszystkich klientów
- Wyszukać klienta po nazwisku
- Zobaczyć wszystkie auta danego klienta
- Zobaczyć historię wszystkich napraw dla tego klienta

**Po co to:**
Przestaniesz szukać po zeszytach "kto to był", będziesz miał wszystkie dane pod ręką.

---

### 2.2 Kartoteka pojazdów

**Co będziesz mógł robić:**
- Dodać nowy pojazd (marka, model, rejestracja, rocznik, VIN)
- Przypisać pojazd do klienta (klient może mieć kilka aut)
- Wyszukać pojazd po numerze rejestracyjnym lub VIN
- Zobaczyć pełną historię napraw danego auta

**Po co to:**
Jak przyjdzie klient z BMW X5 drugi raz, od razu zobaczysz co poprzednio robiliście i ile to zajęło.

---

### 2.3 Zlecenia (naprawy)

**Co będziesz mógł robić:**

**Tworzenie zlecenia:**
- Utworzyć nowe zlecenie dla konkretnego pojazdu
- Wpisać opis usterki zgłoszonej przez klienta
- Dodać zdjęcia (np. uszkodzenia, błędy na komputerze)
- Dodawać notatki w trakcie naprawy

**Statusy zlecenia:**
Zmienić status zlecenia w zależności od etapu:
- **Nowe** - właśnie przyjęte
- **Diagnoza** - mechanik diagnozuje usterkę
- **Wymaga kontaktu** - trzeba zadzwonić do klienta (np. z wyceną)
- **Czeka na części** - zamówione części, czekamy
- **W naprawie** - mechanik robi naprawę
- **Gotowe** - auto gotowe do odbioru
- **Zamknięte** - klient odebrał, sprawa zakończona

**Dopisywanie czasu przez mechaników:**
- Mechanik podchodzi do laptopa (lub wchodzi z telefonu)
- Wybiera swoje imię z listy
- Dopisuje ile czasu poświęcił na czynność (np. "diagnoza - 1.5h")
- Może dodać krótki opis co robił

**Widok zlecenia:**
- Zobaczysz pełną historię zlecenia
- Wszystkie zdjęcia w jednym miejscu
- Wszystkie notatki
- **Sumaryczny czas** jaki wszyscy mechanicy poświęcili na to auto
- Rozbicie kto ile czasu wpisał

**Po co to:**
To jest serce systemu. Tutaj następuje magia - przestajesz zgadywać, zaczynasz wiedzieć.

---

### 2.4 Karta pracownika

**Co będziesz mógł robić:**
- Wejść na kartę pracownika
- Zobaczyć jego statystyki:
  - Ile czasu wpisał **dzisiaj**
  - Ile czasu wpisał **w tym tygodniu**
  - Ile czasu wpisał **w tym miesiącu**
- Zobaczyć rozbicie na poszczególne zlecenia (na czym pracował)
- Zobaczyć historię jego pracy

**Po co to:**
Będziesz wiedział kto ile faktycznie robi

---

### 2.5 Widok ogólny zespołu

**Co będziesz mógł robić:**
- Zobaczyć wszystkich pracowników na jednym ekranie
- Porównać ich czasy w danym okresie
- Zobaczyć udział w zleceniach

**Po co to:**
Szybki rzut oka "jak pracuje zespół" bez klikania po każdym osobno.

---

### 2.6 Zarządzanie użytkownikami

**Role w systemie:**

**Właściciel (Ty):**
- Pełen dostęp do wszystkiego
- Dodawanie/usuwanie pracowników
- Dostęp do wszystkich raportów i statystyk
- Zarządzanie klientami, pojazdami, zleceniami

**Biuro:**
- Zarządzanie klientami i pojazdami
- Tworzenie i edycja zleceń
- Zmiana statusów
- Podgląd statystyk

**Mechanik (wspólne konto):**
- Przeglądanie zleceń
- Dopisywanie swojego czasu do zleceń
- Wybór siebie z listy (bez logowania)
- Edycja swoich czasów (w razie pomyłki)
- Dodawanie notatek i zdjęć do zleceń
- **Brak możliwości usuwania** klientów/aut/zleceń

**Po co to:**
Mechanicy nie muszą się przelogowywać - podchodzą, wybierają siebie, wpisują czas. Ty masz kontrolę nad tym kto co może robić.

---

### 2.7 Wyszukiwanie

**Co będziesz mógł robić:**
- Wyszukać klienta po nazwisku
- Wyszukać pojazd po numerze rejestracyjnym, marce, modelu
- Wyszukać pojazd po VIN
- Filtrować zlecenia po statusie

**Po co to:**
Szybko znajdziesz to czego szukasz, zero scrollowania.

---

### 2.8 Historia zmian (audit log)

**Co będzie rejestrowane:**
- Educje czasów
- Zmiany statusów zlecenia

**Po co to:**
Jak mechanik poprawi swój czas, będziesz wiedział że ten czas był edytowany i z ilu na ile

---

## 3. Czego NIE MA w MVP (może być w przyszłości)

✗ Zarządzanie częściami i magazynem  
✗ Kosztorysy i wyceny  
✗ Automatyczne faktury  
✗ Powiadomienia SMS/Email  
✗ Integracje z księgowością  
✗ Drukowanie dokumentów  
✗ Kalendarz i rezerwacje terminów  

**Dlaczego?**
Skupiamy się na rozwiązaniu Twojego największego problemu: **kontrola czasu pracy**. Resztę dodamy kiedy to zacznie działać i zobaczysz wartość.

---

## 4. Technologia

**Co użyjemy:**
- **Backend:** Laravel (framework PHP, stabilny i sprawdzony)
- **Frontend:** React + Inertia (nowoczesny, szybki interfejs)
- **Hosting:** Laravel Cloud (profesjonalny, zarządzany hosting)
- **Baza danych:** PostgreSQL (niezawodna, skalowalna)

**Co to dla Ciebie znaczy:**
- Szybki system, działa płynnie
- Responsywny - działa na komputerze, tablecie, telefonie
- Skalowalny - jak warsztat urośnie, system da radę

---

## 5. Jak będzie wyglądać praca z systemem?

### Scenariusz 1: Przyjęcie nowego zlecenia

1. Biuro (lub Ty) loguje się do systemu
2. Sprawdza czy klient jest w bazie
   - Jeśli nie - dodaje nowego klienta
3. Sprawdza czy auto jest w bazie
   - Jeśli nie - dodaje nowy pojazd
4. Tworzy nowe zlecenie:
   - Wybiera pojazd
   - Wpisuje opis usterki
   - Dodaje zdjęcia (opcjonalnie)
   - Ustawia status "Nowe"
5. Zlecenie trafia na listę zleceń

---

### Scenariusz 2: Mechanik pracuje nad autem

1. Mechanik podchodzi do laptopa w warsztacie (lub wchodzi z telefonu)
2. System jest otwarty na widoku zleceń
3. Mechanik klika w zlecenie nad którym pracuje
4. Klika "Dodaj czas"
5. Wybiera swoje imię z listy
6. Wpisuje czas (np. 2.5h)
7. Wpisuje co robił (np. "wymiana czujnika")
8. Zapisuje
9. Jeśli skończył swoją część - może zmienić status na "Gotowe" lub przypisać do kogoś innego

---

### Scenariusz 3: Sprawdzenie ile zajęła naprawa

1. Ty lub biuro wchodzi w zlecenie
2. Na dole widzi tabelkę:
   ```
   Mechanik          | Czas    | Czynność
   ------------------|---------|------------------
   Jan Kowalski      | 2.5h    | Wymiana rozrządu
   Piotr Nowak       | 1.0h    | Diagnoza
   Jan Kowalski      | 0.5h    | Test po naprawie
   ------------------|---------|------------------
   RAZEM             | 4.0h    |
   ```
3. Widzisz że naprawa zajęła 4 godziny
4. Mnożysz przez stawkę - wiesz ile naliczyć

---

### Scenariusz 4: Sprawdzenie statystyk pracownika

1. Wchodzisz w zakładkę "Pracownicy"
2. Klikasz na Jana Kowalskiego
3. Widzisz:
   ```
   Dzisiaj:        6.5h
   Ten tydzień:    28h
   Ten miesiąc:    112h
   
   Rozbicie na zlecenia:
   - VW Passat (ZK12345) - 4.0h
   - BMW X5 (ZK67890) - 2.5h
   ```
4. Widzisz dokładnie co i ile robił

---

