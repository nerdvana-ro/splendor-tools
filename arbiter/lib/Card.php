<?php

class Card {
  public array $cost;
  public int $color;
  public int $points;

  private static array $cards;

  function __construct(array $rec) {
    $this->cost = array_slice($rec, 0, Config::NUM_COLORS);
    $this->color = $rec[Config::NUM_COLORS];
    $this->points = $rec[Config::NUM_COLORS + 1];
  }

  static function loadCsv(string $fileName): void {
    self::$cards = [ null ]; // cărțile sînt indexate de la 1
    $csv = Str::loadCsv($fileName, 1);
    foreach ($csv as $rec) {
      self::$cards[] = new Card($rec);
    }
    var_dump(self::$cards);
  }

  static function get(int $index): Card {
    return self::$cards[$index];
  }
}
