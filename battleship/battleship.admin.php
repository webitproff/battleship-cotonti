<?php
/**
 * File: plugins/battleship/battleship.admin.php
 * Admin tools: список игр, статистика, удаление
 */

/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'battleship', 'RWA');
cot_block($usr['isadmin']);

require_once cot_incfile('battleship', 'plug');

Cot::$db->registerTable('battleship_games');
Cot::$db->registerTable('battleship_stats');

$db = Cot::$db;
$db_x = Cot::$db_x;
$L = Cot::$L;
$cfg = Cot::$cfg;

$status = cot_import('status', 'G', 'ALP');
$a = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');

// Удаление игры
if ($a === 'delete' && $id > 0) {
	cot_check_xg();
	$db->delete($db_x . 'battleship_games', 'game_id = ?', [$id]);
	cot_message($L['battleship_admin_deleted'] ?? 'Игра удалена', 'ok');
	cot_redirect(cot_url('admin', 'm=other&p=battleship' . ($status ? '&status=' . $status : ''), '', true));
}

// Массовая очистка finished
if ($a === 'purge_finished') {
	cot_check_xg();
	$n = $db->delete($db_x . 'battleship_games', "status = 'finished'");
	cot_message(($L['battleship_admin_purged'] ?? 'Удалено завершённых игр') . ': ' . (int) $n, 'ok');
	cot_redirect(cot_url('admin', 'm=other&p=battleship', '', true));
}

$out['subtitle'] = $L['battleship_admin_title'] ?? 'Battleship — админка';
$adminTitle = $L['battleship_admin_title'] ?? 'Battleship';
$adminHelp = $L['battleship_admin_help'] ?? '';

$where = [];
switch ($status) {
	case 'placing':
	case 'active':
	case 'finished':
		$where[] = "status = " . $db->quote($status);
		break;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$totalGames = (int) $db->query("SELECT COUNT(*) FROM {$db_x}battleship_games $whereSql")->fetchColumn();
$cntPlacing = (int) $db->query("SELECT COUNT(*) FROM {$db_x}battleship_games WHERE status='placing'")->fetchColumn();
$cntActive  = (int) $db->query("SELECT COUNT(*) FROM {$db_x}battleship_games WHERE status='active'")->fetchColumn();
$cntFinished= (int) $db->query("SELECT COUNT(*) FROM {$db_x}battleship_games WHERE status='finished'")->fetchColumn();

$perPage = 30;
list($pn, $d, $d_url) = cot_import_pagenav('d', $perPage);

$games = $db->query(
	"SELECT * FROM {$db_x}battleship_games $whereSql ORDER BY updated_at DESC LIMIT " . (int) $d . ", " . (int) $perPage
)->fetchAll();

$pagenav = cot_pagenav('admin', 'm=other&p=battleship&status=' . $status, $d, $totalGames, $perPage);

$topPlayers = $db->query(
	"SELECT s.*, u.user_name FROM {$db_x}battleship_stats s
	 LEFT JOIN {$db_x}users u ON u.user_id = s.user_id
	 ORDER BY s.rating DESC, s.wins DESC LIMIT 15"
)->fetchAll();

$mskin = cot_tplfile(['battleship', 'admin'], 'plug');
$t = new XTemplate($mskin);

$t->assign([
	'ADMIN_TITLE'        => $adminTitle,
	'FILTER_ALL_URL'     => cot_url('admin', 'm=other&p=battleship'),
	'FILTER_PLACING_URL' => cot_url('admin', 'm=other&p=battleship&status=placing'),
	'FILTER_ACTIVE_URL'  => cot_url('admin', 'm=other&p=battleship&status=active'),
	'FILTER_FINISHED_URL'=> cot_url('admin', 'm=other&p=battleship&status=finished'),
	'PURGE_URL'          => cot_url('admin', 'm=other&p=battleship&a=purge_finished&x=' . Cot::$sys['xk']),
	'CNT_ALL'            => $cntPlacing + $cntActive + $cntFinished,
	'CNT_PLACING'        => $cntPlacing,
	'CNT_ACTIVE'         => $cntActive,
	'CNT_FINISHED'       => $cntFinished,
	'PAGENAV_PAGES'      => $pagenav['main'] ?? '',
	'PAGENAV_PREV'       => $pagenav['prev'] ?? '',
	'PAGENAV_NEXT'       => $pagenav['next'] ?? '',
	'FILTER_STATUS'      => $status,
]);

$statusL = [
	'placing'  => $L['battleship_status_placing'] ?? 'Расстановка',
	'active'   => $L['battleship_status_active'] ?? 'Идёт',
	'finished' => $L['battleship_status_finished'] ?? 'Завершена',
];

foreach ($games as $g) {
	$p1 = (int) $g['player1_id'];
	$p2 = (int) $g['player2_id'];
	$mode = ($p1 === 0) ? 'AI' : 'PvP';
	$t->assign([
		'GAME_ID'      => (int) $g['game_id'],
		'GAME_MODE'    => $mode,
		'GAME_STATUS'  => $statusL[$g['status']] ?? $g['status'],
		'GAME_P1'      => $p1 === 0 ? 'AI' : $p1,
		'GAME_P2'      => $p2 ?: '—',
		'GAME_WINNER'  => $g['winner_id'] !== null ? (int) $g['winner_id'] : '—',
		'GAME_CREATED' => $g['created_at'],
		'GAME_UPDATED' => $g['updated_at'],
		'GAME_DELETE_URL' => cot_url('admin', 'm=other&p=battleship&a=delete&id=' . (int) $g['game_id'] . '&status=' . $status . '&x=' . Cot::$sys['xk']),
	]);
	$t->parse('MAIN.GAME_ROW');
}

if (!$games) {
	$t->parse('MAIN.EMPTY');
}

foreach ($topPlayers as $row) {
	$t->assign([
		'TOP_NAME'   => $row['user_name'] ?: ('#' . $row['user_id']),
		'TOP_WINS'   => (int) $row['wins'],
		'TOP_LOSSES' => (int) $row['losses'],
		'TOP_RATING' => (int) $row['rating'],
		'TOP_UID'    => (int) $row['user_id'],
	]);
	$t->parse('MAIN.TOP_ROW');
}

cot_display_messages($t);
$t->parse('MAIN');
$pluginBody = $t->text('MAIN');
