<?php
/**
 *  File: battleship.functions.php
 *  Path: plugins/battleship/inc/battleship.functions.php
 *
 *  Game core: board rules, shots, game flow, statistics, lobby.
 *
 *  Conventions:
 *  - Board is 10x10, board[row][col] = ship id (1..10) or 0 for water.
 *    Ship id N has size battleship_sizes()[N - 1].
 *  - Side 1 = player1 (creator), side 2 = player2 (joiner or AI, id 0).
 *  - All shots are stored as a "moves" log; boards are never mutated.
 *
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
 
 
defined('COT_CODE') or die('Wrong URL.');

require_once __DIR__ . '/battleship.ai.php';

// ========== SEE function battleship_lang_load() ==============
// require_once cot_langfile('battleship', 'plug');
// ========== SEE function battleship_lang_load() ==============


Cot::$db->registerTable('battleship_games');
Cot::$db->registerTable('battleship_stats');
Cot::$db->registerTable('battleship_chat');

/* ---------- helpers ---------- */

function battleship_sizes(): array
{
    return [4, 3, 3, 2, 2, 2, 1, 1, 1, 1];
}

function battleship_cfg(string $key, $default)
{
    $v = Cot::$cfg['plugin']['battleship'][$key] ?? null;
    return ($v === null || $v === '') ? $default : $v;
}

function battleship_lang_load(): void
{
    if (!isset(Cot::$L['battleship_title'])) {
        $L = [];
        require cot_langfile('battleship', 'plug');
        Cot::$L = array_merge(Cot::$L, $L);
    }
}

function battleship_msg(string $key): string
{
    return Cot::$L['battleship_' . $key] ?? $key;
}

function battleship_fail(string $key): array
{
    return ['success' => false, 'message' => battleship_msg($key)];
}

function battleship_now(): string
{
    return date('Y-m-d H:i:s');
}

function battleship_user_name(int $id): string
{
    if ($id <= 0) {
        return battleship_msg('ai_name');
    }
    $name = Cot::$db->query('SELECT user_name FROM ' . '`' . Cot::$db_x . 'users`' . ' WHERE user_id = ?', [$id])->fetchColumn();
    return $name !== false ? (string) $name : ('#' . $id);
}

/* ---------- board rules ---------- */

function battleship_empty_board(): array
{
    return array_fill(0, 10, array_fill(0, 10, 0));
}

/** Converts a 100-char string (0-9, a = 10) to a board, or null. */
function battleship_board_from_string(string $s): ?array
{
    if (strlen($s) !== 100 || !preg_match('/^[0-9a]{100}$/', $s)) {
        return null;
    }
    $b = [];
    for ($r = 0; $r < 10; $r++) {
        $row = [];
        for ($c = 0; $c < 10; $c++) {
            $ch = $s[$r * 10 + $c];
            $row[] = $ch === 'a' ? 10 : (int) $ch;
        }
        $b[] = $row;
    }
    return $b;
}

/** @return array<int, array<int, array{0:int,1:int}>> ship id => cells */
function battleship_ship_cells(array $board): array
{
    $cells = [];
    for ($r = 0; $r < 10; $r++) {
        for ($c = 0; $c < 10; $c++) {
            $v = (int) ($board[$r][$c] ?? 0);
            if ($v > 0) {
                $cells[$v][] = [$r, $c];
            }
        }
    }
    return $cells;
}

function battleship_board_placed(array $board): bool
{
    return count($board) === 10 && count(battleship_ship_cells($board)) === 10;
}

