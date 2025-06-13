<?php

class Args {
  private array $binaries;
  private array $names;

  // PHP's getopt() will return a string if an argument appears once, but an
  // array if the argument appears multiple times. Convert the first situation
  // to an array as well.
  function asArray(mixed $x): array {
    return (gettype($x) == 'string') ? [$x] : $x;
  }

  function parse(): void {
    $opts = getopt('b:n:');
    if (empty($opts)) {
      $this->usage();
      exit(1);
    }

    $this->binaries = $this->asArray($opts['b'] ?? []);
    $this->names = $this->asArray($opts['n'] ?? []);
    $this->validate();
  }

  private function usage(): void {
    $scriptName = $_SERVER['SCRIPT_FILENAME'];
    print "Apel: $scriptName -b <cale> -n <nume> [...]\n";
    print "\n";
    print "    -b <cale>:  Fișierul binar executabil al unui client.\n";
    print "    -n <nume>:  Numele clientului.\n";
    print "\n";
    print "Opțiunile -b și -n pot fi repetate pentru fiecare client.\n";
  }

  private function validate(): void {
    if (!count($this->binaries)) {
      throw new SplendorException('Trebuie să specifici cel puțin un binar.');
    }
    if (count($this->binaries) != count($this->names)) {
      throw new SplendorException('Numărul de binare nu corespunde cu numărul de nume.');
    }
    if (count($this->binaries) > Config::MAX_PLAYERS) {
      $msg = sprintf('Numărul maxim de jucători este %d.', Config::MAX_PLAYERS);
      throw new SplendorException($msg);
    }
  }

  function getNumPlayers(): int {
    return count($this->binaries);
  }

  function getPlayer(int $i): array {
    return [ $this->binaries[$i], $this->names[$i] ];
  }
}
