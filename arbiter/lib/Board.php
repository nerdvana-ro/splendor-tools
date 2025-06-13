<?php

class Board {
  public array $chips;
  public array $decks;
  public array $nobles;

  function __construct(int $numPlayers) {
    $this->chips = array_fill(0, 5, Config::SUPPLY[$numPlayers]['chips']);
    $this->chips[] = Config::NUM_GOLD;

    $this->decks = [
      new Deck(Config::FIRST_LEVEL_1_CARD, Config::LAST_LEVEL_1_CARD),
      new Deck(Config::FIRST_LEVEL_2_CARD, Config::LAST_LEVEL_2_CARD),
      new Deck(Config::FIRST_LEVEL_3_CARD, Config::LAST_LEVEL_3_CARD),
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
}
