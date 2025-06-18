<?php

class Player {
  private string $binary;
  public string $name;

  public array $chips;
  public array $cards;
  public array $cardColors;
  public array $reserve; // vector de ReservedCard
  public array $nobles;

  function __construct(string $binary, string $name) {
    $this->binary = ($binary == 'human') ? $binary : realpath($binary);
    $this->name = $name;
    Log::info('Am adăugat jucătorul %s cu binarul %s.', [ $name, $binary ]);

    $this->chips = array_fill(0, Config::NUM_COLORS + 1, 0); // inclusiv 0 aur
    $this->cards = [];
    $this->cardColors = array_fill(0, Config::NUM_COLORS, 0);
    $this->reserve = [];
    $this->nobles = [];
  }

  function getScore(): int {
    $score = count($this->nobles) * Config::NOBLE_POINTS;
    foreach ($this->cards as $id) {
      $score += Card::get($id)->points;
    }
    return $score;
  }

  function payForCard(int $id): array {
    $card = Card::get($id);
    $chips = array_fill(0, Config::NUM_COLORS + 1, 0);

    for ($i = 0; $i < Config::NUM_COLORS; $i++) {
      $cost = max($card->cost[$i] - $this->cardColors[$i], 0);
      $actual = min($cost, $this->chips[$i]);
      $jokers = $cost - $actual;
      $this->chips[$i] -= $cost;
      $chips[$i] += $cost;
      $this->chips[Config::NUM_COLORS] -= $jokers;
      $chips[Config::NUM_COLORS] += $jokers;
    }

    return $chips;
  }

  function gainCard(int $id) {
    $card = Card::get($id);
    $this->cards[] = $id;
    $this->cardColors[$card->color]++;

    // Dacă provenea din rezervă, șterge-o.
    $this->reserve = array_diff($this->reserve, [ $id ]);
  }

  function requestAction(string $gameState): array {
    Log::info('Aștept o acțiune de la %s', [ $this->name ]);
    $tokens = Interactor::interact($this->binary, $gameState);
    return $tokens;
  }

  // $reveal: Arătăm sau nu cărțile ascunse?
  function asInputFile(bool $reveal): string {
    $l = [];
    $l[] = implode(' ', $this->chips);
    $l[] = trim(count($this->cards) . ' ' . implode(' ', $this->cards));
    $l[] = $this->getReserveAsInputFile($reveal);
    $l[] = trim(count($this->nobles) . ' ' . implode(' ', $this->nobles));
    return implode("\n", $l);
  }

  function getReserveAsInputFile(bool $reveal): string {
    $arr = [];
    foreach ($this->reserve as $r) {
      if ($reveal || !$r->hidden) {
        $arr[] = $id;
      } else {
        $arr[] = -Card::get($id)->level;
      }
    }
    return trim(count($arr) . ' ' . implode(' ', $arr));
  }

  function getCardQuantities(): array {
    $res = array_fill(0, Config::NUM_COLORS + 1, 0);

    foreach ($this->cards as $id) {
      $color = Card::get($id)->color;
      $res[$color]++;
    }

    return $res;
  }

  function print(int $myId): void {
    $cardQty = $this->getCardQuantities();

    Log::debug('======== Jucătorul %d (%s)', [ $myId, $this->name ]);
    Log::debug('    Scor: %d', [ $this->getScore() ]);
    $parts = [];
    for ($col = 0; $col <= Config::NUM_COLORS; $col++) {
      if ($cardQty[$col] || $this->chips[$col]) {
        $parts[] = Str::block($col, $cardQty[$col]) .
          Str::chips($col, $this->chips[$col]);
      }
    }
    Log::debug('    ' . implode(' ', $parts));
  }
}
