<?php

class Game {
  const ACTION_TAKE_THREE = 1;
  const ACTION_TAKE_TWO = 2;
  const ACTION_RESERVE = 3;
  const ACTION_BUY = 4;

  private int $n; // Numărul de jucători
  private int $curPlayer;
  private int $roundNo;
  private Board $board;
  private array $players;
  private SaveGame $saveGame;
  private SaveGameTurn $saveGameTurn;

  function __construct(Args $args) {
    $this->initRng($args->getSeed());

    $this->n = $args->getNumPlayers();
    for ($i = 0; $i < $this->n; $i++) {
      list($binary, $name) = $args->getPlayer($i);
      $this->players[] = new Player($binary, $name);
    }

    $this->board = new Board($this->n);
    $this->curPlayer = 0;
    $this->roundNo = 0;
    $this->saveGame = new SaveGame($this->players, $this->board);
  }

  private function initRng(int $seed): void {
    if (!$seed) {
      $micros = microtime(true);
      $seed = $micros * 1_000_000 % 1_000_000_000;
    }
    Log::info('Inițializez RNG cu seed-ul %d.', [ $seed ]);
    srand($seed);
  }

  private function shiftAndCheck(array &$v, int $lo, int $hi): int {
    if (empty($v)) {
      throw new SplendorException('Acțiunea este prea scurtă.');
    }

    $first = array_shift($v);
    if (filter_var($first, FILTER_VALIDATE_INT) === false) {
      throw new SplendorException("Cuvîntul [$first] nu este un întreg.");
    }

    if (($first < $lo) || ($first > $hi)) {
      throw new SplendorException("Valoarea $first nu este cuprinsă între $lo și $hi.");
    }

    return $first;
  }

  private function validateTakeThree(array &$action): void {
    $cnt = $this->shiftAndCheck($action, 0, 3);
    $taken = [];
    while ($cnt--) {
      $color = $this->shiftAndCheck($action, 0, Config::NUM_COLORS - 1);
      if (isset($taken[$color])) {
        throw new SplendorException("Ai cerut două jetoane de culoarea {$color}.");
      }
      $taken[$color] = true;
      if (!$this->board->chips[$color]) {
        throw new SplendorException("Pe masă nu există jetoane de culoarea {$color}.");
      }
    }
  }

  private function validateTakeTwo(array &$action): void {
    $color = $this->shiftAndCheck($action, 0, Config::NUM_COLORS - 1);
    $avail = $this->board->chips[$color];
    if ($avail < Config::TAKE_TWO_LIMIT) {
      throw new SplendorException("Există doar {$avail} jetoane de culoarea {$color}.");
    }
  }

  private function validateReserve(array &$action): void {
    $id = $this->shiftAndCheck($action, -3, Config::NUM_CARDS);

    $numRes = count($this->players[$this->curPlayer]->reserve);
    if ($numRes == Config::MAX_RESERVED_CARDS) {
      throw new SplendorException("Ai deja {$numRes} cărți rezervate.");
    }

    if ($id == 0) {
      throw new SplendorException('ID-ul 0 este ilegal.');
    } else if ($id < 0) {
      $level = -$id;
      if (empty($this->board->decks[$level - 1]->faceDown)) {
        throw new SplendorException("Pachetul de nivel {$level} este gol.");
      }
    } else {
      if (!$this->board->isFaceUp($id)) {
        throw new SplendorException("Cartea #{$id} nu este pe masă.");
      }
    }
  }

  private function validateBuy(array &$action): void {
    $id = $this->shiftAndCheck($action, 1, Config::NUM_CARDS);
    $pl = $this->players[$this->curPlayer];
    $isReserved = $pl->hasInReserve($id);
    $isFaceUp = $this->board->isFaceUp($id);

    if (!$isReserved && !$isFaceUp) {
      throw new SplendorException("Cartea #{$id} nu este nici pe masă nici rezervată.");
    }

    if (!$pl->canBuyCard($id)) {
      throw new SplendorException("Nu îți permiți cartea #{$id}.");
    }
  }

