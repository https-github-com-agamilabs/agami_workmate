<?php
include_once "php/ui/login/check_session.php";
$base_path = dirname(__FILE__);
date_default_timezone_set("Asia/Dhaka");
?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	require_once($base_path . "/configmanager/fileupload_configuration.php");
	?>

	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" integrity="sha512-vEia6TQGr3FqC6h55/NdU3QSM5XR6HSl5fW71QTKrgeER98LIMGwymBVM867C1XHIkYD9nMTfWK2A0xcodKHNA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js" integrity="sha512-hkvXFLlESjeYENO4CNi69z3A1puvONQV5Uh+G4TUDayZxSLyic5Kba9hhuiNLbHqdnKNMk2PxXKm0v7KDnWkYA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

	<!-- Include stylesheet -->
	<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

	<!-- Include the Quill library -->
	<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


	<style>
		[type="submit"]:disabled {
			cursor: not-allowed;
		}

		.large_card_header {
			width: -webkit-fill-available;
			width: -moz-available;
			width: fill-available;
		}

		.sidebar_right {
			position: relative;
			width: 100%;
			max-width: 100%;
			padding: 0 1rem 1rem 1rem;
		}

		@media (min-width: 992px) {
			.sidebar_right {
				position: sticky;
				top: 5rem;
				right: 0;
				width: 20%;
				max-width: 220px;
				padding: 0;
			}
		}
	</style>

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
			/* transition: all 0.3s; */
			transition: .3s transform cubic-bezier(.155, 1.105, .295, 1.12), .3s box-shadow, .3s -webkit-transform cubic-bezier(.155, 1.105, .295, 1.12);
		}

		.task-card:hover {
			/* transform: scale(1.005); */
			/* cursor: pointer; */
			z-index: 2000;
		}

		.deadline_parent_div .deadline_delete_button_root {
			display: none;
		}

		.deadline_parent_div:hover .deadline_delete_button_root {
			display: inline;
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
		.comment .edit_comment,
		.comment .delete_comment {
			display: none;
			transition: .3s all;
		}

		.comment:hover .edit_comment,
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

		.comment_story{
			font-family: serif;
			background: azure;
			padding: 10px;
			border: 1px solid #aaa;
			border-radius: 12px;
			margin-top: 8px;
		}

		.hint_text {
			
			border: 1px solid #aaa;
			border-radius: 12px;
			white-space: normal;
			padding: 5px;
			background: azure;
			font-family: serif;
			font-size: 9pt;

		}
	</style>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner pt-3 pl-3 pl-lg-0 pr-3">

					<div class="card mb-3" style="border-radius: 15px">
						<div class="card-body py-2">
							<div class="media py-2">
								<img src="<?php
											if (!empty($_SESSION["wm_photoname"])) {
												echo $_SESSION["wm_photoname"];
											} else {
												echo 'assets/image/user_icon.png';
											}
											?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-semi-circle mr-3" style="width:40px;height:40px;" alt="...">
								<div class="media-body">
									<input name="create_post" class="form-control bg-white rounded-pill cursor-pointer" type="text" placeholder="What's on your mind?" readonly>
								</div>
							</div>
							<hr class="my-2">

							<div class="input-group form_elem_parent mb-3" style="display1: none;">
								<!-- <div class="input-group-prepend">
									<span class="input-group-text shadow-sm">Channel</span>
								</div> -->
								<select id="task_channel_select" name="channelno" class="form-control shadow-sm" style="width: 100%;" required></select>
							</div>

							<div class="row no-gutters">
								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg text-warning" type="button" data-storytype="3" style="border-radius: 15px;">
										<i class="fas fa-tasks mr-2"></i>
										<span class="d-none d-sm-inline-block">Task</span>
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg text-primary" type="button" data-storytype="1" style="border-radius: 15px;">
										<i class="fas fa-comment-alt mr-2"></i>
										<span class="d-none d-sm-inline-block">Chat</span>
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg text-success" type="button" data-storytype="2" style="border-radius: 15px;">
										<i class="fas fa-bullhorn mr-2"></i>
										<span class="d-none d-sm-inline-block">Notification</span>
									</button>
								</div>
							</div>
						</div>
					</div>

					<div id="task_progress_container"></div>

					<div class="text-center">
						<button id="load_previous_task_progress_button" type="button" class="btn btn-primary font-weight-bold rounded-pill px-4 btn_shadow">
							Load Previous Task Progress
						</button>

						<div class="alert alert-info px-3 py-2 mx-auto" style="display: none;width: max-content;">No info available.</div>
					</div>
				</div>
			</div>

			<div class="sidebar_right">
				<div class="wherework_today my-3"></div>

				<div class="my_watchlist"></div>
			</div>
		</div>
	</div>

	<div id="task_manager_setup_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="max-width: 85%;" role="document">
			<div class="modal-content">
				<form id="task_manager_setup_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Create Post</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="media mb-2">
							<img src="<?php
										if (!empty($_SESSION["wm_photoname"])) {
											echo $_SESSION["wm_photoname"];
										} else {
											echo 'assets/image/user_icon.png';
										}
										?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-semi-circle mr-3" style="width:40px;height:40px;" alt="...">
							<div class="media-body">
								<div class="text-primary font-weight-bold">
									<?php
									if (!empty($_SESSION["wm_firstname"])) {
										echo $_SESSION["wm_firstname"];
									}
									if (!empty($_SESSION["wm_lastname"])) {
										echo " " . $_SESSION["wm_lastname"];
									}
									?>
								</div>
								<div>
									<?php
									if (!empty($_SESSION["wm_designation"])) {
										echo $_SESSION["wm_designation"];
									}
									?>
								</div>
							</div>
						</div>

						<div class="form-group form_elem_parent">
							<label class="d-block mb-0">
								<!-- Channel <span class="text-danger">*</span> -->
								<select name="channelno" class="form-control shadow-sm mt-2" style="width: 100%;"></select>
							</label>
						</div>

						<div class="form-group">
							<textarea name="story" class="form-control shadow-sm" placeholder="What's on your mind?" rows="3"></textarea>
						</div>




						<div class="row align-items-end">

							<div class="col-sm-6">
								<label class="d-block mb-0">
									Type <span class="text-danger">*</span>
									<select name="storytype" class="form-control shadow-sm mt-2" required>
										<option value="3" data-extra-hide='' data-extra-show='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Task</option>
										<option value="1" data-extra-show='' data-extra-hide='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Chat</option>
										<option value="2" data-extra-show='' data-extra-hide='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Notification</option>
									</select>
								</label>
							</div>

							<div class="col-sm-6">
								<label class="d-block mb-0">
									Category <span class="text-danger">*</span>
									<select name="storyphaseno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>


							<div class="col-md-6 mt-3 prioritylevelno_root">
								<label class="d-block mb-0">
									Priority Level <span class="text-danger">*</span>
									<select name="prioritylevelno" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-md-6 mt-3 relativepriority_root">
								<label class="d-block mb-0">
									Priority Value <span class="text-danger">*</span>
									<input name="relativepriority" class="form-control shadow-sm mt-2" type="number" min="0" placeholder="Priority Value...">
								</label>
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-12">
								<div class="text-primary h6">Attachments : </div>
								<div id="story_attachment_container" class="d-flex flex-wrap"></div>

								<div class="text-center text-sm-left">
									<div class="dropdown d-inline-block">
										<input name="fileurl" class="form-control shadow-sm" style="display: none;" type="file" multiple title="Attachment file">

										<button class="btn btn-primary dropdown-toggle shadow-sm mb-2 mb-sm-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="fas fa-upload mr-sm-2"></i> Attachment
										</button>
										<div id="filetype_dropdown_menu" class="dropdown-menu" tabindex="-1" role="menu" aria-hidden="true"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary btn-block shadow" disabled>Post</button>
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

	<div id="move_story_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="move_story_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Move Story</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<select name="channelno" class="form-control form-control-sm"></select>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-5 ripple custom_shadow">Confirm</button>
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
		const PERMISSION_LEVEL = `<?= $_SESSION['wm_permissionlevel']; ?>`;
		const link = `https://www.google.com/maps/search/?api=1`;

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

		// const CHANNELNO = parseInt(window.location.search.split("=").pop(), 10) || -1;
		const LOGGEDIN_USERNO = Number(<?= $userno; ?>) || -1;
		const UCATNO = Number(<?= $_SESSION['wm_ucatno']; ?>) || -1;

		const searchParams = new URLSearchParams(window.location.search);

		const selected_channel = searchParams.has('channelno') ? searchParams.get('channelno') : '';

		function padWithZero(value) {
			return (value < 10) ? `0${value}` : value;
		}

		function formatTime(timeString = "00:00:00") {
			let H = +timeString.substr(0, 2);
			let h = H % 12 || 12;
			let ampm = (H < 12 || H === 24) ? " AM" : " PM";
			return padWithZero(h) + timeString.substr(2, 3) + ampm;
		}

		// show_available_channels([]);
		let channelInterval = setInterval(() => {
			let channel_data = $(`#channels_container`).data(`channel_data`);

			if (channel_data && channel_data.length) {
				show_available_channels(channel_data);
				clearInterval(channelInterval);
			}
		}, 500);

		//get_channels_available_task();
		// get_channel_task_detail();

		function get_channel_task_detail(pageno = 1) {
			// if (pageno == 1) {
			// 	$(`#task_progress_container`).empty();
			// }

			let json = {
				channelno: selected_channel,
				pageno,
				limit: 10
			};

			$(`#load_previous_task_progress_button`).hide().siblings().hide();

			$.post(`php/ui/taskmanager/get_channel_task_detail.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					if (resp.results.length >= 10) {
						$(`#load_previous_task_progress_button`).show();
					} else {
						$(`#load_previous_task_progress_button`).siblings().html(`No${pageno > 1 ? ` more` : ``} info available.`).show();
					}

					// show_available_channels(resp.data);
					if (json.pageno <= 1) {
						$(`#task_progress_container`).empty();
					}
					show_task(resp.results, `#task_progress_container`);
				}
			}, `json`);
		}

		function get_channels_available_task() {
			$.post(`php/ui/taskmanager/selection/get_channels_available_task.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					// show_available_channels(resp.data);
					load_channel_story_detail(resp.data);
				}
			}, `json`);
		}

		function show_available_channels(result) {
			let select1 = $(`#task_channel_select`).empty();
			let select2 = $(`#task_manager_setup_modal_form [name="channelno"]`).empty();
			let select3 = $(`#move_story_modal_form [name="channelno"]`).empty();

			$.each(result, (index, value) => {
				let optgroup1 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select1);
				let optgroup2 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select2);
				let optgroup3 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select3);

				$.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
					$(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup1);
					$(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup2);
					$(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup3);
				});
			});

			if ($(`option`, select1).length == 0) {
				select1.append(`<option value='${selected_channel}'>Selected Channel</option>`);
				select2.append(`<option value='${selected_channel}'>Selected Channel</option>`);
				select3.append(`<option value='${selected_channel}'>Selected Channel</option>`);
			}

			select1.select2({
				placeholder: "Select Channel...",
				allowClear: true
			});

			select2.select2({
				placeholder: "Select Channel...",
				allowClear: true
			});

			select3.select2({
				placeholder: "Select Channel...",
				allowClear: true,
				width: `100%`
			});

			if (selected_channel.length) {
				select1.val(selected_channel).trigger('change');
				select1.parents(`.form_elem_parent`).hide();

				select2.val(selected_channel).trigger('change');
				select2.parents(`.form_elem_parent`).hide();
			}

			if ($(`option`, select1).length == 1) {
				select1.parents(`.form_elem_parent`).hide();
				select2.parents(`.form_elem_parent`).hide();
			}
		}

		function show_channels_available_task() {
			// get_channel_backlogs(1);
			get_channel_task_detail(1);
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
			let select = $(`#task_manager_setup_modal_form [name="prioritylevelno"]`).empty().append(`<option value="">Select...</option>`);

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

		get_filetype();

		function get_filetype() {
			$(`#filetype_dropdown_menu`).empty();

			$.post(`php/ui/chat/get_filetype.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_filetype(resp.data);
				}
			}, `json`);
		}

		function show_filetype(data) {
			let target = $(`#filetype_dropdown_menu`);

			$.each(data, (i, value) => {
				let button = $(`<button class="dropdown-item" data-filetypeno="${value.filetypeno}" type="button" tabindex="0">
						${value.filetypetitle}
					</button>`)
					.appendTo(target);

				(function($) {
					button.click(function(e) {
						$(`#task_manager_setup_modal_form [name="fileurl"]`)
							.data(`filetypeno`, value.filetypeno)
							.trigger("click")
							.siblings(`[data-toggle="dropdown"]`)
							.dropdown(`hide`);
					});
				})(jQuery);
			});
		}

		$(`#task_manager_setup_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			json.channelno = selected_channel;
			delete json.fileurl;
			delete json.shorttitle;
			json.attachments = JSON.stringify($('.attachment_url').map((i, f) => $(f).data()).toArray());

			formSubmit(json, this, `php/ui/taskmanager/backlog/setup_backlog.php`);
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

			console.log(json);

			$.post(url, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(".modal.show").modal("hide");

					let pageno = $("#task_manager_table_pageno_input").val();
					// get_channel_backlogs(pageno);
					get_channel_task_detail(pageno);

					$(formElem).data("backlogno", -1);
					$(formElem).data("parentbacklogno", -1);

					$(`#story_attachment_container`).find(`.story_attachment`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_story_attachment({
								chatno: json.chatno || resp.chatno,
								filetypeno: $(elem).data("filetypeno"),
								shorttitle: $(elem).data("shorttitle"),
								fileurl: $(elem).data("fileurl")
							});
						}
					});
				}
			}, `json`);
		}

		function setup_chat(json) {
			$.post(`php/ui/chat/setup_chat.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$(`#story_target_container`).find(`.story_target`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_chattarget({
								chatno: json.chatno || resp.chatno,
								userno: $(elem).data("userno")
							});
						}
					});

					$(`#story_attachment_container`).find(`.story_attachment`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_story_attachment({
								chatno: json.chatno || resp.chatno,
								filetypeno: $(elem).data("filetypeno"),
								shorttitle: $(elem).data("shorttitle"),
								fileurl: $(elem).data("fileurl")
							});
						}
					});

					$(`#task_manager_setup_modal`).modal("hide");
					toastr.success(resp.message);
					// get_channel_backlogs();
					get_channel_task_detail();
				}
			}, `json`);
		}

		function delete_chat(json) {
			$.post(`php/ui/chat/remove_chat.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_channel_chat_detail();
				}
			}, `json`);
		}

		function set_story_attachment(json) {
			let formData = new FormData();
			$.each(json, (key, value) => formData.append(key, value));
			$.ajax({
				type: "POST",
				url: "php/ui/chat/set_chatattachment.php",
				cache: false,
				contentType: false,
				processData: false,
				data: formData,
				success: function(result) {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						get_channel_chat_detail();
					}
				}
			});
		}

		function delete_story_attachment(json) {
			$.post(`php/ui/chat/remove_chatattachment.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_channel_chat_detail();
				}
			}, `json`);
		}

		$(`#task_manager_setup_modal_form [name="fileurl"]`).change(async function(e) {
			if (this.files.length) {

				for (let index = 0; index < this.files.length; index++) {
					const aFile = this.files[index];

					let fileupload_resp = await upload_file_in_server(aFile);

					let filetypeno = $(this).data(`filetypeno`);
					let filetypetitle = $(`#filetype_dropdown_menu [data-filetypeno="${filetypeno}"]`).html();

					let div = $(`<div class="attachment_url input-group input-group-sm mr-2 mb-2" style="width:max-content;">
							<div class="input-group-prepend">
								<span class="input-group-text shadow-sm">${filetypetitle}</span>
							</div>
							<input name="shorttitle" value="${aFile.name}" class="form-control shadow-sm" type="text" placeholder="Short title for file..." title="Short title for file">
							<div class="input-group-append">
								<button class="delete_button btn btn-light shadow-sm" type="button"> <i class="fas fa-times"></i></button>
							</div>
						</div>`)
						.data({
							isnew: true,
							filetypeno,
							shorttitle: aFile.name,
							fileurl: aFile,
							...fileupload_resp
						})
						.appendTo(`#story_attachment_container`);

					$(`[name="shorttitle"]`, div).trigger(`focus`);

					(function($) {
						$(`.delete_button`, div).click(function(e) {
							div.remove();
						});
					})(jQuery);
				}
			}
		});

		$(`[name="create_post"], button[data-storytype]`).on(`click`, function(e) {
			$(`#task_manager_setup_modal_form`).trigger("reset").data(`backlogno`, -1).data(`parentbacklogno`, -1);
			let modal = $(`#task_manager_setup_modal`).modal(`show`);
			let storytype = $(this).data(`storytype`) || 3;
			$(`[name="storytype"]`, modal).val(storytype);
			$(`[name="storytype"]`, modal).trigger('change');
		});

		$(`[name="story"]`, `#task_manager_setup_modal_form`).on(`input`, function(e) {
			let submitButton = $(`#task_manager_setup_modal_form :submit`);
			submitButton.prop(`disabled`, this.value.length <= 0);
		});

		// $(`#task_manager_setup_modal_form`).submit(function(e) {
		// 	e.preventDefault();
		// 	let json = Object.fromEntries((new FormData(this)).entries());

		// 	$.post(`php/ui/`, json, resp => {
		// 		if (resp.error) {
		// 			toastr.error(resp.message);
		// 		} else {
		// 			toastr.success(resp.message);
		// 		}
		// 	}, `json`);
		// });

		$(`#load_previous_task_progress_button`).click(function(e) {
			let pageno = $(this).data(`pageno`);
			if (pageno == null) {
				pageno = 2;
			} else {
				++pageno;
			}

			$(this).data(`pageno`, pageno);
			// get_channel_backlogs(pageno);
			get_channel_task_detail(pageno);
		});

		$(`#task_channel_select`).change(function(e) {
			$(`#load_previous_task_progress_button`).data(`pageno`, 1);
			// get_channel_backlogs(1);
			get_channel_task_detail(1);
		});

		$(`[name="storytype"]`, `#task_manager_setup_modal`).change(function(e) {
			let option = $(this).find('option:selected');
			console.log(option);

			let toShow = option.data('extra-show');
			let toHide = option.data('extra-hide');
			if (toShow.length) {
				$(toShow).show('slow');
			}

			if (toHide.length) {
				$(toHide).hide('slow');
			}
		});

		function get_channel_backlogs(pageno = 1) {
			if (pageno == 1) {
				$(`#task_progress_container`).empty();
			}

			let json = {
				channelno: $(`#task_channel_select`).val(),
				pageno,
				limit: 10
			};

			$(`#load_previous_task_progress_button`).hide().siblings().hide();

			$.post(`php/ui/taskmanager/backlog/get_channel_backlogs.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					if (resp.results.length >= 10) {
						$(`#load_previous_task_progress_button`).show();
					} else {
						$(`#load_previous_task_progress_button`).siblings().show();
					}

					show_channel_task_detail(resp.results);
				}
			}, `json`);
		}

		function show_channel_task_detail(data) {
			let target = $(`#task_progress_container`);

			$.each(data, (index, value) => {
				let template = $(`<tr>
						<td>${1 + index}</td>
						<td></td>
						<td>
							<div class="d-flex justify-content-center p-0">
								<button class="edit_button btn btn-sm btn-info rounded-semi-circle custom_shadow m-1" type="button" title="Edit">
									<i class="fas fa-edit"></i>
								</button>
								<button class="delete_button btn btn-sm btn-danger rounded-semi-circle custom_shadow m-1" type="button" title="Delete">
									<i class="fas fa-trash"></i>
								</button>
							</div>
						</td>
					</tr>`)
					.appendTo(target);

				(function($) {
					$(`.edit_button`, template).click(function(e) {
						let modal = $(`#modal`).modal(`show`).find(`.modal-title`).html(`Update Data`);
						let form = $(`form`, modal).trigger(`reset`).data(`tablePK`, value.tablePK);

						$(`[name]`, form).each((i, elem) => {
							let elementName = $(elem).attr(`name`);
							if (value[elementName] != null) {
								$(elem).val(value[elementName]);
							}
						});
					});

					$(`.delete_button`, template).click(function(e) {
						if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;
					});
				})(jQuery);
			});
		}


		/**
		 * Hanif
		 * Showed the stories
		 */
		$.post(`php/ui/notification/setup_lastvisit.php`, {
			channelno: selected_channel
		}, resp => {
			if (resp.error) {
				toastr.error(resp.message);
			}
		}, `json`);


		function get_header(value) {
			return `<div class="d-flex justify-content-between px-3 py-2">
						<div class="d-flex flex-row align-items-center large_card_header cursor-pointer">
							<img class='rounded-semi-circle mr-2' src="${value.photo_url||"assets/image/user_icon.png"}" width="40">
							<div class="d-flex flex-column">
								<div class="d-flex flex-wrap align-items-center">
									<div class="mr-2" style='font-weight: bold;font-family: monospace;color:black'>${value.postedby || value.assignedby || ``}</div>
									<div class="small">${value.storytype == 3 ? `${value.priorityleveltitle} (${value.relativepriority})` : ``}</div>
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

							${value.storytype == 3? (value.createwatchlisttime?"<i class='add_to_watchlist cursor-pointer my-auto text-danger fas fa-bookmark px-2' title='Add to watchlist'></i>":"<i class='add_to_watchlist cursor-pointer my-auto text-secondary far fa-bookmark px-2' title='Add to watchlist'></i>"):""}

							<div class="my-auto dropdown">
								<button class="open_dropdown btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<i class="fa fa-ellipsis-h m-1"></i>
								</button>
								<div class="dropdown-menu">
									${value.storytype == 3?`<a class="dropdown-item assign_task_button text-primary">
											<i class="fas fa-user-plus btn btn-primary btn-sm custom_shadow rounded-semi-circle mr-2"></i>
											<span class="font-weight-bold">Assign</span>
										</a>` : ``}
									<a class="dropdown-item edit_button text-info">
										<i class="far fa-edit btn btn-info btn-sm custom_shadow rounded-semi-circle mr-2"></i>
										<span class="font-weight-bold">Edit</span>
									</a>
									<a class="dropdown-item move_button text-alternate">
										<i class="fas fa-dolly btn btn-alternate btn-sm custom_shadow rounded-semi-circle mr-2"></i>
										<span class="font-weight-bold">Move</span>
									</a>
									<a class="dropdown-item delete_button text-danger">
										<i class="fas fa-trash-alt btn btn-danger btn-sm custom_shadow rounded-semi-circle mr-2"></i>
										<span class="font-weight-bold">Remove</span>
									</a>
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
							<div class='d-flex flex-wrap justify-content-between'>
								<div>
									Assigned to
									${schedules.length > 1 ? `#${(index+1)}` : ''}:
									<span class="mr-2" style='font-weight: bold; color:black'>${aSchedule.assignee || ""}</span>
									<div class="dropdown d-inline-block">
										<button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
											<i class="fa fa-ellipsis-h text-primary"></i>
										</button>
										<div class="dropdown-menu">
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="modify_deadline_button dropdown-item text-info">
												<i class="far fa-edit btn btn-info btn-sm custom_shadow rounded-semi-circle mr-2"></i>
												<span class="font-weight-bold">Modify Deadline</span>
											</a>
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="schedule_edit_button dropdown-item text-info">
												<i class="far fa-edit btn btn-info btn-sm custom_shadow rounded-semi-circle mr-2"></i>
												<span class="font-weight-bold">Edit</span>
											</a>
											<a data-cblscheduleno="${aSchedule.cblscheduleno}" class="schedule_delete_button dropdown-item text-danger">
												<i class="fas fa-trash-alt btn btn-danger btn-sm custom_shadow rounded-semi-circle mr-2"></i>
												<span class="font-weight-bold">Remove</span>
											</a>
										</div>
									</div>
								</div>

								<div>
									<div class='d-flex'>
										<div>From</div>
										<div class='ml-2'>${formatDate(aSchedule.scheduledate)}</div>
										<div class='mx-1'>to</div>
										<div>
											${aSchedule.deadlines
												.map((obj, i) => `<span class="deadline_parent_div ${i != 0 ? `text-danger` : ``}">
														${formatDate(obj.deadline)}
														<div class="deadline_delete_button_root bg-danger text-white px-1 shadow-sm">
															<i data-dno="${obj.dno}" class="deadline_delete_button fas fa-times cursor-pointer"></i>
														</div>
													</span>`)
												.join(", ")}
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
								<div class="col-sm-9" id='collapse_tips_and_deadline_${aSchedule.cblscheduleno}'>

									<div class='mt-1 mb-2 d-flex'>
										<i class='fa fa-info-circle fa-info1 text-primary mx-1' style='font-style: italic;'></i>
										<div class='ml-2 hint_text'>
											${deNormaliseUserInput(aSchedule.howto || "<i>No hint.</i>")}
										</div>
									</div>
								</div>

								<div class='col-sm-3 border-left text-center'>
									<button data-cblscheduleno="${aSchedule.cblscheduleno}" class='status_button mt-1 btn btn-sm btn-outline-primary px-2 mb-1' >Update Progress</button>
								</div>

								<div class='col-1 p-0 text-right border-top'>
									<div class='mt-0 d-none'>
										<img title='${aSchedule.assignee}' class='rounded-semi-circle' src="${aSchedule.photo_url || "assets/image/user_icon.png"}" width="35"/>
									</div>
								</div>

								<div class='col-11 border-top'>
									<div class='mt-1 pb-2' id='collapse_progress_${aSchedule.cblscheduleno}'>
										<div class='d-flex flex-wrap'>
											<div class='my-auto'>
												${aSchedule.progress.length ? `Progress` : `<i>No Progress Yet.</i>`}
											</div>
											${aSchedule.progress.length
												? aSchedule.progress
													.map((b) => {
														let percentileClass = `bg-percent-${(Math.round((b.percentile || 0) % 101 / 10) * 10).toFixed(0)}`;

														return `<div class="progress_parent_div" title="${b.statustitle} (${b.percentile || 0}%), Time: ${formatDateTime(b.progresstime)}">
																<div style='min-width: 100px;' class='text-center border mx-2 pl-2 d-flex position-relative'>
																	<div><i class='fa fa-circle ${b.statustitle.split(" ").join('_')}'></i></div>
																	<div class='ml-1 mr-1'>${b.statustitle}</div>
																	<div class="progress_delete_button_root px-2 border-left bg-danger text-white" style0="top:-12px;right:-4px;">
																		<i data-cblprogressno="${b.cblprogressno}" class="progress_delete_button fas fa-times cursor-pointer"></i>
																	</div>
																</div>
																<div class="progress ml-2 my-1" style="width:111px;">
																	<div class="progress-bar ${percentileClass}" role="progressbar" aria-valuenow="${b.percentile || 0}" aria-valuemin="0"
																		aria-valuemax="100" style="width:${b.percentile || 0}%;">${b.percentile || 0}%</div>
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
					let isEditAllowed = aComment.userno == LOGGEDIN_USERNO || UCATNO == 19;
					let isDeleteAllowed = aComment.userno == LOGGEDIN_USERNO || UCATNO == 19;

					if (isSelfComment) { // self
						commenttpl = `<div class="d-flex justify-content-end comment">
								<div class="text-right mr-2">
									<div class="text-primary font-weight-bold line-height-1" style="font-family: monospace;">${aComment.commentedby}</div>
									<pre class="comment_story">${aComment.story}</pre>
									<small>
										<span data-backlogno="${aComment.backlogno}" class="edit_comment ${isEditAllowed ? `` : `d-none`} cursor-pointer text-info ml-2">
											Edit
										</span>
										<span data-backlogno="${aComment.backlogno}" class="delete_comment ${isDeleteAllowed ? `` : `d-none`} cursor-pointer text-danger mx-2">
											Delete
										</span>
										${formatDateTime(aComment.lastupdatetime)}
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
									<div class="text-primary font-weight-bold line-height-1" style="font-family: monospace;">${aComment.commentedby}</div>
									<pre class="comment_story">${aComment.story}</pre>
									<small>
										${formatDateTime(aComment.lastupdatetime)}
										<span data-backlogno="${aComment.backlogno}" class="edit_comment ${isEditAllowed ? `` : `d-none`} cursor-pointer text-info ml-2">
											Edit
										</span>
										<span data-backlogno="${aComment.backlogno}" class="delete_comment ${isDeleteAllowed ? `` : `d-none`} cursor-pointer text-danger mx-2">
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

		let story_log = {};

		function show_task(data, targetContainer) {
			$.each(data, (index, value) => {
				let cardClass = ``;
				let bgClass = ``;

				if (value.storytype == 1) {
					bgClass = `bg-light-blue border border-primary`;
				} else if (value.storytype == 2) {
					bgClass = `bg-light-green border border-success`;
				} else if (value.storytype == 3) {
					// bgClass = `bg-light-white border border-secondary`;

					let schedule = value.schedule || [];
					let progress;

					if (schedule.length) {
						progress = schedule[schedule.length - 1].progress || [];

						if (progress.length) {
							progress = progress[progress.length - 1];
						}
					}

					if (progress && progress.wstatusno == 4) {
						cardClass = `border-left border-danger card-shadow-danger`;
					} else if (progress && progress.wstatusno == 3) {
						let deadlines = schedule[schedule.length - 1].deadlines || [];

						if (deadlines.length > 1) {
							cardClass = `border-left border-warning card-shadow-warning`;
						} else {
							cardClass = `border-left border-success card-shadow-success`;
						}
					} else if (progress && progress.wstatusno == 2) {
						cardClass = `border-left border-info card-shadow-info`;
					}
				}

				let card = $(`<div class="card task-card my-3 ${cardClass} ${bgClass}" style="border-radius:15px;">
						${get_header(value)}
						${get_body(value)}
						<hr class='my-0 py-2'/>
						${get_footer(value)}
						${value.storytype != 2
							? `<hr class='mt-2 my-0 py-1'/>
								${get_comments(value)}`
							: ``
						}
					</div>`)
					.appendTo(targetContainer);

				if (value.storytype == 3) {
					let schedule = value.schedule.length ? value.schedule : [];
					let deadlines = schedule.length ? schedule[schedule.length - 1].deadlines : [];
					let lastDeadline = deadlines.map(a => a.deadline).reduce((a, d) => a && new Date(a) > new Date(d) ? a : d, ``);

					let progress = schedule.length ? schedule[schedule.length - 1].progress : [];
					let lastProgress = progress.length ? progress[progress.length - 1] : null;

					let cardClass = ``;
					let progressClass = ``;

					if (!lastProgress) {} else if (lastProgress.wstatusno == 4) {
						cardClass = ` border-left border-danger card-shadow-danger`;
						progressClass = `bg-danger text-white `;
					} else if (lastProgress.wstatusno == 3) {
						if (deadlines && deadlines.length > 1) {
							cardClass = ` border-left border-warning card-shadow-warning`;
							progressClass = `bg-warning `;
						} else {
							cardClass = ` border-left border-success card-shadow-success`;
							progressClass = `bg-success text-white `;
						}
					} else if (lastProgress.wstatusno == 2) {
						cardClass = ` border-left border-info card-shadow-info`;
						progressClass = `bg-info text-white `;
					}

					let priorityClass = ``;
					if (value.prioritylevelno == 1) {
						priorityClass = `bg-danger text-white `;
					} else if (value.prioritylevelno == 2) {
						priorityClass = `alert-danger `;
					} else if (value.prioritylevelno == 3) {
						priorityClass = `bg-warning `;
					} else if (value.prioritylevelno == 4) {
						priorityClass = `alert-success `;
					} else if (value.prioritylevelno == 5) {
						priorityClass = `bg-success text-white `;
					}

					let scheduleHTML = ``;
					if (schedule.length) {
						$.each(schedule, (_i, valueOfSchedule) => {
							let progressTitle = ``;
							let lastProgress;
							if (valueOfSchedule.progress.length) {
								lastProgress = valueOfSchedule.progress[valueOfSchedule.progress.length - 1];
								progressTitle = `: ${lastProgress.statustitle} (${lastProgress.percentile}%)`;
							}

							let percentileClass = `border-percent-${(Math.round((lastProgress ? lastProgress.percentile : 0) % 101 / 10) * 10).toFixed(0)}`;

							let progressHTML = `<div class="progress-circle mr-1" data-value="${lastProgress ? lastProgress.percentile : 0}" title="${valueOfSchedule.assignee}${progressTitle}">
										<span class="progress-left">
											<span class="progress-bar ${percentileClass}"></span>
										</span>
										<span class="progress-right">
											<span class="progress-bar ${percentileClass}"></span>
										</span>
										<div class="progress-value w-100 he-100 rounded-circle d-flex align-items-center justify-content-center">
											<div class="font-weight-bold">
												<img src="${valueOfSchedule.photo_url || `assets/image/user_icon.png`}"
													class="rounded-circle" style="width:36px;" alt="${valueOfSchedule.assignee}">
											</div>
										</div>
									</div>`;

							scheduleHTML += progressHTML;
						});

						scheduleHTML = `<div class="d-flex">${scheduleHTML}</div>`
					}

					let shortCard = $(`<div class="card card-body${cardClass} cursor-pointer p-2 my-3 short_card_${value.backlogno}" style="border-radius:15px;">
							<div class="d-flex flex-wrap justify-content-between align-items-center">
								<div class="mb-1">${value.story}</div>
								<div class="d-flex flex-wrap justify-content-end align-items-center">
									${lastDeadline.length ? `<div class="font-weight-bold mr-1">${formatDate(lastDeadline)}</div>` : ``}
									${lastProgress ? `<div class="${progressClass}shadow-sm rounded px-2 py-1 mr-1" title="${lastProgress.result || ``}">
											${lastProgress.statustitle}
										</div>` : ``}
									<div class="${priorityClass}font-weight-bold h6 text-center border rounded-circle shadow-sm pt-2 mb-0 mr-1"
										style="width:40px;height:40px;" title="${value.priorityleveltitle} (${value.relativepriority})">
										${value.relativepriority}
									</div>
									${scheduleHTML}
								</div>
							</div>
						</div>`)
						.appendTo(targetContainer);

					card.hide();

					(function($) {
						let touchtime = 0;
						shortCard.on("click", function() {
							if (touchtime == 0) {
								// set first click
								touchtime = new Date().getTime();
							} else {
								// compare first click to this click and see if they occurred within double click threshold
								if (((new Date().getTime()) - touchtime) < 800) {
									// double click occurred
									shortCard.hide();
									card.show();
									touchtime = 0;
								} else {
									// not a double click so set as a new first click
									touchtime = new Date().getTime();
								}
							}
						});
					})(jQuery);
				}

				(function($) {
					let touchtime = 0;
					$(`.large_card_header`, card).on("click", function() {
						if (touchtime == 0) {
							// set first click
							touchtime = new Date().getTime();
						} else {
							// compare first click to this click and see if they occurred within double click threshold
							if (((new Date().getTime()) - touchtime) < 800) {
								// double click occurred
								card.hide();
								$(`.short_card_${value.backlogno}`, targetContainer).show();
								touchtime = 0;
							} else {
								// not a double click so set as a new first click
								touchtime = new Date().getTime();
							}
						}
					});

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

					$(`.move_button`, card).click(function(e) {
						$(`#move_story_modal`).modal("show");
						$(`#move_story_modal_form`).trigger("reset").data(`backlogno`, value.backlogno).data(`parentbacklogno`, value.parentbacklogno);
					});

					$(`.delete_button`, card).click(function(e) {
						if (confirm("Are you sure?")) {
							delete_a_backlogs({
								backlogno: value.backlogno
							});
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

					$(`.deadline_delete_button`, card).click(function(e) {
						if (confirm(`You are going to delete the deadline. Are you sure to proceed?`)) {
							delete_deadline({
								dno: $(this).data(`dno`)
							});
						}
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

					$('.add_to_watchlist', card).click(function() {
						let that = $(this);
						that.removeClass('fa-bookmark');
						that.addClass('fa-rotate');
						that.addClass('fa-circle-notch');
						// that.addClass('fa-spinner');
						that.addClass('fa-spin');
						setTimeout(() => {
							// that.html(that.data('pre'));

							that.addClass('fa-bookmark');
							that.removeClass('fa-rotate');
							that.removeClass('fa-circle-notch');
							// that.removeClass('fa-spinner');
							that.removeClass('fa-spin');

						}, 3000);
						if (value.createwatchlisttime) {
							if (confirm("Are you sure?")) {
								remove_my_watchlist({
									backlogno: value.backlogno
								}, $(this).parents(`.progress_parent_div`));
							}
						} else {
							add_my_watchlist({
								backlogno: value.backlogno
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
							storyphaseno: value.storyphaseno,
							storytype: 1,
							story: comment
						};

						formSubmit(json, this, `php/ui/taskmanager/backlog/setup_backlog.php`);
					});

					$('.edit_comment', card).click(function() {
						let backlogno = $(this).data('backlogno');
						comment_form.data(`backlogno`, backlogno);

						let comment_story = $(this).parents(`.comment`).find(`.comment_story`);
						const delta = quill.clipboard.convert(comment_story.html());
						quill.setContents(delta, 'silent');
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

			setProgress();
		}

		function percentageToDegrees(percentage) {
			return percentage / 100 * 360;
		}

		function setProgress() {
			$(`.progress-circle`).each(function(_i, elem) {
				let progress = Number($(elem).attr(`data-value`)) || 0;

				if (progress < 0) {
					progress = 0;
				}

				let left = $(elem).find(`.progress-left .progress-bar`);
				let right = $(elem).find(`.progress-right .progress-bar`);

				if (progress <= 50) {
					right.css(`transform`, `rotate(${percentageToDegrees(progress)}deg)`);
					left.css(`transform`, `rotate(0deg)`);
				} else {
					right.css(`transform`, `rotate(180deg)`);
					left.css(`transform`, `rotate(${percentageToDegrees(progress - 50)}deg)`);
				}
			});
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
					get_channel_task_detail(pageno);
					// get_channel_backlogs(pageno);
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
					get_channel_task_detail(pageno);
					// get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		function getCaret(el) {
			if (el.selectionStart) {
				return el.selectionStart;
			} else if (document.selection) {
				el.focus();
				var r = document.selection.createRange();
				if (r == null) {
					return 0;
				}
				var re = el.createTextRange(),
					rc = re.duplicate();
				re.moveToBookmark(r.getBookmark());
				rc.setEndPoint('EndToStart', re);
				return rc.text.length;
			}
			return 0;
		}

		function delete_a_backlogs(json) {
			$.post(`php/ui/taskmanager/backlog/remove_backlogs.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$("#task_detail_modal").modal("hide");
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_task_detail(pageno);
					// get_channel_backlogs(pageno);
				}
			}, `json`);
		}

		function delete_progress(json, parentContainer) {
			$.post(`php/ui/taskmanager/progress/remove_progress.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(parentContainer).next(`.fa-arrow-right`).remove();
					$(parentContainer).remove();
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_task_detail(pageno);
					// get_channel_backlogs(pageno);
				}
			}, `json`);
		}
	</script>

	<script>
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

		function delete_schedule(json, parentContainer) {
			$.post(`php/ui/taskmanager/schedule/remove_schedule.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(parentContainer).remove();
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_task_detail(pageno);
				}
			}, `json`);
		}

		// function formSubmit0(json, formElem, url) {
		// 	let backlogno = $(formElem).data("backlogno");
		// 	if (backlogno > 0) {
		// 		json.backlogno = backlogno;
		// 	}

		// 	let parentbacklogno = $(formElem).data("parentbacklogno");
		// 	if (parentbacklogno > 0) {
		// 		json.parentbacklogno = parentbacklogno;
		// 	}

		// 	$.post(url, json, resp => {
		// 		if (resp.error) {
		// 			toastr.error(resp.message);
		// 		} else {
		// 			toastr.success(resp.message);
		// 			$(".modal.show").modal("hide");
		// 			let pageno = $("#task_manager_table_pageno_input").val();
		// 			get_channel_backlogs(pageno);
		// 		}
		// 	}, `json`);
		// }
	</script>

	<script>
		$(`#move_story_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			let backlogno = $(this).data(`backlogno`) || 0;
			if (backlogno > 0) {
				json.backlogno = backlogno;
			} else {
				toastr.error(`Invalid story.`);
			}

			$.post(`php/ui/taskmanager/backlog/update_backlog_channel.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(`#move_story_modal`).modal("hide");
					get_channel_task_detail();
				}
			}, `json`);
		});

		get_user_wherework_today();

		function get_user_wherework_today() {
			$.post(`php/ui/userattlocset/get_user_wherework_today.php`, resp => {
				if (resp.error) {
					// toastr.error(resp.message);
				} else {
					show_user_wherework_today(resp.results);
				}
			}, `json`);
		}

		function show_user_wherework_today(data) {
			let target = $(`.wherework_today`);

			$.each(data, (index, value) => {
				let starttime = formatTime(value.starttime.split(` `)[1]);
				let endtime = formatTime(value.endtime.split(` `)[1]);

				let template = $(`<div class="card card-body p-2 mt-1">
						<a href="${link}&query=${value.loclat}%2C${value.loclon}" style="text-decoration:underline;" target="_blank">${value.locname}</a>
						<div>
							[<span title="${formatDateTime(value.starttime)}">${starttime}</span> -
							<span title="${formatDateTime(value.endtime)}">${endtime}</span>]
						</div>
					</div>`)
					.appendTo(target);
			});
		}
	</script>

</body>

</html>