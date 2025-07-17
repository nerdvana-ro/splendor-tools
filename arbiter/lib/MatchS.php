<?php

class MatchS { // Deoarece „match” este cuvînt rezervat în PHP. Boo!
  private int $numGames;
  private int $curGame;
  private array $playerInfo;
  private string $saveDir;
  private bool $saveInputs;
  private MatchResult $totals;

  function __construct(array $playerInfo, int $numGames, int $seed,
                       string $saveDir, bool $saveInputs) {
    $this->playerInfo = $playerInfo;
    $this->numGames = $numGames;
    $this->initRng($seed);
    $this->saveDir = $saveDir;
    $this->saveInputs = $saveInputs;
    $this->totals = new MatchResult();

    foreach ($this->playerInfo as $rec) {
      $this->totals->addPlayer($rec['name']);
    }
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
    for ($this->curGame = 1; $this->curGame <= $this->numGames; $this->curGame++) {
      $game = new Game($this->playerInfo);
      $game->run();
      $this->saveGameData($game);
      $this->tallyResults($game->getResults(), $game->getNumRounds());
      $this->rotatePlayerInfo();
    }
  }

  private function rotatePlayerInfo(): void {
    $head = array_shift($this->playerInfo);
    $this->playerInfo[] = $head;
  }

  private function saveGameData(Game &$game): void {
    if ($this->saveDir) {
      $fileName = $this->getSaveFile();
      Log::debug('Salvez partida în %s.', [ $fileName ]);
      $game->save($fileName);
      if ($this->saveInputs) {
        $this->saveInputs($game->getInputs());
      }
    }
  }

  private function getSaveFile(): string {
    $fileName = sprintf(Config::SAVE_GAME_FILE, $this->curGame);
    return $this->saveDir . '/' . $fileName;
  }

  private function getSaveInputsDir(): string {
    $rel = sprintf(Config::SAVE_INPUT_DIR, $this->curGame);
    return $this->saveDir . '/' . $rel . '/';
  }

  private function getSaveInputsFile(int $round, int $player): string {
    $fileName = sprintf(Config::SAVE_INPUT_FILE, $round, $player);
    return $this->getSaveInputsDir() . $fileName;
  }

  private function saveInputs(array $inputs): void {
    $dir = $this->getSaveInputsDir();
    exec("rm -rf $dir");
    mkdir($dir);
    Log::debug('Salvez datele de intrare în %s.', [ $dir ]);
    foreach ($inputs as $round => $data) {
      foreach ($data as $player => $input) {
        $fileName = $this->getSaveInputsFile($round, $player);
        file_put_contents($fileName, $input);
      }
    }
  }

  private function tallyResults(array $results, int $numRounds): void {
    $numWinners = 0;
    foreach ($results as $res) {
      $numWinners += $res->winner;
    }

    foreach ($results as $res) {
      $score = ($res->winner) ? (1.0 / $numWinners) : 0;
      $this->totals->grant($res->name, $score, $res->score);
    }
    $this->totals->sort();

    $this->printResults($results, $numRounds);
    $this->printTotals();
  }

  private function printResults(array $results, int $numRounds): void {
    Log::success('');
    Log::success('==== Rezultatele partidei %d (%d runde)',
                 [ $this->curGame, $numRounds ]);
    Log::success('    nume                scor cărți  cîștigător    timp total / maxim');
    Log::success('    ----------------------------------------------------------------');
    foreach ($results as $r) {
      $mark = $r->winner ? '✅' : '  ';
      Log::success('    %-20s %2d   %2d        %s          %0.3f / %0.3f',
                   [ $r->name, $r->score, $r->cards, $mark,
                     $r->sumTimes / 1000, $r->maxTime / 1000 ]);
    }
    Log::success('');
  }

  private function printTotals(): void {
    Log::success('');
    Log::success('==== Totaluri după %d partide', [ $this->curGame ]);
    Log::success('    nume                puncte   prestigiu');
    Log::success('    --------------------------------------');
    foreach (array_keys($this->totals->map) as $name) {
      $score = $this->totals->getScore($name);
      $prestige = $this->totals->getPrestige($name);
      Log::success('    %-20s %5.2f     %4d', [ $name, $score, $prestige ]);
    }
    Log::success('');
  }

}
