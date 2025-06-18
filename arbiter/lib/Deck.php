<?php

class Deck {
  public array $faceUp;
  public array $faceDown;

  function __construct(int $from, int $to) {
    $this->faceDown = range($from, $to);
    shuffle($this->faceDown);

    $this->faceUp = array_splice($this->faceDown, 0, Config::NUM_FACE_UP_CARDS);

    $str1 = implode(' ', $this->faceUp);
    $str2 = implode(' ', $this->faceDown);
    Log::info('Am generat pachetul [%s] [%s]', [ $str1, $str2 ]);
  }

  function drawCard(): int {
    return array_shift($this->faceDown);
  }

  function isFaceUp(int $id): bool {
    return in_array($id, $this->faceUp);
  }

  function removeCard(int $id): void {
    $pos = array_search($id, $this->faceUp);
    if ($pos !== false) {
      $this->faceUp[$pos] = count($this->faceDown)
        ? $this->drawCard()
        : 0;
    }
  }

  function print(): void {
    foreach ($this->faceUp as $id) {
      if ($id) {
        $card = Card::get($id);
        $card->print();
      }
    }
    Log::info('    în pachet: %d', [ count($this->faceDown) ]);
  }

  function asInputFile(): string {
    $up = array_pad($this->faceUp, Config::NUM_FACE_UP_CARDS, 0);
    return trim(count($this->faceDown) . ' ' . implode(' ', $up));
  }
}
