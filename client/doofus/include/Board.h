#ifndef __BOARD_H__
#define __BOARD_H__

#include "ChipSet.h"
#include "Constants.h"

class Board {
 public:
  ChipSet chips;
  int cards[CARD_LEVELS][NUM_FACE_UP_CARDS];

  void readFromStdin();
  bool offers(ChipSet& take);
};

#endif
