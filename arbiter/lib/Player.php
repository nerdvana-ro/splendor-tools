<?php

class Player {
  private string $binary;
  public string $name;

  function __construct(string $binary, string $name) {
    $this->binary = realpath($binary);
    $this->name = $name;
    Log::info('Am adăugat jucătorul %s cu binarul %s.', [ $name, $binary ]);
  }
}
