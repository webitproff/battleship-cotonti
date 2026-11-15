# Battleship for Cotonti

A modern Battleship plugin for the [Cotonti](https://www.cotonti.com) CMS/CMF.

Play against the computer or in PvP with other authorized users. Classic rules, neat Bootstrap 5.3 interface, clear board layout, and live notifications.

---
<img width="1920" height="1080" alt="battleship-cotonti" src="https://github.com/user-attachments/assets/645e6273-ef3d-4993-9c54-19225c879f35" />
<img width="1920" height="1080" alt="battleship_cotonti_002" src="https://github.com/user-attachments/assets/f7e5d843-b25e-46fb-a38f-699d441f9738" />
<img width="1920" height="1080" alt="battleship_cotonti_003" src="https://github.com/user-attachments/assets/72da8066-e1b4-437b-86e0-e0964966ab01" />
<img width="1920" height="949" alt="battleship_cotonti_004" src="https://github.com/user-attachments/assets/f78a91ac-2c6f-4051-a02a-88749f64faea" />
<img width="1920" height="1080" alt="battleship_cotonti_005" src="https://github.com/user-attachments/assets/921d9615-1cec-478f-b438-0f1da748c3c2" />
<img width="1920" height="1080" alt="battleship_cotonti_001" src="https://github.com/user-attachments/assets/8039705a-63b7-43fe-b911-cdb7d6d37950" />



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

---

## Requirements

- Cotonti (current branch)
- PHP 8.1+ (8.5 recommended)
- MySQL / MariaDB
- Bootstrap 5.3 on the site (for correct display)

---

## Installation

1. Copy the `battleship` folder into the `plugins/` directory of your site.
2. Go to **Admin → Extensions**.
3. Find the **Battleship** plugin and install it.
4. If necessary, configure the "Maximum active games per user" parameter.

After installation, the game will be available at:

```
index.php?e=battleship
```

(or via SEO URLs if configured).

---

## Plugin structure

```
battleship/
├── battleship.setup.php          # Plugin registration and configuration
├── battleship.php                # Entry point (standalone)
├── battleship.ajax.php           # AJAX action handler
├── css/
│   └── battleship.css            # Game styles
├── js/
│   └── battleship.js             # Client-side logic
├── lang/
│   └── battleship.ru.lang.php    # Russian language
├── tpl/
│   └── battleship.tpl            # Interface template
├── setup/
│   ├── battleship.install.sql    # Tables on installation
│   └── battleship.uninstall.sql  # Table removal
└── inc/
    └── battleship.functions.php  # Game logic
```

---

## How to play

1. Log in to the site.
2. Open the plugin page.
3. Choose a mode:
   - **Against the computer** — go straight to ship placement.
   - **Create PvP game** — wait for an opponent.
   - **Open games** — join an existing one.
4. Place your ships (or click "Random") and confirm.
5. Shoot at the enemy's board. Watch the notifications and legend.

---

## Technical details

- All actions go through AJAX (`battleship.ajax.php`).
- Game state is stored in tables:
  - `cot_battleship_games`
  - `cot_battleship_stats`
- CSRF protection via the standard Cotonti `x`-token.
- AI uses a simple hunt mode (after a hit, it strikes adjacent cells).

---

## Author

**webitproff**

License: BSD

Repository: [https://github.com/webitproff/battleship-cotonti](https://github.com/webitproff/battleship-cotonti)

---

## Changelog

### 2.0.0
- Completely rewritten interface
- Separate player and opponent boards
- Coordinate grid A1–J10
- Legend and toast notifications
- Improved PvP experience
- PHP 8.5 compatibility
