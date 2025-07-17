<?php

class MatchResult {
  public array $map; // map de nume ==> [ scor, prestigiu ]

  function __construct() {
    $this->map = [];
  }

  function addPlayer(string $name): void {
    $this->map[$name] = [ 0.0, 0 ];
  }

  function grant(string $name, float $score, int $prestige): void {
    $this->map[$name][0] += $score;
    $this->map[$name][1] += $prestige;
  }

  function sort() {
    uasort($this->map, function($a, $b) {
      if ($a[0] != $b[0]) {
        return $b[0] - $a[0];
      }
      return $b[1] - $a[1];
    });
  }

  function getScore(string $name): float {
    return $this->map[$name][0];
  }

  function getPrestige(string $name): int {
    return $this->map[$name][1];
  }
}
