-- plugins/battleship/setup/battleship.install.sql
-- Database schema for Battleship plugin

CREATE TABLE IF NOT EXISTS `cot_battleship_games` (
  `game_id` INT(11) NOT NULL AUTO_INCREMENT,
  `player1_id` INT(11) NOT NULL DEFAULT 0,
  `player2_id` INT(11) DEFAULT NULL,
  `player1_board` TEXT NOT NULL,
  `player2_board` TEXT DEFAULT NULL,
  `player1_shots` TEXT NOT NULL,
  `player2_shots` TEXT NOT NULL,
  `status` ENUM('placing','active','finished') NOT NULL DEFAULT 'placing',
  `turn` TINYINT(4) NOT NULL DEFAULT 1,
  `winner_id` INT(11) DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`game_id`),
  KEY `status` (`status`),
  KEY `p1` (`player1_id`),
  KEY `p2` (`player2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cot_battleship_stats` (
  `user_id` INT(11) NOT NULL,
  `wins` INT(11) NOT NULL DEFAULT 0,
  `losses` INT(11) NOT NULL DEFAULT 0,
  `rating` INT(11) NOT NULL DEFAULT 1000,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
