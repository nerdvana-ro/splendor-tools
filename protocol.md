# Protocol de interacțiune

## Invocarea clientului

Arbitrul va executa programul vostru, numit și **client**, de fiecare dată cînd este rîndul său la mutare.

Arbitrul îi va trimite clientului, la intrarea standard, situația jocului conform specificațiilor de mai jos. Clientul trebuie să tipărească la ieșirea standard o acțiune, conform specificațiilor de mai jos.

Clientul poate tipări orice mesaje la eroarea standard (`cerr` / `stderr`). Arbitrul le va ignora pe toate, cu excepția celor care încep cu prefixul `kibitz<spațiu>`. Pe acestea, arbitrul le va include în partida salvată. Puteți chibița orice doriți (sau nimic) despre numărul de poziții analizate, scoruri, motivul alegerii acțiunii pe care ați ales-o etc.

Clientul are la dispoziție 10 secunde per mutare și poate folosi oricîtă memorie în limita RAM-ului (laptopul meu are 16 GB).

Dacă clientul se termină anormal, depășește timpul sau încearcă să facă o acțiune incorectă, atunci arbitrul va face o acțiune specială, pas, adică clientul nu va face nicio acțiune.

## Datele de intrare

Datele de intrare au următorul format, fără linii goale și fără comentarii, care există doar pentru clarificări

### Detalii globale despre joc

```
// Numărul de jucători, între 1 și 4.
// Numărul de ordine al clientului vostru, între 1 și num_players.
num_players your_id

// Numărul rundei curente, indexată de la 1.
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

Pentru fiecare jucător, începînd cu primul, cîte o secțiune cu conținutul:

```
// Numerele de jetoane de cele șase culori.
num_red num_green num_blue num_white num_black num_gold

// Numărul de cărți urmat de ID-urile acestora (numere între 1 și 90).
num_cards card_1 card_2 ...

// Numărul de cărți rezervate urmat de ID-urile acestora. Pentru cărți secrete, valorile
// -1, -2 sau -3 indică nivelul cărții.
num_reserved_cars rcard_1 rcard_2 ...

// Numărul de nobili, urmat de ID-urile acestora (valori între 1 și 10).
num_nobles nob_1 nob_2 ...
```

