<?php

class Interactor {
  private string $inputFile;
  private string $outputFile;
  private string $errorFile;

  private string $binary;
  private string $input;

  private float $time;    // Timpul petrecut, inclusiv invocarea.
  private array $tokens;   // Ieșirea tokenizată în cuvinte.
  private array $kibitzes; // Liniile chibițate, fără prefixul „kibitz ”.

  function __construct(string $binary, string $input) {
    $pid = getmypid();
    $this->inputFile  = "/tmp/input_$pid.txt";
    $this->outputFile = "/tmp/output_$pid.txt";
    $this->errorFile  = "/tmp/error_$pid.txt";
    $this->binary = $binary;
    $this->input = $input;
    $this->time = 0.0;
    $this->tokens = [];
    $this->kibitzes = [];
  }

  function getOutput(): Output {
    return new Output($this->tokens, $this->kibitzes);
  }

  function getKibitzes(): array {
    return $this->kibitzes;
  }

  function getTime(): int {
    return $this->time;
  }

  function parseAgentOutput(): void {
    $contents = @file_get_contents($this->outputFile);

    if ($contents === false) {
      Log::warn('Fișierul %s nu există.', [ $this->outputFile]);
      return;
    }

    $contents = trim($contents);
    Log::info('Programul a tipărit [%s].', [ $contents ]);
    $this->tokens = preg_split('/\s+/', $contents, -1, PREG_SPLIT_NO_EMPTY);
  }

  function parseAgentError(): void {
    if (!file_exists($this->errorFile)) {
      return;
    }

    $contents = file_get_contents($this->errorFile);
    if ($contents) {
      Log::debug("Programul a tipărit la stderr:\n{$contents}");
    }

    $lines = file($this->errorFile);
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
    $this->tokens = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
  }

  private function interactAgent(): void {
    $dir = dirname($this->binary);
    chdir($dir);
    file_put_contents($this->inputFile, $this->input);
    @unlink($this->outputFile);
    @unlink($this->errorFile);

    Log::debug('Apelez %s în directorul %s cu intrarea:', [ $this->binary, $dir ]);
    Log::debug(trim($this->input));
    $cmd = sprintf('ulimit -t %d && "%s" < %s > %s 2> %s',
                   Config::TIME_LIMIT_PER_MOVE,
                   $this->binary,
                   $this->inputFile,
                   $this->outputFile,
                   $this->errorFile);

    $resultCode = self::runCmd($cmd);
    if ($resultCode !== 0) {
      Log::warn('Agentul s-a terminat cu codul %d.', [ $resultCode ]);
    }

    self::parseAgentOutput();
    self::parseAgentError();
  }

  private function runCmd(string $cmd): int {
    $ignoredOutput = null;
    $resultCode = null;

    $startTime = Util::getTimeMillis();
    exec($cmd, $ignoredOutput, $resultCode);
    $endTime = Util::getTimeMillis();
    $this->time = $endTime - $startTime;
    Log::debug('Timp de rulare: %0.3f secunde.', [ $this->time / 1000 ]);
    return $resultCode;
  }
}
