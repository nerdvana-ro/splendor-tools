<?php

class Log {
  const int FATAL = 0;
  const int ERROR = 1;
  const int WARN = 2;
  const int SUCCESS = 3;
  const int INFO = 4;
  const int DEBUG = 5;

  static function fatal(string $msg, array $args = []): void {
    self::error($msg, $args);
    exit(1);
  }

  static function error(string $msg, array $args = [], $indent = 0): void {
    self::write(self::ERROR, AnsiColors::ERROR, $msg, $args, $indent);
  }

  static function warn(string $msg, array $args = [], $indent = 0): void {
    self::write(self::WARN, AnsiColors::WARNING, $msg, $args, $indent);
  }

  static function success(string $msg, array $args = [], $indent = 0): void {
    self::write(self::SUCCESS, AnsiColors::SUCCESS, $msg, $args, $indent);
  }

  static function info(string $msg, array $args = [], $indent = 0): void {
    self::write(self::INFO, AnsiColors::INFO, $msg, $args, $indent);
  }

  static function debug(string $msg, array $args = [], $indent = 0): void {
    self::write(self::DEBUG, AnsiColors::DEBUG, $msg, $args, $indent);
  }

  private static function write(
    int $level, string $color, string $msg, array $args = [], int $indent = 0): void {

    if (Config::LOG_LEVEL >= $level) {
      $spaces = str_repeat(' ', 4 * $indent);
      $str = vsprintf($msg, $args);
      printf("%s%s%s%s\n", $spaces, $color, $str, AnsiColors::DEFAULT);
    }
  }
}
