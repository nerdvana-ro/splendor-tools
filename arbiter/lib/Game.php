<?php

class Game {
  public int $n; // Numărul de jucători
  public int $curPlayer;
  public int $roundNo;
  public Board $board;
  public array $players;

  function __construct(Args $args) {
    $this->n = $args->getNumPlayers();
    for ($i = 0; $i < $this->n; $i++) {
      list($binary, $name) = $args->getPlayer($i);
      $this->players[] = new Player($binary, $name);
    }

    $this->board = new Board($this->n);
    $this->curPlayer = 0;
    $this->roundNo = 0;
  }

  // Ridică SplendorException pentru mutări invalide.
  private function validateAction(array $action): void {
  }

  private function takeTokens(array $colors, int $qty): void {
    $chipStr = [];
    foreach ($colors as $col) {
      $this->players[$this->curPlayer]->chips[$col] += $qty;
      $this->board->chips[$col] -= $qty;
      $chipStr[] = Str::chips($col, $qty);
    }

    $chipStr = implode(' ', $chipStr);
    Log::info('I-am dat jucătorului %d %s.', [ $this->curPlayer, $chipStr]);
  }

  private function executeAction(array $action): void {
    $type = array_shift($action);
    switch ($type) {
      case 1: $this->takeTokens(array_slice($action, 1), 1); break;
      case 2: $this->takeTokens([ $action[0] ], 2); break;
    }
  }

  private function playRound(): void {
    foreach ($this->players as $id => $p) {
      $state = $this->asInputFile();
      try {
        $action = $p->requestAction($state);
        $this->validateAction($action);
        $this->executeAction($action);
      } catch (SplendorException $e) {
        Log::warn('Jucătorul %d zice pas din cauza excepției: %s',
                  [ $this->curPlayer, $e->getMessage() ]);
      }
      print $state;
      $this->print();
      $this->curPlayer = ($this->curPlayer + 1) % $this->n;
    }
  }

  private function isOver(): bool {
    foreach ($this->players as $p) {
      if ($p->getScore() >= Config::ENDGAME_SCORE) {
        return true;
      }
    }
    if ($this->roundNo >= 2) {
      return true;
    }
    return false;
  }

  function run(): void {
    do {
      $this->playRound();
      $this->roundNo++;
    } while (!$this->isOver());
  }

  function asInputFile(): string {
    $l = [];
    $l[] = $this->n . ' ' . $this->curPlayer;
    $l[] = $this->roundNo;
    $l[] = $this->board->asInputFile();
    foreach ($this->players as $id => $p) {
      $l[] = $p->asInputFile($id == $this->curPlayer);
    }
    return implode("\n", $l) . "\n";
  }

  function print(): void {
    $pname = $this->players[$this->curPlayer]->name;
    Log::debug('================ Runda %d, jucător %d (%s)',
               [ $this->roundNo, $this->curPlayer, $pname]);
    $this->board->print();
    foreach ($this->players as $id => $player) {
      $player->print($id);
    }
  }
}
