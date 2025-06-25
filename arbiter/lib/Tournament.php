<?php

class Tournament {
  private int $numGames;
  private array $playerInfo;
  private string $saveDir;

  function __construct(Args $args) {
    $this->initRng($args->getSeed());
    $this->numGames = $args->getNumGames();
    $this->playerInfo = $args->getPlayers();
    $this->saveDir = $args->getSaveDir();
  }

  private function initRng(int $seed): void {
    if (!$seed) {
      $micros = microtime(true);
      $seed = $micros * 1_000_000 % 1_000_000_000;
    }
    Log::info('Inițializez RNG cu seed-ul %d.', [ $seed ]);
    srand($seed);
  }

  function run(): void {
    for ($g = 1; $g <= $this->numGames; $g++) {
      $game = new Game($this->playerInfo);
      $game->run();
      $results = $game->getResults();
      print json_encode($results) . "\n";

      if ($this->saveDir) {
        $fileName = $this->getSaveFile($g);
        $game->save($fileName);
      }
    }
  }

  function getSaveFile(int $gameNo): string {
    $fileName = sprintf(Config::SAVE_GAME_FILE, $gameNo);
    return $this->saveDir . '/' . $fileName;
  }

}
