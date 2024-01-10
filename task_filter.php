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

	<!-- Include stylesheet -->
	<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

	<!-- Include the Quill library -->
	<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

	<style>
		.bg-light-white {
			background-color: #FFFFFF;
		}

		.bg-light-black {
			background-color: #000000;
		}

		.bg-light-blue {
			background-color: #89CFF077;
		}

		.bg-light-green {
			background-color: #ACE1AF77;

		}

		.bg-light-red {
			background-color: #FA807277;

		}

		.border-light-white {
			border: 1px solid #FFFFFF;
		}

		.border-light-black {
			border: 1px solid #000000;
		}

		.border-light-blue {
			border: 1px solid #89CFF0;
		}

		.border-light-green {
			border: 1px solid #ACE1AF;

		}

		.border-light-red {
			border: 1px solid #FA8072;

		}

		.rounded-semi-circle {
			border-radius: 25%;
		}
	</style>
	<style>
		.task-card {
			transition: all 0.3s;
		}

		.task-card:hover {
			transform: scale(1.01);
			cursor: pointer;
			z-index: 2000;
		}

		.progress_parent_div .progress_delete_button_root {
			display: none;
		}

		.progress_parent_div:hover .progress_delete_button_root {
			display: inline;
		}

		.open_menu>.fa.fa-ellipsis-h {
			display: inline;
		}

		.open_menu.active>.fa.fa-ellipsis-h {
			display: none;
		}

		.open_menu>.fa.fa-times {
			display: none;

		}

		.open_menu.active>.fa.fa-times {
			display: inline;

		}

		.dropdown-toggle::after {
			display: none;
		}
	</style>

	<style>
		.comment .delete_comment {
			display: none;
			transition: .3s all;
		}

		.comment:hover .delete_comment {
			display: inline;
		}

		pre {
			margin-bottom: 0;

			/* max-width: 100%; */
			white-space: break-spaces;

		}

		pre p {
			margin-bottom: 0;
			padding-bottom: 0;
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
					<!-- <div class="app-page-title mb-0">
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
					</div> -->

					<form id="task_filter_form">
						<fieldset class="custom_fieldset bg-night-sky text-white shadow-sm pb-0">
							<legend class="legend-label bg-white text-dark shadow-sm">Task Filter Form</legend>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="d-block mb-0">
											<div class="mb-2">Task Filter For</div>
											<select name="assignedto" class="form-control shadow-sm">
												<option value="<?= $userno ?>">Myself</option>
											</select>
										</label>
									</div>
								</div>

								<!-- <div class="col-md-6">
									<div class="form-group">
										<label class="d-block mb-0">
											Date
											<div id="task_filter_date_input" name="date" class="form-control shadow-sm mt-2" style="cursor: pointer;">
												<i class="fa fa-calendar"></i>&nbsp;
												<span></span>
											</div>
										</label>
									</div>
								</div> -->

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
		const LOGGEDIN_USERNO = parseInt(`<?= $userno; ?>`, 10) || -1;
		const UCATNO = parseInt(`<?= $_SESSION['cogo_ucatno']; ?>`, 10) || -1;

		const ucatno = `<?= $_SESSION['cogo_ucatno']; ?>`;
	</script>
	<script>
		// var start = moment().subtract(6, 'days');
		// var end = moment();

		// function cb(start, end) {
		// 	$('#task_filter_date_input span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		// }

		// $('#task_filter_date_input').daterangepicker({
		// 	startDate: start,
		// 	endDate: end,
		// 	ranges: {
		// 		'Today': [moment(), moment()],
		// 		'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		// 		'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		// 		'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		// 		'Last 90 Days': [moment().subtract(89, 'days'), moment()],
		// 		'This Month': [moment().startOf('month'), moment().endOf('month')],
		// 		'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		// 	}
		// }, cb);

		// cb(start, end);

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
						allowClear: true,
						width: '100%'
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
			// let drp = $('#task_filter_date_input').data('daterangepicker');
			// json.startdate = drp.startDate.format('YYYY-MM-DD');
			// json.enddate = drp.endDate.format('YYYY-MM-DD');

			$.post(`php/ui/taskmanager/filter_task_detail.php`, json, resp => {
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

	<script>

		function get_header(value) {
			return `<div class="d-flex flex-wrap justify-content-between p-2 px-3">
						<div class="d-flex flex-row align-items-center">
							<img class='rounded-semi-circle' src="${value.photo_url||"assets/image/user_icon.png"}" width="40">
							<div class="d-flex flex-column ml-2">
								<div>
									<span style='font-weight: bold; font-family: monospace; color:black'>${value.postedby || value.assignedby || ``}</span>
									<small class='ml-2'>${value.storytype == 3 ? `${value.priorityleveltitle} (${value.relativepriority})`:``}</small>
								</div>
								<small class="mr-2">
									${value.storytime ? formatDateTime(value.storytime) : ``}
								</small>
							</div>
						</div>

						<div class="d-flex flex-row mt-1 ellipsis">
							<div class="collapse" id="collapseExample_${value.backlogno}">
								<div class="d-flex justify-content-center">
									${value.storytype==3 && (UCAT_NO == 19 || UCAT_NO == 13) ?
										`<button class="assign_task_button btn btn-sm btn-alternate rounded-semi-circle custom_shadow m-1" type="button" title="Assign task" data-toggle="tooltip" data-placement="top">
											<i class="fas fa-user-plus"></i>
										</button>
										`
										:``}

										<button class="edit_button btn btn-sm btn-info rounded-semi-circle custom_shadow m-1" type="button" title="Edit task" data-toggle="tooltip" data-placement="top">
											<i class="far fa-edit"></i>
										</button>

										<button class="delete_button btn btn-sm btn-danger rounded-semi-circle custom_shadow m-1" type="button" title="Delete task" data-toggle="tooltip" data-placement="top">
											<i class="fas fa-trash-alt"></i>
										</button>

										${value.storytype==3 && value.assignedto!=null && (UCAT_NO == 19 || UCAT_NO == 13 || value.assignedto == USER_NO)
											? `<button class="status_button btn btn-sm btn-warning custom_shadow  m-1" style='border-radius: 10px' type="button">Status</button>`
											: ``
										}
								</div>
							</div>

							<button class="open_menu d-none btn btn-sm" type="button" data-toggle0="collapse" data-target="#collapseExample_${value.backlogno}" aria-expanded="false" aria-controls="collapseExample_${value.backlogno}">
								<i class="fa fa-ellipsis-h m-1"></i>
								<i class="fa fa-times m-1"></i>
							</button>

							<div class="dropdown">
								<button class="open_dropdown btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<i class="fa fa-ellipsis-h m-1"></i>
								</button>
								<div class="dropdown-menu">
									${value.storytype == 3?`<a class="dropdown-item assign_task_button text-primary"><i class="fas fa-user-plus mr-2"></i> Assign</a>`:""}
									<a class="dropdown-item edit_button text-info"><i class="far fa-edit mr-2"></i> Edit </a>
									<a class="dropdown-item delete_button text-danger"><i class="fas fa-trash-alt mr-2"></i> Remove </a>
								</div>
							</div>
						</div>
					</div>`;
		}

		function get_body(value) {
			return `<div class="card-body py-2">
						<div>${value.story}</div>
					</div>`;
		}

		function get_footer(value) {

			let tpl = ``;

			if (value.storytype != 3) {
				return ``;
			}

			if (!value.schedule) {
				return ``;
			}

			return `<div class="px-2 pb-0 d-flex flex-column">
						${get_assignee(value, value.schedule)}
					</div>`;
		}

		function get_assignee(value, schedules) {
			let today = `<?= date('Y-m-d'); ?>`;
			let start = ``;
			let delay = {};

			let tpl = [];
			for (let index = 0; index < schedules.length; index++) {
				const aSchedule = schedules[index];

				if (aSchedule.progress.find(a => a.wstatusno == 4) != null) {
					cardClass = ` border-left border-danger card-shadow-danger`;
				} else if (aSchedule.progress.find(a => a.wstatusno == 3) != null) {
					if (aSchedule.deadlines && aSchedule.deadlines.length > 1) {
						cardClass = ` border-left border-warning card-shadow-warning`;
					} else {
						cardClass = ` border-left border-success card-shadow-success`;
					}
				} else if (aSchedule.progress.find(a => a.wstatusno == 2) != null) {
					cardClass = ` border-left border-info card-shadow-info`;
					delay = delayedDate(today, start);
				} else {
					cardClass = ``;
					delay = delayedDate(today, start);
				}



				tpl.push(`
						<div class="single_schedule w-100 px-2 py-2 ${cardClass}" id='collapse_parent_${aSchedule.cblscheduleno}'>
							<div class='d-flex justify-content-between'>
								<div>
									Assigned to
									${schedules.length > 1 ? `#${(index+1)}` : ''}:
									<span class="mr-2" style='font-weight: bold; color:black'>${aSchedule.assignee || ""}</span>
									<div class="dropdown d-inline-block">
										<button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
											<i class="fa fa-ellipsis-h text-primary"></i>
										</button>
										<div class="dropdown-menu">
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="modify_deadline_button dropdown-item text-info"><i class="far fa-edit mr-2"></i> Modify Deadline </a>
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="schedule_edit_button dropdown-item text-info"><i class="far fa-edit mr-2"></i> Edit </a>
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="schedule_delete_button dropdown-item text-danger"><i class="fas fa-trash-alt mr-2"></i> Remove </a>
										</div>
									</div>
								</div>

								<div>
									<div class='d-flex'>
										<div>From</div>
										<div class='ml-2'>${formatDate(aSchedule.scheduledate)}</div>
										<div class='mx-1'>to</div>
										<div>
											${aSchedule.deadlines.map((obj, i) => `<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ")}
										</div>
									</div>

									<div>
										${`
											${delay.days_diff > 0 ? `<i class='fa fa-circle mx-2 text-danger'></i> ${delay.days_diff} day(s) behind`: ``}
											${(delay.days_diff <= 0 && delay.hours_diff > 0) ? `<i class='fa fa-circle mx-2 text-danger'></i> ${delay.hours_diff} hour(s) behind`: ``}
										`}
									</div>
								</div>
							</div>

							<div class='row mt-1 flex-wrap'>
								<div class="col-9" id='collapse_tips_and_deadline_${aSchedule.cblscheduleno}'>

									<div class='mt-1 mb-2 d-flex'>
										<i class='fa fa-info-circle fa-info1 text-primary mx-1' style='font-style: italic;'></i>
										<div class='ml-2'>
											${deNormaliseUserInput(aSchedule.howto || "<i>No hint.</i>")}
										</div>
									</div>
								</div>

								<div class='col-3 border-left text-center'>
									<button data-cblscheduleno="${aSchedule.cblscheduleno}" class='status_button mt-1 btn btn-sm btn-outline-primary px-2 mb-1' >Update Progress</button>
								</div>

								<div class='col-1 p-0 text-right border-top'>
									<div class='mt-0 d-none'><img title='${aSchedule.assignee}' class='rounded-semi-circle' src="${aSchedule.photo_url||"assets/image/user_icon.png"}" width="35"/></div>
								</div>

								<div class='col-11 border-top'>
									<div class='mt-1 pb-2' id='collapse_progress_${aSchedule.cblscheduleno}'>
										<div class='d-flex flex-wrap'>
											<div class='my-auto'>
												${aSchedule.progress.length?"Progress":'<i>No Progress Yet.</i>'}
											</div>
											${aSchedule.progress.length
												? aSchedule.progress
													.map((b) => {

														return `<div title='Time: ${b.progresstime}' style='min-width: 100px;' class='progress_parent_div text-center border mx-2 pl-2 d-flex position-relative'>
																	<div><i class='fa fa-circle ${b.statustitle.split(" ").join('_')}'></i></div>
																	<div class='ml-1 mr-1'>${b.statustitle}</div>
																	<div class="progress_delete_button_root px-2 border-left bg-danger text-white" style0="top:-12px;right:-4px;">
																		<i data-cblprogressno="${b.cblprogressno}" class="progress_delete_button fas fa-times cursor-pointer"></i>
																	</div>
																</div>`;

													// return `<div class="media mb-3 bg-info border border-info ">
													// 			<div class="mr-2">${formatDateTime(b.progresstime)}</div>
													// 			<div class="media-body">
													// 				<div>${b.statustitle} (${b.entryby})</div>
													// 				<div>${deNormaliseUserInput(b.result)}</div>
													// 			</div>
													// 		</div>`;
													})
													.join("<div class='my-auto'><i class='fa fa-arrow-right text-secondary'></i></div>")
												: `
					`
											}
										</div>
									</div>
								</div>
							</div>
						</div>`);
			}

			return `<div class='py-1'>${tpl.join(`<hr class="mt-2 my-0 py-0 w-25" />`)}</div>`;
		}

		function get_comments(value) {
			let allowedCommentStoryTypes = [1, 3]; // story type that allowed to have comments
			let commentsHtml = ``;

			if (allowedCommentStoryTypes.includes(value.storytype)) {
				let commentlist = value.comments.map((aComment, _i) => {
					// console.log(aComment);
					let commenttpl = ``;
					let userImgSrc = aComment.photo_url || `assets/image/user_icon.png`;
					let isSelfComment = aComment.userno == value.userno;
					let isDeleteAllowed = aComment.userno == LOGGEDIN_USERNO || UCATNO == 19;

					if (isSelfComment) { // self
						commenttpl = `<div class="d-flex justify-content-end comment">
								<div class="text-right mr-2">
									<pre>${aComment.story}</pre>
									<small>
										${formatDateTime(aComment.lastupdatetime)}
										<span data-backlogno="${aComment.backlogno}" class="delete_comment ${isDeleteAllowed ? `` : `d-none`} cursor-pointer text-danger ml-2">
											Delete
										</span>
									</small>
								</div>
								<div>
									<img class="rounded-semi-circle" src="${userImgSrc}" width="35" title="${aComment.commentedby}" />
								</div>
							</div>`;
					} else { // others
						commenttpl = `<div class="d-flex justify-content-start comment">
								<div>
									<img class="rounded-semi-circle" src="${userImgSrc}" width="35" title="${aComment.commentedby}" />
								</div>
								<div class="text-left ml-2">
									<pre>${aComment.story}</pre>
									<small>
										${formatDateTime(aComment.lastupdatetime)}
										<span data-backlogno="${aComment.backlogno}" class="delete_comment ${isDeleteAllowed ? `` : `d-none`} cursor-pointer text-danger ml-2">
											Delete
										</span>
									</small>
								</div>
							</div>`;
					}

					return commenttpl;
				}).join(`<hr class="my-0 py-1 px-2" style="opacity:0.3;"/>`);

				commentsHtml = `<div class="comments-box pb-3 px-2 w-100">
						<div class="commentlist px-2 mt-2 mb-1">${commentlist}</div>
						<form name="comment-form" class="d-flex px-2">
							<div class='w-100'>
								<div class='comment-area-${value.backlogno} form-control form-control-sm h-auto'></div>
							</div>
							<textarea class="d-none comment-textarea-${value.backlogno} form-control form-control-sm" style="border-radius:10px; white-space: pre-wrap;" placeholder="Write your comment..."></textarea>
							<button class="comment-send btn btn-sm btn-rounded-circle" type="button">
								<i class="fas fa-paper-plane"></i>
							</button>
						</form>
					</div>`;
			}

			return commentsHtml;
		}

		function show_task(data, targetContainer) {
			let cardClass = ``;
			let bgClass = ``;

			$.each(data, (index, value) => {
				if (value.storytype == 1) {
					bgClass = `bg-light-blue border border-primary`;
				} else if (value.storytype == 2) {
					bgClass = `bg-light-green border border-success`;
				} else if (value.storytype == 3) {
					bgClass = `bg-light-white border border-secondary`;
				}

				let card = $(`<div class="card task-card my-3 ${cardClass} ${bgClass}" style='border-radius:15px;'>
						${get_header(value)}
						${get_body(value)}
						<hr class='my-0 py-2'/>
						${get_footer(value)}
						<hr class='mt-2 my-0 py-1'/>
						${get_comments(value)}
					</div>`)
					.appendTo(targetContainer);

				(function($) {
					$(`.assign_task_button`, card).click(function(e) {
						$(`#assign_task_modal`).modal("show");
						let form = $(`#assign_task_modal_form`).trigger("reset").data(`backlogno`, value.backlogno).data(`cblscheduleno`, -1);

						if (PERMISSION_LEVEL == 1) {
							$(`[name="assignedto"]`, form).val(LOGGEDIN_USERNO).attr(`disabled`, true);
						}
					});

					$(`.edit_button`, card).click(function(e) {
						$(`#task_manager_setup_modal`).modal("show");
						$(`#task_manager_setup_modal_form`).trigger("reset").data(`backlogno`, value.backlogno).data(`parentbacklogno`, value.parentbacklogno);

						$(`#task_manager_setup_modal_form [name]`).each((index2, elem) => {
							if (value[$(elem).attr("name")]) {
								$(elem).val(value[$(elem).attr("name")]);
							}
						});

						$(`#task_manager_setup_modal_form [name="channelno"]`).trigger("change");
					});

					$(`.delete_button`, card).click(function(e) {
						if (confirm("Are you sure?")) {
							delete_a_backlogs({
								backlogno: value.backlogno
							}, );
						}
					});

					$(`.status_button`, card).click(function(e) {
						$("#status_update_modal").modal("show");
						$(`#status_update_modal_form`).data("cblscheduleno", $(this).data('cblscheduleno')).data("cblprogressno", -1);
					});

					$(`.modify_deadline_button`, card).click(function(e) {
						$("#deadline_add_modal").modal("show");
						$("#deadline_add_modal_form").trigger("reset").data(`cblscheduleno`, $(this).data(`cblscheduleno`));
					});

					$(`.schedule_edit_button`, card).click(function(e) {
						$(`#assign_task_modal`).modal("show");
						$(`#assign_task_modal_form`).trigger("reset").data(`backlogno`, value.backlogno).data(`cblscheduleno`, $(this).data(`cblscheduleno`));

						let schedule = value.schedule.find(a => a.cblscheduleno == $(this).data(`cblscheduleno`));

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
							}, $(this).parents(`.progress_parent_div`));
						}
					});

					$('.open_menu', card).click(function(e) {
						$('.collapse', card).collapse('toggle');
						$('.open_menu', card).toggleClass('active');
					});


					$('.open_dropdown', card).click(function(e) {
						$('.dropdown-menu', card).dropdown('toggle');
						$('.open_dropdown', card).toggleClass('active');
					});

					const comment_form = $('[name="comment-form"]', card);


					// <!-- Initialize Quill editor -->
					var quill = new Quill(`.comment-area-${value.backlogno}`, {
						theme: 'snow'
					});
					// $('textarea', comment_form).emojioneArea({
					// 	// useSprite: false
					// });

					// $('textarea', comment_form).keypress(function (e) {
					// 	if(e.which === 13 && !e.shiftKey) {
					// 		e.preventDefault();

					// 		comment_form.submit();
					// 	}
					// });
					$('button.comment-send', comment_form).click(function() {
						$(comment_form).trigger('submit');
					});

					$(comment_form).submit(function(event) {
						event.preventDefault();
						// let comment = quill.getText();
						let comment = quill.container.firstChild.innerHTML;

						// let comment = $('textarea.comment', card).val();
						// let commentText = $('textarea.comment', card).emojioneArea();

						console.log(comment, quill.getContents());
						let json = {
							channelno: selected_channel,
							parentbacklogno: value.backlogno,
							storyphaseno: 16,
							storytype: 1,
							story: comment
						};
						formSubmit(json, this, `php/ui/taskmanager/backlog/setup_backlog.php`);
					});

					$('.delete_comment', card).click(function() {
						let backlogno = $(this).data('backlogno');
						if (confirm("Are you sure?")) {
							delete_a_backlogs({
								backlogno: backlogno
							}, );
						}
					});
				})(jQuery);
			});
		}
	</script>
</body>

</html>