function battleship_board_valid(?array $b): bool
{
    if (!is_array($b) || count($b) !== 10) {
        return false;
    }
    for ($r = 0; $r < 10; $r++) {
        if (!is_array($b[$r] ?? null) || count($b[$r]) !== 10) {
            return false;
        }
        for ($c = 0; $c < 10; $c++) {
            $v = $b[$r][$c];
            if (!is_int($v) || $v < 0 || $v > 10) {
                return false;
            }
        }
    }
    $cells = battleship_ship_cells($b);
    if (count($cells) !== 10) {
        return false;
    }
    foreach (battleship_sizes() as $i => $size) {
        $id = $i + 1;
        if (!isset($cells[$id]) || count($cells[$id]) !== $size) {
            return false;
        }
        if ($size > 1) {
            $rs = array_column($cells[$id], 0);
            $cs = array_column($cells[$id], 1);
            $sameRow = count(array_unique($rs)) === 1;
            $sameCol = count(array_unique($cs)) === 1;
            if (!$sameRow && !$sameCol) {
                return false;
            }
            $line = $sameRow ? $cs : $rs;
            if (max($line) - min($line) !== $size - 1) {
                return false;
            }
        }
    }
    // ships must not touch each other (even diagonally)
    for ($r = 0; $r < 10; $r++) {
        for ($c = 0; $c < 10; $c++) {
            $v = $b[$r][$c];
            if ($v === 0) {
                continue;
            }
            for ($dr = -1; $dr <= 1; $dr++) {
                for ($dc = -1; $dc <= 1; $dc++) {
                    $nr = $r + $dr;
                    $nc = $c + $dc;
                    if ($nr < 0 || $nr > 9 || $nc < 0 || $nc > 9) {
                        continue;
                    }
                    $w = $b[$nr][$nc];
                    if ($w !== 0 && $w !== $v) {
                        return false;
                    }
                }
            }
        }
    }
    return true;
}

function battleship_can_place(array $b, int $r, int $c, int $size, bool $horiz): bool
{
    for ($i = 0; $i < $size; $i++) {
        $cr = $r + ($horiz ? 0 : $i);
        $cc = $c + ($horiz ? $i : 0);
        if ($cr > 9 || $cc > 9 || $b[$cr][$cc] !== 0) {
            return false;
        }
        for ($dr = -1; $dr <= 1; $dr++) {
            for ($dc = -1; $dc <= 1; $dc++) {
                $nr = $cr + $dr;
                $nc = $cc + $dc;
                if ($nr >= 0 && $nr <= 9 && $nc >= 0 && $nc <= 9 && $b[$nr][$nc] !== 0) {
                    return false;
                }
            }
        }
    }
    return true;
}

function battleship_random_board(): array
{
    $b = battleship_empty_board();
    for ($attempt = 0; $attempt < 100; $attempt++) {
        $b = battleship_empty_board();
        $ok = true;
        foreach (battleship_sizes() as $i => $size) {
            $placed = false;
            for ($t = 0; $t < 300; $t++) {
                $horiz = (bool) random_int(0, 1);
                $r = random_int(0, $horiz ? 9 : 10 - $size);
                $c = random_int(0, $horiz ? 10 - $size : 9);
                if (battleship_can_place($b, $r, $c, $size, $horiz)) {
                    for ($k = 0; $k < $size; $k++) {
                        $b[$r + ($horiz ? 0 : $k)][$c + ($horiz ? $k : 0)] = $i + 1;
                    }
                    $placed = true;
                    break;
                }
            }
            if (!$placed) {
                $ok = false;
                break;
            }
        }
        if ($ok) {
            return $b;
        }
    }
    return $b;
}

/* ---------- shots ---------- */

/**
 * Builds per-shooter data from the moves log.
 *
 * @return array{shots: array<string,string>, sunk: array<int,array>}
 *   shots: "r_c" => hit|miss|auto ; sunk: list of ['size'=>, 'cells'=>]
 */
function battleship_analyze(array $moves, int $side): array
{
    $shots = [];
    $sunk  = [];
    foreach ($moves as $m) {
        if ((int) $m['p'] !== $side) {
            continue;
        }
        $shots[$m['r'] . '_' . $m['c']] = $m['res'] === 'miss' ? 'miss' : 'hit';
        if ($m['res'] === 'sunk') {
            $sunk[] = ['size' => (int) $m['s'], 'cells' => $m['cells']];
            foreach ($m['auto'] as $a) {
                $k = $a[0] . '_' . $a[1];
                if (!isset($shots[$k])) {
                    $shots[$k] = 'auto';
                }
            }
        }
    }
    return ['shots' => $shots, 'sunk' => $sunk];
}

/**
 * Applies one shot, appends it to $moves and returns the new move (or null if illegal).
 */
