<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once("header.php"); ?>

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

	<style>
		table.dataTable tbody th,
		table.dataTable tbody td {
			padding: .3rem;
		}

		.user_modal {
			padding: 0px !important;
		}

		.user_modal_dialog {
			max-width: 60%;
			margin-top: 0px;
		}

		@media only screen and (max-width: 768px) {
			.user_modal_dialog {
				max-width: 100%;
			}
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

					<div class="card mb-3">
						<div class="card-header d-flex flex-wrap justify-content-between">
							<h5 class="font-weight-bold">Users</h5>
							<button id="users_add_button" class="btn btn-primary rounded-pill px-3 shadow" type="button"><i class="fas fa-plus-circle mr-2"></i>Add</button>
						</div>
						<div class="card-body">
							<form id="users_filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Users Filter Form</legend>

									<div class="row">
										<div class="col-md-6 col-lg-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">User Category</span>
												</div>
												<select name="ucatno" class="form-control shadow-sm">
													<option value="-1">All</option>
												</select>
											</div>
										</div>

										<div class="col-md-6 col-lg-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">User Status</span>
												</div>
												<select name="isactive" class="form-control shadow-sm">
													<option value="-1">All</option>
													<option value="1">Active</option>
													<option value="0">Inactive</option>
												</select>
											</div>
										</div>

										<div class="col-lg-4">
											<div class="d-flex flex-wrap justify-content-end mb-3">
												<button class="btn btn-primary font-weight-bold rounded-pill px-4 shadow mx-1" type="submit">
													<i class="fa fa-search mr-1"></i> Search
												</button>

												<button class="btn btn-warning font-weight-bold rounded-pill px-4 shadow mx-1" type="reset">
													<i class="fa fa-history mr-1"></i> Reset
												</button>
											</div>
										</div>
									</div>
								</fieldset>
							</form>

							<div class="table-responsive my-3">
								<table id="users_table" class="table table-sm table-striped table-hover table-bordered mb-0">
									<thead class="table-secondary">
										<tr>
											<th>Photo</th>
											<th>Username</th>
											<th>Name</th>
											<th>Supervisor </th>
											<th>Affiliation</th>
											<th>Contact</th>
											<th>User Type</th>
											<th>Permission Level</th>
											<th class="text-center">Status</th>
											<th class="text-center">Reset Password</th>
											<th class="text-center">Action</th>
										</tr>
									</thead>
									<tbody> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- USERS SETUP MODAL -->
	<div id="users_setup_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<form id="users_setup_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup User</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 form-group">
								<label class="d-block">
									First Name <span class="text-danger">*</span>
									<input name="firstname" class="form-control shadow-sm mt-2" type="text" placeholder="First Name..." required>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Last Name
									<input name="lastname" class="form-control shadow-sm mt-2" type="text" placeholder="Last Name...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									User Name <span class="text-danger">*</span>
									<input name="username" class="form-control shadow-sm mt-2" type="text" placeholder="User Name..." required>
									<div class="invalid-feedback">Please provide a valid city.</div>
									<div class="valid-feedback">Looks good!</div>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Job Title
									<input name="jobtitle" class="form-control shadow-sm mt-2" type="text" placeholder="Job Title...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Email
									<input name="email" class="form-control shadow-sm mt-2" type="email" placeholder="Email...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Contact No
									<input name="primarycontact" class="form-control shadow-sm mt-2" type="text" placeholder="Contact No...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Affiliation
									<input name="affiliation" class="form-control shadow-sm mt-2" type="text" placeholder="Affiliation...">
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									User Category
									<select name="ucatno" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Supervisor
									<select name="supervisor" class="form-control shadow-sm mt-2"></select>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block mb-0">
									Permission Level <span class="text-danger">*</span>
									<select name="permissionlevel" class="form-control shadow-sm mt-2" required>
										<option value="">Select...</option>
										<option value="0">Employee</option>
										<option value="1">Senior Employee</option>
										<option value="3">Manager</option>
										<option value="7">Admin</option>
									</select>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Password <span class="text-danger">*</span>
									<input name="password" class="form-control shadow-sm mt-2" type="password" placeholder="Password..." required>
								</label>
							</div>

							<div class="col-md-6 form-group">
								<label class="d-block">
									Retype-password <span class="text-danger">*</span>
									<input name="retype_password" class="form-control shadow-sm mt-2" type="password" placeholder="Retype-password..." required>
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

	<!-- RESET PASSWORD MODAL -->
	<div id="reset_password_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="reset_password_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup User</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block">
								Password <span class="text-danger">*</span>
								<input name="password" class="form-control shadow-sm mt-2" type="password" placeholder="Password..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block">
								Retype-password <span class="text-danger">*</span>
								<input name="retype_password" class="form-control shadow-sm mt-2" type="password" placeholder="Retype-password..." required>
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

		let dataTable = $("#users_table").DataTable();
		$(dataTable.table().container()).addClass("table-responsive");

		get_list_usercat();
		load_filtered_users();

		function get_list_usercat() {
			$(`#users_filter_form [name="ucatno"]`).empty().append(`<option value="-1">All</option>`);
			$(`#users_setup_modal_form [name="ucatno"]`).empty();

			$.ajax({
				type: "POST",
				url: "php/ui/user/list_usercat.php",
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						$.each(resp.data, (index, value) => {
							$(`#users_filter_form [name="ucatno"]`).append(new Option(value.ucattitle, value.ucatno));
							$(`#users_setup_modal_form [name="ucatno"]`).append(new Option(value.ucattitle, value.ucatno));
						});
					}
				}
			});
		}

		$("#users_filter_form").submit(function(e) {
			e.preventDefault();
			load_filtered_users();
		});

		function load_filtered_users() {
			if ($.fn.DataTable.isDataTable("#users_table")) {
				$("#users_table").DataTable().clear().destroy();
			}

			let json = Object.fromEntries((new FormData($("#users_filter_form")[0])).entries());

			$.ajax({
				type: "POST",
				url: "php/ui/user/get_users.php",
				data: json,
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						pageSettingsFunc(`get_users`, resp.results);
						show_filtered_users(resp.results);
					}
				}
			});
		}

		function show_filtered_users(data) {
			dataTable = $("#users_table").DataTable({
				"data": data,
				"ordering": false,
				"columns": [{
						"data": "photo_url",
						"render": (data, type, row) => `<div class='text-center'><img src='${row.photo_url||"assets/image/user_icon.png"}' width="40"/></div>`
					},
					{
						"data": "username",
						"render": (data, type, row) => `${row.username || ""} <br> ${row.isactive == 0 ? `<div class="badge badge-danger">INACTIVE</div>` : `<div class="badge badge-success">ACTIVE</div>`}`
					},
					{
						"data": "firstname",
						"render": (data, type, row) => `${row.firstname || ""}${row.lastname ? ` ${row.lastname}` : ""}`
					},
					{
						"data": "supervisor",
						"render": (data, type, row) => `${row.supervisor_name  || ""}`
					},
					{
						"data": "affiliation",
						"render": (data, type, row) => `${row.affiliation || ""} <br> ${row.jobtitle || ""}`
					},
					{
						"data": "email",
						"render": (data, type, row) => `${row.email ? `<i class="fas fa-envelope mr-2"></i>${row.email}` : ""} <br>
								${row.primarycontact ? `<i class="fas fa-phone mr-2"></i>${row.primarycontact}` : ""}`
					},
					{
						"data": "ucattitle",
						"render": (data, type, row) => `${row.ucattitle || ""}`
					},
					{
						"data": "permissionlevel",
						"render": (data, type, row) => {
							if (row.permissionlevel == 0 || row.permissionlevel == null) {
								return `Employee`;
							} else if (row.permissionlevel == 1) {
								return `Senior Employee`;
							} else if (row.permissionlevel == 3) {
								return `Manager`;
							} else if (row.permissionlevel == 7) {
								return `Admin`;
							} else {
								return ``;
							}
						}
					},
					{
						"data": null,
						"searchable": false,
						"sortable": false,
						"render": (data, type, row) => {
							return `<div class="d-flex justify-content-center">
										<button class="status_change_button btn btn-sm btn-alternate font-weight-bold rounded-pill px-3 shadow grow" type="button">
											${row.isactive == 0 ? "Activate" : "Deactivate"}
										</button>
									</div>`;
						}
					},
					{
						"data": null,
						"searchable": false,
						"sortable": false,
						"render": (data, type, row) => {
							return `<div class="d-flex justify-content-center">
										<button class="reset_password_button btn btn-sm btn-alternate font-weight-bold rounded-pill px-3 shadow grow" type="button">Reset</button>
									</div>`;
						}
					},
					{
						"data": null,
						"searchable": false,
						"sortable": false,
						"render": (data, type, row) => {
							return `<div class="d-flex justify-content-center">
										<button class="edit_button btn btn-sm btn-info rounded-circle shadow grow m-1" type="button" title="Edit User">
											<i class="far fa-edit"></i>
										</button>
										<button class="delete_button btn btn-sm btn-danger rounded-circle shadow grow m-1" type="button" title="Delete User">
											<i class="fas fa-times"></i>
										</button>
									</div>`;
						}
					}
				]
			});
		}

		$("#users_table tbody").on("click", `.status_change_button`, function(e) {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();
			if (confirm("Are you sure?")) {
				status_change_of_a_user({
					userno: data.userno,
					isactive: data.isactive == 0 ? 1 : 0
				});
			}
		});

		$("#users_table tbody").on("click", `.reset_password_button`, function(e) {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();
			$("#reset_password_modal").modal("show");
			$("#reset_password_modal_form").data("userno", data.userno);
		});

		$("#users_table tbody").on("click", `.edit_button`, function(e) {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();

			$("#users_setup_modal").modal("show");
			$("#users_setup_modal_form").data("userno", data.userno);

			let users = pageSettingsFunc(`get_users`) || [];
			users = users.filter(a => a.userno != data.userno);

			let supervisorSelect = $(`#users_setup_modal_form [name="supervisor"]`).empty().append(`<option value="">Select...</option>`);

			$.each(users, (_index, value) => {
				supervisorSelect.append(new Option(`${value.firstname || ``} ${value.lastname || ``}`, value.userno));
			});

			$(`#users_setup_modal_form [name="password"], #users_setup_modal_form [name="retype_password"]`).removeAttr("required").parents(`.form-group`).hide();

			$(`#users_setup_modal_form [name]`).each((i, elem) => {
				let elementName = $(elem).attr("name");
				if (data[elementName] != null) {
					$(elem).val(data[elementName]);
				}
			});
		});

		$("#users_table tbody").on("click", `.delete_button`, function(e) {
			let row = $(this).parents("tr");
			let data = dataTable.row(row).data();
			if (confirm("Are you sure?")) {
				delete_a_user({
					userno: data.userno
				});
			}
		});

		function status_change_of_a_user(json) {
			$.ajax({
				type: "POST",
				url: "php/ui/user/toggle_user_activation.php",
				data: json,
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						load_filtered_users();
					}
				}
			});
		}

		function delete_a_user(json) {
			$.ajax({
				type: "POST",
				url: "php/ui/user/remove_user.php",
				data: json,
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						load_filtered_users();
					}
				}
			});
		}

		$("#users_add_button").click(function(e) {
			let users = pageSettingsFunc(`get_users`) || [];
			let supervisorSelect = $(`#users_setup_modal_form [name="supervisor"]`).empty().append(`<option value="">Select...</option>`);

			$.each(users, (_index, value) => {
				supervisorSelect.append(new Option(`${value.firstname || ``} ${value.lastname || ``}`, value.userno));
			});

			$("#users_setup_modal").modal("show");
			$("#users_setup_modal_form").data("userno", -1).trigger("reset");
			$(`#users_setup_modal_form [name="password"], #users_setup_modal_form [name="retype_password"]`).prop("required", true).parents(`.form-group`).show();
		});

		$("#users_setup_modal_form").submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			let userno = parseInt($(this).data("userno")) || 0;

			if (userno > 0) {
				json.userno = userno;
			}

			$.ajax({
				type: "POST",
				url: "php/ui/user/setup_user.php",
				data: json,
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						load_filtered_users();

						$("#users_setup_modal").modal("hide");
						$("#users_setup_modal_form").trigger("reset");
					}
				}
			});
		});

		$("#reset_password_modal_form").submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			let userno = parseInt($(this).data("userno")) || 0;

			if (userno > 0) {
				json.userno = userno;
			} else {
				return;
			}

			$.ajax({
				type: "POST",
				url: "php/ui/user/reset_emp_password.php",
				data: json,
				success: (result) => {
					let resp = $.parseJSON(result);

					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						load_filtered_users();

						$("#reset_password_modal").modal("hide");
						$("#reset_password_modal_form").trigger("reset");
					}
				}
			});
		});

		$(`#users_setup_modal_form [name="username"]`).on("blur", function(e) {
			let json = {
				username: $.trim($(this).val())
			};

			if (json.username.length >= 3) {
				$.ajax({
					type: "POST",
					url: "php/ui/user/check_username_exists.php",
					data: json,
					success: (result) => {
						let resp = $.parseJSON(result);

						if (resp.error) {
							$(`#users_setup_modal_form [name="username"]`).siblings(`.valid-feedback`).hide().siblings(`.invalid-feedback`).html(resp.message).show();
						} else {
							$(`#users_setup_modal_form [name="username"]`).siblings(`.invalid-feedback`).hide().siblings(`.valid-feedback`).html(resp.message).show();
						}
					}
				});
			} else {
				$(`#users_setup_modal_form [name="username"]`).siblings(`.valid-feedback`).hide().siblings(`.invalid-feedback`).html(`Minimum username length is 3.`).show();
			}
		});
	</script>

</body>

</html>