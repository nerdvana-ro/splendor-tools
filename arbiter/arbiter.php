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
    Card::loadCsv(__DIR__ . '/../cards.csv');
    Noble::loadCsv(__DIR__ . '/../nobles.csv');

    $t = new Tournament($args);
    $t->run();
  } catch (SplendorException $e) {
    Log::fatal($e->getMessage());
  }
}
