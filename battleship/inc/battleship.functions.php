<?php
/**
 * File: plugins/battleship/battleship.functions.php
 * Core game logic for Battleship plugin (PHP 8.5 compatible)
 * (mirrored in inc/ for organization)
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Standard ship sizes (classic Russian Battleship)
 */
function battleship_ship_sizes(): array
{
    return [4, 3, 3, 2, 2, 2, 1, 1, 1, 1];
}

/**
 * Empty 10x10 board
 */
function battleship_empty_board(): array
{
    return array_fill(0, 10, array_fill(0, 10, 0));
}

/**
 * Check if a ship can be placed at position (no overlap, no adjacent including diagonal)
 */
function battleship_can_place(array $b, int $sx, int $sy, int $size, bool $horiz): bool
{
    for ($i = 0; $i < $size; $i++) {
        $cx = $sx + ($horiz ? $i : 0);
        $cy = $sy + ($horiz ? 0 : $i);
        if ($cx < 0 || $cx > 9 || $cy < 0 || $cy > 9 || (int)$b[$cx][$cy] !== 0) {
            return false;
        }
        for ($dx = -1; $dx <= 1; $dx++) {
            for ($dy = -1; $dy <= 1; $dy++) {
                $nx = $cx + $dx;
                $ny = $cy + $dy;
                if ($nx >= 0 && $nx <= 9 && $ny >= 0 && $ny <= 9 && (int)$b[$nx][$ny] !== 0) {
                    return false;
                }
            }
        }
    }
    return true;
}

/**
 * Generate random valid board for AI
 */
function battleship_generate_board(): array
{
    $b = battleship_empty_board();
    foreach (battleship_ship_sizes() as $size) {
        $placed = false;
        for ($t = 0; $t < 800; $t++) {
            $horiz = (bool) mt_rand(0, 1);
            $sx = mt_rand(0, 9 - ($horiz ? $size - 1 : 0));
            $sy = mt_rand(0, 9 - ($horiz ? 0 : $size - 1));
            if (battleship_can_place($b, $sx, $sy, $size, $horiz)) {
                for ($i = 0; $i < $size; $i++) {
                    $b[$sx + ($horiz ? $i : 0)][$sy + ($horiz ? 0 : $i)] = $size;
                }
                $placed = true;
                break;
            }
        }
        if (!$placed) {
            // Fallback: restart generation
            return battleship_generate_board();
        }
    }
    return $b;
}

/**
 * Validate that board contains exactly the required ships
 */
function battleship_board_valid(array $board): bool
{
    if (count($board) !== 10) {
        return false;
    }
    for ($i = 0; $i < 10; $i++) {
        if (!is_array($board[$i]) || count($board[$i]) !== 10) {
            return false;
        }
    }

    $visited = [];
    $sizes = [];
    for ($i = 0; $i < 10; $i++) {
        for ($j = 0; $j < 10; $j++) {
            $v = (int) $board[$i][$j];
            if ($v <= 0 || isset($visited[$i . '_' . $j])) {
                continue;
            }
            // Flood fill to find ship segment
            $seg = 1;
            $visited[$i . '_' . $j] = true;
            // Horizontal
            for ($k = $j + 1; $k < 10 && (int)$board[$i][$k] === $v; $k++) {
                $visited[$i . '_' . $k] = true;
                $seg++;
            }
            for ($k = $j - 1; $k >= 0 && (int)$board[$i][$k] === $v; $k--) {
                $visited[$i . '_' . $k] = true;
                $seg++;
            }
            // Vertical (only if still single cell so far)
            if ($seg === 1) {
                for ($k = $i + 1; $k < 10 && (int)$board[$k][$j] === $v; $k++) {
                    $visited[$k . '_' . $j] = true;
                    $seg++;
                }
                for ($k = $i - 1; $k >= 0 && (int)$board[$k][$j] === $v; $k--) {
                    $visited[$k . '_' . $j] = true;
                    $seg++;
                }
            }
            $sizes[] = $seg;
        }
    }
    $need = battleship_ship_sizes();
    sort($sizes);
    sort($need);
    return $sizes === $need;
}

/**
 * Find all fully sunk ships and whether entire fleet is sunk
 * Board values: positive = intact ship size, negative = hit part of ship
 */
