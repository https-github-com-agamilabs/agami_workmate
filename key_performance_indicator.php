<?php
date_default_timezone_set("Asia/Dhaka");
include_once "php/ui/login/check_session.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once("header.php"); ?>

	<style>
		#employee_designation_form .custom-control-label::before,
		#employee_designation_form .custom-control-label::after {
			left: 6rem;
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

					<ul class="tabs-animated-shadow tabs-animated nav">
						<li class="nav-item">
							<a role="tab" class="nav-link active" data-toggle="tab" href="#kpi_score_tab">
								<span>KPI Score</span>
							</a>
						</li>
						<li class="nav-item">
							<a role="tab" class="nav-link" data-toggle="tab" href="#employee_designation_tab">
								<span>Employee Designation</span>
							</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane" id="employee_designation_tab" role="tabpanel">
							<!-- EMPLOYEE DESIGNATION -->
							<div class="card mb-3">
								<div class="card-header justify-content-between">
									<h5 class="font-weight-bold">Employee Designation</h5>
								</div>
								<div class="card-body">
									<form id="employee_designation_form">
										<fieldset class="border rounded px-3 pt-3 mb-3">
											<div class="position-relative row form-group">
												<label class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Employee</label>
												<div class="col-sm-10">
													<select name="empno" class="form-control shadow-sm" required></select>
												</div>
											</div>

											<div class="position-relative row form-group">
												<label class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Designation</label>
												<div class="col-sm-10">
													<select name="desigid" class="form-control shadow-sm" required></select>
												</div>
											</div>

											<div class="position-relative row form-group">
												<label class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Pay Level</label>
												<div class="col-sm-10">
													<input name="paylevelno" class="form-control shadow-sm" type="number" placeholder="Enter Pay Level..." required>
												</div>
											</div>
										</fieldset>

										<div class="position-relative row form-group">
											<label for="joiningdate_input" class="col-sm-2 col-form-label">Joining Date</label>
											<div class="col-sm-10">
												<input id="joiningdate_input" name="joiningdate" class="form-control shadow-sm" type="date" required>
											</div>
										</div>

										<div class="position-relative form-group">
											<div class="custom-checkbox custom-control pl-0">
												<input type="checkbox" id="still_working_checkbox" class="custom-control-input">
												<label class="custom-control-label" for="still_working_checkbox">Still Working</label>
											</div>
										</div>

										<div class="position-relative row form-group">
											<label for="enddate_input" class="col-sm-2 col-form-label">End Date</label>
											<div class="col-sm-10">
												<input id="enddate_input" name="enddate" class="form-control shadow-sm" type="date" disabled>
											</div>
										</div>

										<div class="text-center">
											<button id="del_emp_info_button" class="btn btn-danger px-5 shadow" type="button" style="display: none;">Delete</button>
											<button class="btn btn-primary px-5 shadow" type="submit">Save</button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div class="tab-pane active" id="kpi_score_tab" role="tabpanel">
							<!-- KEY PERFORMANCE INDICATOR SCORE -->
							<div class="card mb-3">
								<div class="card-header justify-content-between">
									<h5 class="font-weight-bold">Key Performance Indicator Score</h5>
								</div>
								<div class="card-body">
									<form id="kpi_score_filter_form">
										<fieldset class="border rounded px-3 pt-3 mb-3">
											<div class="position-relative row form-group">
												<label for="employee_select" class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Employee</label>
												<div class="col-sm-10">
													<select id="employee_select" name="empno" class="form-control shadow-sm" required></select>
												</div>
											</div>

											<div class="position-relative row form-group">
												<label for="designation_select" class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Designation</label>
												<div class="col-sm-10">
													<select id="designation_select" name="desigid" class="form-control shadow-sm" required disabled></select>
												</div>
											</div>

											<div class="position-relative row form-group">
												<label for="paylevel_input" class="col-sm-2 col-form-label pl-1 pl-sm-2 pl-md-3 pr-0">Pay Level</label>
												<div class="col-sm-10">
													<input id="paylevel_input" name="paylevelno" class="form-control shadow-sm" type="number" placeholder="Enter Pay Level..." required disabled>
												</div>
											</div>

											<div class="text-right mb-2">
												<button class="btn btn-primary px-5 shadow" type="submit">Filter</button>
											</div>
										</fieldset>
									</form>

									<div class="table-responsive shadow-sm">
										<form id="emp_kpisetting_add_form" class="w-100">
											<div class="d-table w-100">
												<div class="d-table-row">
													<div class="d-table-cell align-bottom" style="width:300px;min-width:300px;">
														<select name="kpino" class="form-control shadow-sm rounded-0" title="KPI Title" required> </select>
													</div>
													<div class="d-table-cell" style="width:185px;min-width:185px;">
														<div class="input-group">
															<input name="score" class="form-control shadow-sm rounded-0" type="number" placeholder="Score..." required>
															<div class="input-group-append">
																<span class="measure_unit input-group-text rounded-0">Unit</span>
															</div>
														</div>
													</div>
													<div class="d-table-cell" style="min-width:200px;">
														<input name="comment" class="form-control shadow-sm rounded-0" type="text" placeholder="Comment..." required>
													</div>
													<div class="d-table-cell align-middle" style="width:110px;min-width:110px;">
														<button class="btn btn-primary btn-block btn-lg rounded-0 shadow" type="submit">Add</button>
													</div>
												</div>
											</div>
										</form>

										<table class="table table-sm table-bordered table-striped table-hover mb-0">
											<thead class="table-primary">
												<tr>
													<th style="width:50px;min-width:50px;">SL</th>
													<th style="width:250px;min-width:250px;">KPI</th>
													<th style="width:185px;min-width:185px;">Score</th>
													<th style="min-width:200px;">Comment</th>
													<th class="text-center" style="width:110px;min-width:110px;">Action</th>
												</tr>
											</thead>
											<tbody id="emp_kpi_score_tbody"></tbody>
										</table>
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

		get_users();

		function get_users() {
			$(`#employee_designation_form [name="empno"]`).empty();
			$(`#kpi_score_filter_form [name="empno"]`).empty();

			$.post(`php/ui/user/get_users.php`, resp => {
				if (resp.error) {
					// toastr.error(resp.message);
				} else {
					show_users(resp.results);
				}
			}, `json`);
		}

		function show_users(data) {
			let select1 = $(`#employee_designation_form [name="empno"]`).append(`<option value="">Select employee...</option>`);
			let select2 = $(`#kpi_score_filter_form [name="empno"]`).append(`<option value="">Select employee...</option>`);

			$.each(data, (index, value) => {
				$(`<option value="${value.userno}">
						${value.firstname}
						${value.lastname ? `${value.lastname}` : ``}
						${value.jobtitle ? `(${value.jobtitle})` : ``}
					</option>`)
					.appendTo(select1);

				$(`<option value="${value.userno}">
						${value.firstname}
						${value.lastname ? `${value.lastname}` : ``}
						${value.jobtitle ? `(${value.jobtitle})` : ``}
					</option>`)
					.appendTo(select2);
			});
		}

		get_designations();

		function get_designations() {
			$(`#employee_designation_form [name="desigid"]`).empty();

			$.post(`php/ui/kpi/get_designations.php`, resp => {
				if (resp.error) {
					// toastr.error(resp.message);
				} else {
					show_designations(resp.data);
				}
			}, `json`);
		}

		function show_designations(data) {
			let select1 = $(`#employee_designation_form [name="desigid"]`).append(`<option value="">Select designation...</option>`);

			$.each(data, (index, value) => {
				$(`<option value="${value.desigid}">${value.desigtitle}</option>`)
					.appendTo(select1);
			});
		}

		$(`[name="empno"], [name="desigid"]`, `#employee_designation_form`).on(`change`, function(e) {
			get_an_emp_info();
		});

		$(`[name="paylevelno"]`, `#employee_designation_form`)
			.on(`blur`, function(e) {
				get_an_emp_info();
			})
			.on(`keyup`, function(e) {
				if (e.keyCode == 13) {
					get_an_emp_info();
				}
			});

		function get_an_emp_info() {
			$(`#del_emp_info_button`).hide();

			let form = $(`#employee_designation_form`);

			let json = {
				empno: $(`[name="empno"]`, form).val(),
				desigid: $(`[name="desigid"]`, form).val(),
				paylevelno: $(`[name="paylevelno"]`, form).val()
			};

			if (!json.empno || json.empno <= 0 || !json.desigid || json.desigid <= 0 || !json.paylevelno || json.paylevelno <= 0) {
				return;
			}

			$.post(`php/ui/kpi/get_an_emp_info.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_an_emp_info(resp.data);
				}
			}, `json`);
		}

		function show_an_emp_info(data) {
			let form = $(`#employee_designation_form`);

			$(`#still_working_checkbox`).prop(`checked`, true);
			$(`[name="enddate"]`, form).val(``).prop(`disabled`, true);

			if (data) {
				$(`#del_emp_info_button`).show();

				$(`[name="joiningdate"]`, form).val(data.joiningdate || ``);

				if (data.enddate && data.enddate.length) {
					$(`#still_working_checkbox`).prop(`checked`, false);
					$(`[name="enddate"]`, form).val(data.enddate).prop(`disabled`, false);
				}
			}
		}

		$(`#still_working_checkbox`).on(`input`, function(e) {
			let elem = $(`#employee_designation_form [name="enddate"]`).prop(`disabled`, this.checked);

			if (this.checked) {
				elem.val(``);
			}
		});

		$(`#employee_designation_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());

			$.post(`php/ui/kpi/setup_emp_designation.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(this).trigger(`reset`);
				}
			}, `json`);
		});

		$(`#del_emp_info_button`).click(function(e) {
			let form = $(`#employee_designation_form`);

			let json = {
				empno: $(`[name="empno"]`, form).val(),
				desigid: $(`[name="desigid"]`, form).val(),
				paylevelno: $(`[name="paylevelno"]`, form).val()
			};

			if (!json.empno || json.empno <= 0 || !json.desigid || json.desigid <= 0 || !json.paylevelno || json.paylevelno <= 0) {
				return;
			}

			$.post(`php/ui/kpi/del_an_emp_info.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					form.trigger(`reset`);
				}
			}, `json`);
		});
	</script>

	<script>
		get_kpis();

		function get_kpis() {
			let select = $(`#emp_kpisetting_add_form [name="kpino"]`).empty();

			$.post(`php/ui/kpi/get_kpis.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					pageSettingsFunc(`kpis`, resp.data);
					show_kpis(resp.data, select);
				}
			}, `json`);
		}

		function show_kpis(data, select) {
			select.append(`<option value="">Select kpi setting...</option>`);

			$.each(data, (index, value) => {
				$(`<option value="${value.kpino}">${value.kpititle}</option>`)
					.data(value)
					.appendTo(select);
			});
		}

		$(`#kpi_score_filter_form [name="empno"]`).change(function(e) {
			if (this.value > 0) {
				get_emp_current_designation({
					empno: this.value
				});
			}
		});

		function get_emp_current_designation(json) {
			let form = $(`#kpi_score_filter_form`);
			$(`[name="desigid"]`, form).empty();
			$(`[name="paylevelno"]`, form).val(``);

			$.post(`php/ui/kpi/get_emp_current_designation.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					$(`[name="desigid"]`, form)
						.append(`<option value="${resp.data.desigid}">${resp.data.desigtitle}</option>`);

					$(`[name="paylevelno"]`, form).val(resp.data.paylevelno || ``);
				}
			}, `json`);
		}

		$(`#kpi_score_filter_form`).submit(function(e) {
			e.preventDefault();
			get_emp_kpiscore();
		});

		function get_emp_kpiscore() {
			$(`#emp_kpi_score_tbody`).empty();

			let json = {};

			$(`#kpi_score_filter_form [name]`).each((index, elem) => {
				json[$(elem).attr(`name`)] = $(elem).val();
			});

			if (!json.desigid || json.desigid <= 0) {
				toastr.error(`Your selected employee doesn't have any designation. Please set employee designation first.`);
				return;
			}

			if (!json.paylevelno || json.paylevelno <= 0) {
				toastr.error(`Your selected employee doesn't have any pay level. Please set employee designation first.`);
				return;
			}

			$.post(`php/ui/kpi/get_emp_kpiscore.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					show_emp_kpiscore(resp.data);
				}
			}, `json`);
		}

		function show_emp_kpiscore(data) {
			let target = $(`#emp_kpi_score_tbody`);

			let kpis = pageSettingsFunc(`kpis`) || [];

			$.each(data, (index, value) => {
				let measure_unit = kpis.find(a => a.kpino == value.kpino).measureunit || ``;

				let template = $(`<tr>
						<td class="p-0">
							<div class="p-1">${1 + index}</div>
						</td>
						<td class="p-0">
							<div class="kpititle p-1">${value.kpititle || ``}</div>
							<select name="kpino" class="form-control shadow-sm rounded-0" style="display:none;" title="KPI Title" required></select>
						</td>
						<td class="p-0">
							<div class="score p-1">
								${value.score || ``} ${measure_unit}
							</div>
							<div class="score input-group" style="display:none;">
								<input name="score" class="form-control shadow-sm rounded-0" type="number" placeholder="Score..." required>
								<div class="input-group-append">
									<span class="measure_unit input-group-text rounded-0">${measure_unit}</span>
								</div>
							</div>
						</td>
						<td class="p-0">
							<div class="comment p-1">${value.comment || ``}</div>
							<input name="comment" class="form-control shadow-sm rounded-0" style="display:none;" type="text" placeholder="Comment..." required>
						</td>
						<td class="p-0">
							<div class="d-flex justify-content-center p-1">
								<button class="edit_button btn btn-sm btn-info custom_shadow mr-1" type="button" title="Edit">
									Edit
								</button>
								<button class="save_button btn btn-sm btn-primary custom_shadow mr-1" style="display:none;" type="button" title="Save">
									Save
								</button>
								<button class="cancel_button btn btn-sm btn-secondary custom_shadow" style="display:none;" type="button" title="Cancel">
									Cancel
								</button>
								<button class="delete_button btn btn-sm btn-danger custom_shadow" type="button" title="Delete">
									Delete
								</button>
							</div>
						</td>
					</tr>`)
					.appendTo(target);

				show_kpis(kpis, $(`[name="kpino"]`, template));

				(function($) {
					$(`.edit_button`, template).click(function(e) {
						$(`button, .kpititle, [name="kpino"], .score, .comment, [name="comment"]`, template).toggle();

						$(`[name]`, template).each((i, elem) => {
							let elementName = $(elem).attr(`name`);
							if (value[elementName] != null) {
								$(elem).val(value[elementName]);
							}
						});
					});

					$(`.cancel_button`, template).click(function(e) {
						$(`button, .kpititle, [name="kpino"], .score, .comment, [name="comment"]`, template).toggle();
					});

					$(`.save_button`, template).click(function(e) {
						setup_emp_kpiscore({
							kpiscoreno: value.kpiscoreno,
							kpino: $(`[name="kpino"]`, template).val(),
							score: $(`[name="score"]`, template).val(),
							comment: $(`[name="comment"]`, template).val()
						}, template);
					});

					$(`.delete_button`, template).click(function(e) {
						if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;
						del_emp_kpiscore({
							kpiscoreno: value.kpiscoreno
						});
					});
				})(jQuery);
			});
		}

		$(document).on(`change`, `[name="kpino"]`, function(e) {
			let row = $(this).parents(`.d-table-row`);

			if (!row.length) {
				row = $(this).parents(`tr`);
			}

			$(`.measure_unit`, row).text($(`option:selected`, this).data(`measureunit`) || `Unit`);
		});

		$(`#emp_kpisetting_add_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			setup_emp_kpiscore(json, $(this));
		});

		function setup_emp_kpiscore(json, parentContainer) {
			let obj = Object.fromEntries((new FormData($(`#kpi_score_filter_form`)[0])).entries());

			json = {
				...obj,
				...json
			};

			$.post(`php/ui/kpi/setup_emp_kpiscore.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					if (parentContainer.is(`form`)) {
						parentContainer.trigger(`reset`);
					} else {
						$(`button, .kpititle, [name="kpino"], .score, .comment, [name="comment"]`, parentContainer).toggle();
					}
					get_emp_kpiscore();
				}
			}, `json`);
		}

		function del_emp_kpiscore(json) {
			$.post(`php/ui/kpi/del_emp_kpiscore.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					get_emp_kpiscore();
				}
			}, `json`);
		}
	</script>

</body>

</html>