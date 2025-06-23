// Constante referitoare la interfață
const CHIP_STACK_HEIGHT = 7;

// Constante referitoare la regulile jocului.
const NUM_COLORS = 5;
const CHIP_SUPPLY = { 1: 4, 2: 4, 3: 5, 4: 7 }; // după numărul de jucători
const GOLD_SUPPLY = 5;
const NUM_LEVELS = 3;
const NUM_FACE_UP_CARDS = 4;

const CARDS = [
  // * costuri (roșu, verde, albastru, alb, negru)
  // * culoarea bonus
  // * puncte
  // * imaginea de fundal
  [ /* cărțile sînt indexate de la 1 */ ],
  [ [ 0, 0, 0, 3, 0 ], 0, 0, 0 ],
  [ [ 3, 0, 0, 0, 0 ], 1, 0, 8 ],
  [ [ 0, 0, 0, 0, 3 ], 2, 0, 14 ],
  [ [ 0, 0, 3, 0, 0 ], 3, 0, 18 ],
  [ [ 0, 3, 0, 0, 0 ], 4, 0, 26 ],
  [ [ 0, 1, 2, 0, 0 ], 0, 0, 0 ],
  [ [ 0, 0, 1, 2, 0 ], 1, 0, 6 ],
  [ [ 0, 0, 0, 1, 2 ], 2, 0, 14 ],
  [ [ 2, 0, 0, 0, 1 ], 3, 0, 18 ],
  [ [ 1, 2, 0, 0, 0 ], 4, 0, 24 ],
  [ [ 0, 0, 0, 4, 0 ], 0, 1, 2 ],
  [ [ 0, 0, 0, 0, 4 ], 1, 1, 6 ],
  [ [ 4, 0, 0, 0, 0 ], 2, 1, 12 ],
  [ [ 0, 4, 0, 0, 0 ], 3, 1, 20 ],
  [ [ 0, 0, 4, 0, 0 ], 4, 1, 24 ],
  [ [ 2, 0, 0, 2, 0 ], 0, 0, 2 ],
  [ [ 2, 0, 2, 0, 0 ], 1, 0, 8 ],
  [ [ 0, 2, 0, 0, 2 ], 2, 0, 12 ],
  [ [ 0, 0, 2, 0, 2 ], 3, 0, 20 ],
  [ [ 0, 2, 0, 2, 0 ], 4, 0, 26 ],
  [ [ 0, 1, 1, 1, 1 ], 0, 0, 2 ],
  [ [ 1, 0, 1, 1, 1 ], 1, 0, 8 ],
  [ [ 1, 1, 0, 1, 1 ], 2, 0, 12 ],
  [ [ 1, 1, 1, 0, 1 ], 3, 0, 18 ],
  [ [ 1, 1, 1, 1, 0 ], 4, 0, 24 ],
  [ [ 0, 1, 1, 2, 1 ], 0, 0, 0 ],
  [ [ 1, 0, 1, 1, 2 ], 1, 0, 6 ],
  [ [ 2, 1, 0, 1, 1 ], 2, 0, 14 ],
  [ [ 1, 2, 1, 0, 1 ], 3, 0, 20 ],
  [ [ 1, 1, 2, 1, 0 ], 4, 0, 26 ],
  [ [ 0, 1, 0, 2, 2 ], 0, 0, 2 ],
  [ [ 2, 0, 1, 0, 2 ], 1, 0, 6 ],
  [ [ 2, 2, 0, 1, 0 ], 2, 0, 14 ],
  [ [ 0, 2, 2, 0, 1 ], 3, 0, 18 ],
  [ [ 1, 0, 2, 2, 0 ], 4, 0, 24 ],
  [ [ 1, 0, 0, 1, 3 ], 0, 0, 0 ],
  [ [ 0, 1, 3, 1, 0 ], 1, 0, 8 ],
  [ [ 1, 3, 1, 0, 0 ], 2, 0, 12 ],
  [ [ 0, 0, 1, 3, 1 ], 3, 0, 20 ],
  [ [ 3, 1, 0, 0, 1 ], 4, 0, 26 ],
  [ [ 0, 0, 0, 0, 5 ], 0, 2, 1 ],
  [ [ 0, 5, 0, 0, 0 ], 1, 2, 7 ],
  [ [ 0, 0, 5, 0, 0 ], 2, 2, 17 ],
  [ [ 5, 0, 0, 0, 0 ], 3, 2, 19 ],
  [ [ 0, 0, 0, 5, 0 ], 4, 2, 25 ],
  [ [ 6, 0, 0, 0, 0 ], 0, 3, 5 ],
  [ [ 0, 6, 0, 0, 0 ], 1, 3, 7 ],
  [ [ 0, 0, 6, 0, 0 ], 2, 3, 13 ],
  [ [ 0, 0, 0, 6, 0 ], 3, 3, 23 ],
  [ [ 0, 0, 0, 0, 6 ], 4, 3, 29 ],
  [ [ 0, 0, 0, 3, 5 ], 0, 2, 5 ],
  [ [ 0, 3, 5, 0, 0 ], 1, 2, 11 ],
  [ [ 0, 0, 3, 5, 0 ], 2, 2, 13 ],
  [ [ 5, 0, 0, 0, 3 ], 3, 2, 23 ],
  [ [ 3, 5, 0, 0, 0 ], 4, 2, 29 ],
  [ [ 0, 2, 4, 1, 0 ], 0, 2, 1 ],
  [ [ 0, 0, 2, 4, 1 ], 1, 2, 11 ],
  [ [ 1, 0, 0, 2, 4 ], 2, 2, 17 ],
  [ [ 4, 1, 0, 0, 2 ], 3, 2, 19 ],
  [ [ 2, 4, 1, 0, 0 ], 4, 2, 29 ],
  [ [ 2, 0, 0, 2, 3 ], 0, 1, 1 ],
  [ [ 0, 0, 3, 2, 2 ], 1, 1, 11 ],
  [ [ 3, 2, 2, 0, 0 ], 2, 1, 13 ],
  [ [ 2, 3, 0, 0, 2 ], 3, 1, 19 ],
  [ [ 0, 2, 2, 3, 0 ], 4, 1, 25 ],
  [ [ 2, 0, 3, 0, 3 ], 0, 1, 5 ],
  [ [ 3, 2, 0, 3, 0 ], 1, 1, 7 ],
  [ [ 0, 3, 2, 0, 3 ], 2, 1, 17 ],
  [ [ 3, 0, 3, 2, 0 ], 3, 1, 23 ],
  [ [ 0, 3, 0, 3, 2 ], 4, 1, 25 ],
  [ [ 0, 7, 0, 0, 0 ], 0, 4, 3 ],
  [ [ 0, 0, 7, 0, 0 ], 1, 4, 9 ],
  [ [ 0, 0, 0, 7, 0 ], 2, 4, 15 ],
  [ [ 0, 0, 0, 0, 7 ], 3, 4, 21 ],
  [ [ 7, 0, 0, 0, 0 ], 4, 4, 28 ],
  [ [ 3, 7, 0, 0, 0 ], 0, 5, 3 ],
  [ [ 0, 3, 7, 0, 0 ], 1, 5, 10 ],
  [ [ 0, 0, 3, 7, 0 ], 2, 5, 16 ],
  [ [ 0, 0, 0, 3, 7 ], 3, 5, 22 ],
  [ [ 7, 0, 0, 0, 3 ], 4, 5, 28 ],
  [ [ 3, 6, 3, 0, 0 ], 0, 4, 4 ],
  [ [ 0, 3, 6, 3, 0 ], 1, 4, 10 ],
  [ [ 0, 0, 3, 6, 3 ], 2, 4, 15 ],
  [ [ 3, 0, 0, 3, 6 ], 3, 4, 22 ],
  [ [ 6, 3, 0, 0, 3 ], 4, 4, 27 ],
  [ [ 0, 3, 5, 3, 3 ], 0, 3, 4 ],
  [ [ 3, 0, 3, 5, 3 ], 1, 3, 9 ],
  [ [ 3, 3, 0, 3, 5 ], 2, 3, 16 ],
  [ [ 5, 3, 3, 0, 3 ], 3, 3, 21 ],
  [ [ 3, 5, 3, 3, 0 ], 4, 3, 27 ],
];

