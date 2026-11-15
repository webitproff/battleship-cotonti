<?php
/* ====================
[BEGIN_COT_EXT]
Code=battleship
Name=Battleship
Description=Battleship: vs AI and PvP (Bootstrap 5.3)
Version=3.3.2
Date=2026-11-15
Author=webitproff
Copyright=webitproff https://github.com/webitproff/battleship-cotonti
Notes=BSD
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
max_active_games=01:select:1,2,3,5,10:3:Max active games per user
hit_again=02:radio:0:1:1:Extra turn after a hit
ai_level=03:select:easy,normal,hard:normal:AI difficulty
abandon_hours=04:select:1,6,24,72,168:24:Close inactive games after (hours)
chat_enabled=05:radio:0:1:1:Chat between PvP players
chat_cooldown=06:select:3,5,10,15:5:Chat message interval (seconds)
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL.');

/* 
 * File: battleship.setup.php 
 * Path: plugins/battleship/battleship.setup.php
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP
 *  Version=3.3.2
 *  Date=2026-11-15
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  Notes=BSD
 */
