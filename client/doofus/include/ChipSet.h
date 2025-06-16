#ifndef __CHIPSET_H__
#define __CHIPSET_H__

#include "Constants.h"

class ChipSet {
public:
  int c[NUM_COLORS];
  bool feasible;

  void fromArray(int* src);
  int total();
  bool isZero();
  int countPositive();
  int getMax();
  void subtract(ChipSet& other);

  // Scade, dar fără a trece pe minus.
  void boundedSubtract(ChipSet& other);

  // Verifică dacă acest ChipSet poate fi luat legal (trei de culori diferite
  // sau două de aceeași culoare). Nu ține deocamdată cont de oferta de pe
  // tablă.
  bool isValid();
};

#endif
