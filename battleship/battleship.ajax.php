<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL.');

/** File: battleship.ajax.php 
 *  Path: plugins/battleship/battleship.ajax.php 
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
 
 
require_once cot_incfile('battleship', 'plug');
battleship_lang_load();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (Cot::$usr['id'] == 0) {
    echo json_encode(['success' => false, 'message' => Cot::$L['battleship_err_auth']]);
    exit;
}

$x = cot_import('x', 'P', 'ALP');
if ($x === null || ($x !== Cot::$sys['xk'] && (!isset(Cot::$sys['xk_prev']) || $x !== Cot::$sys['xk_prev']))) {
    echo json_encode(['success' => false, 'message' => 'CSRF token mismatch']);
    exit;
}

$action = (string) cot_import('action', 'P', 'ALP');
$gid    = (int) cot_import('game_id', 'P', 'INT');
$uid    = (int) Cot::$usr['id'];

try {
    switch ($action) {
        case 'lobby':
            $data = battleship_lobby($uid);
            break;
        case 'create':
            $data = battleship_create_game($uid, (string) cot_import('mode', 'P', 'ALP'));
            break;
        case 'join':
            $data = battleship_join_game($uid, $gid);
            break;
        case 'place':
            $data = battleship_place_ships($uid, $gid, battleship_board_from_string((string) cot_import('board', 'P', 'ALP')));
            break;
        case 'fire':
            $data = battleship_fire($uid, $gid, (int) cot_import('row', 'P', 'INT'), (int) cot_import('col', 'P', 'INT'));
            break;
        case 'state':
            $data = battleship_get_state($uid, $gid, (int) cot_import('rev', 'P', 'INT'));
            break;
        case 'chatsend':
            $data = battleship_chat_send($uid, $gid, isset($_POST['msg']) ? (string) $_POST['msg'] : '');
            break;
        case 'chatget':
            $data = battleship_chat_get($uid, $gid, (int) cot_import('after', 'P', 'INT'));
            break;
        case 'leave':
            $data = battleship_leave($uid, $gid);
            break;
        default:
            $data = ['success' => false, 'message' => Cot::$L['battleship_err_unknown']];
    }
    echo json_encode($data);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => Cot::$L['battleship_err_unknown']]);
}
exit;
