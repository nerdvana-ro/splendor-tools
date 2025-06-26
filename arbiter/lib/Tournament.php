<?php

class Tournament {
  private int $numGames;
  private int $curGame;
  private array $playerInfo;
  private string $saveDir;
  private array $totals; // map de nume => scor

  function __construct(Args $args) {
    $this->initRng($args->getSeed());
    $this->numGames = $args->getNumGames();
    $this->playerInfo = $args->getPlayers();
    $this->saveDir = $args->getSaveDir();

    foreach ($this->playerInfo as $rec) {
      $this->totals[$rec['name']] = 0;
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
      $this->tallyResults($game->getResults());

      if ($this->saveDir) {
        $fileName = $this->getSaveFile();
        $game->save($fileName);
      }
      $this->rotatePlayerInfo();
    }
  }

  private function rotatePlayerInfo(): void {
    $head = array_shift($this->playerInfo);
    $this->playerInfo[] = $head;
  }

  private function getSaveFile(): string {
    $fileName = sprintf(Config::SAVE_GAME_FILE, $this->curGame);
    return $this->saveDir . '/' . $fileName;
  }

  private function tallyResults(array $results): void {
    $numWinners = 0;
    foreach ($results as $res) {
      $numWinners += $res->winner;
    }

    foreach ($results as $res) {
      if ($res->winner) {
        $this->totals[$res->name] += Config::GAME_POINTS / $numWinners;
      }
    }
    arsort($this->totals);

    $this->printResults($results);
    $this->printTotals();
  }

  private function printResults(array $results): void {
    Log::success('');
    Log::success('==== Rezultatele partidei %d', [ $this->curGame ]);
    Log::success('    nume                scor cărți  cîștigător');
    Log::success('    ------------------------------------------');
    foreach ($results as $r) {
      $mark = $r->winner ? '✅' : ' ';
      Log::success('    %-20s %2d   %2d        %s',
                [ $r->name, $r->score, $r->cards, $mark ]);
    }
    Log::success('');
  }

  private function printTotals(): void {
    Log::success('');
    Log::success('==== Totaluri după %d partide', [ $this->curGame ]);
    Log::success('    nume                puncte');
    Log::success('    --------------------------');
    foreach ($this->totals as $name => $points) {
      Log::success('    %-20s  %2d', [ $name, $points ]);
    }
    Log::success('');
  }

}