function battleship_shoot(array $targetBoard, array &$moves, int $side, int $r, int $c): ?array
{
    if ($r < 0 || $r > 9 || $c < 0 || $c > 9) {
        return null;
    }
    $an = battleship_analyze($moves, $side);
    if (isset($an['shots'][$r . '_' . $c])) {
        return null;
    }

    $m = ['p' => $side, 'r' => $r, 'c' => $c, 'res' => 'miss'];
    $v = (int) ($targetBoard[$r][$c] ?? 0);
    if ($v > 0) {
        $cells = battleship_ship_cells($targetBoard)[$v];
        $size  = count($cells);
        $hits  = 1;
        foreach ($cells as [$cr, $cc]) {
            if (($cr !== $r || $cc !== $c) && ($an['shots'][$cr . '_' . $cc] ?? '') === 'hit') {
                $hits++;
            }
        }
        $m['res'] = $hits === $size ? 'sunk' : 'hit';
        $m['s']   = $size;
        $m['h']   = $hits;
        if ($m['res'] === 'sunk') {
            $m['cells'] = $cells;
            $auto = [];
            foreach ($cells as [$cr, $cc]) {
                for ($dr = -1; $dr <= 1; $dr++) {
                    for ($dc = -1; $dc <= 1; $dc++) {
                        $nr = $cr + $dr;
                        $nc = $cc + $dc;
                        $k  = $nr . '_' . $nc;
                        if ($nr < 0 || $nr > 9 || $nc < 0 || $nc > 9) {
                            continue;
                        }
                        if ((int) $targetBoard[$nr][$nc] === $v || isset($an['shots'][$k]) || isset($auto[$k])) {
                            continue;
                        }
                        $auto[$k] = [$nr, $nc];
                    }
                }
            }
            $m['auto'] = array_values($auto);
            if (count($an['sunk']) + 1 === 10) {
                $m['all'] = true;
            }
        }
    }
    $moves[] = $m;
    return $m;
}

/* ---------- persistence ---------- */

function battleship_decode(?string $json): array
{
    $v = json_decode((string) $json, true);
    return is_array($v) ? $v : [];
}

function battleship_side(array $g, int $uid): int
{
    if ((int) $g['player1_id'] === $uid) {
        return 1;
    }
    if ($g['player2_id'] !== null && (int) $g['player2_id'] === $uid && $uid > 0) {
        return 2;
    }
    return 0;
}

function battleship_save(array &$g): void
{
    $g['rev'] = (int) $g['rev'] + 1;
    $g['updated_at'] = battleship_now();
    Cot::$db->update(Cot::$db_x . 'battleship_games', [
        'player1_board' => $g['player1_board'],
        'player2_board' => $g['player2_board'],
        'moves'         => $g['moves'],
        'status'        => $g['status'],
        'turn'          => (int) $g['turn'],
        'winner_side'   => (int) $g['winner_side'],
        'winner_id'     => $g['winner_id'],
        'finish_reason' => $g['finish_reason'],
        'rev'           => $g['rev'],
        'updated_at'    => $g['updated_at'],
    ], 'game_id = ?', [(int) $g['game_id']]);
}

/** Loads (and locks) a game row inside a transaction. */
function battleship_load_locked(int $gid): ?array
{
    $row = Cot::$db->query('SELECT * FROM ' . Cot::$db_x . 'battleship_games WHERE game_id = ? FOR UPDATE', [$gid])->fetch();
    return $row ?: null;
}

