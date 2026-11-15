<!--
File: plugins/battleship/tpl/battleship.tpl
Modern Battleship UI (Bootstrap 5.3 + refined layout)
-->
<!-- BEGIN: MAIN -->
<div id="battleship-game" class="container-fluid py-3"
     data-ajax-url="{AJAX_URL}"
     data-xk="{XK}"
     data-poll-interval="2200"
     data-game-id="{GAME_ID}"
     data-lang-your-turn="{LANG_YOUR_TURN}"
     data-lang-enemy-turn="{LANG_ENEMY_TURN}"
     data-lang-win="{LANG_WIN}"
     data-lang-lose="{LANG_LOSE}"
     data-lang-not-your-turn="{LANG_NOT_YOUR_TURN}"
     data-lang-already-shot="{LANG_ALREADY_SHOT}"
     data-lang-unknown="{LANG_UNKNOWN}"
     data-lang-waiting="{LANG_WAITING}"
     data-lang-place-hint="{LANG_PLACE_HINT}"
     data-lang-not-all-placed="{LANG_NOT_ALL_PLACED}"
     data-lang-msg-hit="{LANG_MSG_HIT}"
     data-lang-msg-miss="{LANG_MSG_MISS}"
     data-lang-msg-sunk="{LANG_MSG_SUNK}"
     data-lang-msg-damaged="{LANG_MSG_DAMAGED}"
     data-lang-msg-win="{LANG_MSG_WIN}"
     data-lang-msg-lose="{LANG_MSG_LOSE}"
     data-lang-msg-your-turn="{LANG_MSG_YOUR_TURN}"
     data-lang-msg-enemy-turn="{LANG_MSG_ENEMY_TURN}"
     data-lang-confirm-surrender="{LANG_CONFIRM_SURRENDER}"
     data-lang-ai-thinking="{LANG_AI_THINKING}"
     data-lang-ships-left="{LANG_SHIPS_LEFT}">

  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <span class="badge text-bg-primary mb-2">BATTLESHIP</span>
      <h2 class="h3 mb-1">{PHP.L.battleship_title}</h2>
      <p class="text-body-secondary mb-0">{PHP.L.battleship_subtitle}</p>
    </div>
    <!-- IF {IS_LOGGED_IN} -->
    <div class="d-flex flex-wrap gap-2" id="battleship-controls">
      <button class="btn btn-primary" id="btn-mode-ai" type="button">
        <i class="fa-solid fa-robot me-1"></i>{PHP.L.battleship_btn_ai}
      </button>
      <button class="btn btn-outline-primary" id="btn-mode-pvp" type="button">
        <i class="fa-solid fa-users me-1"></i>{PHP.L.battleship_btn_pvp}
      </button>
      <button class="btn btn-outline-secondary" id="btn-list" type="button">{PHP.L.battleship_btn_list}</button>
      <button class="btn btn-outline-secondary" id="btn-stats" type="button">{PHP.L.battleship_btn_stats}</button>
      <a class="btn btn-outline-dark" href="{URL_MY}">{PHP.L.battleship_btn_my}</a>
      <a class="btn btn-outline-secondary" href="{URL_HOME}">{PHP.L.battleship_btn_new_game}</a>
      <button class="btn btn-outline-secondary" id="btn-sound" type="button" data-on="{PHP.L.battleship_btn_sound_on}" data-off="{PHP.L.battleship_btn_sound_off}">{PHP.L.battleship_btn_sound_on}</button>
      <button class="btn btn-outline-danger d-none" id="btn-surrender" type="button">{PHP.L.battleship_btn_surrender}</button>
    </div>
    <!-- ENDIF -->
  </div>

  <!-- IF {IS_LOGGED_IN} -->

  <div id="battleship-status" class="alert alert-info d-flex align-items-center gap-2" role="status" aria-live="polite">
    <i class="fa-solid fa-circle-info"></i>
    <span>{PHP.L.battleship_status_choose}</span>
  </div>
  <p class="small text-body-secondary mb-3">
    {PHP.L.battleship_active_slots}: <strong>{ACTIVE_COUNT}</strong> / {MAX_ACTIVE}
    &nbsp;·&nbsp; <a href="{URL_MY}">{PHP.L.battleship_btn_my}</a>
  </p>

  <div id="battleship-toast" class="battleship-toast" aria-live="polite" aria-atomic="true"></div>

  <!-- Placement -->
  <section id="battleship-placement" class="card shadow-sm mb-4 d-none">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
      <strong>{PHP.L.battleship_placement_title}</strong>
      <span id="battleship-remaining" class="small text-body-secondary"></span>
    </div>
    <div class="card-body">
      <p id="battleship-place-hint" class="text-body-secondary">{PHP.L.battleship_place_hint}</p>
      <div class="d-flex flex-wrap gap-2 mb-3">
        <button class="btn btn-sm btn-outline-secondary" id="btn-rotate" type="button">{PHP.L.battleship_btn_rotate}</button>
        <button class="btn btn-sm btn-outline-secondary" id="btn-random" type="button">{PHP.L.battleship_btn_random}</button>
        <button class="btn btn-sm btn-outline-secondary" id="btn-clear" type="button">{PHP.L.battleship_btn_clear}</button>
        <button class="btn btn-sm btn-primary" id="btn-confirm" type="button" disabled>{PHP.L.battleship_btn_confirm}</button>
      </div>
      <div class="battleship-board-wrap">
        <div id="battleship-place-board" class="battleship-board"></div>
      </div>
    </div>
  </section>

  <!-- Play boards -->
  <div class="row g-4" id="battleship-play-area">
    <div class="col-12 col-xl-6">
      <section class="card h-100 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{PHP.L.battleship_my_fleet}</strong>
          <span class="badge text-bg-secondary">{PHP.L.battleship_defence}</span>
        </div>
        <div class="card-body">
          <div class="bs-board-msg-host position-relative">
          <div id="msg-my" class="bs-board-msg position-absolute bottom-0 end-0" aria-live="polite"></div>
          <div class="battleship-board-wrap">
            <div id="battleship-my-board" class="battleship-board"></div>
          </div>
          </div>
        </div>
      </section>
    </div>
    <div class="col-12 col-xl-6">
      <section class="card h-100 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>{PHP.L.battleship_enemy_fleet}</strong>
          <span class="badge text-bg-danger">{PHP.L.battleship_attack}</span>
        </div>
        <div class="card-body">
          <p class="small text-body-secondary mb-2">{PHP.L.battleship_fire_hint}</p>
          <div class="bs-board-msg-host position-relative">
          <div id="msg-enemy" class="bs-board-msg position-absolute bottom-0 end-0" aria-live="polite"></div>
          <div class="battleship-board-wrap">
            <div id="battleship-enemy-board" class="battleship-board"></div>
          </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <!-- Legend -->
  <section class="card shadow-sm mt-4">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <strong>{PHP.L.battleship_legend}</strong>
        <span class="small text-body-secondary">{PHP.L.battleship_legend_hint}</span>
      </div>
      <div class="battleship-legend">
        <span><i class="legend-swatch swatch-ship"></i>{PHP.L.battleship_legend_ship}</span>
        <span><i class="legend-swatch swatch-hit"></i>{PHP.L.battleship_legend_hit}</span>
        <span><i class="legend-swatch swatch-sunk"></i>{PHP.L.battleship_legend_sunk}</span>
        <span><i class="legend-swatch swatch-miss"></i>{PHP.L.battleship_legend_miss}</span>
        <span><i class="legend-swatch swatch-enemy-hit"></i>{PHP.L.battleship_legend_enemy_hit}</span>
      </div>
    </div>
  </section>

  <!-- Waiting + Stats -->
  <div class="row g-4 mt-1">
    <div class="col-12 col-lg-7">
      <section class="card shadow-sm">
        <div class="card-header"><strong>{PHP.L.battleship_open_games}</strong></div>
        <div class="card-body" id="battleship-waiting">
          <p class="text-body-secondary mb-0">{PHP.L.battleship_open_games_hint}</p>
        </div>
      </section>
    </div>
    <div class="col-12 col-lg-5">
      <section class="card shadow-sm">
        <div class="card-header"><strong>{PHP.L.battleship_stats}</strong></div>
        <div class="card-body" id="battleship-stats">
          <p class="text-body-secondary mb-0">{PHP.L.battleship_stats_hint}</p>
        </div>
      </section>
    </div>
  </div>

  <!-- ELSE -->
  <div class="alert alert-warning">{PHP.L.battleship_err_auth}</div>
  <!-- ENDIF -->
</div>
<!-- END: MAIN -->
