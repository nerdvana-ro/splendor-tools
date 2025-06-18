# Protocol de interacțiune

## Invocarea clientului

Arbitrul va executa programul vostru, numit și **client**, de fiecare dată cînd este rîndul său la mutare.

Arbitrul îi va trimite clientului, la intrarea standard, situația jocului conform specificațiilor de mai jos. Clientul trebuie să tipărească la ieșirea standard o acțiune, conform specificațiilor de mai jos.

Clientul poate tipări orice mesaje la eroarea standard (`cerr` / `stderr`). Arbitrul le va ignora pe toate, cu excepția celor care încep cu prefixul `kibitz<spațiu>`. Pe acestea, arbitrul le va include în partida salvată. Puteți chibița orice doriți (sau nimic) despre numărul de poziții analizate, scoruri, motivul alegerii acțiunii pe care ați ales-o etc.

Clientul are la dispoziție 10 secunde per mutare și poate folosi oricîtă memorie în limita RAM-ului (laptopul meu are 16 GB).

Dacă doriți, clientul poate stoca orice date în directorul curent, în limite rezonabile (cîțiva GB). Dacă doriți să-mi trimiteți în avans niște date precalculate, trimiteți-mi-le cumva.

Dacă clientul se termină anormal, depășește timpul sau încearcă să facă o acțiune incorectă, atunci arbitrul va alege automat mutarea „pas” (adică clientul nu va face nicio acțiune).

## Datele de intrare

Datele de intrare au următorul format, fără linii goale și fără comentarii, care există doar pentru clarificări.

### Detalii globale despre joc

```
// Numărul de jucători, între 1 și 4.
// Numărul de ordine al clientului vostru, între 0 și num_players - 1.
num_players your_id

// Numărul rundei curente, indexată de la 0.
round_number
```

### Detalii despre centrul mesei

```
// Numerele de jetoane de cele șase culori.
num_red num_green num_blue num_white num_black num_gold

// Numărul de cărți de nivelul 1 cu fața în jos.
// ID-urile cărților de nivelul 1 cu fața în sus. Au valori între 1 și 40 sau 0 dacă au rămas
// mai puțin de patru cărți.
pack_1 card_11 card_12 card_13 card_14

// Similar pentru nivelurile 2 și 3. Valori între 41 și 70, respectiv 71 și 90.
pack_2 card_21 card_22 card_23 card_24
pack_3 card_31 card_32 card_33 card_34

// Numărul de nobili, urmat de ID-urile acestora (valori între 1 și 10).
num_nobles nob_1 nob_2 ...
```

### Detalii despre bunurile jucătorilor

Pentru fiecare jucător, în ordinea mesei, cîte o secțiune cu conținutul:

```
// Numerele de jetoane de cele șase culori.
num_red num_green num_blue num_white num_black num_gold

// Numărul de cărți urmat de ID-urile acestora (numere între 1 și 90).
num_cards card_1 card_2 ...

// Numărul de cărți rezervate urmat de ID-urile acestora. Pentru cărți secrete ale altor
// jucători, valorile -1, -2 sau -3 indică nivelul cărții.
num_reserved_cards rcard_1 rcard_2 ...

// Numărul de nobili, urmat de ID-urile acestora (valori între 1 și 10).
num_nobles nob_1 nob_2 ...
```

## Datele de ieșire

Programul vostru trebuie să tipărească acțiunea dorită. Orice acțiune este descrisă prin valori întregi și, eventual, este urmată de cîmpuri suplimentare care descriu jetoanele returnate. Valorile întregi pot fi despărțite prin oricîte spații sau linii noi, pe care arbitrul le ignoră.

### Acțiunea „ia jetoane de culori diferite”

Această acțiune are formatul:

```
// 1 = Identificatorul acțiunii.
// Numărul de jetoane și culorile lor.
1 num_chips color_1 color_2 ...
```

Dacă doriți, puteți lua și doar două jetoane, unul singur sau chiar zero. În cazul rarisim cînd nu poate face nicio acțiune (nu există jetoane de luat și nu poate cumpăra / rezerva nicio carte), clientul trebuie să aleagă această acțiune și să ceară 0 jetoane.

### Acțiunea „ia două jetoane de aceeași culoare”

Această acțiune are formatul:

```
// 2 = Identificatorul acțiunii.
// culoarea jetoanelor cerute
2 color
```

### Acțiunea „rezervă o carte”

Această acțiune are formatul:

```
// 3 = Identificatorul acțiunii.
// ID-ul cărții rezervate
3 card_id
```

Aici, `card_id` se poate referi la una dintre cele 12 cărți cu fața în sus sau poate fi `-1`, `-2` sau `-3` pentru a indica prima carte din pachetul de nivel 1, 2, respectiv 3.

Arbitrul vă va oferi automat un jeton de aur, dacă el este disponibil (clientul vostru trebuie să țină evidența ca să știe dacă l-a primit sau nu).

### Acțiunea „cumpără o carte”

```
// 4 = Identificatorul acțiunii
// ID-ul cărții cumpărate
4 card_id
```

Aici, `card_id` se poate referi la una dintre cele 12 cărți cu fața în sus sau la una dintre cărțile din rezerva clientului.

Arbitrul va deduce automat costurile cărții din mîna clientului. Arbitrul va folosi în ordine, pentru fiecare dintre cele 5 culori:

1. cărți-bonus;
2. dacă încă este nevoie, jetoane de acea culoare;
3. dacă încă este nevoie, jetoane de aur.

### Returnarea jetoanelor

Acțiunile 1-3 îi aduc clientului jetoane suplimentare. Dacă clientul ajunge astfel la `j > 10` jetoane în mînă, continuați linia cu o listă de `j - 10` culori pentru jetoanele de returnat. Arbitrul ține și el evidența bunurilor și știe cîte jetoane așteaptă din partea clientului.

### Acțiuni incorecte

Orice acțiuni care se abat de la aceste reguli sînt considerate mutări incorecte. Ele sînt înlocuite cu acțiunea „pas” și nu afectează starea jocului în niciun fel. Exemple:

* Încercarea de a lua 3 jetoane, din care două au aceeași culoare.
* Încercarea de a lua 2 jetoane de aceeași culoare dintr-un teanc insuficient de mare.
* Încercarea de a rezerva a 4-a carte.
* Încercarea de a cumpăra o carte inexistentă.
* Încercarea de a cumpăra o carte pe care clientul nu o poate plăti.
