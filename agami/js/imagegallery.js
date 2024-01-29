image_gallery_add.addEventListener('click', function () {
	$(`#gallery_image_add_form`).toggle();
});

gallery_image_input.addEventListener('change', function () {
	readURL(this, 'galleryImage');
	imagecrossdiv.style.display = 'initial';
});

$("#gallery_update_modal").on('hidden.bs.modal', function () {
	gallery_image_update_form.reset();
	if (galleryUpdateImage.cropper != undefined) {
		galleryUpdateImage.cropper.destroy();
		imagecrossupdatediv.style.display = 'none';
	}
});


imagecrossdiv.addEventListener('click', function () {
	galleryImage.cropper.destroy();
	gallery_image_input.value = '';
	galleryImage.src = '../assets/image/eventdefault.jpg';
	this.style.display = 'none';
});

gallery_image_update.addEventListener('change', function () {
	readURL(this, 'galleryUpdateImage');
	imagecrossupdatediv.style.display = 'initial';
});

imagecrossupdatediv.addEventListener('click', function () {
	galleryUpdateImage.cropper.destroy();
	gallery_image_update.value = '';
	galleryUpdateImage.src = this.dataset.preimage;
	this.style.display = 'none';
});

function readURL(input, imageContainer) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#' + imageContainer).attr('src', this.result);
			// $('#teacher_profile_pic_preview').attr('src', e.target.result);
			const image = document.getElementById(imageContainer);
			// console.log(image.cropper);
			if (image.cropper != undefined) {
				image.cropper.destroy();
			}
			const cropper = new Cropper(image, {
				aspectRatio: 4 / 3,
				zoomable: false,
				crop(event) {
				},
			});
		}

		reader.readAsDataURL(input.files[0]);
	}
}

gallery_image_add_form.addEventListener('submit', function (event) {
	event.preventDefault();

	$("#gallery_image_add_form :submit").prop("disabled", true).html(`<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`);

	let json = {
		catno: $(`#gallery_image_add_form [name="catno"]`).val(),
		image_title: image_title_input.value,
		externallink: image_external_link_input.value,
		displayorderno: image_displayorderno_input.value,
		display_status: image_display_status_input.value
	};

	let successCounter = 0;
	let triggerOnSuccess = 2;

	if (json.catno == 1) {
		let imageurl = $(`#gallery_image_add_form [name="imageurl"]`).val();
		if (!imageurl.length) {
			toastr.error("Video URL required.");
			$("#gallery_image_add_form :submit").prop("disabled", false).html("Save");
			return;
		}

		json.imageurl = imageurl;
		insert_image_gallery(json);
	} else {
		if (!galleryImage.cropper) {
			toastr.warning('Image is required!');
			$("#gallery_image_add_form :submit").prop("disabled", false).html("Save");
			return;
		}

		galleryImage.cropper.getCroppedCanvas().toBlob(function (blob) {
			let image_promise = upload_file_in_firebase(blob, "files/gallery/images/");
			image_promise.then(
				response => {
					console.log("image upload response", response);
					json.imageactualname = response.fileactualname;
					json.imageurl = response.fileurl;

					successCounter++;
					if (successCounter >= triggerOnSuccess) {
						// upload to server
						insert_image_gallery(json);
					}
				},
				error => {
					toastr.error(error);
					$("#gallery_image_add_form :submit").prop("disabled", false).html("Save");
				}
			);
		}, 'image/jpeg', 0.8);

		// this width x height = thumbnail width x height
		galleryImage.cropper.getCroppedCanvas({ width: 300, height: 224 }).toBlob(function (blob) {
			let image_promise = upload_file_in_firebase(blob, "files/gallery/thumbnailimages/");
			image_promise.then(
				response => {
					console.log("thumbnailimage upload response", response);
					// json.thumbnailimage = response.fileactualname;
					json.thumbnailimageurl = response.fileurl;

					successCounter++;
					if (successCounter >= triggerOnSuccess) {
						// upload to server
						insert_image_gallery(json);
					}
				},
				error => {
					toastr.error(error);
					$("#gallery_image_add_form :submit").prop("disabled", false).html("Save");
				}
			);
		}, 'image/jpeg', 0.8);
	}

});

function insert_image_gallery(json) {
	console.log(json);
	$.ajax({
		type: "POST",
		url: "php/ui/gallery/setup_gallery.php",
		data: json,
		success: (resp) => {
			// console.log('resp=>', resp);
			resp = $.parseJSON(resp);
			// console.log('resp=>', resp);
			if (resp.error) {
				toastr.error(resp.message);
			} else {
				if (galleryImage.cropper) {
					galleryImage.cropper.destroy();
				}
				gallery_image_add_form.reset();
				galleryImage.src = '../assets/image/eventdefault.jpg';
				toastr.success(resp.message);
				gallery_image_add_form.style.display = 'none';
				imagecrossdiv.style.display = 'none';

				$(`#add_video_url_div`).hide();
				$(`#galleryImageDiv`).show();
				get_filtered_gallery();
			}
		},
		complete: () => $("#gallery_image_add_form :submit").prop("disabled", false).html("Save")
	});
}

