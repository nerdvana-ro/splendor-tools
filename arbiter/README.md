Arbitrul organizează partide între mai mulți agenți. Arbitrul ține evidența jocului și invocă pe rînd fiecare agent, comunicîndu-i starea curentă. Apoi citește răspunsul agentului și actualizează starea jocului.

## Pas specific pentru Windows: instalează WSL

Arbitrul este scris în PHP. Există mai multe moduri de a rula PHP în Windows. Instrucțiunile următoare documentează prima metodă.

1. Prin [WSL]([url](https://learn.microsoft.com/en-us/windows/wsl/)) (Windows Subsystem for Linux).
2. Printr-o mașină virtuală.
3. Direct cu PHP pentru Windows (nu am încercat).

Instalați o distribuție de GNU/Linux (implicit Ubuntu). Din terminal, listați distribuțiile disponibilie:

```bash
wsl --list --online
```

Apoi instalați-o pe cea dorită, de exemplu

```bash
wsl --install Ubuntu-25.04
```

Va cere reboot, apoi va finaliza instalarea. Alegeți-vă un nume de utilizator și o parolă. Apoi vă veți găsi într-un prompt de Linux.

Aduceți la zi sistemul și instalați PHP. Pentru Ubuntu, comenzile necesare sînt:

```bash
sudo apt update
sudo apt upgrade
sudo apt install php
```

Testați că PHP merge:

```bash
php --version
```

Puteți vedea sistemul de fișiere Windows din Linux:

```bash
ls /mnt/c/
```

De asemenea, puteți vedea sistemul de fișiere Linux din Windows. Din File Explorer, navighați la Linux > Ubuntu-25.04 > /home/\<username\>/ etc.

## Clonați repoul și testați arbitrul

Navigați într-un director bine ales. 🙂 Apoi:

```bash
git clone https://github.com/nerdvana-ro/splendor-tools
cd splendor-tools
```

Pe viitor, avînd în vedere că eu continui să lucrez la cod, puteți obține ultima versiune a codului executînd, din interiorul directorului, comanda:

```bash
git pull
```

Rulați arbitrul, fără argumente, ca să vă asigurați că merge:

```bash
php arbiter/arbiter.php
```

Dacă merge, veți vedea un mesaj cu instrucțiuni de apelare.

## Compilați clientul Doofus

Pentru aceasta, veți avea nevoie de compilatorul de C++ (`g++`) și de utilitarele `cmake` și `make`. Vom descoperi împreună ce pachete trebuie instalate. Pentru Ubuntu, cred că sînt acestea:

```bash
sudo apt install build-essential cmake
```

Acum puteți compila agentul:

```bash
cd agent/doofus/build
cmake ../
make
cd ../../../
```

## Rulați o partidă între două copii ale agentului Doofus

```bash
php arbiter/arbiter.php --binary agent/doofus/build/doofus --name doofus1 --binary agent/doofus/build/doofus --name doofus2
```

Arbitrul va vărsa ecrane întregi de informații, cu starea jocului după fiecare mutare (grafică text).

Avem nevoie și să salvăm partidele ca să le putem studia. Creați un director pentru partidele salvate, de exemplu:

```bash
mkdir ~/Desktop/games
```

Rulați din nou arbitrul și spuneți-i să salveze partida:

```bash
php arbiter/arbiter.php --binary agent/doofus/build/doofus --name doofus1 --binary agent/doofus/build/doofus --name doofus2 --save ~/Desktop/games/
```

Acum în `~/Desktop/games` veți găsi fișierul `game-001.json`.

## Opțiuni de configurare pentru arbitru

Arbitrul mai admite opțiunile `--games <număr>` pentru a organiza mai mult de o partidă și `--seed <număr>` pentru a genera în mod repetabil același pachet de cărți.

În plus, puteți modifica valorile constantelor din `Config.php`. Fiecare constantă este documentată. De exemplu, puteți reduce nivelul de zgomot modificînd valoarea lui `LOG_LEVEL`, de exemplu în `Log::INFO` ca să nu mai tipărească mesajele de debug.

## Adversar uman

Dacă doriți să jucați voi înșivă o partidă contra agentului, puteți pasa `--binary human --name orice_nume`. Cînd vă vine rîndul, arbitrul va aștepta o mutare de la tastatură, în formatul cunoscut.

