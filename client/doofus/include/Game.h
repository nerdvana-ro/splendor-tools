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
};

#endif
