<?php

class Util {

  static function hasDuplicates(array $arr): bool {
    return count($arr) != count(array_unique($arr));
  }

  static function getTimeMillis(): int {
    return floor(microtime(true) * 1000);
  }

}
