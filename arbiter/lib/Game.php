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

  // Returnează un vector caracteristic de jetoane luate.
  private function validateTakeThree(array &$action): array {
    $gains  = array_fill(0, Config::NUM_COLORS + 1, 0);
    $cnt = $this->shiftAndCheck($action, 0, 3);
    while ($cnt--) {
      $color = $this->shiftAndCheck($action, 0, Config::NUM_COLORS - 1);
      if ($gains[$color]) {
        throw new SplendorException("Ai cerut două jetoane de culoarea {$color}.");
      }
      if (!$this->board->chips[$color]) {
        throw new SplendorException("Pe masă nu există jetoane de culoarea {$color}.");
      }
      $gains[$color]++;
    }

    return $gains;
  }

  // Returnează un vector caracteristic de jetoane luate.
  private function validateTakeTwo(array &$action): array {
    $gains  = array_fill(0, Config::NUM_COLORS + 1, 0);
    $color = $this->shiftAndCheck($action, 0, Config::NUM_COLORS - 1);
    $avail = $this->board->chips[$color];
    if ($avail < Config::TAKE_TWO_LIMIT) {
      throw new SplendorException("Există doar {$avail} jetoane de culoarea {$color}.");
    }
    $gains[$color] += 2;
    return $gains;
  }

  // Returnează un vector caracteristic de jetoane luate.
  private function validateReserve(array &$action): array {
    $gains  = array_fill(0, Config::NUM_COLORS + 1, 0);
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

    if ($this->board->chips[Config::NUM_COLORS]) {
      $gains[Config::NUM_COLORS]++; // primește un aur
    }

    return $gains;
  }

  // Returnează un vector caracteristic de jetoane luate. Acesta este plin de
  // zerouri (cumpărarea nu produce jetoane).
  private function validateBuy(array &$action): array {
    $gains  = array_fill(0, Config::NUM_COLORS + 1, 0);
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

    return $gains;
  }

  private function validateReturnChips(array &$action, array $gains): void {
    $pl = $this->players[$this->curPlayer];
    $chips = [];
    $sum = 0;
    for ($col = 0; $col <= Config::NUM_COLORS; $col++) {
      $chips[] = $pl->chips[$col] + $gains[$col];
      $sum += $chips[$col];
    }

    $returned = [];

    while ($sum > 10) {
      $color = $this->shiftAndCheck($action, 0, Config::NUM_COLORS - 1);
      if (!$chips[$color]) {
        throw new SplendorException("Nu poți returna un jeton de culoarea {$color}.");
      }
      $chips[$color]--;
      $returned[] = $color;
      $sum--;
    }

    if (count($returned)) {
      $joined = implode(', ', $returned);
      $msg = "{$pl->name} a returnat jetoane de culorile {$joined}.";
      $this->saveGameTurn->arbiterMsg = $msg;
    }
  }

  // Ridică SplendorException pentru mutări invalide.
  private function validateAction(array $action): void {
    $type = $this->shiftAndCheck($action, 1, 4);
    switch ($type) {
      case self::ACTION_TAKE_THREE:
        $gains = $this->validateTakeThree($action); break;
      case self::ACTION_TAKE_TWO:
        $gains = $this->validateTakeTwo($action); break;
      case self::ACTION_RESERVE:
        $gains = $this->validateReserve($action); break;
      case self::ACTION_BUY:
        $gains = $this->validateBuy($action); break;
    }

    $this->validateReturnChips($action, $gains);

    if (count($action)) {
      throw new SplendorException("Cuvîntul {$action[0]} este în plus.");
    }
  }

  private function takeChips(array &$action, int $cnt, int $qty): void {
    $chipStr = [];
    $colors = [];
    while ($cnt--) {
      $col = array_shift($action);
      $this->players[$this->curPlayer]->chips[$col] += $qty;
      $this->board->chips[$col] -= $qty;
      $colors[] = $col;
      $chipStr[] = Str::chips($col, $qty);
    }

    $this->saveGameTurn->addTakeChipsTokens($colors, $qty);

    $chipStr = implode(' ', $chipStr);
    Log::info('I-am dat jucătorului %d %s.', [ $this->curPlayer, $chipStr]);
  }

  private function takeThreeChips(array &$action): void {
    $cnt = array_shift($action);
    $this->takeChips($action, $cnt, 1);
  }

  private function takeTwoChips(array &$action): void {
    $this->takeChips($action, 1, 2);
  }

  private function reserveCard(array &$action): void {
    $id = array_shift($action);
    $pl = $this->players[$this->curPlayer];
    $this->saveGameTurn->addReserveCardTokens($id);

    if ($id < 0) {
      $id = $this->board->drawCard(-$id);
      $pl->gainReserve($id, true);
    } else {
      $this->board->removeCard($id);
      $pl->gainReserve($id, false);
    }

    $gold = Config::NUM_COLORS;
    if ($this->board->chips[$gold]) {
      $this->board->chips[$gold]--;
      $pl->chips[$gold]++;
    }
  }

  // Returnează ID-ul nobilului primit sau 0 dacă jucătorul nu primește niciun
  // nobil.
  private function checkNobles(): int {
    $p = $this->players[$this->curPlayer];
    $id = $p->getVisitingNoble($this->board->nobles);
    if ($id) {
      $this->board->deleteNoble($id);
      $p->nobles[] = $id;
      $msg = "{$p->name} primește nobilul #{$id}.";
      $this->saveGameTurn->arbiterMsg = $msg;
    }
    return $id;
  }

  private function buyCard(array &$action): void {
    $id = array_shift($action);
    $pl = $this->players[$this->curPlayer];
    $chips = $pl->payForCard($id);
    $this->board->gainChips($chips);
    $this->board->removeCard($id);
    $pl->gainCard($id);
    $visitingNoble = $this->checkNobles();
    $this->saveGameTurn->addBuyCardTokens($id, $visitingNoble, $chips);
  }

  private function returnChips(array &$action): void {
    $pl = $this->players[$this->curPlayer];
    while (!empty($action)) {
      $color = array_shift($action);
      $pl->chips[$color]--;
      $this->board->chips[$color]++;
      $this->saveGameTurn->returns[] = $color;
    }
  }

  private function executeAction(array $action): void {
    $type = array_shift($action);
    switch ($type) {
      case self::ACTION_TAKE_THREE:
        $this->takeThreeChips($action); break;
      case self::ACTION_TAKE_TWO:
        $this->takeTwoChips($action); break;
      case self::ACTION_RESERVE:
        $this->reserveCard($action); break;
      case self::ACTION_BUY:
        $this->buyCard($action); break;
    }

    $this->returnChips($action);
  }

  private function playRound(): void {
    foreach ($this->players as $id => $p) {
      $this->print();
      $this->saveGameTurn = new SaveGameTurn();
      $state = $this->asInputFile();
      try {
        $output = $p->requestAction($state);
        $this->saveGameTurn->kibitzes = $output->kibitzes;
        $this->validateAction($output->tokens);
        $this->executeAction($output->tokens);
      } catch (SplendorException $e) {
        $pl = $this->players[$this->curPlayer];
        $msg = sprintf('Sar peste %s: %s', $pl->name, $e->getMessage());
        Log::warn($msg);
        $this->saveGameTurn->addTakeChipsTokens([], 0);
        $this->saveGameTurn->arbiterMsg = $msg;
      }
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
    do {
      $this->playRound();
      $this->roundNo++;
    } while (!$this->isOver());
    $this->print();
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
    if ($this->isOver()) {
      Log::info('================ Final (%d runde)', [ $this->roundNo ]);
    } else {
      Log::info('================ Runda %d, jucător %d (%s)',
                [ 1 + $this->roundNo, 1 + $this->curPlayer, $pname]);
    }
    $this->board->print();
    foreach ($this->players as $id => $player) {
      $player->print($id);
    }
  }
}
