<?php

/**
 * Stochează informații despre o singură tură pentru a o salva într-un
 * SaveGame.
 **/

class SaveGameTurn {
  public array $tokens; // un vector de întregi care codifică mutarea
  public array $returns; // culorile jetoanelor returnate
  public array $kibitzes; // un vector de stringuri chibițate de agent
  public string $arbiterMsg; // un mesaj adăugat de arbitru pentru mutări incorecte

  function __construct() {
    $this->tokens = [];
    $this->returns = [];
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

  function addReserveCardTokens(int $id, bool $hidden, bool $gainGold): void {
    $this->tokens = [
      Game::ACTION_RESERVE, $hidden, $gainGold,
    ];
  }

  function addBuyCardTokens(int $id, int $nobleId, array $chips): void {
    $this->tokens = array_merge(
      [ Game::ACTION_BUY, $id, $nobleId ],
      $chips
    );
  }

  function asArray(): array {
    return [
      'tokens' => $this->tokens,
      'returns' => $this->returns,
      'kibitzes' => $this->kibitzes,
      'arbiterMsg' => $this->arbiterMsg,
    ];
  }
}
