#ifndef __PLAYER_H__
#define __PLAYER_H__

#include "Constants.h"

class Player {
 public:
  int chips[NUM_COLORS];
  int cards[NUM_COLORS];

  void readFromStdin();
  bool canBuy(int cardId);

 private:
};

#endif