function battleship_transaction(callable $fn): array
{
    $db = Cot::$db;
    $db->beginTransaction();
    try {
        $res = $fn();
        $db->commit();
        return $res;
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
}

function battleship_cleanup(): void
{
    $hours = max(1, (int) battleship_cfg('abandon_hours', 24));
    $limit = date('Y-m-d H:i:s', time() - $hours * 3600);
    Cot::$db->query(
        "UPDATE " . Cot::$db_x . "battleship_games SET status = 'finished', finish_reason = 'abandon', rev = rev + 1
         WHERE status IN ('placing','active') AND updated_at < ?",
        [$limit]
    );
    battleship_chat_sweep();
}

/* ---------- statistics ---------- */

function battleship_finish(array &$g, int $winnerSide, string $reason): void
{
    // the chat lives only while the game is active
    battleship_chat_purge((int) $g['game_id']);
    $g['status']        = 'finished';
    $g['winner_side']   = $winnerSide;
    $g['finish_reason'] = $reason;
    $g['winner_id']     = $winnerSide === 1 ? (int) $g['player1_id'] : (int) $g['player2_id'];
    if ($reason === 'sunk' || $reason === 'surrender') {
        battleship_record_result($g, $winnerSide);
    }
}

function battleship_record_result(array $g, int $winnerSide): void
{
    $db   = Cot::$db;
    $x    = Cot::$db_x;
    $moves = battleship_decode($g['moves']);
    $isAi = (int) $g['is_ai'] === 1;

    $ids = [1 => (int) $g['player1_id'], 2 => (int) $g['player2_id']];
    $cnt = [1 => ['s' => 0, 'h' => 0], 2 => ['s' => 0, 'h' => 0]];
    foreach ($moves as $m) {
        $cnt[(int) $m['p']]['s']++;
        if ($m['res'] !== 'miss') {
            $cnt[(int) $m['p']]['h']++;
        }
    }

    $rating = [1 => 1000, 2 => 1000];
    if (!$isAi) {
        foreach ([1, 2] as $side) {
            $db->query("INSERT IGNORE INTO {$x}battleship_stats (user_id) VALUES (?)", [$ids[$side]]);
            $rating[$side] = (int) $db->query("SELECT rating FROM {$x}battleship_stats WHERE user_id = ?", [$ids[$side]])->fetchColumn();
        }
    }

    foreach ([1, 2] as $side) {
        $uid = $ids[$side];
        if ($uid <= 0) {
            continue;
        }
        $won = ($side === $winnerSide);
        $db->query("INSERT IGNORE INTO {$x}battleship_stats (user_id) VALUES (?)", [$uid]);
        if ($isAi) {
            $col = $won ? 'ai_wins' : 'ai_losses';
            $db->query(
                "UPDATE {$x}battleship_stats SET {$col} = {$col} + 1, shots = shots + ?, hits = hits + ? WHERE user_id = ?",
                [$cnt[$side]['s'], $cnt[$side]['h'], $uid]
            );
        } else {
            $opp = 3 - $side;
            $expected = 1 / (1 + pow(10, ($rating[$opp] - $rating[$side]) / 400));
            $delta = (int) round(24 * (($won ? 1 : 0) - $expected));
            $col = $won ? 'wins' : 'losses';
            $db->query(
                "UPDATE {$x}battleship_stats SET {$col} = {$col} + 1, rating = GREATEST(0, rating + ?), shots = shots + ?, hits = hits + ? WHERE user_id = ?",
                [$delta, $cnt[$side]['s'], $cnt[$side]['h'], $uid]
            );
        }
    }
}

function battleship_user_stats(int $userId): array
{
    $row = Cot::$db->query(
        'SELECT wins, losses, ai_wins, ai_losses, rating, shots, hits FROM ' . Cot::$db_x . 'battleship_stats WHERE user_id = ?',
        [$userId]
    )->fetch();
    $row = $row ?: ['wins' => 0, 'losses' => 0, 'ai_wins' => 0, 'ai_losses' => 0, 'rating' => 1000, 'shots' => 0, 'hits' => 0];
    foreach ($row as $k => $v) {
        $row[$k] = (int) $v;
    }
    $row['accuracy'] = $row['shots'] > 0 ? (int) round($row['hits'] / $row['shots'] * 100) : 0;
    return $row;
}

/* ---------- state payload ---------- */

function battleship_payload(array $g, int $uid): array
{
    $side = battleship_side($g, $uid);
    $opp  = 3 - $side;
    $isAi = (int) $g['is_ai'] === 1;
    $myBoard  = battleship_decode($g['player' . $side . '_board']);
    $oppBoard = battleship_decode($g['player' . $opp . '_board']);
    $finished = $g['status'] === 'finished';
    $oppId    = $g['player' . $opp . '_id'];

    return [
        'success'     => true,
        'game_id'     => (int) $g['game_id'],
        'rev'         => (int) $g['rev'],
        'status'      => $g['status'],
        'turn'        => (int) $g['turn'],
        'side'        => $side,
        'is_ai'       => $isAi,
        'winner_side' => (int) $g['winner_side'],
        'reason'      => $g['finish_reason'],
        'me'          => battleship_user_name($uid),
        'opp'         => $oppId === null ? '' : battleship_user_name((int) $oppId),
        'opp_joined'  => $oppId !== null,
        'my_ready'    => battleship_board_placed($myBoard),
        'opp_ready'   => battleship_board_placed($oppBoard),
        'my_board'    => battleship_board_placed($myBoard) ? $myBoard : [],
        'opp_board'   => ($finished && battleship_board_placed($oppBoard)) ? $oppBoard : [],
        'moves'       => battleship_decode($g['moves']),
        'hit_again'   => (int) battleship_cfg('hit_again', 1) === 1,
        'chat'        => !$isAi && battleship_chat_enabled(),
    ];
}

function battleship_get_state(int $uid, int $gid, int $knownRev): array
{
    $g = Cot::$db->query('SELECT * FROM ' . Cot::$db_x . 'battleship_games WHERE game_id = ?', [$gid])->fetch();
    if (!$g) {
        return battleship_fail('err_no_game');
    }
    if (battleship_side($g, $uid) === 0) {
        return battleship_fail('err_access');
    }
    if ($knownRev > 0 && $knownRev === (int) $g['rev']) {
        return ['success' => true, 'unchanged' => true, 'status' => $g['status']];
    }
    return battleship_payload($g, $uid);
}

/* ---------- game flow ---------- */

function battleship_active_count(int $uid): int
{
    return (int) Cot::$db->query(
        "SELECT COUNT(*) FROM " . Cot::$db_x . "battleship_games
         WHERE (player1_id = ? OR player2_id = ?) AND status IN ('placing','active')",
        [$uid, $uid]
    )->fetchColumn();
}

function battleship_create_game(int $uid, string $mode): array
{
    if ($mode !== 'ai' && $mode !== 'pvp') {
        return battleship_fail('err_unknown');
    }
    battleship_cleanup();
    $max = max(1, (int) battleship_cfg('max_active_games', 3));
    if (battleship_active_count($uid) >= $max) {
        return battleship_fail('err_limit');
    }

    $now = battleship_now();
    $isAi = $mode === 'ai';
    Cot::$db->insert(Cot::$db_x . 'battleship_games', [
        'player1_id'    => $uid,
        'player2_id'    => $isAi ? 0 : null,
        'is_ai'         => $isAi ? 1 : 0,
        'player1_board' => '[]',
        'player2_board' => $isAi ? json_encode(battleship_random_board()) : '[]',
        'moves'         => '[]',
        'status'        => 'placing',
        'turn'          => 1,
        'created_at'    => $now,
        'updated_at'    => $now,
    ]);
    return ['success' => true, 'game_id' => (int) Cot::$db->lastInsertId()];
}

function battleship_join_game(int $uid, int $gid): array
{
    battleship_cleanup();
    $max = max(1, (int) battleship_cfg('max_active_games', 3));
    if (battleship_active_count($uid) >= $max) {
        return battleship_fail('err_limit');
    }
    return battleship_transaction(function () use ($uid, $gid) {
        $g = battleship_load_locked($gid);
        if (!$g || $g['status'] !== 'placing' || (int) $g['is_ai'] === 1 || $g['player2_id'] !== null) {
            return battleship_fail('err_no_game');
        }
        if ((int) $g['player1_id'] === $uid) {
            return battleship_fail('err_own_game');
        }
        $g['player2_id'] = $uid;
        battleship_save_with_player2($g);
        return ['success' => true, 'game_id' => $gid];
    });
}

function battleship_save_with_player2(array &$g): void
{
    battleship_save($g);
    Cot::$db->update(Cot::$db_x . 'battleship_games', ['player2_id' => (int) $g['player2_id']], 'game_id = ?', [(int) $g['game_id']]);
}

function battleship_place_ships(int $uid, int $gid, ?array $board): array
{
    if (!battleship_board_valid($board)) {
        return battleship_fail('err_board');
    }
    return battleship_transaction(function () use ($uid, $gid, $board) {
        $g = battleship_load_locked($gid);
        if (!$g || $g['status'] !== 'placing') {
            return battleship_fail('err_no_game');
        }
        $side = battleship_side($g, $uid);
        if ($side === 0) {
            return battleship_fail('err_access');
        }
        if (battleship_board_placed(battleship_decode($g['player' . $side . '_board']))) {
            return battleship_fail('err_already_placed');
        }
        $g['player' . $side . '_board'] = json_encode($board);

        $b1 = battleship_decode($g['player1_board']);
        $b2 = battleship_decode($g['player2_board']);
        if ($g['player2_id'] !== null && battleship_board_placed($b1) && battleship_board_placed($b2)) {
            $g['status'] = 'active';
            $g['turn']   = (int) $g['is_ai'] === 1 ? 1 : random_int(1, 2);
        }
        battleship_save($g);
        return battleship_payload($g, $uid);
    });
}

function battleship_fire(int $uid, int $gid, int $r, int $c): array
{
    if ($r < 0 || $r > 9 || $c < 0 || $c > 9) {
        return battleship_fail('err_coords');
    }
    return battleship_transaction(function () use ($uid, $gid, $r, $c) {
        $g = battleship_load_locked($gid);
        if (!$g || $g['status'] !== 'active') {
            return battleship_fail('err_no_game');
        }
        $side = battleship_side($g, $uid);
        if ($side === 0) {
            return battleship_fail('err_access');
        }
        if ((int) $g['turn'] !== $side) {
            return battleship_fail('err_not_your_turn');
        }

        $hitAgain = (int) battleship_cfg('hit_again', 1) === 1;
        $moves    = battleship_decode($g['moves']);
        $opp      = 3 - $side;
        $oppBoard = battleship_decode($g['player' . $opp . '_board']);

        $m = battleship_shoot($oppBoard, $moves, $side, $r, $c);
        if ($m === null) {
            return battleship_fail('err_already_shot');
        }

        if (!empty($m['all'])) {
            $g['moves'] = json_encode($moves);
            battleship_finish($g, $side, 'sunk');
        } else {
            $g['turn'] = ($m['res'] !== 'miss' && $hitAgain) ? $side : $opp;

            // Computer plays its turn(s) right away
            if ((int) $g['is_ai'] === 1 && (int) $g['turn'] === 2) {
                $level = (string) battleship_cfg('ai_level', 'normal');
                $humanBoard = battleship_decode($g['player1_board']);
                for ($guard = 0; $guard < 100; $guard++) {
                    [$ar, $ac] = battleship_ai_pick($moves, $level);
                    $am = battleship_shoot($humanBoard, $moves, 2, $ar, $ac);
                    if ($am === null) {
                        break;
                    }
                    if (!empty($am['all'])) {
                        $g['moves'] = json_encode($moves);
                        battleship_finish($g, 2, 'sunk');
                        break;
                    }
                    if ($am['res'] === 'miss' || !$hitAgain) {
                        $g['turn'] = 1;
                        break;
                    }
                }
            }
            $g['moves'] = json_encode($moves);
        }
        battleship_save($g);
        return battleship_payload($g, $uid);
    });
}

function battleship_leave(int $uid, int $gid): array
{
    return battleship_transaction(function () use ($uid, $gid) {
        $g = battleship_load_locked($gid);
        if (!$g || $g['status'] === 'finished') {
            return battleship_fail('err_no_game');
        }
        $side = battleship_side($g, $uid);
        if ($side === 0) {
            return battleship_fail('err_access');
        }
        // nobody joined yet: simply remove the open game
        if ($g['player2_id'] === null) {
            Cot::$db->delete(Cot::$db_x . 'battleship_games', 'game_id = ?', [$gid]);
            return ['success' => true, 'deleted' => true];
        }
        if ($g['status'] === 'active') {
            battleship_finish($g, 3 - $side, 'surrender');
        } else {
            battleship_finish($g, 0, 'abandon');
            $g['winner_id'] = null;
        }
        battleship_save($g);
        return battleship_payload($g, $uid);
    });
}

/* ---------- lobby ---------- */

function battleship_lobby(int $uid): array
{
    battleship_cleanup();
    $db = Cot::$db;
    $x  = Cot::$db_x;
    $ut = '`' . $x . 'users`';

    $open = $db->query(
        "SELECT g.game_id, g.created_at, u.user_name
         FROM {$x}battleship_games g LEFT JOIN {$ut} u ON u.user_id = g.player1_id
         WHERE g.status = 'placing' AND g.is_ai = 0 AND g.player2_id IS NULL AND g.player1_id <> ?
         ORDER BY g.created_at DESC LIMIT 20",
        [$uid]
    )->fetchAll();

    $rows = $db->query(
        "SELECT g.game_id, g.status, g.is_ai, g.turn, g.player1_id, g.player2_id, g.updated_at, u1.user_name AS n1, u2.user_name AS n2
         FROM {$x}battleship_games g
         LEFT JOIN {$ut} u1 ON u1.user_id = g.player1_id
         LEFT JOIN {$ut} u2 ON u2.user_id = g.player2_id
         WHERE (g.player1_id = ? OR g.player2_id = ?) AND g.status IN ('placing','active')
         ORDER BY g.updated_at DESC LIMIT 20",
        [$uid, $uid]
    )->fetchAll();
    $mine = [];
    foreach ($rows as $row) {
        $side = (int) $row['player1_id'] === $uid ? 1 : 2;
        $oppName = $side === 1 ? $row['n2'] : $row['n1'];
        $oppId   = $side === 1 ? $row['player2_id'] : $row['player1_id'];
        $mine[] = [
            'game_id'    => (int) $row['game_id'],
            'status'     => $row['status'],
            'is_ai'      => (int) $row['is_ai'] === 1,
            'my_turn'    => $row['status'] === 'active' && (int) $row['turn'] === $side,
            'opp'        => $oppId === null ? '' : ((int) $oppId === 0 ? battleship_msg('ai_name') : (string) $oppName),
            'updated_at' => $row['updated_at'],
        ];
    }

    $top = $db->query(
        "SELECT s.user_id, u.user_name, s.rating, s.wins, s.losses
         FROM {$x}battleship_stats s INNER JOIN {$ut} u ON u.user_id = s.user_id
         WHERE s.wins + s.losses > 0
         ORDER BY s.rating DESC, s.wins DESC LIMIT 10"
    )->fetchAll();

    return [
        'success' => true,
        'open'    => $open,
        'mine'    => $mine,
        'top'     => $top,
        'stats'   => battleship_user_stats($uid),
        'me'      => $uid,
    ];
}


/* ---------- chat (PvP only, lives while status = active) ---------- */

function battleship_chat_enabled(): bool
{
    return (int) battleship_cfg('chat_enabled', 1) === 1;
}

function battleship_chat_cooldown(): int
{
    return min(30, max(2, (int) battleship_cfg('chat_cooldown', 5)));
}

function battleship_chat_max(): int
{
    return 255;
}

function battleship_chat_purge(int $gid): void
{
    Cot::$db->query('DELETE FROM ' . Cot::$db_x . 'battleship_chat WHERE game_id = ?', [$gid]);
}

/** Removes messages of finished and deleted games (safety net). */
function battleship_chat_sweep(): void
{
    $x = Cot::$db_x;
    Cot::$db->query(
        "DELETE c FROM {$x}battleship_chat c
         INNER JOIN {$x}battleship_games g ON g.game_id = c.game_id
         WHERE g.status = 'finished'"
    );
    Cot::$db->query(
        "DELETE c FROM {$x}battleship_chat c
         LEFT JOIN {$x}battleship_games g ON g.game_id = c.game_id
         WHERE g.game_id IS NULL"
    );
}

function battleship_chat_clean(string $s): string
{
    $s = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $s) ?? '';
    $s = preg_replace('/\s+/u', ' ', $s) ?? '';
    return trim($s);
}

