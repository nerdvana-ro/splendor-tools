#include <algorithm>
#include <stdio.h>
#include "Board.h"
#include "Constants.h"
#include "Util.h"

void Board::readFromStdin() {
  int x, ignored;

  for (int col = 0; col <= NUM_COLORS; col++) {
    scanf("%d", &chips.c[col]);
  }

  cards.reserve(NUM_CARD_LEVELS * NUM_FACE_UP_CARDS_PER_LEVEL);
  for (int l = 0; l < NUM_CARD_LEVELS; l++) {
    scanf("%d", &ignored); // numărul de cărți cu fața în jos
    for (int i = 0; i < NUM_FACE_UP_CARDS_PER_LEVEL; i++) {
      scanf("%d", &x);
      if (x) {
        cards.push_back(x);
      }
    }
  }
  std::reverse(cards.begin(), cards.end());

  Util::ignoreArrayFromStdin(); // ignoră nobilii
}

bool Board::offers(ChipSet& take) {
  bool enough = true;
  for (int col = 0; col < NUM_COLORS; col++) {
    enough &=
      (take.c[col] <= 0) ||                                     // nu avem de luat
      ((take.c[col] == 1) && (chips.c[col] >= 1)) ||            // avem de luat 1
      ((take.c[col] == 2) && (chips.c[col] >= TAKE_TWO_LIMIT)); // avem de luat 2
  }
  return enough;
}
