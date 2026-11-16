<?php
/**
 *  File: battleship.en.lang.php
 *  Path: plugins/battleship/lang/battleship.en.lang.php
 *
 *  Placeholders: {cell} - coordinate (D5), {size} - number of decks, {name} - player name
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

// ========================
// PLUGIN INFO (ADMIN)
// ========================
$L['info_name']  = 'Battleship';
$L['info_desc']  = 'Battleship game: against the computer and PvP between authorized users, with chat, humorous notifications and sounds';
$L['info_notes'] = 'Latest version <a target="_blank" href="https://github.com/webitproff/battleship-cotonti"><strong>via the GitHub link</strong></a>';

// ========================
// TITLES AND DESCRIPTIONS (same values, pulled by other keys)
// ========================
$L['battleship_title'] = $L['info_name'];
$L['battleship_desc']  = $L['info_desc'];
$L['battleship_name']  = $L['info_name'];

// Plugin admin

$L['cfg_max_active_games']      = 'Maximum active games per user';
$L['cfg_max_active_games_hint'] = 'Made to avoid collecting garbage in the DB.';

$L['cfg_hit_again']      = 'Extra turn after a hit';
$L['cfg_hit_again_hint'] = 'Classic rules: hit — shoot again';

$L['cfg_ai_level']      = 'Difficulty against the computer';
$L['cfg_ai_level_hint'] = ''
    . '<strong>easy</strong> — random, <br>'
    . '<strong>normal</strong> — finishes off damaged ships, <br>' 
    . '<strong>hard</strong> — calculates probabilities';

$L['cfg_abandon_hours'] = 'Close inactive games after (hours)';

$L['cfg_chat_enabled']       = 'Chat between players in PvP';
$L['cfg_chat_enabled_hint']  = 'Messages are stored only while the battle is in progress';

$L['cfg_chat_cooldown'] = 'Interval between chat messages (sec)';

// Admin interface

$L['battleship_admin_title'] = 'Battleship: management';
$L['battleship_admin_help'] = 'List of games, deleting individual matches, purging finished ones and top players. Chat is stored in the DB only during the battle and is deleted automatically.';
$L['battleship_admin_f_all'] = 'All';
$L['battleship_admin_st_placing'] = 'Placement';
$L['battleship_admin_st_active'] = 'Active';
$L['battleship_admin_st_finished'] = 'Finished';
$L['battleship_admin_deleted'] = 'Game deleted';
$L['battleship_admin_purged'] = 'Finished games deleted';
$L['battleship_admin_purge'] = 'Purge finished';
$L['battleship_admin_purge_confirm'] = 'Delete all finished games?';
$L['battleship_admin_delete'] = 'Delete';
$L['battleship_admin_delete_confirm'] = 'Delete this game?';
$L['battleship_admin_no_games'] = 'No games.';
$L['battleship_admin_top'] = 'Top players by rating';
$L['battleship_admin_col_p1'] = 'Player 1';
$L['battleship_admin_col_p2'] = 'Player 2';
$L['battleship_admin_col_winner'] = 'Winner';
$L['battleship_admin_col_created'] = 'Created';
$L['battleship_admin_col_player'] = 'Player';

// Frontend
$L['battleship_subtitle'] = 'Place your fleet, find the enemy ships and sink them first';
$L['battleship_loading'] = 'Loading…';
$L['battleship_login_required'] = 'Log in to the site to play.';
$L['battleship_btn_login'] = 'Log in';

// Lobby
$L['battleship_lobby_title'] = 'Lobby';
$L['battleship_card_ai'] = 'Against the computer';
$L['battleship_card_ai_desc'] = 'Practice without waiting. The computer is always ready for battle.';
$L['battleship_btn_ai'] = 'Play against the computer';
$L['battleship_card_pvp'] = 'Play against a human';
$L['battleship_card_pvp_desc'] = 'Create a game and wait for an opponent or join an open one.';
$L['battleship_btn_pvp'] = 'Create PvP game';
$L['battleship_open_games'] = 'Open games';
$L['battleship_no_open_games'] = 'No one is waiting for an opponent yet. Create a game first!';
$L['battleship_my_games'] = 'My active games';
$L['battleship_no_my_games'] = 'No unfinished games.';
$L['battleship_top_players'] = 'Best captains';
$L['battleship_no_top'] = 'Rating is empty yet.';
$L['battleship_my_stats'] = 'My statistics';
$L['battleship_stat_pvp'] = 'PvP: wins / losses';
$L['battleship_stat_ai'] = 'Against the computer: wins / losses';
$L['battleship_stat_rating'] = 'Rating';
$L['battleship_stat_accuracy'] = 'Accuracy';
$L['battleship_btn_join'] = 'Join';
$L['battleship_btn_resume'] = 'Continue';
$L['battleship_btn_refresh'] = 'Refresh';
$L['battleship_btn_lobby'] = 'To lobby';
$L['battleship_game_n'] = 'Game #{n}';
$L['battleship_vs_ai'] = 'against the computer';
$L['battleship_vs'] = 'against {name}';
$L['battleship_wait_opp'] = 'waiting for opponent';
$L['battleship_state_placing'] = 'placement';
$L['battleship_state_your_turn'] = 'your turn';
$L['battleship_state_enemy_turn'] = 'opponent\'s turn';

// Game
$L['battleship_ai_name'] = 'Captain Computer';
$L['battleship_you'] = 'You';
$L['battleship_your_fleet'] = 'Your fleet';
$L['battleship_enemy_waters'] = 'Enemy waters';
$L['battleship_aim'] = 'Target';
$L['battleship_aim_none'] = 'hover over a cell';
$L['battleship_fleet_left'] = 'Ships remaining';
$L['battleship_deck'] = '{size}-deck';
$L['battleship_decks_short'] = '{size}d';
$L['battleship_btn_surrender'] = 'Surrender';
$L['battleship_btn_surrender_confirm'] = 'Are you sure you want to surrender?';
$L['battleship_btn_cancel_game'] = 'Cancel game';
$L['battleship_btn_leave'] = 'Leave game';

// Placement
$L['battleship_place_title'] = 'Fleet placement';
$L['battleship_place_hint'] = 'Select a ship, hover over the board and click. Key R or the "Rotate" button changes orientation. Clicking on an already placed ship returns it to port. Ships must not touch each other, even at corners.';
$L['battleship_btn_rotate'] = 'Rotate (R)';
$L['battleship_btn_random'] = 'Random';
$L['battleship_btn_clear'] = 'Clear';
$L['battleship_btn_confirm'] = 'Ready for battle';
$L['battleship_orient_h'] = 'horizontal';
$L['battleship_orient_v'] = 'vertical';
$L['battleship_port'] = 'Port';
$L['battleship_placed'] = 'Placed: {n} of {total}';
$L['battleship_not_all_placed'] = 'Not all ships are placed.';

// Status banner
$L['battleship_st_choose'] = 'Choose a game mode.';
$L['battleship_st_waiting_join'] = 'Waiting for an opponent. You can place your fleet in the meantime.';
$L['battleship_st_waiting_ready'] = 'Fleet ready. Waiting for the opponent to finish placement…';
$L['battleship_st_opp_waiting_you'] = 'The opponent is ready and waiting for you. Place your fleet!';
$L['battleship_st_opp_joined'] = 'Opponent {name} has joined the game!';
$L['battleship_st_your_turn'] = 'Your turn — choose a cell in the enemy waters.';
$L['battleship_st_enemy_turn'] = 'Opponent\'s turn…';
$L['battleship_st_win'] = 'Victory! The enemy fleet is at the bottom.';
$L['battleship_st_lose'] = 'Defeat. Your fleet has been sunk.';
$L['battleship_st_win_surrender'] = 'The opponent surrendered. Victory is yours!';
$L['battleship_st_lose_surrender'] = 'You surrendered. It happens.';
$L['battleship_st_abandon'] = 'Game closed: the opponent did not respond for too long.';
$L['battleship_st_left'] = 'Game ended without result.';
$L['battleship_badge_you'] = 'Your turn';
$L['battleship_badge_enemy'] = 'Opponent\'s turn';
$L['battleship_badge_wait'] = 'Waiting';
$L['battleship_badge_place'] = 'Placement';
$L['battleship_badge_over'] = 'Game over';
$L['battleship_extra_turn'] = 'Hit — shoot again!';
$L['battleship_result_win'] = 'You won!';
$L['battleship_result_lose'] = 'You lost';
$L['battleship_result_stats'] = 'Shots: {shots}, hits: {hits}, accuracy: {acc}%';

// Legend
$L['battleship_legend_title'] = 'Legend';
$L['battleship_lg_water'] = 'Water';
$L['battleship_lg_water_d'] = 'A cell that has not been shot at yet.';
$L['battleship_lg_ship'] = 'Your ship';
$L['battleship_lg_ship_d'] = 'An intact deck of your ship. Only you can see it.';
$L['battleship_lg_hit'] = 'Hit';
$L['battleship_lg_hit_d'] = 'The ship is damaged but still afloat.';
$L['battleship_lg_sunk'] = 'Sunk';
$L['battleship_lg_sunk_d'] = 'The ship is completely destroyed.';
$L['battleship_lg_miss'] = 'Miss';
$L['battleship_lg_miss_d'] = 'There was a shot here, but only water.';
$L['battleship_lg_auto'] = 'Marked automatically';
$L['battleship_lg_auto_d'] = 'There can be no ships around a sunk ship.';
$L['battleship_lg_ok'] = 'Can place';
$L['battleship_lg_ok_d'] = 'During placement: the ship will be placed here.';
$L['battleship_lg_bad'] = 'Cannot place';
$L['battleship_lg_bad_d'] = 'During placement: blocked by the board edge or a neighboring ship.';
$L['battleship_coords_hint'] = 'Columns are letters A–J, rows are numbers 1–10. A cell is named like in chess: e.g., D5.';

// Log
$L['battleship_log_title'] = 'Battle log';
$L['battleship_log_empty'] = 'The battle has not started yet. The first salvo will decide a lot.';
$L['battleship_log_you'] = 'You';
$L['battleship_log_opp'] = 'Opponent';

// Errors (server)
$L['battleship_err_auth'] = 'Authorization required.';
$L['battleship_err_limit'] = 'The limit of active games has been reached.';
$L['battleship_err_no_game'] = 'Game not found or no longer available.';
$L['battleship_err_own_game'] = 'You cannot join your own game.';
$L['battleship_err_access'] = 'No access to the game.';
$L['battleship_err_coords'] = 'Invalid coordinates.';
$L['battleship_err_board'] = 'Invalid ship placement.';
$L['battleship_err_already_placed'] = 'The fleet has already been placed.';
$L['battleship_err_not_your_turn'] = 'It is not your turn.';
$L['battleship_err_already_shot'] = 'This cell has already been shot at.';
$L['battleship_err_unknown'] = 'Unknown error.';
$L['battleship_err_network'] = 'Could not connect to the server. Check your connection.';

// Phrases (random choice; {cell}, {size}, {name} are replaced)
$L['battleship_phr_my_miss'] = [
    '{cell}: miss! The fish are shocked but alive.',
    '{cell}: miss. You just scared a seagull.',
    '{cell}: empty. The ocean politely kept silent.',
    '{cell}: water. Very wet water.',
    '{cell}: no luck, the shell went to the bottom to look for Atlantis.',
    '{cell}: miss. But what a beautiful splash!',
];
$L['battleship_phr_my_hit'] = [
    '{cell}: hit! There is definitely something there.',
    '{cell}: direct hit! The enemy swallowed nervously.',
    '{cell}: bullseye! Well, on the deck.',
    '{cell}: hit! You can hear someone dropping the helm.',
    '{cell}: fire for effect, target found!',
];
$L['battleship_phr_my_damaged'] = [
    '{cell}: ship damaged! It is still breathing, finish it off.',
    '{cell}: another hole! The enemy is already looking for a bucket.',
    '{cell}: damaged! The deck is cracking, the crew is panicking.',
    '{cell}: direct hit, the ship is taking on water!',
];
$L['battleship_phr_my_sunk'] = [
    '{cell}: {size}-deck ship sunk! Say hi to Poseidon.',
    '{cell}: the ship went underwater. It gurgled farewell.',
    '{cell}: sunk! One less ship in the ocean.',
    '{cell}: enemy {size}-deck ship sent to the underwater museum.',
];
$L['battleship_phr_opp_miss'] = [
    '{name} shoots at {cell}: miss. Your fleet exhaled.',
    '{name} at {cell}: miss. The seagulls applaud.',
    '{name} aimed at {cell}, hit the ocean.',
    '{cell}: the opponent missed. Seems they were shooting blind.',
];
$L['battleship_phr_opp_hit'] = [
    '{name} hit {cell}! Your ship took one on the deck.',
    '{cell}: you were hit. The captain drops his coffee.',
    '{name} at {cell}: hole! Everyone hold on tight.',
];
$L['battleship_phr_opp_damaged'] = [
    '{cell}: your ship is damaged! The crew is looking for patches.',
    '{name} hit {cell} again. The ship creaks but holds.',
    '{cell}: another hole, water is rising!',
];
$L['battleship_phr_opp_sunk'] = [
    '{cell}: your {size}-deck ship has been sunk. A moment of silence.',
    '{name} sank your ship at {cell}. It was a good one.',
    '{cell}: the ship went to the bottom. Lifeboats are already on the way.',
];
$L['battleship_phr_your_turn'] = [
    'Your turn, captain!',
    'Guns are loaded. Where do we shoot?',
    'Time to act. Choose a target!',
];
$L['battleship_phr_enemy_turn'] = [
    'The opponent is thinking…',
    'The enemy is choosing a target. Cross your fingers.',
    'Opponent\'s turn. You can have some tea meanwhile.',
];
$L['battleship_phr_win'] = [
    'Admiral, this is victory! The enemy fleet is feeding the fish.',
    'Clean victory! Someone bring the captain a medal.',
];
$L['battleship_phr_lose'] = [
    'Your fleet is at the bottom, but your honor is afloat. Rematch?',
    'Defeat. But now you know where the bottom is.',
];

// Navigation / sound
$L['battleship_nav_home'] = 'Lobby';
$L['battleship_nav_my'] = 'My games';
$L['battleship_all_my_games'] = 'All my games';
$L['battleship_sound_on'] = 'Sound on';
$L['battleship_sound_off'] = 'Sound off';

// Chat
$L['battleship_chat_title'] = 'Chat with opponent';
$L['battleship_chat_note'] = 'up to 255 characters, deleted after the battle';
$L['battleship_chat_empty'] = 'Quiet so far. Greet your opponent!';
$L['battleship_chat_placeholder'] = 'Message…';
$L['battleship_chat_send'] = 'Send';
$L['battleship_err_chat_off'] = 'Chat is disabled.';
$L['battleship_err_chat_empty'] = 'Empty message.';
$L['battleship_err_chat_long'] = 'Message is longer than 255 characters.';
$L['battleship_err_chat_closed'] = 'Chat is only available during a battle between players.';
$L['battleship_err_chat_wait'] = 'Too frequent. Wait a bit.';

// My games page
$L['battleship_my_subtitle'] = 'Continue started matches, cancel unnecessary ones and view history';
$L['battleship_my_active'] = 'Unfinished games';
$L['battleship_my_history'] = 'Recently finished games';
$L['battleship_my_no_history'] = 'No finished games yet.';
$L['battleship_my_col_mode'] = 'Mode';
$L['battleship_my_col_opp'] = 'Opponent';
$L['battleship_my_col_status'] = 'Status';
$L['battleship_my_col_updated'] = 'Updated';
$L['battleship_my_col_actions'] = 'Actions';
$L['battleship_my_col_result'] = 'Result';
$L['battleship_my_col_reason'] = 'Reason';
$L['battleship_my_mode_ai'] = 'Against the computer';
$L['battleship_my_mode_pvp'] = 'PvP';
$L['battleship_my_cancelled'] = 'Game finished.';
$L['battleship_my_res_none'] = 'No result';
$L['battleship_my_reason_sunk'] = 'Fleet sunk';
$L['battleship_my_reason_surrender'] = 'Surrender';
$L['battleship_my_reason_abandon'] = 'Game closed';