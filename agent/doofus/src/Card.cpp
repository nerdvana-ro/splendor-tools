#include "Card.h"

Card Card::get(int id) {
  return Card((int*)CARDS[id]);
}

Card::Card(int* src) {
  cost.fromArray(src);
  color = src[NUM_COLORS];
  points = src[NUM_COLORS + 1];
}
