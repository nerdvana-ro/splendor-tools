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

  void collectRandomChips();
  void returnRandomChips();
  bool tryToBuyCard();
  bool tryToCollectForCard();
  bool canCollectForCard(int cardId);
  void takeAction(ChipSet& take);
  void returnAction(ChipSet& take);
};

#endif
