<?php

class Tournament2 {
  private array $playerInfo;
  private int $numGames;
  private string $saveDir;
  private bool $saveInputs;

  private array $roster; // indicii jucătorilor de împerecheat
  private int $roundNo;
  private int $roundSeed;
  private MatchResult $results;

  function __construct(array $playerInfo, int $numGames,
                       string $saveDir, bool $saveInputs) {
    $this->playerInfo = $playerInfo;
    $this->numGames = $numGames;
    $this->saveDir = $saveDir;
    $this->saveInputs = $saveInputs;

    $this->results = new MatchResult();
  }

  private function getMatchDir(int $p1, int $p2): string {
    return sprintf('%s/round-%02d/%s---%s',
                   $this->saveDir,
                   $this->roundNo,
                   $this->playerInfo[$p1]['name'],
                   $this->playerInfo[$p2]['name']);
  }

  function run(): void {
    $this->makeRoster();
    $this->runRounds();
  }

  private function makeRoster(): void {
    $n = count($this->playerInfo);
    $size = $n + $n % 2;
    $this->roster = range(0, $size - 1);
  }

  private function runRounds(): void {
    $numRounds = count($this->roster) - 1;
    for ($r = 1; $r <= $numRounds; $r++) {
      $msg = "                Runda {$r} / {$numRounds}               ";
      Log::successBanner($msg);
      $this->roundNo = $r;
      $this->roundSeed = rand(100_000_000, 999_999_999);
      $this->runRound();
      $this->rotateRoster();
      $this->report();
    }
  }

  private function runRound(): void {
    $half = count($this->roster) / 2;
    for ($i = 0; $i < $half; $i++) {
      $p1 = $this->roster[$i];
      $p2 = $this->roster[$i + $half];
      if ($this->roundNo % 2 == 0) {
        $tmp = $p1; $p1 = $p2; $p2 = $tmp;
      }
      if ($p1 == count($this->playerInfo)) {
        Log::info("Jucătorul {$p2} stă pe bară.");
      } else if ($p2 == count($this->playerInfo)) {
        Log::info("Jucătorul {$p1} stă pe bară.");
      } else {
        $this->runMatch($p1, $p2);
      }
    }
  }

  private function runMatch(int $p1, int $p2): void {
    $this->matchBanner($p1, $p2);
    $info = [
      $this->playerInfo[$p1],
      $this->playerInfo[$p2],
    ];

    $saveDir = $this->getMatchDir($p1, $p2);
    @mkdir($saveDir, 0777, true); // recursive
    $m = new MatchS($info,
                    $this->numGames,
                    $this->roundSeed,
                    $saveDir,
                    $this->saveInputs);
    $m->run();

    $this->results->add($m->getResults());
  }

  private function matchBanner(int $p1, int $p2): void {
    $msg = sprintf("     Meciul %s - %s     ",
                   $this->playerInfo[$p1]['name'],
                   $this->playerInfo[$p2]['name']);
    Log::successBanner($msg);
  }

  private function rotateRoster(): void {
    // 0 1 2 3 4   5 6 7 8 9 ===> 0 5 1 2 3   6 7 8 9 4
    $half = count($this->roster) / 2;
    $this->roster = array_merge(
      [ $this->roster[0] ],
      [ $this->roster[$half] ],
      array_slice($this->roster, 1, $half - 2),
      array_slice($this->roster, $half + 1, $half - 1),
      [ $this->roster[$half - 1] ]
    );
  }

  private function report(): void {
    Log::success('');
    Log::success("================ Situația după runda {$this->roundNo}");
    $this->results->print($this->roundNo * $this->numGames);
    Log::success('');
  }
}
