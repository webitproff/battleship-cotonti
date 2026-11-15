<?php
/**
 * File: battleship.ru.lang.php
 * Path: plugins/battleship/lang/battleship.ru.lang.php
 *
 * Placeholders: {cell} - координата (D5), {size} - число палуб, {name} - имя игрока
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
$L['info_desc'] = 'Морской бой: против компьютера и PvP между авторизованными пользователями';
$L['cfg_max_active_games'] = ['Максимум активных игр у пользователя', ''];
$L['cfg_hit_again'] = ['Дополнительный ход после попадания', 'Классические правила: попал — стреляешь снова'];
$L['cfg_ai_level'] = ['Сложность компьютера', 'easy — случайно, normal — добивает раненых, hard — считает вероятности'];
$L['cfg_abandon_hours'] = ['Закрывать неактивные игры через (часов)', ''];

$L['battleship_title'] = 'Морской бой';
$L['battleship_subtitle'] = 'Расставьте флот, найдите корабли соперника и потопите их первыми';
$L['battleship_loading'] = 'Загрузка…';
$L['battleship_login_required'] = 'Чтобы играть, войдите на сайт.';
$L['battleship_btn_login'] = 'Войти';

// Lobby
$L['battleship_lobby_title'] = 'Лобби';
$L['battleship_card_ai'] = 'Против компьютера';
$L['battleship_card_ai_desc'] = 'Тренировка без ожидания. Компьютер всегда готов к бою.';
$L['battleship_btn_ai'] = 'Играть с компьютером';
$L['battleship_card_pvp'] = 'Игра с человеком';
$L['battleship_card_pvp_desc'] = 'Создайте игру и дождитесь соперника или присоединитесь к открытой.';
$L['battleship_btn_pvp'] = 'Создать PvP-игру';
$L['battleship_open_games'] = 'Открытые игры';
$L['battleship_no_open_games'] = 'Пока никто не ждёт соперника. Создайте игру первым!';
$L['battleship_my_games'] = 'Мои активные игры';
$L['battleship_no_my_games'] = 'Нет незавершённых игр.';
$L['battleship_top_players'] = 'Лучшие капитаны';
$L['battleship_no_top'] = 'Рейтинг пока пуст.';
$L['battleship_my_stats'] = 'Моя статистика';
$L['battleship_stat_pvp'] = 'PvP: победы / поражения';
$L['battleship_stat_ai'] = 'С компьютером: победы / поражения';
$L['battleship_stat_rating'] = 'Рейтинг';
$L['battleship_stat_accuracy'] = 'Меткость';
$L['battleship_btn_join'] = 'Присоединиться';
$L['battleship_btn_resume'] = 'Продолжить';
$L['battleship_btn_refresh'] = 'Обновить';
$L['battleship_btn_lobby'] = 'В лобби';
$L['battleship_game_n'] = 'Игра №{n}';
$L['battleship_vs_ai'] = 'против компьютера';
$L['battleship_vs'] = 'против {name}';
$L['battleship_wait_opp'] = 'ждёт соперника';
$L['battleship_state_placing'] = 'расстановка';
$L['battleship_state_your_turn'] = 'ваш ход';
$L['battleship_state_enemy_turn'] = 'ход соперника';

// Game
$L['battleship_ai_name'] = 'Капитан Компьютер';
$L['battleship_you'] = 'Вы';
$L['battleship_your_fleet'] = 'Ваш флот';
$L['battleship_enemy_waters'] = 'Воды соперника';
$L['battleship_aim'] = 'Цель';
$L['battleship_aim_none'] = 'наведите на клетку';
$L['battleship_fleet_left'] = 'Осталось кораблей';
$L['battleship_deck'] = '{size}-палубный';
$L['battleship_decks_short'] = '{size}п';
$L['battleship_btn_surrender'] = 'Сдаться';
$L['battleship_btn_surrender_confirm'] = 'Точно сдаться?';
$L['battleship_btn_cancel_game'] = 'Отменить игру';
$L['battleship_btn_leave'] = 'Выйти из игры';

// Placement
$L['battleship_place_title'] = 'Расстановка флота';
$L['battleship_place_hint'] = 'Выберите корабль, наведите на поле и кликните. Клавиша R или кнопка «Повернуть» меняет ориентацию. Клик по уже поставленному кораблю возвращает его в порт. Корабли не должны касаться друг друга, даже углами.';
$L['battleship_btn_rotate'] = 'Повернуть (R)';
$L['battleship_btn_random'] = 'Случайно';
$L['battleship_btn_clear'] = 'Очистить';
$L['battleship_btn_confirm'] = 'Готово к бою';
$L['battleship_orient_h'] = 'горизонтально';
$L['battleship_orient_v'] = 'вертикально';
$L['battleship_port'] = 'Порт';
$L['battleship_placed'] = 'Расставлено: {n} из {total}';
$L['battleship_not_all_placed'] = 'Расставлены не все корабли.';

// Status banner
$L['battleship_st_choose'] = 'Выберите режим игры.';
$L['battleship_st_waiting_join'] = 'Ожидаем соперника. Пока можно расставить флот.';
$L['battleship_st_waiting_ready'] = 'Флот готов. Ждём, пока соперник закончит расстановку…';
$L['battleship_st_opp_waiting_you'] = 'Соперник уже готов и ждёт вас. Расставляйте флот!';
$L['battleship_st_opp_joined'] = 'Соперник {name} присоединился к игре!';
$L['battleship_st_your_turn'] = 'Ваш ход — выберите клетку в водах соперника.';
$L['battleship_st_enemy_turn'] = 'Ход соперника…';
$L['battleship_st_win'] = 'Победа! Флот соперника на дне.';
$L['battleship_st_lose'] = 'Поражение. Ваш флот потоплен.';
$L['battleship_st_win_surrender'] = 'Соперник сдался. Победа за вами!';
$L['battleship_st_lose_surrender'] = 'Вы сдались. Бывает.';
$L['battleship_st_abandon'] = 'Игра закрыта: соперник не отвечал слишком долго.';
$L['battleship_st_left'] = 'Игра завершена без результата.';
$L['battleship_badge_you'] = 'Ваш ход';
$L['battleship_badge_enemy'] = 'Ход соперника';
$L['battleship_badge_wait'] = 'Ожидание';
$L['battleship_badge_place'] = 'Расстановка';
$L['battleship_badge_over'] = 'Игра окончена';
$L['battleship_extra_turn'] = 'Попали — стреляйте ещё раз!';
$L['battleship_result_win'] = 'Вы победили!';
$L['battleship_result_lose'] = 'Вы проиграли';
$L['battleship_result_stats'] = 'Выстрелов: {shots}, попаданий: {hits}, меткость: {acc}%';

// Legend
$L['battleship_legend_title'] = 'Обозначения';
$L['battleship_lg_water'] = 'Вода';
$L['battleship_lg_water_d'] = 'Клетка, в которую ещё не стреляли.';
$L['battleship_lg_ship'] = 'Ваш корабль';
$L['battleship_lg_ship_d'] = 'Целая палуба вашего корабля. Видите только вы.';
$L['battleship_lg_hit'] = 'Попадание';
$L['battleship_lg_hit_d'] = 'Корабль подбит, но ещё держится на плаву.';
$L['battleship_lg_sunk'] = 'Потоплен';
$L['battleship_lg_sunk_d'] = 'Корабль уничтожен целиком.';
$L['battleship_lg_miss'] = 'Мимо';
$L['battleship_lg_miss_d'] = 'Здесь был выстрел, но только вода.';
$L['battleship_lg_auto'] = 'Отмечено автоматически';
$L['battleship_lg_auto_d'] = 'Вокруг потопленного корабля кораблей быть не может.';
$L['battleship_lg_ok'] = 'Можно поставить';
$L['battleship_lg_ok_d'] = 'При расстановке: корабль встанет сюда.';
$L['battleship_lg_bad'] = 'Нельзя поставить';
$L['battleship_lg_bad_d'] = 'При расстановке: мешает край поля или соседний корабль.';
$L['battleship_coords_hint'] = 'Столбцы — буквы A–J, строки — цифры 1–10. Клетка называется как в шахматах: например, D5.';

// Log
$L['battleship_log_title'] = 'Журнал боя';
$L['battleship_log_empty'] = 'Бой ещё не начался. Первый залп решит многое.';
$L['battleship_log_you'] = 'Вы';
$L['battleship_log_opp'] = 'Соперник';

// Errors (server)
$L['battleship_err_auth'] = 'Требуется авторизация.';
$L['battleship_err_limit'] = 'Достигнут лимит активных игр.';
$L['battleship_err_no_game'] = 'Игра не найдена или уже недоступна.';
$L['battleship_err_own_game'] = 'Нельзя присоединиться к своей игре.';
$L['battleship_err_access'] = 'Нет доступа к игре.';
$L['battleship_err_coords'] = 'Некорректные координаты.';
$L['battleship_err_board'] = 'Некорректная расстановка кораблей.';
$L['battleship_err_already_placed'] = 'Флот уже расставлен.';
$L['battleship_err_not_your_turn'] = 'Сейчас не ваш ход.';
$L['battleship_err_already_shot'] = 'Сюда уже стреляли.';
$L['battleship_err_unknown'] = 'Неизвестная ошибка.';
$L['battleship_err_network'] = 'Не удалось связаться с сервером. Проверьте соединение.';

// Phrases (random choice; {cell}, {size}, {name} are replaced)
$L['battleship_phr_my_miss'] = [
    '{cell}: мимо! Рыбы в шоке, но живы.',
    '{cell}: промах. Вы только что напугали чайку.',
    '{cell}: пусто. Океан вежливо промолчал.',
    '{cell}: вода. Очень мокрая вода.',
    '{cell}: не повезло, снаряд ушёл на дно искать Атлантиду.',
    '{cell}: мимо. Зато красивый всплеск!',
];
$L['battleship_phr_my_hit'] = [
    '{cell}: попал! Что-то там точно есть.',
    '{cell}: есть пробитие! Враг нервно сглотнул.',
    '{cell}: в яблочко! Ну, в палубу.',
    '{cell}: попадание! Слышно, как кто-то уронил штурвал.',
    '{cell}: огонь на поражение, цель найдена!',
];
$L['battleship_phr_my_damaged'] = [
    '{cell}: корабль повреждён! Он ещё дышит, добейте.',
    '{cell}: ещё одна пробоина! Враг уже ищет ведро.',
    '{cell}: повредили! Палуба трещит, экипаж паникует.',
    '{cell}: прямое попадание, корабль дал течь!',
];
$L['battleship_phr_my_sunk'] = [
    '{cell}: потоплен {size}-палубник! Привет, Посейдону.',
    '{cell}: корабль ушёл под воду. Булькнул напоследок.',
    '{cell}: потопили! Одним кораблём в океане меньше.',
    '{cell}: вражеский {size}-палубник отправлен в подводный музей.',
];
$L['battleship_phr_opp_miss'] = [
    '{name} стреляет в {cell}: мимо. Ваш флот выдохнул.',
    '{name} в {cell}: промах. Чайки аплодируют.',
    '{name} целился в {cell}, попал в океан.',
    '{cell}: соперник промазал. Кажется, он стрелял вслепую.',
];
$L['battleship_phr_opp_hit'] = [
    '{name} попал в {cell}! Ваш корабль получил по палубе.',
    '{cell}: по вам попали. Капитан роняет кофе.',
    '{name} в {cell}: пробоина! Срочно всем держаться.',
];
$L['battleship_phr_opp_damaged'] = [
    '{cell}: ваш корабль повреждён! Экипаж ищет заплатки.',
    '{name} снова попал в {cell}. Корабль скрипит, но держится.',
    '{cell}: ещё одна пробоина, вода прибывает!',
];
$L['battleship_phr_opp_sunk'] = [
    '{cell}: ваш {size}-палубник потоплен. Держим минуту молчания.',
    '{name} потопил ваш корабль в {cell}. Он был хорошим.',
    '{cell}: корабль ушёл ко дну. Спасательные шлюпки уже в пути.',
];
$L['battleship_phr_your_turn'] = [
    'Ваш ход, капитан!',
    'Орудия заряжены. Куда стреляем?',
    'Время действовать. Выбирайте цель!',
];
$L['battleship_phr_enemy_turn'] = [
    'Соперник думает…',
    'Враг выбирает цель. Скрестите пальцы.',
    'Ход соперника. Можно пока выпить чаю.',
];
$L['battleship_phr_win'] = [
    'Адмирал, это победа! Флот соперника кормит рыб.',
    'Чистая победа! Кто-нибудь, принесите капитану медаль.',
];
$L['battleship_phr_lose'] = [
    'Ваш флот на дне, но честь на плаву. Реванш?',
    'Поражение. Зато теперь вы знаете, где находится дно.',
];

// ===== 2.1.0 =====
$L['cfg_chat_enabled'] = ['Чат между игроками в PvP', 'Сообщения хранятся только пока идёт бой'];
$L['cfg_chat_cooldown'] = ['Интервал между сообщениями чата (сек)', ''];

// Navigation / sound
$L['battleship_nav_home'] = 'Лобби';
$L['battleship_nav_my'] = 'Мои игры';
$L['battleship_all_my_games'] = 'Все мои игры';
$L['battleship_sound_on'] = 'Звук вкл';
$L['battleship_sound_off'] = 'Звук выкл';

// Chat
$L['battleship_chat_title'] = 'Чат с соперником';
$L['battleship_chat_note'] = 'до 255 символов, удаляется после боя';
$L['battleship_chat_empty'] = 'Пока тихо. Поприветствуйте соперника!';
$L['battleship_chat_placeholder'] = 'Сообщение…';
$L['battleship_chat_send'] = 'Отправить';
$L['battleship_err_chat_off'] = 'Чат отключён.';
$L['battleship_err_chat_empty'] = 'Пустое сообщение.';
$L['battleship_err_chat_long'] = 'Сообщение длиннее 255 символов.';
$L['battleship_err_chat_closed'] = 'Чат доступен только во время боя между игроками.';
$L['battleship_err_chat_wait'] = 'Слишком часто. Подождите немного.';

// My games page
$L['battleship_my_subtitle'] = 'Продолжайте начатые партии, отменяйте лишние и смотрите историю';
$L['battleship_my_active'] = 'Незавершённые игры';
$L['battleship_my_history'] = 'Последние завершённые игры';
$L['battleship_my_no_history'] = 'Завершённых игр пока нет.';
$L['battleship_my_col_mode'] = 'Режим';
$L['battleship_my_col_opp'] = 'Соперник';
$L['battleship_my_col_status'] = 'Статус';
$L['battleship_my_col_updated'] = 'Обновлена';
$L['battleship_my_col_actions'] = 'Действия';
$L['battleship_my_col_result'] = 'Итог';
$L['battleship_my_col_reason'] = 'Причина';
$L['battleship_my_mode_ai'] = 'Против компьютера';
$L['battleship_my_mode_pvp'] = 'PvP';
$L['battleship_my_cancelled'] = 'Игра завершена.';
$L['battleship_my_res_none'] = 'Без результата';
$L['battleship_my_reason_sunk'] = 'Флот потоплен';
$L['battleship_my_reason_surrender'] = 'Сдача';
$L['battleship_my_reason_abandon'] = 'Игра закрыта';

// Admin
$L['battleship_admin_title'] = 'Морской бой: управление';
$L['battleship_admin_help'] = 'Список игр, удаление отдельных партий, очистка завершённых и топ игроков. Чат хранится в БД только во время боя и удаляется автоматически.';
$L['battleship_admin_f_all'] = 'Все';
$L['battleship_admin_st_placing'] = 'Расстановка';
$L['battleship_admin_st_active'] = 'Активные';
$L['battleship_admin_st_finished'] = 'Завершённые';
$L['battleship_admin_deleted'] = 'Игра удалена';
$L['battleship_admin_purged'] = 'Удалено завершённых игр';
$L['battleship_admin_purge'] = 'Очистить завершённые';
$L['battleship_admin_purge_confirm'] = 'Удалить все завершённые игры?';
$L['battleship_admin_delete'] = 'Удалить';
$L['battleship_admin_delete_confirm'] = 'Удалить эту игру?';
$L['battleship_admin_no_games'] = 'Игр нет.';
$L['battleship_admin_top'] = 'Топ игроков по рейтингу';
$L['battleship_admin_col_p1'] = 'Игрок 1';
$L['battleship_admin_col_p2'] = 'Игрок 2';
$L['battleship_admin_col_winner'] = 'Победитель';
$L['battleship_admin_col_created'] = 'Создана';
$L['battleship_admin_col_player'] = 'Игрок';
