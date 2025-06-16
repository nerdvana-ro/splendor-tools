#ifndef __CARD_H__
#define __CARD_H__

#include "ChipSet.h"
#include "Constants.h"

class Card {
public:
  ChipSet cost;
  int color;
  int points;

  static Card get(int id);

private:
  Card(int* src);
};

#endif
