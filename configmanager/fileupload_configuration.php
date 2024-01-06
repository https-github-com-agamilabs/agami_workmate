<?php
	$base_path = dirname(dirname(__FILE__));
	include_once($base_path . "/php/db/config.php");
?>
<script>
	const PUBLIC_ACCESS_URL = `<?= $publicAccessUrl; ?>`;
</script>

<script type="text/javascript">
	var Upload = function(file, options) {
		this.file = file;
		console.log(this.file);
		if (options) {
			this.name = options.name || "";
		} else {
			this.name = "";
		}
		if (options) {
			this.location = options.location || "";
		} else {
			this.location = "";
		}

		if (options) {
			this.type = options.contentType || options.type || "";
		} else {
			this.type = "";
		}


		console.log(`this =>`, this);

	};

	Upload.prototype.getType = function() {
		return this.file.type;
	};
	Upload.prototype.getSize = function() {
		return this.file.size;
	};
	Upload.prototype.getName = function() {
		return this.file.name;
	};
	Upload.prototype.doUpload = function(onsuccess, onfail) {
		var that = this;
		var formData = new FormData();

		// add assoc key values, this will be posts values
		formData.append("file", this.file, this.getName());
		formData.append("upload_file", true);
		formData.append("ext", this.type);
		if (this.location && this.location.length > 0) {
			formData.append("location", this.location);
		}

		$.ajax({
			type: "POST",
			url: PUBLIC_ACCESS_URL + "assets_manager/upload_file.php",
			xhr: function() {
				var myXhr = $.ajaxSettings.xhr();
				if (myXhr.upload) {
					myXhr.upload.addEventListener('progress', that.progressHandling, false);
				}
				return myXhr;
			},
			success: function(data) {
				console.log(data);
				// your callback here
				onsuccess(data);

			},
			error: function(error) {
				console.log(error);

				// handle error
				onfail(error);
			},
			async: true,
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 60000
		});
	};

	Upload.prototype.progressHandling = function(event) {
		var percent = 0;
		var position = event.loaded || event.position;
		var total = event.total;
		var progress_bar_id = "#progress-wrp";
		if (event.lengthComputable) {
			percent = Math.ceil(position / total * 100);
		}
		// update progressbars classes so it fits your code
		$(progress_bar_id + " .progress-bar").css("width", +percent + "%");
		$(progress_bar_id + " .status").text(percent + "%");
	};
</script>

<script>
	var FileControl = function(fileurl) {
		this.fileurl = fileurl;
	};

	FileControl.prototype.doDelete = function(onsuccess, onfail) {
		var that = this;
		var formData = new FormData();

		// add assoc key values, this will be posts values
		formData.append("fileurl", this.fileurl);
		formData.append("delete_file", true);

		$.ajax({
			type: "POST",
			url: PUBLIC_ACCESS_URL + "assets_manager/delete_file.php",
			success: function(data) {
				console.log(data);
				// your callback here
				onsuccess(data);
			},
			error: function(error) {
				console.log(error);
				// handle error
				onfail(error);
			},
			async: true,
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 60000
		});
	}
</script>


