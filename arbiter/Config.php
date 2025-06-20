<?php

/**
 * Fișier de configurare.
 **/

class Config {
  // Una dintre constantele din lib/Log.php. Cu cît este mai mare, cu atît
  // arbitrul va scrie pe ecran mai multe informații de debug.
  const LOG_LEVEL = Log::DEBUG;

  const CHIP_CHAR = '◉';
  const BLOCK_CHAR = '🂠';

  const KIBITZ_PREFIX = 'kibitz ';

  // Limită hard pentru cazul în care agenții intră în buclă.
  const MAX_ROUNDS = 100;

  /**
   * Constante specifice jocului Splendor.
   **/
  const MAX_PLAYERS = 4;
  const NUM_COLORS = 5;
  const NUM_GOLD = 5;
  const TAKE_TWO_LIMIT = 4;
  const NUM_NOBLES = 10;
  const NOBLE_POINTS = 3;
  const NUM_CARDS = 90;
  const CARD_LEVELS = 3;
  const CARD_LEVEL_RANGES = [
    [], // nivelurile sînt indexate de la 1
    [  1, 40 ],
    [ 41, 70 ],
    [ 71, 90 ],
  ];
  const NUM_FACE_UP_CARDS = 4;
  const MAX_RESERVED_CARDS = 3;
  const ENDGAME_SCORE = 15;

  // Limite în funcție de numărul de jucători.
  const SUPPLY = [
    '1' => [ 'chips' => 4, 'nobles' => 2 ],
    '2' => [ 'chips' => 4, 'nobles' => 3 ],
    '3' => [ 'chips' => 5, 'nobles' => 4 ],
    '4' => [ 'chips' => 7, 'nobles' => 5 ],
  ];
}
