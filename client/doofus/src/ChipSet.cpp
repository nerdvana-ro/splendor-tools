#include "ChipSet.h"
#include "Util.h"
#include <stdio.h>

void ChipSet::fromArray(int* src) {
  for (int col = 0; col < NUM_COLORS; col++) {
    c[col] = src[col];
  }
}

int ChipSet::total() {
  int sum = 0;
  for (int col = 0; col < NUM_COLORS; col++) {
    sum += c[col];
  }
  return sum;
}

bool ChipSet::isZero() {
  bool result = true;
  for (int col = 0; col < NUM_COLORS; col++) {
    result &= (c[col] == 0);
  }
  return result;
}

int ChipSet::countPositive() {
  int result = 0;
  for (int col = 0; col < NUM_COLORS; col++) {
    result += (c[col] > 0);
  }
  return result;
}

int ChipSet::getMax() {
  int result = 0;
  for (int col = 0; col < NUM_COLORS; col++) {
    result = Util::max(result, c[col]);
  }
  return result;
}

void ChipSet::subtract(ChipSet& other) {
  for (int col = 0; col < NUM_COLORS; col++) {
    c[col] -= other.c[col];
  }
}

void ChipSet::boundedSubtract(ChipSet& other) {
  for (int col = 0; col < NUM_COLORS; col++) {
    c[col] = Util::max(c[col] - other.c[col], 0);
  }
}

bool ChipSet::isValid() {
  int cnt = countPositive(), max = getMax();

  return ((cnt <= 3) && (max <= 1)) ||
    ((cnt <= 1) && (max <= 2));
}
