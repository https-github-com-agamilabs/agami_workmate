<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css" />
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

	<script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.colVis.min.js"></script>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<!-- TASK MANAGER CARD -->
					<div class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold mb-0">Task Manager</h5>
							<button id="task_manager_add_button" class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow" type="button">
								<i class="fa fa-plus-circle mr-1"></i> Add
							</button>
						</div>
						<div class="card-body">
							<form id="task_manager_filter_form">
								<div class="row">
									<div class="col-md-6">
										<div class="input-group mb-3">
											<!-- <div class="input-group-prepend">
												<span class="input-group-text shadow-sm">Channel</span>
											</div> -->
											<select name="channelno" class="form-control shadow-sm" style="width: 100%;" required></select>
										</div>
									</div>

									<div class="col-md-6">
										<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow mx-1" type="submit">
											<i class="fa fa-search mr-1"></i> Search
										</button>
									</div>
								</div>
							</form>

							<!-- TASK MANAGER TABLE -->
							<div class="table-responsive mb-3">
								<table id="task_manager_table" class="table table-sm table-striped table-hover table-bordered mb-0">
									<thead class="table-primary">
										<tr>
											<th style="width: 40px;">SL</th>
											<th style="width: 150px;">Story Phase</th>
											<th>Story</th>
											<th style="width: 150px;">Priority</th>
											<?php
											if ($ucatno == 19) : ?>
												<th class="text-center" style="width: 50px;">Status</th>
											<?php endif; ?>
											<th class="text-center" style="width: 50px;">Action</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>

							<!-- TASK MANAGER TABLE PAGINATION -->
							<div class="card rounded-pill">
								<div class="card-body" style="padding:5px;color:white;font-size:24px">
									<div class="d-flex justify-content-between">
										<div class="pagination-button rounded-pill" id="task_manager_table_previous_page">
											<i class="fa fa-arrow-left mb-2"></i>
										</div>
										<div class="pagination-pageno rounded-pill">
											<div style="font-size:16px;" id="task_manager_table_pageno_div" class="font-weight-bold mt-2">Page: 1</div>
											<input id="task_manager_table_pageno_input" placeholder="Enter Page No" class="text-primary form-control rounded-pill" type="text" value="1" style="padding:0px;margin:0px 0px;border:none;display:none;text-align:center;font-size:16px;">
										</div>
										<div class="pagination-button rounded-pill" id="task_manager_table_next_page">
											<i class="fa fa-arrow-right mb-2"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="task_detail_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Task Detail</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body pt-0"> </div>
			</div>
		</div>
	</div>

	<div id="task_manager_setup_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form id="task_manager_setup_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Task</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12 form-group">
								<label class="d-block mb-0">
									Channel <i class="fa fa-star-of-life small text-danger mb-2"></i>
									<select name="channelno" class="form-control shadow-sm" style="width: 100%;" required></select>
								</label>
							</div>

							<div class="col-md-12 form-group">
								<label class="d-block mb-0">
									Story <span class="text-danger">*</span>
									<textarea name="story" class="form-control shadow-sm mt-2" placeholder="Story..." rows="3" required></textarea>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Points <span class="text-danger">*</span>
									<input name="points" class="form-control shadow-sm mt-2" type="number" min="0" placeholder="Points..." required>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Story Phase <span class="text-danger">*</span>
									<select name="storyphaseno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Priority Level <span class="text-danger">*</span>
									<select name="prioritylevelno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Priority Value
									<input name="relativepriority" class="form-control shadow-sm mt-2" type="number" min="0" placeholder="Priority Value...">
								</label>
							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="assign_task_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form id="assign_task_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Assign Task</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-4 form-group">
								<label class="d-block mb-0">
									Assigned To <span class="text-danger">*</span>
									<select name="assignedto" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-md-4 form-group">
								<label class="d-block mb-0">
									Start Date <span class="text-danger">*</span>
									<input name="scheduledate" class="form-control shadow-sm mt-2" type="date" required>
								</label>
							</div>

							<div class="col-md-4 form-group">
								<label class="d-block mb-0">
									Duration
									<input name="duration" class="form-control shadow-sm mt-2" type="number" step="0.01">
								</label>
							</div>
						</div>

						<h5 class="font-weight-bold">How to solve (Tips)</h5>

						<div id="task_how_to_solve_container">
							<p></p>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Assign</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="deadline_add_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="deadline_add_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Modify Deadline</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block mb-0">
								Deadline <span class="text-danger">*</span>
								<input name="deadline" class="form-control shadow-sm mt-2" type="date" required>
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
		const PERMISSION_LEVEL = <?= $_SESSION['cogo_permissionlevel']; ?>;
		const USERNO = <?= $_SESSION['cogo_userno']; ?>;
		let howToSolveTextEditor;

		ClassicEditor
			.create(document.querySelector("#task_how_to_solve_container"), {
				// plugins: [Base64UploadAdapter]
			})
			.then(editor => {
				howToSolveTextEditor = editor;
				// console.log(editor);
			})
			.catch(error => {
				console.error(error);
			});

		function formatDate(date) {
			date = new Date(date);
			return date.getDate().toString().padStart(2, 0) + "-" + (date.getMonth() + 1).toString().padStart(2, 0) + "-" + date.getFullYear();
		}

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
			$(`#task_manager_filter_form [name="channelno"], #task_manager_setup_modal_form [name="channelno"]`).empty();

			$.each(result, (index, value) => {
				let optgroup1 = $(`<optgroup>`).attr("label", value.channeltitle).appendTo(`#task_manager_filter_form [name="channelno"]`);
				let optgroup2 = $(`<optgroup>`).attr("label", value.channeltitle).appendTo(`#task_manager_setup_modal_form [name="channelno"]`);

				$.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
					optgroup1.append(new Option(`${valueOfSubChannels.channeltitle} ${valueOfSubChannels.availabletask ? `(${valueOfSubChannels.availabletask})` : ``}`, valueOfSubChannels.channelno));
					optgroup2.append(new Option(valueOfSubChannels.channeltitle, valueOfSubChannels.channelno));
				});
			});

			$(`#task_manager_filter_form [name="channelno"]`)
				.select2({
					placeholder: "Select Channel...",
					allowClear: true
				});

			$(`#task_manager_setup_modal_form [name="channelno"]`)
				.select2({
					placeholder: "Select Channel...",
					allowClear: true
				});

			let channelno = localStorage.getItem(`channelno`);
			if (channelno > 0) {
				$(`#task_manager_filter_form [name="channelno"]`).val(channelno).trigger("change");
			}

			get_channel_backlogs(1);
		}

		get_story_phase();

		function get_story_phase() {
			let select = $(`#task_manager_setup_modal_form [name="storyphaseno"]`).empty();

			$.post(`php/ui/taskmanager/selection/list_storyphase.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.results, (index, value) => {
						select.append(new Option(value.storyphasetitle, value.storyphaseno));
					});
				}
			}, `json`);
		}

		get_priority_level();

		function get_priority_level() {
			let select = $(`#task_manager_setup_modal_form [name="prioritylevelno"]`).empty();

			$.post(`php/ui/taskmanager/selection/list_prioritylevel.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.results, (index, value) => {
						select.append(new Option(value.priorityleveltitle, value.prioritylevelno));
					});
				}
			}, `json`);
		}

		get_my_fellow();

		function get_my_fellow() {
			let select = $(`#assign_task_modal_form [name="assignedto"]`).empty();

			$.post(`php/ui/taskmanager/selection/get_my_fellow.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.results, (index, value) => {
						select.append(new Option(value.userfullname, value.userno));
					});
				}
			}, `json`);
		}

		$("#task_manager_filter_form").submit(function(e) {
			e.preventDefault();
			let pageno = $("#task_manager_table_pageno_input").val();
			get_channel_backlogs(pageno);
		});

		$("#task_manager_table_pageno_input").on("keyup", function(e) {
			if (e.keyCode == 13) {
				let pageno = parseFloat($(this).val()) || 1;
				get_channel_backlogs(pageno);
				$(this).hide();
				$(this).siblings().text(`Page: ${pageno}`).show();
			}
		});

		$(document).on("click", "#task_manager_table_pageno_div", function(e) {
			$(this).hide();
			$(this).siblings().show().select();
		});

		$("#task_manager_table_previous_page").on("click", function(e) {
			let pageno = $("#task_manager_table_pageno_input").val();
			if (pageno == 1) {
				toastr.warning("Current page is the first page");
			} else {
				--pageno;
				$("#task_manager_table_pageno_div").text(`Page: ${pageno}`);
				$("#task_manager_table_pageno_input").val(pageno);
				get_channel_backlogs(pageno);
			}
		});

		$("#task_manager_table_next_page").on("click", function(e) {
			let pageno = $("#task_manager_table_pageno_input").val();
			++pageno;
			$("#task_manager_table_pageno_div").text(`Page: ${pageno}`);
			$("#task_manager_table_pageno_input").val(pageno);
			get_channel_backlogs(pageno);
		});

		function get_channel_backlogs(pageno) {
			if ($.fn.DataTable.isDataTable("#task_manager_table")) {
				$("#task_manager_table").DataTable().clear().destroy();
			}

			$(`#task_manager_table tbody`).empty();

			let json = Object.fromEntries((new FormData($("#task_manager_filter_form")[0])).entries());
			json.pageno = pageno;
			json.limit = 10;

			localStorage.setItem(`channelno`, json.channelno);

			$.post(`php/ui/taskmanager/backlog/get_channel_backlogs.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else if (resp.results.length) {
					show_channel_backlogs(resp.results);
				} else {
					$(`<tr>
							<td colspan="6">
								<div class="text-center text-secondary w-100">
									<div class="py-4">
										<i class="fas fa-calendar-alt fa-3x"></i>
										<h5 class="text-500 font-weight-normal mb-0">No Story found</h5>
									</div>
								</div>
							</td>
						</tr>`)
						.appendTo(`#task_manager_table tbody`);
				}
			}, `json`);
		}

		let dataTable = $("#task_manager_table").DataTable();
		$(dataTable.table().container()).addClass("table-responsive");

		function show_channel_backlogs(data) {
			dataTable = $("#task_manager_table").DataTable({
				data: data,
				paging: false,
				info: false,
				dom: "lBfrtip",
				buttons: [{
						extend: 'excelHtml5',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'pdfHtml5',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: "print",
						exportOptions: {
							columns: ":visible",
						},
					},
					"colvis",
				],
				columnDefs: [{
					// default visible columns; if visible = false, column is hidden
					targets: 0,
					visible: true,
				}, ],
				fnRowCallback: (nRow, aData, iDisplayIndex, iDisplayIndexFull) => {
					if (aData.schedule && aData.schedule.length) {
						/*
						 * all assigned task is rejected, means abondoned
						 * or, all non-rejected task is completed, means completed
						 * otherwise, in-progress
						 */

						if (aData.schedule.filter(a => a.progress.find(b => b.wstatusno == 4) != null).length == aData.schedule.length) {
							$(nRow).addClass(`table-danger`);
						} else if (aData.schedule.filter(a => a.progress.find(b => b.wstatusno == 4 || b.wstatusno == 3) != null).length == aData.schedule.length) {
							if (aData.schedule.deadlines && aData.schedule.deadlines.length > 1) {
								$(nRow).addClass(`table-warning`);
							} else {
								$(nRow).addClass(`table-success`);
							}
						} else if (aData.schedule.length) {
							$(nRow).addClass(`table-info`);
						}
					}

					let pageno = Number($("#task_manager_table_pageno_input").val()) - 1;
					let limit = 10;
					let slno = (limit * pageno) + iDisplayIndexFull + 1;

					$("td:first", nRow).html(slno);
					return nRow;
				},
				columns: [{
						data: null,
						"searchable": false,
						"sortable": false
					},
					{
						data: "storyphasetitle",
					},
					{
						data: "story",
						render: (data, type, row, meta) => {
							return row.story;
						}
					},
					{
						data: "priorityleveltitle",
						render: (data, type, row, meta) => {
							return `${row.priorityleveltitle} (${row.relativepriority})`;
						}
					},
					<?php
					if ($ucatno == 19) : ?> {
							data: "approved",
							searchable: false,
							sortable: false,
							render: (data, type, row, meta) => {
								return $("<div>")
									.append(`

										${(row.approved == 1) ?
											`<div class="badge badge-info mb-2">Approved</div>
											<button class="approve_button btn btn-sm btn-danger font-weight-bold rounded-pill px-3 custom_shadow" type="button">Reject</button>` :
											`<div class="badge badge-info mb-2">Rejected</div>
											<button class="approve_button btn btn-sm btn-success font-weight-bold rounded-pill px-3 custom_shadow" type="button">Approve</button>`
										}

								`)
									.html();
							}
						},
					<?php endif; ?> {
						data: "backlogno",
						searchable: false,
						sortable: false,
						render: () => {
							return $("<div>")
								.append(`<div class="d-flex justify-content-center">
									<button class="detail_button btn btn-sm btn-primary rounded-pill px-3 custom_shadow m-1" type="button">Detail</button>
								</div>`)
								.html();
						}
					}
				],
			});

			$(dataTable.table().container()).addClass("table-responsive");
		}

		$(document).on(`click`, `.detail_button`, function() {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();
			$("#task_detail_modal").modal("show");
			show_task_detail(data);
		});

		function show_task_detail(data) {
			$(`#task_detail_modal .modal-body`).empty();

			let card = $(`<div class="card shadow-none">
						<div class="card-header justify-content-between px-0">
							<div class="alert alert-info text-center px-2 py-1 mb-0" style="width: max-content;">${data.storyphasetitle}</div>
							<div class="d-flex justify-content-center">
								<button class="create_subtask_button btn btn-sm btn-dark rounded-circle custom_shadow m-1" type="button" title="Create Sub-task" data-toggle="tooltip" data-placement="top">
									<i class="fas fa-tasks"></i>
								</button>
								<button class="assign_task_button btn btn-sm btn-alternate rounded-circle custom_shadow m-1" type="button" title="Assign task" data-toggle="tooltip" data-placement="top">
									<i class="fas fa-user-plus"></i>
								</button>
								<button class="edit_button btn btn-sm btn-info rounded-circle custom_shadow m-1" type="button" title="Edit task" data-toggle="tooltip" data-placement="top">
									<i class="far fa-edit"></i>
								</button>
								<?php
								if ($ucatno == 19) : ?>
									<button class="delete_button btn btn-sm btn-danger rounded-circle custom_shadow m-1" type="button" title="Delete task" data-toggle="tooltip" data-placement="top">
										<i class="fas fa-trash-alt"></i>
									</button>
								<?php endif; ?>
							</div>
						</div>
						<div class="card-body px-0 py-2">
							<div class="d-flex flex-wrap">
								<div class="alert alert-primary text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">Points: ${data.points}</div>
								<div class="alert alert-danger text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">Priority: ${data.priorityleveltitle} (${data.relativepriority})</div>
								<div class="alert alert-info text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">By: ${data.assignedby || ``}</div>
							</div>
							<div class="mb-2">${data.story}</div>
							${data.schedule.length
								? data.schedule
									.map(a => `<div class="single_schedule w-100 shadow-sm p-2 border rounded mb-2">
										<div class="d-flex flex-wrap-reverse justify-content-between">
											<div>Assignee: ${a.assignee}</div>
											<div class="d-flex flex-wrap-reverse justify-content-end">
												<div>
													[${formatDate(a.scheduledate)}
													to
													${a.deadlines.map((obj, i) => `<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ")}]
												</div>
												<?php
												if ($ucatno == 19) : ?>
													<button data-cblscheduleno="${a.cblscheduleno}" class="modify_deadline_button btn btn-sm btn-info custom_shadow ml-2" type="button" title="Modify Deadline" data-toggle="tooltip" data-placement="top">
														Modify Deadline
													</button>
													<button data-cblscheduleno="${a.cblscheduleno}" class="schedule_edit_button btn btn-sm btn-info rounded-circle custom_shadow ml-2" type="button" title="Edit Schedule" data-toggle="tooltip" data-placement="top">
														<i class="far fa-edit"></i>
													</button>
													<button data-cblscheduleno="${a.cblscheduleno}" class="schedule_delete_button btn btn-sm btn-danger rounded-circle custom_shadow ml-1" type="button" title="Delete Schedule" data-toggle="tooltip" data-placement="top">
														<i class="fas fa-trash-alt"></i>
													</button>
												<?php endif; ?>
											</div>
										</div>
										<div class='text-bold'>How to solve (Tips)</div>
										<div>${deNormaliseUserInput(a.howto)}</div>
										<hr>
										${a.progress.length
											? a.progress
												.map(b => `<div class="media mb-3">
													<div class="mr-2" title="${formatDateTime(b.progresstime)}" data-toggle="tooltip" data-placement="top">${formatDateTime(b.progresstime)}</div>
													<div class="media-body">
														<div>${b.statustitle} (${b.entryby})</div>
														<div>${deNormaliseUserInput(b.result)}</div>
													</div>
													<?php
													if ($ucatno == 19) : ?>
														<button data-cblprogressno="${b.cblprogressno}"  class="progress_delete_button btn btn-sm btn-danger rounded-circle custom_shadow ml-1" type="button" title="Delete Progress" data-toggle="tooltip" data-placement="top">
															<i class="fas fa-trash-alt"></i>
														</button>
													<?php endif; ?>
												</div>`)
												.join("")
											: ``
										}
									</div>`)
									.join("")
								: `<div class="text-danger pl-3">Not Assigned</div>`
							}
						</div>
					</div>`);

			card.appendTo(`#task_detail_modal .modal-body`);

			(function($) {
				$(`.create_subtask_button`, card).click(function(e) {
					$("#task_manager_setup_modal").modal("show");
					$("#task_manager_setup_modal_form").trigger("reset").data(`backlogno`, -1).data(`parentbacklogno`, data.backlogno);
					$(`#task_manager_setup_modal_form [name="channelno"]`).val(null).trigger("change");
				});

				$(`.assign_task_button`, card).click(function(e) {
					$(`#assign_task_modal`).modal("show");
					let form = $(`#assign_task_modal_form`).trigger("reset").data(`backlogno`, data.backlogno).data(`cblscheduleno`, -1);

					if (PERMISSION_LEVEL == 1) {
						$(`[name="assignedto"]`, form).val(USERNO).attr(`disabled`, true);
					}
				});

				$(`.edit_button`, card).click(function(e) {
					$(`#task_manager_setup_modal`).modal("show");
					$(`#task_manager_setup_modal_form`).trigger("reset").data(`backlogno`, data.backlogno).data(`parentbacklogno`, data.parentbacklogno);

					$(`#task_manager_setup_modal_form [name]`).each((index2, elem) => {
						if (data[$(elem).attr("name")]) {
							$(elem).val(data[$(elem).attr("name")]);
						}
					});

					$(`#task_manager_setup_modal_form [name="channelno"]`).trigger("change");
				});

				$(`.delete_button`, card).click(function(e) {
					if (confirm("Are you sure?")) {
						delete_a_backlogs({
							backlogno: data.backlogno
						}, );
					}
				});

				$(`.modify_deadline_button`, card).click(function(e) {
					$("#deadline_add_modal").modal("show");
					$("#deadline_add_modal_form").trigger("reset").data(`cblscheduleno`, $(this).data(`cblscheduleno`));
				});

				$(`.schedule_edit_button`, card).click(function(e) {
					$(`#assign_task_modal`).modal("show");
					$(`#assign_task_modal_form`).trigger("reset").data(`backlogno`, data.backlogno).data(`cblscheduleno`, $(this).data(`cblscheduleno`));

					let schedule = data.schedule.find(a => a.cblscheduleno == $(this).data(`cblscheduleno`));

					$(`#assign_task_modal_form [name]`).each((index2, elem) => {
						if (schedule[$(elem).attr("name")]) {
							$(elem).val(schedule[$(elem).attr("name")]);
						}
					});

					howToSolveTextEditor.setData(deNormaliseUserInput(schedule.howto));
					$(`#assign_task_modal_form`).data(`schedule`, schedule);
				});

				$(`.schedule_delete_button`, card).click(function(e) {
					if (confirm("Are you sure?")) {
						delete_schedule({
							cblscheduleno: $(this).data(`cblscheduleno`)
						}, $(this).parents(`.single_schedule`));
					}
				});

				$(`.progress_delete_button`, card).click(function(e) {
					if (confirm("Are you sure?")) {
						delete_progress({
							cblprogressno: $(this).data(`cblprogressno`)
						}, $(this).parents(`.media`));
					}
				});
			})(jQuery);
		}

		$(document).on(`click`, `.approve_button`, function() {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();
			console.log(`data =>`, data);
			if (confirm("Are you sure?")) {
				approve_backlog({
					backlogno: data.backlogno
				});
			}
		});

		function approve_backlog(json) {
			$.post(`php/ui/taskmanager/backlog/approve_backlog.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		function delete_a_backlogs(json) {
			$.post(`php/ui/taskmanager/backlog/remove_backlogs.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$("#task_detail_modal").modal("hide");
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		function delete_schedule(json, parentContainer) {
			$.post(`php/ui/taskmanager/schedule/remove_schedule.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(parentContainer).remove();
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		function delete_progress(json, parentContainer) {
			$.post(`php/ui/taskmanager/progress/remove_progress.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(parentContainer).remove();
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		$("#task_manager_add_button").click(() => {
			$("#task_manager_setup_modal").modal("show");
			$("#task_manager_setup_modal_form").trigger("reset").data(`backlogno`, -1).data(`parentbacklogno`, -1);
			$(`#task_manager_setup_modal_form [name="channelno"]`).val(null).trigger("change");
		});

		$("#task_manager_setup_modal_form").submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			formSubmit(json, this, `php/ui/taskmanager/backlog/setup_backlog.php`);
		});

		$(`#assign_task_modal_form`).submit(function(e) {
			e.preventDefault();

			$(`[name="assignedto"]`, this).attr(`disabled`, false);

			let json = {
				assignedto: $(`[name="assignedto"]`, this).val(),
				scheduledate: $(`[name="scheduledate"]`, this).val(),
				howto: howToSolveTextEditor.getData(),
				duration: $(`[name="duration"]`, this).val()
			};

			let cblscheduleno = $(this).data(`cblscheduleno`);
			if (cblscheduleno > 0) {
				json.cblscheduleno = cblscheduleno;
			}

			let backlogno = $(this).data(`backlogno`);
			if (backlogno > 0) {
				json.backlogno = backlogno;
			} else {
				toastr.error(`Backlog cannot be empty!`);
				return;
			}

			let schedule = $(`#assign_task_modal_form`).data(`schedule`);
			if (cblscheduleno > 0 && schedule.scheduledate == json.scheduledate && schedule.duration == json.duration) {
				delete json.scheduledate;
				delete json.duration;
			} else if (cblscheduleno > 0 && (schedule.scheduledate != json.scheduledate || schedule.duration != json.duration)) {
				if (!confirm(`All old deadlines will deleted and a new deadline will be reinitialized. Are you sure?`)) {
					return;
				}
			}

			formSubmit(json, this, `php/ui/taskmanager/schedule/setup_schedule.php`);
		});

		function formSubmit(json, formElem, url) {
			let backlogno = $(formElem).data("backlogno");
			if (backlogno > 0) {
				json.backlogno = backlogno;
			}

			let parentbacklogno = $(formElem).data("parentbacklogno");
			if (parentbacklogno > 0) {
				json.parentbacklogno = parentbacklogno;
			}

			$.post(url, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(".modal.show").modal("hide");
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		$(`#deadline_add_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());

			let cblscheduleno = $(this).data("cblscheduleno");
			if (cblscheduleno > 0) {
				json.cblscheduleno = cblscheduleno;
			}

			$.post(`php/ui/taskmanager/schedule/add_deadline.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$("#deadline_add_modal").modal("hide");
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		});

		function delete_deadline(json) {
			$.post(`php/ui/taskmanager/schedule/remove_deadline.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);
				}
			}, `json`);
		}
	</script>
</body>

</html>