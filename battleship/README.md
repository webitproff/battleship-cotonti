<!-- File: README.md -->
<!-- Path: plugins/battleship/README.md -->

# Battleship (Морской бой) 2.1.0 for Cotonti

Игра против компьютера и PvP между авторизованными пользователями. Bootstrap 5.3, PHP 8.x.

## Установка / обновление
1. Скопируйте папку `plugins/battleship` в `plugins/` сайта (при обновлении перезапишите файлы; старый `tpl/battleship.tpl` больше не нужен).
2. Админка → Расширения → Battleship → Установить (или «Обновить» с 2.0.x: добавится таблица чата).
   При обновлении с 1.x таблицы игр и статистики пересоздаются (формат данных изменился).

## Режимы (роутер battleship.php)
| Режим | URL | Файл | Шаблон |
|---|---|---|---|
| home | `?e=battleship` | `inc/battleship.home.php` | `tpl/battleship.home.tpl` |
| my | `?e=battleship&m=my` | `inc/battleship.my.php` | `tpl/battleship.my.tpl` |
| play | `?e=battleship&m=play&id=N` | `inc/battleship.play.php` | `tpl/battleship.play.tpl` |

CSS и JS подключаются только в `home` и `play`. Страница `my` рендерится на сервере.

## Чат (только PvP)
- до 255 символов, не чаще одного сообщения в 5 секунд от пользователя (настраивается: 3/5/10/15 с);
- хранится в таблице `battleship_chat` только пока игра в статусе `active`;
- при завершении (победа, сдача, закрытие по таймауту, удаление игры в админке) сообщения удаляются автоматически;
  дополнительно страховочная очистка выполняется при каждом обращении к лобби.

## Звуки
Web Audio API, внешних файлов нет. Мимо, попал, потопил, победа, поражение, постановка корабля.
Кнопка «Звук вкл / Звук выкл», состояние хранится в localStorage.

## Админка
Админка → Другое → Battleship (`admin?m=other&p=battleship`): фильтр (все / расстановка / активные / завершённые),
удаление игры, очистка завершённых, топ игроков.

## Настройки
Лимит активных игр, ход после попадания, сложность ИИ, закрытие неактивных игр, чат вкл/выкл, интервал чата.

## Файлы
`battleship.php` (роутер), `battleship.ajax.php` (API), `battleship.admin.php` (tools), `battleship.setup.php`,
`inc/` (functions, ai, home, my, play), `tpl/` (home, my, play, admin), `js/battleship.js`, `css/battleship.css`,
`lang/` (ru, en), `setup/` (install, uninstall, update 2.0.0 и 2.1.0).
