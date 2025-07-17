<?php

class Core {

  static function init(): void {
    self::setAutoload();
    self::loadGameData();
  }

  private static function setAutoload(): void {
    // Încarcă la cerere clasele din directorul lib/.
    spl_autoload_register(function($className) {
      $fileName = sprintf('%s/%s.php', __DIR__, $className);
      if (file_exists($fileName)) {
        require_once $fileName;
      }
    });
  }

  private static function loadGameData(): void {
    Card::loadCsv(__DIR__ . '/../../cards.csv');
    Noble::loadCsv(__DIR__ . '/../../nobles.csv');
  }
}

Core::init();
