<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/lib/Core.php';

main();

function main(): void {
  try {
    $args = new Args();
    $args->parse();

    Util::initRng($args->getSeed());

    $t = new Tournament34(
      $args->getPlayers(),
      $args->getTableSize(),
      $args->getNumGames(),
      $args->getSaveDir(),
      $args->getSaveInputs()
    );
    $t->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