function battleship_find_sunk(array $board): array
{
    $cells = [];
    $allSunk = true;
    $visited = [];

    for ($i = 0; $i < 10; $i++) {
        for ($j = 0; $j < 10; $j++) {
            $v = isset($board[$i][$j]) ? (int)$board[$i][$j] : 0;
            if ($v > 0) {
                $allSunk = false; // still intact ships
                continue;
            }
            if ($v >= 0 || isset($visited[$i . '_' . $j])) {
                continue;
            }
            // Found a hit cell belonging to a ship (negative value)
            $shipId = abs($v);
            $seg = [[$i, $j]];
            $visited[$i . '_' . $j] = true;

            // Expand horizontally
            for ($k = $j + 1; $k < 10 && isset($board[$i][$k]) && abs((int)$board[$i][$k]) === $shipId; $k++) {
                $visited[$i . '_' . $k] = true;
                $seg[] = [$i, $k];
            }
            for ($k = $j - 1; $k >= 0 && isset($board[$i][$k]) && abs((int)$board[$i][$k]) === $shipId; $k--) {
                $visited[$i . '_' . $k] = true;
                $seg[] = [$i, $k];
            }
            // Expand vertically if needed
            if (count($seg) === 1) {
                for ($k = $i + 1; $k < 10 && isset($board[$k][$j]) && abs((int)$board[$k][$j]) === $shipId; $k++) {
                    $visited[$k . '_' . $j] = true;
                    $seg[] = [$k, $j];
                }
                for ($k = $i - 1; $k >= 0 && isset($board[$k][$j]) && abs((int)$board[$k][$j]) === $shipId; $k--) {
                    $visited[$k . '_' . $j] = true;
                    $seg[] = [$k, $j];
                }
            }

            // Check if entire ship is sunk (all cells negative / hit)
            $fullySunk = true;
            foreach ($seg as [$cx, $cy]) {
                if ((int)$board[$cx][$cy] > 0) {
                    $fullySunk = false;
                    break;
                }
            }
            if ($fullySunk && count($seg) === $shipId) {
                foreach ($seg as $c) {
                    $cells[] = $c;
                }
            } else {
                $allSunk = false;
            }
        }
    }

    // Extra check: any positive cells left?
    if ($allSunk) {
        for ($i = 0; $i < 10; $i++) {
            for ($j = 0; $j < 10; $j++) {
                if (isset($board[$i][$j]) && (int)$board[$i][$j] > 0) {
                    $allSunk = false;
                    break 2;
                }
            }
        }
    }

    return ['cells' => $cells, 'all' => $allSunk];
}

/**
 * Cells of the ship at (x,y) if that ship is fully sunk after the shot; else [].
 */
function battleship_sunk_ship_at(array $board, int $x, int $y): array
{
    if (!isset($board[$x][$y])) {
        return [];
    }
    $v = (int) $board[$x][$y];
    if ($v >= 0) {
        return []; // not a hit cell
    }
    $shipId = abs($v);
    $seg = [[$x, $y]];
    $visited = [$x . '_' . $y => true];

    // horizontal
    for ($k = $y + 1; $k < 10 && isset($board[$x][$k]) && abs((int)$board[$x][$k]) === $shipId; $k++) {
        $visited[$x . '_' . $k] = true;
        $seg[] = [$x, $k];
    }
    for ($k = $y - 1; $k >= 0 && isset($board[$x][$k]) && abs((int)$board[$x][$k]) === $shipId; $k--) {
        $visited[$x . '_' . $k] = true;
        $seg[] = [$x, $k];
    }
    if (count($seg) === 1) {
        for ($k = $x + 1; $k < 10 && isset($board[$k][$y]) && abs((int)$board[$k][$y]) === $shipId; $k++) {
            $seg[] = [$k, $y];
        }
        for ($k = $x - 1; $k >= 0 && isset($board[$k][$y]) && abs((int)$board[$k][$y]) === $shipId; $k--) {
            $seg[] = [$k, $y];
        }
    }

    if (count($seg) !== $shipId) {
        return [];
    }
    foreach ($seg as [$cx, $cy]) {
        if ((int)$board[$cx][$cy] > 0) {
            return [];
        }
    }
    return $seg;
}


/**
 * Update player statistics after a finished game
 */
