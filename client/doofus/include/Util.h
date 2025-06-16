#ifndef __UTIL_H__
#define __UTIL_H__

#include <random>

class Util {
 public:
  static std::random_device rd;
  static std::mt19937 rng;

  static int min(int x, int y);
  static int max(int x, int y);
  static int rand();
  static void shuffle(int* v, int size);
  static void ignoreArrayFromStdin();
};

#endif
