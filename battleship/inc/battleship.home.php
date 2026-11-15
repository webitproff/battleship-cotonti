<?php
/**
 * File: plugins/battleship/inc/battleship.home.php
 * Mode: home (lobby)
 */

defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] <= 0) {
	// guest: still show page shell
}

$out['subtitle'] = Cot::$L['battleship_title'];

$mskin = cot_tplfile(['battleship', 'home'], 'plug');
if (!$mskin) {
	$mskin = cot_tplfile('battleship', 'plug'); // fallback
}
$t = new XTemplate($mskin);

$activeCount = Cot::$usr['id'] > 0 ? battleship_count_active((int) Cot::$usr['id']) : 0;
$maxActive = (int) (Cot::$cfg['plugin']['battleship']['max_active_games'] ?? 3);

$t->assign([
	'AJAX_URL'               => cot_url('plug', ['r' => 'battleship']),
	'IS_LOGGED_IN'           => (Cot::$usr['id'] > 0),
	'XK'                     => Cot::$sys['xk'],
	'URL_MY'                 => cot_url('plug', 'e=battleship&m=my'),
	'URL_HOME'               => cot_url('plug', 'e=battleship'),
	'ACTIVE_COUNT'           => $activeCount,
	'MAX_ACTIVE'             => $maxActive,
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

