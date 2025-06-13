<?php

// Încarcă la cerere clasele din directorul lib/.
spl_autoload_register(function($className) {
  $fileName = sprintf('%s/lib/%s.php', __DIR__, $className);
  if (file_exists($fileName)) {
    require_once $fileName;
  }
});

require_once __DIR__ . '/Config.php';

main();

function main(): void {
  try {
    $args = new Args();
    $args->parse();
    $game = new Game($args);
    $game->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
