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
					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-briefcase icon-gradient bg-amy-crisp"></i>
								</div>
								<div>
									Alltime Incomplete Task
									<div class="page-title-subheading">All employees' incomplete task mentioned here.</div>
								</div>
							</div>
						</div>
					</div>

					<div id="incomplete_task_container"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
		get_filtered_task();

		function get_filtered_task() {
			$(`#incomplete_task_container`).empty();

			$.post(`php/ui/taskmanager/selection/get_all_incomplete_task_alltime.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					reject(resp.message);
				} else if (resp.results.length) {
					show_task(resp.results, `#incomplete_task_container`);
				}
			}, `json`);
		}
	</script>
</body>

</html>