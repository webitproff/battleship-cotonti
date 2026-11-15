<?php
/**
 * File: plugins/battleship/lang/battleship.ru.lang.php
 * Russian language file for Battleship plugin
 */

defined('COT_CODE') or die('Wrong URL.');

$L['battleship_title']                    = 'Морской бой';
$L['battleship_subtitle']                 = 'Классическая игра против компьютера или другого игрока';
$L['battleship_btn_ai']                   = 'Против компьютера';
$L['battleship_btn_pvp']                  = 'Создать PvP-игру';
$L['battleship_btn_list']                 = 'Открытые игры';
$L['battleship_btn_stats']                = 'Моя статистика';
$L['battleship_btn_rotate']               = 'Повернуть';
$L['battleship_btn_random']               = 'Случайно';
$L['battleship_btn_confirm']              = 'Готово';
$L['battleship_btn_surrender']            = 'Сдаться';
$L['battleship_btn_clear']                = 'Очистить';

$L['battleship_status_choose']            = 'Выберите режим игры';
$L['battleship_status_place_hint']        = 'Расставьте корабли. Кликните по клетке, чтобы поставить текущий корабль. «Повернуть» меняет ориентацию.';
$L['battleship_status_not_all_placed']    = 'Не все корабли расставлены. Осталось: ';
$L['battleship_status_waiting']           = 'Ожидание соперника...';
$L['battleship_status_your_turn']         = 'Ваш ход — стреляйте!';
$L['battleship_status_enemy_turn']        = 'Ход соперника...';
$L['battleship_status_win']               = 'Победа! Флот противника уничтожен.';
$L['battleship_status_lose']              = 'Поражение. Ваш флот потоплен.';
$L['battleship_status_not_your_turn']     = 'Сейчас не ваш ход.';
$L['battleship_status_already_shot']      = 'Вы уже стреляли в эту клетку.';
$L['battleship_status_sunk']              = 'Корабль потоплен!';
$L['battleship_status_placing']           = 'Расстановка кораблей';
$L['battleship_status_active']            = 'Игра идёт';

$L['battleship_err_auth']                 = 'Требуется авторизация.';
$L['battleship_err_limit']                = 'Достигнут лимит активных игр.';
$L['battleship_err_no_game']              = 'Игра не найдена.';
$L['battleship_err_no_moves']             = 'Нет доступных ходов.';
$L['battleship_err_not_ai_turn']          = 'Сейчас не ход компьютера.';
$L['battleship_err_own_game']             = 'Нельзя присоединиться к своей игре.';
$L['battleship_err_access']               = 'Нет доступа к игре.';
$L['battleship_err_coords']               = 'Некорректные координаты.';
$L['battleship_err_board']                = 'Некорректная расстановка кораблей.';
$L['battleship_err_unknown']              = 'Неизвестная ошибка.';
$L['battleship_err_csrf']                 = 'Ошибка безопасности (CSRF). Обновите страницу.';

$L['battleship_placement_title']          = 'Расстановка кораблей';
$L['battleship_place_hint']               = 'Расставьте корабли. Кликните по клетке, чтобы поставить текущий корабль. «Повернуть» меняет ориентацию.';
$L['battleship_my_fleet']                 = 'Ваш флот';
$L['battleship_enemy_fleet']              = 'Поле противника';
$L['battleship_defence']                  = 'Оборона';
$L['battleship_attack']                   = 'Атака';
$L['battleship_fire_hint']                = 'Кликните по клетке, чтобы выстрелить.';
$L['battleship_legend']                   = 'Легенда';
$L['battleship_legend_hint']              = 'Обозначения клеток на поле';
$L['battleship_legend_ship']              = 'Ваш корабль';
$L['battleship_legend_hit']               = 'Попадание';
$L['battleship_legend_sunk']              = 'Потопленный корабль';
$L['battleship_legend_miss']              = 'Промах';
$L['battleship_legend_enemy_hit']         = 'Враг попал';
$L['battleship_open_games']               = 'Открытые игры';
$L['battleship_open_games_hint']          = 'Нажмите «Открытые игры», чтобы увидеть список.';
$L['battleship_stats']                    = 'Моя статистика';
$L['battleship_stats_hint']               = 'Нажмите «Моя статистика», чтобы загрузить данные.';
$L['battleship_ships_left']               = 'Осталось:';
$L['battleship_stats_wins']               = 'Побед';
$L['battleship_stats_losses']             = 'Поражений';
$L['battleship_stats_rating']             = 'Рейтинг';
$L['battleship_no_waiting']               = 'Нет открытых игр. Создайте свою!';
$L['battleship_join']                     = 'Присоединиться';
$L['battleship_confirm_surrender']        = 'Точно сдаться? Игра будет проиграна.';
$L['battleship_ai_thinking']              = 'Компьютер думает...';

