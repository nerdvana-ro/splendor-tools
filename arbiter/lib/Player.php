<?php

class Player {
  private string $binary;
  public string $name;

  public array $chips;
  public array $cards;
  public array $reserve; // vector de ReservedCard
  public array $nobles;

  function __construct(string $binary, string $name) {
    $this->binary = realpath($binary);
    $this->name = $name;
    Log::info('Am adăugat jucătorul %s cu binarul %s.', [ $name, $binary ]);

    $this->chips = array_fill(0, Config::NUM_COLORS + 1, 0); // inclusiv 0 aur
    $this->cards = [];
    $this->reserve = [];
    $this->nobles = [];
  }

  function getScore(): int {
    return 0;
  }

  function requestAction(string $gameState): void {
    Log::info('Aștept o acțiune de la %s', [ $this->name ]);
    $tokens = Interactor::interact($this->binary, $gameState);
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

  function print(int $myId): void {
    Log::debug('======== Jucătorul %d (%s)', [ $myId, $this->name ]);
  }
}
