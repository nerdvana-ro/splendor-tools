$(function() {

  let viewer = null;
  let lastLoadedJson = null;
  let cardBackStub = $('#card-back-stub').detach().removeAttr('id');
  let cardFrontStub = $('#card-front-stub').detach().removeAttr('id');
  let chipStub = $('#chip-stub').detach().removeAttr('id');
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

    drawCard() {
      return this.faceDown.shift();
    }

    replaceCard(col) {
      this.faceUp[col] = this.faceDown.length
        ? this.drawCard()
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
          let card = this.decks[r].faceUp[c];
          if (card && (card.id == id)) {
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
    reserve;
    score;

    constructor(name) {
      this.name = name;
      this.chips = Array(NUM_COLORS + 1).fill(0);
      this.cards = Array(NUM_COLORS).fill(0);
      this.reserve = [];
      this.score = 0;
    }

    reserveCard(id) {
      this.reserve.push(id);
    }

    // Returnează poziția pe care a găsit cartea sau null dacă nu a găsit-o.
    findAndDeleteReservedCard(id) {
      let pos = this.reserve.indexOf(id);
      if (pos == -1) {
        return null;
      } else {
        this.reserve.splice(pos, 1);
        return pos;
      }
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
      this.createChipStacks();
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

    getNoble(id) {
      return $(`#nobles .noble[data-id=${id}]`);
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

    cloneChip(color) {
      let elem = chipStub.clone();
      elem.css('background-image', this.getChipImg(color));
      return elem;
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

    createChipStacks() {
      $('#chips').empty();
      for (let col = 0; col <= NUM_COLORS; col++) {
        let s = chipStackStub.clone();
        for (let i = 0; i < MAX_CHIPS_PER_STACK; i++) {
          let div = this.cloneChip(col);
          div.css({
            'bottom': CHIP_HEIGHT * i,
            'z-index': i,
          });
          div.appendTo(s);
        }
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
      this.getNoble(id).remove();
    }

    addPlayerNoble(playerId, nobleId) {
      let container = this.getPlayer(playerId).find('.nobles');
      let div = this.nobles[nobleId].clone();
      container.append(div);
    }

    addPlayerReserve(playerId, cardId, secret) {
      let container = this.getPlayer(playerId).find('.reserve');
      let div = this.cards[cardId].clone();
      if (secret) {
        div.addClass('secret');
      }
      container.append(div);
    }

    deletePlayerReserve(playerId, pos) {
      let container = this.getPlayer(playerId).find('.reserve');
      container.find('.card').eq(pos).remove();
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

    paintDecks() {
      for (let d = 0; d < this.game.board.decks.length; d++) {
        this.updateCardBack(d);
      }
    }

    paintChipStack(col) {
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

    paintChips() {
      for (let col = 0; col <= NUM_COLORS; col++) {
        this.paintChipStack(col);
      }
    }

    paintAll() {
      this.paintDecks();
      this.paintChips();
      $('#log').empty();
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

      this.ui.paintAll();
      this.updateRoundAndPlayer();
    }

    updateRoundAndPlayer() {
      if (!this.isOver()) {
        $('.controls .cur-round').text(1 + this.curRound);
        $('.controls .num-rounds').text(this.rounds.length);
        $('.player .turn-marker').css('visibility', 'hidden');
        let pl = this.ui.getPlayer(this.curPlayer);
        pl.find('.turn-marker').css('visibility', 'visible');
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

    animate(obj, src, dest) {
      obj.addClass('animated');
      obj.css('position', 'absolute');
      obj.css(src.offset());
      obj.appendTo('body');

      obj.animate(dest.offset(), {
        done: function() {
          obj.remove();
        },
      });
    }

    animateChips(color, qty) {
      if (qty) {
        let elem = this.ui.cloneChip(color);
        let src = this.ui.getChipStack(color);
        let dest = this.ui.getPlayerChip(this.curPlayer, color);
        if (qty < 0) {
          [src, dest] = [dest, src];
        }
        this.animate(elem, src, dest);
      }
    }

    animateFaceupCard(row, col) {
      let src = this.ui.getCardDiv(row, col);
      let elem = src.clone();
      let dest = this.ui.getPlayer(this.curPlayer).find('.reserve');
      this.animate(elem, src, dest);
    }

    animateCardBack(row) {
      let src = this.ui.getDeck(row).find('.card-back');
      let elem = src.clone();
      let dest = this.ui.getPlayer(this.curPlayer).find('.reserve');
      this.animate(elem, src, dest);
    }

    animateReservedCard(id) {
      let elem = this.ui.cards[id].clone();
      let src = this.ui.getPlayer(this.curPlayer).find('.reserve');
      let dest = this.ui.getPlayer(this.curPlayer);
      this.animate(elem, src, dest);
    }

    animateNoble(id) {
      let src = this.ui.getNoble(id);
      let elem = src.clone();
      let dest = this.ui.getPlayer(this.curPlayer).find('.nobles');
      this.animate(elem, src, dest);
    }

    // Transferă qty jetoane de culoarea color de pe masă la jucător. Dacă
    // valoarea este pozitivă, jucătorul ia jetoane. Dacă valoarea este
    // negativă, jucătorul plătește jetoane.
    transferChips(color, qty) {
      this.game.board.chips[color] -= qty;
      this.getCurPlayer().chips[color] += qty;
      this.ui.paintChipStack(color);
      this.ui.updatePlayerChip(this.curPlayer, color);
      this.animateChips(color, qty);
    }

    gainPoints(delta) {
      this.getCurPlayer().score += delta;
      this.ui.updatePlayerScore(this.curPlayer);
    }

    gainCardCount(color) {
      this.getCurPlayer().cards[color]++;
      this.ui.updatePlayerCard(this.curPlayer, color);
    }

    deletePlayerReserve(pos) {
      this.ui.deletePlayerReserve(this.curPlayer, pos);
    }

    replaceCardFromBoard(id) {
      let pos = this.game.board.findCard(id);
      this.animateFaceupCard(pos.row, pos.col);
      this.game.replaceCard(pos.row, pos.col);
      this.ui.updateCard(pos.row, pos.col);
      this.ui.updateCardBack(pos.row);
    }

    gainCard(id) {
      let card = new Card(id);
      this.gainPoints(card.points);
      this.gainCardCount(card.color);

      let pos = this.getCurPlayer().findAndDeleteReservedCard(id);
      if (pos == null) {
        this.replaceCardFromBoard(id);
      } else {
        this.deletePlayerReserve(pos);
        this.animateReservedCard(id);
      }
    }

    actionTake(qty) {
      for (let c = 0; c < NUM_COLORS; c++) {
        this.transferChips(c, qty[c]);
      }
    }

    actionReserve(id) {
      let secret = false;
      if (id > 0) {
        this.replaceCardFromBoard(id);
      } else {
        let row = -id - 1;
        let card = this.game.board.decks[row].drawCard();
        id = card.id;
        secret = true;
        this.ui.updateCardBack(row);
        this.animateCardBack(row);
      }

      this.getCurPlayer().reserveCard(id);
      this.ui.addPlayerReserve(this.curPlayer, id, secret);
      if (this.game.board.chips[NUM_COLORS]) {
        this.transferChips(NUM_COLORS, 1);
      }
    }

    grantNoble(id) {
      if (id) {
        this.animateNoble(id);
        this.ui.deleteBoardNoble(id);
        this.ui.addPlayerNoble(this.curPlayer, id);
        this.gainPoints(NOBLE_POINTS);
      }
    }

    actionBuy(id, cost) {
      for (let c = 0; c <= NUM_COLORS; c++) {
        this.transferChips(c, -cost[c]);
      }
      this.gainCard(id);
    }

    executeAction(tokens) {
      let type = tokens.shift();
      switch (type) {
        case 1:
        case 2:
          this.actionTake(tokens);
          break;
        case 3:
          this.actionReserve(tokens.shift());
          break;
        case 4:
          let id = tokens.shift();
          this.actionBuy(id, tokens);
          break;
      }
    }

    returnChips(colors) {
      for (const c of colors) {
        this.transferChips(c, -1);
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
      this.grantNoble(move.nobleId);

      if (++this.curPlayer == this.game.players.length) {
        this.curRound++;
        this.curPlayer = 0;
      }
      this.updateRoundAndPlayer();
    }
  }

  init();

  function init() {
    $('#file-field').on('change', fileUploaded);
    $('#btn-forward').on('click', moveForward);
    $('#btn-replay').on('click', loadGame);
    $(document).on('keydown', keyHandler);
  }

  function loadGame() {
    let data = JSON.parse(lastLoadedJson);
    viewer = new Viewer(data);
    $('.round-info').css('visibility', 'visible');
    $('.controls button').css('visibility', 'visible');
    $('.columns').css('visibility', 'visible');
  }

  // Vezi https://stackoverflow.com/a/39515846/6022817.
  function fileUploaded(evt) {
    let fl_file = evt.target.files[0];
    let reader = new FileReader();

    let display_file = ( e ) => {
      lastLoadedJson = e.target.result;
      loadGame();
    };

    let on_reader_load = () => {
      return display_file;
    };

    reader.onload = on_reader_load();
    reader.readAsText(fl_file);

    // Dacă utilizatorul încarcă un alt fișier cu același nume, evenimentul
    // change nu se mai declanșează. Pentru a preveni asta, șterge cîmpul.
    $("#file-field").val('');
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
