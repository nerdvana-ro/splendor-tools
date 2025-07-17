<?php

require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/lib/Core.php';

main();

function main(): void {
  try {
    $args = new Args();
    $args->parse();

    $t = new Tournament2(
      $args->getPlayers(),
      $args->getNumGames(),
      $args->getSaveDir(),
      $args->getSaveInputs()
    );
    $t->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
