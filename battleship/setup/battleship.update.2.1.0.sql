-- File: battleship.update.2.1.0.sql
-- Path: plugins/battleship/setup/battleship.update.2.1.0.sql
-- 2.1.0: PvP chat (messages live only while the game status is 'active').

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
