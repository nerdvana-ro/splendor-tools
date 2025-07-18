<?php

class MatchResult {
  public array $map; // map de nume ==> [ scor, prestigiu, număr de partide ]

  function __construct() {
    $this->map = [];
  }

  function grant(string $name, float $score, int $prestige, int $numGames): void {
    if (isset($this->map[$name])) {
      $this->map[$name]['score'] += $score;
      $this->map[$name]['prestige'] += $prestige;
      $this->map[$name]['numGames'] += $numGames;
    } else {
      $this->map[$name] = [
        'score' => $score,
        'prestige' => $prestige,
        'numGames' => $numGames,
      ];
    }
  }

  function add(MatchResult $other): void {
    foreach ($other->map as $name => $rec) {
      $this->grant($name, $rec['score'], $rec['prestige'], $rec['numGames']);
    }
  }

  private function sort() {
    uasort($this->map, function($a, $b) {
      if ($a['score'] != $b['score']) {
        return $b['score'] - $a['score'];
      }
      if ($a['prestige'] != $b['prestige']) {
        return $b['prestige'] - $a['prestige'];
      }
      return $a['numGames'] - $b['numGames'];
    });
  }

  function print(): void {
    self::sort();
    Log::success('    nume                partide puncte   prestigiu    prestigiu mediu');
    Log::success('    -----------------------------------------------------------------');
    foreach ($this->map as $name => $rec) {
      Log::success('    %-20s   %2d    %5.2f     %4d            %5.2f',
                   [ $name,
                     $rec['numGames'],
                     $rec['score'],
                     $rec['prestige'],
                     $rec['prestige'] / $rec['numGames']
                   ]);
    }
  }
}
