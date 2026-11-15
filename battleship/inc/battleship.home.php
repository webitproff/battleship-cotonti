<?php
/**
 * File: battleship.home.php
 * Path: plugins/battleship/inc/battleship.home.php
 * Mode "home": lobby (vs AI / PvP / open games / statistics). The lobby itself is drawn by js/battleship.js.
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

Cot::$out['subtitle'] = Cot::$L['battleship_title'];

$t = new XTemplate(cot_tplfile(['battleship', 'home'], 'plug'));
$t->assign(battleship_tpl_vars('home'));

cot_display_messages($t);