// Вариативные фразы для уведомлений
$L['battleship_msg_hit']                  = 'Попал!|Прямо в цель!|Есть пробитие!|Корабль задет!|Отличный выстрел!|Повредил!|Есть контакт!';
$L['battleship_msg_miss']                 = 'Мимо!|В молоко!|Промах!|Вода, сплошная вода...|Рядом, но мимо!|Плеск, и только.|Мимо кассы!';
$L['battleship_msg_sunk']                 = 'Потопил!|Корабль на дне!|Флот минус один!|Красава, потопил!|Отправлен на дно!|Больше не всплывёт!|Полный разгром корабля!';
$L['battleship_msg_damaged']              = 'Повредил!|Корабль горит!|Есть повреждения!|Не потопил, но задел!';
$L['battleship_msg_win']                  = 'Победа!|Флот противника уничтожен!|Вы выиграли эту битву!|Море за нами!';
$L['battleship_msg_lose']                 = 'Поражение...|Ваш флот потоплен.|В следующий раз повезёт.|Компьютер оказался сильнее.';
$L['battleship_msg_your_turn']            = 'Ваш ход!|Стреляйте!|Огонь!|Выбирайте цель!';
$L['battleship_msg_enemy_turn']           = 'Ход соперника...|Ждём ответа...|Противник целится...';

$L['battleship_btn_my']                  = 'Мои игры';
$L['battleship_btn_new_game']             = 'Новая игра';
$L['battleship_btn_continue']             = 'Продолжить';
$L['battleship_btn_abandon']              = 'Отменить';
$L['battleship_my_title']                 = 'Мои игры';
$L['battleship_my_empty']                 = 'У вас пока нет игр.';
$L['battleship_my_hint']                  = 'Отмена игры в статусе «Расстановка» освобождает слот без потери рейтинга. В активной игре отмена считается поражением.';
$L['battleship_active_slots']             = 'Активных слотов';
$L['battleship_col_mode']                 = 'Режим';
$L['battleship_col_status']               = 'Статус';
$L['battleship_col_updated']              = 'Обновлено';
$L['battleship_confirm_abandon']          = 'Отменить эту игру?';
$L['battleship_abandoned']                = 'Игра отменена.';
$L['battleship_status_finished']          = 'Завершена';
$L['battleship_err_limit_hint']           = 'Достигнут лимит активных игр. Откройте «Мои игры» и отмените ненужные партии.';

$L['battleship_admin_title']              = 'Морской бой — админка';
$L['battleship_admin_help']               = 'Управление партиями и статистикой.';
$L['battleship_admin_deleted']            = 'Игра удалена.';
$L['battleship_admin_purged']             = 'Удалено завершённых игр';
$L['battleship_admin_purge']              = 'Очистить завершённые';
$L['battleship_admin_purge_confirm']      = 'Удалить все завершённые игры?';
$L['battleship_admin_winner']             = 'Победитель';
$L['battleship_admin_top']                = 'Топ по рейтингу';
$L['battleship_btn_sound_on']             = 'Звук вкл';
$L['battleship_btn_sound_off']            = 'Звук выкл';
