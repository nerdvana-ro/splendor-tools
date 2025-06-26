<?php

class Util {

  static function hasDuplicates(array $arr): bool {
    return count($arr) != count(array_unique($arr));
  }

}
