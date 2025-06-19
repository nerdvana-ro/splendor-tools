<?php

class Interactor {
  const string INPUT_FILE = '/tmp/input.txt';
  const string OUTPUT_FILE = '/tmp/output.txt';
  const string ERROR_FILE = '/tmp/error.txt';
  const int TIMEOUT = 10; // secunde

  private string $binary;
  private string $input;

  private array $output;   // Ieșirea tokenizată în cuvinte.
  private array $kibitzes; // Liniile chibițate, fără prefixul „kibitz ”.

  function __construct(string $binary, string $input) {
    $this->binary = $binary;
    $this->input = $input;
    $this->kibitzes = [];
  }

  function getOutput(): array {
    return $this->output;
  }

  function getKibitzes(): array {
    return $this->kibitzes;
  }

  function parseAgentOutput(): void {
    $contents = @file_get_contents(self::OUTPUT_FILE);

    if ($contents === false) {
      Log::warn('Fișierul %s nu există.', [ self::OUTPUT_FILE ]);
      return;
    }

    $contents = trim($contents);
    Log::info('Programul a tipărit [%s].', [ $contents ]);
    $this->output = preg_split('/\s+/', $contents, -1, PREG_SPLIT_NO_EMPTY);
  }

  function parseAgentError(): void {
    if (!file_exists(self::ERROR_FILE)) {
      return;
    }
    Log::debug("Programul a tipărit la stderr:\n%s",
               [ file_get_contents(self::ERROR_FILE) ]);
    $lines = file(self::ERROR_FILE);

    foreach ($lines as $line) {
      if (Str::startsWith($line, Config::KIBITZ_PREFIX)) {
        $suf = substr($line, strlen(Config::KIBITZ_PREFIX));
        $this->kibitzes[] = trim($suf);
      }
    }
  }

  function run(): void {
    if ($this->binary == 'human') {
      self::interactHuman();
    } else {
      self::interactAgent();
    }
  }

  private function interactHuman(): void {
    $line = readline('Introdu o mutare: ');
    $this->output = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
  }

  private function interactAgent(): void {
    $dir = dirname($this->binary);
    chdir($dir);
    file_put_contents(self::INPUT_FILE, $this->input);
    @unlink(self::OUTPUT_FILE);
    @unlink(self::ERROR_FILE);

    Log::debug('Apelez %s în directorul %s.', [ $this->binary, $dir ]);
    $cmd = sprintf('ulimit -t %d && %s < %s > %s 2> %s',
                   self::TIMEOUT,
                   $this->binary,
                   self::INPUT_FILE,
                   self::OUTPUT_FILE,
                   self::ERROR_FILE);
    $output = null;
    $resultCode = null;
    exec($cmd, $output, $resultCode);
    if ($resultCode !== 0) {
      Log::warn('Agentul s-a terminat cu codul %d.', [ $resultCode ]);
    }

    self::parseAgentOutput();
    self::parseAgentError();
  }
}
