<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka");
	?>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner pt-3 pl-3 pl-lg-0 pr-3">

					<div class="card mb-3">
						<div class="card-header sticky-top" style="top: 60px;">
							<div class="d-flex justify-content-between w-100">
								<h5>Channel</h5>
								<button id="channel_add_button" class="btn btn-primary font-weight-bold rounded-pill px-3 py-0 shadow" type="button">
									<i class="fa fa-plus-circle mr-1"></i> Add
								</button>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive shadow-sm rounded">
								<table class="table table-sm table-striped table-bordered table-hover mb-0">
									<thead class="table-primary">
										<tr>
											<th>SL</th>
											<th>Parent Channel</th>
											<th>Sub-Channel</th>
											<th class="border-right-0">Team Members</th>
											<th class="border-left-0"></th>
										</tr>
									</thead>
									<tbody id="channel_table_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="setup_channel_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="setup_channel_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Channels Setup</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block">
								Channel Title <span class="text-danger">*</span>
								<input name="channeltitle" class="form-control shadow-sm mt-2" type="text" placeholder="Channel Title..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								Parent Channel
								<select name="parentchannel" class="form-control shadow-sm mt-2"></select>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								Status
								<select name="isactive" class="form-control shadow-sm mt-2">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
									<option value="-1">Archived</option>
								</select>
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

	<div id="setup_channel_members_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="setup_channel_members_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Channel Member</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block">
								Channel
								<select name="channelno" class="form-control shadow-sm mt-2" disabled></select>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								<div class="mb-2">User</div>
								<select name="userno" class="form-control shadow-sm" style="width: 100%;"></select>
							</label>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="button" class="btn btn-secondary rounded-pill px-4 shadow" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<script>
		get_filtered_users();

		function get_filtered_users() {
			let json = {
				isactive: 1,
				ucatno: -1
			};

			$(`#setup_channel_members_modal_form [name="userno"]`).empty();

			$.post(`php/ui/user/get_users.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$.each(resp.results, (index, value) => $(`#setup_channel_members_modal_form [name="userno"]`).append(new Option(`${value.firstname || ""}${value.lastname ? ` ${value.lastname}` : ""}`, value.userno)));

					$(`#setup_channel_members_modal_form [name="userno"]`)
						.select2({
							placeholder: "Select Member...",
							allowClear: true,
							multiple: true,
						})
						.val(null)
						.trigger("change");
				}
			}, `json`);
		}

		$(`#setup_channel_members_modal_form [name="userno"]`).on("select2:select", function(e) {
			let data = e.params.data;
			// console.log(data);
			let channelno = $(`#setup_channel_members_modal_form [name="channelno"]`).val();
			if (data.id) {
				$.post(`php/ui/settings/channel/set_channelmember.php`, {
					userno: data.id,
					channelno
				}, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						//get_list_of_channels();
						// $(`#setup_channel_members_modal_form [name="channelno"]`).val(channelno);
						// $("#setup_channel_members_modal").modal("hide");
					}
				}, `json`);
			}
		});

		$(`#setup_channel_members_modal_form [name="userno"]`).on("select2:unselect", function(e) {
			let data = e.params.data;
			// console.log(data);
			let channelno = $(`#setup_channel_members_modal_form [name="channelno"]`).val();
			if (data.id && confirm("Are you sure?")) {
				$.post(`php/ui/settings/channel/remove_channelmember.php`, {
					userno: data.id,
					channelno
				}, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						//get_list_of_channels();
						// $(`#setup_channel_members_modal_form [name="channelno"]`).val(channelno);
						// $("#setup_channel_members_modal").modal("hide");
					}
				}, `json`);
			}
		});

		get_list_of_channels();

		function get_list_of_channels() {
			$("#channel_table_tbody").empty();
			$(`#setup_channel_modal_form [name="parentchannel"]`).empty();
			$(`#setup_channel_members_modal_form [name="channelno"]`).empty();

			$(`#setup_channel_modal_form [name="parentchannel"]`).append(new Option(`Select...`, ``));

			$.post(`php/ui/settings/channel/get_channels.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					load_all_channel(resp.data);
					$.each(resp.data, (index, value) => {
						$(`#setup_channel_modal_form [name="parentchannel"]`).append(new Option(value.channeltitle, value.channelno));
						$(`#setup_channel_members_modal_form [name="channelno"]`).append(new Option(value.channeltitle, value.channelno));
						$.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
							$(`#setup_channel_members_modal_form [name="channelno"]`).append(new Option(valueOfSubChannels.channeltitle, valueOfSubChannels.channelno));
						});
					});
				}
			}, `json`);
		}

		function load_all_channel(data, parentchannel) {
			$.each(data, (index, value) => {
				let slCell = $("<td>").append($(`#channel_table_tbody tr`).length + 1);
				let parentchannelCell = $("<td>").append(parentchannel || "-");
				let channeltitleCell = $("<td>");
				let channeltitleDiv = $("<div>")
				.attr('style', 'display: flex; justify-content:space-between;')
				.append(`
					<i class='mr-1 mr-lg-2 my-auto fa ${value.isactive==0?'fa-lock text-secondary':value.isactive<0?'fa-archive text-warning':'fa-check-circle text-success'} mr-1'></i>
					<div style='max-width: 30vw; text-align: left;'>${value.channeltitle}</div>
				`).appendTo(channeltitleCell);

				let channelActionDiv = $("<div>").attr("class", "ml-auto d-flex justify-content-end p-0").appendTo(channeltitleDiv);
				let editButton = $("<i>")
					.appendTo(channelActionDiv)
					.attr({
						"class": "fas fa-pen text-info mx-1 ml-1 my-auto",
						"type": "button",
						"title": "Edit Channel",
						"style": "cursor: pointer; border-radius: 100%;padding: 5px;border: 0.5px solid;"
					});

				let deleteButton = $("<i>")
					.appendTo(channelActionDiv)
					.attr({
						"class": "fas fa-times text-danger mx-1 ml-1 my-auto",
						"type": "button",
						"title": "Delete Channel",
						"style": "cursor: pointer; border-radius: 100%;padding: 5px;border: 0.5px solid;"
					});

				let membersCell = $("<td>").attr("class", "border-right-0").append(value.members?.length ? value.members.map(a => `${a.firstname}${a.lastname ? ` ${a.lastname}` : ``}`).join(", ") : "-");

				let actionCell = $("<td>").attr("class", "border-left-0");
				let actionDiv = $("<div>").attr("class", "d-flex justify-content-end p-0").appendTo(actionCell);

				// value.parentchannel
				if (true) {
					let memberButton = $("<button>")
						.append(`<i class="far fa-edit"></i>`)
						.appendTo(actionDiv)
						.attr({
							"class": "btn btn-sm btn-primary rounded-circle shadow grow m-1",
							"type": "button",
							"title": "Add Member"
						});

					(function($) {
						memberButton.click(function(e) {
							e.preventDefault();
							$("#setup_channel_members_modal").modal("show");
							$(`#setup_channel_members_modal_form [name="channelno"]`).val(value.channelno);
							$(`#setup_channel_members_modal_form [name="userno"]`).val(value.members.map(a => a.userno) || []).trigger("change");
						});
					})(jQuery);
				}

				$("<tr>").append(slCell, parentchannelCell, channeltitleCell, membersCell, actionCell).appendTo("#channel_table_tbody");
				if (value.subchannels) {
					load_all_channel(value.subchannels, value.channeltitle);
				}

				(function($) {
					editButton.click(function(e) {
						e.preventDefault();
						$("#setup_channel_modal").modal("show");
						$("#setup_channel_modal .modal-title").text(`Update Channel`);
						$("#setup_channel_modal_form").trigger("reset").data("channelno", value.channelno);
						$(`#setup_channel_modal_form [name]`).each((i, elem) => $(elem).val(value[$(elem).attr("name")]));
					});

					deleteButton.click(function(e) {
						e.preventDefault();
						if (confirm("Are you sure?")) {
							delete_channel({
								channelno: value.channelno
							});
						}
					});
				})(jQuery);
			});
		}

		$("#channel_add_button").click(function(e) {
			e.preventDefault();
			$("#setup_channel_modal").modal("show");
			$("#setup_channel_modal .modal-title").text(`Add Channel`);
			$("#setup_channel_modal_form").trigger("reset").data("channelno", -1);
		});

		$("#setup_channel_modal_form").submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(e.target)).entries());

			let channelno = parseInt($(this).data("channelno"), 10) || 0;
			if (channelno > 0) {
				json.channelno = channelno;
			}

			$.post(`php/ui/settings/channel/setup_channel.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$("#setup_channel_modal").modal("hide");
					get_list_of_channels();
				}
			}, `json`);
		});

		function delete_channel(json) {
			$.post(`php/ui/settings/channel/remove_channel.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_list_of_channels();
				}
			}, `json`);
		}
	</script>

</body>

</html>