<?php

class GameResult {
  public string $name;
  public int $score;
  public int $cards;
  public bool $winner;

  function __construct(Player $p) {
    $this->name = $p->name;
    $this->score = $p->getScore();
    $this->cards = count($p->cards);
    $this->winner = false;
  }

  private function cmp(GameResult $other): int {
    if ($this->score != $other->score) {
      return $other->score - $this->score; // scorurile mai mari primele
    }
    return $this->cards - $other->cards;
  }

  private static function sort(array &$results): void {
    usort($results, function(GameResult $a, GameResult $b) {
      return $a->cmp($b);
    });
  }

  static function decideWinners(array &$results): void {
    self::sort($results);
    if ($results[0]->score >= Config::ENDGAME_SCORE) {
      foreach ($results as &$res) {
        if ($res->cmp($results[0]) == 0) {
          $res->winner = true;
        }
      }
    }
  }
}
