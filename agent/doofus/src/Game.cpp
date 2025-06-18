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

  ChipSet take = saveForSomeCard(); // posibil zero
  padTake(take);
  takeAction(take);
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

  for (int card: board.cards) {
    take = computeTake(card);
    if (!take.isZero()) {
      fprintf(stderr, "kibitz Strîng pentru cartea %d.\n", card);
      return take;
    }
  }

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

void Game::padTake(ChipSet& take) {
  int cnt = take.countPositive(), max = take.getMax();
  int bcnt = board.chips.countPositive(), bmax = board.chips.getMax();

  if (max == 2) {
    // Am planificat deja să luăm două de aceeași culoare.
  } else if ((max == 0) && (bcnt == 1) && (bmax >= TAKE_TWO_LIMIT)) {
    // Încă nu am luat nimic și merită să luăm două de aceeași culoare.
    int col = board.chips.findColorWithCount(bmax);
    take.c[col] = 2;
  } else {
    padTakeWithOnes(take);
  }
}

void Game::padTakeWithOnes(ChipSet& take) {
  std::vector<int> cols;
  for (int i = 0; i < NUM_COLORS; i++) {
    if ((take.c[i] <= 0) && (board.chips.c[i] > 0)) {
      cols.push_back(i);
    }
  }
  Util::shuffle(cols);

  int cnt = take.countPositive();
  // Împreună cu ce am planificat deja să iau, nu pot lua mai mult de 3 jetoane.
  int limit1 = 3 - cnt;
  // Nu vreau să depășesc 10 jetoane în mînă dacă nu este necesar.
  int limit2 = Util::max(HAND_LIMIT - player.chips.total() - cnt, 0);
  // Nu pot lua mai multe jetoane decît numărul de culori disponibile.
  int limit3 = cols.size();
  int toTake = Util::min(Util::min(limit1, limit2), limit3);

  if (toTake) {
    fprintf(stderr, "kibitz Completez cu %d jetoane la întîmplare.\n", toTake);
    for (int i = 0; i < toTake; i++) {
      take.c[cols[i]] = 1;
    }
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
