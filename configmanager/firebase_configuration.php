<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-analytics.js"></script>

<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-storage.js"></script>

<script>
	// Your web app's Firebase configuration
	// For Firebase JS SDK v7.20.0 and later, measurementId is optional
	const firebaseConfig = {
		apiKey: "AIzaSyDOp17r4BA98w7-fDk91y7kdoKownYdw9o",
		authDomain: "bubban-edu-bd.firebaseapp.com",
		projectId: "bubban-edu-bd",
		storageBucket: "bubban-edu-bd.appspot.com",
		messagingSenderId: "724783438944",
		appId: "1:724783438944:web:7ebffa50b536c1f68d7705",
		measurementId: "G-MB7PCY7W4T"
	};

	// Initialize Firebase
	firebase.initializeApp(firebaseConfig);
	firebase.analytics();

	let storageRef = firebase.storage().ref();

	function upload_file_in_firebase(file, target_dir = "files/") {
		target_dir = target_dir.endsWith("/") ? target_dir : (target_dir + "/");
		let extension = ".";
		if (file instanceof File) {
			extension += file.name.split(".").pop().toLowerCase();
		} else {
			extension += "jpg";
		}
		// let fileactualname = target_dir + (file.name || random_string(10));
		let fileactualname = target_dir + random_string(5, extension);

		return new Promise(function(resolve, reject) {
			storageRef.child(fileactualname).put(file).then(function(snapshot) {
				// console.log('Uploaded', snapshot.totalBytes, 'bytes.');
				// console.log('File metadata:', snapshot.metadata);
				// Let's get a download URL for the file.
				snapshot.ref.getDownloadURL().then(function(fileurl) {
					console.log('File available at', fileurl);
					resolve({
						fileactualname,
						fileurl
					});
				});
			}).catch(function(error) {
				console.log('Upload failed:', error);
				reject(error);
			});
		});
	}

	function upload_base64string_in_firebase(file, target_dir = "files/") {
		target_dir = target_dir.endsWith("/") ? target_dir : (target_dir + "/");
		// let fileactualname = target_dir + (file.name || random_string(10));
		let fileactualname = target_dir + random_string(5);
		let contentType = file.substr(file.indexOf(":") + 1, file.indexOf(";") - file.indexOf(":") - 1) || 'image/jpg';
		let fileToString = file.split(',')[1];
		// console.log(fileToString);
		// console.log("contentType", contentType);

		return new Promise(function(resolve, reject) {
			storageRef.child(fileactualname).putString(fileToString, 'base64', {
					contentType: contentType
				})
				.then(function(snapshot) {
					// console.log('Uploaded', snapshot.totalBytes, 'bytes.');
					// console.log('File metadata:', snapshot.metadata);
					// Let's get a download URL for the file.
					snapshot.ref.getDownloadURL().then(function(fileurl) {
						console.log('File available at', fileurl);
						resolve({
							fileactualname,
							fileurl
						});
					});
				}).catch(function(error) {
					console.log('Upload failed:', error);
					reject(error);
				});
		});
	}

	function delete_file_from_firebase(option) {
		return new Promise(function(resolve, reject) {
			let fileWithLocation = "";

			if (option.fileWithLocation) { // files/images/agamilabslogo.png
				fileWithLocation = option.fileWithLocation;
			} else if (option.url || option) {
				let url = option.url || option;

				let len = (url.indexOf('?') < 0 ? url.length : url.indexOf('?')) - url.indexOf('/o/') - 3;

				fileWithLocation = url.substr(url.indexOf('/o/') + 3, len);

			} else {
				toastr.error('No data provided');
				return;
			}

			console.log(fileWithLocation);
			// Create a reference to the file to delete
			let desertRef = storageRef.child(fileWithLocation);

			// Delete the file
			desertRef.delete().then(() => {
				// File deleted successfully
				resolve('File was deleted');
			}).catch((error) => {
				// Uh-oh, an error occurred!
				reject('Unable to delete this file');
			});
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