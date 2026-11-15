<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
Order=10
[END_COT_EXT]
==================== */

/**
 * File: plugins/battleship/battleship.php
 * Standalone page entry for Battleship
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('battleship', 'plug');

Resources::addFile(Cot::$cfg['plugins_dir'] . '/battleship/css/battleship.css', 'css', 50, 'global');
Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/battleship/js/battleship.js');

$t->assign([
    'AJAX_URL'              => cot_url('plug', ['r' => 'battleship']),
    'IS_LOGGED_IN'          => (Cot::$usr['id'] > 0),
    'XK'                    => Cot::$sys['xk'],
    'LANG_YOUR_TURN'        => Cot::$L['battleship_status_your_turn'],
    'LANG_ENEMY_TURN'       => Cot::$L['battleship_status_enemy_turn'],
    'LANG_WIN'              => Cot::$L['battleship_status_win'],
    'LANG_LOSE'             => Cot::$L['battleship_status_lose'],
    'LANG_NOT_YOUR_TURN'    => Cot::$L['battleship_status_not_your_turn'],
    'LANG_ALREADY_SHOT'     => Cot::$L['battleship_status_already_shot'],
    'LANG_UNKNOWN'          => Cot::$L['battleship_err_unknown'],
    'LANG_WAITING'          => Cot::$L['battleship_status_waiting'],
    'LANG_PLACE_HINT'       => Cot::$L['battleship_status_place_hint'],
    'LANG_NOT_ALL_PLACED'   => Cot::$L['battleship_status_not_all_placed'],
    'LANG_MSG_HIT'          => Cot::$L['battleship_msg_hit'],
    'LANG_MSG_MISS'         => Cot::$L['battleship_msg_miss'],
    'LANG_MSG_SUNK'         => Cot::$L['battleship_msg_sunk'],
    'LANG_MSG_DAMAGED'      => Cot::$L['battleship_msg_damaged'],
    'LANG_MSG_WIN'          => Cot::$L['battleship_msg_win'],
    'LANG_MSG_LOSE'         => Cot::$L['battleship_msg_lose'],
    'LANG_MSG_YOUR_TURN'    => Cot::$L['battleship_msg_your_turn'],
    'LANG_MSG_ENEMY_TURN'   => Cot::$L['battleship_msg_enemy_turn'],
    'LANG_CONFIRM_SURRENDER'=> Cot::$L['battleship_confirm_surrender'],
    'LANG_AI_THINKING'      => Cot::$L['battleship_ai_thinking'],
    'LANG_SHIPS_LEFT'       => Cot::$L['battleship_ships_left'],
]);
