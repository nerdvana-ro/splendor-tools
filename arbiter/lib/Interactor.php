<?php

class Interactor {
  const string INPUT_FILE = '/tmp/input.txt';
  const string OUTPUT_FILE = '/tmp/output.txt';
  const int TIMEOUT = 10; // secunde

  // Citește fișierul de ieșire și îl sparge în tokeni.
  static function readOutputFile(): array {
    $contents = @file_get_contents(self::OUTPUT_FILE);

    if ($contents === false) {
      Log::warn('Fișierul %s nu există.', [ self::OUTPUT_FILE ]);
      return [];
    }

    $contents = trim($contents);
    Log::info('Programul a tipărit [%s].', [ $contents ]);
    return preg_split('/\s+/', $contents, -1, PREG_SPLIT_NO_EMPTY);
  }

  // Returnează un array de tokeni citiți de la ieșirea clientului.
  static function interact(string $binary, string $input): array {
    $dir = dirname($binary);
    chdir($dir);
    file_put_contents(self::INPUT_FILE, $input);
    @unlink(self::OUTPUT_FILE);

    Log::debug('Apelez %s în directorul %s.', [ $binary, $dir ]);
    $cmd = sprintf('ulimit -t %d && %s < %s > %s',
                   self::TIMEOUT, $binary, self::INPUT_FILE, self::OUTPUT_FILE);
    $output = null;
    $resultCode = null;
    exec($cmd, $output, $resultCode);
    if ($resultCode !== 0) {
      Log::warn('Clientul s-a terminat cu codul %d.', [ $resultCode ]);
    }

    return self::readOutputFile($choices);
  }
}
