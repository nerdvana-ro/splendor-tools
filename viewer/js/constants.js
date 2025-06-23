// Constante referitoare la interfață
const CHIP_STACK_HEIGHT = 7;

// Constante referitoare la regulile jocului.
const NUM_COLORS = 5;
const CHIP_SUPPLY = { 1: 4, 2: 4, 3: 5, 4: 7 }; // după numărul de jucători
const GOLD_SUPPLY = 5;
const NUM_LEVELS = 3;
const NUM_FACE_UP_CARDS = 4;
const NOBLE_POINTS = 3;

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
{"players":["doofus1","doofus2"],"decks":[[14,13,2,25,15,9,20,39,23,3,32,17,27,11,24,22,36,40,26,19,16,10,29,6,5,37,30,12,8,4,31,21,33,18,35,28,38,34,1,7],[64,65,67,51,68,45,66,43,42,56,50,53,41,48,49,61,58,44,63,47,59,70,55,52,69,60,54,46,57,62],[88,76,84,85,73,78,79,82,86,90,75,71,77,87,80,89,74,83,72,81]],"nobles":[9,3,8],"rounds":[[{"tokens":[1,0,1,0,1,1],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,0,1,0,1],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,1,0,1,0,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 25.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,0,1,1,1,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 25.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,25,0,1,1,1,1,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,1,1,0,1,0],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,1,0,1,0,1],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,0,0,1,0],"returns":["2"],"kibitzes":["Str\u00eeng pentru cartea 67."],"arbiterMsg":"doofus2 a returnat jetoane de culorile 2."}],[{"tokens":[1,0,1,1,1,0],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,67,0,3,2,0,3,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,0,0,1,0],"returns":[],"kibitzes":["Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[1,1,0,1,1,0],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,0,1,0,1,0],"returns":["0","0"],"kibitzes":["Str\u00eeng pentru cartea 65."],"arbiterMsg":"doofus1 a returnat jetoane de culorile 0, 0."},{"tokens":[1,1,1,0,0,0],"returns":[],"kibitzes":["Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,65,0,0,2,2,3,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,1,1,1,0,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 2.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[1,1,0,1,1,0],"returns":[],"kibitzes":["Completez cu 3 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,2,0,3,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,1,0,1,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 9.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""},{"tokens":[4,14,0,0,2,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,9,0,2,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,20,0,0,0,0,1,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,51,0,0,0,0,2,3,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,1,1,0,0,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 68.","Completez cu 1 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,23,0,0,1,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,68,0,0,1,2,0,2,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[1,1,0,1,0,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 66."],"arbiterMsg":""},{"tokens":[1,1,1,0,0,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 64."],"arbiterMsg":""}],[{"tokens":[4,66,0,1,0,2,0,1,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,64,0,2,1,0,0,1,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[2,0,0,0,2,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 39."],"arbiterMsg":""},{"tokens":[1,1,0,0,1,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 39.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,39,0,0,0,0,2,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,1,1,1,0,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 32.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[4,32,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,17,0,2,0,1,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,27,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[1,0,1,0,1,1],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 42.","Completez cu 2 jetoane la \u00eent\u00eemplare."],"arbiterMsg":""}],[{"tokens":[2,2,0,0,0,0],"returns":[],"kibitzes":["Str\u00eeng pentru cartea 13."],"arbiterMsg":""},{"tokens":[4,42,0,0,2,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,13,0,2,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,11,0,0,0,0,2,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,22,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,36,0,0,0,0,0,2,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,24,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,26,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,19,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,40,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,10,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,29,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,6,9,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":"doofus1 prime\u0219te nobilul #9."},{"tokens":[4,5,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,16,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,30,0,0,0,1,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,3,8,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":"doofus1 prime\u0219te nobilul #8."},{"tokens":[4,12,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,37,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,31,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,21,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,33,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,18,3,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":"doofus1 prime\u0219te nobilul #3."},{"tokens":[4,35,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}],[{"tokens":[4,56,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""},{"tokens":[4,88,0,0,0,0,0,0,0],"returns":[],"kibitzes":[],"arbiterMsg":""}]]}
`;
