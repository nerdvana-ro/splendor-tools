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
}
