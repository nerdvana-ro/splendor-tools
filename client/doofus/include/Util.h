#ifndef __UTIL_H__
#define __UTIL_H__

#include <random>

class Util {
 public:
  static std::random_device rd;
  static std::mt19937 rng;

  static int min(int x, int y);
  static int max(int x, int y);
  static int rand(int lo, int hi);
  static void shuffle(std::vector<int>& s);
  static void ignoreArrayFromStdin();
};

#endif