<script>
	function upload_file_in_server(file, target_dir = "files/") {
		target_dir = target_dir.endsWith("/") ? target_dir : (target_dir + "/");

		let extension = ".jpg";
		if (file && file.name) {
			extension = "." + (file.name.split(".").pop().toLowerCase() || "jpg")
		}

		console.log(`extension =>`, extension);

		// let fileactualname = target_dir + (file.name || random_string(10));
		let fileactualname = target_dir + random_string(5, extension);
		console.log(`fileactualname =>`, fileactualname);

		return new Promise(function(resolve, reject) {

			var upload = new Upload(file, {
				name: fileactualname,
				location: target_dir,
				type: "image/" + extension.split('.').pop().toLowerCase()
			});

			// maby check size or type here with upload.getSize() and upload.getType()

			// execute upload
			upload.doUpload(function(resp) {
				resp = JSON.parse(resp);
				console.log('File available at', resp.fileurl);
				resolve({
					fileactualname,
					fileurl: resp.fileurl
				});
			}, function(error) {
				console.log('Upload failed:', error);
				reject(error);
			});
			return;

			// storageRef.child(fileactualname).put(file).then(function(snapshot) {
			// 	// console.log('Uploaded', snapshot.totalBytes, 'bytes.');
			// 	// console.log('File metadata:', snapshot.metadata);
			// 	// Let's get a download URL for the file.
			// 	snapshot.ref.getDownloadURL().then(function(fileurl) {
			// 		console.log('File available at', fileurl);
			// 		resolve({
			// 			fileactualname,
			// 			fileurl
			// 		});
			// 	});
			// }).catch(function(error) {
			// 	console.log('Upload failed:', error);
			// 	reject(error);
			// });
		});
	}

	function upload_base64string_in_server(file, target_dir = "files/") {
		target_dir = target_dir.endsWith("/") ? target_dir : (target_dir + "/");
		// let fileactualname = target_dir + (file.name || random_string(10));
		let fileactualname = target_dir + random_string(5);
		let contentType = file.substr(file.indexOf(":") + 1, file.indexOf(";") - file.indexOf(":") - 1) || 'image/jpg';
		let fileToString = file.split(',')[1];
		// console.log(fileToString);
		// console.log("contentType", contentType);

		return new Promise(function(resolve, reject) {

			var upload = new Upload(file, {
				name: fileactualname,
				contentType: contentType
			});

			// maby check size or type here with upload.getSize() and upload.getType()

			// execute upload
			upload.doUpload(function(resp) {
				resp = JSON.parse(resp);

				console.log('File available at', resp.fileurl);
				resolve({
					fileactualname,
					fileurl: resp.fileurl
				});
			}, function(error) {
				console.log('Upload failed:', error);
				reject(error);
			});
			return;

			// storageRef.child(fileactualname).putString(fileToString, 'base64', {
			// 		contentType: contentType
			// 	})
			// 	.then(function(snapshot) {
			// 		// console.log('Uploaded', snapshot.totalBytes, 'bytes.');
			// 		// console.log('File metadata:', snapshot.metadata);
			// 		// Let's get a download URL for the file.
			// 		snapshot.ref.getDownloadURL().then(function(fileurl) {
			// 			console.log('File available at', fileurl);
			// 			resolve({
			// 				fileactualname,
			// 				fileurl
			// 			});
			// 		});
			// 	}).catch(function(error) {
			// 		console.log('Upload failed:', error);
			// 		reject(error);
			// 	});
		});
	}

	function delete_file_from_server(option) {
		return new Promise(function(resolve, reject) {
			let fileWithLocation = "";

			if (option.fileWithLocation) { // files/images/agamilabslogo.png
				fileWithLocation = option.fileWithLocation;
			} else if (option.url || option) {
				let url = option.url || option;
				if (url) {
					fileWithLocation = url.substr(url.indexOf('files'));
				} else {
					// fileWithLocation = ""
					return;
				}

			} else {
				toastr.error('No data provided');
				reject("No data provided");
				return;
			}

			console.log(fileWithLocation);

			var fileControl = new FileControl(fileWithLocation);

			fileControl.doDelete(function(successmsg) {
				resolve(successmsg);
			}, function(failmsg) {
				reject(failmsg);
			});

			// // Create a reference to the file to delete
			// let desertRef = storageRef.child(fileWithLocation);
			//
			// // Delete the file
			// desertRef.delete().then(() => {
			// 	// File deleted successfully
			// 	resolve('File was deleted');
			// }).catch((error) => {
			// 	// Uh-oh, an error occurred!
			// 	reject('Unable to delete this file');
			// });
		});
	}

	function random_string(len = 10, ext = ".jpg", charSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789") {
		// Math.random().toString(36).substr(2, 5)

		let randomString = "";
		for (let i = 0; i < len; i++) {
			let randomPoz = Math.floor(Math.random() * charSet.length);
			randomString += charSet.substring(randomPoz, randomPoz + 1);
		}
		return `${randomString}_${Date.now()}${ext}`;
	}
</script>