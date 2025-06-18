#ifndef __GAME_H__
#define __GAME_H__

#include "Board.h"
#include "Player.h"

class Game {
 public:
  void readFromStdin();
  void chooseAndMakeMove();

 private:
  Board board;
  Player player;

  int getBuyableCardId();

  // Returnează un ChipSet gol dacă nu poate colecționa nimic astfel încît să
  // poată cumpăra o carte la tura următoare.
  ChipSet saveForSomeCard();

  // Returnează jetoanele pe care trebuie să le ia jucătorul pentru a putea
  // cumpăra cartea. Presupune că jucătorul nu poate cumpăra deja cartea.
  // Evaluează, în ordine, trei condiții.
  //
  // 1. Jucătorul nu trebuie să strîngă mai mult de 10 jetoane.
  // 2. Jetoanele necesare trebuie să poată fi luate într-o singură mutare.
  // 3. Pe masă trebuie să existe acele jetoane.
  //
  // Rezultatul conține valori pozitive pentru culorile de luat și valori
  // negative pentru culorile care pot fi returnate, la nevoie.
  ChipSet computeTake(int cardId);

  // Adaugă jetoane de luat la take. take este o colecție legală și care puate
  // fi luată într-o singură tură (poate fi și zero).
  void padTake(ChipSet& take);

  // Adaugă jetoane de luat la take, știind că luăm cel mult trei jetoane de
  // culori diferite.
  void padTakeWithOnes(ChipSet& take);

  void buyAction(int card);
  void takeAction(ChipSet& take);
  void returnAction(ChipSet& take);
};

#endif
