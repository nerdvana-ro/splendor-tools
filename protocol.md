# Protocol de interacțiune

## Invocarea clientului

Arbitrul va executa programul vostru, numit și **client**, de fiecare dată cînd este rîndul său la mutare.

Arbitrul îi va trimite clientului, la intrarea standard, situația jocului conform specificațiilor de mai jos. Clientul trebuie să tipărească la ieșirea standard o acțiune, conform specificațiilor de mai jos.

Clientul poate tipări orice mesaje la eroarea standard (`cerr` / `stderr`). Arbitrul le va ignora pe toate, cu excepția celor care încep cu prefixul `kibitz<spațiu>`. Pe acestea, arbitrul le va include în partida salvată. Puteți chibița orice doriți (sau nimic) despre numărul de poziții analizate, scoruri, motivul alegerii acțiunii pe care ați ales-o etc.

Clientul are la dispoziție 10 secunde per mutare și poate folosi oricîtă memorie în limita RAM-ului (laptopul meu are 16 GB).

Dacă clientul se termină anormal, depășește timpul sau încearcă să facă o acțiune incorectă, atunci arbitrul va face o acțiune specială, pas, adică clientul nu va face nicio acțiune.

## Datele de intrare

Datele de intrare au următorul format (fără porțiunile de la `//` pînă la sfîrșitul liniei, care sînt clarificări)

```
num_players                      // Numărul de jucători, între 1 și 4.
```
