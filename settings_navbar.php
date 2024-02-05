<?php
if (isset($_GET['lang'])) {
	$_SESSION["lang"] = $_GET['lang'];
} else if (!isset($_SESSION["lang"])) {
	$_SESSION["lang"] = "en";
}


$lang = $_SESSION["lang"];

require_once dirname(__FILE__) . "/lang_converter/converter.php";
// $jasonFilePath = $basePath . "/lang-json/$lang/profile.json";

if (!isset($arrayData)) {
	$arrayData = array();
}
$arrayData = array_merge($arrayData, langConverter($lang, 'profile'));
?>
<style media="screen">
	.logo-src {
		background: url("<?= $response["orglogourl"]; ?>") !important;
		height: 51px !important;
		background-size: contain !important;
		background-repeat: no-repeat !important;
		background-position: center !important;
		width: 109px !important;
	}
</style>
<div class="app-header header-shadow d-print-none">
	<div class="app-header__logo">
		<div class="logo-src mx-auto"></div>
		<div class="header__pane ml-auto">
			<div>
				<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
					<span class="hamburger-box">
						<span class="hamburger-inner"></span>
					</span>
				</button>
			</div>
		</div>
	</div>

	<div class="app-header__mobile-menu">
		<div>
			<button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</button>
		</div>
	</div>

	<div class="app-header__menu">
		<span>
			<button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
				<span class="btn-icon-wrapper">
					<i class="fa fa-ellipsis-v fa-w-6"></i>
				</span>
			</button>
		</span>
	</div>

	<div class="app-header__content">
		<div class="app-header-left">
			<!-- <div class="search-wrapper">
				<div class="input-holder">
					<input type="text" class="search-input" placeholder="Type to search">
					<button class="search-icon"><span></span></button>
				</div>
				<button class="close"></button>
			</div> -->
		</div>

		<div class="app-header-right">
			<div class="header-btn-lg pr-0">
				<div class="widget-content p-0">
					<div class="widget-content-wrapper">
						<div class="widget-content-left">
							<div>
								<select name="lang" class="form-control form-control-sm shadow-sm" title="Language">
									<option value="en">EN</option>
									<option value="bn">BN</option>
								</select>
							</div>
						</div>
						<div class="widget-content-left ml-1 ml-xl-3">
							<div class="btn-group">
								<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
									<img width="42" class="rounded-circle" src="<?php
																				if (!empty($_SESSION["photoname"])) {
																					echo $_SESSION["photoname"];
																				} else {
																					echo "assets/image/user_icon.png";
																				}
																				?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" alt="">
									<i class="fa fa-angle-down ml-2 opacity-8"></i>
								</a>
								<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-180px, 44px, 0px);">
									<h6 tabindex="-1" class="dropdown-header d-flex justify-content-between align-self-center">
										<span id="light_theme" class="badge badge-light shadow-sm border grow cursor-pointer">Light</span>
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input" id="theme_custom_switch" checked>
											<label class="custom-control-label" for="theme_custom_switch"> </label>
										</div>
										<span id="dark_theme" class="badge badge-dark shadow-sm border grow cursor-pointer">Dark</span>
									</h6>
									<div tabindex="-1" class="dropdown-divider"></div>
									<button id="change_password_button" type="button" tabindex="0" class="dropdown-item">
										<i class="fas fa-lock-open mr-2"></i> <?= $arrayData['lang_change_password']; ?>
									</button>
									<!-- <div tabindex="-1" class="dropdown-divider"></div> -->
									<a href="logout.php" tabindex="0" class="dropdown-item">
										<i class="fas fa-sign-out-alt mr-2"></i> <?= $arrayData['lang_logout']; ?>
									</a>
								</div>
							</div>
						</div>
						<div class="widget-content-left ml-3 header-user-info">
							<div class="widget-heading">
								<?php
								if (!empty($_SESSION["cogo_firstname"])) {
									echo $_SESSION["cogo_firstname"];
								}
								if (!empty($_SESSION["cogo_lastname"])) {
									echo " " . $_SESSION["cogo_lastname"];
								}
								?>
							</div>
							<div class="widget-subheading">
								<?php
								if (!empty($moduletitles)) {
									echo "Accessibility: " . $moduletitles;
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- PASSWORD CHANGE MODAL -->
<div class="modal animated fadeInUp fade-scale" tabindex="-1" role="dialog" id="modal_change_password">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?= $arrayData['lang_change_your_password']; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="password_message_root"> </div>

				<form id="user_form_change_password">
					<div class="form-group">
						<label class="d-block mb-0">
							<?= $arrayData['lang_old_password']; ?> <span class="text-danger">*</span>
							<input name="oldpassword" class="form-control shadow-sm mt-2" type="password" placeholder="<?= $arrayData['lang_old_password']; ?>..." required>
						</label>
					</div>

					<div class="form-group">
						<label class="d-block mb-0">
							<?= $arrayData['lang_type_new_password']; ?> <span class="text-danger">*</span>
							<input name="newpassword" class="form-control shadow-sm mt-2" type="password" placeholder="<?= $arrayData['lang_type_new_password']; ?>..." required>
						</label>
					</div>

					<div class="form-group">
						<label class="d-block mb-0">
							<?= $arrayData['lang_re-type_new_password']; ?><span class="text-danger">*</span>
							<input name="newconfirmpassword" class="form-control shadow-sm mt-2" type="password" placeholder="<?= $arrayData['lang_re-type_new_password']; ?>..." required>
						</label>
					</div>

					<div class="text-center">
						<button type="submit" class="btn btn-primary ripple custom_shadow"><?= $arrayData['lang_change_password']; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	let langSelect = document.getElementsByName(`lang`)[0];
	langSelect.value = `<?= $_SESSION["lang"] ?>`;

	langSelect.addEventListener(`change`, function(e) {
		if (location.search.length) {
			if (location.href.lastIndexOf(`lang`) >= 0) {
				let start = location.href.lastIndexOf(`lang`) + 5,
					end = start + 2;
				let lang = location.href.substring(start, end);

				location.href = location.href.replace(lang, this.value);
			} else {
				location.href = `${location.href}&lang=${this.value}`;
			}
		} else {
			location.href = `${location.href}?lang=${this.value}`;
		}
	});

	$(".modal").on("hidden.bs.modal", function(e) {
		let element = $(".modal-backdrop.show");
		if (element.length) {
			element.remove();
		}
	});

	function get_alert(className, iconClassName, textToDisplay) {
		$(`#password_message_root`).html(`<div class="alert ${className} rounded-pill" role="alert">
			<i class="fas ${iconClassName} mr-2"></i> ${textToDisplay}
		</div>`);
	}

	$(`#change_password_button`).click(function() {
		$(`#modal_change_password`).modal(`show`);
		$(`#password_message_root`).empty();
	});

	$(`#user_form_change_password`).submit(function(e) {
		e.preventDefault();
		let json = Object.fromEntries((new FormData(this)).entries());

		if (json.newpassword !== json.newconfirmpassword) {
			get_alert(`alert-warning`, `fa-exclamation-circle`, `Password mismatch!`);
			return;
		}

		$.post(`php/ui/user/change_password_of_an_user.php`, json, resp => {
			if (resp.error) {
				get_alert(`alert-warning`, `fa-exclamation-circle`, result.message);
			} else {
				toastr.success(resp.message);

				$(`#modal_change_password`).modal(`hide`);
				get_alert(`alert-success`, `fa-check-circle`, result.message);
				document.getElementById(`user_form_change_password`).reset();
			}
		}, `json`);
	});
</script>