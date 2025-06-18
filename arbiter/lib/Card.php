<?php

class Card {
  public int $id;
  public array $cost;
  public int $color;
  public int $points;
  public int $level;

  private static array $cards;

  function __construct(array $rec, int $id) {
    $this->id = $id;
    $this->cost = array_slice($rec, 0, Config::NUM_COLORS);
    $this->color = $rec[Config::NUM_COLORS];
    $this->points = $rec[Config::NUM_COLORS + 1];
    $this->level = 1;
    while ($id > Config::CARD_LEVEL_RANGES[$this->level][1]) {
      $this->level++;
    }
  }

  static function loadCsv(string $fileName): void {
    self::$cards = [ null ]; // cărțile sînt indexate de la 1
    $csv = Str::loadCsv($fileName, 1);
    $id = 1;
    foreach ($csv as $rec) {
      self::$cards[] = new Card($rec, $id++);
    }
  }

  static function get(int $index): Card {
    return self::$cards[$index];
  }

  function print(): void {
    $str = sprintf('    [#%02d]    %d      ', $this->id, $this->points);
    $str .= Str::block($this->color, 1);
    $str .= '     ';
    for ($col = 0; $col < Config::NUM_COLORS; $col++) {
      if ($this->cost[$col]) {
        $str .= Str::chips($col, $this->cost[$col]);
        $str .= ' ';
      }
    }
    Log::debug($str);
  }
}
