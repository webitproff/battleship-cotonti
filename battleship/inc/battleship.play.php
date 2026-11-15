<?php
/**
 * File: plugins/battleship/inc/battleship.play.php
 * Mode: play — конкретная партия
 */

defined('COT_CODE') or die('Wrong URL.');

cot_block(Cot::$usr['id'] > 0);

$gid = (int) cot_import('id', 'G', 'INT');
if ($gid < 1) {
	cot_message(Cot::$L['battleship_err_no_game'], 'error');
	cot_redirect(cot_url('plug', 'e=battleship&m=my', '', true));
}

$state = battleship_get_state((int) Cot::$usr['id'], $gid);
if (empty($state['success'])) {
	cot_message($state['message'] ?? Cot::$L['battleship_err_no_game'], 'error');
	cot_redirect(cot_url('plug', 'e=battleship&m=my', '', true));
}

$out['subtitle'] = Cot::$L['battleship_title'] . ' #' . $gid;

$mskin = cot_tplfile(['battleship', 'play'], 'plug');
$t = new XTemplate($mskin);

$t->assign([
	'AJAX_URL'               => cot_url('plug', ['r' => 'battleship']),
	'IS_LOGGED_IN'           => true,
	'XK'                     => Cot::$sys['xk'],
	'GAME_ID'                => $gid,
	'URL_MY'                 => cot_url('plug', 'e=battleship&m=my'),
	'URL_HOME'               => cot_url('plug', 'e=battleship'),
	'LANG_YOUR_TURN'         => Cot::$L['battleship_status_your_turn'],
	'LANG_ENEMY_TURN'        => Cot::$L['battleship_status_enemy_turn'],
	'LANG_WIN'               => Cot::$L['battleship_status_win'],
	'LANG_LOSE'              => Cot::$L['battleship_status_lose'],
	'LANG_NOT_YOUR_TURN'     => Cot::$L['battleship_status_not_your_turn'],
	'LANG_ALREADY_SHOT'      => Cot::$L['battleship_status_already_shot'],
	'LANG_UNKNOWN'           => Cot::$L['battleship_err_unknown'],
	'LANG_WAITING'           => Cot::$L['battleship_status_waiting'],
	'LANG_PLACE_HINT'        => Cot::$L['battleship_status_place_hint'],
	'LANG_NOT_ALL_PLACED'    => Cot::$L['battleship_status_not_all_placed'],
	'LANG_MSG_HIT'           => Cot::$L['battleship_msg_hit'],
	'LANG_MSG_MISS'          => Cot::$L['battleship_msg_miss'],
	'LANG_MSG_SUNK'          => Cot::$L['battleship_msg_sunk'],
	'LANG_MSG_DAMAGED'       => Cot::$L['battleship_msg_damaged'],
	'LANG_MSG_WIN'           => Cot::$L['battleship_msg_win'],
	'LANG_MSG_LOSE'          => Cot::$L['battleship_msg_lose'],
	'LANG_MSG_YOUR_TURN'     => Cot::$L['battleship_msg_your_turn'],
	'LANG_MSG_ENEMY_TURN'    => Cot::$L['battleship_msg_enemy_turn'],
	'LANG_CONFIRM_SURRENDER' => Cot::$L['battleship_confirm_surrender'],
	'LANG_AI_THINKING'       => Cot::$L['battleship_ai_thinking'],
	'LANG_SHIPS_LEFT'        => Cot::$L['battleship_ships_left'],
]);

