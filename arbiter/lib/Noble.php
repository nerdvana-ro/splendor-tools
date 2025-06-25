<?php

class Noble {
  public int $id;
  public array $cost;

  private static array $nobles;

  function __construct(int $id, array $rec) {
    $this->id = $id;
    $this->cost = $rec;
  }

  static function loadCsv(string $fileName): void {
    self::$nobles = [ null ]; // nobilii sînt indexați de la 1
    $csv = Str::loadCsv($fileName, 1);
    $line = 1;
    foreach ($csv as $rec) {
      self::$nobles[] = new Noble($line++, $rec);
    }
  }

  static function get(int $index): Noble {
    return self::$nobles[$index];
  }

  function print(): void {
    $str = '';
    for ($col = 0; $col < Config::NUM_COLORS; $col++) {
      if ($this->cost[$col]) {
        $str .= Str::block($col, $this->cost[$col]);
        $str .= ' ';
      }
    }
    Log::info("    [#%02d] %s", [ $this->id, $str ]);
  }
}
