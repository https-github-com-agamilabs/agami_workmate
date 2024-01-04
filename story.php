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
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="card mb-3">
						<div class="card-body py-2">
							<div class="media">
								<img src="<?php
											if (!empty($_SESSION["cogo_photoname"])) {
												echo $_SESSION["cogo_photoname"];
											} else {
												echo 'assets/image/user_icon.png';
											}
											?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-circle mr-3" style="width:40px;height:40px;" alt="...">
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
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="3">
										<i class="fas fa-tasks text-warning mr-2 d-none d-sm-inline-block"></i> Task
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="1">
										<i class="fas fa-comment-alt text-danger mr-2 d-none d-sm-inline-block"></i> Chat
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="2">
										<i class="fas fa-bullhorn text-success mr-2 d-none d-sm-inline-block"></i> Notification
									</button>
								</div>


							</div>
						</div>
					</div>

					<div id="task_progress_container"></div>

					<div class="text-center mb-2">
						<button id="load_previous_task_progress_button" type="button" class="btn btn-primary font-weight-bold rounded-pill px-4 btn_shadow">
							Load Previous Task Progress
						</button>

						<div class="alert alert-info py-2" style="display: none;">No info available.</div>
					</div>
				</div>
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
										?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-circle mr-3" style="width:40px;height:40px;" alt="...">
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
								<select name="channelno" class="form-control shadow-sm mt-2" style="width: 100%;" required></select>
							</label>
						</div>

						<div class="form-group">
							<textarea name="message" class="form-control shadow-sm" placeholder="What's on your mind?" rows="3"></textarea>
						</div>




						<div class="row align-items-end">


							<div class="col-sm-6">
								<label class="d-block mb-0">
									Category <span class="text-danger">*</span>
									<select name="storyphaseno" class="form-control shadow-sm mt-2" required></select>
								</label>
							</div>

							<div class="col-sm-6">
								<label class="d-block mb-0">
									Type <span class="text-danger">*</span>
									<select name="storytype" class="form-control shadow-sm mt-2" required>
										<option value="1" data-extra-show='' data-extra-hide='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Chat</option>
										<option value="2" data-extra-show='' data-extra-hide='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Notification</option>
										<option value="3" data-extra-hide='' data-extra-show='#task_manager_setup_modal_form .prioritylevelno_root, #task_manager_setup_modal_form .relativepriority_root'>Task</option>
									</select>
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
								<div id="chat_attachment_container" class="d-flex flex-wrap"></div>

								<div class="text-center text-sm-left">
									<div class="dropdown d-inline-block">
										<input name="fileurl" class="form-control shadow-sm" style="display: none;" type="file" title="Attachment file">

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

	<script>
		const ucatno = `<?= $ucatno; ?>`;
		const searchParams = new URLSearchParams(window.location.search);

		const selected_channel = searchParams.has('channelno') ? searchParams.get('channelno') : '';

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

			$.post(url, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(".modal.show").modal("hide");
					let pageno = $("#task_manager_table_pageno_input").val();
					get_channel_backlogs(pageno);

					$(`#chat_attachment_container`).find(`.chat_attachment`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_chat_attachment({
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
					$(`#chat_target_container`).find(`.chat_target`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_chattarget({
								chatno: json.chatno || resp.chatno,
								userno: $(elem).data("userno")
							});
						}
					});

					$(`#chat_attachment_container`).find(`.chat_attachment`).each((index, elem) => {
						if ($(elem).data("isnew")) {
							set_chat_attachment({
								chatno: json.chatno || resp.chatno,
								filetypeno: $(elem).data("filetypeno"),
								shorttitle: $(elem).data("shorttitle"),
								fileurl: $(elem).data("fileurl")
							});
						}
					});

					$(`#task_manager_setup_modal`).modal("hide");
					toastr.success(resp.message);
					get_channel_backlogs();
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

		function set_chat_attachment(json) {
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

		function delete_chat_attachment(json) {
			$.post(`php/ui/chat/remove_chatattachment.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_channel_chat_detail();
				}
			}, `json`);
		}

		$(`#task_manager_setup_modal_form [name="fileurl"]`).change(function(e) {
			if (this.files.length) {
				let filetypeno = $(this).data(`filetypeno`);
				let filetypetitle = $(`#filetype_dropdown_menu [data-filetypeno="${filetypeno}"]`).html();

				let div = $(`<div class="input-group input-group-sm mr-2 mb-2" style="width:max-content;">
						<div class="input-group-prepend">
							<span class="input-group-text shadow-sm">${filetypetitle}</span>
						</div>
						<input name="shorttitle" value="${this.files[0].name}" class="form-control shadow-sm" type="text" placeholder="Short title for file..." title="Short title for file">
						<div class="input-group-append">
							<button class="delete_button btn btn-light shadow-sm" type="button"> <i class="fas fa-times"></i></button>
						</div>
					</div>`)
					.data({
						isnew: true,
						filetypeno,
						shorttitle: this.files[0].name,
						fileurl: this.files[0]
					})
					.appendTo(`#chat_attachment_container`);

				$(`[name="shorttitle"]`, div).trigger(`focus`);

				(function($) {
					$(`.delete_button`, div).click(function(e) {
						div.remove();
					});
				})(jQuery);
			}
		});

		$(`[name="create_post"], button[data-storytype]`).on(`click`, function(e) {
			let modal = $(`#task_manager_setup_modal`).modal(`show`);
			let storytype = $(this).data(`storytype`) || 3;
			$(`[name="storytype"]`, modal).val(storytype);
		});

		$(`[name="message"]`, `#task_manager_setup_modal_form`).on(`input`, function(e) {
			let submitButton = $(`#task_manager_setup_modal_form :submit`);
			submitButton.prop(`disabled`, this.value.length <= 0);
		});

		$(`#task_manager_setup_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());

			$.post(`php/ui/`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
				}
			}, `json`);
		});

		$(`#load_previous_task_progress_button`).click(function(e) {
			let pageno = $(this).data(`pageno`);
			if (pageno == null) {
				pageno = 2;
			} else {
				++pageno;
			}

			$(this).data(`pageno`, pageno);
			get_channel_backlogs(pageno);
		});

		$(`#task_channel_select`).change(function(e) {
			$(`#load_previous_task_progress_button`).data(`pageno`, 1);
			get_channel_backlogs(1);
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
								<button class="edit_button btn btn-sm btn-info rounded-circle custom_shadow m-1" type="button" title="Edit">
									<i class="fas fa-edit"></i>
								</button>
								<button class="delete_button btn btn-sm btn-danger rounded-circle custom_shadow m-1" type="button" title="Delete">
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
	</script>
</body>

</html>