<?php

/**
 * Stochează informații despre o singură tură pentru a o salva într-un
 * SaveGame.
 **/

class SaveGameTurn {
  public array $tokens;      // un vector de întregi care codifică mutarea
  public array $returns;     // culorile jetoanelor returnate
  public int $nobleId;       // nobilul primit dacă există, 0 altfel
  public array $kibitzes;    // un vector de stringuri chibițate de agent
  public string $arbiterMsg; // un mesaj adăugat de arbitru pentru mutări incorecte

  function __construct() {
    $this->tokens = [];
    $this->returns = [];
    $this->nobleId = 0;
    $this->kibitzes = [];
    $this->arbiterMsg = '';
  }

  function addTakeChipsTokens(array $colors, int $qty): void {
    $chips = array_fill(0, Config::NUM_COLORS, 0);
    foreach ($colors as $col) {
      $chips[$col] += $qty;
    }
    $action = ($qty == 2) ? Game::ACTION_TAKE_TWO : Game::ACTION_TAKE_THREE;
    $this->tokens = array_merge([ $action ], $chips);
  }

  function addReserveCardTokens(int $id): void {
    $this->tokens = [ Game::ACTION_RESERVE, $id ];
  }

  function addBuyCardTokens(int $id, array $chips): void {
    $this->tokens = array_merge(
      [ Game::ACTION_BUY, $id ],
      $chips
    );
  }

  function asArray(): array {
    return [
      'tokens' => $this->tokens,
      'returns' => $this->returns,
      'nobleId' => $this->nobleId,
      'kibitzes' => $this->kibitzes,
      'arbiterMsg' => $this->arbiterMsg,
    ];
  }
}