get_filtered_gallery();
function get_filtered_gallery() {
	var limit = document.getElementById('imagegallery_filter_limit_select').value;
	var display_status = document.getElementById('imagegallery_filter_displaystatus_select').value;

	$.ajax({
		url: 'php/ui/gallery/get_categorized_gallery.php',
		type: 'POST',
		data: {
			limit: limit,
			display_status: display_status
		},
		success: (resp) => {
			//   console.log('resp=>', resp);
			resp = $.parseJSON(resp);
			console.log('resp=>', resp);
			document.getElementById("galleryContainer").innerHTML = "";

			if (resp.error) {
				// toastr.error(resp.message);
				document.getElementById("galleryContainer").innerHTML = resp.message;
				document.getElementById("galleryContainer").style.color = "red";
			} else {
				show_gallery(resp.data)
			}
		}
	});
}

document.getElementById('filter_imagegallery_button').addEventListener('click', function (e) {
	get_filtered_gallery();
});

function show_gallery(data) {
	$.each(data, (index, value) => {
		let card = $(`<div class="card position-relative m-2" style="width: 18rem;">
					${value.catno == 1 ?
				`<iframe height="250" src="${value.imageurl}" title="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>` :
				`<img src="${value.thumbnailimageurl}" onerror="this.src='${ERROR_IMAGE}';" class="card-img-top img-fluid" alt="Events Cover image">`}
                    <div class="position-absolute" style="top: 10px;right: 5px;">
                        <button class="edit_button btn btn-sm btn-info shadow" title="Edit Events">
                            <i class="far fa-edit"></i>
                        </button>
                        <button class="delete_button btn btn-sm btn-danger shadow" title="Delete Events">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <a href="${value.externallink || `javascript:void(0);`}" class="h6 font-weight-bold mb-0">${value.image_title}</a>
                    </div>
                </div>`)
			.appendTo(`#galleryContainer`);

		(function ($) {
			card.find(`.edit_button`).click(function (e) {
				$('#gallery_update_modal').modal('show');
				gallery_image_update_form.reset();
				galleryUpdateImage.style.height = "auto";
				galleryUpdateImage.src = value.thumbnailimageurl;
				originalfilename.innerHTML = "Original image name " + (value.imageactualname || "");
				$(`#gallery_image_update_form [name="catno"]`).data(`prevcatno`, value.catno).val(value.catno);
				image_title_update.value = value.image_title;
				image_title_update.dataset.imageno = value.imageno;
				image_external_link_update.value = value.externallink;
				image_displayorderno_update.value = value.displayorderno;
				image_display_status_update.value = value.display_status;
				imagecrossupdatediv.style.display = 'none';
				imagecrossupdatediv.dataset.preimage = value.imageurl;

				if (value.catno == 1) {
					$(`#galleryImageUpdateDiv`).hide();
					$(`#update_video_url_div`).show();
					$(`#gallery_image_update_form [name="imageurl"]`).val(value.imageurl);
				} else {
					$(`#update_video_url_div`).hide();
					$(`#galleryImageUpdateDiv`).show();
				}

				galleryUpdateImage.dataset.imageactualname = value.imageactualname;
				galleryUpdateImage.dataset.imageurl = value.imageurl;
				galleryUpdateImage.dataset.thumbnailimageurl = value.thumbnailimageurl;
			});

			card.find(`.delete_button`).click(function (e) {
				if (!confirm("Image & it's information will be deleted permanently. Are you sure?")) return;

				delete_imagegallery({
					imageno: value.imageno
				}, value.catno != 1 ? value.thumbnailimageurl : null, value.catno != 1 ? value.imageurl : null);
			});
		})(jQuery);
	});
}

