--
-- File: battleship.uninstall.sql
-- Path: plugins/battleship/setup/battleship.uninstall.sql
-- Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
-- Description=Battleship: vs AI and PvP with Chat & Sounds
-- Version=3.3.3
-- Date=2026-11-16
-- Author=webitproff
-- Copyright=webitproff https://github.com/webitproff/battleship-cotonti
-- 
-- 

DROP TABLE IF EXISTS `cot_battleship_games`;
DROP TABLE IF EXISTS `cot_battleship_stats`;
DROP TABLE IF EXISTS `cot_battleship_chat`;
