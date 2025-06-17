#include <random>
#include <stdio.h>
#include <vector>
#include "Card.h"
#include "Game.h"
#include "Util.h"

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
  int card = getBuyableCardId();
  if (card) {
    buyAction(card);
    return;
  }

  ChipSet take = saveForSomeCard();
  if (!take.isZero()) {
    takeAction(take);
    return;
  }

  takeRandomChips();
}

int Game::getBuyableCardId() {
  for (int card: board.cards) {
    if (player.canBuy(card)) {
      return card;
    }
  }
  return 0;
}

ChipSet Game::saveForSomeCard() {
  ChipSet take;
  int i = 0;

  do {
    take = computeTake(board.cards[i++]);
  } while (take.isZero() && (i < board.cards.size() - 1));

  return take;
}

ChipSet Game::computeTake(int cardId) {
  Card card = Card::get(cardId);
  ChipSet take(card.cost);
  take.boundedSubtract(player.cards);
  // Acum take reprezintă jetoanele necesare în mînă, peste bonusuri.
  if (take.total() <= HAND_LIMIT) {
    take.subtract(player.chips);

    // Acum take reprezintă jetoanele de luat
    if (!take.isSingleTake() || !board.offers(take)) {
      take.clear();
    }
  } else {
    take.clear();
  }

  return take;
}

void Game::takeRandomChips() {
  fprintf(stderr, "kibitz Trag jetoane la întîmplare.\n");
  std::vector<int> s = board.chips.getNonEmpty();

  bool worthTakingTwo =
    (s.size() == 1) &&
    (board.chips.c[s[0]] >= TAKE_TWO_LIMIT);

  if (worthTakingTwo) {
    printf("2 %d", s[0]);
    player.chips.c[s[0]] += 2;
  } else {
    int toTake = Util::min(s.size(), 3);
    Util::shuffle(s);
    printf("1 %d", toTake);
    for (int i = 0; i < toTake; i++) {
      printf(" %d", s[i]);
      player.chips.c[s[i]]++;
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

void Game::buyAction(int card) {
  printf("4 %d\n", card);
}

void Game::takeAction(ChipSet& take) {
  int cnt = take.countPositive(), max = take.getMax();

  bool takeTwo = (cnt == 1) && (max == 2);

  if (takeTwo) {
    int col = take.findColorWithCount(2);
    printf("2 %d", col);
    player.chips.c[col] += 2;
  } else {
    printf("1 %d", cnt);
    for (int col = 0; col < NUM_COLORS; col++) {
      if (take.c[col] > 0) {
        printf(" %d", col);
        player.chips.c[col]++;
      }
    }
  }

  returnAction(take);

  printf("\n");
}

void Game::returnAction(ChipSet& take) {
  int toReturn = player.chips.total() - HAND_LIMIT;

  for (int col = 0; col < NUM_COLORS; col++) {
    while ((take.c[col] < 0) && (toReturn > 0)) {
      printf(" %d", col);
      take.c[col]++;
      toReturn--;
    }
  }
}
