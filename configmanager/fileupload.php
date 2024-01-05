<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
	<meta charset="utf-8">
	<title></title>
	<script src="../vendor/jquery/jquery.min.js"></script>

</head>

<body>

	<div>
		<input type="file" name="fileupload" id="fileupload">
	</div>


	<?php require 'fileupload_configuration.php'; ?>

	<script type="text/javascript">
		document.getElementById('fileupload').addEventListener('change', function() {
			console.log(event.target.files);
			upload_file_in_server(event.target.files[0]);
		});
	</script>

</body>

</html>