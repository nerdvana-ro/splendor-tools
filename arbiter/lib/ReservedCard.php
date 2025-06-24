<?php

class ReservedCard {
  public int $id;
  // Cărțile trase din pachet sînt ascunse, dar culoarea pachetului este
  // vizibilă.
  public bool $secret;

  function __construct(int $id, bool $secret) {
    $this->id = $id;
    $this->secret = $secret;
  }
}
