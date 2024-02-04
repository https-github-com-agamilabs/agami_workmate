<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

	<!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script> -->

	<!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script> -->
	<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script> -->

	<!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet" /> -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script> -->

	<!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script> -->

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

	<style>
		#top_div {
			width: 100%;
			background-color: #999;

			background: -moz-linear-gradient(270deg, rgba(102, 102, 102, 1) 0%, rgba(51, 51, 51, 1) 100%);
			/* ff3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(102, 102, 102, 1)), color-stop(100%, rgba(51, 51, 51, 1)));
			/* safari4+,chrome */
			background: -webkit-linear-gradient(270deg, rgba(102, 102, 102, 1) 0%, rgba(51, 51, 51, 1) 100%);
			/* safari5.1+,chrome10+ */
			background: -o-linear-gradient(270deg, rgba(102, 102, 102, 1) 0%, rgba(51, 51, 51, 1) 100%);
			/* opera 11.10+ */
			background: -ms-linear-gradient(270deg, rgba(102, 102, 102, 1) 0%, rgba(51, 51, 51, 1) 100%);
			/* ie10+ */
			background: linear-gradient(180deg, rgba(102, 102, 102, 1) 0%, rgba(51, 51, 51, 1) 100%);
			/* w3c */
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#666666', endColorstr='#333333', GradientType=0);
			/* ie6-9 */
			border-bottom: 1px solid rgb(104, 104, 104);
		}

		.timer_button.text-success {
			color: #2ecb3d !important;
		}

		.timer_button.text-danger {
			color: #df3739 !important;
		}
	</style>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner pt-3 pl-0 pr-3">

					<div id="top_div">
						<div class="d-flex flex-wrap-reverse justify-content-between p-2">
							<div class="d-flex flex-wrap">
								<div class="alert alert-primary shadow-sm border border-primary text-center mb-0">
									<div class="d-flex flex-wrap justify-content-between mb-3">
										<div>
											<input id="startdate_input" type="date" class="form-control shadow-sm" value="<?= date('Y-m-d'); ?>">
										</div>
										<div class="display-4 mx-1" style="line-height: .5;"> - </div>
										<div>
											<input id="enddate_input" type="date" class="form-control shadow-sm" value="<?= date('Y-m-d'); ?>">
										</div>
									</div>
									<div class="d-flex flex-wrap justify-content-between">
										<div>
											<i class="fas fa-calendar-day"></i> <span id="today_date_span">Tue Jun 15 2021</span>
										</div>
										<div>
											<i class="fas fa-clock"></i> <span id="today_time_span">00:00:00 AM</span>
										</div>
										<div>
											<i class="fas fa-eye"></i> <span id="display_total_time_span">00:00:00</span>
										</div>
									</div>
								</div>

								<div class="align-self-center ml-2">
									<div class="d-flex flex-wrap justify-content-between">
										<div id="set_today_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">&nbsp;&nbsp;&nbsp;Today&nbsp;&nbsp;&nbsp;</div>
										<div id="set_yesterday_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">&nbsp;Yesterday&nbsp;</div>
									</div>
									<div class="d-flex flex-wrap justify-content-between">
										<div id="set_last_week_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">Last Week</div>
										<div id="set_last_month_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">Last Month</div>
									</div>
									<div class="d-flex flex-wrap justify-content-between">
										<div id="set_this_week_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">This Week</div>
										<div id="set_this_month_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded" role="button">This Month</div>
									</div>
								</div>
							</div>

							<div class="d-flex flex-wrap-reverse justify-content-between mb-2">
								<div class="alert alert-primary shadow-sm border border-primary text-center mb-0" style="width: 250px;">
									<h1 class="stopwatch mb-0" style="line-height: unset;">00:00:00</h1>
								</div>

								<i class="timer_button timer_click_button far fa-5x fa-play-circle btn text-success"></i>
							</div>
						</div>
					</div>

					<div class="table-responsive mt-3">
						<table id="time_keeper_table" class="table table-sm table-bordered table-striped table-hover text-center mb-0">
							<thead class="thead-dark">
								<tr>
									<th>Date</th>
									<th>In</th>
									<th>Out</th>
									<th>h:m:s</th>
									<th>People Name</th>
									<th>Work For</th>
									<th>Admin Comment</th>
								</tr>
							</thead>
							<tbody id="time_keeper_table_tbody"></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="time_keeper_update_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="time_keeper_update_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Time Keeper Update</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block">
								Start Time <span class="text-danger">*</span>
								<input name="starttime" class="form-control shadow-sm mt-2" type="datetime-local" placeholder="Start Time..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								End Time
								<input name="endtime" class="form-control shadow-sm mt-2" type="datetime-local" placeholder="End Time...">
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								Comment
								<textarea name="comment" class="form-control shadow-sm mt-2" placeholder="Comment..." rows="3"></textarea>
							</label>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		const ucatno = <?= $_SESSION['cogo_ucatno'] ?>;

		if (ucatno == 19) {
			$("#time_keeper_table_tbody").siblings("thead").find("tr:first").prepend(`<th></th>`);
		}

		if (now - savedAt < (6 * 60 * 60 * 1000) || formatDateToYYYYMMDD(new Date(savedAt)) == formatDateToYYYYMMDD(new Date(now))) {
			$("#startdate_input").val(localStorage.getItem(`startdate`));
			$("#enddate_input").val(localStorage.getItem(`enddate`));
		}

		function add_row_in_table(data) {
			let totalElapsedTime = 0;

			$.each(data, (index, value) => {
				let actionCell = $("<td>").css("width", "60px");
				let actionDiv = $("<div>").attr("class", "d-flex justify-content-center").appendTo(actionCell);

				let toggleStatusButton = $("<i>")
					.appendTo(actionDiv)
					.attr({
						"class": `fas ${value.endtime ? `fa-play-circle text-success` : `fa-stop-circle text-danger`} mx-1`,
						"type": "button",
						"title": "Toggle Timer"
					});

				let editButton = $("<i>")
					.appendTo(actionDiv)
					.attr({
						"class": "fas fa-pen text-info mx-1",
						"type": "button",
						"title": "Edit Timer"
					});

				let deleteButton = $("<i>")
					.appendTo(actionDiv)
					.attr({
						"class": "fas fa-times text-danger mx-1",
						"type": "button",
						"title": "Delete Timer"
					});

				let [date, intime] = value.starttime.split(" ");

				let dateCell = $("<td>").append(date);
				let inTimeCell = $("<td>")
					.attr("title", value.starttime ? formatDateTime(value.starttime) : "")
					.append(formatTime(intime));

				let [enddate, outtime] = value.endtime ? value.endtime.split(" ") : ["", null];

				let outTimeCell = $("<td>")
					.attr("title", value.endtime ? formatDateTime(value.endtime) : "")
					.attr("name", "outTimeCell")
					.append(outtime ? formatTime(outtime) : `--:--`);

				let hr = parseInt(value.elapsedtime / 3600, 10);
				let min = parseInt((value.elapsedtime % 3600) / 60, 10);
				let sec = parseInt(value.elapsedtime % 60, 10);
				totalElapsedTime += parseInt(value.elapsedtime, 10);

				let hmsTimeCell = $("<td>").attr("name", "hmsTimeCell").attr("class", value.endtime ? "" : `bg-danger text-white`).append(value.endtime ? `${padZero(hr)}:${padZero(min)}:${padZero(sec)}` : `--:--`);
				let peopleNameCell = $("<td>").append(value.userfullname);
				let workFowCell = $("<td>").append(value.workfor_name ?? `AGAMiLabs Ltd.`);
				let commentCell = $("<td>").append(value.comment || "");

				let row = $("<tr>").appendTo("#time_keeper_table_tbody");
				if (ucatno == 19) {
					row.append(actionCell);
				}
				row.append(dateCell, inTimeCell, outTimeCell, hmsTimeCell, peopleNameCell, workFowCell, commentCell);

				(function($) {
					toggleStatusButton.click(function(e) {
						e.preventDefault();
						if (confirm("Are you sure?")) {
							onoff_workingtime({
								userno: value.empno
							});
						}
					});

					editButton.click(function(e) {
						e.preventDefault();
						$("#time_keeper_update_modal").modal("show");
						$("#time_keeper_update_modal_form").data("timeno", value.timeno);
						$(`#time_keeper_update_modal_form [name="starttime"]`).val(value.starttime ? value.starttime.replace(" ", "T").slice(0, -3) : ``);
						$(`#time_keeper_update_modal_form [name="endtime"]`).val(value.endtime ? value.endtime.replace(" ", "T").slice(0, -3) : ``);
					});

					deleteButton.click(function(e) {
						e.preventDefault();
						if (confirm("Are you sure?")) {
							delete_workingtime({
								timeno: value.timeno
							});
						}
					});
				})(jQuery);
			});

			let hr = parseInt(totalElapsedTime / 3600, 10);
			let min = parseInt((totalElapsedTime % 3600) / 60, 10);
			let sec = parseInt(totalElapsedTime % 60, 10);
			$("#display_total_time_span").html(`${padZero(hr)}:${padZero(min)}:${padZero(sec)}`);

			let dataTable = $("#time_keeper_table").DataTable({
				"paging": false,
				"order": [
					[(ucatno == 19 ? 1 : 0), "desc"]
				]
			});
			$(dataTable.table().container()).addClass("table-responsive");
		}

		$("#time_keeper_update_modal_form").submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());

			json.timeno = parseInt($(this).data("timeno")) || 0;

			$.post(`php/ui/workingtime/update_workingtime.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$("#time_keeper_update_modal").modal("hide");
					get_employee_workingtime();
				}
			}, `json`);
		});

		function delete_workingtime(json) {
			$.post(`php/ui/workingtime/remove_workingtime.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_employee_workingtime();
				}
			}, `json`);
		}

		$("#startdate_input, #enddate_input").on("input", function(e) {
			get_employee_workingtime();
		});

		$("#set_today_button").click(() => setTimerToToday());
		$("#set_yesterday_button").click(() => setTimerToYesterday());
		$("#set_last_week_button").click(() => setTimerToLastWeek());
		$("#set_last_month_button").click(() => setTimerToLastMonth());
		$("#set_this_week_button").click(() => setTimerToCurrentWeek());
		$("#set_this_month_button").click(() => setTimerToCurrentMonth());

		function setTimerToToday() {
			let today = new Date();
			setTimeframe(new Date(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0), new Date(today.getFullYear(), today.getMonth(), today.getDate(), 23, 59, 59));
		}

		function setTimerToYesterday() {
			let today = new Date();
			setTimeframe(new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1, 0, 0, 0), new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1, 23, 59, 59));
		}

		function setTimerToLastWeek() {
			let beforeOneWeek = new Date(Date.now() - ONE_DAY_IN_SECOND * 7 * 1000);
			if (beforeOneWeek.getDay() == 0) {
				beforeOneWeek = new Date(Date.now() - ONE_DAY_IN_SECOND * 14 * 1000);
			}
			let day = beforeOneWeek.getDay(),
				diffToMonday = beforeOneWeek.getDay() - 1,
				lastMonday = new Date(beforeOneWeek.getTime() - ONE_DAY_IN_SECOND * diffToMonday * 1000),
				lastSunday = new Date(lastMonday.getTime() + ONE_DAY_IN_SECOND * 6 * 1000);
			setTimeframe(new Date(lastMonday.getFullYear(), lastMonday.getMonth(), lastMonday.getDate(), 0, 0, 0), new Date(lastSunday.getFullYear(), lastSunday.getMonth(), lastSunday.getDate(), 23, 59, 59));
		}

		function setTimerToLastMonth() {
			let timerStartDay = new Date();
			timerStartDay = new Date(timerStartDay.getFullYear(), timerStartDay.getMonth(), 0);
			timerStartDay.setDate(1);

			// 0 will result in the last day of the previous month
			let timerEndDay = new Date(timerStartDay.getFullYear(), timerStartDay.getMonth() + 1, 0);
			setTimeframe(new Date(timerStartDay.getFullYear(), timerStartDay.getMonth(), timerStartDay.getDate(), 0, 0, 0), new Date(timerEndDay.getFullYear(), timerEndDay.getMonth(), timerEndDay.getDate(), 23, 59, 59));
		}

		function setTimerToCurrentWeek() {
			let today = new Date();
			let thisDay = today.getDay(),
				diffToMonday = today.getDate() - thisDay + (thisDay == 0 ? -6 : 1);
			let monday = new Date(today.setDate(diffToMonday));
			let timerEndDay = new Date();
			setTimeframe(new Date(monday.getFullYear(), monday.getMonth(), monday.getDate(), 0, 0, 0), new Date(timerEndDay.getFullYear(), timerEndDay.getMonth(), timerEndDay.getDate(), 23, 59, 59));
		}

		function setTimerToCurrentMonth() {
			let timerStartDay = new Date();
			timerStartDay.setDate(1);
			let timerEndDay = new Date();
			setTimeframe(new Date(timerStartDay.getFullYear(), timerStartDay.getMonth(), timerStartDay.getDate(), 0, 0, 0), new Date(timerEndDay.getFullYear(), timerEndDay.getMonth(), timerEndDay.getDate(), 23, 59, 59));
		}
	</script>

	<script>
		// get_my_task_todo({
		// 	// scheduledate: `2022-05-09`
		// });

		function get_my_task_todo(json) {
			$.post(`php/ui/taskmanager/selection/get_my_task_todo.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
				}
			}, `json`);
		}
	</script>
</body>

</html>