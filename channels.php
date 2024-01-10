<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka");
	?>

	<style>
		#new_chat_message_card_container {
			position: fixed;
			z-index: 155;
			bottom: 0;
			transition: all .2s;
		}

		#channel_new_thread {
			position: fixed;
			z-index: 155;
			right: -30px;
			top: 0;
			height: 100vh;
			/* transform: translate(500px); */
			transition: all .2s;
			box-shadow: -0.46875rem 0 2.1875rem rgb(4 9 20 / 3%), -0.9375rem 0 1.40625rem rgb(4 9 20 / 3%), -0.25rem 0 0.53125rem rgb(4 9 20 / 5%), -0.125rem 0 0.1875rem rgb(4 9 20 / 3%);
		}

		#channel_new_thread .btn-open-options {
			border-radius: 50px;
			position: absolute;
			left: -114px;
			bottom: 20px;
			padding: 0;
			height: 54px;
			line-height: 54px;
			width: 54px;
			text-align: center;
			display: block;
			box-shadow: 0 0.46875rem 2.1875rem rgb(4 9 20 / 3%), 0 0.9375rem 1.40625rem rgb(4 9 20 / 3%), 0 0.25rem 0.53125rem rgb(4 9 20 / 5%), 0 0.125rem 0.1875rem rgb(4 9 20 / 3%);
			margin-top: -27px;
		}

		.chat_parent_body img {
			max-width: 100%;
		}

		/* .product_hover_buttons>.reply_button,
		.product_hover_buttons>.edit_button,
		.product_hover_buttons>.delete_button {
			display: none;
			transition: opacity 1s ease-out;
			opacity: 0;
		}

		#chat_container .card-body:hover .product_hover_buttons>.reply_button,
		#chat_container .card-body:hover .product_hover_buttons>.edit_button,
		#chat_container .card-body:hover .product_hover_buttons>.delete_button {
			opacity: 1;
			display: inline-flex;
		} */

		.ck-balloon-panel {
			--ck-z-modal: 1050;
		}
	</style>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header fixed-footer">
		<?php include_once("navbar.php"); ?>

		<div id="channel_new_thread">
			<button type="button" id="add_chat_button" class="btn-open-options btn btn-warning" data-toggle="tooltip" data-placement="top" title="নতুন আলোচনা শুরু করতে ক্লিক করুন">
				<i class="fa fa-plus fa-w-16 fa-spin fa-2x"></i>
			</button>
		</div>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="tab-content">
						<div class="tab-pane active" id="chat_tabpanel" role="tabpanel">
							<div id="chat_container" class="mb-5"> </div>
							<div class="text-center mb-2">
								<button id="load_previous_chat_button" type="button" class="btn btn-primary font-weight-bold rounded-pill px-4 btn_shadow">
									Load Previous Chat
								</button>
								<div class="alert alert-info py-2" style="display: none;">No more massage.</div>
							</div>
						</div>
						<div class="tab-pane" id="task_progress_tabpanel" role="tabpanel">
							<div id="task_progress_container"></div>
							<div class="text-center mb-2">
								<button id="load_previous_task_progress_button" type="button" class="btn btn-primary font-weight-bold rounded-pill px-4 btn_shadow">
									Load Previous Task Progress
								</button>
								<div class="alert alert-info py-2" style="display: none;">No more task progress.</div>
							</div>
						</div>
					</div>
				</div>

				<?php include_once("footer.php"); ?>
			</div>
		</div>
	</div>

	<div id="setup_chat_modal" class="modal animated fadeInDown" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form id="setup_chat_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Message</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body px-2 px-md-3">
						<div class="media mb-2">
							<img src="<?php
										if (!empty($_SESSION["cogo_photoname"])) {
											echo $_SESSION["cogo_photoname"];
										} else {
											echo 'assets/image/user_icon.png';
										}
										?>" onerror="this.src='assets/image/user_icon.png'" class="align-self-start rounded-circle shadow-sm mr-2 mr-md-3" width="50" height="50" alt="Profile Picture">

							<div class="media-body">
								<div class="d-flex flex-wrap mb-2">
									<div class="h6 font-weight-bold mr-4">
										<?php
										if (!empty($_SESSION["cogo_firstname"])) {
											echo $_SESSION["cogo_firstname"];
										}
										if (!empty($_SESSION["cogo_lastname"])) {
											echo " " . $_SESSION["cogo_lastname"];
										}
										?>
									</div>

									<span class="mr-2">with</span>

									<div id="chat_target_container" class="d-flex flex-wrap small"> </div>

									<div class="dropdown btn-group">
										<button class="btn btn-sm btn-primary rounded-pill shadow dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">Mention someone</button>
										<div id="chat_target_select_dropdown_menu" tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu"> </div>
									</div>
								</div>

								<div class="row">
									<div class="col-6 input-group input-group-sm pr-1">
										<select name="catno" class="form-control shadow-sm rounded-pill" required></select>
									</div>

									<div class="col-6 input-group input-group-sm pl-1">
										<select name="statusno" class="form-control shadow-sm rounded-pill" required></select>
									</div>
								</div>
							</div>
						</div>

						<div id="chat_message_container">
							<p></p>
						</div>

						<div class="row mt-3">
							<div class="col-lg-6">
								<div id="chat_attachment_container" class="d-flex flex-wrap small"> </div>
							</div>

							<div class="col-4 col-lg-2">
								<div class="input-group input-group-sm mb-2">
									<select name="filetypeno" class="form-control shadow-sm rounded-pill"></select>
								</div>
							</div>

							<div class="col-6 col-lg-3">
								<div class="input-group input-group-sm mb-2">
									<input name="shorttitle" class="form-control shadow-sm rounded-pill" type="text" placeholder="Short title for file...">
								</div>
							</div>

							<div class="col-2 col-lg-1">
								<input name="fileurl" class="form-control shadow-sm" style="display: none;" type="file">
								<button id="chat_attachment_upload_button" class="btn btn-sm btn-primary rounded-pill shadow" type="button">
									<i class="fas fa-upload"></i>
								</button>
							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button class="btn btn-sm btn-primary rounded-pill px-4 shadow" type="submit">
							<i class="fas fa-paper-plane mr-2"></i>Save
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		const CHANNELNO = parseInt(window.location.search.split("=").pop(), 10) || -1;
		const LOGGEDIN_USERNO = parseInt(`<?= $userno; ?>`, 10) || -1;
		const UCATNO = parseInt(`<?= $ucatno; ?>`, 10) || -1;

		let chatEditor;

		ClassicEditor
			.create(document.querySelector("#chat_message_container"), {
				// plugins: [Base64UploadAdapter]
			})
			.then(editor => {
				chatEditor = editor;
				// console.log(editor);
			})
			.catch(error => {
				console.error(error);
			});

		var pageSettings = {};

		function pageSettingsFunc(key = "", value) {
			if (!pageSettings) {
				pageSettings = {};
			}

			if (value) {
				pageSettings[key] = value;
			}

			return pageSettings.hasOwnProperty(key) ? pageSettings[key] : undefined;
		};

		function padWithZero(value) {
			return (value < 10) ? `0${value}` : value;
		}

		function formatDateTime(dateTime) {
			let date = new Date(dateTime);
			let hours = date.getHours();
			let minutes = date.getMinutes();
			let ampm = hours >= 12 ? 'PM' : 'AM';
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
			minutes = minutes < 10 ? '0' + minutes : minutes;
			let strTime = hours + ':' + minutes + ' ' + ampm;
			return padWithZero(date.getDate()) + "-" + padWithZero(date.getMonth() + 1) + "-" + date.getFullYear() + "  " + strTime;
		}

		function formatTime(timeString = "00:00:00") {
			let H = +timeString.substr(0, 2);
			let h = H % 12 || 12;
			let ampm = (H < 12 || H === 24) ? " AM" : " PM";
			return padWithZero(h) + timeString.substr(2, 3) + ampm;
		}

		function formatDate(date) {
			date = new Date(date);
			return date.getDate().toString().padStart(2, 0) + "-" + (date.getMonth() + 1).toString().padStart(2, 0) + "-" + date.getFullYear();
		}

		$.post(`php/ui/notification/setup_lastvisit.php`, {
			channelno: CHANNELNO
		}, resp => {
			if (resp.error) {
				toastr.error(resp.message);
			}
		}, `json`);

		get_categories();
		get_statuses();
		get_filetype(`#new_chat_message_filetypeno_select`);
		get_users();
		get_channel_chat_detail();

		function get_categories() {
			$(`#setup_chat_modal_form [name="catno"]`).empty();
			$(`#setup_chat_modal_form [name="catno"]`).append(new Option("Select Category", ""));

			$.post(`php/ui/settings/category/get_categories.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.data, (i, value) => $(`#setup_chat_modal_form [name="catno"]`).append(new Option(value.cattitle, value.catno)));
				}
			}, `json`);
		}

		function get_statuses() {
			$(`#setup_chat_modal_form [name="statusno"]`).empty();
			$(`#setup_chat_modal_form [name="statusno"]`).append(new Option("Select Status", ""));

			$.post(`php/ui/settings/status/get_statuses.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.data, (i, value) => $(`#setup_chat_modal_form [name="statusno"]`).append(new Option(value.statustitle, value.statusno)));
				}
			}, `json`);
		}

		function get_filetype() {
			$(`#setup_chat_modal_form [name="filetypeno"]`).empty();
			$(`#setup_chat_modal_form [name="filetypeno"]`).append(new Option("Select File Type", ""));

			$.post(`php/ui/chat/get_filetype.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.data, (i, value) => $(`#setup_chat_modal_form [name="filetypeno"]`).append(new Option(value.filetypetitle, value.filetypeno)));
				}
			}, `json`);
		}

		function get_users() {
			$.post(`php/ui/user/get_users.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_users(resp.results);
				}
			}, `json`);
		}

		function show_users(data) {
			$.each(data, (i, value) => {
				let button = $("<button>")
					.append(`${value.firstname}${value.lastname ? ` ${value.lastname}` : ``}`)
					.attr({
						type: "button",
						tabindex: "0",
						class: "dropdown-item"
					})
					.appendTo(`#chat_target_select_dropdown_menu`);

				(function($) {
					button.click(function(e) {
						e.preventDefault();
						let chat_target = $(`#chat_target_container`).find(`.chat_target`).toArray().find(a => $(a).data("userno") == value.userno);

						if (chat_target) {
							chat_target.remove();
						} else {
							let div = $("<div>")
								.data({
									userno: value.userno,
									isnew: true
								})
								.append(`${value.firstname}${value.lastname ? ` ${value.lastname}` : ``}
									<button type="button" class="close py-0" style="line-height: 0.8;" aria-label="Close"><span aria-hidden="true" class="small">×</span></button>`)
								.attr({
									class: "chat_target alert alert-info alert-dismissible fade show rounded-pill pl-3 pr-4 py-1 mb-2 mr-1 shadow border-info",
									style: "width: fit-content;"
								})
								.appendTo(`#chat_target_container`);

							(function($) {
								div.find(`button`).click(function(e) {
									e.preventDefault();
									div.remove();
								});
							})(jQuery);
						}
					});
				})(jQuery);
			});
		}

		$(`#load_previous_chat_button`).click(function(e) {
			let pageno = $(this).data(`pageno`);
			if (pageno == null) {
				pageno = 2;
			} else {
				++pageno;
			}

			$(this).data(`pageno`, pageno);
			get_channel_chat_detail(pageno);
		});

		function get_channel_chat_detail(pageno = 1) {
			if (pageno == 1) {
				$("#chat_container").empty();
			}

			let json = {
				channelno: CHANNELNO,
				pageno,
				limit: 10
			};

			$(`#load_previous_chat_button`).hide().siblings().hide();

			$.post(`php/ui/chat/get_channel_chat_detail.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					if (resp.data.length >= 10) {
						$(`#load_previous_chat_button`).show();
					} else {
						$(`#load_previous_chat_button`).siblings().show();
					}
					load_channel_chat_detail(resp.data);
					// scrollToLatest();
				}
			}, `json`);
		}

		// new code on 16 december 2021 [part 4 start]
		let chat_log = {};
		let last_scrollTop = undefined;
		// new code on 16 dec 2021 [part 4 end]

		function load_channel_chat_detail(data, chatContainerTarget) {
			let lastdate = null;

			// new code on 16 december 2021 [part 1 start]
			if (!chatContainerTarget) {
				if ($(`#default`).length) {
					chatContainerTarget = $(`#default`);
				} else {
					chatContainerTarget = $("<div>")
						.attr({
							"id": "default",
							"class": "card card-body mb-2"
						})
						.appendTo("#chat_container");
				}
			}
			// let prev_data = $(chatContainerTarget).data('data') || [];
			let prev_data = JSON.parse(JSON.stringify(chat_log[$(chatContainerTarget).attr('id')] || []));
			let updated_root = undefined;
			// new code on 16 dec 2021 [part 1 end]

			$.each(data, (index, value) => {

				// new code on 16 december 2021 [part 2 start]
				let prev_data_index = JSON.stringify(prev_data[index] || {});
				let new_data_index = JSON.stringify(value);
				let update_available = prev_data[index] ? prev_data_index != new_data_index : false;
				// console.log(`prev_data at index =>`, index, prev_data_index);
				// console.log(`new_data at index =>`, index, new_data_index);
				// console.log(`update check =>`, update_available);

				// new code on 16 dec 2021 [part 2 end]


				let [indate, intime] = value.createtime.split(" ");

				if (lastdate != indate) {
					lastdate = indate;

					$(`<div class="alert ${$(chatContainerTarget).prop('id') == 'default' ? "alert-info border-info" : "alert-warning border-warning mt-2"} fade show rounded-pill px-3 py-1 mx-auto mb-2 shadow" style="width: fit-content;">
							${new Date(indate).toDateString()} <i class="fas fa-chevron-down"></i>
						</div>`)
						.appendTo(chatContainerTarget);
					// .appendTo("#chat_container");
				}

				let templateLiteral = $(`<div ${$(chatContainerTarget).prop('id') == 'default' ? `class="single_chat"` : ``}>
						<div class="${update_available?"last-update":""} media${(chatContainerTarget) ? ` mt-2` : ``}">
							<div class="mr-2" data-toggle="tooltip" data-placement="top" title="${formatDateTime(value.createtime)}">${formatTime(intime)}</div>
							<div class="media-body chat_parent_body" id='${value.chatno}'>
								<div class="d-flex flex-wrap justify-content-between">
									<div class="">
										<a href="javascript:void(0);" class="h6 text-alternate">${value.userfullname}</a>
										${value.tags.length ? ` is with` : ``}
										${value.tags.map(a => `<a href="javascript:void(0);" class="h6 small text-alternate">${a.userfullname}</a>`).join(", ")}
									</div>
									<div class="product_hover_buttons">
										${(value.messenger == LOGGEDIN_USERNO) ?
											`<a href="javascript:void(0);" class="edit_button badge badge-info" role="button" data-toggle="tooltip" data-placement="top" title="Edit Message">
												<i class="fas fa-edit"></i>
											</a>` : ``
										}
										${(value.messenger == LOGGEDIN_USERNO || UCATNO == 19) ?
											`<a href="javascript:void(0);" class="delete_button badge badge-danger" role="button" data-toggle="tooltip" data-placement="top" title="Delete Message">
												<i class="fas fa-trash"></i>
											</a>` : ``
										}
										${(!value.parentchatno) ?
											`<a href="javascript:void(0);" class="reply_button badge badge-primary" role="button" data-toggle="tooltip" data-placement="top" title="Reply to thread">
												<i class="fas fa-comment"></i>
											</a>
											<a href="javascript:void(0);" class="badge badge-info" data-toggle="tooltip" data-placement="top" title="Category">${value.cattitle}</a>
											<a href="javascript:void(0);" class="badge badge-light border" data-toggle="tooltip" data-placement="top" title="Status">${value.statustitle}</a>` : ``
										}
									</div>
								</div>
								<div>${$("<div>").append(value.message).html()}</div>
								<div>${value.attachments.map(a => `<a href="assets/attachments/${a.fileurl}" target="_blank">${a.shorttitle || a.fileurl}</a>`).join(", ")}</div>
								${value.replies?.length ? `<hr class="mb-0">` : ``}
							</div>
						</div>
						${(!value.parentchatno) ?
							`<div class="product_hover_buttons">
								<a href="javascript:void(0);" class="reply_button badge badge-primary" style="width: fit-content;" role="button" data-toggle="tooltip" data-placement="top" title="Reply to thread">
									<i class="fas fa-comment mr-2"></i> Reply
								</a>
							</div>` : ``
						}
					</div>`)
					.appendTo(chatContainerTarget);

				// if (update_available) {
				// 	updated_root = templateLiteral;
				// }

				(function($) {
					templateLiteral.find(`.edit_button`).click(function(e) {
						$("#setup_chat_modal").modal("show");
						$("#setup_chat_modal .modal-title").html(chatContainerTarget ? `Update Reply Message` : `Update Message`);
						$("#setup_chat_modal_form").trigger("reset").data({
							chatno: value.chatno,
							parentchatno: value.parentchatno
						});

						$(`#setup_chat_modal_form [name]`).each((indexInElem, elem) => $(elem).val(value[$(elem).attr("name")]));

						if (chatContainerTarget) {
							$(`#setup_chat_modal_form [name="catno"]`).removeAttr("required").parents(`.input-group `).hide();
							$(`#setup_chat_modal_form [name="statusno"]`).removeAttr("required").parents(`.input-group `).hide();
						}

						$(`#chat_target_container, #chat_attachment_container`).empty();
						chatEditor.setData(value.message);

						$.each(value.tags, (indexInTags, valueOfTags) => {
							let div = $("<div>")
								.data(`userno`, valueOfTags.userno)
								.append(`${valueOfTags.userfullname}
									<button type="button" class="close py-0" style="line-height: 0.8;" aria-label="Close"><span aria-hidden="true" class="small">×</span></button>`)
								.attr({
									class: "chat_target alert alert-info alert-dismissible fade show rounded-pill pl-3 pr-4 py-1 mb-2 mr-1 shadow border-info",
									style: "width: fit-content;"
								})
								.appendTo(`#chat_target_container`);

							(function($) {
								div.find(`button`).click(function(e) {
									e.preventDefault();
									if (confirm("Are you sure?")) {
										div.remove();
										delete_chattarget({
											chattargetno: valueOfTags.chattargetno
										});
									}
								});
							})(jQuery);
						});

						$.each(value.attachments, (indexInAttachments, valueOfAttachments) => {
							let div = $("<div>")
								.append(`${valueOfAttachments.shorttitle || valueOfAttachments.fileurl}
									<button type="button" class="close py-0" style="line-height: 0.8;" aria-label="Close"><span aria-hidden="true" class="small">×</span></button>`)
								.attr({
									class: "chat_attachment alert alert-info alert-dismissible fade show rounded-pill pl-3 pr-4 py-1 mb-2 mr-1 shadow border-info",
									style: "width: fit-content;"
								})
								.appendTo(`#chat_attachment_container`);

							(function($) {
								div.find(`button`).click(function(e) {
									e.preventDefault();
									if (confirm("Are you sure?")) {
										div.remove();
										delete_chat_attachment({
											attachno: valueOfAttachments.attachno
										});
									}
								});
							})(jQuery);
						});
					});

					templateLiteral.find(`.delete_button`).click(function(e) {
						if (confirm("Are you sure?")) {
							delete_chat({
								chatno: value.chatno
							});
						}
					});

					templateLiteral.closest(".single_chat").find(`.reply_button`).click(function(e) {
						$("#setup_chat_modal").modal("show");
						$("#setup_chat_modal .modal-title").html(`Reply to thread`);
						$("#setup_chat_modal_form").trigger("reset").data({
							chatno: -1,
							parentchatno: value.parentchatno || value.chatno
						});

						$(`#setup_chat_modal_form [name="catno"]`).val(value.catno).removeAttr("required").parents(`.input-group `).hide();
						$(`#setup_chat_modal_form [name="statusno"]`).val(value.statusno).removeAttr("required").parents(`.input-group `).hide();

						$(`#chat_target_container, #chat_attachment_container`).empty();
						chatEditor.setData(``);
					});

					if (value.replies?.length) {
						load_channel_chat_detail(value.replies, templateLiteral.find(`.chat_parent_body`));
					}
				})(jQuery);

			});

			// new code on 16 december 2021 [part 3 start]
			//$(chatContainerTarget).data('data', data);

			chat_log[$(chatContainerTarget).attr('id')] = JSON.parse(JSON.stringify(data));
			// new code on 16 dec 2021 [part 3 end]
		}

		function scrollToLatest() {
			// if (chat_log['default'] && $(`#${$(chatContainerTarget).attr('id')} .last-update`).length) {
			if (chat_log['default'] && $(`.last-update`).length) {
				// last_scrollTop = $(updated_root).offset.scrollTop + 150;
				// bg - warning
				console.log(`scrolling to last update`);


				// $.each($(`#${$(chatContainerTarget).attr('id')} .last-update`), (o, p) => {
				$.each($(`.last-update`), (o, p) => {
					if ($(p).find('.last-update').length) {
						$(p).find('.last-update').addClass('bg-info text-white');
					} else {
						$(p).addClass('bg-info text-white');
					}
				});
				last_scrollTop = window.scrollY + $('.last-update')[$('.last-update').length - 1].getBoundingClientRect().top;
				$("html, body").animate({
					scrollTop: last_scrollTop,
					duration: 0
				});
			} else {
				console.log(`scrolling to bottom`);

				$("html, body").animate({
					scrollTop: last_scrollTop || ($(document).height() + 150),
					duration: 0
				});
			}
		}

		$("#chat_attachment_upload_button").click(function(e) {
			e.preventDefault();
			let filetypeno = $(`#setup_chat_modal_form [name="filetypeno"]`).val();
			if (filetypeno) {
				$(`#setup_chat_modal_form [name="fileurl"]`).trigger("click");
			} else {
				toastr.error("File-type must be selected!");
			}
		});

		$(`#setup_chat_modal_form [name="fileurl"]`).change(function(e) {
			let filetypeno = $(`#setup_chat_modal_form [name="filetypeno"]`).val();
			let shorttitle = $(`#setup_chat_modal_form [name="shorttitle"]`).val();

			if (this.files.length) {
				let div = $("<div>")
					.data({
						isnew: true,
						filetypeno,
						shorttitle,
						fileurl: this.files[0]
					})
					.append(`${shorttitle || this.files[0].name}
							<button type="button" class="close py-0" style="line-height: 0.8;" aria-label="Close"><span aria-hidden="true" class="small">×</span></button>`)
					.attr({
						class: "chat_attachment alert alert-info alert-dismissible fade show rounded-pill pl-3 pr-4 py-1 mb-2 mr-1 shadow border-info",
						style: "width: fit-content;"
					})
					.appendTo(`#chat_attachment_container`);

				(function($) {
					div.find(`button`).click(function(e) {
						e.preventDefault();
						div.remove();
					});
				})(jQuery);
			}
		});

		$("#setup_chat_modal_form").submit(function(e) {
			e.preventDefault();

			$(`#setup_chat_modal_form [name="catno"], #setup_chat_modal_form [name="statusno"]`).prop("required", true).parents(`.input-group `).show();

			let json = {
				channelno: CHANNELNO,
				message: chatEditor.getData(),
				catno: $(`#setup_chat_modal_form [name="catno"]`).val(),
				statusno: $(`#setup_chat_modal_form [name="statusno"]`).val(),
			};

			let chatno = parseInt($(this).data("chatno"), 10) || -1;
			if (chatno > 0) {
				json.chatno = chatno;
			}

			let parentchatno = parseInt($(this).data("parentchatno"), 10) || -1;
			if (parentchatno > 0) {
				json.parentchatno = parentchatno;
			}

			if (json.catno <= 0) {
				toastr.error("Category must be selected!");
				return;
			}

			if (json.statusno <= 0) {
				toastr.error("Status must be selected!");
				return;
			}

			if (json.message.length <= 0) {
				toastr.error("Message Text cannot be empty!");
				return;
			}

			setup_chat(json);
		});

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

					$("#setup_chat_modal").modal("hide");
					toastr.success(resp.message);
					get_channel_chat_detail();
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

		function set_chattarget(json) {
			$.post(`php/ui/chat/set_chattarget.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_channel_chat_detail();
				}
			}, `json`);
		}

		function delete_chattarget(json) {
			$.post(`php/ui/chat/remove_chattarget.php`, json, resp => {
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

		$("#add_chat_button").click(function(e) {
			$("#setup_chat_modal").modal("show");
			$("#setup_chat_modal .modal-title").html(`Create New Message`);
			$("#setup_chat_modal_form").trigger("reset").data({
				chatno: -1,
				parentchatno: -1
			});
			$(`#chat_target_container, #chat_attachment_container`).empty();
			chatEditor.setData(``);
		});

		$("#new_chat_message_card_container").width($("#chat_container").width() - 15);

		setInterval(function() {
			get_channel_chat_detail();
		}, 300000);

		setInterval(function() {
			get_channel_task_detail();
		}, 1800000);

		$(`#load_previous_task_progress_button`).click(function(e) {
			let pageno = $(this).data(`pageno`);
			if (pageno == null) {
				pageno = 2;
			} else {
				++pageno;
			}

			$(this).data(`pageno`, pageno);
			get_channel_task_detail(pageno);
		});

		get_channel_task_detail(1);

		function get_channel_task_detail(pageno = 1) {
			if (pageno == 1) {
				$(`#task_progress_container`).empty();
			}

			let json = {
				channelno: CHANNELNO,
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
						$(`#load_previous_task_progress_button`).siblings().show();
					}

					show_task(resp.results, `#task_progress_container`);
				}
			}, `json`);
		}

		$(`#chat_tab`).on(`shown.bs.tab`, () => $(`#channel_new_thread`).show());
		$(`#task_progress_tab`).on(`shown.bs.tab`, () => $(`#channel_new_thread`).hide());
		let appContainer = $(`.app-container`);
		if ($(window).width() < 992 && !appContainer.hasClass(`closed-sidebar`) && !appContainer.hasClass(`closed-sidebar-mobile`)) {
			appContainer.addClass(`closed-sidebar closed-sidebar-mobile`);
		}
	</script>

</body>

</html>