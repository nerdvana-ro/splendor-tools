#include "Board.h"
#include "Constants.h"
#include "Util.h"
#include <stdio.h>

void Board::readFromStdin() {
  int num_nobles, ignored;

  for (int col = 0; col <= NUM_COLORS; col++) {
    scanf("%d", &chips[col]);
  }

  for (int l = 0; l < CARD_LEVELS; l++) {
    scanf("%d", &ignored); // pack_<l>
    for (int i = 0; i < NUM_FACE_UP_CARDS; i++) {
      scanf("%d", &cards[l][i]);
    }
  }

  Util::ignoreArrayFromStdin(); // ignoră nobilii
}
