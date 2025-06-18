#ifndef __BOARD_H__
#define __BOARD_H__

#include <vector>
#include "ChipSet.h"
#include "Constants.h"

class Board {
 public:
  ChipSet chips;
  std::vector<int> cards; // convenție: cărțile de nivel mare primele

  void readFromStdin();
  bool offers(ChipSet& take);
};

#endif
