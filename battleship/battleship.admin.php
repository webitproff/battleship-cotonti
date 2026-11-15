<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * File: battleship.admin.php
 * Path: plugins/battleship/battleship.admin.php
 * Admin tools (Admin -> Other -> Battleship): games list with filter, delete a game,
 * purge finished games, top players.
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP
 *  Version=3.3.2
 *  Date=2026-11-15
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  Notes=BSD
 */
defined('COT_CODE') or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'battleship', 'RWA');
cot_block($usr['isadmin']);

require_once cot_incfile('battleship', 'plug');
battleship_lang_load();

$db   = Cot::$db;
$db_x = Cot::$db_x;
$L    = Cot::$L;

$status = (string) cot_import('status', 'G', 'ALP');
if (!in_array($status, ['placing', 'active', 'finished'], true)) {
	$status = '';
}
$a  = cot_import('a', 'G', 'ALP');
$id = (int) cot_import('id', 'G', 'INT');

$base = 'm=other&p=battleship';

// Delete one game (and its chat)
if ($a === 'delete' && $id > 0) {
	cot_check_xg();
	$db->delete($db_x . 'battleship_games', 'game_id = ?', [$id]);
	battleship_chat_purge($id);
	cot_message($L['battleship_admin_deleted'], 'ok');
	cot_redirect(cot_url('admin', $base . ($status ? '&status=' . $status : ''), '', true));
}

// Purge all finished games
if ($a === 'purge_finished') {
	cot_check_xg();
	$n = $db->delete($db_x . 'battleship_games', "status = 'finished'");
	battleship_chat_sweep();
	cot_message($L['battleship_admin_purged'] . ': ' . (int) $n, 'ok');
	cot_redirect(cot_url('admin', $base, '', true));
}

Cot::$out['subtitle'] = $L['battleship_admin_title'];

$where = $status ? ('WHERE g.status = ' . $db->quote($status)) : '';

$totalGames = (int) $db->query("SELECT COUNT(*) FROM {$db_x}battleship_games g $where")->fetchColumn();
$cnt = ['placing' => 0, 'active' => 0, 'finished' => 0];
foreach ($db->query("SELECT status, COUNT(*) AS n FROM {$db_x}battleship_games GROUP BY status")->fetchAll() as $r) {
	$cnt[$r['status']] = (int) $r['n'];
}

$perPage = 30;
list($pn, $d, $d_url) = cot_import_pagenav('d', $perPage);

$ut = '`' . $db_x . 'users`';
$games = $db->query(
	"SELECT g.*, u1.user_name AS n1, u2.user_name AS n2
	 FROM {$db_x}battleship_games g
	 LEFT JOIN {$ut} u1 ON u1.user_id = g.player1_id
	 LEFT JOIN {$ut} u2 ON u2.user_id = g.player2_id
	 $where
	 ORDER BY g.updated_at DESC LIMIT " . (int) $d . ', ' . (int) $perPage
)->fetchAll();

$pagenav = cot_pagenav('admin', $base . ($status ? '&status=' . $status : ''), $d, $totalGames, $perPage);

$top = $db->query(
	"SELECT s.*, u.user_name FROM {$db_x}battleship_stats s
	 LEFT JOIN {$ut} u ON u.user_id = s.user_id
	 ORDER BY s.rating DESC, s.wins DESC LIMIT 15"
)->fetchAll();

$t = new XTemplate(cot_tplfile(['battleship', 'admin'], 'plug'));

$t->assign([
	'ADMIN_TITLE'         => $L['battleship_admin_title'],
	'FILTER_ALL_URL'      => cot_url('admin', $base),
	'FILTER_PLACING_URL'  => cot_url('admin', $base . '&status=placing'),
	'FILTER_ACTIVE_URL'   => cot_url('admin', $base . '&status=active'),
	'FILTER_FINISHED_URL' => cot_url('admin', $base . '&status=finished'),
	'FILTER_ALL_CLASS'      => $status === '' ? 'active' : '',
	'FILTER_PLACING_CLASS'  => $status === 'placing' ? 'active' : '',
	'FILTER_ACTIVE_CLASS'   => $status === 'active' ? 'active' : '',
	'FILTER_FINISHED_CLASS' => $status === 'finished' ? 'active' : '',
	'PURGE_URL'           => cot_url('admin', $base . '&a=purge_finished&x=' . Cot::$sys['xk']),
	'CNT_ALL'             => array_sum($cnt),
	'CNT_PLACING'         => $cnt['placing'],
	'CNT_ACTIVE'          => $cnt['active'],
	'CNT_FINISHED'        => $cnt['finished'],
	'PAGENAV_PAGES'       => $pagenav['main'] ?? '',
	'PAGENAV_PREV'        => $pagenav['prev'] ?? '',
	'PAGENAV_NEXT'        => $pagenav['next'] ?? '',
	'FILTER_STATUS'       => $status,
]);

$statusL = [
	'placing'  => $L['battleship_admin_st_placing'],
	'active'   => $L['battleship_admin_st_active'],
	'finished' => $L['battleship_admin_st_finished'],
];

foreach ($games as $g) {
	$isAi = (int) $g['is_ai'] === 1;
	$ws   = (int) $g['winner_side'];
	if ($g['status'] !== 'finished' || $ws === 0) {
		$winner = '—';
	} elseif ($ws === 1) {
		$winner = (string) $g['n1'];
	} else {
		$winner = $isAi ? $L['battleship_ai_name'] : (string) $g['n2'];
	}
	$t->assign([
		'GAME_ID'         => (int) $g['game_id'],
		'GAME_MODE'       => $isAi ? 'AI' : 'PvP',
		'GAME_STATUS'     => $statusL[$g['status']] ?? $g['status'],
		'GAME_STATUS_KEY' => $g['status'],
		'GAME_P1'         => htmlspecialchars((string) ($g['n1'] ?: ('#' . $g['player1_id']))),
		'GAME_P2'         => $isAi ? $L['battleship_ai_name'] : ($g['player2_id'] === null ? '—' : htmlspecialchars((string) ($g['n2'] ?: ('#' . $g['player2_id'])))),
		'GAME_WINNER'     => htmlspecialchars($winner),
		'GAME_REASON'     => $g['finish_reason'] !== '' ? ($L['battleship_my_reason_' . $g['finish_reason']] ?? $g['finish_reason']) : '—',
		'GAME_CREATED'    => $g['created_at'],
		'GAME_UPDATED'    => $g['updated_at'],
		'GAME_DELETE_URL' => cot_url('admin', $base . '&a=delete&id=' . (int) $g['game_id'] . '&status=' . $status . '&x=' . Cot::$sys['xk']),
	]);
	$t->parse('MAIN.GAME_ROW');
}
if (!$games) {
	$t->parse('MAIN.EMPTY');
}

foreach ($top as $row) {
	$t->assign([
		'TOP_NAME'   => htmlspecialchars((string) ($row['user_name'] ?: ('#' . $row['user_id']))),
		'TOP_WINS'   => (int) $row['wins'],
		'TOP_LOSSES' => (int) $row['losses'],
		'TOP_AI'     => (int) $row['ai_wins'] . ' / ' . (int) $row['ai_losses'],
		'TOP_RATING' => (int) $row['rating'],
		'TOP_UID'    => (int) $row['user_id'],
	]);
	$t->parse('MAIN.TOP_ROW');
}
if (!$top) {
	$t->parse('MAIN.TOP_EMPTY');
}

cot_display_messages($t);
$t->parse('MAIN');
$pluginBody = $t->text('MAIN');
