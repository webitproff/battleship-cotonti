<!-- 
 *  File: battleship.my.tpl 
 *  Path: plugins/battleship/tpl/battleship.my.tpl
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP
 *  Version=3.3.2
 *  Date=2026-11-15
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  Notes=BSD
 -->

<!-- BEGIN: MAIN -->

<div class="bs-game container-fluid p-3" id="bs-app">

	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<div class="mb-3">
		<h1 class="h3 mb-0"><i class="bs-logo" aria-hidden="true">&#9875;</i> {PHP.L.battleship_title}</h1>
		<div class="text-body-secondary small">{PHP.L.battleship_my_subtitle}</div>
	</div>

	<ul class="nav nav-pills mb-3">
		<li class="nav-item"><a class="nav-link" href="{BS_URL_HOME}">{PHP.L.battleship_nav_home}</a></li>
		<li class="nav-item"><a class="nav-link active" href="{BS_URL_MY}">{PHP.L.battleship_nav_my}</a></li>
	</ul>

	<!-- IF {BS_LOGGED} -->

		<div class="card mb-3">
			<div class="card-header fw-semibold">{PHP.L.battleship_my_active}</div>
			<div class="table-responsive">
				<table class="table table-hover align-middle mb-0">
					<thead>
						<tr>
							<th>#</th>
							<th>{PHP.L.battleship_my_col_mode}</th>
							<th>{PHP.L.battleship_my_col_opp}</th>
							<th>{PHP.L.battleship_my_col_status}</th>
							<th>{PHP.L.battleship_my_col_updated}</th>
							<th class="text-end">{PHP.L.battleship_my_col_actions}</th>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN: ACTIVE_ROW -->
						<tr>
							<td>{ROW_ID}</td>
							<td>{ROW_MODE}</td>
							<td>{ROW_OPP}</td>
							<td><span class="badge text-bg-{ROW_STATUS_CLASS}">{ROW_STATUS}</span></td>
							<td class="text-body-secondary small">{ROW_UPDATED}</td>
							<td class="text-end text-nowrap">
								<a class="btn btn-sm btn-primary" href="{ROW_PLAY_URL}">{PHP.L.battleship_btn_resume}</a>
								<a class="btn btn-sm btn-outline-danger" href="{ROW_CANCEL_URL}" data-confirm="{PHP.L.battleship_btn_surrender_confirm}">{ROW_CANCEL_LABEL}</a>
							</td>
						</tr>
						<!-- END: ACTIVE_ROW -->
						<!-- BEGIN: ACTIVE_EMPTY -->
						<tr><td colspan="6" class="text-body-secondary">{PHP.L.battleship_no_my_games}</td></tr>
						<!-- END: ACTIVE_EMPTY -->
					</tbody>
				</table>
			</div>
		</div>

		<div class="card mb-3">
			<div class="card-header fw-semibold">{PHP.L.battleship_my_history}</div>
			<div class="table-responsive">
				<table class="table align-middle mb-0">
					<thead>
						<tr>
							<th>#</th>
							<th>{PHP.L.battleship_my_col_mode}</th>
							<th>{PHP.L.battleship_my_col_opp}</th>
							<th>{PHP.L.battleship_my_col_result}</th>
							<th>{PHP.L.battleship_my_col_reason}</th>
							<th>{PHP.L.battleship_my_col_updated}</th>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN: FINISHED_ROW -->
						<tr>
							<td>{FROW_ID}</td>
							<td>{FROW_MODE}</td>
							<td>{FROW_OPP}</td>
							<td><span class="badge text-bg-{FROW_RESULT_CLASS}">{FROW_RESULT}</span></td>
							<td class="text-body-secondary small">{FROW_REASON}</td>
							<td class="text-body-secondary small">{FROW_UPDATED}</td>
						</tr>
						<!-- END: FINISHED_ROW -->
						<!-- BEGIN: FINISHED_EMPTY -->
						<tr><td colspan="6" class="text-body-secondary">{PHP.L.battleship_my_no_history}</td></tr>
						<!-- END: FINISHED_EMPTY -->
					</tbody>
				</table>
			</div>
		</div>

		<script>
		document.addEventListener('click', function (e) {
			var a = e.target.closest ? e.target.closest('[data-confirm]') : null;
			if (a && !window.confirm(a.getAttribute('data-confirm'))) { e.preventDefault(); }
		});
		</script>

	<!-- ELSE -->
		<div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
			<span>{PHP.L.battleship_login_required}</span>
			<a class="btn btn-primary btn-sm" href="{BS_LOGIN_URL}">{PHP.L.battleship_btn_login}</a>
		</div>
	<!-- ENDIF -->

</div>
<!-- END: MAIN -->
