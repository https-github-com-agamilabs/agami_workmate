<?php
$basePath = dirname(dirname(__FILE__));
include_once $basePath . "/php/ui/login/check_session.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/shared/layout/header.php"; ?>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-tachometer-alt icon-gradient bg-amy-crisp"></i>
								</div>
								<div>
									Dashboard
									<div class="page-title-subheading">Your agami statistics mentioned here.</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card mb-3 d-none">
						<div class="card-header">
							<h5 class="font-weight-bold">Settings</h5>
						</div>
						<div class="card-body">
							<fieldset class="custom_fieldset pt-1 mb-2">
								<legend class="px-2 mb-0" style="width: max-content;">Settings</legend>
								<div class="settings_container"></div>
								<form class="orgsettings_form">
									<div class="row align-items-end">
										<div class="col-sm-6 mb-2">
											<label class="d-block mb-0">
												Set ID <span class="text-danger">*</span>
												<select name="setid" class="form-control form-control-sm shadow-sm mt-1" required>
													${settings.data.map(a => `<option value="${a.setid}">${a.settitle}</option>`).join(``)}
												</select>
											</label>
										</div>
										<div class="col-sm-6 mb-2">
											<label class="d-block mb-0">
												Set Label <span class="text-danger">*</span>
												<input name="setlabel" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Set Label..." required>
											</label>
										</div>
										<div class="col-sm-6 mb-3 mb-sm-2 d-none">
											<label class="d-block mb-0">
												File
												<input name="fileurl" class="form-control form-control-sm shadow-sm mt-1" type="file">
											</label>
										</div>
										<div class="col-sm-12 text-right">
											<button class="btn btn-primary btn-sm px-5 ripple custom_shadow" type="submit" title="Save Organization Settings">Save</button>
										</div>
									</div>
								</form>
							</fieldset>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<script>
		let settingsContainer = $(`.settings_container`);

		// get_orgsettings({
		// 	orgno: value.orgno
		// }, settingsContainer);

		function get_orgsettings(json, target) {
			target.empty();

			$.post(`php/ui/orgsettings/get_orgsettings.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_orgsettings(resp.results, target);
					sessionStorage.setItem(`orgsettings_${json.orgno}`, JSON.stringify(resp.results));
				}
			}, `json`);
		}

		function show_orgsettings(data, target) {
			$.each(data, (index, value) => {
				let template = $(`<div class="position-relative shadow-sm border rounded p-2 mb-2">
						${value.fileurl ? `<img src="${value.fileurl}" style="height:50px;" />` : ``}
						${value.setlabel ? `<div>${value.setlabel}</div>` : ``}
						${value.settitle ? `<div>${value.settitle}</div>` : ``}
						<div class="position-absolute" style="top:0;right:5px;">
							<button class="delete_orgsettings_button btn btn-sm btn-danger ripple rounded-circle custom_shadow m-1" type="button" title="Delete">
								<i class="fas fa-trash"></i>
							</button>
						</div>
					</div>`)
					.appendTo(target);

				(function($) {
					$(`.delete_orgsettings_button`, template).click(function(e) {
						if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

						let json = {
							orgno: value.orgno,
							setid: value.setid
						};

						remove_an_orgsetting(json, target);
					});
				})(jQuery);
			});
		}

		function setup_orgsettings(json, target) {
			if (json.fileurl.size) {

			} else {
				json.fileurl = null;
			}

			$.post(`php/ui/orgsettings/setup_orgsettings.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_orgsettings({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}

		function remove_an_orgsetting(json, target) {
			$.post(`php/ui/orgsettings/remove_an_orgsetting.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_orgsettings({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}
	</script>

</body>

</html>