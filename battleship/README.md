# Battleship v.3.3.3

**Battleship is a web application, an online game against the computer and PvP between authorized users, with chat, humorous notifications and sound.**

Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+

---

[![Version](https://img.shields.io/badge/version-3.3.3-green.svg)](https://github.com/webitproff/battleship-cotonti/releases)
[![Cotonti Compatibility](https://img.shields.io/badge/Cotonti-v.1-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5-purple.svg)](https://www.php.net/releases/8_5_0.php)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-blue.svg)](https://www.mysql.com/)
[![Bootstrap v5.3.8](https://img.shields.io/badge/Bootstrap-v5.3.8-blueviolet.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/license-BSD-blue.svg)](https://github.com/webitproff/battleship-cotonti/blob/main/LICENSE)

---

---
<img width="1920" height="1080" alt="battleship-cotonti" src="https://github.com/user-attachments/assets/645e6273-ef3d-4993-9c54-19225c879f35" />
<img width="1920" height="1080" alt="battleship_cotonti_002" src="https://github.com/user-attachments/assets/f7e5d843-b25e-46fb-a38f-699d441f9738" />
<img width="1920" height="1080" alt="battleship_cotonti_003" src="https://github.com/user-attachments/assets/72da8066-e1b4-437b-86e0-e0964966ab01" />
<img width="1920" height="949" alt="battleship_cotonti_004" src="https://github.com/user-attachments/assets/f78a91ac-2c6f-4051-a02a-88749f64faea" />
<img width="1920" height="1080" alt="battleship_cotonti_005" src="https://github.com/user-attachments/assets/921d9615-1cec-478f-b438-0f1da748c3c2" />
<img width="1920" height="1080" alt="battleship_cotonti_001" src="https://github.com/user-attachments/assets/8039705a-63b7-43fe-b911-cdb7d6d37950" />



## Installation / update

1. Copy the `plugins/battleship` folder into the site's `plugins/`.
2. Admin → Extensions → Battleship → Install.
3. Add a link to the game anywhere on the site, approximately like this:
```html
<!-- IF {PHP|cot_plugin_active('battleship')} -->
<li>
	<a class="nav-link" href="{PHP|cot_url('battleship')}" title="{PHP.L.battleship_desc}">
		<span class="me-2">
			<i class="fa-solid fa-ship"></i>
		</span>{PHP.L.battleship_title} 
	</a>
</li>
<!-- ENDIF -->
```

---
## Features

- **Game modes**
  - Against the computer (AI)
  - PvP — create an open game and join an existing one

- **Interface**
  - Two separate boards: "Your fleet" and "Opponent's board"
  - Coordinates **A–J** / **1–10**
  - Legend with color-coded cell markings
  - Toast notifications with various phrases ("Hit!", "Miss!", "Sunk!", etc.)
  - Responsive layout for Bootstrap 5.3

- **Ship placement**
  - Manual placement with preview
  - Random placement
  - Rotate ship
  - Clear board
  - Counter of remaining ships

- **Game mechanics**
  - Classic fleet composition: 1×4, 2×3, 3×2, 4×1
  - Ships do not touch each other (including diagonals)
  - Sinking detection
  - Player statistics (wins / losses / rating)
  - Ability to surrender


## Modes (router battleship.php)

| Mode | URL | File | Template |
|---|---|---|---|
| home | `?e=battleship` | `inc/battleship.home.php` | `tpl/battleship.home.tpl` |
| my | `?e=battleship&m=my` | `inc/battleship.my.php` | `tpl/battleship.my.tpl` |
| play | `?e=battleship&m=play&id=N` | `inc/battleship.play.php` | `tpl/battleship.play.tpl` |

CSS and JS are included only in `home` and `play`. The `my` page is rendered on the server.

## Chat (PvP only)
- up to 255 characters, no more than one message per 5 seconds per user (configurable: 3/5/10/15 s);
- stored in the `battleship_chat` table only while the game is in `active` status;
- upon completion (victory, surrender, timeout closure, game deletion in admin) messages are deleted automatically;
  additionally, a safety cleanup is performed on each access to the lobby.

## Sounds
Web Audio API, no external files. Miss, hit, sunk, victory, defeat, ship placement.
Chat: own signals for sending (click and 2525 Hz tone, like NASA's quindar tone) and for receiving (air hiss, 2475 Hz tone and a short "roger").
"Sound on / Sound off" button, state stored in localStorage.

## Admin
Admin → Other → Battleship (`admin?m=other&p=battleship`): filter (all / placement / active / finished),
delete game, purge finished, top players.

## Settings
Active games limit, extra turn after hit, AI difficulty, inactive games closure, chat on/off, chat interval.

## Files
```
plugins/battleship/
├── battleship.setup.php
├── battleship.php
├── battleship.tools.php
├── battleship.ajax.php
├── inc/
│   ├── battleship.ai.php
│   ├── battleship.functions.php
│   ├── battleship.home.php
│   ├── battleship.my.php
│   └── battleship.play.php
├── tpl/
│   ├── battleship.admin.tpl
│   ├── battleship.home.tpl
│   ├── battleship.my.tpl
│   └── battleship.play.tpl
├── js/
│   └── battleship.js
├── css/
│   └── battleship.css
├── lang/
│   ├── battleship.en.lang.php
│   └── battleship.ru.lang.php
└── setup/
    ├── battleship.install.sql
    └── battleship.uninstall.sql
```

---
> **Order modifications or a new extension**    
> **via [the contacts on the author's page](https://github.com/webitproff)**     
---



---
> РУССКИЙ
---

File: README.md


# Battleship (Морской бой) v.3.3.3


Морской бой — это веб-приложение, онлайн-игра против компьютера и PvP между авторизованными пользователями, с чатом, шуточными уведомлениями и озвучкой.

Веб-приложение (плагин) для CMF Cotonti v.1.0, совместимо с PHP 8.5+, MySQL 8.4+

---

[![Version](https://img.shields.io/badge/version-3.3.3-green.svg)](https://github.com/webitproff/battleship-cotonti/releases)
[![Cotonti Compatibility](https://img.shields.io/badge/Cotonti-v.1-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5-purple.svg)](https://www.php.net/releases/8_5_0.php)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-blue.svg)](https://www.mysql.com/)
[![Bootstrap v5.3.8](https://img.shields.io/badge/Bootstrap-v5.3.8-blueviolet.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/license-BSD-blue.svg)](https://github.com/webitproff/battleship-cotonti/blob/main/LICENSE)

---

## Установка / обновление

1. Скопируйте папку `plugins/battleship` в `plugins/` сайта.
2. Админка → Расширения → Battleship → Установить.
3. Добавьте ссылку на игру в любом месте сайта примерно такого вида:
```html
<!-- IF {PHP|cot_plugin_active('battleship')} -->
<li>
	<a class="nav-link" href="{PHP|cot_url('battleship')}" title="{PHP.L.battleship_desc}">
		<span class="me-2">
			<i class="fa-solid fa-ship"></i>
		</span>{PHP.L.battleship_title} 
	</a>
</li>
<!-- ENDIF -->
```

---
## Возможности

- **Режимы игры**
  - Против компьютера (ИИ)
  - PvP — создание открытой игры и присоединение к существующей

- **Интерфейс**
  - Два отдельных поля: «Ваш флот» и «Поле противника»
  - Координаты **A–J** / **1–10**
  - Легенда с цветными обозначениями клеток
  - Toast-уведомления с разными фразами («Попал!», «Мимо!», «Потопил!» и т.д.)
  - Адаптивная вёрстка под Bootstrap 5.3

- **Расстановка кораблей**
  - Ручная расстановка с превью
  - Случайная расстановка
  - Поворот корабля
  - Очистка поля
  - Счётчик оставшихся кораблей

- **Игровая механика**
  - Классический состав флота: 1×4, 2×3, 3×2, 4×1
  - Корабли не касаются друг друга (включая диагонали)
  - Определение потопления
  - Статистика игроков (победы / поражения / рейтинг)
  - Возможность сдаться


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
Чат: свои сигналы на отправку (щелчок и тон 2525 Гц, как квиндар-тон NASA) и на получение (шипение эфира, тон 2475 Гц и короткий «roger»).
Кнопка «Звук вкл / Звук выкл», состояние хранится в localStorage.

## Админка
Админка → Другое → Battleship (`admin?m=other&p=battleship`): фильтр (все / расстановка / активные / завершённые),
удаление игры, очистка завершённых, топ игроков.

## Настройки
Лимит активных игр, ход после попадания, сложность ИИ, закрытие неактивных игр, чат вкл/выкл, интервал чата.

## Файлы
```
plugins/battleship/
├── battleship.setup.php
├── battleship.php
├── battleship.tools.php
├── battleship.ajax.php
├── inc/
│   ├── battleship.ai.php
│   ├── battleship.functions.php
│   ├── battleship.home.php
│   ├── battleship.my.php
│   └── battleship.play.php
├── tpl/
│   ├── battleship.admin.tpl
│   ├── battleship.home.tpl
│   ├── battleship.my.tpl
│   └── battleship.play.tpl
├── js/
│   └── battleship.js
├── css/
│   └── battleship.css
├── lang/
│   ├── battleship.en.lang.php
│   └── battleship.ru.lang.php
└── setup/
    ├── battleship.install.sql
    └── battleship.uninstall.sql
```

---
> **Заказать правки или новое расширение**    
> **можно [по контактам на странице автора](https://github.com/webitproff)**     
---




## Author

**webitproff**

License: BSD

Repository: [https://github.com/webitproff/battleship-cotonti](https://github.com/webitproff/battleship-cotonti)

Data: 2026 Nov 16
---
