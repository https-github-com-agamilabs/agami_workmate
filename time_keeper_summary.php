<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka");
	?>

	<style>
		.table-bordered thead th,
		.table-bordered thead td {
			border-bottom-width: 1px;
		}

		.vertical_text {
			writing-mode: vertical-lr;
			-webkit-transform: rotate(-180deg);
			-moz-transform: rotate(-180deg);
			transform: rotate(-180deg);
		}
	</style>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="card mb-3">
						<div class="card-body">
							<div class="row">
								<div class="col-md-6 input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text shadow-sm">Start Date</span>
									</div>
									<input id="time_keeper_startdate_input" name="startdate" class="form-control shadow-sm" type="date" value="<?= date('Y-m-d'); ?>" required>
								</div>

								<div class="col-md-6 input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text shadow-sm">End Date</span>
									</div>
									<input id="time_keeper_enddate_input" name="enddate" class="form-control shadow-sm" type="date" value="<?= date('Y-m-d'); ?>" required>
								</div>
							</div>

							<div class="d-flex flex-wrap justify-content-center">
								<div id="set_today_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">&nbsp;&nbsp;&nbsp;Today&nbsp;&nbsp;&nbsp;</div>
								<div id="set_yesterday_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">&nbsp;Yesterday&nbsp;</div>
								<div id="set_last_week_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">Last Week</div>
								<div id="set_last_month_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">Last Month</div>
								<div id="set_this_week_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">This Week</div>
								<div id="set_this_month_button" class="alert-primary shadow-sm border border-primary text-center px-2 py-1 rounded ml-1 mb-1" role="button">This Month</div>
							</div>

							<div class="table-responsive mt-3">
								<table id="time_keeper_summary_table" class="table table-sm table-striped table-bordered table-hover mb-0"> </table>
							</div>
						</div>
					</div>

					<div class="card mb-3">
						<div class="card-header">
							<h5 class="font-weight-bold">Summary</h5>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table-sm">
									<tbody>
										<tr>
											<th>Working Day</th>
											<th>:</th>
											<th id="working_day_cell">0d</th>
										</tr>
										<tr>
											<th>Holiday</th>
											<th>:</th>
											<th id="holiday_cell">0d</th>
										</tr>
										<tr>
											<th>Working Hour</th>
											<th>:</th>
											<th id="working_hour_cell">0h</th>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- Second card start -->

	<!-- Second card end -->
	<script>
		const MONTH_SHORT = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		const WEEK_DAY = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
		// const ONE_DAY_IN_SECOND = 86400; // 60 * 60 * 24

		// function padZero(value) {
		// 	return value.toString().padStart(2, 0);
		// }

		function formatDate(date = new Date()) {
			let dateTime = new Date(date);
			return `${padZero(dateTime.getDate())}-${padZero(dateTime.getMonth() + 1)}-${dateTime.getFullYear()}`;
		};

		// function formatDateToYYYYMMDD(date = new Date()) {
		// 	return `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}`;
		// }

		function groupArrayOfObjects(list, key) {
			return list.reduce((accumulator, currentValue) => {
				(accumulator[currentValue[key]] = accumulator[currentValue[key]] || []).push(currentValue);
				return accumulator;
			}, {});
		};

		// let savedAt = localStorage.getItem(`savedAt`);
		// let now = Date.now();

		if (now - savedAt < (6 * 60 * 60 * 1000) || formatDateToYYYYMMDD(new Date(savedAt)) == formatDateToYYYYMMDD(new Date(now))) {
			$("#time_keeper_startdate_input").val(localStorage.getItem(`startdate`));
			$("#time_keeper_enddate_input").val(localStorage.getItem(`enddate`));
		}

		$("#time_keeper_startdate_input, #time_keeper_enddate_input").on("input", function(e) {
			get_employee_elapsedtime();
		});

		get_employee_elapsedtime();

		function get_employee_elapsedtime() {
			let json = {
				startdate: $("#time_keeper_startdate_input").val(),
				enddate: $("#time_keeper_enddate_input").val()
			};

			localStorage.setItem(`startdate`, json.startdate);
			localStorage.setItem(`enddate`, json.enddate);
			localStorage.setItem(`savedAt`, Date.now());

			$("#time_keeper_summary_table").empty();
			$(`#working_day_cell`).html(`0d`);
			$(`#holiday_cell`).html(`0d`);
			$(`#working_hour_cell`).html(`0h`);

			$.post(`php/ui/workingtime/get_emp_elapsedtime.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					get_holidays({
						start_date: json.startdate,
						end_date: json.enddate
					}, resp.data);
				}
			}, `json`);
		}

		const FOUR_HOUR = 14400; //4*60*60
		const EIGHT_HOUR = 28800; //8*60*60
		const HOLIDAY_TYPE = [``];

		function get_holidays(json, empElapsedTime) {
			$.post("php/ui/holiday/get_holidays.php", json, (resp) => {
				if (resp.error) {
					toastr.error(resp.message);
				}

				show_employee_elapsedtime(json.start_date, json.end_date, empElapsedTime, resp.data);
			}, "json");
		}

		function show_employee_elapsedtime(startDateStr, endDateStr, data, holidays = []) {
			$("#time_keeper_summary_table")
				.append(`<thead>
						<tr>
							<th rowspan="2">Name</th>
						</tr>
						<tr></tr>
					</thead>
					<tbody></tbody>`);

			let totalWorkingTime = 0,
				fullDay = 0,
				halfDay = 0,
				lastMonthIndex = -1;

			// workingDates calculated if date exists
			// let workingdates = [...new Set(data.map(a => a.workingdate))];
			// workingdates.sort((a, b) => new Date(a) - new Date(b));

			// workingDates calculated from the selected range
			let workingdates = [];
			const date = new Date(startDateStr);
			const endDate = new Date(endDateStr);

			while (date <= endDate) {
				workingdates.push(new Date(date).toISOString().slice(0, 10));
				date.setDate(date.getDate() + 1);
			}

			// console.log(workingdates);

			$.each(workingdates, (index, value) => {
				let date = new Date(value);
				let dayOfWeek = date.getDay();
				let monthIndex = date.getMonth();
				let holiday = holidays.find(a => a.holidaydate == value && a.minworkinghour == 0);

				if (!holiday) {
					if (dayOfWeek == 4) {
						totalWorkingTime += FOUR_HOUR;
						halfDay++;
					} else if ((0 <= dayOfWeek && dayOfWeek <= 3) || dayOfWeek == 6) {
						totalWorkingTime += EIGHT_HOUR;
						fullDay++;
					}
				}

				if (lastMonthIndex != monthIndex) {
					lastMonthIndex = monthIndex;
					$(`#time_keeper_summary_table thead tr:nth-child(1)`)
						.append(`<th colspan="" class="text-center">${MONTH_SHORT[monthIndex]}, ${date.getFullYear()}</th>`);
				} else {
					let lastCell = $(`#time_keeper_summary_table thead tr:nth-child(1) th:last`);
					lastCell.attr("colspan", parseInt((lastCell.attr("colspan") || 1)) + 1);
				}

				$("#time_keeper_summary_table")
					.find("thead>tr:nth-child(2)")
					.append(`<th class="text-center${holiday ? ` table-alternate` : ``}"
						data-toggle="tooltip" data-html="true" data-placement="top"
						title="${formatDate(date)} (${WEEK_DAY[dayOfWeek]})${holiday ? `<br>${holiday.reasontext || holiday.hdtypeid}` : ``}">
							${padZero(date.getDate())}
						</th>`);
			});

			$("#time_keeper_summary_table")
				.find("thead>tr:first")
				.append(`<th rowspan="2" class="text-right"><i class="fas fa-greater-than-equal"></i> 8h</th>
					<th rowspan="2" class="text-right"><i class="fas fa-less-than"></i> 8h & <i class="fas fa-greater-than-equal"></i> 4h</th>
					<th rowspan="2" class="text-right"><i class="fas fa-less-than"></i> 4h</th>
					<th rowspan="2" class="text-center">Total <br>Time</th>
					<th rowspan="2" class="text-center">Deficient <br>Time</th>
					<th rowspan="2" class="text-center">Additional <br>Time</th>`);

			let totalElapsedTime = 0;
			let successTimeCount = 0;
			let warningTimeCount = 0;
			let dangerTimeCount = 0;
			let separetorClass = "";

			let groupedData = groupArrayOfObjects(data, "empno");

			$.each(groupedData, (empno, value) => {
				totalElapsedTime = 0;
				successTimeCount = 0;
				warningTimeCount = 0;
				dangerTimeCount = 0;

				let tbodyRow = $("<tr>")
					.append(`<td>${value[0].empfullname}</td>`)
					.appendTo("#time_keeper_summary_table>tbody");

				$.each(workingdates, (index2, value2) => {
					let timeData = value.find(a => a.workingdate == value2);
					let isFriday = ((new Date(value2)).getDay() == 5);
					let holiday = holidays.find(a => a.holidaydate == value2 && a.minworkinghour == 0);

					if (timeData) {
						if (timeData.dailyelapsedtime >= EIGHT_HOUR) {
							successTimeCount++;
							separetorClass = `table-success`;
						} else if (timeData.dailyelapsedtime >= FOUR_HOUR) {
							warningTimeCount++;
							separetorClass = `table-warning`;
						} else {
							dangerTimeCount++;
							separetorClass = `table-danger`;
						}

						if (holiday) {
							separetorClass = `table-alternate`;
						}

						let hr = parseInt(timeData.dailyelapsedtime / 3600, 10) || 0;
						let min = parseInt((timeData.dailyelapsedtime % 3600) / 60, 10) || 0;
						totalElapsedTime += (parseInt(timeData.dailyelapsedtime, 10) || 0);

						tbodyRow
							.append(`<td class="${separetorClass} text-center">
								${padZero(hr)}h ${padZero(min)}m
							</td>`);
					} else {
						if (holiday) {
							separetorClass = `table-alternate`;
						} else {
							separetorClass = ``;
						}

						tbodyRow
							.append(`<td class="${separetorClass} text-center"${holiday ? ` data-holidayno="${holiday.holidayno}"` : ``}>
								${holiday ? `<div class="vertical_text">${holiday.reasontext || holiday.hdtypeid}</div>` : `-`}
							</td>`);
					}
				});

				tbodyRow.append(`<td class="text-success text-right">${successTimeCount}</td>
					<td class="text-warning text-right">${warningTimeCount}</td>
					<td class="text-danger text-right">${dangerTimeCount}</td>`);

				let hr = parseInt(totalElapsedTime / 3600, 10) || 0;
				let min = parseInt((totalElapsedTime % 3600) / 60, 10) || 0;

				tbodyRow.append(`<td class="text-center">${padZero(hr)}h ${padZero(min)}m</td>`);

				if (totalElapsedTime < totalWorkingTime) {
					hr = parseInt((totalWorkingTime - totalElapsedTime) / 3600, 10) || 0;
					min = parseInt(((totalWorkingTime - totalElapsedTime) % 3600) / 60, 10) || 0;
					tbodyRow.append(`<td class="text-center">${padZero(hr)}h ${padZero(min)}m</td> <td class="text-center">-</td>`);
				} else if (totalElapsedTime > totalWorkingTime) {
					hr = parseInt((totalElapsedTime - totalWorkingTime) / 3600, 10) || 0;
					min = parseInt(((totalElapsedTime - totalWorkingTime) % 3600) / 60, 10) || 0;
					tbodyRow.append(`<td class="text-center">-</td> <td class="text-center">${padZero(hr)}h ${padZero(min)}m</td>`);
				} else {
					tbodyRow.append(`<td class="text-center">-</td> <td class="text-center">-</td>`);
				}
			});

			$.each(holidays, (index, value) => {
				let cells = $(`#time_keeper_summary_table>tbody [data-holidayno="${value.holidayno}"]`);

				if (Object.keys(groupedData).length == cells.length) {
					$(cells[0]).attr(`rowspan`, cells.length);
					cells.not($(cells[0])).remove();
				}
			});

			$(`#working_day_cell`).html(`${halfDay + fullDay}d (${halfDay} + ${fullDay})`);
			$(`#holiday_cell`).html(`${holidays.filter(a => a.minworkinghour == 0).length}d`);
			$(`#working_hour_cell`).html(`${halfDay * 4 + fullDay * 8}h (${halfDay} * 4 + ${fullDay} * 8)`);
		}

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

		function setTimeframe(fromDate, toDate) {
			$("#time_keeper_startdate_input").val(formatDateToYYYYMMDD(fromDate));
			$("#time_keeper_enddate_input").val(formatDateToYYYYMMDD(toDate));
			get_employee_elapsedtime();
		}
	</script>

</body>

</html>