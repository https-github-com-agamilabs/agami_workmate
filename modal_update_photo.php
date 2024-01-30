<link href="./vendor/cropper/cropper.min.css" rel="stylesheet">
<script src="./vendor/cropper/cropper.min.js"></script>
<?php
include_once dirname(__FILE__) . "/configmanager/firebase_configuration.php";
?>
<!-- MODAL FOR CROPPING SELECTED IMAGE -->
<div id="image_cropping_modal" class="modal animated fadeInUp" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Crop Image Before Upload</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="photo-update-hint" class="text-center h5"></div>

				<div class="row text-center justify-content-center">
					<div>
						<div>
							<div>Old Photo</div>
							<img id="photo_preview_previous_image" style='width:150px;' src="assets/image/default-placeholder.png" class="img-fluid border border-success">
						</div>

						<div class="my-2"></div>

						<div class="d-none" id="preview_photo_root">
							<div>New Photo (Preview)</div>
							<div class="img-preview preview-lg"></div>
						</div>
					</div>

					<div class="my-3" style="width:30px;"></div>
					<div class="py-2">
						<img id="photo_preview_image" style='width:300px; height:300px; cursor:pointer;' src="assets/image/default-placeholder.png" class="img-fluid border border-danger">

						<div class="mt-2">
							Click here to change photo
						</div>
						<input id="photo_file_input" type="file" style="display:none" accept="image/x-png,image/gif,image/jpeg">
					</div>
				</div>
			</div>
			<div class="modal-footer py-2">
				<button type="button" id="crop_image_button" class="btn btn-primary shadow">Crop and Upload</button>
				<button type="button" class="btn btn-secondary shadow" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(() => {
		$(document).on('click', '#photo_preview_image', function() {
			console.log(`image =>`, this);

			$("#photo_file_input").trigger('click');
		});

		$(document).on('change', "#photo_file_input", function(e) {
			if (this.files && this.files[0]) {
				let reader = new FileReader();

				reader.onload = function(e) {
					$("#photo_preview_image").attr("src", this.result);
					const image = $("#photo_preview_image")[0];
					if (image.cropper != undefined) {
						image.cropper.destroy();
					}
					const cropper = new Cropper(image, {
						aspectRatio: 1 / 1,
						preview: '.img-preview',
						zoomable: false,
						crop(event) {
							// $('#preview_photo_root').removeClass('d-none');
						},
					});

					$("#image_cropping_modal").modal("show");
					$('img-preview img').attr({
						'style': 'width: 256px;height: 144px;'
					});
				}

				reader.readAsDataURL(this.files[0]);
			}
		});

		$(document).on('click', "#crop_image_button", function(e) {
			e.preventDefault();

			$("#crop_image_button")
				.prop("disabled", true)
				.html(`<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`);

			if (photo_preview_image.cropper) {
				photo_preview_image.cropper.getCroppedCanvas({
					width: 300,
					height: 300
				}).toBlob(function(blob) {
					let prevphotoname = $("#photo_preview_image").data("src");
					let target = $("#crop_image_button").data(`target`);
					let uploadData = $("#crop_image_button").data(`upload_data`);

					(upload_file_in_firebase(blob, uploadData.target_dir)).then(
						response => {
							console.log("file upload response", response);
							target.data(`response`, response);

							if (uploadData.preview_target.length) {
								uploadData.preview_target.attr(`src`, response.fileurl);
							}

							photo_preview_image.cropper.destroy();
							$("#image_cropping_modal").modal("hide");

							$("#crop_image_button").prop("disabled", false).html("Crop");

							$("#crop_image_button").trigger('on_image_uploaded', response);

						},
						error => {
							toastr.error(error);
							$("#crop_image_button").prop("disabled", false).html("Crop");
						}
					);
				}, 'image/jpeg', 0.8);
			}
		});
	});
</script>