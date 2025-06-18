// Un agent rudimentar de Splendor.
//
// * Cumpără cartea de nivel maxim pe care o poate cumpăra.
// * Altfel încearcă să strîngă jetoane pentru cartea de nivel maxim.
// * Altfel strînge jetoane la întîmplare.
// * Nu urmărește nobilii.
// * Nu rezervă cărți.
// * Nu folosește aur.
#include "Game.h"

int main(int argc, char** argv) {
  Game game;
  game.readFromStdin();
  game.chooseAndMakeMove();

  return 0;
}
