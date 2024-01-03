<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>


	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">
					<div class="app-page-title mb-0">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-briefcase icon-gradient bg-amy-crisp"></i>
								</div>
								<div>
									Task Filter
									<div class="page-title-subheading">Employees' complete/incomplete task mentioned here.</div>
								</div>
							</div>
						</div>
					</div>

					<form id="task_filter_form">
						<fieldset class="custom_fieldset bg-night-sky text-white shadow-sm pb-0">
							<legend class="legend-label bg-white text-dark shadow-sm">Task Filter Form</legend>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="d-block mb-0">
											<div class="mb-2">Task Filter For</div>
											<select name="assignedto" class="form-control shadow-sm">
												<option value="<?= $userno ?>">Myself</option>
											</select>
										</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="d-block mb-0">
											Date
											<div id="task_filter_date_input" name="date" class="form-control shadow-sm mt-2" style="cursor: pointer;">
												<i class="fa fa-calendar"></i>&nbsp;
												<span></span>
											</div>
										</label>
									</div>
								</div>

								<div class="col-md-8">
									<div class="position-relative form-check form-check-inline">
										<label class="form-check-label">
											<input name="wstatusno" type="radio" class="form-check-input" value="-1"> All Task
										</label>
									</div>
									<div class="position-relative form-check form-check-inline">
										<label class="form-check-label">
											<input name="wstatusno" type="radio" class="form-check-input" value="2" checked> Incomplete
										</label>
									</div>
									<div class="position-relative form-check form-check-inline">
										<label class="form-check-label">
											<input name="wstatusno" type="radio" class="form-check-input" value="3"> Complete
										</label>
									</div>
									<div class="position-relative form-check form-check-inline form-group">
										<label class="form-check-label">
											<input name="wstatusno" type="radio" class="form-check-input" value="4"> Abondoned
										</label>
									</div>
								</div>

								<div class="col-md-4 text-right">
									<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow mb-2" type="submit">
										<i class="fa fa-search mr-1"></i> Search
									</button>
								</div>
							</div>
						</fieldset>
					</form>

					<div id="task_filter_container" class="mt-3"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
		var start = moment().subtract(6, 'days');
		var end = moment();

		function cb(start, end) {
			$('#task_filter_date_input span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}

		$('#task_filter_date_input').daterangepicker({
			startDate: start,
			endDate: end,
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'Last 90 Days': [moment().subtract(89, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);

		cb(start, end);

		get_my_fellow();

		function get_my_fellow() {
			let select = $(`#task_filter_form [name="assignedto"]`).empty();

			$.post(`php/ui/taskmanager/selection/get_my_fellow.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.results, (index, value) => {
						let selected = USER_NO == value.userno;
						select.append(new Option(value.userfullname, value.userno, selected, selected));
					});

					select.select2({
						placeholder: "Select Employee...",
						allowClear: true
					});

					get_filtered_task();
				}
			}, `json`);
		}

		$(`#task_filter_form`).submit(function(e) {
			e.preventDefault();
			get_filtered_task();
		});

		function get_filtered_task() {
			$(`#task_filter_container`).empty();

			let json = Object.fromEntries((new FormData($(`#task_filter_form`)[0])).entries());
			let drp = $('#task_filter_date_input').data('daterangepicker');
			json.startdate = drp.startDate.format('YYYY-MM-DD');
			json.enddate = drp.endDate.format('YYYY-MM-DD');

			$.post(`php/ui/taskmanager/selection/get_filtered_task.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					reject(resp.message);
				} else {
					if (resp.results.length) {
						show_task(resp.results, `#task_filter_container`);
					}
				}
			}, `json`);
		}
	</script>
</body>

</html>