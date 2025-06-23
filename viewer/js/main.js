$(function() {

  let ui = null;
  let cardBackStub = $('#card-back-stub').detach().removeAttr('id');
  let cardFrontStub = $('#card-front-stub').detach().removeAttr('id');
  let chipStackStub = $('#chip-stack-stub').detach().removeAttr('id');
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
  }

  class Player {
    name;
    chips;
    cards;

    constructor(name) {
      this.name = name;
      this.chips = Array(NUM_COLORS + 1).fill(0);
      this.cards = Array(NUM_COLORS).fill(0);
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

    getCard(row, col) {
      return this.board.decks[row].faceUp[col];
    }
  }

  class UI {
    game;
    cards;

    constructor(game) {
      this.game = game;
      this.createCards();
      this.createDecks();
      this.createChips();
      this.createNobles();
      this.createPlayers();
      this.updatePlayerCardsAndChips();
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
      this.createCardCosts(div.find('.costs'), costs);
      return div;
    }

    createCards() {
      if (this.cards == undefined) {
        this.cards = [ [] ]; // cărțile sînt indexate de la 1.
        for (let id = 1; id < CARDS.length; id++) {
          this.cards.push(this.createCard(id, CARDS[id]));
        }
      }
    }

    createDecks() {
      for (let row = 1; row <= NUM_LEVELS; row++) {
        let deck = NUM_LEVELS - row;
        let div = this.getDeck(row - 1);
        div.empty();
        let back = cardBackStub.clone();
        back.addClass(`card-back-${row}`);
        div.append(back);

        for (let c = 0; c < NUM_FACE_UP_CARDS; c++) {
          let card = game.board.decks[deck].faceUp[c];
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
      $('#nobles').empty();
      for (const id of game.board.nobles) {
        this.createNoble(id);
      }
    }

    createNoble(id) {
      let n = nobleStub.clone();
      n.css('background-image', this.getNobleImg(id));
      n.attr('data-id', id);
      n.appendTo('#nobles');
      for (let col = 0; col < NUM_COLORS; col++) {
        let cost = NOBLES[id][col];
        if (cost) {
          let mc = minicardStub.clone();
          let imgs = this.getDigitImg(cost) + ', ' + this.getMinicardImg(col);
          mc.css('background-image', imgs);
          n.find('.costs').append(mc);
        }
      }
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

    updatePlayerCard(p, col) {
      let div = this.getPlayerMinicard(p, col);
      let cnt = this.game.players[p].cards[col];
      cnt = col;

      let digit = this.getDigitImg(cnt);
      let bg = this.getMinicardImg(col);
      let imgs = cnt ? (digit + ', ' + bg) : bg;

      div.css('background-image', imgs);
      div.css('opacity', cnt ? 1 : 0.3);
    }

    updatePlayerChip(p, col) {
      let div = this.getPlayerChip(p, col);
      let cnt = this.game.players[p].chips[col];
      cnt = col;

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

    drawCardBack(deck) {
      let cnt = game.board.decks[deck].faceDown.length;
      let div = this.getDeck(deck).find('.card-back');
      if (cnt) {
        div.css('visibility', 'visible');
        div.find('.counter').text(cnt);
      } else {
        div.css('visibility', 'hidden');
      }
    }

    drawDecks() {
      for (let d = 0; d < game.board.decks.length; d++) {
        this.drawCardBack(d);
      }
    }

    drawChipStack(col) {
      let cnt = game.board.chips[col];
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
  }

  init();

  function init() {
    $('#file-field').on('change', fileUploaded);
    loadGameFile(SAMPLE_GAME);
  }

  function loadGameFile(json) {
    let data = JSON.parse(json);
    game = new Game(data);
    ui = new UI(game);
    ui.drawAll();
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

});
