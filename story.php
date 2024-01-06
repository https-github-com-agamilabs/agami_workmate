<?php
include_once "php/ui/login/check_session.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

	<style>
		[type="submit"]:disabled {
			cursor: not-allowed;
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
			transition: all 0.3s;
		}

		.task-card:hover {
			transform: scale(1.05);
			cursor: pointer;
			z-index: 2000;
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

		.dropdown-toggle::after{
			display: none;
		}
	</style>
	<?php
	$base_path = dirname(__FILE__);
	require_once($base_path . "/configmanager/fileupload_configuration.php");
	//require_once "configmanager/fileupload_configuration.php";
	?>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="card mb-3" style="border-radius: 15px">
						<div class="card-body py-2">
							<div class="media py-2">
								<img src="<?php
											if (!empty($_SESSION["cogo_photoname"])) {
												echo $_SESSION["cogo_photoname"];
											} else {
												echo 'assets/image/user_icon.png';
											}
											?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-semi-circle mr-3" style="width:40px;height:40px;" alt="...">
								<div class="media-body">
									<input name="create_post" class="form-control shadow-sm rounded-pill cursor-pointer" type="text" placeholder="What's on your mind?" readonly>
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
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="3" style="border-radius: 15px;">
										<i class="fas fa-tasks text-warning mr-2"></i>
										<span class="d-none d-sm-inline-block">Task</span>
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="1" style="border-radius: 15px;">
										<i class="fas fa-comment-alt text-danger mr-2"></i>
										<span class="d-none d-sm-inline-block">Chat</span>
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="2" style="border-radius: 15px;">
										<i class="fas fa-bullhorn text-success mr-2"></i>
										<span class="d-none d-sm-inline-block">Notification</span>
									</button>
								</div>


							</div>
						</div>
					</div>

					<div id="task_progress_container"></div>

					<div class="col-md-6">
						<button id="load_previous_task_progress_button" type="button" class="btn btn-primary font-weight-bold rounded-pill px-4 btn_shadow">
							Load Previous Task Progress
						</button>

						<div class="alert alert-info py-2" style="display: none;">No info available.</div>
					</div>
				</div>
			</div>

			<div style="width: 20%; max-width: 220px;">

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
										if (!empty($_SESSION["cogo_photoname"])) {
											echo $_SESSION["cogo_photoname"];
										} else {
											echo 'assets/image/user_icon.png';
										}
										?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-semi-circle mr-3" style="width:40px;height:40px;" alt="...">
							<div class="media-body">
								<div class="text-primary font-weight-bold">
									<?php
									if (!empty($_SESSION["cogo_firstname"])) {
										echo $_SESSION["cogo_firstname"];
									}
									if (!empty($_SESSION["cogo_lastname"])) {
										echo " " . $_SESSION["cogo_lastname"];
									}
									?>
								</div>
								<div>
									<?php
									if (!empty($_SESSION["cogo_designation"])) {
										echo $_SESSION["cogo_designation"];
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

	<script>
		const PERMISSION_LEVEL = `<?= $_SESSION['cogo_permissionlevel']; ?>`;

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

		const CHANNELNO = parseInt(window.location.search.split("=").pop(), 10) || -1;
		const LOGGEDIN_USERNO = parseInt(`<?= $userno; ?>`, 10) || -1;
		const UCATNO = parseInt(`<?= $ucatno; ?>`, 10) || -1;

		const ucatno = `<?= $ucatno; ?>`;
		const searchParams = new URLSearchParams(window.location.search);

		const selected_channel = searchParams.has('channelno') ? searchParams.get('channelno') : '';

		show_available_channels([]);
		//get_channels_available_task();
		get_channel_task_detail();

		function get_channel_task_detail(pageno = 1) {
			// if (pageno == 1) {
			// 	$(`#task_progress_container`).empty();
			// }

			let json = {
				channelno: CHANNELNO,
				pageno,
				limit: 10
			};

			//$(`#load_previous_task_progress_button`).hide().siblings().hide();

			$.post(`php/ui/chat/get_channel_task_detail.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
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

			$.each(result, (index, value) => {
				let optgroup1 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select1);
				let optgroup2 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select2);

				$.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
					$(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup1);
					$(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup2);
				});
			});
			if ($(`option`, select1).length == 0) {
				select1.append(`<option value='${selected_channel}'>Selected Channel</option>`);
				select2.append(`<option value='${selected_channel}'>Selected Channel</option>`);
			}

			select1
				.select2({
					placeholder: "Select Channel...",
					allowClear: true
				});

			select2
				.select2({
					placeholder: "Select Channel...",
					allowClear: true
				});



			if (selected_channel.length) {
				select1.val(selected_channel)
					.trigger('change');
				select1.parents(`.form_elem_parent`).hide();

				select2.val(selected_channel)
					.trigger('change');
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

					console.log(div.data());
				}


			}
		});

		$(`[name="create_post"], button[data-storytype]`).on(`click`, function(e) {
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
			channelno: CHANNELNO
		}, resp => {
			if (resp.error) {
				toastr.error(resp.message);
			}
		}, `json`);

		let story_log = {};

		function show_task(data, targetContainer) {
			let today = `<?= date('Y-m-d'); ?>`,
				start = ``,
				delay = {},
				cardClass = ``;

			$.each(data, (index, value) => {
				start = value.deadlines.length ? value.deadlines[value.deadlines.length - 1].deadline : value.scheduledate;

				if (value.storytype == 3) {
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
				}

				let bgClass = ``;
				if (value.storytype == 1) {
					bgClass = `bg-light-blue border border-primary`;

				} else if (value.storytype == 2) {
					bgClass = `bg-light-green border border-success`;

				} else if (value.storytype == 3) {
					bgClass = `bg-light-white border border-secondary`;

				}

				// console.log(`delay =>`, delay);

				let card = $(`<div class="card task-card my-3 ${cardClass} ${bgClass}" style='border-radius:15px;'>
						
						<div class="d-flex flex-wrap justify-content-between p-2 px-3">
							<div class="d-flex flex-row align-items-center"> 
								<img class='rounded-semi-circle' src="https://i.imgur.com/UXdKE3o.jpg" width="40">
								<div class="d-flex flex-column ml-2"> 
									<div>
										<span style='font-weight: bold; font-family: monospace; color:black'>${value.assignedby || ``}</span> 
										<small class='ml-2'>${value.storytype == 3 ? `${value.priorityleveltitle} (${value.relativepriority})`:``}</small>
									</div>
									<small class="mr-2">
										${value.storytime || ``}
										${value.storytype == 3 && value.assignedto!=null ?	
											`${delay.days_diff > 0 ? `<i class='fa fa-circle mx-2 text-danger'></i> ${delay.days_diff} day(s) behind`: ``}
											${(delay.days_diff <= 0 && delay.hours_diff > 0) ? `<i class='fa fa-circle mx-2 text-danger'></i> ${delay.hours_diff} hour(s) behind`: ``}
											`:``
										}
										
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
										<a class="dropdown-item assign_task_button text-primary"><i class="fas fa-user-plus mr-2"></i> Assign Task</a>
										<a class="dropdown-item edit_button text-info"><i class="far fa-edit mr-2"></i> Edit Task</a>
										<a class="dropdown-item delete_button text-danger"><i class="fas fa-trash-alt mr-2"></i> Remove Task</a>
										<a class="dropdown-item status_button text-warning">Update Status</a>
									</div>
								</div>
								
							</div>
						</div> 

						<div class="card-body py-2">
							<div>${value.story}</div>
						</div>

						
						${value.storytype == 3 && value.assignedto!=null ? `
						<div class="card-footer p-2 bg-transparent">
							<div class="w-100 px-2 py-1">
								${value.assignee ? `<div>Assignee: ${value.assignee}</div>` : ``}
								<div class="d-flex justify-content-between">
									<div>How to solve (Tips)</div>
									<div>
										[${formatDate(value.scheduledate)}
										to
										${value.deadlines.map((obj, i) => `<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ")}]
									</div>
								</div>
								<div>${deNormaliseUserInput(value.howto)}</div>
								<hr>
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
								</div>` : ``
							}
						</div>
					</div>`)
					.appendTo(targetContainer);

				(function($) {

					$(`.assign_task_button`, card).click(function(e) {
						$(`#assign_task_modal`).modal("show");
						let form = $(`#assign_task_modal_form`).trigger("reset").data(`backlogno`, value.backlogno).data(`cblscheduleno`, -1);

						if (PERMISSION_LEVEL == 1) {
							$(`[name="assignedto"]`, form).val(USERNO).attr(`disabled`, true);
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
						$(`#status_update_modal_form`).data("cblscheduleno", value.cblscheduleno).data("cblprogressno", -1);
					});

					$('.open_menu', card).click(function(e) {
						$('.collapse', card).collapse('toggle');
						$('.open_menu', card).toggleClass('active');
					});


					$('.open_dropdown', card).click(function(e) {
						$('.dropdown-menu', card).dropdown('toggle');
						$('.open_dropdown', card).toggleClass('active');
					});
				})(jQuery);
			});
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
</body>

</html>