  // Ridică SplendorException pentru mutări invalide.
  private function validateAction(array $action): void {
    $type = $this->shiftAndCheck($action, 1, 4);
    switch ($type) {
      case self::ACTION_TAKE_THREE:
        $this->validateTakeThree($action); break;
      case self::ACTION_TAKE_TWO:
        $this->validateTakeTwo($action); break;
      case self::ACTION_RESERVE:
        $this->validateReserve($action); break;
      case self::ACTION_BUY:
        $this->validateBuy($action); break;
    }

    if (count($action)) {
      throw new SplendorException("Cuvîntul {$action[0]} este în plus.");
    }
  }

  private function takeChips(array $colors, int $qty): void {
    $chipStr = [];
    foreach ($colors as $col) {
      $this->players[$this->curPlayer]->chips[$col] += $qty;
      $this->board->chips[$col] -= $qty;
      $chipStr[] = Str::chips($col, $qty);
    }

    $this->saveGameTurn->addTakeChipsTokens($colors, $qty);

    $chipStr = implode(' ', $chipStr);
    Log::info('I-am dat jucătorului %d %s.', [ $this->curPlayer, $chipStr]);
  }

  private function reserveCard(int $id): void {
    $pl = $this->players[$this->curPlayer];

    if ($id < 0) {
      $id = $this->board->drawCard(-$id);
      $hidden = true;
    } else {
      $this->board->removeCard($id);
      $hidden = false;
    }

    $pl->gainReserve($id, $hidden);

    $gold = Config::NUM_COLORS;
    $gainGold = $this->board->chips[$gold];
    if ($gainGold) {
      $this->board->chips[$gold]--;
      $pl->chips[$gold]++;
    }

    $this->saveGameTurn->addReserveCardTokens($id, $hidden, $gainGold);
  }

  private function buyCard(int $id): void {
    $pl = $this->players[$this->curPlayer];
    $chips = $pl->payForCard($id);
    $this->board->gainChips($chips);
    $this->board->removeCard($id);
    $pl->gainCard($id);
    $this->saveGameTurn->addBuyCardTokens($id, $chips);
  }

  private function executeAction(array $action): void {
    $type = array_shift($action);
    switch ($type) {
      case self::ACTION_TAKE_THREE:
        $this->takeChips(array_slice($action, 1), 1); break;
      case self::ACTION_TAKE_TWO:
        $this->takeChips([ $action[0] ], 2); break;
      case self::ACTION_RESERVE:
        $this->reserveCard($action[0]); break;
      case self::ACTION_BUY:
        $this->buyCard($action[0]); break;
    }
  }

  private function playRound(): void {
    foreach ($this->players as $id => $p) {
      $this->saveGameTurn = new SaveGameTurn();
      $state = $this->asInputFile();
      try {
        $output = $p->requestAction($state);
        $this->saveGameTurn->kibitzes = $output->kibitzes;
        $this->validateAction($output->tokens);
        $this->executeAction($output->tokens);
      } catch (SplendorException $e) {
        $msg = sprintf('Jucătorul %d zice pas din cauza erorii: %s',
                       $this->curPlayer, $e->getMessage());
        Log::warn($msg);
        $this->saveGameTurn->addTakeChipsTokens([], 0);
        $this->saveGameTurn->arbiterMsg = $msg;
      }
      $this->print();
      $this->curPlayer = ($this->curPlayer + 1) % $this->n;
      $this->saveGame->addTurn($this->saveGameTurn);
    }
  }

  private function isOver(): bool {
    foreach ($this->players as $p) {
      if ($p->getScore() >= Config::ENDGAME_SCORE) {
        return true;
      }
    }
    if ($this->roundNo >= Config::MAX_ROUNDS) {
      return true;
    }
    return false;
  }

  function run(): void {
    $this->print();
    do {
      $this->playRound();
      $this->roundNo++;
    } while (!$this->isOver());
  }

  function save(string $saveGameFile): void {
    $json = $this->saveGame->asJson() . "\n";
    file_put_contents($saveGameFile, $json);
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
    Log::info('================ Runda %d, jucător %d (%s)',
              [ $this->roundNo, $this->curPlayer, $pname]);
    $this->board->print();
    foreach ($this->players as $id => $player) {
      $player->print($id);
    }
  }
}