const NOBLES = [
  // * costuri (roșu, verde, albastru, alb, negru)
  [ /* nobilii sînt indexați de la 1 */ ],
  // costuri
  [ 4, 4, 0, 0, 0 ],
  [ 0, 4, 4, 0, 0 ],
  [ 0, 0, 4, 4, 0 ],
  [ 0, 0, 0, 4, 4 ],
  [ 4, 0, 0, 0, 4 ],
  [ 3, 3, 3, 0, 0 ],
  [ 0, 3, 3, 3, 0 ],
  [ 0, 0, 3, 3, 3 ],
  [ 3, 0, 0, 3, 3 ],
  [ 3, 3, 0, 0, 3 ],
];

const SAMPLE_GAME = `
{"players":["doofus1","doofus2"],"decks":[[28,4,24,12,11,14,30,33,10,37,15,9,23,19,3,20,17,29,5,35,2,8,26,27,16,34,18,39,7,6,1,40,22,25,21,31,13,36,32,38],[44,63,54,42,56,53,47,43,57,55,69,51,50,45,41,67,58,66,52,62,70,68,46,59,65,48,49,60,64,61],[86,79,82,75,77,74,80,90,88,83,71,73,85,87,89,84,72,76,81,78]],"nobles":[9,6,8],"rounds":[[{"tokens":[1,1,0,1,0,1],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,0,1,1,0],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,0,1,1,1,0],"kibitzes":["Str\u00eeng pentru cartea 24.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,0,1,1,0,1],"kibitzes":["Str\u00eeng pentru cartea 24.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,24,0,1,1,1,0,1,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,0,1,1,1,0],"kibitzes":["Str\u00eeng pentru cartea 4.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,1,1,0,0,1],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,4,0,0,0,3,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,0,1,1,0],"kibitzes":["Str\u00eeng pentru cartea 28.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,1,1,0,0],"kibitzes":["Str\u00eeng pentru cartea 28.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,28,0,2,1,0,0,1,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,0,0,0,0,0],"kibitzes":["Str\u00eeng pentru cartea 63."],"arbiterMsg":"Juc\u0103torul 1 zice pas din cauza erorii: Cuv\u00eentul 1 este \u00een plus."}],[{"tokens":[1,1,1,1,0,0],"kibitzes":["Str\u00eeng pentru cartea 30.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,0,0,0,0],"kibitzes":["Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,30,0,1,1,1,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,33,0,2,2,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,1,1,0,0],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,1,0,0,1],"kibitzes":["Str\u00eeng pentru cartea 10.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,1,1,0,0,1],"kibitzes":["Str\u00eeng pentru cartea 10.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,10,0,1,2,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,37,0,0,1,2,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,1,1,0,0,1],"kibitzes":["Str\u00eeng pentru cartea 12.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,0,1,1,0,0],"kibitzes":["Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,12,0,0,0,0,0,3,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,9,0,2,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,23,0,1,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,19,0,0,0,1,0,1,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[2,0,0,0,0,2],"kibitzes":["Str\u00eeng pentru cartea 3."],"arbiterMsg":""}],[{"tokens":[4,11,0,0,0,0,1,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,3,0,0,0,0,0,2,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,20,0,0,1,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,29,0,1,1,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[2,2,0,0,0,0],"kibitzes":["Str\u00eeng pentru cartea 63."],"arbiterMsg":""},{"tokens":[4,15,0,0,0,1,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,63,0,2,1,1,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,0,1,1,0,1],"kibitzes":["Str\u00eeng pentru cartea 56.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,35,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,56,0,0,1,1,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,0,1,1,0],"kibitzes":["Str\u00eeng pentru cartea 53.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,1,1,0,0],"kibitzes":["Str\u00eeng pentru cartea 17.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,53,8,0,0,1,2,0,0],"kibitzes":[],"arbiterMsg":"doofus1 prime\u0219te nobilul #8."},{"tokens":[4,17,0,1,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,8,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,26,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,27,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,16,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,34,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,18,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,39,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,86,0,0,1,1,1,1,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,7,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,6,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,1,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,44,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,40,0,1,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,22,6,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":"doofus2 prime\u0219te nobilul #6."}],[{"tokens":[4,25,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,21,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,31,9,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":"doofus1 prime\u0219te nobilul #9."},{"tokens":[4,13,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,36,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,43,0,0,0,0,0,0,0],"kibitzes":[],"arbiterMsg":""}]]}
`;
