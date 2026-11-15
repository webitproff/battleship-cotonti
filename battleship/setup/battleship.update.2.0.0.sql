-- File: battleship.update.2.0.0.sql
-- Path: plugins/battleship/setup/battleship.update.2.0.0.sql
-- Upgrade from 1.x: the data format changed (board = ship ids, moves log),
-- so old games and statistics are recreated from scratch.

DROP TABLE IF EXISTS `cot_battleship_games`;
DROP TABLE IF EXISTS `cot_battleship_stats`;

CREATE TABLE `cot_battleship_games` (
  `game_id` INT(11) NOT NULL AUTO_INCREMENT,
  `player1_id` INT(11) NOT NULL DEFAULT 0,
  `player2_id` INT(11) DEFAULT NULL,
  `is_ai` TINYINT(1) NOT NULL DEFAULT 0,
  `player1_board` TEXT NOT NULL,
  `player2_board` TEXT NOT NULL,
  `moves` MEDIUMTEXT NOT NULL,
  `status` ENUM('placing','active','finished') NOT NULL DEFAULT 'placing',
  `turn` TINYINT(4) NOT NULL DEFAULT 1,
  `winner_side` TINYINT(4) NOT NULL DEFAULT 0,
  `winner_id` INT(11) DEFAULT NULL,
  `finish_reason` VARCHAR(16) NOT NULL DEFAULT '',
  `rev` INT(11) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`game_id`),
  KEY `status` (`status`),
  KEY `p1` (`player1_id`),
  KEY `p2` (`player2_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cot_battleship_stats` (
  `user_id` INT(11) NOT NULL,
  `wins` INT(11) NOT NULL DEFAULT 0,
  `losses` INT(11) NOT NULL DEFAULT 0,
  `ai_wins` INT(11) NOT NULL DEFAULT 0,
  `ai_losses` INT(11) NOT NULL DEFAULT 0,
  `rating` INT(11) NOT NULL DEFAULT 1000,
  `shots` INT(11) NOT NULL DEFAULT 0,
  `hits` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `rating` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
