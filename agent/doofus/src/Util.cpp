#include <algorithm>
#include <ranges>
#include <stdio.h>
#include <stdlib.h>
#include "Util.h"

std::random_device Util::rd;
std::mt19937 Util::rng(rd());

int Util::min(int x, int y) {
  return (x < y) ? x : y;
}

int Util::max(int x, int y) {
  return (x > y) ? x : y;
}

int Util::rand(int lo, int hi) {
  std::uniform_int_distribution<> distrib(lo, hi);
  return distrib(rng);
}

void Util::shuffle(std::vector<int>& s) {
  std::ranges::shuffle(s, rng);
}

void Util::ignoreArrayFromStdin() {
  int size, x;
  scanf("%d", &size);
  while (size--) {
    scanf("%d", &x);
  }
}
