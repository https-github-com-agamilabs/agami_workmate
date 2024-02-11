<?php
include_once "check_user_org_session_setting.php";

$basePath = dirname(__FILE__);
include_once $basePath . "/php/ui/login/check_session.php";

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

	<!-- ORG SETUP MODAL -->
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
											<?= $orgData['lang_contact_no']; ?> <span class="text-danger">*</span>
											<input name="primarycontact" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="<?= $orgData['lang_contact_no']; ?>..." required>
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

	<!-- USER ORG SETUP MODAL -->
	<div id="userorg_setup_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form id="userorg_setup_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup User Organization</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 form-group">
								<label class="d-block mb-0">
									User <span class="text-danger">*</span>
									<select name="foruserno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Unique user ID
									<input name="uuid" class="form-control shadow-sm mt-2" type="text" placeholder="Unique user ID...">
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Role in the company
									<select name="ucatno" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Is there any supervisor
									<select name="supervisor" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Which module is assigned
									<select name="moduleno" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Position/designation at company
									<input name="designation" class="form-control shadow-sm mt-2" type="text" placeholder="Position/designation at company...">
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									Hourly rate
									<input name="hourlyrate" class="form-control shadow-sm mt-2" type="number" step="0.01" placeholder="Hourly rate...">
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									Monthly salary
									<input name="monthlysalary" class="form-control shadow-sm mt-2" type="number" step="0.01" placeholder="Monthly salary...">
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Daily work load (Hr)
									<input name="dailyworkinghour" class="form-control shadow-sm mt-2" type="number" step="0.01" placeholder="Daily work load (Hr)...">
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Time flexibility
									<select name="timeflexibility" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									What is his level of permission?
									<select name="permissionlevel" class="form-control shadow-sm mt-2">
										<option value="">Select...</option>
										<option value="0">Employee</option>
										<option value="1">Senior Employee</option>
										<option value="3">Manager</option>
										<option value="7">Admin</option>
									</select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Time zone
									<select name="timezone" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Shift
									<select name="shiftno" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									From
									<input name="starttime" class="form-control shadow-sm mt-2" type="time">
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									To
									<input name="endtime" class="form-control shadow-sm mt-2" type="time">
								</label>
							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-5 ripple custom_shadow">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="userorg_workinglocation_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document" style="max-width: 85% !important;;">
			<div class="modal-content">
				<form id="userorg_workinglocation_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Restrict User Working Location</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body table-responsive">

						<table class="table table-sm table-bordered" id="table_working_location">
							<thead>
								<tr>
									<th>Location</th>
									<th>Radius</th>
									<th>From</th>
									<th>To</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
							<tfoot>
								<tr class="text-center">
									<th colspan="5">
										Restrict Working Location
									</th>
								</tr>
								<tr>
									<th>Location</th>
									<th>Radius</th>
									<th>From</th>
									<th>To</th>
									<th>Action</th>
								</tr>
								<tr>
									<th>
										<select name="locno" class="form-control shadow-sm mt-2" required></select>
									</th>
									<th>
										<select name="mindistance" class="form-control shadow-sm mt-2">
											<option value="10">10 Meters</option>
											<option value="25">25 Meters</option>
											<option value="50">50 Meters</option>
											<option value="100">100 Meters</option>
											<option value="250">250 Meters</option>
										</select>
									</th>
									<th>
										<input name="starttime" class="form-control shadow-sm mt-2" type="datetime-local">

									</th>
									<th>
										<input name="endtime" class="form-control shadow-sm mt-2" type="datetime-local">


									</th>
									<th>
										<button class="btn btn-sm btn-success px-2">Add</button>
									</th>
								</tr>
							</tfoot>
						</table>

						<div class="row">
							<!-- <div class="col-lg-12 form-group">
								<label class="d-block mb-0">
									User <span class="text-danger">*</span>
									<select name="userno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div> -->

							<!-- <div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Working Location
									<select name="locno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-lg-6 form-group">
								<label class="d-block mb-0">
									Radius (Distance from Location)
									<select name="mindistance" class="form-control shadow-sm mt-2">
										<option value="10">10 Meters</option>
										<option value="25">25 Meters</option>
										<option value="50">50 Meters</option>
										<option value="100">100 Meters</option>
										<option value="250">250 Meters</option>
									</select>
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									Start time
									<input name="starttime" class="form-control shadow-sm mt-2" type="time">
								</label>
							</div>

							<div class="col-lg-3 form-group">
								<label class="d-block mb-0">
									End time
									<input name="endtime" class="form-control shadow-sm mt-2" type="time">
								</label>
							</div> -->
						</div>
					</div>
					<div class="modal-footer py-2">
						<!-- <button type="submit" class="btn btn-primary rounded-pill px-5 ripple custom_shadow">Save</button> -->
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php require "modal_update_photo.php"; ?>

	<script>
		const link = `https://www.google.com/maps/search/?api=1`;
		const DEFAULT_PHOTO = `assets/image/no_image_found.jpg`;
		const USERNO = <?= $userno ?>;
	</script>

	<script src="js/organizations.js" defer></script>

</body>

</html>