<?php

class Args {

  const OPTIONS = [
    'binary:',
    'name:',
    'games:',
    'seed:',
    'save:',
  ];


  private array $binaries;
  private array $names;
  private int $numGames;
  private int $seed;
  private string $saveDir;

  // PHP's getopt() will return a string if an argument appears once, but an
  // array if the argument appears multiple times. Convert the first situation
  // to an array as well.
  function asArray(mixed $x): array {
    return (gettype($x) == 'string') ? [$x] : $x;
  }

  function parse(): void {
    $opts = getopt('', self::OPTIONS);
    if (empty($opts)) {
      $this->usage();
      exit(1);
    }

    $this->binaries = $this->asArray($opts['binary'] ?? []);
    $this->names = $this->asArray($opts['name'] ?? []);
    $this->numGames = $opts['games'] ?? 1;
    $this->seed = $opts['seed'] ?? 0;
    $this->saveDir = $opts['save'] ?? '';
    $this->validate();
  }

  private function usage(): void {
    $scriptName = $_SERVER['SCRIPT_FILENAME'];
    print "Apel: $scriptName --binary <cale> --name <nume> [...]\n";
    print "\n";
    print "    --binary <cale>    Fișierul binar executabil al unui agent sau 'human' pentru jucător uman.\n";
    print "    --name <nume>      Numele agentului.\n";
    print "    --games <număr>    Numărul de partide.\n";
    print "    --seed <număr>     Seed-ul pentru RNG (0 sau lipsă pentru seed bazat pe timp)\n";
    print "    --save <cale>      Directorul unde vom salva partidele.\n";
    print "\n";
    print "Opțiunile --binary și --name pot fi repetate pentru fiecare agent.\n";
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
    if (Util::hasDuplicates($this->names)) {
      throw new SplendorException('Jucătorii trebuie să aibă nume distincte.');
    }
    if (!$this->numGames) {
      throw new SplendorException('Argumentul --games nu poate fi 0.');
    }
    if ($this->saveDir && !is_dir($this->saveDir)) {
      $msg = sprintf('Directorul [%s] nu există.', $this->saveDir);
      throw new SplendorException($msg);
    }
  }

  function getPlayers(): array {
    $result = [];
    foreach ($this->binaries as $i => $binary) {
      $realBinary = ($binary == 'human')
        ? $binary
        : realpath($binary);
      $result[] = [
        'binary' => $realBinary,
        'name' => $this->names[$i],
      ];
    }
    return $result;
  }

  function getNumGames(): int {
    return $this->numGames;
  }

  function getSeed(): int {
    return $this->seed;
  }

  function getSaveDir(): string {
    return $this->saveDir;
  }
}