function battleship_update_stats(int $winnerId, int $loserId): void
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;

    if ($winnerId > 0) {
        $db->query(
            "INSERT INTO {$db_x}battleship_stats (user_id, wins, losses, rating)
             VALUES (?, 1, 0, 1016)
             ON DUPLICATE KEY UPDATE wins = wins + 1, rating = rating + 16",
            [$winnerId]
        );
    }
    if ($loserId > 0) {
        $db->query(
            "INSERT INTO {$db_x}battleship_stats (user_id, wins, losses, rating)
             VALUES (?, 0, 1, 984)
             ON DUPLICATE KEY UPDATE losses = losses + 1, rating = GREATEST(0, rating - 16)",
            [$loserId]
        );
    }
}

/**
 * Create a new game (AI or PvP)
 */
function battleship_create_game(int $userId, string $mode): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    if ($mode !== 'ai' && $mode !== 'pvp') {
        return ['success' => false, 'message' => $L['battleship_err_unknown']];
    }

    $max = (int) (Cot::$cfg['plugin']['battleship']['max_active_games'] ?? 3);
    if ($max < 1) {
        $max = 3;
    }

    $active = (int) $db->query(
        "SELECT COUNT(*) FROM {$db_x}battleship_games
         WHERE (player1_id = ? OR player2_id = ?) AND status IN ('placing', 'active')",
        [$userId, $userId]
    )->fetchColumn();

    if ($active >= $max) {
        return ['success' => false, 'message' => ($L['battleship_err_limit_hint'] ?? $L['battleship_err_limit'])];
    }

    $now = date('Y-m-d H:i:s');

    if ($mode === 'ai') {
        // AI is always player1 (id=0), human is player2
        $db->insert($db_x . 'battleship_games', [
            'player1_id'    => 0,
            'player2_id'    => $userId,
            'player1_board' => json_encode(battleship_generate_board()),
            'player2_board' => json_encode([]),
            'player1_shots' => json_encode([]),
            'player2_shots' => json_encode([]),
            'status'        => 'placing',
            'turn'          => 2, // human starts after placement
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
        return [
            'success' => true,
            'game_id' => (int) $db->lastInsertId(),
            'mode'    => 'ai',
        ];
    }

    // PvP: creator is player1
    $db->insert($db_x . 'battleship_games', [
        'player1_id'    => $userId,
        'player2_id'    => null,
        'player1_board' => json_encode([]),
        'player2_board' => json_encode([]),
        'player1_shots' => json_encode([]),
        'player2_shots' => json_encode([]),
        'status'        => 'placing',
        'turn'          => 1,
        'created_at'    => $now,
        'updated_at'    => $now,
    ]);
    return [
        'success' => true,
        'game_id' => (int) $db->lastInsertId(),
        'mode'    => 'pvp',
    ];
}

/**
 * Join an open PvP game
 */
function battleship_join_game(int $userId, int $gameId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games
         WHERE game_id = ? AND status = 'placing' AND player2_id IS NULL AND player1_id <> 0",
        [$gameId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }
    if ((int)$game['player1_id'] === $userId) {
        return ['success' => false, 'message' => $L['battleship_err_own_game']];
    }

    $db->update($db_x . 'battleship_games', [
        'player2_id' => $userId,
        'updated_at' => date('Y-m-d H:i:s'),
    ], 'game_id = ?', [$gameId]);

    return ['success' => true, 'game_id' => $gameId];
}

/**
 * Place ships on the board
 */
function battleship_place_ships(int $userId, int $gameId, ?array $board): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    if (!is_array($board) || !battleship_board_valid($board)) {
        return ['success' => false, 'message' => $L['battleship_err_board']];
    }

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games WHERE game_id = ? AND status = 'placing'",
        [$gameId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }

    $isP1 = ((int)$game['player1_id'] === $userId);
    $isP2 = ((int)$game['player2_id'] === $userId);
    if (!$isP1 && !$isP2) {
        return ['success' => false, 'message' => $L['battleship_err_access']];
    }

    $data = [
        ($isP1 ? 'player1_board' : 'player2_board') => json_encode($board),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    // AI game: human is always player2
    if ((int)$game['player1_id'] === 0) {
        $data['status'] = 'active';
        $data['turn']   = 2;
    } else {
        // PvP: check if both have placed
        $b1 = $isP1 ? $board : json_decode($game['player1_board'] ?: '[]', true);
        $b2 = $isP2 ? $board : json_decode($game['player2_board'] ?: '[]', true);

        $b1Ready = is_array($b1) && battleship_board_valid($b1);
        $b2Ready = is_array($b2) && battleship_board_valid($b2);

        if ($b1Ready && $b2Ready) {
            $data['status'] = 'active';
            $data['turn']   = 1; // player1 starts
        }
    }

    $db->update($db_x . 'battleship_games', $data, 'game_id = ?', [$gameId]);
    return ['success' => true];
}

/**
 * Fire a shot
 */
function battleship_fire(int $userId, int $gameId, int $x, int $y): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    if ($x < 0 || $x > 9 || $y < 0 || $y > 9) {
        return ['success' => false, 'message' => $L['battleship_err_coords']];
    }

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games WHERE game_id = ? AND status = 'active'",
        [$gameId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }

    $isP1 = ((int)$game['player1_id'] === $userId);
    $isP2 = ((int)$game['player2_id'] === $userId);
    if (!$isP1 && !$isP2) {
        return ['success' => false, 'message' => $L['battleship_err_access']];
    }

    $myTurnNum = $isP1 ? 1 : 2;
    if ((int)$game['turn'] !== $myTurnNum) {
        return ['success' => false, 'message' => $L['battleship_status_not_your_turn']];
    }

    $enemyBoard = json_decode($isP1 ? $game['player2_board'] : $game['player1_board'], true) ?: [];
    $myShots    = json_decode($isP1 ? $game['player1_shots'] : $game['player2_shots'], true) ?: [];

    $key = $x . '_' . $y;
    if (isset($myShots[$key])) {
        return ['success' => false, 'message' => $L['battleship_status_already_shot']];
    }

    $result = 'miss';
    $wasHit = false;
    if (!empty($enemyBoard[$x][$y]) && (int)$enemyBoard[$x][$y] > 0) {
        $result = 'hit';
        $wasHit = true;
        $enemyBoard[$x][$y] = -(int)$enemyBoard[$x][$y];
    }
    $myShots[$key] = $result;

    $sunk = battleship_find_sunk($enemyBoard);
    // Только корабль, задетый ЭТИМ выстрелом (иначе «потопил» на каждый последующий hit)
    $sunkThis = ($result === 'hit') ? battleship_sunk_ship_at($enemyBoard, $x, $y) : [];

    $data = [
        ($isP1 ? 'player1_shots' : 'player2_shots') => json_encode($myShots),
        ($isP1 ? 'player2_board' : 'player1_board') => json_encode($enemyBoard),
        'turn'       => $isP1 ? 2 : 1,
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $allSunk = $sunk['all'];
    if ($allSunk) {
        $data['status']    = 'finished';
        $data['winner_id'] = $userId;
        $loserId = $isP1 ? (int)$game['player2_id'] : (int)$game['player1_id'];
        battleship_update_stats($userId, $loserId);
    }

    $db->update($db_x . 'battleship_games', $data, 'game_id = ?', [$gameId]);

    return [
        'success'    => true,
        'result'     => $result,
        'sunk'       => count($sunkThis) > 0,
        'all_sunk'   => $allSunk,
        'sunk_cells' => $sunkThis,
        'x'          => $x,
        'y'          => $y,
    ];
}

/**
 * AI makes a move (simple random for now, with slight preference for adjacent after hit)
 */
function battleship_ai_move(int $userId, int $gameId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games
         WHERE game_id = ? AND status = 'active' AND player1_id = 0 AND player2_id = ?",
        [$gameId, $userId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }
    if ((int)$game['turn'] !== 1) {
        return ['success' => false, 'message' => $L['battleship_err_not_ai_turn']];
    }

    $userBoard = json_decode($game['player2_board'], true) ?: [];
    $aiShots   = json_decode($game['player1_shots'], true) ?: [];

    // Simple smart AI: hunt mode after a hit
    $candidates = [];
    $free = [];
    for ($i = 0; $i < 10; $i++) {
        for ($j = 0; $j < 10; $j++) {
            $k = $i . '_' . $j;
            if (!isset($aiShots[$k])) {
                $free[] = [$i, $j];
                // Check adjacent hits
                $adj = [[$i-1,$j],[$i+1,$j],[$i,$j-1],[$i,$j+1]];
                foreach ($adj as [$ax, $ay]) {
                    if ($ax >= 0 && $ax <= 9 && $ay >= 0 && $ay <= 9) {
                        $ak = $ax . '_' . $ay;
                        if (isset($aiShots[$ak]) && $aiShots[$ak] === 'hit') {
                            $candidates[] = [$i, $j];
                            break;
                        }
                    }
                }
            }
        }
    }

    if (!$free) {
        return ['success' => false, 'message' => $L['battleship_err_no_moves']];
    }

    if (!empty($candidates)) {
        $pick = $candidates[array_rand($candidates)];
    } else {
        $pick = $free[array_rand($free)];
    }
    [$x, $y] = $pick;

    $result = 'miss';
    if (!empty($userBoard[$x][$y]) && (int)$userBoard[$x][$y] > 0) {
        $result = 'hit';
        $userBoard[$x][$y] = -(int)$userBoard[$x][$y];
    }
    $aiShots[$x . '_' . $y] = $result;

    $sunk = battleship_find_sunk($userBoard);
    $sunkThis = ($result === 'hit') ? battleship_sunk_ship_at($userBoard, $x, $y) : [];

    $data = [
        'player2_board' => json_encode($userBoard),
        'player1_shots' => json_encode($aiShots),
        'turn'          => 2,
        'updated_at'    => date('Y-m-d H:i:s'),
    ];

    $allSunk = $sunk['all'];
    if ($allSunk) {
        $data['status']    = 'finished';
        $data['winner_id'] = 0;
        battleship_update_stats(0, $userId);
    }

    $db->update($db_x . 'battleship_games', $data, 'game_id = ?', [$gameId]);

    return [
        'success'    => true,
        'ai_shot'    => ['x' => $x, 'y' => $y],
        'result'     => $result,
        'sunk'       => count($sunkThis) > 0,
        'all_sunk'   => $allSunk,
        'sunk_cells' => $sunkThis,
    ];
}

/**
 * Surrender the current game
 */
function battleship_surrender(int $userId, int $gameId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games WHERE game_id = ? AND status IN ('active', 'placing')",
        [$gameId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }

    $isP1 = ((int)$game['player1_id'] === $userId);
    $isP2 = ((int)$game['player2_id'] === $userId);
    if (!$isP1 && !$isP2) {
        return ['success' => false, 'message' => $L['battleship_err_access']];
    }

    $winner = $isP1 ? (int)$game['player2_id'] : (int)$game['player1_id'];
    // If AI (0) or no second player yet, still finish
    $db->update($db_x . 'battleship_games', [
        'status'     => 'finished',
        'winner_id'  => $winner,
        'updated_at' => date('Y-m-d H:i:s'),
    ], 'game_id = ?', [$gameId]);

    if ($winner > 0 || $userId > 0) {
        battleship_update_stats($winner, $userId);
    }

    return ['success' => true];
}

/**
 * Get current game state for the player
 */
function battleship_get_state(int $userId, int $gameId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    $game = $db->query("SELECT * FROM {$db_x}battleship_games WHERE game_id = ?", [$gameId])->fetch();
    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }

    $isP1 = ((int)$game['player1_id'] === $userId);
    $isP2 = ((int)$game['player2_id'] === $userId);
    if (!$isP1 && !$isP2) {
        return ['success' => false, 'message' => $L['battleship_err_access']];
    }

    $myBoard    = json_decode($isP1 ? $game['player1_board'] : $game['player2_board'], true) ?: [];
    $myShots    = json_decode($isP1 ? $game['player1_shots'] : $game['player2_shots'], true) ?: [];
    $enemyShots = json_decode($isP1 ? $game['player2_shots'] : $game['player1_shots'], true) ?: [];
    $enemyBoard = json_decode($isP1 ? $game['player2_board'] : $game['player1_board'], true) ?: [];

    $sunkEnemy = battleship_find_sunk($enemyBoard);
    $sunkMine  = battleship_find_sunk($myBoard);

    $winnerId = $game['winner_id'] !== null ? (int)$game['winner_id'] : null;
    $youWon = false;
    if ($game['status'] === 'finished' && $winnerId !== null) {
        if ((int)$game['player1_id'] === 0) {
            // AI game: human wins if winner_id != 0
            $youWon = ($winnerId !== 0);
        } else {
            $youWon = ($winnerId === $userId);
        }
    }

    return [
        'success'     => true,
        'status'      => $game['status'],
        'turn'        => (int) $game['turn'],
        'my_side'     => $isP1 ? 1 : 2,
        'my_board'    => $myBoard,
        'my_shots'    => $myShots,
        'enemy_shots' => $enemyShots,
        'sunk_enemy'  => $sunkEnemy['cells'],
        'sunk_mine'   => $sunkMine['cells'],
        'winner_id'   => $winnerId,
        'you_won'     => $youWon,
        'mode'        => ((int)$game['player1_id'] === 0) ? 'ai' : 'pvp',
        'updated_at'  => $game['updated_at'],
    ];
}

