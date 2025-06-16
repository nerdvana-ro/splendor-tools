#include "Game.h"

int main(int argc, char** argv) {
  Game game;
  game.readFromStdin();
  game.chooseAndMakeMove();

  return 0;
}
