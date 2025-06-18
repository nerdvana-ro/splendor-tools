// Client care face doar echo la ce citește la intrare.
#include <stdio.h>
#include <string.h>

const int MAX_LINE_SIZE = 1000;

void strip_trailing_newline(char* s) {
  int len = strlen(s);
  if (len && (s[len - 1] == '\n')) {
    s[len - 1] = '\0';
    // Go, Windows, go!
    if ((len >= 2) && (s[len - 2] == '\r')) {
      s[len - 2] = '\0';
    }
  }
}

int main() {
  char msg[MAX_LINE_SIZE + 2]; // spațiu pentru \n\0

  while (fgets(msg, MAX_LINE_SIZE, stdin)) {
    strip_trailing_newline(msg);
    printf("Eu sînt echo.cpp și am citit linia: [%s]\n", msg);
  }

  return 0;
}
