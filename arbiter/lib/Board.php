<?php

class Board {
  public array $chips;
  public array $decks;
  public array $nobles;

  function __construct(int $numPlayers) {
    $this->chips = array_fill(0, Config::NUM_COLORS, Config::SUPPLY[$numPlayers]['chips']);
    $this->chips[] = Config::NUM_GOLD;

    $r = Config::CARD_LEVEL_RANGES;
    $this->decks = [
      new Deck($r[1][0], $r[1][1]),
      new Deck($r[2][0], $r[2][1]),
      new Deck($r[3][0], $r[3][1]),
    ];

    $noblesInPlay = Config::SUPPLY[$numPlayers]['nobles'];
    $this->drawNobles($noblesInPlay);
  }

  function drawNobles(int $qty): void {
    $this->nobles = [];
    while ($qty--) {
      do {
        $x = rand(1, Config::NUM_NOBLES);
      } while (in_array($x, $this->nobles));
      $this->nobles[] = $x;
    }
    $str = implode(' ', $this->nobles);
    Log::info('Am generat nobilii %s.', [ $str ]);
  }

  function asInputFile(): string {
    $l = [];
    $l[] = implode(' ', $this->chips);
    foreach ($this->decks as $d) {
      $l[] = $d->asInputFile();
    }
    $l[] = trim(count($this->nobles) . ' ' . implode(' ', $this->nobles));
    return implode("\n", $l);
  }

  function print(): void {
    $this->printNobles();
    $this->printCards();
    $this->printChips();
  }

  function printNobles(): void {
    Log::debug("======== Nobili:");
    foreach ($this->nobles as $id) {
      $noble = Noble::get($id);
      $str = '';
      for ($col = 0; $col < Config::NUM_COLORS; $col++) {
        if ($noble->cost[$col]) {
          $str .= Str::chips($col, $noble->cost[$col]);
          $str .= ' ';
        }
      }
      Log::debug("    [#%2d] %s", [ $id, $str ]);
    }
  }

  function printCards(): void {
    Log::debug('======== Cărți:');
    Log::debug('      ID  puncte culoare  cost');
    for ($level = Config::CARD_LEVELS - 1; $level >= 0; $level--) {
      $this->decks[$level]->print();
      if ($level) {
        Log::debug('    ' . str_repeat('-', 40));
      }
    }
  }

  function printChips(): void {
    Log::debug("======== Jetoane:");
    $str = '    ';
    for ($col = 0; $col <= Config::NUM_COLORS; $col++) {
      if ($this->chips[$col]) {
        $str .= Str::chips($col, $this->chips[$col]);
        $str .= ' ';
      }
    }
    Log::debug($str);
  }

}
