#include "Player.h"
#include "Util.h"
#include <stdio.h>

void Player::readFromStdin() {
  for (int col = 0; col <= NUM_COLORS; col++) {
    scanf("%d", &chips[col]);
    cards[col] = 0;
  }

  int num_cards, color;
  scanf("%d", &num_cards);
  while (num_cards--) {
    scanf("%d", &color);
    cards[color++];
  }

  Util::ignoreArrayFromStdin(); // ignoră cărțile rezervate
  Util::ignoreArrayFromStdin(); // ignoră nobilii
}

bool Player::canBuy(int cardId) {
  return false;
}
