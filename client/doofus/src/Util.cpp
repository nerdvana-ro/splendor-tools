#include "Util.h"
#include <stdio.h>

void Util::ignoreArrayFromStdin() {
  int size, x;
  scanf("%d", &size);
  while (size--) {
    scanf("%d", &x);
  }
}
