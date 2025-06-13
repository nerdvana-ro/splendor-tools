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

  function print(): void {
    foreach ($this->faceUp as $id) {
      $card = Card::get($id);
      $str = sprintf('    [#%02d]    %d      ', $id, $card->points);
      $str .= Str::block($card->color);
      $str .= '     ';
      for ($col = 0; $col < Config::NUM_COLORS; $col++) {
        if ($card->cost[$col]) {
          $str .= Str::chips($col, $card->cost[$col]);
          $str .= ' ';
        }
      }
      Log::debug($str);
    }
    Log::debug('    în pachet: %d', [ count($this->faceDown) ]);
  }

  function asInputFile(): string {
    $up = array_pad($this->faceUp, Config::NUM_FACE_UP_CARDS, 0);
    return trim(count($this->faceDown) . ' ' . implode(' ', $up));
  }
}
