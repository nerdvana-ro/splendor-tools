<?php

class MatchResult {
  public array $map; // map de nume ==> [ scor, prestigiu ]

  function __construct() {
    $this->map = [];
  }

  function grant(string $name, float $score, int $prestige): void {
    if (isset($this->map[$name])) {
      $this->map[$name][0] += $score;
      $this->map[$name][1] += $prestige;
    } else {
      $this->map[$name] = [ $score, $prestige ];
    }
  }

  function getScore(string $name): float {
    return $this->map[$name][0];
  }

  function getPrestige(string $name): int {
    return $this->map[$name][1];
  }

  function add(MatchResult $other): void {
    foreach ($other->map as $name => $rec) {
      $this->grant($name, $rec[0], $rec[1]);
    }
  }

  private function sort() {
    uasort($this->map, function($a, $b) {
      if ($a[0] != $b[0]) {
        return $b[0] - $a[0];
      }
      return $b[1] - $a[1];
    });
  }

  function print(int $numGames): void {
    self::sort();
    Log::success('    nume                puncte   prestigiu    prestigiu mediu');
    Log::success('    ----------------------------------------------------------');
    foreach ($this->map as $name => $rec) {
      Log::success('    %-20s %5.2f     %4d            %5.2f',
                   [ $name, $rec[0], $rec[1], $rec[1] / $numGames ]);
    }
  }
}
