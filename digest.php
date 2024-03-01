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

					<ul class="tabs-animated-shadow tabs-animated nav">
						<li class="nav-item">
							<a role="tab" class="nav-link active" id="tab-c-0" data-toggle="tab" href="#all_employee_gantt_chart_tab">
								<span>All Employee</span>
							</a>
						</li>
						
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="all_employee_gantt_chart_tab" role="tabpanel">
							<!-- ALL EMPLOYEE GANTT CHART CARD -->
							<div class="card mb-3">
								<div class="card-header">
									<h5 class="font-weight-bold mb-0">All Employee Activity Digest</h5>
								</div>
								<div class="card-body">
									<form id="employee_digest_filter_form">
										<div class="row">
											<div class="col-md-4 input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Start Date</span>
												</div>
												<input name="startdate" type="date" class="form-control shadow-sm" value="<?= date("Y-m-d", strtotime("-3 day")); ?>">
											</div>

											<div class="col-md-4 input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">End Date</span>
												</div>
												<input name="enddate" type="date" class="form-control shadow-sm" value="<?= date("Y-m-d", strtotime("6 day")); ?>">
											</div>

											<div class="col-md-4 mt-1 mb-3">
												<button class="btn btn-sm btn-primary font-weight-bold rounded-pill px-4 shadow" type="submit">Filter</button>
											</div>
										</div>
									</form>

									<div class="table-responsive shadow-sm border rounded">
										<table id="employee_digest_table" class="table table-sm table-bordered mb-0"></table>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		const TABLE_CLASS = ["table-primary", "table-secondary", "table-success", "table-danger", "table-warning", "table-info"];
		const MONTH_SHORT = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

		const dateToYYYYmmdd = (d) => [padZero(d.getDate()), MONTH_SHORT[d.getMonth()], d.getFullYear()].join(" ");

		function groupArrayOfObjects(list = [], key = "") {
			return list.reduce((accumulator, currentValue) => {
				accumulator[currentValue[key]] = [...accumulator[currentValue[key]] || [], currentValue];
				return accumulator;
			}, {});
		};
	</script>

	<script>
		$("#employee_digest_filter_form").submit(function(e) {
			e.preventDefault();
			get_employee_digest_data();
		});

		get_employee_digest_data();

		function get_employee_digest_data() {
			$("#employee_digest_table").empty();

			let json = Object.fromEntries((new FormData($("#employee_digest_filter_form")[0])).entries());

			$.post(`php/ui/report/produce_digest.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					$("#employee_digest_table").append(`<tbody> <tr> <th class="text-center"><h4>${resp.message}</h4></th> </tr> </tbody>`);
				} else {
					show_ganttchart_data(resp.digest);
				}
			}, `json`);
		}

		function show_ganttchart_data(data) {
			$("#employee_digest_table").append(`<thead>
					<tr>
						<th rowspan="2" class="text-left">Name</th>
					</tr>
					<tr></tr>
				</thead>
				<tbody></tbody>`);

			let dates = [];
			let json = Object.fromEntries((new FormData($("#employee_digest_filter_form")[0])).entries());

			let date = new Date(json.startdate);
			let enddate = new Date(json.enddate);
			while (date <= new Date(enddate)) {
				dates = [...dates, new Date(date)];
				date.setDate(date.getDate() + 1);
			}
			
			let lastMonthIndex = -1;

			$.each(dates, (indexInDates, valueOfDates) => {
				let monthIndex = valueOfDates.getMonth();

				if (lastMonthIndex != monthIndex) {
					lastMonthIndex = monthIndex;
					$(`#employee_digest_table thead tr:nth-child(1)`).append(`<th colspan="" class="text-center">${MONTH_SHORT[monthIndex]}, ${valueOfDates.getFullYear()}</th>`);
				} else {
					let lastCell = $(`#employee_digest_table thead tr:nth-child(1) th:last`);
					lastCell.attr("colspan", parseInt((lastCell.attr("colspan") || 1)) + 1);
				}

				$("#employee_digest_table")
					.find("thead>tr:nth-child(2)")
					.append(`<th class="text-center" title="${dateToYYYYmmdd(valueOfDates)}" data-toggle="tooltip" data-placement="top" data-html="true">
							${padZero(valueOfDates.getDate())}
						</th>`);
			});

			let groupedData = groupArrayOfObjects(data, `empno`);

			$.each(groupedData, (indexInData, valueOfData) => {
				let row = $(`<tr>
						<td class="text-left">${valueOfData[0].firstname} ${valueOfData[0].lastname?valueOfData[0].lastname:``}</td>
						</tr>`)
					.appendTo(`#employee_digest_table tbody`);

				$.each(dates, (indexInDates, valueOfDates) => {
					let filteredData = valueOfData.filter(a => {
						let startdate = new Date(a.startdate),
							enddate = new Date(a.enddate),
							extendeddate = a.extendeddate ? new Date(a.extendeddate) : enddate,
							lastprogressdate = a.lastprogressdate ? new Date(a.lastprogressdate) : enddate;

						return startdate <= valueOfDates && valueOfDates <= enddate && valueOfDates <= extendeddate && valueOfDates <= lastprogressdate;
					});

					if (filteredData.length) {
						row.append(`<td class="${TABLE_CLASS[indexInData % TABLE_CLASS.length]} text-truncate"
										title="${filteredData
											.map(a =>
												`${a.channeltitle} [${dateToYYYYmmdd(new Date(a.startdate))} - ${dateToYYYYmmdd(new Date(a.enddate))}]`
												)
											.join(", ")}">
									</td>`);
					} else {
						row.append(`<td></td>`);
					}
				});
			});
		}
	</script>
</body>

</html>