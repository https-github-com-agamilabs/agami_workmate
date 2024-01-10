loadImageCropper(1, 1, 300, 300, function (resp) {
	console.log('profilepicresp=>', resp);
	var resp = JSON.parse(resp);
	if (resp.error) {
		toastr.error(resp.message);
		//$("#people_profile_pic_modal").modal('hide');
	} else {
		toastr.success(resp.message);
		$("#people_profile_pic_modal").modal('hide');
		get_peopleprimary();
	}
});

function loadImageCropper(ratioWidth, ratioHeight, imageWidth, imageHeight, callback) {
	var avatar = document.getElementById('tchrProPic');
	var image = document.getElementById('image');
	var input = document.getElementById('people_profile_pic_input');
	var $modal = $('#people_profile_pic_modal');
	var cropper;

	input.addEventListener('change', function (e) {
		var files = e.target.files;
		var done = function (url) {
			input.value = '';
			image.src = url;

			$modal.modal('show');
		};
		var reader;
		var file;
		var url;
		if (files && files.length > 0) {
			file = files[0];
			if (URL) {
				done(URL.createObjectURL(file));
			} else if (FileReader) {
				reader = new FileReader();
				reader.onload = function (e) {
					done(reader.result);
				};
				reader.readAsDataURL(file);
			}
		}
	});

	$modal.on('shown.bs.modal', function () {
		cropper = new Cropper(image, {
			dragMode: 'move',
			aspectRatio: ratioWidth / ratioHeight,
			autoCropArea: 0.65,
			restore: false,
			guides: false,
			center: false,
			highlight: false,
			cropBoxMovable: true,
			cropBoxResizable: true,
			toggleDragModeOnDblclick: false,
			viewMode: 3,
		});
	}).on('hidden.bs.modal', function () {
		cropper.destroy();
		cropper = null;
	});

	document.getElementById('crop').addEventListener('click', function () {
		let submitButton = $("#crop")
			.prop("disabled", true)
			.html(`<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`);

		var initialAvatarURL;
		var canvas;

		if (cropper) {
			canvas = cropper.getCroppedCanvas({
				width: imageWidth,
				height: imageHeight,
			});
			initialAvatarURL = avatar.src;

			canvas.toBlob(function (blob) {
				(upload_file_in_server(blob, "files/people/")).then(
					response => {
						console.log("image upload response", response);
						let json = {
							peopleno: $(`#people_detail_update_form`).data(`peopleno`),
							// photoactualname: response.fileactualname,
							photo_url: response.fileurl,
						};

						$.ajax({
							type: 'POST',
							url: `${publicAccessUrl}php/ui/user/setup_user.php`,
							data: json,
							success: (resp) => {
								avatar.src = canvas.toDataURL();
								if (callback != null || callback != undefined) {
									callback(resp);
									$modal.modal('hide');
								}

								let prevphoto_url = $(`#tchrProPic`).data(`photo_url`);

								if (!resp.error && prevphoto_url) {
									(delete_file_from_firebase({
										url: decodeURIComponent(prevphoto_url)
									})).then(
										result => console.log(result),
										error => console.log(error)
									);
								}
							},
							complete: () => submitButton.prop("disabled", false).html("Crop & Upload")
						});
					},
					error => {
						toastr.error(error);
						submitButton.prop("disabled", false).html("Crop & Upload");
					}
				);
			});
		}
	});
}
