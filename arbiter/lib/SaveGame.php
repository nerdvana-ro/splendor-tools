<?php

/**
 * O clasă care reține informațiile necesare pentru a salva o partidă.
 * Informațiile sînt structurate ca tabele asociative, nu ca obiecte.
 **/

class SaveGame {
  private array $playerNames; // strings
  private array $nobles;
  private array $decks;
  private array $rounds;

  function __construct(array $players, Board $board) {
    $this->playerNames = array_column($players, 'name');
    $this->addDecks($board->decks);
    $this->addNobles($board->nobles);
    $this->rounds = [];
  }

  function addNobles(array $nobleIds): void {
    $this->nobles = $nobleIds;
  }

  function addDecks(array $decks): void {
    $this->decks = [];
    foreach ($decks as $deck) {
      $cardIds = array_merge($deck->faceUp, $deck->faceDown);
      $this->decks[] = $cardIds;
    }
  }

  function addTurn(SaveGameTurn $turn): void {
    $complete = !count($this->rounds) ||
      (count(end($this->rounds)) == count($this->playerNames));

    if ($complete) {
      $this->rounds[] = [];
    }

    $this->rounds[count($this->rounds) - 1][] = $turn->asArray();
  }

  function asJson(): string {
    $data = [
      'players' => $this->playerNames,
      'decks' => $this->decks,
      'nobles' => $this->nobles,
      'rounds' => $this->rounds,
    ];
    return json_encode($data);
  }
}
