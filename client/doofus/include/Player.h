#ifndef __PLAYER_H__
#define __PLAYER_H__

#include "Card.h"
#include "ChipSet.h"
#include "Constants.h"

class Player {
public:
  ChipSet chips;
  ChipSet cards;

  void readFromStdin();
  bool canBuy(int cardId);
  ChipSet computeTake(int cardId);
};

#endif
