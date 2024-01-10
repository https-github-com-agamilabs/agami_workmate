<?php
$basePath = dirname(__FILE__);
include_once $basePath . "/php/ui/login/check_session.php";

date_default_timezone_set("Asia/Dhaka");
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

$default_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
$lang = $default_lang;
if (isset($_GET['lang'])) {
	$lang = $_GET['lang'];
	//$_SESSION['lang'] = $lang;
}


include_once($basePath . "/lang_converter/converter.php");
$jasonFilePath =  "$basePath/lang-json/$lang/profile.json";
$arrayData = langConverter($jasonFilePath);
//print_r($arrayData);
//echo $arrayData[$lang]['lang_about_us'];

// echo $jasonFilePath;
// print_r($arrayData);

include_once $basePath . "/configmanager/firebase_configuration.php";
if (!defined("DB_USER")) {
	include_once $basePath . '/php/db/config.php';
}
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/header.php"; ?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/cropperjs/1.4.3/cropper.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cropperjs/1.4.3/cropper.js"></script>

	<script type="text/javascript" src="./js/select_elem_data_load.js"></script>
	<script type="text/javascript" src="./js/basic_crud_type_1.js"></script>

	<style>
		.img-raised {
			box-shadow: 0 5px 15px -8px rgb(0 0 0 / 24%), 0 8px 10px -5px rgb(0 0 0 / 20%);
		}

		.profile_card .profile {
			text-align: center;
		}

		.profile_card .profile img {
			max-width: 180px;
			width: 100%;
			margin: -5.5rem auto 0 auto;
		}

		@media (min-width: 576px) {

			.left_content,
			.right_content {
				position: sticky;
				/* top: 154px; */
				top: 60px;
			}
		}

		.list-group-flush .list-group-item {
			padding: .5rem .75rem;
		}

		.select2-container .select2-selection--single {
			height: 28px !important;
		}

		.select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 28px !important;
		}

		.select2-container--default .select2-selection--single .select2-selection__arrow {
			height: 26px !important;
		}
	</style>

	<link rel="stylesheet" href="./css/page_explainer.css">

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once "navbar.php"; ?>

		<div class="app-main">
			<?php include_once "sidebar.php"; ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-user-cog icon-gradient bg-midnight-bloom  bg-amy-crisp"></i>
								</div>
								<div>
									<?= $arrayData[$lang]['lang_profile']; ?>
									<!-- <div class="page-title-subheading"><?= $arrayData[$lang]['lang_your_doctor_statistics_mentioned_here.']; ?></div> -->
								</div>
								<div class="">
									<div class="help-tip">
										<!-- <p>This is the inline help tip! It can contain all kinds of HTML. Style it as you please.<br />

											<a href="#">Here is a link</a>
										</p> -->
										<p>
											<br>
											<?= $arrayData[$lang]['lang_1._this_page_mainly_describes_your_personal_information_which_information_are_given_by_you_previously.']; ?>
											<br><br>
											<?= $arrayData[$lang]['lang_2._if_you_want_to_change_your_personal_information_please_click_the_edit_profile_button_from_where_you_can_save_your_information_after_necessary_updating.']; ?>

										</p>

									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="pt-5">
						<!-- PROFILE CARD -->
						<div class="card profile_card pt-3 mx-auto mb-3" style="max-width: 40rem;">
							<div class="card-body">
								<div class="profile">
									<div class="avatar">
										<label for="people_profile_pic_input" style="cursor: pointer;">
											<img id="tchrProPic" src="assets/image/profile_pic.png" onerror="this.onerror=null;this.src='assets/image/profile_pic.png';" alt="Profile Picture" class="img-raised rounded img-fluid">
										</label>
										<input type="file" class="sr-only" id="people_profile_pic_input" name="image" accept="image/*">
									</div>
									<div class="people_detail_collapse collapse show">
										<h5 id="people_name" class="font-weight-bold"></h5>
										<div class="h6"><?= $response['orgname']; ?></div>
										<div id="people_primarycontact" class="text-secondary"></div>
										<div id="people_email" class="text-secondary mb-2"></div>

										<button id="people_detail_edit_profile_button" class="btn btn-outline-info btn-sm ripple custom_shadow" type="button">
											<i class="fas fa-edit"></i> <?= $arrayData[$lang]['lang_edit_profile']; ?>
										</button>
									</div>
									<form id="people_detail_update_form" class="people_detail_collapse collapse text-left mb-0">
										<div class="row">
											<div class="col-md-6 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_first_name']; ?> <span class="text-danger">*</span>
													<input name="firstname" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="First Name..." required>
												</label>
											</div>
											<div class="col-md-6 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_last_name']; ?>
													<input name="lastname" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Last Name...">
												</label>
											</div>
											<div class="col-md-12 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_mobile_number']; ?><span class="text-danger">*</span>
													<div class="input-group input-group-sm mt-1">
														<div class="input-group-prepend">
															<span class="input-group-text shadow-sm bg-white border-right-0 pr-1" style="font-size:.875rem;font-weight: 400;line-height: 1.55;padding-bottom: .2rem;">
																+880
															</span>
														</div>
														<input name="primarycontact" class="form-control shadow-sm border-left-0 pl-1" type="tel" placeholder="Mobile number..." required>
													</div>
												</label>
											</div>
											<div class="col-md-12 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_email']; ?>
													<input name="email" class="form-control form-control-sm shadow-sm mt-1" type="email" placeholder="Email...">
												</label>
											</div>
											<!-- <div class="col-md-12 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_date_of_birth']; ?><span class="text-danger">*</span>
													<input name="dob" class="form-control form-control-sm shadow-sm mt-1" type="date" required>
												</label>
											</div>
											<div class="col-md-6 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_gender']; ?> <span class="text-danger">*</span>
													<select name="gender" class="form-control form-control-sm shadow-sm mt-1" required>
														<option value="1">Male</option>
														<option value="2">Female</option>
														<option value="3">Others</option>
													</select>
												</label>
											</div>
											<div class="col-md-6 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_blood_group']; ?>
													<select name="bloodgroup" class="form-control form-control-sm shadow-sm mt-1">
														<option value="">Select...</option>
														<option value="A+">A+</option>
														<option value="A-">A-</option>
														<option value="B+">B+</option>
														<option value="B-">B-</option>
														<option value="AB+">AB+</option>
														<option value="AB-">AB-</option>
														<option value="O+">O+</option>
														<option value="O-">O-</option>
													</select>
												</label>
											</div>
											<div class="col-md-12 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_street']; ?>
													<input name="street" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Street...">
												</label>
											</div>
											<div class="col-md-12 mb-2">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_postcode']; ?>
													<select name="postcode" class="form-control form-control-sm shadow-sm mt-1"></select>
												</label>
											</div>
											<div class="col-md-6 mb-3">
												<label class="d-block mb-0">
													<?= $arrayData[$lang]['lang_country']; ?>
													<input name="country" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Country...">
												</label>
											</div>
											<div class="col-md-6 mb-3">
												<label class="d-block mb-0">

													<?= $arrayData[$lang]['lang_nid_number']; ?>
													<input name="nid" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="NID...">
												</label>
											</div> -->
											<div class="col-12 text-center">
												<button id="people_detail_form_cancel_button" class="btn btn-outline-secondary btn-sm ripple custom_shadow" type="button">
													<i class="fas fa-ban"></i> <?= $arrayData[$lang]['lang_cancel']; ?>
												</button>
												<button class="btn btn-primary btn-sm ripple custom_shadow" type="submit">
													<i class="fas fa-check-double"></i> <?= $arrayData[$lang]['lang_save']; ?>
												</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<!-- <ul class="list-group list-group-flush people_detail_collapse collapse show">
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_gender']; ?></h6>
									<span id="people_gender" class="text-secondary"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_blood_group']; ?></h6>
									<span id="people_bloodgroup" class="text-secondary"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_date_of_birth']; ?></h6>
									<span id="people_dob" class="text-secondary"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_nid_number']; ?></h6>
									<span id="people_nid" class="text-secondary"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_street']; ?></h6>
									<span id="people_street"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_postcode']; ?></h6>
									<span id="people_postcode"></span>
								</li>
								<li class="list-group-item grow border-top d-flex flex-wrap justify-content-between align-items-center">
									<h6 class="mb-0"><?= $arrayData[$lang]['lang_country']; ?></h6>
									<span id="people_country" class="text-secondary"></span>
								</li>
							</ul> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- pic modal - start -->
	<div id="people_profile_pic_modal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Profile Picture</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="img-container">
						<img id="image" height="100%;" width="100%">
					</div>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary ripple custom_shadow" data-dismiss="modal">Cancel</button>
					<button id="crop" type="button" class="btn btn-primary ripple custom_shadow">Crop & Upload</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		var publicAccessUrl = `<?= $publicAccessUrl ?>`;
	</script>

	<script type="text/javascript" src="js/imagecrop.js" defer></script>
	<script type="text/javascript" src="js/people_detail.js" defer></script>

</body>

</html>