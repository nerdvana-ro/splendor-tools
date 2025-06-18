#include <stdio.h>
#include "Card.h"
#include "Player.h"
#include "Util.h"

void Player::readFromStdin() {
  for (int col = 0; col <= NUM_COLORS; col++) {
    scanf("%d", &chips.c[col]);
    cards.c[col] = 0;
  }

  int num_cards, id;
  scanf("%d", &num_cards);
  while (num_cards--) {
    scanf("%d", &id);
    int color = Card::get(id).color;
    cards.c[color]++;
  }

  Util::ignoreArrayFromStdin(); // ignoră cărțile rezervate
  Util::ignoreArrayFromStdin(); // ignoră nobilii
}

bool Player::canBuy(int cardId) {
  Card card = Card::get(cardId);
  ChipSet take(card.cost);
  take.boundedSubtract(chips);
  take.boundedSubtract(cards);
  return take.isZero();
}
