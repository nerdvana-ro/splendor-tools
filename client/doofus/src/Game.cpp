#include "Card.h"
#include "Game.h"
#include "Util.h"
#include <random>
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
  if (!tryToBuyCard()) {
    if (!tryToCollectForCard()) {
      collectRandomChips();
    }
  }
}

void Game::collectRandomChips() {
  fprintf(stderr, "kibitz Trag jetoane la întîmplare\n");
  int stack[NUM_COLORS];
  int numStacks = 0;
  for (int col = 0; col < NUM_COLORS; col++) {
    if (board.chips.c[col]) {
      stack[numStacks++] = col;
    }
  }

  if ((numStacks == 1) && (board.chips.c[stack[0]] >= TAKE_TWO_LIMIT)) {
    printf("2 %d", stack[0]);
    player.chips.c[stack[0]] += 2;
  } else {
    int takeStacks = Util::min(numStacks, 3);
    Util::shuffle(stack, numStacks);
    printf("1 %d", takeStacks);
    for (int i = 0; i < takeStacks; i++) {
      printf(" %d", stack[i]);
      player.chips.c[stack[i]]++;
    }
  }

  returnRandomChips();

  printf("\n");
}

void Game::returnRandomChips() {
  while (player.chips.total() > HAND_LIMIT) {
    int col;
    do {
      col = Util::rand(0, NUM_COLORS - 1);
    } while (!player.chips.c[col]);
    printf(" %d", col);
    player.chips.c[col]--;
  }
}

bool Game::tryToBuyCard() {
  for (int l = CARD_LEVELS - 1; l >= 0; l--) {
    for (int i = 0; i < NUM_FACE_UP_CARDS; i++) {
      int card = board.cards[l][i];
      if (card && player.canBuy(card)) {
        printf("4 %d\n", card);
        return true;
      }
    }
  }
  return false;
}

bool Game::tryToCollectForCard() {
  for (int l = CARD_LEVELS - 1; l >= 0; l--) {
    for (int i = 0; i < NUM_FACE_UP_CARDS; i++) {
      if (canCollectForCard(board.cards[l][i])) {
        return true;
      }
    }
  }
  return false;
}

bool Game::canCollectForCard(int cardId) {
  ChipSet take = player.computeTake(cardId);
  if (take.feasible && board.offers(take)) {
    takeAction(take);
    return true;
  }
  return false;
}

void Game::takeAction(ChipSet& take) {
  int cnt = take.countPositive(), max = take.getMax();

  bool takeTwo = (cnt == 1) && (max == 2);

  if (takeTwo) {
    int col = 0;
    while (take.c[col] != 2) {
      col++;
    }
    printf("2 %d", col);
    player.chips.c[col] += 2;
  } else {
    printf("1 %d", cnt);
    for (int col = 0; col < NUM_COLORS; col++) {
      if (take.c[col]) {
        printf(" %d", col);
        player.chips.c[col]++;
      }
    }
  }

  if (player.chips.total() > HAND_LIMIT) {
    returnAction(take);
  }

  printf("\n");
}

void Game::returnAction(ChipSet& take) {
  int toReturn = player.chips.total() - HAND_LIMIT;

  for (int col = 0; col < NUM_COLORS; col++) {
    while ((take.c[col] < 0) && toReturn) {
      printf(" %d", col);
      take.c[col]++;
      toReturn--;
    }
  }
}
