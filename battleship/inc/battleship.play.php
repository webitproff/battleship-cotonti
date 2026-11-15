<?php
/**
 * File: battleship.play.php
 * Path: plugins/battleship/inc/battleship.play.php
 * Mode "play": a single game (?e=battleship&m=play&id=N). Only the two players may open it.
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

$bsUid = (int) Cot::$usr['id'];
$bsGid = (int) cot_import('id', 'G', 'INT');

if ($bsUid > 0) {
	$bsGame = $bsGid > 0
		? Cot::$db->query('SELECT player1_id, player2_id FROM ' . Cot::$db_x . 'battleship_games WHERE game_id = ?', [$bsGid])->fetch()
		: false;
	if (!$bsGame || battleship_side($bsGame, $bsUid) === 0) {
		cot_message(Cot::$L['battleship_err_no_game'], 'error');
		cot_redirect(battleship_url('home', [], true));
	}
}

Cot::$out['subtitle'] = Cot::$L['battleship_title'] . ' #' . $bsGid;

$t = new XTemplate(cot_tplfile(['battleship', 'play'], 'plug'));
$t->assign(battleship_tpl_vars('play', ['gameId' => $bsGid]));
$t->assign('BS_GAME_ID', $bsGid);

cot_display_messages($t);