function battleship_chat_send(int $uid, int $gid, string $text): array
{
    if (!battleship_chat_enabled()) {
        return battleship_fail('err_chat_off');
    }
    $text = battleship_chat_clean($text);
    if ($text === '') {
        return battleship_fail('err_chat_empty');
    }
    if (mb_strlen($text) > battleship_chat_max()) {
        return battleship_fail('err_chat_long');
    }
    return battleship_transaction(function () use ($uid, $gid, $text) {
        $db = Cot::$db;
        $t  = Cot::$db_x . 'battleship_chat';
        $g  = battleship_load_locked($gid);   // the row lock also serializes the rate limit check
        if (!$g) {
            return battleship_fail('err_no_game');
        }
        if (battleship_side($g, $uid) === 0) {
            return battleship_fail('err_access');
        }
        if ($g['status'] !== 'active' || (int) $g['is_ai'] === 1) {
            return battleship_fail('err_chat_closed');
        }
        $now  = time();
        $cd   = battleship_chat_cooldown();
        $last = (int) $db->query("SELECT MAX(created_ts) FROM {$t} WHERE user_id = ?", [$uid])->fetchColumn();
        if ($last > 0 && $now - $last < $cd) {
            return ['success' => false, 'message' => battleship_msg('err_chat_wait'), 'wait' => $cd - ($now - $last)];
        }
        $db->insert($t, ['game_id' => $gid, 'user_id' => $uid, 'message' => $text, 'created_ts' => $now]);
        $id = (int) $db->lastInsertId();

        // keep at most 200 latest messages per game
        $cut = $db->query("SELECT msg_id FROM {$t} WHERE game_id = ? ORDER BY msg_id DESC LIMIT 1 OFFSET 199", [$gid])->fetchColumn();
        if ($cut) {
            $db->query("DELETE FROM {$t} WHERE game_id = ? AND msg_id < ?", [$gid, (int) $cut]);
        }
        return [
            'success'  => true,
            'cooldown' => $cd,
            'chat'     => ['id' => $id, 'mine' => true, 'text' => $text, 'ts' => $now],
        ];
    });
}

