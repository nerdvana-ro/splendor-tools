<?php

class Str {

  static function startsWith($string, $prefix): bool {
    $start = substr($string, 0, strlen($prefix));
    return $start == $prefix;
  }

  static function loadCsv(string $fileName, int $linesToIgnore): array {
    $result = [];
    $lines = file($fileName);
    $lines = array_slice($lines, $linesToIgnore);
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line) {
        $parts = str_getcsv($line, ',', '"', "\\");
        foreach ($parts as &$part) {
          $part = trim($part);
        }
        $result[] = $parts;
      }
    }
    return $result;
  }

  static function chips(int $color, int $count): string {
    return
      AnsiColors::CHIPS[$color] .
      str_repeat(Config::CHIP_CHAR, $count) .
      AnsiColors::DEFAULT;
  }

  static function block(int $color, int $count): string {
    return
      AnsiColors::CHIPS[$color] .
      str_repeat(Config::BLOCK_CHAR, $count) .
      AnsiColors::DEFAULT;
  }

}
