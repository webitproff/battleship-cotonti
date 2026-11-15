<!--
File: plugins/battleship/tpl/battleship.admin.tpl
-->
<!-- BEGIN: MAIN -->
<div class="block">
  <h2>{ADMIN_TITLE}</h2>
  {MESSAGES}

  <div class="marginbottom10">
    <a href="{FILTER_ALL_URL}" class="button">{PHP.L.All} ({CNT_ALL})</a>
    <a href="{FILTER_PLACING_URL}" class="button">{PHP.L.battleship_status_placing} ({CNT_PLACING})</a>
    <a href="{FILTER_ACTIVE_URL}" class="button">{PHP.L.battleship_status_active} ({CNT_ACTIVE})</a>
    <a href="{FILTER_FINISHED_URL}" class="button">{PHP.L.battleship_status_finished} ({CNT_FINISHED})</a>
    <a href="{PURGE_URL}" class="button" onclick="return confirm('{PHP.L.battleship_admin_purge_confirm}');">{PHP.L.battleship_admin_purge}</a>
  </div>

  <table class="cells">
    <tr>
      <td class="coltop">ID</td>
      <td class="coltop">{PHP.L.battleship_col_mode}</td>
      <td class="coltop">{PHP.L.battleship_col_status}</td>
      <td class="coltop">P1</td>
      <td class="coltop">P2</td>
      <td class="coltop">{PHP.L.battleship_admin_winner}</td>
      <td class="coltop">{PHP.L.battleship_col_updated}</td>
      <td class="coltop"></td>
    </tr>
    <!-- BEGIN: GAME_ROW -->
    <tr>
      <td>{GAME_ID}</td>
      <td>{GAME_MODE}</td>
      <td>{GAME_STATUS}</td>
      <td>{GAME_P1}</td>
      <td>{GAME_P2}</td>
      <td>{GAME_WINNER}</td>
      <td>{GAME_UPDATED}</td>
      <td class="centerall">
        <a href="{GAME_DELETE_URL}" onclick="return confirm('{PHP.L.Delete}?');">{PHP.L.Delete}</a>
      </td>
    </tr>
    <!-- END: GAME_ROW -->
    <!-- BEGIN: EMPTY -->
    <tr><td colspan="8" class="centerall">{PHP.L.None}</td></tr>
    <!-- END: EMPTY -->
  </table>

  <p class="paging">{PAGENAV_PREV} {PAGENAV_PAGES} {PAGENAV_NEXT}</p>

  <h3>{PHP.L.battleship_admin_top}</h3>
  <table class="cells">
    <tr>
      <td class="coltop">{PHP.L.User}</td>
      <td class="coltop">{PHP.L.battleship_stats_wins}</td>
      <td class="coltop">{PHP.L.battleship_stats_losses}</td>
      <td class="coltop">{PHP.L.battleship_stats_rating}</td>
    </tr>
    <!-- BEGIN: TOP_ROW -->
    <tr>
      <td>{TOP_NAME} <span class="text-muted">#{TOP_UID}</span></td>
      <td>{TOP_WINS}</td>
      <td>{TOP_LOSSES}</td>
      <td>{TOP_RATING}</td>
    </tr>
    <!-- END: TOP_ROW -->
  </table>
</div>
<!-- END: MAIN -->
