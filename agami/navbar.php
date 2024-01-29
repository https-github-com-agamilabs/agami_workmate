<style media="screen">
	.logo-src {
		background-image: url("../<?= $response["orglogourl"]; ?>") !important;
		height: 60px !important;
		background-size: contain !important;
		width: 62px !important;
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
							<div class="btn-group">
								<a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
									<img width="42" class="rounded-circle" src="<?php
																				if (!empty($_SESSION["photoname"])) {
																					echo $_SESSION["photoname"];
																				} else {
																					echo "../assets/image/user_icon.png";
																				}
																				?>" onerror="this.onerror=null;this.src='../assets/image/user_icon.png'" alt="">
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
									<button id="change_password_button" type="button" tabindex="0" class="dropdown-item">Change Password</button>
									<div tabindex="-1" class="dropdown-divider"></div>
									<a href="../logout.php" tabindex="0" class="dropdown-item">Logout</a>
								</div>
							</div>
						</div>
						<div class="widget-content-left ml-3 header-user-info">
							<div class="widget-heading">
								<?php
								if (!empty($_SESSION["firstname"])) {
									echo $_SESSION["firstname"];
								}
								if (!empty($_SESSION["lastname"])) {
									echo " " . $_SESSION["lastname"];
								}
								?>
							</div>
							<div class="widget-subheading">
								<?php
								if (!empty($_SESSION["designation"])) {
									echo $_SESSION["designation"];
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
				<h5 class="modal-title">Change Your Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="password_message_root"> </div>

				<form id="user_form_change_password">
					<div class="form-group">
						<label class="d-block mb-0">
							Old Password <span class="text-danger">*</span>
							<input name="oldpassword" class="form-control shadow-sm mt-2" type="password" placeholder="Old Password..." required>
						</label>
					</div>

					<div class="form-group">
						<label class="d-block mb-0">
							Type New Password <span class="text-danger">*</span>
							<input name="newpassword" class="form-control shadow-sm mt-2" type="password" placeholder="Type New Password..." required>
						</label>
					</div>

					<div class="form-group">
						<label class="d-block mb-0">
							Re-type New Password <span class="text-danger">*</span>
							<input name="newconfirmpassword" class="form-control shadow-sm mt-2" type="password" placeholder="Re-type New Password..." required>
						</label>
					</div>

					<div class="text-center">
						<button type="submit" class="btn btn-primary ripple custom_shadow">Change Password</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
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

		$.post(`php/ui/users/change_password_of_a_user.php`, json, resp => {
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