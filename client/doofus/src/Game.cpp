#include "Game.h"
#include <stdio.h>

void Game::readFromStdin() {
  int ignored, player_id;
  scanf("%d %d", &ignored, &player_id); // num_players
  scanf("%d", &ignored); // round_number
  board.readFromStdin();
  for (int i = 0; i <= player_id; i++) {
    player.readFromStdin(); // reține-l doar pe al nostru
  }
}

void Game::chooseAndMakeMove() {
  for (int l = CARD_LEVELS - 1; l >= 0; l--) {
    for (int i = 0; i < NUM_FACE_UP_CARDS; i++) {
      int card = board.cards[l][i];
      if (card && player.canBuy(card)) {
        printf("4 %d\n", card);
      }
    }
  }
}
