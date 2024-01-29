<?php
$basePath = dirname(dirname(__FILE__));
include_once $basePath . "/php/ui/login/check_session.php";
include_once $basePath . "/configmanager/firebase_configuration.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/shared/layout/header.php"; ?>

	<link href="../vendor/cropper/cropper.min.css" rel="stylesheet">
	<script src="../vendor/cropper/cropper.min.js"></script>

	<style media="screen">
		.imageeditdiv {
			position: absolute;
			bottom: 20px;
			right: 30px;
			opacity: .8;
			padding-left: 10px;
			padding-right: 10px;
			background-color: #ccc;
			padding-top: 5px;
			/* cursor: pointer; */
		}

		.imagecrossdiv {
			position: absolute;
			top: 20px;
			right: 30px;
			opacity: .8;
			background-color: white;
			cursor: pointer;
			height: 25px;
			display: none;
		}

		.imagecrossdiv:hover {
			-webkit-box-shadow: 2px 2px 20px 8px rgba(241, 243, 244, 0.83);
			box-shadow: 2px 2px 20px 8px rgba(241, 243, 244, 0.83);
		}

		.imageeditdiv:hover {
			opacity: 1;
		}

		.card-title {
			text-align: center;
		}

		.boxsizingBorder {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}

		.galleryContainer {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			justify-content: center;
		}

		.gallery_modal {
			padding: 0px !important;
		}

		.gallery_modal_dialog {
			max-width: 60%;
			margin-top: 0px;
		}

		@media only screen and (max-width: 500px) {
			.pagecontent {
				padding-left: 10px !important;
				padding-right: 10px !important;
			}
		}

		@media only screen and (max-width: 768px) {
			.gallery_modal_dialog {
				max-width: 100%;
			}
		}
	</style>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div style="display:flex;justify-content:space-between;flex-wrap:wrap">
						<div class="h4">
							Gallery
						</div>
						<div>
							<button class="btn btn-success ripple custom_shadow" type="button" name="button" id="image_gallery_add" data-clicked='0'>New</button>
						</div>
					</div>

					<div class="alert alert-warning rounded-pill font-weight-bold text-center py-2">
						Click the 'New' button to add gallery here
					</div>

					<div class='row' style='margin:10px 0px 10px -10px'>
						<!-- LIMIT -->
						<div class="col-sm-6 col-md-4">
							<div class="input-group mb-3 input-group-md">
								<div class="input-group-prepend">
									<span class="input-group-text">Limit</span>
								</div>
								<select id='imagegallery_filter_limit_select' type="text" class="form-control shadow-sm">
									<option value="25">25</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
							</div>
						</div>

						<!-- CATEGORY -->
						<div class="col-sm-6 col-md-4">
							<div class="input-group mb-3 input-group-md">
								<div class="input-group-prepend">
									<span class="input-group-text">Display Status</span>
								</div>
								<select id='imagegallery_filter_displaystatus_select' type="text" class="form-control shadow-sm">
									<option value="-1">Any</option>
									<option value="1">ON</option>
									<option value="0">OFF</option>
								</select>
							</div>
						</div>

						<!-- FILTER BUTTON -->
						<div class="col-md-4 text-right">
							<button class="btn btn-primary ripple custom_shadow" id='filter_imagegallery_button'>Filter</button>
						</div>
					</div>

					<hr>

					<form id="gallery_image_add_form" style="display:none" class="animated fadeInDown">
						<div class="row">
							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Category
									<select name="catno" class="form-control shadow-sm mt-2" required>
										<option value="1">Video</option>
										<option value="2" selected>Photo</option>
									</select>
								</label>
							</div>

							<div id="add_video_url_div" class="col-md-6 form-group" style="display: none;">
								<label class="d-block mb-0">
									Video URL (Embed)
									<input name="imageurl" class="form-control shadow-sm mt-2" type="text" placeholder="Video URL (Embed)...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_title_input">Title</label>
								<input type="text" class="form-control shadow-sm" id="image_title_input" placeholder="Title" required>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_external_link_input">External Link (If Any)</label>
								<input type="text" class="form-control shadow-sm" id="image_external_link_input" placeholder="External link...">
							</div>

							<div class="col-md-6 form-group">
								<label for="image_short_desc_input">Display Order No</label>
								<input class="form-control shadow-sm" type="number" id="image_displayorderno_input" placeholder="Display order no" required>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_display_status_input">Display Status</label>
								<select class="form-control shadow-sm" id="image_display_status_input" required>
									<option value="1">ON</option>
									<option value="0">OFF</option>
								</select>
							</div>
						</div>

						<div id="galleryImageDiv" style="margin-bottom:10px;position:relative">
							<img src="../assets/image/eventdefault.jpg" id="galleryImage" onerror="this.onerror=null;this.src='../assets/image/errordefault.jpg';" class="shadow" class="shadow" style="width:100%;">
							<div class="imageeditdiv">
								<label for="gallery_image_input">
									<i class="fas fa-camera" style="font-size:32px;cursor: pointer;">
									</i>
								</label>
								<input id="gallery_image_input" type="file" style="display:none" accept="image/*">
							</div>
							<div class="imagecrossdiv" id="imagecrossdiv">
								<svg viewBox="0 0 24 24" width="24px" height="24px" x="0" y="0" preserveAspectRatio="xMinYMin meet" class="artdeco-icon" focusable="false">
									<path d="M20,5.32L13.32,12,20,18.68,18.66,20,12,13.33,5.34,20,4,18.68,10.68,12,4,5.32,5.32,4,12,10.69,18.68,4Z" class="large-icon" style="fill: red"></path>
								</svg>
							</div>
						</div>

						<div style="text-align:center">
							<button type="submit" class="btn btn-primary ripple px-5 custom_shadow">Save</button>
						</div>
						<hr>
					</form>

					<div class="galleryContainer" id="galleryContainer">

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade gallery_modal fade-scale" tabindex="-1" role="dialog" id="gallery_update_modal">
		<div class="modal-dialog modal-lg gallery_modal_dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Gallery Update</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="gallery_image_update_form">
						<div class="row">
							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Category
									<select name="catno" class="form-control shadow-sm mt-2" required>
										<option value="1">Video</option>
										<option value="2" selected>Photo</option>
									</select>
								</label>
							</div>

							<div id="update_video_url_div" class="col-md-6 form-group" style="display: none;">
								<label class="d-block mb-0">
									Video URL (Embed)
									<input name="imageurl" class="form-control shadow-sm mt-2" type="text" placeholder="Video URL (Embed)...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_title_update">Image Title</label>
								<input type="text" class="form-control shadow-sm" id="image_title_update" placeholder="Image title" required>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_external_link_edit">External Link (If Any)</label>
								<input type="text" class="form-control shadow-sm" id="image_external_link_update" placeholder="external link...">
							</div>

							<div class="col-md-6 form-group">
								<label for="image_short_desc_update">Display Order No</label>
								<input class="form-control shadow-sm" id="image_displayorderno_update" type="number" placeholder="Display Order No" required>
							</div>

							<div class="col-md-6 form-group">
								<label for="image_display_status_update">Display status</label>
								<select class="form-control shadow-sm" id="image_display_status_update" required>
									<option value="1">ON</option>
									<option value="0">OFF</option>
								</select>
							</div>
						</div>

						<div id="galleryImageUpdateDiv" style="margin-bottom:10px;position:relative">
							<img src="../assets/image/eventdefault.jpg" id="galleryUpdateImage" onerror="this.onerror=null;this.src='../assets/image/errordefault.jpg';" class="shadow" class="shadow" style="width:100%; height: auto;">
							<div class="imageeditdiv">
								<label for="gallery_image_update">
									<i class="fas fa-camera" style="font-size:32px;cursor: pointer;">
									</i>
								</label>
								<input id="gallery_image_update" type="file" style="display:none" accept="image/*">
							</div>
							<div class="imagecrossdiv" id="imagecrossupdatediv">
								<svg viewBox="0 0 24 24" width="24px" height="24px" x="0" y="0" preserveAspectRatio="xMinYMin meet" class="artdeco-icon" focusable="false">
									<path d="M20,5.32L13.32,12,20,18.68,18.66,20,12,13.33,5.34,20,4,18.68,10.68,12,4,5.32,5.32,4,12,10.69,18.68,4Z" class="large-icon" style="fill: red"></path>
								</svg>
							</div>
						</div>

						<div class="mb-3">
							<small style="color:green" id="originalfilename"></small>
						</div>

						<div style="text-align:center">
							<button type="submit" class="btn btn-primary ripple shadow px-5">Save</button>
						</div>
					</form>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary ripple custom_shadow" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		const ERROR_IMAGE = `../assets/image/errordefault.jpg`;
	</script>

	<script type="text/javascript" src="js/imagegallery.js" defer></script>

</body>

</html>