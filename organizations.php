<?php
$basePath = dirname(__FILE__);
include_once $basePath . "/php/ui/login/check_session.php";
include_once $basePath . "/configmanager/firebase_configuration.php";

//require 'dependancy_checker.php';

date_default_timezone_set("Asia/Dhaka");
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if (isset($_GET['lang'])) {
	$_SESSION["lang"] = $_GET['lang'];
} else if (!isset($_SESSION["lang"])) {
	$_SESSION["lang"] = "en";
}
$lang = $_SESSION["lang"];

require_once dirname(__FILE__) . "/lang_converter/converter.php";
// $jasonFilePath = $basePath . "/lang-json/$lang/organizations.json";
if (!isset($orgData)) {
	$orgData = array();
}
$orgData = array_merge($orgData, langConverter($lang, 'organizations'));

?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/shared/layout/header.php";	?>

	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src="js/basic_crud_type_1.js"></script>
	<script src="js/select_elem_data_load.js"></script>
</head>

<body>
	<script>
		console.log(<?= json_encode($orgData) ?>);
	</script>

	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once "settings_navbar.php"; ?>

		<div class="app-main">
			<?php include_once "settings_sidebar.php"; ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div id="orgs_card" class="card bg-transparent shadow-none mb-3">
						<div class="card-header justify-content-between shadow-sm">
							<h5 class="font-weight-bold" style="text-transform: initial;"><?= $orgData['lang_your_organizations']; ?></h5>
							<button class="add_button btn btn-primary btn-sm ripple rounded-pill px-3 custom_shadow" type="button">
								<i class="fa fa-plus-circle mr-1"></i> <?= $orgData['lang_add']; ?>
							</button>
						</div>
						<div class="card-body px-0 bg-transparent">
							<div id="orgs_container"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="orgs_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form class="setup_form">
					<div class="modal-header">
						<h5 class="modal-title"><?= $orgData['lang_setup_organization']; ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<fieldset class="custom_fieldset pt-1 pb-0 mb-2">
							<legend class="h5 px-2 mb-0" style="width: max-content;"><?= $orgData['lang_organization_info']; ?></legend>

							<div class="row justify-content-around">
								<div class="col-lg-3 text-center d-flex flex-column justify-content-center">
									<label class="mb-0">
										<img src="assets/store_logo/demo_logo.png" onerror="this.onerror=null;this.src='assets/store_logo/demo_logo.png';" title="Click to add organization logo" class="img-fluid shadow-sm rounded preview_orglogo" style="width: 100px;" alt="Org Logo">
									</label>
								</div>

								<div class="col-lg-9 row">

									<div class="col-lg-12 mb-2">
										<label class="d-block mb-0">
											<?= $orgData['lang_name']; ?> <span class="text-danger">*</span>
											<input name="orgname" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Name..." required>
										</label>
									</div>

									<div class="col-lg-6 mb-2">
										<label class="d-block mb-0">
											<?= $orgData['lang_type']; ?> <span class="text-danger">*</span>
											<select name="orgtypeid" class="form-control form-control-sm shadow-sm mt-1" required></select>
										</label>
									</div>

									<div class="col-lg-6 mb-2">
										<label class="d-block mb-0">
											<?= $orgData['lang_privacy']; ?> <span class="text-danger">*</span>
											<select name="privacy" class="form-control form-control-sm shadow-sm mt-1" required></select>
										</label>
									</div>

									<div class="col-lg-12 mb-2">
										<label class="d-block mb-0">
											<?= $orgData['lang_contact_no']; ?> <span class="text-danger">*</span>
											<input name="contactno" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_contact_no']; ?>..." required>
										</label>
									</div>
								</div>

							</div>
						</fieldset>

						<fieldset class="custom_fieldset pt-1 pb-0 mb-2">
							<legend class="h5 px-2 mb-0" style="width: max-content;"><?= $orgData['lang_office_time']; ?></legend>

							<div class="row">
								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_start_time']; ?>
										<input name="starttime" class="form-control form-control-sm shadow-sm mt-1" type="time">
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_end_time']; ?>
										<input name="endtime" class="form-control form-control-sm shadow-sm mt-1" type="time">
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_weekend_1']; ?>
										<select name="weekend1" class="form-control form-control-sm shadow-sm mt-1">
											<option value=""><?= $orgData['lang_choose_an_option']; ?></option>
											<option value="SAT"><?= $orgData['lang_saturday']; ?></option>
											<option value="SUN"><?= $orgData['lang_sunday']; ?></option>
											<option value="MON"><?= $orgData['lang_monday']; ?></option>
											<option value="TUE"><?= $orgData['lang_tuesday']; ?></option>
											<option value="WED"><?= $orgData['lang_wednesday']; ?></option>
											<option value="THU"><?= $orgData['lang_thursday']; ?></option>
											<option value="FRI"><?= $orgData['lang_friday']; ?></option>
										</select>
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_weekend_2']; ?>
										<select name="weekend2" class="form-control form-control-sm shadow-sm mt-1">
											<option value=""><?= $orgData['lang_choose_an_option']; ?></option>
											<option value="SAT"><?= $orgData['lang_saturday']; ?></option>
											<option value="SUN"><?= $orgData['lang_sunday']; ?></option>
											<option value="MON"><?= $orgData['lang_monday']; ?></option>
											<option value="TUE"><?= $orgData['lang_tuesday']; ?></option>
											<option value="WED"><?= $orgData['lang_wednesday']; ?></option>
											<option value="THU"><?= $orgData['lang_thursday']; ?></option>
											<option value="FRI"><?= $orgData['lang_friday']; ?></option>
										</select>
									</label>
								</div>
							</div>
						</fieldset>

						<fieldset class="custom_fieldset pt-1 pb-0 mb-2">
							<legend class="h5 px-2 mb-0" style="width: max-content;"><?= $orgData['lang_address']; ?></legend>

							<div class="row">
								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_street']; ?>
										<input name="street" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_street']; ?>...">
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_city']; ?>
										<input name="city" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_city']; ?>...">
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_state']; ?>
										<input name="state" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_state']; ?>...">
									</label>
								</div>

								<div class="col-lg-6 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_country']; ?>
										<input name="country" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_country']; ?>...">
									</label>
								</div>

								<div class="col-lg-12">
									<div class="input-group input-group-sm mb-2">
										<input id="googlemapurl" type="text" class="form-control shadow-sm" placeholder="https://www.google.com/maps/place/...">
										<div class="input-group-append">
											<button id="get_lat_lon_button" class="btn btn-secondary custom_shadow" type="button"><?= $orgData['lang_get_lat_&_lon']; ?></button>
										</div>
									</div>
								</div>

								<div class="col-6 pr-1 pr-sm-3 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_latitude']; ?>
										<input name="gpslat" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_latitude']; ?>...">
									</label>
								</div>

								<div class="col-6 pl-1 pl-sm-3 mb-2">
									<label class="d-block mb-0">
										<?= $orgData['lang_longitude']; ?>
										<input name="gpslon" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_longitude']; ?>...">
									</label>
								</div>
							</div>
						</fieldset>

						<label class="d-block mb-0">
							<?= $orgData['lang_organization_note']; ?>
							<textarea name="orgnote" class="form-control form-control-sm shadow-sm mt-1" placeholder="<?= $orgData['lang_organization_note']; ?>..." rows="3"></textarea>
						</label>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary btn-sm ripple rounded-pill px-5 custom_shadow"><?= $orgData['lang_save']; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="common_account_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?= $orgData['lang_common_accounts']; ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="common_accounts_title" class="text-center h5 font-weight-bold"></div>

					<div class="table-responsive rounded shadow-sm">
						<table class="table table-sm table-bordered table-hover table-striped text-center mb-0">
							<thead class="table-primary">
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th><?= $orgData['lang_parent_accno']; ?></th>
									<th><?= $orgData['lang_level']; ?></th>
									<th><?= $orgData['lang_voucher_type']; ?></th>
								</tr>
							</thead>
							<tbody id="common_accounts_tbody"></tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary rounded-pill px-4 ripple custom_shadow" data-dismiss="modal"><?= $orgData['lang_close']; ?></button>
					<button id="confirm_common_accounts_button" type="button" class="btn btn-primary rounded-pill px-4 ripple custom_shadow"><?= $orgData['lang_confirm']; ?></button>
				</div>
			</div>
		</div>
	</div>

	<div id="reopen_accyear_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="reopen_accyear_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Reopen Accounting Year</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<label class="d-block mb-0">
							Closing Date <span class="text-danger">*</span>
							<input name="closingdate" class="form-control shadow-sm mt-2" type="date" placeholder="Closing Date..." required>
						</label>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 ripple custom_shadow">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="bring_forward_accyear_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Bring Forward (B/F) Accounting Year Info</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="get_balancesheet_form" class="row justify-content-center">
						<div class="col-lg-8">
							<div class="input-group input-group-sm">
								<div class="input-group-prepend">
									<span class="input-group-text shadow-sm">From Accounting Year</span>
								</div>
								<select name="bf_accyear" class="form-control shadow-sm"></select>
								<div class="input-group-append">
									<button class="btn btn-primary ripple custom_shadow" type="submit">
										Get Balancesheet
									</button>
								</div>
							</div>
						</div>
					</form>

					<div class="table-responsive shadow-sm rounded my-3">
						<table class="table table-sm table-striped table-hover mb-0">
							<thead class="table-primary">
								<tr>
									<th></th>
									<th>Acc No</th>
									<th>Acc Name</th>
									<th class="text-right">Debit</th>
									<th class="text-right">Credit</th>
								</tr>
							</thead>
							<tbody id="bring_forward_tbody"></tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary rounded-pill px-4 ripple custom_shadow" data-dismiss="modal">Close</button>
					<button id="confirm_bring_forward_button" type="button" class="btn btn-primary rounded-pill px-4 ripple custom_shadow">
						Confirm Bring Forward
					</button>
				</div>
			</div>
		</div>
	</div>

	<?php require "modal_update_photo.php"; ?>

	<script>
		const link = `https://www.google.com/maps/search/?api=1`;
		const DEFAULT_PHOTO = `assets/image/no_image_found.jpg`;
		const USERNO = <?= $userno ?>;

		function padZero(value) {
			return value < 10 ? `0${value}` : `${value}`;
		}

		function formatDateTime(dateTime) {
			const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			let date = new Date(dateTime);
			let hours = date.getHours();
			let minutes = date.getMinutes();
			let ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
			minutes = minutes < 10 ? '0' + minutes : minutes;
			let strTime = hours + ':' + minutes + ' ' + ampm;
			return padZero(date.getDate()) + " " + months[date.getMonth()] + " " + date.getFullYear() + " " + strTime;
		}

		function formatTime(timeString = "00:00:00") {
			if (!timeString || !timeString.length) {
				return ``;
			}

			let H = +timeString.substr(0, 2);
			let h = H % 12 || 12;
			let ampm = (H < 12 || H === 24) ? " AM" : " PM";
			return h + timeString.substr(2, 3) + ampm;
		}

		function differenceOfDays(date) {
			const date1 = new Date();
			const date2 = new Date(date);
			if (date2 - date1 <= 0) {
				return 0;
			}

			const diffTime = Math.abs(date2 - date1);
			return diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
		}

		const orgType = new SelectElemDataLoad({
			readURL: `${publicAccessUrl}php/ui/settings/get_orgtype.php`,
			targets: [{
				selectElem: `#orgs_modal [name="orgtypeid"]`,
				defaultOptionText: `Select...`,
				defaultOptionValue: ``
			}],
			optionText: `orgtypename`,
			optionValue: `orgtypeid`
		});

		const privacy = new SelectElemDataLoad({
			readURL: `${publicAccessUrl}php/ui/settings/get_orgprivacy.php`,
			targets: [{
				selectElem: `#orgs_modal [name="privacy"]`,
				defaultOptionText: `Select...`,
				defaultOptionValue: ``
			}],
			optionText: `privacytext`,
			optionValue: `id`
		});

		const settings = new SelectElemDataLoad({
			readURL: `${publicAccessUrl}php/ui/orgsettings/get_settings.php`
		});

		const commonAccTypes = new SelectElemDataLoad({
			readURL: `${publicAccessUrl}php/ui/organization/get_commontypes.php`
		});

		const modules = new SelectElemDataLoad({
			readURL: `${publicAccessUrl}php/ui/organization/get_modules.php`
		});

		const orgAccS2Settings = (additionalParams = {}, placeholder = `Select account...`) => {
			return {
				placeholder,
				allowClear: true,
				width: `calc(100% - 0px)`,
				ajax: {
					url: `${publicAccessUrl}php/ui/settings/pop_vorgaccounts.php`,
					dataType: "json",
					type: "POST",
					data: function(params) {
						return {
							search_key: params.term,
							pageno: params.page || 1,
							limit: 20,
							...additionalParams
						};
					},
					processResults: function(data, params) {
						params.pageno = params.page || 1;

						$.each(data.results, (index, value) => {
							value.id = value.accno;
							value.text = `[${value.accno}] ${value.accname}`;
						});

						return data;
					},
					cache: false
				}
			}
		};

		function load_org_settings(target) {
			let settingsInterval = setInterval(() => {
				if (settings.data && settings.data.length) {
					target.empty().append(`<option value="">Select...</option>`);

					$.each(settings.data, (index, value) => {
						target.append(`<option value="${value.setid}">${value.settitle}</option>`);
					});

					clearInterval(settingsInterval);
				}
			}, 500);
		}

		function load_common_account_types(target) {
			let commonAccTypesInterval = setInterval(() => {
				if (commonAccTypes.data && commonAccTypes.data.length) {
					target.empty().append(`<option value="">Select...</option>`);

					$.each(commonAccTypes.data, (index, value) => {
						target.append(`<option value="${value.commontypeno}">${value.commontypetitle}</option>`);
					});

					clearInterval(commonAccTypesInterval);
				}
			}, 500);
		}

		function load_modules(target) {
			let modulesInterval = setInterval(() => {
				if (modules.data && modules.data.length) {
					target.empty();

					$.each(modules.data, (index, value) => {
						target.append(`<option value="${value.moduleno}">${value.moduletitle}</option>`);
					});

					clearInterval(modulesInterval);
				}
			}, 500);
		}

		function get_my_valid_packages(target) {
			target.empty();
			let formElem = target.parents(`.tab-pane`).find(`form`);

			$.post(`${publicAccessUrl}php/ui/package/get_my_valid_packages.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					target.hide().siblings(`.invalid-feedback`).show();
					formElem.hide();
				} else {
					target.show().siblings(`.invalid-feedback`).hide();
					formElem.show();
					show_my_valid_packages(resp.results, target);
				}
			}, `json`);
		}

		function show_my_valid_packages(data, target) {
			let packages = [];

			$.each(data, (index, value) => {
				let pack = packages.find(a => a.purchaseno == value.purchaseno);

				if (pack) {
					pack.items = [...pack.items, {
						item: value.item,
						package_qty: value.package_qty,
						used_qty: value.used_qty,
					}];
				} else {
					packages = [...packages, {
						purchaseno: value.purchaseno,
						licensekey: value.licensekey,
						offerno: value.offerno,
						offertitle: value.offertitle,
						items: [{
							item: value.item,
							package_qty: value.package_qty,
							used_qty: value.used_qty,
						}]
					}];
				}
			});

			$.each(packages, (index, value) => {
				let template = $(`<option value="${value.purchaseno}">${value.offertitle} (${value.licensekey})</option>`)
					.data(value)
					.appendTo(target);
			});
		}

		function get_active_accyear(json, target) {
			target.empty();

			let cardHeader = $(`.card-header:first`, target.parents(`.org_card`));
			let classNames = cardHeader.prop(`class`).split(` `).filter(a => a.startsWith(`alert`)).join(` `);

			$.post(`${publicAccessUrl}php/ui/accounting/get_active_accyear.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					if (classNames.length) {
						cardHeader.removeClass(classNames);
					}
					cardHeader.addClass(`alert-danger`);
				} else {
					show_active_accyear(resp.results, target);
				}
			}, `json`);
		}

		function show_active_accyear(data, target) {
			$.each(data, (index, value) => {
				let template = $(`<option value="${value.accyear}">${value.accyear}</option>`)
					.data(value)
					.appendTo(target);
			});
		}

		class Organization extends BasicCRUD {
			show(data) {
				let thisObj = this;

				$.each(data, (index, value) => {
					// ADDRESS
					let address = ``;

					if (value.street && value.street.length) {
						address += value.street;
					}

					if (value.city && value.city.length) {
						if (address.length) {
							address += `, `;
						}
						address += value.city;
					}

					if (value.country && value.country.length) {
						if (address.length) {
							address += `, `;
						}
						address += value.country;
					}

					let contactno = value.contactno;

					// OFFICE TIME
					let officeTime = ``;

					if (value.starttime && value.starttime.length) {
						if (value.endtime && value.endtime.length) {
							officeTime += `Office Time: `;
						} else {
							officeTime += `Office Open: `;
						}

						officeTime += `<b>${formatTime(value.starttime)}</b>`;
					}

					if (value.endtime && value.endtime.length) {
						if (officeTime.length) {
							officeTime += ` - `;
						}
						officeTime += `<b>${formatTime(value.endtime)}</b>`;
					}

					// WEEKEND
					let weekend = ``;

					if (value.weekend1 && value.weekend1.length) {
						if (value.endtime && value.endtime.length) {
							weekend += `Weekend: `;
						}
						weekend += `<b>${value.weekend1}</b>`;
					}

					if (value.weekend2 && value.weekend2.length) {
						if (weekend.length) {
							weekend += `, `;
						}
						weekend += `<b>${value.weekend2}</b>`;
					}

					let validityClass = `alert-success`;

					if (value.verifiedno != 1) {
						validityClass = `alert-danger`;
					} else if (value.pack_validuntil && value.pack_validuntil.length) {
						if (differenceOfDays(value.pack_validuntil) <= 7) {
							validityClass = `alert-warning`;
						}
					}

					let isModule1 = value.modules.find(a => a.moduleno == 1) ? true : false;

					let template = $(`<div class="card org_card mb-3">
							<div class="card-header justify-content-between ${validityClass}">
								<div style="text-transform: initial;">
									<h5 class="font-weight-bold mb-0">${value.orgname}</h5>
									${value.orgtypename && value.orgtypename.length ? `<div class="small">(${value.orgtypename})</div>` : ``}
								</div>
								${value.verifiedno == 1 ? `<div class="input-group input-group-sm mx-auto mr-md-0 mt-2 mt-md-0" style="max-width:346px;">
									<div class="input-group-prepend">
										<span class="input-group-text shadow-sm text-capitalize">With</span>
									</div>
									<select name="accyear" class="form-control shadow-sm"></select>
									<div class="input-group-append">
										<button class="proceed_to_accounting btn btn-primary ripple custom_shadow" type="button" title="Proceed To Accounting">
											<i class="fas fa-sign-in-alt mr-1"></i>
											Proceed <span class="d-none d-sm-inline">To Accounting</span>
										</button>
									</div>
								</div>` : ``}
							</div>
							<div class="card-body">
								<div class="media">
									<img src="${value.picurl || `assets/store_logo/demo_logo.png`}" class="align-self-start img-fluid rounded shadow-sm border mr-3 cursor-pointer preview_orglogo" style="width:100px;" alt="...">

									<div class="media-body position-relative">
										${address.length ? `<div class="d-flex flex-wrap align-items-center font-size-lg">
												<div><i class="fas fa-home mr-2"></i></div>
												<div class="mr-2">${address}</div>
												${value.gpslat && value.gpslon
													? `<a href="${link}&query=${value.gpslat}%2C${value.gpslon}" target="_blank" class="small" title="View organization in map">
														(View Map)
													</a>` : ``}
											</div>` : ``}
										${contactno.length ? `<div  class="d-flex flex-wrap align-items-center font-size-lg">
												<div><i class="fas fa-phone-alt mr-2"></i></div>
												<div>${contactno}</div>
											</div>` : ``}
										${officeTime.length ? `<div>${officeTime}</div>` : ``}
										${weekend.length ? `<div>${weekend}</div>` : ``}
									</div>
									${value.createdby == USERNO || isModule1
										? `<div class="">
											<button class="edit_button btn btn-sm btn-info ripple rounded-circle custom_shadow mr-sm-1 mb-1" type="button" title="Edit">
												<i class="fas fa-edit"></i>
											</button>
											<button class="delete_button btn btn-sm btn-danger ripple rounded-circle custom_shadow mr-sm-1 mb-1" type="button" title="Delete">
												<i class="fas fa-trash"></i>
											</button>
										</div>`
										: ``}
								</div>
								${value.orgnote && value.orgnote.length ? `<div>${value.orgnote}</div>` : ``}
								${value.createdby == USERNO || isModule1
									? `<hr class="my-2">
									<div class="">
										<div class="text-center mb-2">
											<a class="h5" data-toggle="collapse" href="#org_${value.orgno}_controller_collapse" class="collapsed">
												Organization Controller
												<i class="fas fa-angle-down rotate-icon"></i>
											</a>
										</div>
										<div id="org_${value.orgno}_controller_collapse" class="collapse">
											<ul class="nav tabs-animated tabs-animated-shadow">
												<li class="nav-item">
													<a data-toggle="tab" href="#org_${value.orgno}_setting_tabpane" class="nav-link active">
														<span>Settings</span>
													</a>
												</li>
												<li class="nav-item">
													<a data-toggle="tab" href="#org_${value.orgno}_acchead_tabpane" class="nav-link">
														<span>Acc Head Setup</span>
													</a>
												</li>
												<li class="nav-item">
													<a data-toggle="tab" href="#org_${value.orgno}_accyear_tabpane" class="nav-link">
														<span>Accounting Year</span>
													</a>
												</li>
												<li class="nav-item">
													<a data-toggle="tab" href="#org_${value.orgno}_module_tabpane" class="nav-link">
														<span>Users Module</span>
													</a>
												</li>
											</ul>
											<div class="tab-content">
												<div class="tab-pane active" id="org_${value.orgno}_setting_tabpane" role="tabpanel">
													<div class="settings_container"></div>
													<fieldset class="custom_fieldset position-relative pt-1" style="background:azure;">
														<form class="orgsettings_form org_${value.orgno}_collapse mb-0">
															<div class="row">
																<div class="col-sm-6">
																	<label class="d-block mb-0">
																		Set ID <span class="text-danger">*</span>
																		<select name="setid" class="form-control form-control-sm shadow-sm mt-1" required></select>
																	</label>
																</div>
																<div class="col-sm-6">
																	<label class="d-block mb-0">
																		Set Label <span class="text-danger">*</span>
																		<input name="setlabel" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Set Label..." required>
																	</label>
																</div>
															</div>
															<div class="mb-2 d-none">
																<label class="d-block mb-0">
																	File
																	<input name="fileurl" class="form-control form-control-sm shadow-sm mt-1" type="file">
																</label>
															</div>
															<div class="text-right mt-2">
																<button class="btn btn-primary btn-sm px-5 ripple custom_shadow" type="submit" title="Save Organization Settings">Save</button>
															</div>
														</form>
													</fieldset>
												</div>

												<div class="tab-pane" id="org_${value.orgno}_acchead_tabpane" role="tabpanel">
													${value.headcount > 0
														? `<div class="alert-info h5 text-center border border-info shadow-sm rounded-pill py-2">
															Acc Head setup already completed.
															</div>`
														: `<div class="row justify-content-center">
															<div class="col-md-10 col-lg-8">
																<form class="relevant_account_filter_form mb-0">
																	<div class="input-group">
																		<div class="input-group-prepend">
																			<span class="input-group-text shadow-sm">Type</span>
																		</div>
																		<select name="commontypeno" class="form-control shadow-sm" required></select>
																		<div class="input-group-append">
																			<button class="btn btn-secondary ripple custom_shadow" type="submit">
																				Get Relevant Account
																			</button>
																		</div>
																	</div>
																</form>
															</div>
														</div>`}
												</div>

												<div class="tab-pane" id="org_${value.orgno}_accyear_tabpane" role="tabpanel">
													<div class="row">
														<div class="col-md-8">
															<p>Number of active accounting year is controlled by the applied package. </p>
														</div>
														<div class="col-md-4">
															<label class="d-block">
																<div class="d-flex justify-content-between align-items-end">
																	<div>Your Valid Packages</div>
																	<div>
																		<a href="my_packages.php" class="btn btn-primary btn-sm ripple custom_shadow">Buy</a>
																	</div>
																</div>
																<select name="purchaseno" class="form-control form-control-sm shadow-sm mt-2"></select>
																<div class="invalid-feedback">You don't have any valid package. Please buy a new package.</div>
															</label>
														</div>
													</div>

													<div class="accyear_info_container_div">
														<div class="table-responsive rounded shadow-sm">
															<form class="accyear_add_form w-100 mb-0">
																<div class="d-table w-100">
																	<div class="d-table-row">
																		<div class="d-table-cell align-bottom" style="min-width:350px;">
																			<input name="accyear" class="form-control shadow-sm rounded-0" type="text" maxlength="9" placeholder="Accounting Year e.g. <?= date("Y") . "-" . date("Y", strtotime("+1 year")) ?>" required>
																		</div>
																		<div class="d-table-cell" style="width:170px;min-width:170px;">
																			<input name="startdate" class="form-control shadow-sm rounded-0" type="date" required>
																		</div>
																		<div class="d-table-cell" style="width:170px;min-width:170px;">
																			<input name="closingdate" class="form-control shadow-sm rounded-0" type="date" required>
																		</div>
																		<div class="d-table-cell align-middle" style="width:115px;min-width:115px;">
																			<button class="btn btn-primary btn-block btn-lg rounded-0 ripple custom_shadow" type="submit">Add</button>
																		</div>
																	</div>
																</div>
															</form>

															<table class="table table-sm table-bordered table-hover table-striped text-center mb-0">
																<thead class="table-primary">
																	<tr>
																		<th style="width:50px;min-width:50px;">SL</th>
																		<th style="min-width:300px;">Acc Year</th>
																		<th style="width:170px;min-width:170px;">Start Date</th>
																		<th style="width:170px;min-width:170px;">Closing Date</th>
																		<th class="text-center" style="width:115px;min-width:115px;">Action</th>
																	</tr>
																</thead>
																<tbody class="accyear_info_container"></tbody>
															</table>
														</div>
													</div>
												</div>

												<div class="tab-pane" id="org_${value.orgno}_module_tabpane" role="tabpanel">
													<div class="row">
														<div class="col-md-8">
															<p>Number of active user (along with assigned modules) is controlled by the applied package.</p>
														</div>
														<div class="col-md-4">
															<label class="d-block">
																<div class="d-flex justify-content-between align-items-end">
																	<div>Your Valid Packages</div>
																	<div>
																		<a href="my_packages.php" class="btn btn-primary btn-sm ripple custom_shadow">Buy</a>
																	</div>
																</div>
																<select name="purchaseno" class="form-control form-control-sm shadow-sm mt-2"></select>
																<div class="invalid-feedback">You don't have any valid package. Please buy a new package.</div>
															</label>
														</div>
													</div>

													<div class="userorgmodule_info_container_div">
														<div class="table-responsive rounded shadow-sm">
															<form class="userorgmodule_add_form w-100 mb-0">
																<div class="d-table w-100">
																	<div class="d-table-row">
																		<div class="d-table-cell align-bottom" style="min-width:350px;">
																			<input name="username" class="form-control shadow-sm rounded-0" placeholder="Username..." required>
																		</div>
																		<div class="d-table-cell" style="width:200px;min-width:200px;">
																			<select name="moduleno" class="form-control shadow-sm rounded-0" title="Select module" required></select>
																		</div>
																		<div class="d-table-cell align-middle" style="width:240px;min-width:240px;">
																			<button class="btn btn-primary btn-block btn-lg rounded-0 ripple custom_shadow" type="submit">Add</button>
																		</div>
																	</div>
																</div>
															</form>

															<table class="table table-sm table-bordered table-hover table-striped text-center mb-0">
																<thead class="table-primary">
																	<tr>
																		<th style="width:50px;min-width:50px;">SL</th>
																		<th class="text-left" style="min-width:300px;">Name</th>
																		<th style="width:200px;min-width:200px;">Module</th>
																		<th style="width:120px;min-width:120px;">Validity</th>
																		<th style="width:120px;min-width:120px;">Action</th>
																	</tr>
																</thead>
																<tbody class="userorgmodule_info_container"></tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>`
									: ``}
							</div>
							${value.verifiedno == 1 ? `<div class="card-footer p-0">
								<div class="input-group input-group-sm mx-auto mr-md-0" style="max-width:346px;">
									<div class="input-group-prepend">
										<span class="input-group-text shadow-sm text-capitalize">With</span>
									</div>
									<select name="accyear" class="form-control shadow-sm rounded-0"></select>
									<div class="input-group-append">
										<button class="proceed_to_accounting btn btn-primary ripple rounded-0 custom_shadow" type="button" title="Proceed To Accounting">
											<i class="fas fa-sign-in-alt mr-1"></i>
											Proceed <span class="d-none d-sm-inline">To Accounting</span>
										</button>
									</div>
								</div>
							</div>` : ``}
						</div>`)
						.data(value)
						.appendTo(this.targetContainer);

					let setidSelect = $(`.orgsettings_form [name="setid"]`, template);
					let commonAcctypeSelect = $(`.relevant_account_filter_form [name="commontypeno"]`, template);
					let moduleSelect = $(`.userorgmodule_add_form [name="moduleno"]`, template);
					let packageSelect = $(`[name="purchaseno"]`, template);

					let settingsContainer = $(`.settings_container`, template);
					let accyearInfoContainer = $(`.accyear_info_container`, template);
					let userorgmoduleInfoContainer = $(`.userorgmodule_info_container`, template);

					get_active_accyear({
						orgno: value.orgno
					}, $(`select[name="accyear"]`, template));

					(function($) {

						// $(`.preview_orglogo`, template).data(`response`, null);

						if (value.picurl && value.picurl.length) {
							$(`.preview_orglogo`, template)
								// .attr(`src`, value.picurl)
								.data(`response`, {
									fileurl: value.picurl
								});
						}

						// console.log($('.preview_orglogo', template));

						$('.preview_orglogo', template).on('click', function(e) {
							console.log($(this).data(`response`));
							show_image_cropping_modal($(this), {
								title: `Organization logo`,
								target_dir: `files/orglogo/`,
								preview_target: $(this),
								prev_fileurl: $(this).data(`response`)
							}, function(upload_response) {
								console.log(upload_response);

								let json = {};
								let tablePK = Number(value[thisObj.tablePK]) || 0;
								let url = thisObj.updateURL;

								console.log(thisObj, url);
								if (tablePK > 0) {
									json[thisObj.tablePK] = tablePK;
								}

								if (!upload_response?.fileurl.length) {
									return;
								}

								json["picurl"] = upload_response.fileurl;

								$.post(url, json, resp => thisObj.successCallback(resp), "json");
							});
						});

						// console.log($(`.preview_orglogo`, template).data(`response`));

						$(`.edit_button`, template).click((e) => {

							if (value.picurl && value.picurl.length) {
								$(`.preview_orglogo`, thisObj.setupForm)
									.attr(`src`, value.picurl)
									.data(`response`, {
										fileurl: value.picurl
									});
							}

							thisObj.setupModal.modal(`show`).find(`.modal-title`).html(`Update ${thisObj.topic}`);
							thisObj.setupForm.trigger("reset").data(thisObj.tablePK, value[thisObj.tablePK]).data(`action`, thisObj.updateURL);

							$(`[name]`, thisObj.setupForm).each((i, elem) => {
								let elementName = $(elem).attr("name");
								if (value[elementName] != null && elementName != `picurl`) {
									$(elem).val(value[elementName]);
								}
							});
						});

						thisObj.deleteButtonTrigger(template, value);

						$(`[href="#org_${value.orgno}_controller_collapse"]`, template).click(function(e) {
							if (!$(this).data(`is_loaded`)) {
								load_org_settings(setidSelect);
								load_common_account_types(commonAcctypeSelect);
								load_modules(moduleSelect);
								get_my_valid_packages(packageSelect);

								get_orgsettings({
									orgno: value.orgno
								}, settingsContainer);

								get_org_accountingyear({
									orgno: value.orgno
								}, accyearInfoContainer);

								get_userorgmodule_info({
									orgno: value.orgno
								}, userorgmoduleInfoContainer);
								$(this).data(`is_loaded`, true);
							}
						});

						$(`.relevant_account_filter_form`, template).submit(function(e) {
							e.preventDefault();
							$(`#common_account_modal`).data(`orgno`, value.orgno).data(`commontypeno`, commonAcctypeSelect.val());
							get_commonaccounts();
						});

						$(`.accyear_add_form`, template).submit(function(e) {
							e.preventDefault();

							let packageSelect = $(`#org_${value.orgno}_accyear_tabpane [name="purchaseno"]`);
							let purchaseno = packageSelect.val();
							let aPackage = $(`option:selected`, packageSelect).data();
							let item = aPackage.items.find(a => a.item == `ACCYEAR`);

							if (item.package_qty == item.used_qty) {
								toastr.error(`Your package has already been used up. Please select a different package to add accounting year.`);
								return;
							}

							if (!confirm(`You are going to add an accounting year. It will use up your available quota which you can not alter. Are you sure to proceed?`)) return;

							$(this).data(`orgno`, value.orgno).data(`purchaseno`, purchaseno);
							insert_accyear_of_an_org($(this), accyearInfoContainer);
						});

						$(`.userorgmodule_add_form`, template).submit(function(e) {
							e.preventDefault();

							let packageSelect = $(`#org_${value.orgno}_module_tabpane [name="purchaseno"]`);
							let purchaseno = packageSelect.val();
							let aPackage = $(`option:selected`, packageSelect).data();
							let item = aPackage.items.find(a => a.item == `ORGUSER`);

							if (item.package_qty == item.used_qty) {
								toastr.error(`Your package has already been used up. Please select a different package to add user.`);
								return;
							}

							if (!confirm(`You are going to add an user. It will use up your available quota which you can not alter. Are you sure to proceed?`)) return;

							$(this).data(`orgno`, value.orgno).data(`purchaseno`, purchaseno);
							add_userorgmodule($(this), userorgmoduleInfoContainer);
						});

						$(`.proceed_to_accounting`, template).click(function(e) {
							let json = {
								orgno: value.orgno,
								accyear: $(this).parents(`.input-group`).find(`select[name="accyear"]`).val()
							};

							$.post(`php/ui/organization/start_org_accounting.php`, json, resp => {
								if (resp.error) {
									toastr.error(resp.message);
								} else {
									location.href = resp.redirecturl;
								}
							}, `json`);
						});

						$(`.orgsettings_form`, template).submit((e) => {
							e.preventDefault();
							let json = Object.fromEntries((new FormData(e.target)).entries());
							json.orgno = value.orgno;
							setup_orgsettings(json, settingsContainer);
						});
					})(jQuery);
				});
			}

			setupFormSubmitTrigger() {
				this.setupForm.submit((e) => {
					e.preventDefault();
					let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());

					let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
					let url = this.setupForm.data(`action`);

					if (tablePK > 0) {
						json[this.tablePK] = tablePK;
					}

					delete json.picurl;

					$.post(url, json, resp => this.successCallback(resp), "json");
				});
			}
		}

		const organization = new Organization({
			readURL: `${publicAccessUrl}php/ui/organization/get_my_org.php`,
			createURL: `${publicAccessUrl}php/ui/organization/setup_organization.php`,
			updateURL: `${publicAccessUrl}php/ui/organization/setup_organization.php`,
			deleteURL: `${publicAccessUrl}php/ui/organization/remove_organization.php`,
			targetCard: `#orgs_card`,
			targetContainer: `#orgs_container`,
			setupModal: `#orgs_modal`,
			topic: `Organization`,
			tablePK: `orgno`
		});

		organization.get();

		$(`#get_lat_lon_button`).click(() => get_lat_lon());

		// $(`#googlemapurl`).on('paste', function() {
		//     get_lat_lon();
		// });

		function get_lat_lon() {
			let url = $(`#googlemapurl`).val();

			let splitUrl = url.split('!3d');
			let latLong = splitUrl[splitUrl.length - 1].split('!4d');
			let latitude = parseFloat(latLong[0]),
				longitude;

			if (latLong.indexOf('?') !== -1) {
				longitude = latLong[1].split('\\?')[0];
			} else {
				longitude = latLong[1];
			}

			longitude = parseFloat(longitude);

			$(`#orgs_modal [name=gpslat]`).val(latitude);
			$(`#orgs_modal [name=gpslon]`).val(longitude);
		}

		// ORG SETTINGS

		function get_orgsettings(json, target) {
			target.empty();

			$.post(`${publicAccessUrl}php/ui/orgsettings/get_orgsettings.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_orgsettings(resp.results, target);
					sessionStorage.setItem(`orgsettings_${json.orgno}`, JSON.stringify(resp.results));
				}
			}, `json`);
		}

		function show_orgsettings(data, target) {
			let setidSelect = target.data(`orgsettings`, data).siblings(`fieldset`).find(`.orgsettings_form [name="setid"]`);
			$(`option:hidden`, setidSelect).show();

			$.each(data, (index, value) => {
				$(`option[value="${value.setid}"]`, setidSelect).hide();

				let template = $(`<div class="position-relative shadow-sm border rounded p-2 mb-2">
						${value.fileurl ? `<img src="${value.fileurl}" style="height:50px;" />` : ``}
						<div>
							${value.settitle || ``}:
							<span class="edit_toggle">${value.setlabel || ``}</span>
							<span class="d-inline mt-1">
								<input name="setlabel" class="form-control form-control-sm shadow-sm edit_toggle" style="max-width:300px;display: none;" value="${value.setlabel || ``}" />
							</span>
						</div>
						<div class="position-absolute" style="top:0;right:5px;">
							<button class="edit_orgsettings_button btn btn-sm btn-info rounded-circle custom_shadow p-1 m-1" type="button" title="Update">
								<i class="fas fa-edit"></i>
							</button>
							<button class="cancel_orgsettings_button btn btn-sm btn-secondary custom_shadow m-1" style="display: none;" type="button" title="Update">
								Cancel
							</button>
							<button class="save_orgsettings_button btn btn-sm btn-success custom_shadow m-1" style="display: none;" type="button" title="Update">
								Save
							</button>
						</div>
					</div>`)
					.appendTo(target);

				(function($) {
					// $(`.delete_orgsettings_button`, template).click(function(e) {
					// 	if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

					// 	let json = {
					// 		orgno: value.orgno,
					// 		setid: value.setid
					// 	};

					// 	remove_an_orgsetting(json, target);
					// });

					$(`.edit_orgsettings_button`, template).click(function(e) {
						$(`.edit_toggle, button`, template).toggle();
					});

					$(`.cancel_orgsettings_button`, template).click(function(e) {
						$(`.edit_toggle, button`, template).toggle();
					});

					$(`.save_orgsettings_button`, template).click(function(e) {
						let json = {
							orgno: value.orgno,
							setid: value.setid,
							setlabel: $(`[name="setlabel"]`, template).val()
						};

						if (json.setlabel == value.setlabel) {
							toastr.error(`Set Label haven't change.`);
							return;
						}

						setup_orgsettings(json, target);
					});
				})(jQuery);
			});
		}

		function setup_orgsettings(json, target) {
			let fileurlElem = target.siblings(`fieldset`).find(`.orgsettings_form [name="fileurl"]`);

			if (json.fileurl && json.fileurl.size) {
				json.fileurl = fileurlElem.data(`response`).fileurl;
			} else {
				json.fileurl = null;
			}

			$.post(`${publicAccessUrl}php/ui/orgsettings/setup_orgsettings.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					fileurlElem.data(`response`, null);
					get_orgsettings({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}

		function remove_an_orgsetting(json, target) {
			$.post(`${publicAccessUrl}php/ui/orgsettings/remove_an_orgsetting.php`, json, resp => {
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

		// COMMON ACCOUNTS

		function get_commonaccounts() {
			$(`#common_accounts_tbody`).empty();

			let json = {
				commontypeno: $(`#common_account_modal`).data(`commontypeno`)
			}

			$.post(`${publicAccessUrl}php/ui/organization/get_commonaccounts.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_commonaccounts(resp.data);
				}
			}, `json`);
		}

		function show_commonaccounts(data) {
			$(`#common_account_modal`).modal(`show`);
			let target = $(`#common_accounts_tbody`);

			$.each(data, (index, value) => {
				let template = $(`<tr>
						<td>${1 + index}</td>
						<td>${value.accno}</td>
						<td>${value.accname}</td>
						<td>${value.praccno}</td>
						<td>${value.levelno}</td>
						<td>
							${value.vtype == 1
								? `<span class="badge badge-success">Yes</span>`
								: `<span class="badge badge-danger">No</span>`}
						</td>
					</tr>`)
					.appendTo(target);
			});
		}

		$(`#confirm_common_accounts_button`).click(function(e) {
			let modal = $(`#common_account_modal`);

			let json = {
				orgno: modal.data(`orgno`),
				commontypeno: modal.data(`commontypeno`),
			};

			$.post(`${publicAccessUrl}php/ui/organization/init_orgaccounthead.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					modal.modal(`hide`);
					organization.get();
				}
			}, `json`);
		});

		// ORG ACC YEAR

		function get_org_accountingyear(json, target) {
			target.empty();

			$.post(`php/ui/organization/get_org_accountingyear.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_orgaccyear_info(resp.results, target, json.orgno);
					//sessionStorage.setItem(`orgsettings_${json.orgno}`, JSON.stringify(resp.results));
				}
			}, `json`);
		}

		function show_orgaccyear_info(data, target, orgno) {
			$.each(data, (index, value) => {
				let template = $(`<tr class="${value.accyearstatus == 1 ? `table-success` : `table-secondary`}">
						<td>${1 + index}</td>
						<td>${value.accyear || ``}</td>
						<td>${value.startdate || ``}</td>
						<td>${value.closingdate || ``}</td>
						<td class="text-center">
							${value.init_transno == null
								? `<button class="bring_forward_button btn btn-sm btn-alternate ripple custom_shadow" type="button" title="Bring forward accounting year info" data-toggle="tooltip" data-placement="top">
									B/F
								</button>`
								: ``}
							${value.accyearstatus == 1
								? `<button class="close_accyear_button btn btn-sm btn-danger ripple custom_shadow" type="button" title="Close accounting year" data-toggle="tooltip" data-placement="top">
									Close
								</button>`
								: `<button class="reopen_accyear_button btn btn-sm btn-success ripple custom_shadow" type="button" title="Reopen accounting year" data-toggle="tooltip" data-placement="top">
									Reopen
								</button>`}
							<button class="delete_accyear_button btn btn-sm btn-danger ripple custom_shadow d-none" type="button" title="Delete">
								<i class="fas fa-trash"></i>
							</button>
						</td>
					</tr>`)
					.appendTo(target);

				(function($) {
					$(`.bring_forward_button`, template).click(function(e) {
						let modal = $(`#bring_forward_accyear_modal`);
						let select = $(`[name="bf_accyear"]`, modal).empty();

						$.each(data, (indexInData, valueOfData) => {
							if (valueOfData.accyear != value.accyear && valueOfData.accyearstatus == 0) {
								$(`<option value="${valueOfData.accyear}">${valueOfData.accyear}</option>`)
									.appendTo(select);
							}
						});

						if ($(`option`, select).length) {
							modal.modal(`show`);
						} else {
							toastr.error(`No closed accounting year found.`);
							return;
						}

						let orgsettings = $(`.settings_container`, target.parents(`.org_card`)).data(`orgsettings`);
						let orgsetting = orgsettings.find(a => a.setid == `ACCL`);
						if (orgsetting) {
							$(`#get_balancesheet_form`).data(`maxlevel`, orgsetting.setlabel);
						}

						$(`#confirm_bring_forward_button`)
							.data({
								orgno,
								for_accyear: value.accyear
							});
					});

					$(`.close_accyear_button`, template).click(function(e) {
						if (!confirm(`Your are going to close "${value.accyear}" accounting year. Are you sure to proceed?`)) return;

						let json = {
							orgno,
							accyear: value.accyear
						};

						close_accounting_year(json, target);
					});

					$(`.reopen_accyear_button`, template).click(function(e) {
						let modal = $(`#reopen_accyear_modal`).modal(`show`);
						$(`form`, modal)
							.trigger(`reset`)
							.data({
								orgno,
								accyear: value.accyear,
								target
							});
					});
				})(jQuery);
			});
		}

		function insert_accyear_of_an_org(form, target) {
			let json = Object.fromEntries((new FormData(form[0])).entries());
			json.orgno = Number(form.data(`orgno`)) || -1;
			json.purchaseno = Number(form.data(`purchaseno`)) || -1;

			if (json.orgno <= 0) {
				toastr.error(`Select an organization.`);
				return;
			}

			if (json.purchaseno <= 0) {
				toastr.error(`Select a package.`);
				return;
			}

			$.post(`${publicAccessUrl}php/ui/organization/insert_accyear_of_an_org.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					form.trigger(`reset`);
					get_my_valid_packages($(`[name="purchaseno"]`));
					get_org_accountingyear({
						orgno: json.orgno
					}, target);
					get_active_accyear({
						orgno: json.orgno
					}, $(`select[name="accyear"]`, target.parents(`.org_card`)));
				}
			}, `json`);
		}

		function close_accounting_year(json, target) {
			$.post(`php/ui/accounting/close_accounting_year_only.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_org_accountingyear({
						orgno: json.orgno
					}, target);
					get_active_accyear({
						orgno: json.orgno
					}, $(`select[name="accyear"]`, target.parents(`.org_card`)));
				}
			}, `json`);
		}

		$(`#reopen_accyear_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			json.orgno = $(this).data(`orgno`);
			json.accyear = $(this).data(`accyear`);

			let target = $(this).data(`target`);

			$.post(`php/ui/accounting/reopen_accounting_year_only.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(`#reopen_accyear_modal`).modal(`hide`);
					get_org_accountingyear({
						orgno: json.orgno
					}, target);
					get_active_accyear({
						orgno: json.orgno
					}, $(`select[name="accyear"]`, target.parents(`.org_card`)));
				}
			}, `json`);
		});

		$(`#get_balancesheet_form`).submit(function(e) {
			e.preventDefault();

			$(`#bring_forward_tbody`).empty();

			let json = Object.fromEntries((new FormData(this)).entries());
			json.maxlevel = $(this).data(`maxlevel`);
			json.levelno = json.maxlevel;
			json.accountingyear = json.bf_accyear;

			delete json.bf_accyear;

			$.post(`php/ui/report/get_balancesheet.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_balancesheet(resp.data);
				}
			}, `json`);
		});

		function show_balancesheet(data) {
			let tbody = $(`#bring_forward_tbody`);

			$(`<tr class="table-secondary h6">
					<th>Assets</th>
					<th colspan="4"></th>
				</tr>`)
				.appendTo(tbody);

			let totalDebit = 0;
			let totalCredit = 0;

			$.each(data.asset.accounts, (index, account) => {
				totalDebit += Number(account.totaldebit);
				totalCredit += Number(account.totalcredit);

				let row = $(`<tr>
						<td></td>
						<td>${account.accno}</td>
						<td>${account.accname}</td>
						<td class="text-right">
							${account.opat == `DR`
								? Number(account.totaldebit).toFixed(2)
								: ``}
						</td>
						<td class="text-right">
							${account.opat == `CR`
								? Number(account.totalcredit).toFixed(2)
								: ``}
						</td>
					</tr>`)
					.appendTo(tbody);
			});

			$(`<tr class="table-secondary h6">
					<th colspan="5">Liabilities and Owners equity</th>
				</tr>
				<tr> <th colspan="5">Liabilities:</th> </tr>`)
				.appendTo(tbody);

			$.each(data.liability.accounts, (index, account) => {
				totalDebit += Number(account.totaldebit);
				totalCredit += Number(account.totalcredit);

				let row = $(`<tr>
						<td></td>
						<td>${account.accno}</td>
						<td>${account.accname}</td>
						<td class="text-right">
							${account.opat == `DR`
								? Number(account.totaldebit).toFixed(2)
								: ``}
						</td>
						<td class="text-right">
							${account.opat == `CR`
								? Number(account.totalcredit).toFixed(2)
								: ``}
						</td>
					</tr>`)
					.appendTo(tbody);
			});

			$(`<tr> <th colspan="5">B/F Equity for Returned Earning:</th> </tr>`)
				.appendTo(tbody);

			$.each(data.equity.accounts, (index, account) => {
				totalDebit += Number(account.totaldebit);
				totalCredit += Number(account.totalcredit);

				let row = $(`<tr>
						<td colspan="2" style="width:205px;">
							<select name="profitloss_accno" class="form-control form-control-sm shadow-sm"></select>
						</td>
						<td>${account.accname}</td>
						<td class="text-right">
							${account.totaldebit != 0
								? Number(account.totaldebit).toFixed(2)
								: ``}
						</td>
						<td class="text-right">
							${account.totalcredit != 0
								? Number(account.totalcredit).toFixed(2)
								: ``}
						</td>
					</tr>`)
					.appendTo(tbody);

				$(`[name="profitloss_accno"]`, row)
					.select2(orgAccS2Settings({
						acctypeno: 2000
					}, `Select Equity Head...`));
			});

			$(`<tr class="table-info h6 text-right">
					<th colspan="3" class="text-center">Total</th>
					<th>${totalDebit.toFixed(2)}</th>
					<th>${totalCredit.toFixed(2)}</th>
				</tr>`)
				.appendTo(tbody);
		}

		$(`#confirm_bring_forward_button`).click(function(e) {
			let json = {
				for_accyear: $(this).data(`for_accyear`),
				bf_accyear: $(`#bring_forward_accyear_modal [name="bf_accyear"]`).val(),
				orgno: $(this).data(`orgno`),
				profitloss_accno: $(`#bring_forward_accyear_modal [name="profitloss_accno"]`).val()
			};

			$.post(`php/ui/accounting/bringforward_accounting.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
				}
			}, `json`);
		});

		// USER ORG MODULE

		function get_userorgmodule_info(json, target) {
			target.empty();

			$.post(`php/ui/organization/get_userorgmodules.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_userorgmodule_info(resp.results, target);

					//sessionStorage.setItem(`orgusermodule_${json.orgno}`, JSON.stringify(resp.results));
				}
			}, `json`);
		}

		function show_userorgmodule_info(data, target) {
			$.each(data, (index, value) => {
				let template = $(`<tr class="${value.verified == 1 ? `table-success` : `table-danger`}">
						<td>${1 + index}</td>
						<td class="text-left">
							${value.firstname || ``}
							${value.lastname || ``}
							(${value.userno != USERNO ? value.username : `you`})
						</td>
						<td class="p-0">
							<div class="module_div">${value.moduletitle || ``}</div>
							<div class="module_div" style="display:none;">
								<select name="moduleno" class="form-control shadow-sm rounded-0" title="Select module" required></select>
							</div>
						</td>
						<td class="text-center">
							${value.userno != USERNO
								? `<button class="toggle_userorgmodule_button btn btn-sm ${value.verified == 1 ? `btn-danger` : `btn-success`} ripple custom_shadow" type="button" title="${value.verified == 1 ? `Deactivate` : `Activate`} user">
									${value.verified == 1 ? `Deactivate` : `Activate`}
								</button>`
								: ``}
						</td>
						<td class="text-center">
							${value.userno != USERNO
								? `<button class="edit_userorgmodule_button btn btn-sm btn-info custom_shadow" type="button" title="Update module">
									Edit
								</button>
								<button class="cancel_userorgmodule_button btn btn-sm btn-secondary custom_shadow" style="display:none;" type="button" title="Cancel">
									Cancel
								</button>
								<button class="save_userorgmodule_button btn btn-sm btn-primary custom_shadow" style="display:none;" type="button" title="Save module">
									Save
								</button>`
								: ``}
						</td>
					</tr>`)
					.appendTo(target);

				let moduleSelect = $(`[name="moduleno"]`, template);
				load_modules(moduleSelect);

				(function($) {
					$(`.toggle_userorgmodule_button`, template).click(function(e) {
						let json = {
							orgno: value.orgno,
							userno: value.userno,
							moduleno: value.moduleno,
						};

						toggle_userorgmodule_activation(json, target);
					});

					$(`.edit_userorgmodule_button`, template).click(function(e) {
						$(`.module_div, .edit_userorgmodule_button, .save_userorgmodule_button, .cancel_userorgmodule_button`, template).toggle();

						moduleSelect.val(value.moduleno);
					});

					$(`.cancel_userorgmodule_button`, template).click(function(e) {
						$(`.module_div, .edit_userorgmodule_button, .save_userorgmodule_button, .cancel_userorgmodule_button`, template).toggle();
					});

					$(`.save_userorgmodule_button`, template).click(function(e) {
						let toggleElem = $(`.module_div, .edit_userorgmodule_button, .save_userorgmodule_button, .cancel_userorgmodule_button`, template);

						if (moduleSelect.val() != value.moduleno) {
							update_userorgmodule({
								orgno: value.orgno,
								foruserno: value.userno,
								old_moduleno: value.moduleno,
								new_moduleno: moduleSelect.val()
							}, target);
						} else {
							toastr.error(`You haven't change the module.`);
						}
					});
				})(jQuery);
			});
		}

		function add_userorgmodule(form, target) {
			let json = Object.fromEntries((new FormData(form[0])).entries());
			json.orgno = Number(form.data(`orgno`)) || -1;
			json.purchaseno = Number(form.data(`purchaseno`)) || -1;

			if (json.orgno <= 0) {
				toastr.error(`Select an organization.`);
				return;
			}

			if (json.purchaseno <= 0) {
				toastr.error(`Select a package.`);
				return;
			}

			$.post(`${publicAccessUrl}php/ui/organization/add_userorgmodule.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					form.trigger(`reset`);
					get_my_valid_packages($(`[name="purchaseno"]`));
					get_userorgmodule_info({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}

		function update_userorgmodule(json, target) {
			$.post(`${publicAccessUrl}php/ui/organization/update_userorgmodule.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_userorgmodule_info({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}

		function toggle_userorgmodule_activation(json, target) {
			$.post(`${publicAccessUrl}php/ui/organization/toggle_userorgmodule_activation.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_userorgmodule_info({
						orgno: json.orgno
					}, target);
				}
			}, `json`);
		}

		$(document).on(`change`, `[name="fileurl"]`, function(e) {
			show_image_cropping_modal($(this), {
				title: `Settings related file`
			});
		});

		function show_image_cropping_modal(target, data, callback) {
			console.log(target, data);
			const image = $("#photo_preview_image")[0];
			if (image.cropper != undefined) {
				image.cropper.destroy();
			}
			$('#photo-update-hint').html(data.title);
			$("#crop_image_button")
				.data(`target`, target)
				.data(`upload_data`, data);

			$("#crop_image_button").unbind('on_image_uploaded');
			$("#crop_image_button").on('on_image_uploaded', function(e, upload_response) {
				console.log("received upload completed");
				console.log("upload_response", upload_response);

				if (callback) {
					callback(upload_response);
				}
			});

			if (data.prev_fileurl && data.prev_fileurl.fileurl) {
				$('#photo_preview_previous_image').attr('src', data.prev_fileurl.fileurl);
			} else {
				$('#photo_preview_previous_image').attr('src', DEFAULT_PHOTO);
			}
			$('#photo_preview_image').attr('src', DEFAULT_PHOTO);
			$('#image_cropping_modal').modal('show');
		}
	</script>

</body>

</html>