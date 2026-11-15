<?php
/**
 * File: battleship.my.php
 * Path: plugins/battleship/inc/battleship.my.php
 * Mode "my": my games - list, continue, cancel / surrender. Rendered on the server (no JS needed).
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
$bsX   = Cot::$db_x;

Cot::$out['subtitle'] = Cot::$L['battleship_title'] . ' - ' . Cot::$L['battleship_nav_my'];

// Cancel / surrender
if ($bsUid > 0) {
	$bsAct = cot_import('a', 'G', 'ALP');
	$bsId  = (int) cot_import('id', 'G', 'INT');
	if ($bsAct === 'cancel' && $bsId > 0) {
		cot_check_xg();
		try {
			$bsRes = battleship_leave($bsUid, $bsId);
		} catch (Throwable $e) {
			$bsRes = ['success' => false, 'message' => Cot::$L['battleship_err_unknown']];
		}
		cot_message($bsRes['success'] ? Cot::$L['battleship_my_cancelled'] : $bsRes['message'], $bsRes['success'] ? 'ok' : 'error');
		cot_redirect(battleship_url('my', [], true));
	}
}

$t = new XTemplate(cot_tplfile(['battleship', 'my'], 'plug'));
$t->assign(battleship_tpl_vars('my'));

if ($bsUid > 0) {
	battleship_cleanup();

	$bsUsers = '`' . $bsX . 'users`';
	$bsRows = Cot::$db->query(
		"SELECT g.*, u1.user_name AS n1, u2.user_name AS n2
		 FROM {$bsX}battleship_games g
		 LEFT JOIN {$bsUsers} u1 ON u1.user_id = g.player1_id
		 LEFT JOIN {$bsUsers} u2 ON u2.user_id = g.player2_id
		 WHERE g.player1_id = ? OR g.player2_id = ?
		 ORDER BY g.updated_at DESC LIMIT 60",
		[$bsUid, $bsUid]
	)->fetchAll();

	$bsActive = 0;
	$bsDone   = 0;
	foreach ($bsRows as $g) {
		$side   = (int) $g['player1_id'] === $bsUid ? 1 : 2;
		$isAi   = (int) $g['is_ai'] === 1;
		$oppId  = $side === 1 ? $g['player2_id'] : $g['player1_id'];
		$oppNm  = $side === 1 ? $g['n2'] : $g['n1'];
		if ($isAi) {
			$opp = Cot::$L['battleship_ai_name'];
		} elseif ($oppId === null) {
			$opp = Cot::$L['battleship_wait_opp'];
		} else {
			$opp = (string) $oppNm;
		}
		$mode = $isAi ? Cot::$L['battleship_my_mode_ai'] : Cot::$L['battleship_my_mode_pvp'];

		if ($g['status'] !== 'finished') {
			$myTurn = $g['status'] === 'active' && (int) $g['turn'] === $side;
			$t->assign([
				'ROW_ID'           => (int) $g['game_id'],
				'ROW_MODE'         => $mode,
				'ROW_OPP'          => htmlspecialchars($opp),
				'ROW_STATUS'       => $g['status'] === 'placing' ? Cot::$L['battleship_state_placing'] : ($myTurn ? Cot::$L['battleship_state_your_turn'] : Cot::$L['battleship_state_enemy_turn']),
				'ROW_STATUS_CLASS' => $g['status'] === 'placing' ? 'warning' : ($myTurn ? 'success' : 'secondary'),
				'ROW_UPDATED'      => $g['updated_at'],
				'ROW_PLAY_URL'     => battleship_url('play', ['id' => (int) $g['game_id']]),
				'ROW_CANCEL_URL'   => battleship_url('my', ['a' => 'cancel', 'id' => (int) $g['game_id'], 'x' => Cot::$sys['xk']]),
				'ROW_CANCEL_LABEL' => ($g['status'] === 'active') ? Cot::$L['battleship_btn_surrender'] : Cot::$L['battleship_btn_cancel_game'],
			]);
			$t->parse('MAIN.ACTIVE_ROW');
			$bsActive++;
		} elseif ($bsDone < 15) {
			$ws = (int) $g['winner_side'];
			if ($ws === 0) {
				$res = Cot::$L['battleship_my_res_none'];
				$cls = 'secondary';
			} elseif ($ws === $side) {
				$res = Cot::$L['battleship_result_win'];
				$cls = 'success';
			} else {
				$res = Cot::$L['battleship_result_lose'];
				$cls = 'danger';
			}
			$t->assign([
				'FROW_ID'           => (int) $g['game_id'],
				'FROW_MODE'         => $mode,
				'FROW_OPP'          => htmlspecialchars($opp),
				'FROW_RESULT'       => $res,
				'FROW_RESULT_CLASS' => $cls,
				'FROW_REASON'       => Cot::$L['battleship_my_reason_' . ($g['finish_reason'] ?: 'sunk')] ?? '',
				'FROW_UPDATED'      => $g['updated_at'],
			]);
			$t->parse('MAIN.FINISHED_ROW');
			$bsDone++;
		}
	}
	if (!$bsActive) {
		$t->parse('MAIN.ACTIVE_EMPTY');
	}
	if (!$bsDone) {
		$t->parse('MAIN.FINISHED_EMPTY');
	}
}

cot_display_messages($t);