/**
 * List open PvP games waiting for opponent
 */
function battleship_list_waiting(int $userId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;

    $rows = $db->query(
        "SELECT game_id, player1_id, created_at FROM {$db_x}battleship_games
         WHERE status = 'placing' AND player2_id IS NULL AND player1_id <> 0 AND player1_id <> ?
         ORDER BY created_at DESC LIMIT 20",
        [$userId]
    )->fetchAll();

    return ['success' => true, 'games' => $rows];
}

/**
 * User statistics
 */
function battleship_user_stats(int $userId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;

    $row = $db->query(
        "SELECT wins, losses, rating FROM {$db_x}battleship_stats WHERE user_id = ?",
        [$userId]
    )->fetch();

    return [
        'success' => true,
        'stats'   => $row ?: ['wins' => 0, 'losses' => 0, 'rating' => 1000],
    ];
}


/**
 * List games of current user (all statuses)
 */
function battleship_list_my_games(int $userId, int $limit = 30): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;

    $rows = $db->query(
        "SELECT game_id, player1_id, player2_id, status, turn, winner_id, created_at, updated_at
         FROM {$db_x}battleship_games
         WHERE player1_id = ? OR player2_id = ?
         ORDER BY updated_at DESC
         LIMIT " . (int) $limit,
        [$userId, $userId]
    )->fetchAll();

    return ['success' => true, 'games' => $rows ?: []];
}

