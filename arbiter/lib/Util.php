<?php

class Util {

  static function hasDuplicates(array $arr): bool {
    return count($arr) != count(array_unique($arr));
  }

  static function getTimeMillis(): int {
    return floor(microtime(true) * 1000);
  }

  static function initRng(int $seed): void {
    if (!$seed) {
      $micros = microtime(true);
      $seed = $micros * 1_000_000 % 1_000_000_000;
    }
    Log::info('Inițializez RNG cu seed-ul %d.', [ $seed ]);
    srand($seed);
  }

}
