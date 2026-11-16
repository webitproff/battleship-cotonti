/**
 *  File: battleship.js
 *  Path: plugins/battleship/js/battleship.js
 *
 *  Battleship client: lobby, ship placement, battle, log, toasts, polling.
 *  Requires Bootstrap 5.3 (toasts degrade gracefully without its JS).
 *
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP with Chat & Sounds
 *  Version=3.3.3
 *  Date=2026-11-16
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  License: BSD
 */
 

(function () {
	'use strict';

	var app = document.getElementById('bs-app');
	var root = document.getElementById('bs-root');
	if (!app || !root) { return; }

	var cfg;
	try { cfg = JSON.parse(app.getAttribute('data-bsg-config')); } catch (e) { return; }

	var L = cfg.lang || {};
	var LET = 'ABCDEFGHIJ';
	var SIZES = [4, 3, 3, 2, 2, 2, 1, 1, 1, 1];

	cfg.urls = cfg.urls || {};
	function playUrl(id) { return String(cfg.urls.play || '').replace('__ID__', id); }
	function go(url) { window.location.href = url; }

	/* ---------- helpers ---------- */

	function t(key, vars) {
		var s = L[key];
		if (s === undefined) { s = key; }
		if (Array.isArray(s)) { s = s[Math.floor(Math.random() * s.length)]; }
		return String(s).replace(/\{(\w+)\}/g, function (m, k) {
			return (vars && vars[k] !== undefined) ? vars[k] : m;
		});
	}

	function el(tag, cls, text) {
		var e = document.createElement(tag);
		if (cls) { e.className = cls; }
		if (text !== undefined && text !== null) { e.textContent = text; }
		return e;
	}

	function btn(cls, text, onClick) {
		var b = el('button', 'btn ' + cls, text);
		b.type = 'button';
		if (onClick) { b.addEventListener('click', onClick); }
		return b;
	}

	function cellName(r, c) { return LET.charAt(c) + (r + 1); }

	function clear(node) { while (node.firstChild) { node.removeChild(node.firstChild); } }

	function api(action, data) {
		var f = new FormData();
		f.append('action', action);
		f.append('x', cfg.xk);
		Object.keys(data || {}).forEach(function (k) { f.append(k, data[k]); });
		return fetch(cfg.ajax, { method: 'POST', body: f, credentials: 'same-origin' })
			.then(function (r) { return r.text(); })
			.then(function (txt) {
				try { return JSON.parse(txt); } catch (e) { return { success: false, message: t('err_unknown') }; }
			})
			.catch(function () { return { success: false, message: t('err_network') }; });
	}

	function toast(msg, type) {
		var box = document.getElementById('bs-toasts');
		if (!box) { return; }
		var d = el('div', 'toast align-items-center border-0 text-bg-' + (type || 'secondary'));
		d.setAttribute('role', 'status');
		var f = el('div', 'd-flex');
		f.appendChild(el('div', 'toast-body', msg));
		var b = el('button', 'btn-close btn-close-white me-2 m-auto');
		b.type = 'button';
		b.setAttribute('aria-label', 'Close');
		b.addEventListener('click', function () { d.remove(); });
		f.appendChild(b);
		d.appendChild(f);
		box.appendChild(d);
		while (box.children.length > 4) { box.removeChild(box.firstChild); }
		if (window.bootstrap && window.bootstrap.Toast) {
			d.addEventListener('hidden.bs.toast', function () { d.remove(); });
			new window.bootstrap.Toast(d, { delay: 3800 }).show();
		} else {
			d.classList.add('show');
			setTimeout(function () { d.remove(); }, 3800);
		}
	}

	/** Two-step confirmation button. */
	function confirmBtn(cls, label, confirmLabel, action) {
		var timer = null;
		var b = btn(cls, label);
		b.addEventListener('click', function () {
			if (b.getAttribute('data-armed')) {
				clearTimeout(timer);
				action();
				return;
			}
			b.setAttribute('data-armed', '1');
			b.textContent = confirmLabel;
			timer = setTimeout(function () {
				b.removeAttribute('data-armed');
				b.textContent = label;
			}, 3000);
		});
		return b;
	}

	/* ---------- sounds (Web Audio API, no external files) ---------- */

	var Snd = (function () {
		var ctx = null, on = true;
		try { on = window.localStorage.getItem('bsg_sound') !== '0'; } catch (e) { on = true; }

		function ac() {
			if (!ctx) {
				var C = window.AudioContext || window.webkitAudioContext;
				if (!C) { return null; }
				try { ctx = new C(); } catch (e) { return null; }
			}
			if (ctx.state === 'suspended' && ctx.resume) { ctx.resume(); }
			return ctx;
		}

		function tone(freq, start, dur, type, vol, slideTo) {
			var c = ac();
			if (!c) { return; }
			var o = c.createOscillator(), g = c.createGain(), t0 = c.currentTime + start;
			o.type = type || 'sine';
			o.frequency.setValueAtTime(freq, t0);
			if (slideTo) { o.frequency.exponentialRampToValueAtTime(slideTo, t0 + dur); }
			g.gain.setValueAtTime(0.0001, t0);
			g.gain.exponentialRampToValueAtTime(vol || 0.15, t0 + 0.01);
			g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
			o.connect(g);
			g.connect(c.destination);
			o.start(t0);
			o.stop(t0 + dur + 0.02);
		}

		// short burst of band-passed noise: the "squelch" of a radio channel
		function noise(start, dur, vol, freq) {
			var c = ac();
			if (!c) { return; }
			var len = Math.max(1, Math.floor(c.sampleRate * dur));
			var buf = c.createBuffer(1, len, c.sampleRate);
			var data = buf.getChannelData(0);
			for (var i = 0; i < len; i++) { data[i] = (Math.random() * 2 - 1) * (1 - i / len); }
			var src = c.createBufferSource(), f = c.createBiquadFilter(), g = c.createGain(), t0 = c.currentTime + start;
			src.buffer = buf;
			f.type = 'bandpass';
			f.frequency.value = freq || 2200;
			f.Q.value = 0.9;
			g.gain.setValueAtTime(vol || 0.12, t0);
			g.gain.exponentialRampToValueAtTime(0.0001, t0 + dur);
			src.connect(f);
			f.connect(g);
			g.connect(c.destination);
			src.start(t0);
		}

		var sounds = {
			// message sent: key click + Quindar "intro" tone (2525 Hz), like pressing push-to-talk
			send: function () {
				noise(0, 0.025, 0.14, 3000);
				tone(2525, 0.03, 0.16, 'sine', 0.13);
			},
			// message received: channel squelch + Quindar "outro" tone (2475 Hz) + short roger beep
			recv: function () {
				noise(0, 0.11, 0.1, 1800);
				tone(2475, 0.12, 0.1, 'sine', 0.13);
				tone(1320, 0.27, 0.07, 'sine', 0.11);
				tone(1760, 0.36, 0.09, 'sine', 0.11);
			},
			place: function () { tone(900, 0, 0.05, 'square', 0.06); },
			miss: function () { tone(160, 0, 0.16, 'sine', 0.22, 90); },
			hit: function () { tone(700, 0, 0.05, 'square', 0.1); tone(700, 0.09, 0.05, 'square', 0.1); },
			sunk: function () {
				[600, 480, 360, 250, 160].forEach(function (f, i) { tone(f, i * 0.11, 0.14, 'sawtooth', 0.1); });
			},
			win: function () {
				[523, 659, 784, 1047].forEach(function (f, i) { tone(f, i * 0.15, 0.22, 'triangle', 0.16); });
				tone(1047, 0.62, 0.5, 'triangle', 0.16);
			},
			lose: function () {
				[392, 349, 311, 262].forEach(function (f, i) { tone(f, i * 0.22, 0.3, 'triangle', 0.16); });
			}
		};

		// browsers allow audio only after a user gesture
		document.addEventListener('pointerdown', function () { if (on) { ac(); } }, { passive: true });

		return {
			play: function (name) { if (on && sounds[name]) { try { sounds[name](); } catch (e) { /* ignore */ } } },
			isOn: function () { return on; },
			toggle: function () {
				on = !on;
				try { window.localStorage.setItem('bsg_sound', on ? '1' : '0'); } catch (e) { /* ignore */ }
				if (on) { sounds.place(); }
				return on;
			}
		};
	})();

	function soundBtn() {
		var b = btn('btn-outline-secondary btn-sm', '', null);
		function label() { b.textContent = (Snd.isOn() ? '♪ ' + t('sound_on') : '✕ ' + t('sound_off')); }
		label();
		b.addEventListener('click', function () { Snd.toggle(); label(); });
		return b;
	}

	function soundsFor(fresh, g, prev) {
		var delay = 0;
		fresh.forEach(function (m) {
			var k = m.res === 'miss' ? 'miss' : (m.res === 'sunk' ? 'sunk' : 'hit');
			setTimeout(function () { Snd.play(k); }, delay);
			delay += 450;
		});
		if (prev && prev.status !== 'finished' && g.status === 'finished' && g.winner_side !== 0) {
			setTimeout(function () { Snd.play(g.winner_side === g.side ? 'win' : 'lose'); }, delay + 300);
		}
	}

	/* ---------- chat (PvP, only while the game is active) ---------- */

	var chat = { gid: 0, el: null, list: null, empty: null, input: null, send: null, counter: null,
		lastId: 0, first: true, opp: '', timer: null, coolTimer: null, left: 0 };

	function chatOn(g) { return g.status === 'active' && !!g.chat; }

	function chatStop() {
		if (chat.timer) { clearTimeout(chat.timer); chat.timer = null; }
		if (chat.coolTimer) { clearInterval(chat.coolTimer); chat.coolTimer = null; }
		if (chat.el && chat.el.parentNode) { chat.el.parentNode.removeChild(chat.el); }
		chat.gid = 0;
		chat.el = null;
	}

	function chatCounter() {
		if (chat.counter) { chat.counter.textContent = chat.input.value.length + '/' + (cfg.chatMax || 255); }
	}

	function chatCooldown(sec) {
		if (chat.coolTimer) { clearInterval(chat.coolTimer); }
		chat.left = sec;
		function paint() {
			if (!chat.send) { return; }
			if (chat.left > 0) {
				chat.send.disabled = true;
				chat.send.textContent = t('chat_send') + ' (' + chat.left + ')';
			} else {
				chat.send.disabled = false;
				chat.send.textContent = t('chat_send');
			}
		}
		paint();
		chat.coolTimer = setInterval(function () {
			chat.left--;
			paint();
			if (chat.left <= 0) { clearInterval(chat.coolTimer); chat.coolTimer = null; }
		}, 1000);
	}

	function chatAdd(it, notify) {
		if (chat.empty && chat.empty.parentNode) { chat.empty.parentNode.removeChild(chat.empty); chat.empty = null; }
		var near = chat.list.scrollHeight - chat.list.scrollTop - chat.list.clientHeight < 40;
		var li = el('li', 'bsg-msg ' + (it.mine ? 'is-mine' : 'is-theirs'));
		var bubble = el('div', 'bubble', it.text);
		var d = new Date(it.ts * 1000);
		var meta = el('small', '', (it.mine ? t('you') : chat.opp) + ' · ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
		li.appendChild(bubble);
		li.appendChild(meta);
		chat.list.appendChild(li);
		while (chat.list.children.length > 100) { chat.list.removeChild(chat.list.firstChild); }
		if (near || it.mine) { chat.list.scrollTop = chat.list.scrollHeight; }
		if (notify && !it.mine) {
			Snd.play('recv');
			toast(chat.opp + ': ' + (it.text.length > 70 ? it.text.slice(0, 70) + '…' : it.text), 'info');
		}
	}

	function chatSend() {
		var text = chat.input.value.trim();
		if (!text || chat.left > 0 || !chat.gid) { return; }
		var gid = chat.gid;
		chat.send.disabled = true;
		api('chatsend', { game_id: gid, msg: text }).then(function (res) {
			if (chat.gid !== gid) { return; }
			if (!res.success) {
				toast(res.message || t('err_unknown'), 'warning');
				if (res.wait) { chatCooldown(res.wait); } else { chat.send.disabled = false; }
				return;
			}
			chat.input.value = '';
			chatCounter();
			chat.lastId = Math.max(chat.lastId, res.chat.id);
			chatAdd(res.chat, false);
			Snd.play('send');
			chatCooldown(res.cooldown || cfg.chatCooldown || 5);
		});
	}

	function chatPoll(delay) {
		if (chat.timer) { clearTimeout(chat.timer); }
		chat.timer = setTimeout(function () {
			var gid = chat.gid;
			if (!gid) { return; }
			api('chatget', { game_id: gid, after: chat.lastId }).then(function (res) {
				if (chat.gid !== gid) { return; }
				if (res.success && res.closed) { chatStop(); return; }   // battle is over: the chat is erased on the server
				if (res.success) {
					res.items.forEach(function (it) {
						chatAdd(it, !chat.first);
						chat.lastId = Math.max(chat.lastId, it.id);
					});
					chat.first = false;
				}
				chatPoll(2500);
			});
		}, delay);
	}

	function chatBuild(g) {
		chat.gid = g.game_id;
		chat.lastId = 0;
		chat.first = true;
		chat.opp = g.opp;
		chat.left = 0;

		var card = el('div', 'card mb-3');
		var head = el('div', 'card-header d-flex flex-wrap justify-content-between align-items-center gap-1');
		head.appendChild(el('span', 'fw-semibold', t('chat_title')));
		head.appendChild(el('small', 'text-body-secondary', t('chat_note')));
		card.appendChild(head);

		var list = el('ul', 'list-unstyled bsg-chat-list mb-0 p-2');
		var empty = el('li', 'text-body-secondary small', t('chat_empty'));
		list.appendChild(empty);
		card.appendChild(list);

		var form = el('form', 'card-footer');
		var grp = el('div', 'input-group input-group-sm');
		var input = el('input', 'form-control');
		input.type = 'text';
		input.maxLength = cfg.chatMax || 255;
		input.placeholder = t('chat_placeholder');
		input.autocomplete = 'off';
		var send = el('button', 'btn btn-primary', t('chat_send'));
		send.type = 'submit';
		grp.appendChild(input);
		grp.appendChild(send);
		form.appendChild(grp);
		var counter = el('div', 'form-text text-end', '0/' + (cfg.chatMax || 255));
		form.appendChild(counter);
		form.addEventListener('submit', function (e) { e.preventDefault(); chatSend(); });
		input.addEventListener('input', chatCounter);
		card.appendChild(form);

		chat.el = card;
		chat.list = list;
		chat.empty = empty;
		chat.input = input;
		chat.send = send;
		chat.counter = counter;
	}

	/** Returns the persistent chat node (built once per game) or null when chat is not available. */
	function chatNodeFor(g) {
		if (!chatOn(g)) {
			if (chat.el) { chatStop(); }
			return null;
		}
		if (chat.gid !== g.game_id || !chat.el) {
			chatBuild(g);
			chatPoll(300);
		}
		return chat.el;
	}

	/* ---------- state ---------- */

	var S = { view: 'lobby', gid: 0, game: null, seen: 0, log: [], busy: false, pollTimer: null, mounted: 0 };
	var pl = null;

	function stopTimers() {
		if (S.pollTimer) { clearTimeout(S.pollTimer); S.pollTimer = null; }
	}

	function setHeaderActions(nodes) {
		var h = document.getElementById('bs-header-actions');
		if (!h) { return; }
		clear(h);
		nodes.forEach(function (n) { h.appendChild(n); });
	}

	/* ---------- grid with axes ---------- */

	/**
	 * @param {object} o {tag, cls, factory(r,c,cellEl), onHover(r,c)|null}
	 * @returns {{el: HTMLElement, cells: HTMLElement[][]}}
	 */
	function buildGrid(o) {
		var grid = el('div', 'bsg-grid ' + (o.cls || ''));
		var colL = [], rowL = [], cells = [];
		grid.appendChild(el('div', 'bsg-label'));
		for (var c = 0; c < 10; c++) {
			var cl = el('div', 'bsg-label', LET.charAt(c));
			colL.push(cl);
			grid.appendChild(cl);
		}
		for (var r = 0; r < 10; r++) {
			var rl = el('div', 'bsg-label', String(r + 1));
			rowL.push(rl);
			grid.appendChild(rl);
			cells[r] = [];
			for (c = 0; c < 10; c++) {
				var ce = el(o.tag || 'div', 'bsg-cell');
				if (ce.tagName === 'BUTTON') { ce.type = 'button'; }
				ce.setAttribute('data-r', r);
				ce.setAttribute('data-c', c);
				ce.setAttribute('aria-label', cellName(r, c));
				cells[r][c] = ce;
				grid.appendChild(ce);
			}
		}
		var last = null;
		function hl(on, r2, c2) {
			if (r2 === null) { return; }
			rowL[r2].classList.toggle('hl', on);
			colL[c2].classList.toggle('hl', on);
		}
		grid.addEventListener('mouseover', function (e) {
			var ce2 = e.target.closest ? e.target.closest('[data-r]') : null;
			if (!ce2 || !grid.contains(ce2)) { return; }
			var r2 = +ce2.getAttribute('data-r'), c2 = +ce2.getAttribute('data-c');
			if (last) { hl(false, last[0], last[1]); }
			last = [r2, c2];
			hl(true, r2, c2);
			if (o.onHover) { o.onHover(r2, c2); }
		});
		grid.addEventListener('mouseleave', function () {
			if (last) { hl(false, last[0], last[1]); }
			last = null;
			if (o.onLeave) { o.onLeave(); }
		});
		return { el: grid, cells: cells };
	}

	var STATE_CLASSES = ['is-ship', 'is-reveal', 'is-hit', 'is-sunk', 'is-miss', 'is-auto'];

	function paintCell(ce, state) {
		STATE_CLASSES.forEach(function (c) { ce.classList.remove(c); });
		if (state) { ce.classList.add('is-' + state); }
	}

	/* ---------- legend ---------- */

	function legendCard() {
		var card = el('div', 'card mb-3');
		var head = el('div', 'card-header fw-semibold', t('legend_title'));
		var body = el('div', 'card-body py-2');
		var items = [
			['water', ''], ['ship', 'is-ship'], ['hit', 'is-hit'], ['sunk', 'is-sunk'],
			['miss', 'is-miss'], ['auto', 'is-auto'], ['ok', 'is-ok'], ['bad', 'is-bad']
		];
		var list = el('div', 'row row-cols-1 row-cols-sm-2 g-2');
		items.forEach(function (it) {
			var col = el('div', 'col');
			var row = el('div', 'd-flex align-items-start gap-2');
			row.appendChild(el('span', 'bsg-cell bsg-swatch ' + it[1]));
			var txt = el('div', 'small lh-sm');
			txt.appendChild(el('div', 'fw-semibold', t('lg_' + it[0])));
			txt.appendChild(el('div', 'text-body-secondary', t('lg_' + it[0] + '_d')));
			row.appendChild(txt);
			col.appendChild(row);
			list.appendChild(col);
		});
		body.appendChild(list);
		body.appendChild(el('div', 'small text-body-secondary mt-2 border-top pt-2', t('coords_hint')));
		card.appendChild(head);
		card.appendChild(body);
		return card;
	}

	function shipChip(size, cls) {
		var s = el('span', 'bsg-ship ' + (cls || ''));
		for (var i = 0; i < size; i++) { s.appendChild(el('i')); }
		s.setAttribute('title', t('deck', { size: size }));
		return s;
	}

	/* ---------- moves analysis ---------- */

	function shotMap(moves, p) {
		var m = {};
		moves.forEach(function (mv) {
			if (mv.p !== p) { return; }
			var k = mv.r + '_' + mv.c;
			if (mv.res === 'sunk') {
				m[k] = 'sunk';
				(mv.cells || []).forEach(function (cc) { m[cc[0] + '_' + cc[1]] = 'sunk'; });
				(mv.auto || []).forEach(function (a) {
					var ak = a[0] + '_' + a[1];
					if (!m[ak]) { m[ak] = 'auto'; }
				});
			} else {
				m[k] = mv.res === 'miss' ? 'miss' : 'hit';
			}
		});
		return m;
	}

	function sunkSizes(moves, p) {
		var out = [];
		moves.forEach(function (mv) { if (mv.p === p && mv.res === 'sunk') { out.push(mv.s); } });
		return out;
	}

	function fleetChips(moves, shooter) {
		var wrap = el('div', 'd-flex flex-wrap gap-1');
		var left = sunkSizes(moves, shooter).slice();
		SIZES.forEach(function (size) {
			var i = left.indexOf(size);
			var sunk = i !== -1;
			if (sunk) { left.splice(i, 1); }
			wrap.appendChild(shipChip(size, 'is-static' + (sunk ? ' is-sunk' : '')));
		});
		return wrap;
	}

	/* ---------- ship placement ---------- */

	function newPlacement() {
		var b = [];
		for (var r = 0; r < 10; r++) { b.push([0, 0, 0, 0, 0, 0, 0, 0, 0, 0]); }
		return { board: b, horiz: true, sel: 1, hover: null, cells: null };
	}

	function plCan(r, c, size, horiz) {
		for (var i = 0; i < size; i++) {
			var cr = r + (horiz ? 0 : i), cc = c + (horiz ? i : 0);
			if (cr > 9 || cc > 9) { return false; }
			for (var dr = -1; dr <= 1; dr++) {
				for (var dc = -1; dc <= 1; dc++) {
					var nr = cr + dr, nc = cc + dc;
					if (nr >= 0 && nr <= 9 && nc >= 0 && nc <= 9 && pl.board[nr][nc] !== 0) { return false; }
				}
			}
		}
		return true;
	}

	function plPlaced(id) {
		for (var r = 0; r < 10; r++) { for (var c = 0; c < 10; c++) { if (pl.board[r][c] === id) { return true; } } }
		return false;
	}

	function plNextFree() {
		for (var id = 1; id <= 10; id++) { if (!plPlaced(id)) { return id; } }
		return 0;
	}

	function plCount() {
		var n = 0;
		for (var id = 1; id <= 10; id++) { if (plPlaced(id)) { n++; } }
		return n;
	}

	function plRemove(id) {
		for (var r = 0; r < 10; r++) { for (var c = 0; c < 10; c++) { if (pl.board[r][c] === id) { pl.board[r][c] = 0; } } }
	}

	function plRandom() {
		for (var attempt = 0; attempt < 50; attempt++) {
			pl = Object.assign(newPlacement(), { horiz: pl.horiz, cells: pl.cells });
			var ok = true;
			for (var id = 1; id <= 10 && ok; id++) {
				var size = SIZES[id - 1], done = false;
				for (var k = 0; k < 300 && !done; k++) {
					var h = Math.random() < 0.5;
					var r = Math.floor(Math.random() * (h ? 10 : 11 - size));
					var c = Math.floor(Math.random() * (h ? 11 - size : 10));
					if (plCan(r, c, size, h)) {
						for (var i = 0; i < size; i++) { pl.board[r + (h ? 0 : i)][c + (h ? i : 0)] = id; }
						done = true;
					}
				}
				ok = done;
			}
			if (ok) { pl.sel = 0; return; }
		}
	}

	function plSerialize() {
		var s = '';
		for (var r = 0; r < 10; r++) {
			for (var c = 0; c < 10; c++) {
				var v = pl.board[r][c];
				s += v === 10 ? 'a' : String(v);
			}
		}
		return s;
	}

	function plPaint() {
		for (var r = 0; r < 10; r++) {
			for (var c = 0; c < 10; c++) {
				var ce = pl.cells[r][c];
				ce.classList.remove('is-ok', 'is-bad');
				paintCell(ce, pl.board[r][c] > 0 ? 'ship' : '');
			}
		}
		if (pl.hover && pl.sel) {
			var size = SIZES[pl.sel - 1];
			var ok = plCan(pl.hover[0], pl.hover[1], size, pl.horiz);
			for (var i = 0; i < size; i++) {
				var rr = pl.hover[0] + (pl.horiz ? 0 : i), cc = pl.hover[1] + (pl.horiz ? i : 0);
				if (rr < 10 && cc < 10) { pl.cells[rr][cc].classList.add(ok ? 'is-ok' : 'is-bad'); }
			}
		}
	}

	function plRefreshUi() {
		plPaint();
		var port = document.getElementById('bs-port');
		if (port) {
			clear(port);
			for (var id = 1; id <= 10; id++) {
				(function (id2) {
					var done = plPlaced(id2);
					var chip = shipChip(SIZES[id2 - 1], (done ? 'is-done ' : '') + (pl.sel === id2 ? 'is-selected' : ''));
					if (!done) { chip.addEventListener('click', function () { pl.sel = id2; plRefreshUi(); }); }
					port.appendChild(chip);
				})(id);
			}
		}
		var cnt = document.getElementById('bs-placed');
		if (cnt) { cnt.textContent = t('placed', { n: plCount(), total: 10 }); }
		var ok = document.getElementById('bs-confirm');
		if (ok) { ok.disabled = plCount() !== 10; }
		var rot = document.getElementById('bs-orient');
		if (rot) { rot.textContent = pl.horiz ? t('orient_h') : t('orient_v'); }
	}

	function plRotate() {
		if (!pl) { return; }
		pl.horiz = !pl.horiz;
		plRefreshUi();
	}

	document.addEventListener('keydown', function (e) {
		if (S.view !== 'place' || !pl) { return; }
		var tag = (e.target && e.target.tagName) || '';
		if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') { return; }
		if (e.key === 'r' || e.key === 'R' || e.key === 'к' || e.key === 'К') {
			e.preventDefault();
			plRotate();
		}
	});

	function renderPlacement(g) {
		stopTimers();
		S.view = 'place';
		S.mounted = g.game_id;
		pl = newPlacement();
		clear(root);

		var infoBox = el('div', 'mb-3');
		infoBox.id = 'bs-place-info';
		root.appendChild(infoBox);

		var row = el('div', 'row g-3');
		var left = el('div', 'col-lg-7');
		var right = el('div', 'col-lg-5');

		var boardCard = el('div', 'card mb-3');
		boardCard.appendChild(el('div', 'card-header fw-semibold', t('place_title')));
		var bb = el('div', 'card-body');
		var grid = buildGrid({
			tag: 'div', cls: 'is-place',
			onHover: function (r, c) { pl.hover = [r, c]; plPaint(); },
			onLeave: function () { pl.hover = null; plPaint(); }
		});
		pl.cells = grid.cells;
		grid.el.addEventListener('click', function (e) {
			var ce = e.target.closest('[data-r]');
			if (!ce) { return; }
			var r = +ce.getAttribute('data-r'), c = +ce.getAttribute('data-c');
			var v = pl.board[r][c];
			if (v > 0) {
				plRemove(v);
				pl.sel = v;
			} else if (pl.sel) {
				var size = SIZES[pl.sel - 1];
				if (!plCan(r, c, size, pl.horiz)) {
					toast(t('lg_bad_d'), 'warning');
					return;
				}
				for (var i = 0; i < size; i++) { pl.board[r + (pl.horiz ? 0 : i)][c + (pl.horiz ? i : 0)] = pl.sel; }
				Snd.play('place');
				pl.sel = plNextFree();
			}
			pl.hover = [r, c];
			plRefreshUi();
		});
		bb.appendChild(grid.el);
		boardCard.appendChild(bb);
		left.appendChild(boardCard);

		var ctl = el('div', 'card mb-3');
		ctl.appendChild(el('div', 'card-header fw-semibold', t('port')));
		var cb = el('div', 'card-body');
		cb.appendChild(el('p', 'small text-body-secondary', t('place_hint')));
		var port = el('div', 'd-flex flex-wrap gap-2 mb-3');
		port.id = 'bs-port';
		cb.appendChild(port);
		var meta = el('div', 'd-flex justify-content-between small mb-3');
		var placed = el('span', '', '');
		placed.id = 'bs-placed';
		var orient = el('span', 'badge text-bg-secondary', '');
		orient.id = 'bs-orient';
		meta.appendChild(placed);
		meta.appendChild(orient);
		cb.appendChild(meta);

		var acts = el('div', 'd-flex flex-wrap gap-2');
		acts.appendChild(btn('btn-outline-secondary', t('btn_rotate'), plRotate));
		acts.appendChild(btn('btn-outline-primary', t('btn_random'), function () { plRandom(); Snd.play('place'); plRefreshUi(); }));
		acts.appendChild(btn('btn-outline-danger', t('btn_clear'), function () { pl = Object.assign(newPlacement(), { horiz: pl.horiz, cells: pl.cells }); plRefreshUi(); }));
		var ok = btn('btn-success ms-auto', t('btn_confirm'), confirmPlacement);
		ok.id = 'bs-confirm';
		ok.disabled = true;
		acts.appendChild(ok);
		cb.appendChild(acts);
		ctl.appendChild(cb);
		right.appendChild(ctl);
		right.appendChild(legendCard());

		row.appendChild(left);
		row.appendChild(right);
		root.appendChild(row);

		setHeaderActions([leaveButton(g), soundBtn(), btn('btn-outline-secondary btn-sm', t('btn_lobby'), goLobby)]);
		updatePlaceInfo(g);
		plRefreshUi();
		schedulePoll();
	}

	function updatePlaceInfo(g) {
		var box = document.getElementById('bs-place-info');
		if (!box) { return; }
		clear(box);
		var b = bannerInfo(g);
		var a = el('div', 'alert alert-' + b.cls + ' mb-0 d-flex align-items-center gap-2');
		a.appendChild(el('span', 'badge text-bg-' + b.cls, b.badge));
		a.appendChild(el('span', 'bsg-banner-text', b.text));
		box.appendChild(a);
	}

	function confirmPlacement() {
		if (!pl || plCount() !== 10) { toast(t('not_all_placed'), 'warning'); return; }
		var g = S.game;
		api('place', { game_id: g.game_id, board: plSerialize() }).then(function (res) {
			if (!res.success) { toast(res.message || t('err_unknown'), 'danger'); return; }
			pl = null;
			applyState(res);
		});
	}

	/* ---------- banner / messages ---------- */

	function bannerInfo(g) {
		var r = { cls: 'secondary', badge: '', text: '' };
		if (g.status === 'finished') {
			var win = g.winner_side === g.side;
			r.badge = t('badge_over');
			if (g.reason === 'abandon' || g.winner_side === 0) {
				r.cls = 'secondary';
				r.text = t('st_abandon');
			} else if (g.reason === 'surrender') {
				r.cls = win ? 'success' : 'danger';
				r.text = win ? t('st_win_surrender') : t('st_lose_surrender');
			} else {
				r.cls = win ? 'success' : 'danger';
				r.text = win ? t('st_win') : t('st_lose');
			}
			return r;
		}
		if (g.status === 'placing') {
			if (!g.my_ready) {
				r.cls = 'info';
				r.badge = t('badge_place');
				r.text = !g.opp_joined ? t('st_waiting_join') : (g.opp_ready ? t('st_opp_waiting_you') : t('place_hint').split('.')[0] + '.');
			} else {
				r.cls = 'warning';
				r.badge = t('badge_wait');
				r.text = g.opp_joined ? t('st_waiting_ready') : t('st_waiting_join');
			}
			return r;
		}
		if (g.turn === g.side) {
			r.cls = 'success';
			r.badge = t('badge_you');
			r.text = t('st_your_turn');
		} else {
			r.cls = 'secondary';
			r.badge = t('badge_enemy');
			r.text = t('st_enemy_turn');
		}
		return r;
	}

	function describeMove(m, g) {
		var mine = m.p === g.side;
		var kind = m.res === 'miss' ? 'miss' : (m.res === 'sunk' ? 'sunk' : (m.h > 1 ? 'damaged' : 'hit'));
		var text = t((mine ? 'phr_my_' : 'phr_opp_') + kind, {
			cell: cellName(m.r, m.c), size: m.s || '', name: g.opp || t('ai_name')
		});
		var toastType = mine
			? (kind === 'miss' ? 'secondary' : 'success')
			: (kind === 'miss' ? 'secondary' : (kind === 'sunk' ? 'danger' : 'warning'));
		return {
			text: text, mine: mine, toastType: toastType,
			cls: m.res === 'miss' ? 'is-miss' : (m.res === 'sunk' ? 'is-sunk' : 'is-hit'),
			cell: cellName(m.r, m.c)
		};
	}

	function leaveButton(g) {
		var label, confirmLabel;
		if (!g.opp_joined) { label = t('btn_cancel_game'); confirmLabel = t('btn_surrender_confirm'); }
		else if (g.status === 'active') { label = t('btn_surrender'); confirmLabel = t('btn_surrender_confirm'); }
		else { label = t('btn_leave'); confirmLabel = t('btn_surrender_confirm'); }
		return confirmBtn('btn-outline-danger btn-sm', label, confirmLabel, function () {
			api('leave', { game_id: g.game_id }).then(function (res) {
				if (!res.success) { toast(res.message || t('err_unknown'), 'danger'); return; }
				if (res.deleted) { goLobby(); return; }
				applyState(res);
			});
		});
	}

	/* ---------- battle view ---------- */

	function renderBattle(g) {
		stopTimers();
		S.view = 'game';
		S.mounted = g.game_id;
		pl = null;
		var chatFocus = !!(chat.input && document.activeElement === chat.input);
		clear(root);

		var side = g.side, opp = 3 - side;
		var myTurn = g.status === 'active' && g.turn === side;
		var b = bannerInfo(g);

		var banner = el('div', 'alert alert-' + b.cls + ' d-flex flex-wrap align-items-center gap-2');
		banner.appendChild(el('span', 'badge text-bg-' + b.cls, b.badge));
		banner.appendChild(el('span', 'bsg-banner-text flex-grow-1', b.text));
		if (g.status !== 'finished') { banner.appendChild(leaveButton(g)); }
		root.appendChild(banner);

		if (g.status === 'finished') { root.appendChild(resultCard(g)); }

		var row = el('div', 'row g-3');

		// my board
		var colMine = el('div', 'col-xl-6 order-2 order-xl-1');
		var cardMine = el('div', 'card h-100' + (g.status === 'active' && !myTurn ? ' bsg-player is-turn' : ''));
		var hm = el('div', 'card-header d-flex flex-wrap justify-content-between align-items-center gap-2');
		hm.appendChild(el('span', 'fw-semibold', t('your_fleet') + ' - ' + g.me));
		hm.appendChild(fleetChips(g.moves, opp));
		cardMine.appendChild(hm);
		var bm = el('div', 'card-body');
		var mineShots = shotMap(g.moves, opp);
		var gridMine = buildGrid({ tag: 'div' });
		for (var r = 0; r < 10; r++) {
			for (var c = 0; c < 10; c++) {
				var st = mineShots[r + '_' + c] || '';
				if (!st && g.my_board.length && g.my_board[r][c] > 0) { st = 'ship'; }
				paintCell(gridMine.cells[r][c], st);
			}
		}
		markFresh(gridMine.cells, g, opp);
		bm.appendChild(gridMine.el);
		cardMine.appendChild(bm);
		colMine.appendChild(cardMine);

		// enemy board
		var colEnemy = el('div', 'col-xl-6 order-1 order-xl-2');
		var cardEnemy = el('div', 'card h-100' + (myTurn ? ' bsg-player is-turn' : ''));
		var he = el('div', 'card-header d-flex flex-wrap justify-content-between align-items-center gap-2');
		he.appendChild(el('span', 'fw-semibold', t('enemy_waters') + (g.opp ? ' - ' + g.opp : '')));
		he.appendChild(fleetChips(g.moves, side));
		cardEnemy.appendChild(he);
		var be = el('div', 'card-body');
		var enemyShots = shotMap(g.moves, side);
		var aim = el('span', 'bsg-aim fw-semibold', t('aim_none'));
		var gridEnemy = buildGrid({
			tag: 'button',
			cls: (myTurn ? 'is-target' : '') + (g.status === 'placing' ? ' is-disabled' : ''),
			onHover: function (r2, c2) { aim.textContent = cellName(r2, c2); },
			onLeave: function () { aim.textContent = t('aim_none'); }
		});
		for (r = 0; r < 10; r++) {
			for (c = 0; c < 10; c++) {
				st = enemyShots[r + '_' + c] || '';
				if (!st && g.opp_board.length && g.opp_board[r][c] > 0) { st = 'reveal'; }
				paintCell(gridEnemy.cells[r][c], st);
			}
		}
		markFresh(gridEnemy.cells, g, side);
		gridEnemy.el.addEventListener('click', function (e) {
			var ce = e.target.closest('[data-r]');
			if (!ce) { return; }
			fire(+ce.getAttribute('data-r'), +ce.getAttribute('data-c'));
		});
		be.appendChild(gridEnemy.el);
		var foot = el('div', 'text-center small mt-2');
		foot.appendChild(el('span', 'text-body-secondary', t('aim') + ': '));
		foot.appendChild(aim);
		be.appendChild(foot);
		cardEnemy.appendChild(be);
		colEnemy.appendChild(cardEnemy);

		row.appendChild(colMine);
		row.appendChild(colEnemy);
		root.appendChild(row);

		var row2 = el('div', 'row g-3 mt-0');
		var colLog = el('div', 'col-lg-7');
		var chatNode = chatNodeFor(g);
		if (chatNode) { colLog.appendChild(chatNode); }
		colLog.appendChild(logCard());
		var colLeg = el('div', 'col-lg-5');
		colLeg.appendChild(legendCard());
		row2.appendChild(colLog);
		row2.appendChild(colLeg);
		root.appendChild(row2);
		if (chatFocus && chat.input) { chat.input.focus(); }

		setHeaderActions([soundBtn(), btn('btn-outline-secondary btn-sm', t('btn_lobby'), goLobby)]);
		schedulePoll();
	}

	function markFresh(cells, g, shooter) {
		(S.fresh || []).forEach(function (m) {
			if (m.p !== shooter) { return; }
			cells[m.r][m.c].classList.add('just-shot');
		});
	}

	function resultCard(g) {
		var win = g.winner_side === g.side;
		var card = el('div', 'card mb-3 border-' + (g.winner_side === 0 ? 'secondary' : (win ? 'success' : 'danger')));
		var body = el('div', 'card-body d-flex flex-wrap align-items-center justify-content-between gap-2');
		var info = el('div');
		var title = g.winner_side === 0 ? t('badge_over') : (win ? t('result_win') : t('result_lose'));
		info.appendChild(el('div', 'h5 mb-1', title));
		if (g.winner_side !== 0) { info.appendChild(el('div', 'text-body-secondary', t(win ? 'phr_win' : 'phr_lose'))); }
		var shots = 0, hits = 0;
		g.moves.forEach(function (m) { if (m.p === g.side) { shots++; if (m.res !== 'miss') { hits++; } } });
		info.appendChild(el('div', 'small mt-1', t('result_stats', {
			shots: shots, hits: hits, acc: shots ? Math.round(hits / shots * 100) : 0
		})));
		body.appendChild(info);
		body.appendChild(btn('btn-primary', t('btn_lobby'), goLobby));
		card.appendChild(body);
		return card;
	}

	function logCard() {
		var card = el('div', 'card');
		card.appendChild(el('div', 'card-header fw-semibold', t('log_title')));
		if (!S.log.length) {
			card.appendChild(el('div', 'card-body text-body-secondary', t('log_empty')));
			return card;
		}
		var list = el('ul', 'list-group list-group-flush bsg-log');
		for (var i = S.log.length - 1; i >= 0; i--) {
			var d = S.log[i];
			var li = el('li', 'list-group-item ' + d.cls + (d.mine ? ' is-mine' : ' is-theirs'));
			li.appendChild(el('span', 'bsg-who fw-semibold', (d.mine ? t('log_you') : t('log_opp')) + ': '));
			li.appendChild(document.createTextNode(d.text));
			list.appendChild(li);
		}
		card.appendChild(list);
		return card;
	}

	function fire(r, c) {
		var g = S.game;
		if (!g || S.busy) { return; }
		if (g.status !== 'active') { return; }
		if (g.turn !== g.side) { toast(t('err_not_your_turn'), 'warning'); return; }
		if (shotMap(g.moves, g.side)[r + '_' + c]) { toast(t('err_already_shot'), 'warning'); return; }
		S.busy = true;
		api('fire', { game_id: g.game_id, row: r, col: c }).then(function (res) {
			S.busy = false;
			if (!res.success) { toast(res.message || t('err_unknown'), 'danger'); return; }
			applyState(res);
		});
	}

	/* ---------- state application ---------- */

	function applyState(g, initial) {
		var prev = S.game;
		S.game = g;
		S.gid = g.game_id;

		var fresh = [];
		for (var i = S.seen; i < g.moves.length; i++) {
			var d = describeMove(g.moves[i], g);
			S.log.push(d);
			fresh.push(g.moves[i]);
			if (!initial) { toast(d.text, d.toastType); }
		}
		S.seen = g.moves.length;
		S.fresh = initial ? [] : fresh;
		if (!initial) { soundsFor(fresh, g, prev); }
		if (!chatOn(g) && chat.el) { chatStop(); }

		if (!initial && prev) {
			if (!prev.opp_joined && g.opp_joined) { toast(t('st_opp_joined', { name: g.opp }), 'info'); }
			var last = fresh.length ? fresh[fresh.length - 1] : null;
			if (g.status === 'active' && g.turn === g.side) {
				if (last && last.p === g.side && last.res !== 'miss' && g.hit_again) {
					toast(t('extra_turn'), 'info');
				} else if (prev.turn !== g.side || prev.status !== 'active') {
					toast(t('phr_your_turn'), 'primary');
				}
			}
			if (prev.status !== 'finished' && g.status === 'finished' && g.winner_side !== 0) {
				var win = g.winner_side === g.side;
				if (g.reason === 'sunk') { toast(t(win ? 'phr_win' : 'phr_lose'), win ? 'success' : 'danger'); }
			}
		}

		if (g.status === 'placing' && !g.my_ready) {
			if (S.view === 'place' && S.mounted === g.game_id && pl) {
				updatePlaceInfo(g);
				setHeaderActions([leaveButton(g), soundBtn(), btn('btn-outline-secondary btn-sm', t('btn_lobby'), goLobby)]);
				schedulePoll();
			} else {
				renderPlacement(g);
			}
			return;
		}
		renderBattle(g);
	}

	function schedulePoll() {
		if (S.pollTimer) { clearTimeout(S.pollTimer); S.pollTimer = null; }
		var g = S.game;
		if (!g || g.status === 'finished') { return; }
		var delay = g.status === 'placing' ? 2500 : (g.turn === g.side ? 5000 : 2000);
		S.pollTimer = setTimeout(function () {
			var gid = S.gid;
			if (S.busy) { schedulePoll(); return; }
			api('state', { game_id: gid, rev: S.game.rev }).then(function (res) {
				if (S.gid !== gid || (S.view !== 'game' && S.view !== 'place')) { return; }
				if (!res.success) {
					toast(res.message || t('err_unknown'), 'danger');
					goLobby();
					return;
				}
				if (res.unchanged) { schedulePoll(); return; }
				applyState(res);
			});
		}, delay);
	}

	/* ---------- lobby ---------- */

	function openGame(id) {
		return api('state', { game_id: id }).then(function (res) {
			if (!res.success) {
				toast(res.message || t('err_unknown'), 'danger');
				if (cfg.page === 'play') { setTimeout(function () { go(cfg.urls.home); }, 1800); }
				return;
			}
			stopTimers();
			S.game = null;
			S.log = [];
			S.seen = 0;
			S.fresh = [];
			S.view = 'game';
			S.mounted = 0;
			applyState(res, true);
		});
	}

	function createGame(mode) {
		api('create', { mode: mode }).then(function (res) {
			if (!res.success) { toast(res.message || t('err_unknown'), 'danger'); return; }
			go(playUrl(res.game_id));
		});
	}

	function joinGame(id) {
		api('join', { game_id: id }).then(function (res) {
			if (!res.success) { toast(res.message || t('err_unknown'), 'danger'); loadLobby(); return; }
			go(playUrl(res.game_id));
		});
	}

	function goLobby() {
		stopTimers();
		if (cfg.page === 'play') { go(cfg.urls.home); return; }
		S.view = 'lobby';
		S.game = null;
		S.gid = 0;
		S.sig = '';
		pl = null;
		loadLobby();
	}

	function loadLobby() {
		api('lobby').then(function (d) {
			if (S.view !== 'lobby') { return; }
			if (!d.success) { toast(d.message || t('err_unknown'), 'danger'); }
			else {
				var sig = JSON.stringify(d);
				if (sig !== S.sig) { S.sig = sig; renderLobby(d); }
			}
			stopTimers();
			S.pollTimer = setTimeout(loadLobby, 5000);
		});
	}

	function modeCard(title, desc, label, cls, onClick) {
		var col = el('div', 'col-md-6');
		var card = el('div', 'card h-100');
		var body = el('div', 'card-body d-flex flex-column');
		body.appendChild(el('h2', 'h5', title));
		body.appendChild(el('p', 'text-body-secondary flex-grow-1', desc));
		body.appendChild(btn(cls, label, onClick));
		card.appendChild(body);
		col.appendChild(card);
		return col;
	}

	function renderLobby(d) {
		clear(root);
		setHeaderActions([btn('btn-outline-secondary btn-sm', t('btn_refresh'), function () { S.sig = ''; loadLobby(); })]);

		var modes = el('div', 'row g-3 mb-3');
		modes.appendChild(modeCard(t('card_ai'), t('card_ai_desc'), t('btn_ai'), 'btn-primary', function () { createGame('ai'); }));
		modes.appendChild(modeCard(t('card_pvp'), t('card_pvp_desc'), t('btn_pvp'), 'btn-success', function () { createGame('pvp'); }));
		root.appendChild(modes);

		var row = el('div', 'row g-3');
		var left = el('div', 'col-lg-7');
		var right = el('div', 'col-lg-5');

		// my games
		var cm = el('div', 'card mb-3');
		var cmh = el('div', 'card-header d-flex justify-content-between align-items-center');
		cmh.appendChild(el('span', 'fw-semibold', t('my_games')));
		var allLink = el('a', 'small', t('all_my_games'));
		allLink.href = cfg.urls.my || '#';
		cmh.appendChild(allLink);
		cm.appendChild(cmh);
		if (!d.mine.length) {
			cm.appendChild(el('div', 'card-body text-body-secondary', t('no_my_games')));
		} else {
			var lm = el('ul', 'list-group list-group-flush');
			d.mine.forEach(function (g) {
				var li = el('li', 'list-group-item d-flex flex-wrap align-items-center justify-content-between gap-2');
				var info = el('div');
				info.appendChild(el('div', 'fw-semibold', t('game_n', { n: g.game_id }) + ' ' + (g.is_ai ? t('vs_ai') : (g.opp ? t('vs', { name: g.opp }) : t('wait_opp')))));
				var state = g.status === 'placing' ? t('state_placing') : (g.my_turn ? t('state_your_turn') : t('state_enemy_turn'));
				info.appendChild(el('span', 'badge text-bg-' + (g.my_turn ? 'success' : 'secondary'), state));
				li.appendChild(info);
				li.appendChild(btn('btn-sm btn-outline-primary', t('btn_resume'), function () { go(playUrl(g.game_id)); }));
				lm.appendChild(li);
			});
			cm.appendChild(lm);
		}
		left.appendChild(cm);

		// open games
		var co = el('div', 'card mb-3');
		co.appendChild(el('div', 'card-header fw-semibold', t('open_games')));
		if (!d.open.length) {
			co.appendChild(el('div', 'card-body text-body-secondary', t('no_open_games')));
		} else {
			var lo = el('ul', 'list-group list-group-flush');
			d.open.forEach(function (g) {
				var li = el('li', 'list-group-item d-flex align-items-center justify-content-between gap-2');
				var info = el('div');
				info.appendChild(el('div', 'fw-semibold', g.user_name || '?'));
				info.appendChild(el('small', 'text-body-secondary', t('game_n', { n: g.game_id }) + ' · ' + g.created_at));
				li.appendChild(info);
				li.appendChild(btn('btn-sm btn-success', t('btn_join'), function () { joinGame(+g.game_id); }));
				lo.appendChild(li);
			});
			co.appendChild(lo);
		}
		left.appendChild(co);

		// stats
		var cs = el('div', 'card mb-3');
		cs.appendChild(el('div', 'card-header fw-semibold', t('my_stats')));
		var s = d.stats;
		var dl = el('ul', 'list-group list-group-flush');
		[
			[t('stat_rating'), s.rating],
			[t('stat_pvp'), s.wins + ' / ' + s.losses],
			[t('stat_ai'), s.ai_wins + ' / ' + s.ai_losses],
			[t('stat_accuracy'), s.accuracy + '%']
		].forEach(function (p) {
			var li = el('li', 'list-group-item d-flex justify-content-between');
			li.appendChild(el('span', 'text-body-secondary', p[0]));
			li.appendChild(el('strong', '', String(p[1])));
			dl.appendChild(li);
		});
		cs.appendChild(dl);
		right.appendChild(cs);

		// top
		var ct = el('div', 'card mb-3');
		ct.appendChild(el('div', 'card-header fw-semibold', t('top_players')));
		if (!d.top.length) {
			ct.appendChild(el('div', 'card-body text-body-secondary', t('no_top')));
		} else {
			var ol = el('ol', 'list-group list-group-flush list-group-numbered');
			d.top.forEach(function (p) {
				var li = el('li', 'list-group-item d-flex justify-content-between align-items-center');
				li.appendChild(el('span', 'ms-2 me-auto', p.user_name));
				li.appendChild(el('span', 'badge text-bg-primary me-2', String(p.rating)));
				li.appendChild(el('small', 'text-body-secondary', p.wins + '/' + p.losses));
				ol.appendChild(li);
			});
			ct.appendChild(ol);
		}
		right.appendChild(ct);

		row.appendChild(left);
		row.appendChild(right);
		root.appendChild(row);
		root.appendChild(legendCard());
	}

	/* ---------- start ---------- */

	if (cfg.page === 'play' && cfg.gameId) {
		S.view = 'game';
		S.gid = cfg.gameId;
		openGame(cfg.gameId);
	} else {
		goLobby();
	}
})();
