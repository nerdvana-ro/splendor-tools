<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/lib/Core.php';

main();

function main(): void {
  try {
    $args = new Args();
    $args->parse();
    Card::loadCsv(__DIR__ . '/../cards.csv');
    Noble::loadCsv(__DIR__ . '/../nobles.csv');

    $m = new MatchS(
      $args->getPlayers(),
      $args->getNumGames(),
      $args->getSeed(),
      $args->getSaveDir(),
      $args->getSaveInputs()
    );
    $m->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
