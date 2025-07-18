<?php

class Tournament34 {
  private array $playerInfo;
  private int $tableSize;
  private int $numGames;
  private string $saveDir;
  private bool $saveInputs;

  private array $matches; // Vector de vectori de lungime $tableSize.
  private int $matchNo;
  private int $matchSeed;
  private MatchResult $results;

  function __construct(array $playerInfo, int $tableSize, int $numGames,
                       string $saveDir, bool $saveInputs) {
    if (($tableSize <= 2) || ($tableSize > Config::MAX_PLAYERS)) {
      $fmt = "Pentru acest tip de turneu, --table-size trebuie să fie cuprins între %d și %d.";
      $msg = sprintf($fmt, 3, Config::MAX_PLAYERS);
      throw new SplendorException($msg);
    }
    $this->playerInfo = $playerInfo;
    $this->numGames = $numGames;
    $this->tableSize = $tableSize;
    $this->saveDir = $saveDir;
    $this->saveInputs = $saveInputs;

    $this->matches = [];
    $this->results = new MatchResult();
  }

  // Pentru meciul curent.
  private function getMatchDir(): string {
    $ids = $this->matches[$this->matchNo];
    $names = [];
    foreach ($ids as $id) {
      $names[] = $this->playerInfo[$id]['name'];
    }
    $nameStr = implode('---', $names);

    return sprintf('%s/%s', $this->saveDir, $nameStr);
  }

  function run(): void {
    $this->genMatches();
    $this->runMatches();
  }

  private function genMatches(): void {
    $this->genComb(0, []);
    $this->shuffleMatches();
  }

  // Combinări de count($playerInfo) luate cîte $tableSize.
  private function genComb(int $k, array $work): void {
    if ($k == $this->tableSize) {
      $this->matches[] = $work;
    }
    $start = $k ? ($work[$k - 1] + 1) : 0;
    for ($i = $start; $i < count($this->playerInfo); $i++) {
      $work[$k] = $i;
      $this->genComb($k + 1, $work);
    }
  }

  private function shuffleMatches(): void {
    foreach ($this->matches as &$match) {
      shuffle($match);
    }
    shuffle($this->matches);
  }

  private function runMatches(): void {
    for ($t = 0; $t < count($this->matches); $t++) {
      $this->matchNo = $t;
      $this->matchSeed = rand(100_000_000, 999_999_999);
      $this->matchBanner();
      $this->runMatch();
      $this->report();
    }
  }

  private function matchBanner(): void {
    $ids = $this->matches[$this->matchNo];
    $names = [];
    foreach ($ids as $id) {
      $names[] = $this->playerInfo[$id]['name'];
    }
    $nameStr = implode(', ', $names);
    $msg = sprintf("  Meciul %d / %d: %s  ",
                   $this->matchNo + 1, count($this->matches), $nameStr);
    Log::successBanner($msg);
  }

  private function runMatch(): void {
    $ids = $this->matches[$this->matchNo];
    $info = [];
    foreach ($ids as $id) {
      $info[] = $this->playerInfo[$id];
    }

    $saveDir = $this->getMatchDir();
    @mkdir($saveDir, 0777, true); // recursive

    $m = new MatchS($info,
                    $this->numGames,
                    $this->matchSeed,
                    $saveDir,
                    $this->saveInputs);
    $m->run();

    $this->results->add($m->getResults());
  }

  private function report(): void {
    $m = $this->matchNo + 1;
    Log::success('');
    Log::success("================ Situația după meciul {$m}");
    $this->results->print();
    Log::success('');
  }
}
