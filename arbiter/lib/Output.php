<?php

/**
 * Ieșirea unui agent, compusă din
 * - un vector de întregi (acțiunea
 * - un vector de string-uri (mesajele chibițate)
 */

class Output {
  public array $tokens;
  public array $kibitzes;

  function __construct($tokens, $kibitzes) {
    $this->tokens = $tokens;
    $this->kibitzes = $kibitzes;
  }
}
