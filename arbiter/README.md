Arbitrul este unealta care interfațează mai mulți agenți. Arbitrul ține evidența jocului și invocă pe rînd fiecare agent, comunicîndu-i starea curentă. Apoi citește răspunsul agentului și actualizează starea jocului.

## Pas specific pentru Windows: instalați WSL

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

Va cere reboot, apoi va finaliza instalarea. Alegeți un nume de utilizator și o parolă. Apoi vă veți afla într-un prompt de Linux.

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

De asemenea, puteți vedea sistemul de fișiere Linux din Windows. Din File Explorer, navigați la Linux > Ubuntu-25.04 > /home/\<username\>/ etc.
