#ifndef __BOARD_H__
#define __BOARD_H__

#include "Constants.h"

class Board {
 public:
  int chips[NUM_COLORS];
  int cards[CARD_LEVELS][NUM_FACE_UP_CARDS];

  void readFromStdin();

 private:
};

#endif