function battleship_chat_get(int $uid, int $gid, int $after): array
{
    $db = Cot::$db;
    $t  = Cot::$db_x . 'battleship_chat';
    $g  = $db->query('SELECT game_id, player1_id, player2_id, status, is_ai FROM ' . Cot::$db_x . 'battleship_games WHERE game_id = ?', [$gid])->fetch();
    if (!$g) {
        return battleship_fail('err_no_game');
    }
    if (battleship_side($g, $uid) === 0) {
        return battleship_fail('err_access');
    }
    if ($g['status'] !== 'active' || (int) $g['is_ai'] === 1 || !battleship_chat_enabled()) {
        return ['success' => true, 'closed' => true, 'items' => []];
    }
    if ($after > 0) {
        $rows = $db->query(
            "SELECT msg_id, user_id, message, created_ts FROM {$t} WHERE game_id = ? AND msg_id > ? ORDER BY msg_id ASC LIMIT 50",
            [$gid, $after]
        )->fetchAll();
    } else {
        $rows = $db->query(
            "SELECT * FROM (SELECT msg_id, user_id, message, created_ts FROM {$t} WHERE game_id = ? ORDER BY msg_id DESC LIMIT 50) x ORDER BY msg_id ASC",
            [$gid]
        )->fetchAll();
    }
    $items = [];
    foreach ($rows as $r) {
        $items[] = [
            'id'   => (int) $r['msg_id'],
            'mine' => (int) $r['user_id'] === $uid,
            'text' => (string) $r['message'],
            'ts'   => (int) $r['created_ts'],
        ];
    }
    return ['success' => true, 'closed' => false, 'items' => $items, 'cooldown' => battleship_chat_cooldown()];
}

