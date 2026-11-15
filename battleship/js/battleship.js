/**
 * File: plugins/battleship/js/battleship.js
 * Modern Battleship client — refined UI + full game logic
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('battleship-game');
    if (!root) return;

    // DOM
    var myBoardEl    = document.getElementById('battleship-my-board');
    var enemyBoardEl = document.getElementById('battleship-enemy-board');
    var placeBoard   = document.getElementById('battleship-place-board');
    var placeWrap    = document.getElementById('battleship-placement');
    var statusEl     = document.getElementById('battleship-status');
    var waitEl       = document.getElementById('battleship-waiting');
    var statsEl      = document.getElementById('battleship-stats');
    var surBtn       = document.getElementById('btn-surrender');
    var remainingEl  = document.getElementById('battleship-remaining');
    var toastEl      = document.getElementById('battleship-toast');
    var msgMyEl      = document.getElementById('msg-my');
    var msgEnemyEl   = document.getElementById('msg-enemy');
    var confirmBtn   = document.getElementById('btn-confirm');

    var AJAX = root.dataset.ajaxUrl;
    var XK   = root.dataset.xk;
    var POLL = parseInt(root.dataset.pollInterval, 10) || 2200;

    var L = {
      yourTurn:     root.dataset.langYourTurn,
      enemyTurn:    root.dataset.langEnemyTurn,
      win:          root.dataset.langWin,
      lose:         root.dataset.langLose,
      notYourTurn:  root.dataset.langNotYourTurn,
      alreadyShot:  root.dataset.langAlreadyShot,
      unknown:      root.dataset.langUnknown,
      waiting:      root.dataset.langWaiting,
      placeHint:    root.dataset.langPlaceHint,
      notAllPlaced: root.dataset.langNotAllPlaced,
      confirmSurr:  root.dataset.langConfirmSurrender || 'Сдаться?',
      aiThinking:   root.dataset.langAiThinking || 'Компьютер думает...',
      shipsLeft:    root.dataset.langShipsLeft || 'Осталось:'
    };

    function pool(str) {
      return (str || '').split('|').map(function (s) { return s.trim(); }).filter(Boolean);
    }

    var MSG = {
      hit:      pool(root.dataset.langMsgHit),
      miss:     pool(root.dataset.langMsgMiss),
      sunk:     pool(root.dataset.langMsgSunk),
      damaged:  pool(root.dataset.langMsgDamaged),
      win:      pool(root.dataset.langMsgWin),
      lose:     pool(root.dataset.langMsgLose),
      yourTurn: pool(root.dataset.langMsgYourTurn),
      enemyTurn:pool(root.dataset.langMsgEnemyTurn)
    };

    function pick(arr, fallback) {
      if (!arr || !arr.length) return fallback || '';
      return arr[Math.floor(Math.random() * arr.length)];
    }

    var SIZES = [4, 3, 3, 2, 2, 2, 1, 1, 1, 1];
    var LETTERS = 'ABCDEFGHIJ'.split('');
    var gid = null, mode = null, mySide = 0;
    var mb = [], ms = {}, es = {}, sE = [], sM = [];
    var myTurn = true, poll = null, busy = false;
    var pl = { active: false, horiz: true, board: [], rem: [] };

    // ---------- Helpers ----------
    function setStatus(msg, type) {
      if (!statusEl) return;
      var span = statusEl.querySelector('span');
      if (span) span.textContent = msg;
      else statusEl.textContent = msg;
      statusEl.className = 'alert d-flex align-items-center gap-2 alert-' + (type || 'info');
    }

    // Message queues: one active message per target, rest wait
    var msgQueues = { me: [], enemy: [], center: [] };
    var msgBusy = { me: false, enemy: false, center: false };
    var msgTimers = { me: null, enemy: null, center: null };

    function showOnTarget(target, msg, type, duration) {
      duration = duration || 2600;
      type = type || 'secondary';

      if (target === 'center') {
        if (!toastEl) return;
        toastEl.textContent = msg;
        toastEl.className = 'battleship-toast show ' + type;
        clearTimeout(msgTimers.center);
        msgTimers.center = setTimeout(function () {
          toastEl.classList.remove('show');
          msgBusy.center = false;
          pumpQueue('center');
        }, duration);
        return;
      }

      var el = target === 'me' ? msgMyEl : msgEnemyEl;
      if (!el) return;
      el.innerHTML = '<span class="bs-msg-inner">' + msg + '</span>';
      el.className = 'bs-board-msg show ' + type;
      clearTimeout(msgTimers[target]);
      msgTimers[target] = setTimeout(function () {
        el.classList.remove('show');
        // clear content after fade
        setTimeout(function () {
          if (!el.classList.contains('show')) el.innerHTML = '';
        }, 220);
        msgBusy[target] = false;
        pumpQueue(target);
      }, duration);
    }

    function pumpQueue(target) {
      if (msgBusy[target]) return;
      var q = msgQueues[target];
      if (!q.length) return;
      msgBusy[target] = true;
      var item = q.shift();
      showOnTarget(target, item.msg, item.type, item.duration);
    }

    /**
     * Contextual notify
     * target: 'me' | 'enemy' | 'center'
     * Player actions (shots) → enemy board
     * Opponent actions → my board
     * General (win/lose/waiting/errors) → center
     */
    function notify(msg, type, target, duration) {
      if (!msg) return;
      target = target || 'center';
      msgQueues[target].push({ msg: msg, type: type || 'secondary', duration: duration || 2600 });
      // keep queue short
      if (msgQueues[target].length > 4) msgQueues[target].shift();
      pumpQueue(target);
    }

    // backward-compatible alias used in many places
    function toast(msg, type, target) {
      notify(msg, type, target || 'center');
    }

    function api(action, data) {
      var f = new FormData();
      f.append('action', action);
      f.append('x', XK);
      data = data || {};
      Object.keys(data).forEach(function (k) { f.append(k, data[k]); });
      return fetch(AJAX, { method: 'POST', body: f, credentials: 'same-origin' })
        .then(function (r) {
          return r.text().then(function (txt) {
            try { return JSON.parse(txt); }
            catch (e) {
              console.error('[battleship] non-JSON:', txt);
              throw new Error('Сервер вернул не-JSON');
            }
          });
        });
    }

    function emptyBoard() {
      var b = [];
      for (var i = 0; i < 10; i++) {
        var r = [];
        for (var j = 0; j < 10; j++) r.push(0);
        b.push(r);
      }
      return b;
    }

    function keySet(arr) {
      var s = {};
      (arr || []).forEach(function (p) { s[p[0] + '_' + p[1]] = true; });
      return s;
    }

    function canPlace(b, sx, sy, sz, h) {
      for (var i = 0; i < sz; i++) {
        var cx = sx + (h ? i : 0), cy = sy + (h ? 0 : i);
        if (cx > 9 || cy > 9 || b[cx][cy] !== 0) return false;
        for (var dx = -1; dx <= 1; dx++) {
          for (var dy = -1; dy <= 1; dy++) {
            var nx = cx + dx, ny = cy + dy;
            if (nx >= 0 && nx <= 9 && ny >= 0 && ny <= 9 && b[nx][ny] !== 0) return false;
          }
        }
      }
      return true;
    }

    // ---------- Board with integrated axis labels ----------
    function buildBoard(container, interactive, clickFn) {
      container.innerHTML = '';
      // corner
      var corner = document.createElement('div');
      corner.className = 'bs-axis bs-corner';
      container.appendChild(corner);
      // X labels A-J
      for (var i = 0; i < 10; i++) {
        var lx = document.createElement('div');
        lx.className = 'bs-axis';
        lx.textContent = LETTERS[i];
        container.appendChild(lx);
      }
      // rows
      for (var y = 0; y < 10; y++) {
        var ly = document.createElement('div');
        ly.className = 'bs-axis';
        ly.textContent = (y + 1);
        container.appendChild(ly);
        for (var x = 0; x < 10; x++) {
          (function (x, y) {
            var c = document.createElement('button');
            c.type = 'button';
            c.className = 'bs-cell';
            c.dataset.x = x;
            c.dataset.y = y;
            if (!interactive) c.disabled = true;
            if (clickFn) c.addEventListener('click', function () { clickFn(x, y); });
            container.appendChild(c);
          })(x, y);
        }
      }
    }

    function cellAt(boardEl, x, y) {
      // grid: corner + 10 labels + (for each of 10 rows: 1 label + 10 cells)
      // index = 1 + 10 + y * 11 + 1 + x = 12 + y*11 + x
      var idx = 12 + y * 11 + x;
      return boardEl.children[idx] || null;
    }

    // ---------- Placement ----------
    function renderPlaceBoard() {
      if (!placeBoard) return;
      buildBoard(placeBoard, true, placeShip);
      // apply current ships
      for (var x = 0; x < 10; x++) {
        for (var y = 0; y < 10; y++) {
          if (pl.board[x][y] > 0) {
            var c = cellAt(placeBoard, x, y);
            if (c) c.classList.add('is-ship');
          }
        }
      }
      // hover preview
      placeBoard.querySelectorAll('.bs-cell').forEach(function (c) {
        c.addEventListener('mouseenter', function () {
          previewShip(+c.dataset.x, +c.dataset.y, true);
        });
        c.addEventListener('mouseleave', function () {
          previewShip(+c.dataset.x, +c.dataset.y, false);
        });
      });
      updateRemaining();
      if (confirmBtn) confirmBtn.disabled = pl.rem.length !== 0;
    }

    function previewShip(x, y, show) {
      if (!pl.active || !pl.rem.length) return;
      placeBoard.querySelectorAll('.bs-cell').forEach(function (c) {
        c.classList.remove('is-preview', 'is-preview-invalid');
      });
      if (!show) return;
      var sz = pl.rem[0];
      var ok = canPlace(pl.board, x, y, sz, pl.horiz);
      for (var i = 0; i < sz; i++) {
        var cx = x + (pl.horiz ? i : 0);
        var cy = y + (pl.horiz ? 0 : i);
        if (cx > 9 || cy > 9) continue;
        var cell = cellAt(placeBoard, cx, cy);
        if (cell) cell.classList.add(ok ? 'is-preview' : 'is-preview-invalid');
      }
    }

    function placeShip(x, y) {
      if (!pl.active || !pl.rem.length) return;
      var sz = pl.rem[0];
      if (!canPlace(pl.board, x, y, sz, pl.horiz)) {
        toast('Сюда нельзя поставить', 'warning');
        return;
      }
      for (var i = 0; i < sz; i++) {
        pl.board[x + (pl.horiz ? i : 0)][y + (pl.horiz ? 0 : i)] = sz;
      }
      pl.rem.shift();
      renderPlaceBoard();
      if (!pl.rem.length) toast('Все корабли расставлены! Нажмите «Готово»', 'success');
    }

    function randomPlace() {
      pl.board = emptyBoard();
      pl.rem = SIZES.slice();
      var attempts = 0;
      while (pl.rem.length && attempts < 2000) {
        attempts++;
        var sz = pl.rem[0];
        var h = Math.random() < 0.5;
        var sx = Math.floor(Math.random() * (10 - (h ? sz - 1 : 0)));
        var sy = Math.floor(Math.random() * (10 - (h ? 0 : sz - 1)));
        if (canPlace(pl.board, sx, sy, sz, h)) {
          for (var i = 0; i < sz; i++) {
            pl.board[sx + (h ? i : 0)][sy + (h ? 0 : i)] = sz;
          }
          pl.rem.shift();
        }
      }
      if (pl.rem.length) { randomPlace(); return; }
      renderPlaceBoard();
      toast('Корабли расставлены случайно', 'secondary');
    }

    function clearPlace() {
      pl.board = emptyBoard();
      pl.rem = SIZES.slice();
      renderPlaceBoard();
    }

    function updateRemaining() {
      if (!remainingEl) return;
      if (!pl.rem.length) {
        remainingEl.innerHTML = '<span class="text-success">Все корабли на местах ✓</span>';
        return;
      }
      var counts = {};
      pl.rem.forEach(function (s) { counts[s] = (counts[s] || 0) + 1; });
      var parts = [];
      Object.keys(counts).sort(function (a, b) { return b - a; }).forEach(function (s) {
        parts.push(counts[s] + '×' + s);
      });
      remainingEl.textContent = L.shipsLeft + ' ' + parts.join(', ');
    }

    // ---------- Game boards ----------
    function renderMyBoard() {
      if (!myBoardEl) return;
      buildBoard(myBoardEl, false);
      var sunk = keySet(sM);
      for (var x = 0; x < 10; x++) {
        for (var y = 0; y < 10; y++) {
          var c = cellAt(myBoardEl, x, y);
          if (!c) continue;
          var v = (mb[x] && mb[x][y] !== undefined) ? mb[x][y] : 0;
          var k = x + '_' + y;
          if (v > 0) c.classList.add('is-ship');
          if (v < 0) {
            if (sunk[k]) c.classList.add('is-sunk');
            else c.classList.add('is-hit');
          }
          if (es[k] === 'miss') c.classList.add('is-miss');
          if (es[k] === 'hit' && v >= 0) c.classList.add('is-enemy-hit');
        }
      }
    }

    function renderEnemyBoard() {
      if (!enemyBoardEl) return;
      buildBoard(enemyBoardEl, true, fire);
      var sunk = keySet(sE);
      for (var x = 0; x < 10; x++) {
        for (var y = 0; y < 10; y++) {
          var c = cellAt(enemyBoardEl, x, y);
          if (!c) continue;
          var k = x + '_' + y;
          if (ms[k] === 'hit') {
            if (sunk[k]) c.classList.add('is-sunk');
            else c.classList.add('is-hit');
            c.disabled = true;
          } else if (ms[k] === 'miss') {
            c.classList.add('is-miss');
            c.disabled = true;
          }
        }
      }
    }

    function renderBoards() {
      renderMyBoard();
      renderEnemyBoard();
    }

    // ---------- Game flow ----------
    function applyState(s) {
      mySide = s.my_side;
      mb = s.my_board || [];
      ms = s.my_shots || {};
      es = s.enemy_shots || {};
      sE = s.sunk_enemy || [];
      sM = s.sunk_mine || [];
      myTurn = (s.turn === mySide);
      mode = s.mode || mode;

      if (s.status === 'placing') {
        setStatus(L.waiting, 'warning');
        startPoll();
        return;
      }

      if (s.status === 'finished') {
        stopPoll();
        renderBoards();
        var weWon = !!s.you_won;
        if (weWon) {
          setStatus(pick(MSG.win, L.win), 'success');
          notify(pick(MSG.win, L.win), 'success', 'center');
        } else {
          setStatus(pick(MSG.lose, L.lose), 'danger');
          notify(pick(MSG.lose, L.lose), 'danger', 'center');
        }
        if (surBtn) surBtn.classList.add('d-none');
        return;
      }

      if (s.status === 'active') {
        if (placeWrap) placeWrap.classList.add('d-none');
        renderBoards();
        if (myTurn) {
          setStatus(pick(MSG.yourTurn, L.yourTurn), 'success');
          stopPoll();
        } else {
          setStatus(pick(MSG.enemyTurn, L.enemyTurn), 'info');
          startPoll();
        }
        if (surBtn) surBtn.classList.remove('d-none');
      }
    }

    function startPoll() {
      stopPoll();
      poll = setInterval(function () {
        if (!gid || busy) return;
        api('state', { game_id: gid }).then(function (r) {
          if (r.success) applyState(r);
        }).catch(function () {});
      }, POLL);
    }

    function stopPoll() {
      if (poll) { clearInterval(poll); poll = null; }
    }

    function startGame(m) {
      busy = true;
      api('create', { mode: m }).then(function (r) {
        busy = false;
        if (!r.success) {
          setStatus(r.message || L.unknown, 'danger');
          toast(r.message || L.unknown, 'danger');
          return;
        }
        gid = r.game_id;
        mode = m;
        mb = []; ms = {}; es = {}; sE = []; sM = [];
        pl.board = emptyBoard();
        pl.rem = SIZES.slice();
        pl.horiz = true;
        pl.active = true;

        if (waitEl) waitEl.innerHTML = '<p class="text-body-secondary mb-0">Нет открытых игр. Создайте свою!</p>';
        if (statsEl) statsEl.innerHTML = '<p class="text-body-secondary mb-0">Нажмите «Моя статистика»</p>';
        if (placeWrap) placeWrap.classList.remove('d-none');
        if (surBtn) surBtn.classList.remove('d-none');

        document.getElementById('btn-rotate').onclick = function () {
          pl.horiz = !pl.horiz;
          toast(pl.horiz ? 'Горизонтально' : 'Вертикально', 'secondary');
        };
        document.getElementById('btn-random').onclick = randomPlace;
        document.getElementById('btn-clear').onclick = clearPlace;
        document.getElementById('btn-confirm').onclick = confirmPlace;

        renderPlaceBoard();
        setStatus(L.placeHint, 'primary');
      }).catch(function (e) {
        busy = false;
        toast('Ошибка: ' + e.message, 'danger');
      });
    }

    function confirmPlace() {
      if (pl.rem.length !== 0) {
        setStatus(L.notAllPlaced + pl.rem.length, 'warning');
        toast(L.notAllPlaced + pl.rem.length, 'warning');
        return;
      }
      busy = true;
      api('place', { game_id: gid, board: JSON.stringify(pl.board) }).then(function (r) {
        busy = false;
        if (!r.success) {
          setStatus(r.message || L.unknown, 'danger');
          toast(r.message || L.unknown, 'danger');
          return;
        }
        pl.active = false;
        if (placeWrap) placeWrap.classList.add('d-none');
        mb = pl.board;

        if (mode === 'ai') {
          mySide = 2;
          myTurn = true;
          renderBoards();
          setStatus(pick(MSG.yourTurn, L.yourTurn), 'success');
          toast(pick(MSG.yourTurn, 'Ваш ход!'), 'success');
        } else {
          mySide = 1;
          myTurn = false;
          renderBoards();
          setStatus(L.waiting, 'warning');
          toast(L.waiting, 'secondary');
          startPoll();
        }
      }).catch(function (e) {
        busy = false;
        toast('Ошибка: ' + e.message, 'danger');
      });
    }

    function fire(x, y) {
      if (!gid || busy) return;
      if (!myTurn) {
        setStatus(L.notYourTurn, 'warning');
        toast(L.notYourTurn, 'warning');
        return;
      }
      var k = x + '_' + y;
      if (ms[k]) {
        setStatus(L.alreadyShot, 'warning');
        toast(L.alreadyShot, 'warning');
        return;
      }

      busy = true;
      api('fire', { game_id: gid, x2: x, y2: y }).then(function (r) {
        busy = false;
        if (!r.success) {
          setStatus(r.message || L.unknown, 'danger');
          toast(r.message || L.unknown, 'danger');
          return;
        }

        ms[k] = r.result;
        if (r.sunk_cells && r.sunk_cells.length) {
          r.sunk_cells.forEach(function (p) { sE.push(p); });
        }
        renderEnemyBoard();

        var coord = LETTERS[x] + (y + 1);
        if (r.all_sunk) {
          setStatus(pick(MSG.win, L.win), 'success');
          notify(pick(MSG.sunk, 'Потопил!') + ' ' + pick(MSG.win, L.win), 'success', 'center');
          stopPoll();
          if (surBtn) surBtn.classList.add('d-none');
          return;
        }

        if (r.result === 'hit') {
          if (r.sunk_cells && r.sunk_cells.length) {
            notify(pick(MSG.sunk, 'Потопил!') + ' (' + coord + ')', 'danger', 'enemy');
          } else {
            notify(pick(MSG.hit, 'Попал!') + ' (' + coord + ')', 'success', 'enemy');
          }
        } else {
          notify(pick(MSG.miss, 'Мимо!') + ' (' + coord + ')', 'secondary', 'enemy');
        }

        if (mode === 'ai') {
          myTurn = false;
          setStatus(L.aiThinking, 'info');
          setTimeout(function () {
            api('aimove', { game_id: gid }).then(function (a) {
              if (!a.success) {
                setStatus(a.message || L.unknown, 'danger');
                return;
              }
              var ak = a.ai_shot.x + '_' + a.ai_shot.y;
              es[ak] = a.result;
              if (a.sunk_cells && a.sunk_cells.length) {
                a.sunk_cells.forEach(function (p) {
                  sM.push(p);
                  if (mb[p[0]]) mb[p[0]][p[1]] = -Math.abs(mb[p[0]][p[1]] || 1);
                });
              } else if (a.result === 'hit') {
                if (mb[a.ai_shot.x]) {
                  mb[a.ai_shot.x][a.ai_shot.y] = -Math.abs(mb[a.ai_shot.x][a.ai_shot.y] || 1);
                }
              }
              renderMyBoard();

              var acoord = LETTERS[a.ai_shot.x] + (a.ai_shot.y + 1);
              if (a.all_sunk) {
                setStatus(pick(MSG.lose, L.lose), 'danger');
                notify(pick(MSG.lose, L.lose), 'danger', 'center');
                if (surBtn) surBtn.classList.add('d-none');
                return;
              }
              if (a.result === 'hit') {
                notify('Враг: ' + pick(MSG.hit, 'Попал!') + ' (' + acoord + ')', 'danger', 'me');
              } else {
                notify('Враг: ' + pick(MSG.miss, 'Мимо!') + ' (' + acoord + ')', 'secondary', 'me');
              }
              myTurn = true;
              setStatus(pick(MSG.yourTurn, L.yourTurn), 'success');
            }).catch(function () {
              myTurn = true;
              setStatus(L.yourTurn, 'success');
            });
          }, 600);
        } else {
          myTurn = false;
          setStatus(pick(MSG.enemyTurn, L.enemyTurn), 'info');
          startPoll();
        }
      }).catch(function (e) {
        busy = false;
        toast('Ошибка: ' + e.message, 'danger');
      });
    }

    function listWaiting() {
      api('list').then(function (r) {
        if (!r.success || !waitEl) return;
        if (!r.games || !r.games.length) {
          waitEl.innerHTML = '<p class="text-body-secondary mb-0">Нет открытых игр. Создайте свою!</p>';
          return;
        }
        var html = '<div class="list-group list-group-flush">';
        r.games.forEach(function (g) {
          html += '<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-gid="' + g.game_id + '">' +
            '<span>Игра #' + g.game_id + '</span><small class="text-body-secondary">' + g.created_at + '</small></button>';
        });
        html += '</div>';
        waitEl.innerHTML = html;
        waitEl.querySelectorAll('[data-gid]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            joinGame(+btn.dataset.gid);
          });
        });
      }).catch(function () {});
    }

    function joinGame(id) {
      busy = true;
      api('join', { game_id: id }).then(function (j) {
        busy = false;
        if (!j.success) {
          toast(j.message || L.unknown, 'danger');
          return;
        }
        gid = j.game_id;
        mode = 'pvp';
        mb = []; ms = {}; es = {}; sE = []; sM = [];
        pl.board = emptyBoard();
        pl.rem = SIZES.slice();
        pl.horiz = true;
        pl.active = true;

        if (waitEl) waitEl.innerHTML = '';
        if (placeWrap) placeWrap.classList.remove('d-none');

        document.getElementById('btn-rotate').onclick = function () {
          pl.horiz = !pl.horiz;
          toast(pl.horiz ? 'Горизонтально' : 'Вертикально', 'secondary');
        };
        document.getElementById('btn-random').onclick = randomPlace;
        document.getElementById('btn-clear').onclick = clearPlace;
        document.getElementById('btn-confirm').onclick = confirmPlace;

        renderPlaceBoard();
        setStatus(L.placeHint, 'primary');
        toast('Вы присоединились! Расставьте корабли.', 'success');
      }).catch(function (e) {
        busy = false;
        toast('Ошибка: ' + e.message, 'danger');
      });
    }

    function showStats() {
      api('stats').then(function (r) {
        if (!r.success || !statsEl) return;
        var s = r.stats;
        statsEl.innerHTML =
          '<div class="d-flex flex-wrap gap-4 justify-content-center text-center">' +
          '<div><div class="fs-3 fw-bold text-success">' + s.wins + '</div><div class="small text-body-secondary">Побед</div></div>' +
          '<div><div class="fs-3 fw-bold text-danger">' + s.losses + '</div><div class="small text-body-secondary">Поражений</div></div>' +
          '<div><div class="fs-3 fw-bold text-primary">' + s.rating + '</div><div class="small text-body-secondary">Рейтинг</div></div>' +
          '</div>';
      }).catch(function () {});
    }

    function doSurrender() {
      if (!gid) return;
      if (!confirm(L.confirmSurr)) return;
      busy = true;
      api('surrender', { game_id: gid }).then(function (r) {
        busy = false;
        if (r.success) {
          stopPoll();
          setStatus(pick(MSG.lose, L.lose), 'danger');
          notify(pick(MSG.lose, L.lose), 'danger', 'center');
          if (surBtn) surBtn.classList.add('d-none');
        }
      }).catch(function () { busy = false; });
    }

    // Bind
    var bAi = document.getElementById('btn-mode-ai');
    var bPvp = document.getElementById('btn-mode-pvp');
    var bList = document.getElementById('btn-list');
    var bStats = document.getElementById('btn-stats');
    if (bAi) bAi.addEventListener('click', function () { startGame('ai'); });
    if (bPvp) bPvp.addEventListener('click', function () { startGame('pvp'); });
    if (bList) bList.addEventListener('click', listWaiting);
    if (bStats) bStats.addEventListener('click', showStats);
    if (surBtn) surBtn.addEventListener('click', doSurrender);

    // Initial empty boards
    if (myBoardEl) buildBoard(myBoardEl, false);
    if (enemyBoardEl) buildBoard(enemyBoardEl, false);
  });
})();
