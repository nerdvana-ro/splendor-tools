<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/lib/Core.php';

main();

function main(): void {
  try {
    $args = new Args();
    $args->parse();

    Util::initRng($args->getSeed());

    $m = new MatchS(
      $args->getPlayers(),
      $args->getNumGames(),
      0,
      $args->getSaveDir(),
      $args->getSaveInputs()
    );
    $m->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