/* ---------- URLs and template helpers ---------- */

function battleship_url(string $mode, array $params = [], bool $raw = false): string
{
    $p = ['e' => 'battleship'];
    if ($mode !== 'home') {
        $p['m'] = $mode;
    }
    return cot_url('plug', array_merge($p, $params), '', $raw);
}

/** Config for the JS client (all battleship_* language strings except admin ones). */
function battleship_page_config(string $page, array $extra = []): array
{
    battleship_lang_load();
    $lang = [];
    foreach (Cot::$L as $key => $value) {
        $key = (string) $key;
        if (strpos($key, 'battleship_') === 0 && strpos($key, 'battleship_admin_') !== 0) {
            $lang[substr($key, 11)] = $value;
        }
    }
    return array_merge([
        'page'         => $page,
        'ajax'         => cot_url('plug', ['r' => 'battleship'], '', true),
        'xk'           => Cot::$sys['xk'],
        'hitAgain'     => (int) battleship_cfg('hit_again', 1) === 1,
        'chatCooldown' => battleship_chat_cooldown(),
        'chatMax'      => battleship_chat_max(),
        'urls'         => [
            'home' => battleship_url('home', [], true),
            'my'   => battleship_url('my', [], true),
            'play' => battleship_url('play', ['id' => '__ID__'], true),
        ],
        'lang'         => $lang,
    ], $extra);
}

/** Common template variables for the mode templates. */
function battleship_tpl_vars(string $page, array $extra = []): array
{
    $json = json_encode(battleship_page_config($page, $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return [
        'BS_CONFIG'    => htmlspecialchars($json, ENT_QUOTES, 'UTF-8'),
        'BS_LOGGED'    => (Cot::$usr['id'] > 0),
        'BS_LOGIN_URL' => cot_url('login'),
        'BS_URL_HOME'  => battleship_url('home'),
        'BS_URL_MY'    => battleship_url('my'),
    ];
}
