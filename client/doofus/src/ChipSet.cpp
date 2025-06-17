#include <stdio.h>
#include "ChipSet.h"
#include "Util.h"

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

void ChipSet::clear() {
  for (int col = 0; col < NUM_COLORS; col++) {
    c[col] = 0;
  }
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

int ChipSet::findColorWithCount(int cnt) {
  int col = 0;
  while ((col < NUM_COLORS) && (c[col] != cnt)) {
    col++;
  }
  return col;
}

std::vector<int> ChipSet::getNonEmpty() {
  std::vector<int> res;
  for (int col = 0; col < NUM_COLORS; col++) {
    if (c[col]) {
      res.push_back(col);
    }
  }
  return res;
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

bool ChipSet::isSingleTake() {
  int cnt = countPositive(), max = getMax();

  return ((cnt <= 3) && (max <= 1)) ||
    ((cnt <= 1) && (max <= 2));
}
