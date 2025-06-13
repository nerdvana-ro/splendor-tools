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

  function run(): void {
    $this->print();
  }

  function print(): void {
    $pname = $this->players[$this->curPlayer]->name;
    Log::debug('======== Runda %d, jucător %d (%s)',
               [ $this->roundNo, $this->curPlayer, $pname]);
    $this->board->print();
    foreach ($this->players as $id => $player) {
      $player->print($id);
    }
  }
}
