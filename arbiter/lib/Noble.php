<?php

class Noble {
  public array $cost;

  private static array $nobles;

  function __construct(array $rec) {
    $this->cost = $rec;
  }

  static function loadCsv(string $fileName): void {
    self::$nobles = [ null ]; // nobilii sînt indexați de la 1
    $csv = Str::loadCsv($fileName, 1);
    foreach ($csv as $rec) {
      self::$nobles[] = new Noble($rec);
    }
  }

  static function get(int $index): Noble {
    return self::$nobles[$index];
  }
}
