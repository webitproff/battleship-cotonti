<?php
/**
 * File: plugins/battleship/battleship.php
 * Standalone entry + mode router
 *
 * Modes:
 *   (default)/home — лобби
 *   my             — мои игры (управление)
 *   play           — игра (id=game_id)
 */

/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
Order=10
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL.');

$env['location'] = 'battleship';

require_once cot_incfile('battleship', 'plug');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'battleship');
cot_block($usr['auth_read']);

$m = cot_import('m', 'G', 'ALP');
if (empty($m) || !in_array($m, ['home', 'my', 'play'], true)) {
	$m = 'home';
}

// CSS/JS only for play + home (boards)
if (in_array($m, ['home', 'play'], true)) {
	Resources::addFile(Cot::$cfg['plugins_dir'] . '/battleship/css/battleship.css', 'css', 50, 'global');
	Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/battleship/js/battleship.js');
}

require_once cot_incfile('battleship', 'plug', $m);
