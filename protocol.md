# Protocol de interacțiune

## Codificarea pieselor

Cele 16 piese sînt codificate prin numerele de la 0 la 15. Cei 4 biți se referă la următoarele atribute:

* Bitul 3: valoarea 0 pentru piese negre, 1 pentru piese albe.
* Bitul 2: valoarea 0 pentru piese mici, 1 pentru piese mari.
* Bitul 1: valoarea 0 pentru piese rotunde, 1 pentru piese pătrate.
* Bitul 0: valoarea 0 pentru piese pline, 1 pentru piese găurite.

Nu este obligatoriu să adoptați această codificare (nici pe oricare alta). Puteți opera abstract, pe biți. Dar este codificarea pe care o adoptă vizualizarea grafică a partidelor.

Valoarea specială „-1” are ocazional semnificația „nicio piesă”.

## Codificarea pătratelor

Pătratele tablei sînt codificate de la stînga la dreapta și de sus în jos prin valorile de la 0 la 15. Așadar, indicii pătratelor sînt:

```
 0  1  2  3
 4  5  6  7
 8  9 10 11
12 13 14 15
```

Valoarea specială „-1” are ocazional semnificația „nicăieri”.

## Invocarea programului vostru

Programul vostru va fi invocat ori de cîte ori este rîndul lui să mute. El va primi în fișierul `input.txt` situația jocului astfel:

```
p0 p1 ... p15
hand
```

Unde

* _p0 ... p15_ reprezintă valorile pieselor din cele 16 pătrate ale tablei. Dacă un pătrat este gol, valoarea _p_ corespunzătoare va fi -1.
* _hand_ reprezintă piesa din mînă, pe care v-a oferit-o adversarul.

La prima mutare, tabla va consta doar din valori -1, iar _hand_ va avea valoarea -1.
