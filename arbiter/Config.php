<?php

/**
 * Fișier de configurare.
 **/

class Config {
  // Una dintre constantele din lib/Log.php. Cu cît este mai mare, cu atît
  // arbitrul va scrie pe ecran mai multe informații de debug.
  const LOG_LEVEL = Log::DEBUG;

  /**
   * Constante specifice jocului Splendor.
   **/
  const MAX_PLAYERS = 4;
  const NUM_COLORS = 5;
  const NUM_GOLD = 5;
  const NUM_NOBLES = 10;
  const FIRST_LEVEL_1_CARD = 1;
  const LAST_LEVEL_1_CARD = 40;
  const FIRST_LEVEL_2_CARD = 41;
  const LAST_LEVEL_2_CARD = 70;
  const FIRST_LEVEL_3_CARD = 71;
  const LAST_LEVEL_3_CARD = 90;
  const NUM_FACE_UP_CARDS = 4;


  // Limite în funcție de numărul de jucători.
  const SUPPLY = [
    '1' => [ 'chips' => 4, 'nobles' => 2 ],
    '2' => [ 'chips' => 4, 'nobles' => 3 ],
    '3' => [ 'chips' => 5, 'nobles' => 4 ],
    '4' => [ 'chips' => 7, 'nobles' => 5 ],
  ];
}
