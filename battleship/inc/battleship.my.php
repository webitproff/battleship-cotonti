<?php
/**
 * File: plugins/battleship/inc/battleship.my.php
 * Mode: my — список и управление своими играми
 */

defined('COT_CODE') or die('Wrong URL.');

cot_block(Cot::$usr['id'] > 0);

$out['subtitle'] = Cot::$L['battleship_my_title'] ?? 'Мои игры';

// POST abandon
$a = cot_import('a', 'G', 'ALP');
$gid = (int) cot_import('id', 'G', 'INT');
if ($a === 'abandon' && $gid > 0) {
	cot_check_xg();
	$res = battleship_abandon_game((int) Cot::$usr['id'], $gid);
	if ($res['success']) {
		cot_message($res['message'] ?? Cot::$L['battleship_abandoned'], 'ok');
	} else {
		cot_message($res['message'] ?? Cot::$L['battleship_err_unknown'], 'error');
	}
	cot_redirect(cot_url('plug', 'e=battleship&m=my', '', true));
}

$list = battleship_list_my_games((int) Cot::$usr['id'], 50);
$games = $list['games'] ?? [];
$activeCount = battleship_count_active((int) Cot::$usr['id']);
$maxActive = (int) (Cot::$cfg['plugin']['battleship']['max_active_games'] ?? 3);

$mskin = cot_tplfile(['battleship', 'my'], 'plug');
$t = new XTemplate($mskin);

$t->assign([
	'URL_HOME'     => cot_url('plug', 'e=battleship'),
	'URL_MY'       => cot_url('plug', 'e=battleship&m=my'),
	'ACTIVE_COUNT' => $activeCount,
	'MAX_ACTIVE'   => $maxActive,
	'XK'           => Cot::$sys['xk'],
]);

$statusLabels = [
	'placing'  => Cot::$L['battleship_status_placing'] ?? 'Расстановка',
	'active'   => Cot::$L['battleship_status_active'] ?? 'Идёт',
	'finished' => Cot::$L['battleship_status_finished'] ?? 'Завершена',
];

foreach ($games as $g) {
	$gid = (int) $g['game_id'];
	$st = $g['status'];
	$isAi = ((int) $g['player1_id'] === 0);
	$canContinue = in_array($st, ['placing', 'active'], true);
	$canAbandon = $canContinue;

	$modeLabel = $isAi ? 'AI' : 'PvP';

	$t->assign([
		'GAME_ID'         => $gid,
		'GAME_STATUS'     => $st,
		'GAME_STATUS_L'   => $statusLabels[$st] ?? $st,
		'GAME_MODE'       => $modeLabel,
		'GAME_CREATED'    => $g['created_at'],
		'GAME_UPDATED'    => $g['updated_at'],
		'GAME_URL_PLAY'   => cot_url('plug', 'e=battleship&m=play&id=' . $gid),
		'GAME_URL_ABANDON'=> cot_url('plug', 'e=battleship&m=my&a=abandon&id=' . $gid . '&x=' . Cot::$sys['xk']),
		'GAME_CAN_PLAY'   => $canContinue,
		'GAME_CAN_ABANDON'=> $canAbandon,
		'GAME_WINNER'     => $g['winner_id'],
	]);
	$t->parse('MAIN.GAME_ROW');
}

if (!$games) {
	$t->parse('MAIN.EMPTY');
}

cot_display_messages($t);
