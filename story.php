<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

				</div>
			</div>
		</div>
	</div>

	<script>
		const ucatno = `<?= $ucatno; ?>`;
	</script>
</body>

</html>