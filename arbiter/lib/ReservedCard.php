<?php

class ReservedCard {
  public int $id;
  // Cărțile trase din pachet sînt ascunse, dar culoarea pachetului este
  // vizibilă.
  public bool $hidden;

  function __construct(int $id, bool $hidden) {
    $this->id = $id;
    $this->hidden = $hidden;
  }
}