gallery_image_update_form.addEventListener('submit', function (event) {
	event.preventDefault();

	$("#gallery_image_update_form :submit").prop("disabled", true).html(`<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`);

	let json = {
		catno: $(`#gallery_image_update_form [name="catno"]`).val(),
		image_title: image_title_update.value,
		externallink: image_external_link_update.value,
		displayorderno: image_displayorderno_update.value,
		display_status: image_display_status_update.value,
		imageno: image_title_update.dataset.imageno
	};

	let prevcatno = $(`#gallery_image_update_form [name="catno"]`).data(`prevcatno`);
	let prevThumbnailimageurl = galleryUpdateImage.dataset.thumbnailimageurl;
	let previmageurl = imagecrossupdatediv.dataset.imageurl;
	let previmageactualname = galleryUpdateImage.dataset.imageactualname;

	if (json.catno == 1) {
		let imageurl = $(`#gallery_image_update_form [name="imageurl"]`).val();
		if (!imageurl.length) {
			toastr.error("Video URL required.");
			$("#gallery_image_update_form :submit").prop("disabled", false).html("Save");
			return;
		}

		json.imageurl = imageurl;
		update_image_gallery(json);
	} else {
		if (galleryUpdateImage.cropper) {
			let successCounter = 0;
			let triggerOnSuccess = 2;

			galleryUpdateImage.cropper.getCroppedCanvas().toBlob(function (blob) {
				let image_promise = upload_file_in_firebase(blob, "files/gallery/images/");
				image_promise.then(
					response => {
						console.log("image upload response", response);
						json.imageactualname = response.fileactualname;
						json.imageurl = response.fileurl;

						successCounter++;
						if (successCounter >= triggerOnSuccess) {
							update_image_gallery(json, prevcatno != 1 ? prevThumbnailimageurl : null, prevcatno != 1 ? previmageurl : null);
						}
					},
					error => {
						toastr.error(error);
						$("#gallery_image_update_form :submit").prop("disabled", false).html("Save");
					}
				);
			}, 'image/jpeg', 0.8);

			// this width x height = thumbnail width x height
			galleryUpdateImage.cropper.getCroppedCanvas({ width: 300, height: 224 }).toBlob(function (blob) {
				let image_promise = upload_file_in_firebase(blob, "files/gallery/thumbnailimages/");
				image_promise.then(
					response => {
						console.log("thumbnailimage upload response", response);
						// json.thumbnailimage = response.fileactualname;
						json.thumbnailimageurl = response.fileurl;

						successCounter++;
						if (successCounter >= triggerOnSuccess) {
							update_image_gallery(json, prevcatno != 1 ? prevThumbnailimageurl : null, prevcatno != 1 ? previmageurl : null);
						}
					},
					error => {
						toastr.error(error);
						$("#gallery_image_update_form :submit").prop("disabled", false).html("Save");
					}
				);
			}, 'image/jpeg', 0.8);
		} else {
			json.imageactualname = previmageactualname;
			json.imageurl = previmageurl;
			json.thumbnailimageurl = prevThumbnailimageurl;
			update_image_gallery(json);
		}
	}
});

function update_image_gallery(json, prevThumbnailimageurl, previmageurl) {
	console.log(`json =>`, json);
	$.ajax({
		type: 'POST',
		url: 'php/ui/gallery/setup_gallery.php',
		data: json,
		success: (resp) => {
			//console.log('resp=>', resp);
			resp = $.parseJSON(resp);
			console.log('resp=>', resp);
			if (resp.error) {
				toastr.error(resp.message);
			} else {
				// if (prevThumbnailimageurl) {
				// 	(delete_file_from_firebase({
				// 		url: decodeURIComponent(prevThumbnailimageurl)
				// 	})).then(
				// 		result => console.log(result),
				// 		error => console.log(error)
				// 	);
				// }

				// if (previmageurl) {
				// 	(delete_file_from_firebase({
				// 		url: decodeURIComponent(previmageurl)
				// 	})).then(
				// 		result => console.log(result),
				// 		error => console.log(error)
				// 	);
				// }

				$('#gallery_update_modal').modal('hide');
				toastr.success(resp.message);
				gallery_image_update_form.reset();
				get_filtered_gallery();
			}
		},
		complete: () => $("#gallery_image_update_form :submit").prop("disabled", false).html("Save")
	});
}

function delete_imagegallery(json, prevThumbnailimageurl, previmageurl) {
	$.ajax({
		type: 'POST',
		url: 'php/ui/gallery/delete_gallery.php',
		data: json,
		success: (resp) => {
			//console.log('resp=>', resp);
			resp = $.parseJSON(resp);
			console.log('resp=>', resp);
			if (resp.error) {
				toastr.error(resp.message);
			} else {
				// if (prevThumbnailimageurl) {
				// 	(delete_file_from_firebase({
				// 		url: decodeURIComponent(prevThumbnailimageurl)
				// 	})).then(
				// 		result => console.log(result),
				// 		error => console.log(error)
				// 	);
				// }

				// if (previmageurl) {
				// 	(delete_file_from_firebase({
				// 		url: decodeURIComponent(previmageurl)
				// 	})).then(
				// 		result => console.log(result),
				// 		error => console.log(error)
				// 	);
				// }

				toastr.success(resp.message);
				get_filtered_gallery();
			}
		}
	});
}

$(`#gallery_image_add_form [name="catno"]`).change(function (e) {
	let catno = this.value;

	if (catno == 1) {
		$(`#galleryImageDiv`).hide();
		$(`#add_video_url_div`).show();
	} else {
		$(`#add_video_url_div`).hide();
		$(`#galleryImageDiv`).show();
	}
});