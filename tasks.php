<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	date_default_timezone_set("Asia/Dhaka");
	include_once("header.php");
	?>

	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

	<script src="./js/basic_crud_type_1.js"></script>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div id="task_card">
						<div class="card mb-3">
							<div class="card-header">
								<div class="font-weight-bold h5 mb-0">Tasks</div>
							</div>
							<div class="card-body">
								<form class="filter_form">
									<div class="row align-items-end">
										<div class="col-lg-6">
											<div class="form-group">
												<label class="d-block mb-0">
													<div class="font-weight-bold mb-2">Channel</div>
													<select name="channelno" class="form-control shadow-sm" style="width: 100%;"></select>
												</label>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="form-group">
												<label class="d-block mb-0">
													<div class="font-weight-bold mb-2">Task Filter For</div>
													<select name="assignedto" class="form-control shadow-sm">
														<option value="<?= $userno ?>">Myself</option>
													</select>
												</label>
											</div>
										</div>
										<div class="col-lg-6 font-weight-bold">
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
										<div class="col-lg-6">
											<div class="form-group">
												<label class="d-block font-weight-bold mb-0">
													Date
													<div id="task_filter_date_input" name="date" class="form-control shadow-sm mt-2" style="cursor: pointer;">
														<i class="fa fa-calendar"></i>&nbsp;
														<span></span>
													</div>
												</label>
											</div>
										</div>
										<div class="col-lg-12 text-right">
											<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow mb-2" type="submit">
												<i class="fa fa-search mr-1"></i> Search
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>

						<div id="task_container" class=""></div>
					</div>

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

		get_channels_available_task();

		function get_channels_available_task() {
			$.post(`php/ui/taskmanager/selection/get_channels_available_task.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_channels_available_task(resp.data);
				}
			}, `json`);
		}

		function show_channels_available_task(result) {
			let select1 = $(`#task_card .filter_form [name="channelno"]`).empty();

			$.each(result, (index, value) => {
				let optgroup1 = $(`<optgroup>`).attr("label", value.channeltitle).appendTo(select1);

				$.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
					optgroup1.append(new Option(`${valueOfSubChannels.channeltitle} ${valueOfSubChannels.availabletask ? `(${valueOfSubChannels.availabletask})` : ``}`, valueOfSubChannels.channelno));
				});
			});

			select1
				.select2({
					placeholder: "Select Channel...",
					allowClear: true
				});

			let channelno = localStorage.getItem(`channelno`);
			if (channelno > 0) {
				select1.val(channelno).trigger("change");
			}
		}

		get_my_fellow();

		function get_my_fellow() {
			let select = $(`#task_card .filter_form [name="assignedto"]`).empty();

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

					// get_filtered_task();
				}
			}, `json`);
		}

		/*
		// TASK
		*/

		class Task extends BasicCRUD {
			show(data) {
				let thisObj = this;
				let today = `<?= date('Y-m-d'); ?>`,
					start = ``,
					delay = {},
					cardClass = ``;

				$.each(data, (index, value) => {
					start = value.deadlines.length ? value.deadlines[value.deadlines.length - 1].deadline : value.scheduledate;

					if (value.progress.find(a => a.wstatusno == 4) != null) {
						cardClass = ` border-left border-danger card-shadow-danger`;
					} else if (value.progress.find(a => a.wstatusno == 3) != null) {
						if (value.deadlines && value.deadlines.length > 1) {
							cardClass = ` border-left border-warning card-shadow-warning`;
						} else {
							cardClass = ` border-left border-success card-shadow-success`;
						}
					} else if (value.progress.find(a => a.wstatusno == 2) != null) {
						cardClass = ` border-left border-info card-shadow-info`;
						delay = delayedDate(today, start);
					} else {
						cardClass = ``;
						delay = delayedDate(today, start);
					}

					// console.log(`delay =>`, delay);

					let template = $(`<div class="card mb-3${cardClass}">
							<div class="card-header justify-content-between" style="height:auto;">
								<div class="my-md-1">
									<div class="d-flex flex-wrap justify-content-center justify-content-md-start">
										<div class="bg-info text-white rounded text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${value.channeltitle}</div>
										<div class="alert alert-info text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${value.priorityleveltitle} (${value.relativepriority})</div>
										${delay.days_diff > 0
											? `<div class="alert alert-danger text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${delay.days_diff} day(s) behind</div>`
											: ``}
										${(delay.days_diff <= 0 && delay.hours_diff > 0)
											? `<div class="alert alert-danger text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${delay.hours_diff} hour(s) behind</div>`
											: ``}
									</div>
									<div class="small mt-1">
										<div style="text-transform:none;">
											${value.storyphasetitle},
											Points: ${value.points},
											By: ${value.assignedby || ``},
											${value.assignee ? `<span>To: ${value.assignee}</span>` : ``}
											<span>
												[${formatDate(value.scheduledate)} to
												${value.deadlines.map((obj, i) =>
													`<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ")}]
											</span>
										</div>
									</div>
								</div>
								${UCAT_NO == 19 || value.assignedto == USER_NO
									? `<button class="status_button btn btn-sm btn-info custom_shadow" type="button">Update Status</button>`
									: ``
								}
							</div>
							<div class="card-body py-2">
								<div>${value.story}</div>
								${value.howto ? `<hr> <div>${deNormaliseUserInput(value.howto)}</div>` : ``}
							</div>
							<div class="card-footer p-2">
								<div class="w-100 px-2 py-1">
									${value.progress.length
										? value.progress
											.map(b => `<div class="media mb-3">
												<div class="mr-2">${formatDateTime(b.progresstime)}</div>
												<div class="media-body">
													<div>${b.statustitle} (${b.entryby})</div>
													<div>${deNormaliseUserInput(b.result)}</div>
												</div>
											</div>`)
											.join("")
										: ``
									}
								</div>
							</div>
						</div>`)
						.appendTo(this.targetContainer);

					(function($) {
						$(`.status_button`, template).click(function(e) {
							$("#status_update_modal").modal("show");
							$(`#status_update_modal_form`).data("cblscheduleno", value.cblscheduleno).data("cblprogressno", -1);
						});
					})(jQuery);

					(function($) {
						thisObj.editButtonTrigger(template, value);

						thisObj.deleteButtonTrigger(template, value);
					})(jQuery);
				});
			}
		}

		const task = new Task({
			readURL: `php/ui/taskmanager/selection/get_filtered_backlog.php`,
			createURL: `php/ui/syllabus/insert_a_syllabus.php`,
			updateURL: `php/ui/syllabus/update_a_syllabus.php`,
			deleteURL: `php/ui/syllabus/delete_a_syllabus.php`,
			targetCard: `#task_card`,
			targetContainer: `#task_container`,
			setupModal: `#task_modal`,
			topic: `Task`,
			tablePK: `sylno`
		});

		task.get();
	</script>

</body>

</html>