$(function() {

  let viewer = null;
  let cardBackStub = $('#card-back-stub').detach().removeAttr('id');
  let cardFrontStub = $('#card-front-stub').detach().removeAttr('id');
  let chipStackStub = $('#chip-stack-stub').detach().removeAttr('id');
  let kibitzStub = $('#kibitz-stub').detach().removeAttr('id');
  let minicardStub = $('#minicard-stub').detach().removeAttr('id');
  let nobleStub = $('#noble-stub').detach().removeAttr('id');
  let playerStub = $('#player-stub').detach().removeAttr('id');

  class Card {
    id;
    cost;
    color;
    points;
    background;   // ID-ul imaginii de fundal

    constructor(id) {
      let rec = CARDS[id];
      this.id = id;
      this.cost = rec[0];
      this.color = rec[1];
      this.points = rec[2];
      this.background = rec[3];
    }
  }

  class Deck {
    faceUp;
    faceDown; // vectori de Card

    constructor(ids) {
      this.faceUp = [];
      this.faceDown = [];

      for (const id of ids.slice(0, NUM_FACE_UP_CARDS)) {
        this.faceUp.push(new Card(id));
      }
      for (const id of ids.slice(NUM_FACE_UP_CARDS)) {
        this.faceDown.push(new Card(id));
      }
    }

    replaceCard(col) {
      this.faceUp[col] = this.faceDown.length
        ? this.faceDown.shift()
        : null;
    }
  }

  class Board {
    chips;
    decks;
    nobles;

    constructor(data) {
      let n = data.players.length;
      this.chips = Array(NUM_COLORS).fill(CHIP_SUPPLY[n]);
      this.chips.push(GOLD_SUPPLY);

      this.decks = [];
      for (const arr of data.decks) {
        this.decks.push(new Deck(arr));
      }
      this.nobles = data.nobles;
    }

    findCard(id) {
      for (let r = 0; r < this.decks.length; r++) {
        for (let c = 0; c < this.decks[r].faceUp.length; c++) {
          if (this.decks[r].faceUp[c].id == id) {
            return { row: r, col: c };
          }
        }
      }
      return null;
    }
  }

  class Player {
    name;
    chips;
    cards;
    score;

    constructor(name) {
      this.name = name;
      this.chips = Array(NUM_COLORS + 1).fill(0);
      this.cards = Array(NUM_COLORS).fill(0);
      this.score = 0;
    }
  }

  class Game {
    board;
    players;

    constructor(data) {
      this.board = new Board(data);

      this.players = [];
      for (const name of data.players) {
        this.players.push(new Player(name));
      }
    }

    replaceCard(row, col) {
      this.board.decks[row].replaceCard(col);
    }
  }

  class UI {
    game;
    cards;
    nobles;

    constructor(game) {
      this.game = game;
      this.createCards();
      this.createNobles();
      this.createDecks();
      this.createChips();
      this.createPlayers();
      this.updatePlayerCardsAndChips();
      this.updateNobles();
    }

    getDecks() {
      return $('#decks').find('.deck');
    }

    getDeck(row) {
      return this.getDecks().eq(row);
    }

    getCardDiv(row, col) {
      return this.getDeck(row).find('.card-front').eq(col);
    }

    getChipStack(col) {
      return $('#chips').find('.chip-stack').eq(col);
    }

    getPlayer(id) {
      return $('#players').find('.player').eq(id);
    }

    getPlayerMinicard(id, col) {
      return this.getPlayer(id).find('.cards .minicard').eq(col);
    }

    getPlayerChip(id, col) {
      return this.getPlayer(id).find('.chips .chip').eq(col);
    }

    getPlayerScore(id) {
      return this.getPlayer(id).find('.score');
    }

    getCardFrontImg(id) {
      let fmt = id.toString().padStart(2, '0');
      return `url(img/card-front-${fmt}.webp)`;
    }

    getCardHeaderImg(color) {
      return `url(img/card-header-${color}.webp`;
    }

    getChipImg(color) {
      return `url(img/chip-${color}.webp)`;
    }

    getDigitImg(digit) {
      return `url(img/digit-${digit}.webp)`;
    }

    getGemImg(color) {
      return `url(img/gem-${color}.webp`;
    }

    getMinicardImg(color) {
      return `url(img/minicard-${color}.webp)`;
    }

    getNobleImg(id) {
      return `url(img/noble-${id}.webp)`;
    }

    createCardCosts(elem, costs) {
      let i = 0;
      for (let color = 0; color < NUM_COLORS; color++) {
        let cost = costs[color];
        if (cost) {
          let img = this.getDigitImg(cost) + ', ' + this.getMinicardImg(color);
          elem.find('.cost').eq(i).css('background-image', img);
          i++;
        }
      }

      // hide other cost circles
      elem.find('.cost').slice(i).css('visibility', 'hidden');
    }

    createCard(id, data) {
      let div = cardFrontStub.clone();
      let costs = data[0];
      let color = data[1];
      let points = data[2];
      let bg = data[3];
      div.css('background-image', this.getCardFrontImg(bg));
      div.find('.header').css('background-image', this.getCardHeaderImg(color));
      div.find('.gem').css('background-image', this.getGemImg(color));
      if (points) {
        div.find('.points').css('background-image', this.getDigitImg(points));
      }
      div.find('.id').text('#' + id);
      this.createCardCosts(div.find('.costs'), costs);
      return div;
    }

    createCards() {
      if (this.cards == undefined) {
        this.cards = [ [] ]; // Cărțile sînt indexate de la 1.
        for (let id = 1; id < CARDS.length; id++) {
          this.cards.push(this.createCard(id, CARDS[id]));
        }
      }
    }

    createDecks() {
      for (let row = 0; row < NUM_LEVELS; row++) {
        let div = this.getDeck(row);
        div.empty();
        let back = cardBackStub.clone();
        back.addClass(`card-back-${row+1}`);
        div.append(back);

        for (let c = 0; c < NUM_FACE_UP_CARDS; c++) {
          let card = this.game.board.decks[row].faceUp[c];
          let cardDiv = this.cards[card.id].clone();
          div.append(cardDiv);
        }
      }
    }

    createChips() {
      $('#chips').empty();
      let self = this;
      for (let col = 0; col <= NUM_COLORS; col++) {
        let s = chipStackStub.clone();
        s.find('.chip').each(function(i) {
          $(this).css('background-image', self.getChipImg(col));
          $(this).css('bottom', CHIP_STACK_HEIGHT * i);
          $(this).css('z-index', i);
        });
        s.appendTo('#chips');
      }
    }

    createNobles() {
      if (this.nobles == undefined) {
        this.nobles = [ [] ]; // Nobilii sînt indexați de la 1.
        for (let id = 1; id < NOBLES.length; id++) {
          this.nobles.push(this.createNoble(id));
        }
      }
    }

    createNoble(id) {
      let n = nobleStub.clone();
      n.css('background-image', this.getNobleImg(id));
      n.find('.id').text('#' + id);
      n.attr('data-id', id);
      for (let col = 0; col < NUM_COLORS; col++) {
        let cost = NOBLES[id][col];
        if (cost) {
          let mc = minicardStub.clone();
          let imgs = this.getDigitImg(cost) + ', ' + this.getMinicardImg(col);
          mc.css('background-image', imgs);
          n.find('.costs').append(mc);
        }
      }
      return n;
    }

    deleteBoardNoble(id) {
      $(`#nobles .noble[data-id=${id}]`).remove();
    }

    addPlayerNoble(playerId, nobleId) {
      let container = this.getPlayer(playerId).find('.nobles');
      let div = this.nobles[nobleId].clone();
      container.append(div);
    }

    createPlayerCards(elem) {
      for (let col = 0; col < NUM_COLORS; col++) {
        elem.append(minicardStub.clone());
      }
    }

    createPlayers() {
      $('#players').empty();
      for (const p of this.game.players) {
        let div = playerStub.clone();
        div.find('.name').text(p.name);
        this.createPlayerCards(div.find('.cards'));
        $('#players').append(div);
      }
    }

    updateNobles() {
      $('#nobles').empty();
      for (const id of this.game.board.nobles) {
        let div = this.nobles[id].clone();
        $('#nobles').append(div);
      }
    }

    updatePlayerCard(p, col) {
      let div = this.getPlayerMinicard(p, col);
      let cnt = this.game.players[p].cards[col];

      let digit = this.getDigitImg(cnt);
      let bg = this.getMinicardImg(col);
      let imgs = cnt ? (digit + ', ' + bg) : bg;

      div.css('background-image', imgs);
      div.css('opacity', cnt ? 1 : 0.3);
    }

    updatePlayerChip(p, col) {
      let div = this.getPlayerChip(p, col);
      let cnt = this.game.players[p].chips[col];

      let digit = this.getDigitImg(cnt);
      let bg = this.getChipImg(col);
      let imgs = cnt ? (digit + ', ' + bg) : bg;

      div.css('background-image', imgs);
      div.css('visibility', cnt ? 'visible' : 'hidden');
    }

    updatePlayerCardsAndChips() {
      for (let p = 0; p < this.game.players.length; p++) {
        for (let col = 0; col < NUM_COLORS; col++) {
          this.updatePlayerCard(p, col);
        }
        for (let col = 0; col <= NUM_COLORS; col++) {
          this.updatePlayerChip(p, col);
        }
      }
    }

    updatePlayerScore(p) {
      let score = this.game.players[p].score;
      this.getPlayerScore(p).text(score);
    }

    updateCard(row, col) {
      let card = this.game.board.decks[row].faceUp[col];
      let existingDiv = this.getCardDiv(row, col);
      if (card) {
        let div = this.cards[card.id].clone();
        existingDiv.replaceWith(div);
      } else {
        existingDiv.css('visibility', 'hidden');
      }
    }

    updateCardBack(deck) {
      let cnt = this.game.board.decks[deck].faceDown.length;
      let div = this.getDeck(deck).find('.card-back');
      if (cnt) {
        div.css('visibility', 'visible');
        div.find('.counter').text(cnt);
      } else {
        div.css('visibility', 'hidden');
      }
    }

    drawDecks() {
      for (let d = 0; d < this.game.board.decks.length; d++) {
        this.updateCardBack(d);
      }
    }

    drawChipStack(col) {
      let cnt = this.game.board.chips[col];
      let s = this.getChipStack(col);
      s.find('.counter').text(cnt);
      s.find('.chip').each(function(i) {
        // Afișează întotdeauna primul chip, dar posibil transparent.
        let disp = (!i || (i < cnt)) ? 'block' : 'none';
        $(this).css('display', disp);
        $(this).css('opacity', cnt ? 1 : 0.3);
      });
    }

    drawChips() {
      for (let col = 0; col <= NUM_COLORS; col++) {
        this.drawChipStack(col);
      }
    }

    drawAll() {
      this.drawDecks();
      this.drawChips();
    }

    logMessage(name, msg) {
      let elem = kibitzStub.clone();
      elem.find('.name').text(name);
      elem.find('.message').text(msg);
      $('#log').append(elem);
    }

    logKibitz(playerId, msg) {
      let name = this.game.players[playerId].name;
      this.logMessage(name, msg);
    }
  }

  // Menține logica acestei clase sincronizată cu arbiter/lib/SaveGameTurn.php.
  class Viewer {
    game;
    ui;
    rounds;
    curRound;
    curPlayer;

    constructor(data) {
      this.game = new Game(data);
      this.ui = new UI(this.game);
      this.rounds = data.rounds;
      this.curRound = 0;
      this.curPlayer = 0;

      this.ui.drawAll();
      this.updateRound();
    }

    updateRound() {
      if (!this.isOver()) {
        $('.controls .cur-round').text(1 + this.curRound);
        $('.controls .num-rounds').text(this.rounds.length);
      }
    }

    isOver() {
      return (this.curRound == this.rounds.length);
    }

    getCurPlayer() {
      return this.game.players[this.curPlayer];
    }

    logKibitzes(kibitzes) {
      for (const k of kibitzes) {
        this.ui.logKibitz(this.curPlayer, k);
      }
    }

    logArbiterMessage(msg) {
      if (msg) {
        this.ui.logMessage('Arbitru', msg);
      }
    }

    modifyBoardChips(color, delta) {
      this.game.board.chips[color] += delta;
      this.ui.drawChipStack(color);
    }

    modifyPlayerChips(color, delta) {
      this.getCurPlayer().chips[color] += delta;
      this.ui.updatePlayerChip(this.curPlayer, color);
    }

    gainPoints(delta) {
      this.getCurPlayer().score += delta;
      this.ui.updatePlayerScore(this.curPlayer);
    }

    gainCardCount(color) {
      this.getCurPlayer().cards[color]++;
      this.ui.updatePlayerCard(this.curPlayer, color);
    }

    gainCardFromReserve() {
    }

    gainCardFromBoard(id) {
      let pos = this.game.board.findCard(id);
      this.game.replaceCard(pos.row, pos.col);
      this.ui.updateCard(pos.row, pos.col);
      this.ui.updateCardBack(pos.row);
    }

    gainCard(id) {
      let card = new Card(id);
      this.gainPoints(card.points);
      this.gainCardCount(card.color);

      if (false) { /* todo: find in reserve */
        this.gainCardFromReserve(id);
      } else {
        this.gainCardFromBoard(id);
      }
    }

    actionTake(qty) {
      for (let c = 0; c < NUM_COLORS; c++) {
        this.modifyBoardChips(c, -qty[c]);
        this.modifyPlayerChips(c, +qty[c]);
      }
    }

    gainNoble(id) {
      this.ui.deleteBoardNoble(id);
      this.ui.addPlayerNoble(this.curPlayer, id);
      this.gainPoints(NOBLE_POINTS);
    }

    actionBuy(id, nobleId, cost) {
      for (let c = 0; c <= NUM_COLORS; c++) {
        this.modifyBoardChips(c, +cost[c]);
        this.modifyPlayerChips(c, -cost[c]);
      }
      this.gainCard(id);
      if (nobleId) {
        this.gainNoble(nobleId);
      }
    }

    executeAction(tokens) {
      let type = tokens.shift();
      switch (type) {
        case 1:
        case 2:
          this.actionTake(tokens);
          break;
        case 4:
          let id = tokens.shift();
          let nobleId = tokens.shift();
          this.actionBuy(id, nobleId, tokens);
          break;
      }
    }

    returnChips(colors) {
      for (const c of colors) {
        this.modifyBoardChips(c, +1);
        this.modifyPlayerChips(c, -1);
      }
    }

    moveForward() {
      if (this.isOver()) {
        return;
      }

      let move = this.rounds[this.curRound][this.curPlayer];
      this.logKibitzes(move.kibitzes);
      this.logArbiterMessage(move.arbiterMsg);
      this.executeAction(move.tokens);
      this.returnChips(move.returns);

      if (++this.curPlayer == this.game.players.length) {
        this.curRound++;
        this.curPlayer = 0;
        this.updateRound();
      }
    }
  }

  init();

  function init() {
    $('#file-field').on('change', fileUploaded);
    $('#btn-forward').on('click', moveForward);
    $(document).on('keydown', keyHandler);
    loadGameFile(SAMPLE_GAME);
  }

  function loadGameFile(json) {
    let data = JSON.parse(json);
    viewer = new Viewer(data);
  }

  // Vezi https://stackoverflow.com/a/39515846/6022817.
  function fileUploaded(evt) {
    let fl_file = evt.target.files[0];
    let reader = new FileReader();

    let display_file = ( e ) => {
      loadGameFile(e.target.result);
    };

    let on_reader_load = () => {
      return display_file;
    };

    reader.onload = on_reader_load();
    reader.readAsText(fl_file);
  }

  function keyHandler(e) {
    let captured = true;
    switch (e.which) {
      case 39: moveForward(); break;     // right arrow
      default: captured = false;
    }
    if (captured) {
      // let other keys work as expected (enter, tab etc.)
      e.preventDefault();
    }
  }

  function moveForward() {
    viewer.moveForward();
  }

});
