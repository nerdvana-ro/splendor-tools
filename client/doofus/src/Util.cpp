#include "Util.h"
#include <stdio.h>
#include <stdlib.h>

std::random_device Util::rd;
std::mt19937 Util::rng(rd());

int Util::min(int x, int y) {
  return (x < y) ? x : y;
}

int Util::max(int x, int y) {
  return (x > y) ? x : y;
}

int Util::rand() {
  int x = rng();
  return abs(x);
}

void Util::shuffle(int* v, int size) {
  for (int i = 1; i < size; i++ ) {
    std::uniform_int_distribution<> distrib(0, i);
    int j = distrib(rng);
    int tmp = v[i]; v[i] = v[j]; v[j] = tmp;
  }
}

void Util::ignoreArrayFromStdin() {
  int size, x;
  scanf("%d", &size);
  while (size--) {
    scanf("%d", &x);
  }
}
