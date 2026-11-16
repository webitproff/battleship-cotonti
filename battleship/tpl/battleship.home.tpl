<!-- 
 *  File: battleship.home.tpl
 *  Path: plugins/battleship/tpl/battleship.home.tpl
 *
 *  Web Application (Plugin) for CMF Cotonti v.1.0 compatible with PHP 8.5+, MySQL 8.4+
 *  Description=Battleship: vs AI and PvP with Chat & Sounds
 *  Version=3.3.3
 *  Date=2026-11-16
 *  Author=webitproff
 *  Copyright=webitproff https://github.com/webitproff/battleship-cotonti
 *  
 -->

<!-- BEGIN: MAIN -->

<div class="bs-game container-fluid px-3 py-5" id="bs-app" data-bsg-config="{BS_CONFIG}">

	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
		<div>
			<h1 class="h3 mb-0"><i class="bs-logo" aria-hidden="true">&#9875;</i> {PHP.L.battleship_title}</h1>
			<div class="text-body-secondary small">{PHP.L.battleship_subtitle}</div>
		</div>
		<div id="bs-header-actions" class="d-flex gap-2"></div>
	</div>

	<ul class="nav nav-pills mb-3">
		<li class="nav-item"><a class="nav-link active" href="{BS_URL_HOME}">{PHP.L.battleship_nav_home}</a></li>
		<li class="nav-item"><a class="nav-link" href="{BS_URL_MY}">{PHP.L.battleship_nav_my}</a></li>
	</ul>

	<!-- IF {BS_LOGGED} -->
		<div id="bs-root">
			<div class="text-center text-body-secondary py-5">
				<div class="spinner-border spinner-border-sm me-2" role="status"></div>{PHP.L.battleship_loading}
			</div>
		</div>
		<div class="toast-container position-fixed bottom-0 end-0 p-3" id="bs-toasts" aria-live="polite"></div>
	<!-- ELSE -->
		<div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
			<span>{PHP.L.battleship_login_required}</span>
			<a class="btn btn-primary btn-sm" href="{BS_LOGIN_URL}">{PHP.L.battleship_btn_login}</a>
		</div>
	<!-- ENDIF -->

</div>
<!-- END: MAIN -->
