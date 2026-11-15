<?php
/**
 * File: plugins/battleship/lang/battleship.en.lang.php
 * English language file for Battleship plugin
 */

defined('COT_CODE') or die('Wrong URL.');

$L['battleship_title']                    = 'Battleship';
$L['battleship_subtitle']                 = 'Classic game against the computer or another player';
$L['battleship_btn_ai']                   = 'Against the computer';
$L['battleship_btn_pvp']                  = 'Create PvP game';
$L['battleship_btn_list']                 = 'Open games';
$L['battleship_btn_stats']                = 'My statistics';
$L['battleship_btn_rotate']               = 'Rotate';
$L['battleship_btn_random']               = 'Random';
$L['battleship_btn_confirm']              = 'Done';
$L['battleship_btn_surrender']            = 'Surrender';
$L['battleship_btn_clear']                = 'Clear';

$L['battleship_status_choose']            = 'Choose a game mode';
$L['battleship_status_place_hint']        = 'Place your ships. Click a cell to place the current ship. "Rotate" changes orientation.';
$L['battleship_status_not_all_placed']    = 'Not all ships are placed. Remaining: ';
$L['battleship_status_waiting']           = 'Waiting for an opponent...';
$L['battleship_status_your_turn']         = 'Your turn — fire!';
$L['battleship_status_enemy_turn']        = 'Opponent\'s turn...';
$L['battleship_status_win']               = 'Victory! The enemy fleet has been destroyed.';
$L['battleship_status_lose']              = 'Defeat. Your fleet has been sunk.';
$L['battleship_status_not_your_turn']     = 'It is not your turn.';
$L['battleship_status_already_shot']      = 'You have already fired at this cell.';
$L['battleship_status_sunk']              = 'Ship sunk!';
$L['battleship_status_placing']           = 'Ship placement';
$L['battleship_status_active']            = 'Game in progress';

$L['battleship_err_auth']                 = 'Authorization required.';
$L['battleship_err_limit']                = 'The limit of active games has been reached.';
$L['battleship_err_no_game']              = 'Game not found.';
$L['battleship_err_no_moves']             = 'No available moves.';
$L['battleship_err_not_ai_turn']          = 'It is not the computer\'s turn.';
$L['battleship_err_own_game']             = 'You cannot join your own game.';
$L['battleship_err_access']               = 'No access to the game.';
$L['battleship_err_coords']               = 'Invalid coordinates.';
$L['battleship_err_board']                = 'Invalid ship placement.';
$L['battleship_err_unknown']              = 'Unknown error.';
$L['battleship_err_csrf']                 = 'Security error (CSRF). Refresh the page.';

$L['battleship_placement_title']          = 'Ship placement';
$L['battleship_place_hint']               = 'Place your ships. Click a cell to place the current ship. "Rotate" changes orientation.';
$L['battleship_my_fleet']                 = 'Your fleet';
$L['battleship_enemy_fleet']              = 'Opponent\'s board';
$L['battleship_defence']                  = 'Defense';
$L['battleship_attack']                   = 'Attack';
$L['battleship_fire_hint']                = 'Click a cell to fire.';
$L['battleship_legend']                   = 'Legend';
$L['battleship_legend_hint']              = 'Cell markings on the board';
$L['battleship_legend_ship']              = 'Your ship';
$L['battleship_legend_hit']               = 'Hit';
$L['battleship_legend_sunk']              = 'Sunk ship';
$L['battleship_legend_miss']              = 'Miss';
$L['battleship_legend_enemy_hit']         = 'Enemy hit';
$L['battleship_open_games']               = 'Open games';
$L['battleship_open_games_hint']          = 'Click "Open games" to see the list.';
$L['battleship_stats']                    = 'My statistics';
$L['battleship_stats_hint']               = 'Click "My statistics" to load data.';
$L['battleship_ships_left']               = 'Remaining:';
$L['battleship_stats_wins']               = 'Wins';
$L['battleship_stats_losses']             = 'Losses';
$L['battleship_stats_rating']             = 'Rating';
$L['battleship_no_waiting']               = 'No open games. Create your own!';
$L['battleship_join']                     = 'Join';
$L['battleship_confirm_surrender']        = 'Are you sure you want to surrender? The game will be lost.';
$L['battleship_ai_thinking']              = 'The computer is thinking...';

// Variative notification phrases
$L['battleship_msg_hit']                  = 'Hit!|Right on target!|Direct hit!|Ship hit!|Great shot!|Damaged!|Contact!';
$L['battleship_msg_miss']                 = 'Miss!|Missed!|Miss!|Water, nothing but water...|Close, but no!|Splash, and nothing.|Missed completely!';
$L['battleship_msg_sunk']                 = 'Sunk!|Ship at the bottom!|Fleet minus one!|Nice, sunk!|Sent to the bottom!|It won\'t surface again!|Ship completely destroyed!';
$L['battleship_msg_damaged']              = 'Damaged!|Ship on fire!|Damage taken!|Not sunk, but hit!';
$L['battleship_msg_win']                  = 'Victory!|Enemy fleet destroyed!|You won this battle!|The sea is ours!';
$L['battleship_msg_lose']                 = 'Defeat...|Your fleet has been sunk.|Better luck next time.|The computer turned out stronger.';
$L['battleship_msg_your_turn']            = 'Your turn!|Shoot!|Fire!|Choose a target!';
$L['battleship_msg_enemy_turn']           = 'Opponent\'s turn...|Waiting for a response...|The opponent is aiming...';
