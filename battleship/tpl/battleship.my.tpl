<!--
File: plugins/battleship/tpl/battleship.my.tpl
Mode: my — мои игры
-->
<!-- BEGIN: MAIN -->
<div class="container py-3">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
      <h2 class="h3 mb-1">{PHP.L.battleship_my_title}</h2>
      <p class="text-body-secondary mb-0">
        {PHP.L.battleship_active_slots}: <strong>{ACTIVE_COUNT}</strong> / {MAX_ACTIVE}
      </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-primary" href="{URL_HOME}">{PHP.L.battleship_btn_new_game}</a>
      <a class="btn btn-outline-secondary" href="{URL_HOME}">{PHP.L.battleship_title}</a>
    </div>
  </div>

  {MESSAGES}

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>{PHP.L.battleship_col_mode}</th>
              <th>{PHP.L.battleship_col_status}</th>
              <th>{PHP.L.battleship_col_updated}</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <!-- BEGIN: GAME_ROW -->
            <tr>
              <td>{GAME_ID}</td>
              <td><span class="badge text-bg-secondary">{GAME_MODE}</span></td>
              <td>{GAME_STATUS_L}</td>
              <td class="small text-body-secondary">{GAME_UPDATED}</td>
              <td class="text-end text-nowrap">
                <!-- IF {GAME_CAN_PLAY} -->
                <a class="btn btn-sm btn-primary" href="{GAME_URL_PLAY}">{PHP.L.battleship_btn_continue}</a>
                <!-- ENDIF -->
                <!-- IF {GAME_CAN_ABANDON} -->
                <a class="btn btn-sm btn-outline-danger" href="{GAME_URL_ABANDON}"
                   onclick="return confirm('{PHP.L.battleship_confirm_abandon}');">{PHP.L.battleship_btn_abandon}</a>
                <!-- ENDIF -->
                <!-- IF !{GAME_CAN_PLAY} -->
                <span class="text-body-secondary small">—</span>
                <!-- ENDIF -->
              </td>
            </tr>
            <!-- END: GAME_ROW -->
            <!-- BEGIN: EMPTY -->
            <tr>
              <td colspan="5" class="text-center text-body-secondary py-4">{PHP.L.battleship_my_empty}</td>
            </tr>
            <!-- END: EMPTY -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <p class="small text-body-secondary mt-3">{PHP.L.battleship_my_hint}</p>
</div>
<!-- END: MAIN -->
