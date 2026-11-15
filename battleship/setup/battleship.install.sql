-- File: battleship.install.sql
-- Path: plugins/battleship/setup/battleship.install.sql

CREATE TABLE IF NOT EXISTS `cot_battleship_games` (
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

CREATE TABLE IF NOT EXISTS `cot_battleship_stats` (
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

CREATE TABLE IF NOT EXISTS `cot_battleship_chat` (
  `msg_id` INT(11) NOT NULL AUTO_INCREMENT,
  `game_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `created_ts` INT(11) NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `game_msg` (`game_id`, `msg_id`),
  KEY `user_ts` (`user_id`, `created_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
