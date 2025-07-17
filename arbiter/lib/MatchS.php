<?php

class MatchS { // Deoarece „match” este cuvînt rezervat în PHP. Boo!
  private array $playerInfo;
  private int $numGames;
  private int $seed;
  private string $saveDir;
  private bool $saveInputs;

  private int $curGame;
  private MatchResult $results;

  function __construct(array $playerInfo, int $numGames, int $seed,
                       string $saveDir, bool $saveInputs) {
    $this->playerInfo = $playerInfo;
    $this->numGames = $numGames;
    $this->seed = $seed;
    $this->saveDir = $saveDir;
    $this->saveInputs = $saveInputs;
    $this->results = new MatchResult();
  }

  function run(): void {
    for ($this->curGame = 1; $this->curGame <= $this->numGames; $this->curGame++) {
      $game = new Game($this->playerInfo, $this->seed);
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

  function getResults(): MatchResult {
    return $this->results;
  }

  private function tallyResults(array $results, int $numRounds): void {
    $numWinners = 0;
    foreach ($results as $res) {
      $numWinners += $res->winner;
    }

    foreach ($results as $res) {
      $score = ($res->winner) ? (1.0 / $numWinners) : 0;
      $this->results->grant($res->name, $score, $res->score);
    }

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
    $this->results->print($this->curGame);
    Log::success('');
  }

}
