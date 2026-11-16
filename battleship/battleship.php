<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
Order=10
[END_COT_EXT]
==================== */

/**
 * File: battleship.php
 * Path: plugins/battleship/battleship.php
 * Standalone entry + mode router
 *
 * Modes (files in plugins/battleship/inc/, templates in plugins/battleship/tpl/):
 *   home (default) - ?e=battleship              lobby: AI / PvP / open games / statistics
 *   my             - ?e=battleship&m=my          my games: list, continue, cancel
 *   play           - ?e=battleship&m=play&id=N   a single game
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
 

defined('COT_CODE') or die('Wrong URL.');

Cot::$env['location'] = 'battleship';

require_once cot_incfile('battleship', 'plug');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'battleship');
cot_block($usr['auth_read']);

battleship_lang_load();

$m = cot_import('m', 'G', 'ALP');
if (empty($m) || !in_array($m, ['home', 'my', 'play'], true)) {
	$m = 'home';
}

// CSS/JS only where boards are drawn (home + play)
if (in_array($m, ['home', 'play'], true)) {
	Resources::addFile(Cot::$cfg['plugins_dir'] . '/battleship/css/battleship.css', 'css', 50, 'global');
	Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/battleship/js/battleship.js');
}

require_once cot_incfile('battleship', 'plug', $m);


/* 
 * мы внутри хука "standalone", дальше смотрим в файле system/plugin.php 
 * ... $pluginBody = Cot::$currentRoute->controller->runAction(Cot::$currentRoute->action);
 * ..... 'BODY' => $pluginBody,
 * ....... $t->parse('MAIN'); и $t->out('MAIN');
 * поэтому здесь не дублируем указанное выше
 * 
 */
