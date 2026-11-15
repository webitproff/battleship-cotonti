<?php
/**
 * File: plugins/battleship/battleship.ajax.php
 * AJAX endpoint for Battleship actions
 */

/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('battleship', 'plug');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

if (Cot::$usr['id'] == 0) {
    echo json_encode(['success' => false, 'message' => Cot::$L['battleship_err_auth']]);
    exit;
}

$x = cot_import('x', 'P', 'ALP');
if ($x === null || ($x !== Cot::$sys['xk'] && (!isset(Cot::$sys['xk_prev']) || $x !== Cot::$sys['xk_prev']))) {
    echo json_encode(['success' => false, 'message' => Cot::$L['battleship_err_csrf'] ?? 'CSRF token mismatch']);
    exit;
}

$action = cot_import('action', 'P', 'TXT');
$gid    = (int) cot_import('game_id', 'P', 'INT');
$uid    = (int) Cot::$usr['id'];

try {
    $data = match ($action) {
        'create'    => battleship_create_game($uid, (string) cot_import('mode', 'P', 'TXT')),
        'join'      => battleship_join_game($uid, $gid),
        'place'     => battleship_place_ships($uid, $gid, json_decode((string) cot_import('board', 'P', 'TXT'), true)),
        'fire'      => battleship_fire($uid, $gid, (int) cot_import('x2', 'P', 'INT'), (int) cot_import('y2', 'P', 'INT')),
        'aimove'    => battleship_ai_move($uid, $gid),
        'state'     => battleship_get_state($uid, $gid),
        'list'      => battleship_list_waiting($uid),
        'mylist'    => battleship_list_my_games($uid),
        'stats'     => battleship_user_stats($uid),
        'surrender' => battleship_surrender($uid, $gid),
        'abandon'   => battleship_abandon_game($uid, $gid),
        default     => ['success' => false, 'message' => Cot::$L['battleship_err_unknown']],
    };
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
exit;
