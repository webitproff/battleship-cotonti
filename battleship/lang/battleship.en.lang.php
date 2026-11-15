<?php
/**
 * File: battleship.en.lang.php
 * Path: plugins/battleship/lang/battleship.en.lang.php
 *
 * Placeholders: {cell} - coordinate (D5), {size} - ship length, {name} - player name
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

// Plugin admin
$L['info_desc'] = 'Battleship: vs computer and PvP between registered users';
$L['cfg_max_active_games'] = ['Max active games per user', ''];
$L['cfg_hit_again'] = ['Extra turn after a hit', 'Classic rules: hit and shoot again'];
$L['cfg_ai_level'] = ['Computer difficulty', 'easy - random, normal - finishes wounded ships, hard - probability based'];
$L['cfg_abandon_hours'] = ['Close inactive games after (hours)', ''];

$L['battleship_title'] = 'Battleship';
$L['battleship_subtitle'] = 'Place your fleet, find the enemy ships and sink them first';
$L['battleship_loading'] = 'Loading…';
$L['battleship_login_required'] = 'Please log in to play.';
$L['battleship_btn_login'] = 'Log in';

// Lobby
$L['battleship_lobby_title'] = 'Lobby';
$L['battleship_card_ai'] = 'Versus computer';
$L['battleship_card_ai_desc'] = 'Practice without waiting. The computer is always ready for battle.';
$L['battleship_btn_ai'] = 'Play vs computer';
$L['battleship_card_pvp'] = 'Versus a human';
$L['battleship_card_pvp_desc'] = 'Create a game and wait for an opponent, or join an open one.';
$L['battleship_btn_pvp'] = 'Create PvP game';
$L['battleship_open_games'] = 'Open games';
$L['battleship_no_open_games'] = 'Nobody is waiting yet. Create a game first!';
$L['battleship_my_games'] = 'My active games';
$L['battleship_no_my_games'] = 'No unfinished games.';
$L['battleship_top_players'] = 'Top captains';
$L['battleship_no_top'] = 'The ranking is empty for now.';
$L['battleship_my_stats'] = 'My statistics';
$L['battleship_stat_pvp'] = 'PvP: wins / losses';
$L['battleship_stat_ai'] = 'Vs computer: wins / losses';
$L['battleship_stat_rating'] = 'Rating';
$L['battleship_stat_accuracy'] = 'Accuracy';
$L['battleship_btn_join'] = 'Join';
$L['battleship_btn_resume'] = 'Resume';
$L['battleship_btn_refresh'] = 'Refresh';
$L['battleship_btn_lobby'] = 'Back to lobby';
$L['battleship_game_n'] = 'Game #{n}';
$L['battleship_vs_ai'] = 'vs computer';
$L['battleship_vs'] = 'vs {name}';
$L['battleship_wait_opp'] = 'waiting for opponent';
$L['battleship_state_placing'] = 'placing ships';
$L['battleship_state_your_turn'] = 'your turn';
$L['battleship_state_enemy_turn'] = 'opponent\'s turn';

// Game
$L['battleship_ai_name'] = 'Captain Computer';
$L['battleship_you'] = 'You';
$L['battleship_your_fleet'] = 'Your fleet';
$L['battleship_enemy_waters'] = 'Enemy waters';
$L['battleship_aim'] = 'Target';
$L['battleship_aim_none'] = 'hover over a cell';
$L['battleship_fleet_left'] = 'Ships left';
$L['battleship_deck'] = '{size}-deck';
$L['battleship_decks_short'] = '{size}d';
$L['battleship_btn_surrender'] = 'Surrender';
$L['battleship_btn_surrender_confirm'] = 'Really surrender?';
$L['battleship_btn_cancel_game'] = 'Cancel game';
$L['battleship_btn_leave'] = 'Leave game';

// Placement
$L['battleship_place_title'] = 'Place your fleet';
$L['battleship_place_hint'] = 'Pick a ship, hover over the board and click. Press R or use the Rotate button to change orientation. Click a placed ship to return it to port. Ships must not touch each other, not even diagonally.';
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
$L['battleship_st_waiting_join'] = 'Waiting for an opponent. You can place your fleet meanwhile.';
$L['battleship_st_waiting_ready'] = 'Your fleet is ready. Waiting for the opponent to finish placing…';
$L['battleship_st_opp_waiting_you'] = 'Your opponent is ready and waiting. Place your fleet!';
$L['battleship_st_opp_joined'] = '{name} joined the game!';
$L['battleship_st_your_turn'] = 'Your turn - pick a cell in enemy waters.';
$L['battleship_st_enemy_turn'] = 'Opponent\'s turn…';
$L['battleship_st_win'] = 'Victory! The enemy fleet is at the bottom.';
$L['battleship_st_lose'] = 'Defeat. Your fleet is sunk.';
$L['battleship_st_win_surrender'] = 'The opponent surrendered. You win!';
$L['battleship_st_lose_surrender'] = 'You surrendered. It happens.';
$L['battleship_st_abandon'] = 'Game closed: the opponent was away for too long.';
$L['battleship_st_left'] = 'The game ended with no result.';
$L['battleship_badge_you'] = 'Your turn';
$L['battleship_badge_enemy'] = 'Enemy turn';
$L['battleship_badge_wait'] = 'Waiting';
$L['battleship_badge_place'] = 'Placing ships';
$L['battleship_badge_over'] = 'Game over';
$L['battleship_extra_turn'] = 'A hit - shoot again!';
$L['battleship_result_win'] = 'You won!';
$L['battleship_result_lose'] = 'You lost';
$L['battleship_result_stats'] = 'Shots: {shots}, hits: {hits}, accuracy: {acc}%';

// Legend
$L['battleship_legend_title'] = 'Legend';
$L['battleship_lg_water'] = 'Water';
$L['battleship_lg_water_d'] = 'A cell nobody has shot at yet.';
$L['battleship_lg_ship'] = 'Your ship';
$L['battleship_lg_ship_d'] = 'An intact deck of your ship. Only you can see it.';
$L['battleship_lg_hit'] = 'Hit';
$L['battleship_lg_hit_d'] = 'The ship is damaged but still afloat.';
$L['battleship_lg_sunk'] = 'Sunk';
$L['battleship_lg_sunk_d'] = 'The whole ship is destroyed.';
$L['battleship_lg_miss'] = 'Miss';
$L['battleship_lg_miss_d'] = 'A shot landed here, but only water.';
$L['battleship_lg_auto'] = 'Auto-marked';
$L['battleship_lg_auto_d'] = 'No ships can be next to a sunk ship.';
$L['battleship_lg_ok'] = 'Can place';
$L['battleship_lg_ok_d'] = 'During placement: the ship fits here.';
$L['battleship_lg_bad'] = 'Cannot place';
$L['battleship_lg_bad_d'] = 'During placement: the edge or a neighbouring ship is in the way.';
$L['battleship_coords_hint'] = 'Columns are letters A-J, rows are numbers 1-10. Cells are named like in chess, e.g. D5.';

// Log
$L['battleship_log_title'] = 'Battle log';
$L['battleship_log_empty'] = 'The battle has not started yet. The first salvo decides a lot.';
$L['battleship_log_you'] = 'You';
$L['battleship_log_opp'] = 'Opponent';

// Errors (server)
$L['battleship_err_auth'] = 'Authorization required.';
$L['battleship_err_limit'] = 'Active games limit reached.';
$L['battleship_err_no_game'] = 'Game not found or no longer available.';
$L['battleship_err_own_game'] = 'You cannot join your own game.';
$L['battleship_err_access'] = 'No access to this game.';
$L['battleship_err_coords'] = 'Invalid coordinates.';
$L['battleship_err_board'] = 'Invalid ship placement.';
$L['battleship_err_already_placed'] = 'Fleet is already placed.';
$L['battleship_err_not_your_turn'] = 'It is not your turn.';
$L['battleship_err_already_shot'] = 'You already shot there.';
$L['battleship_err_unknown'] = 'Unknown error.';
$L['battleship_err_network'] = 'Could not reach the server. Check your connection.';

// Phrases
$L['battleship_phr_my_miss'] = [
    '{cell}: miss! The fish are shocked but alive.',
    '{cell}: nothing. You just scared a seagull.',
    '{cell}: empty. The ocean politely stayed silent.',
    '{cell}: water. Very wet water.',
    '{cell}: the shell went to look for Atlantis.',
    '{cell}: miss. Nice splash though!',
];
$L['battleship_phr_my_hit'] = [
    '{cell}: hit! There is definitely something there.',
    '{cell}: a hole! The enemy swallowed nervously.',
    '{cell}: bullseye! Well, deck-eye.',
    '{cell}: direct hit! Someone dropped the helm.',
    '{cell}: target found, keep firing!',
];
$L['battleship_phr_my_damaged'] = [
    '{cell}: ship damaged! Still breathing, finish it.',
    '{cell}: another hole! The enemy is looking for a bucket.',
    '{cell}: damaged! The deck creaks, the crew panics.',
    '{cell}: direct hit, the ship is leaking!',
];
$L['battleship_phr_my_sunk'] = [
    '{cell}: {size}-deck ship sunk! Say hi to Poseidon.',
    '{cell}: the ship went under. It gurgled a goodbye.',
    '{cell}: sunk! One ship fewer in the ocean.',
    '{cell}: the enemy {size}-decker is now an underwater museum.',
];
$L['battleship_phr_opp_miss'] = [
    '{name} fires at {cell}: miss. Your fleet exhales.',
    '{name} at {cell}: miss. The seagulls applaud.',
    '{name} aimed at {cell} and hit the ocean.',
    '{cell}: the opponent missed. Probably shooting blindfolded.',
];
$L['battleship_phr_opp_hit'] = [
    '{name} hit {cell}! Your ship took one on the deck.',
    '{cell}: you were hit. The captain spills his coffee.',
    '{name} at {cell}: a breach! Everybody hold on.',
];
$L['battleship_phr_opp_damaged'] = [
    '{cell}: your ship is damaged! The crew is looking for patches.',
    '{name} hit {cell} again. The ship creaks but holds.',
    '{cell}: another breach, water is rising!',
];
$L['battleship_phr_opp_sunk'] = [
    '{cell}: your {size}-deck ship is sunk. A minute of silence.',
    '{name} sank your ship at {cell}. It was a good one.',
    '{cell}: the ship went down. Lifeboats are on the way.',
];
$L['battleship_phr_your_turn'] = [
    'Your turn, captain!',
    'Guns are loaded. Where do we fire?',
    'Time to act. Pick a target!',
];
$L['battleship_phr_enemy_turn'] = [
    'The opponent is thinking…',
    'The enemy is picking a target. Fingers crossed.',
    'Opponent\'s turn. Time for a cup of tea.',
];
$L['battleship_phr_win'] = [
    'Admiral, victory! The enemy fleet is feeding the fish.',
    'A clean win! Someone bring the captain a medal.',
];
$L['battleship_phr_lose'] = [
    'Your fleet is down, but your honour floats. Rematch?',
    'Defeat. At least you now know where the seabed is.',
];

// ===== 2.1.0 =====
$L['cfg_chat_enabled'] = ['Chat between PvP players', 'Messages are kept only while the battle is on'];
$L['cfg_chat_cooldown'] = ['Chat message interval (seconds)', ''];

// Navigation / sound
$L['battleship_nav_home'] = 'Lobby';
$L['battleship_nav_my'] = 'My games';
$L['battleship_all_my_games'] = 'All my games';
$L['battleship_sound_on'] = 'Sound on';
$L['battleship_sound_off'] = 'Sound off';

// Chat
$L['battleship_chat_title'] = 'Chat with opponent';
$L['battleship_chat_note'] = 'up to 255 characters, erased after the battle';
$L['battleship_chat_empty'] = 'Quiet so far. Say hi to your opponent!';
$L['battleship_chat_placeholder'] = 'Message…';
$L['battleship_chat_send'] = 'Send';
$L['battleship_err_chat_off'] = 'Chat is disabled.';
$L['battleship_err_chat_empty'] = 'Empty message.';
$L['battleship_err_chat_long'] = 'Message is longer than 255 characters.';
$L['battleship_err_chat_closed'] = 'Chat is available only during a battle between players.';
$L['battleship_err_chat_wait'] = 'Too fast. Please wait a moment.';

// My games page
$L['battleship_my_subtitle'] = 'Resume started games, cancel extra ones and browse history';
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
$L['battleship_my_mode_ai'] = 'Vs computer';
$L['battleship_my_mode_pvp'] = 'PvP';
$L['battleship_my_cancelled'] = 'The game is over.';
$L['battleship_my_res_none'] = 'No result';
$L['battleship_my_reason_sunk'] = 'Fleet sunk';
$L['battleship_my_reason_surrender'] = 'Surrender';
$L['battleship_my_reason_abandon'] = 'Game closed';

// Admin
$L['battleship_admin_title'] = 'Battleship: management';
$L['battleship_admin_help'] = 'Games list, delete single games, purge finished ones and top players. The chat lives in the DB only during a battle and is removed automatically.';
$L['battleship_admin_f_all'] = 'All';
$L['battleship_admin_st_placing'] = 'Placing';
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