/**
 * Cancel / abandon game.
 * - placing: just mark finished without rating change (or soft-delete feel)
 * - active: treat as surrender (opponent wins)
 */
function battleship_abandon_game(int $userId, int $gameId): array
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    $L = Cot::$L;

    $game = $db->query(
        "SELECT * FROM {$db_x}battleship_games WHERE game_id = ? AND status IN ('placing', 'active')",
        [$gameId]
    )->fetch();

    if (!$game) {
        return ['success' => false, 'message' => $L['battleship_err_no_game']];
    }

    $isP1 = ((int) $game['player1_id'] === $userId);
    $isP2 = ((int) $game['player2_id'] === $userId);
    if (!$isP1 && !$isP2) {
        return ['success' => false, 'message' => $L['battleship_err_access']];
    }

    $now = date('Y-m-d H:i:s');

    // Still placing (no real match started) — free the slot without rating loss
    if ($game['status'] === 'placing') {
        $db->update($db_x . 'battleship_games', [
            'status'     => 'finished',
            'winner_id'  => null,
            'updated_at' => $now,
        ], 'game_id = ?', [$gameId]);
        return ['success' => true, 'message' => $L['battleship_abandoned'] ?? 'Игра отменена'];
    }

    // Active — opponent wins (surrender)
    $winner = $isP1 ? (int) $game['player2_id'] : (int) $game['player1_id'];
    $db->update($db_x . 'battleship_games', [
        'status'     => 'finished',
        'winner_id'  => $winner,
        'updated_at' => $now,
    ], 'game_id = ?', [$gameId]);
    battleship_update_stats($winner, $userId);

    return ['success' => true, 'message' => $L['battleship_status_lose']];
}

/**
 * Count active+placing games for user
 */
function battleship_count_active(int $userId): int
{
    $db = Cot::$db;
    $db_x = Cot::$db_x;
    return (int) $db->query(
        "SELECT COUNT(*) FROM {$db_x}battleship_games
         WHERE (player1_id = ? OR player2_id = ?) AND status IN ('placing', 'active')",
        [$userId, $userId]
    )->fetchColumn();
}
