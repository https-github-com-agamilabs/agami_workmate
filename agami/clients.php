<?php
$basePath = dirname(dirname(__FILE__));
include_once $basePath . "/php/ui/login/check_session.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/shared/layout/header.php"; ?>

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
	<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

	<script src="../js/select_elem_data_load.js"></script>
	<script src="../js/basic_crud_type_1.js"></script>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-users icon-gradient bg-happy-itmeo"></i>
								</div>
								<div>
									Client Users
									<div class="page-title-subheading">Your client users mentioned here.</div>
								</div>
							</div>
						</div>
					</div>

					<!-- CLIENT USERS CARD -->
					<div id="client_users_card" class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold">Client Users</h5>
						</div>
						<div class="card-body">
							<form class="filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Client Users Filter Form</legend>

									<div class="row">
										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Limit</span>
												</div>
												<select name="limit" class="form-control shadow-sm">
													<option value="10">10</option>
													<option value="25">25</option>
													<option value="50">50</option>
												</select>
											</div>
										</div>

										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">User Status</span>
												</div>
												<select name="userstatusno" class="form-control shadow-sm">
													<option value="-1">All...</option>
													<option value="1">Verified</option>
													<option value="0">Not verified</option>
												</select>
											</div>
										</div>

										<div class="col-xl-4 text-right mb-3">
											<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow" type="submit">
												<i class="fa fa-search mr-1"></i> Search
											</button>
										</div>
									</div>
								</fieldset>
							</form>

							<div class="table-responsive my-3">
								<table class="table table-sm table-striped table-bordered table-hover mb-0">
									<thead class="table-primary">
										<tr>
											<th>SL</th>
											<th>Name</th>
											<th>Contact No</th>
											<th>Email</th>
											<th>Created Time</th>
											<th>Updated Time</th>
											<th class="text-center">Status</th>
										</tr>
									</thead>
									<tbody id="client_users_tbody"> </tbody>
								</table>
							</div>

							<div class="card rounded-pill">
								<div class="card-body p-1" style="font-size:24px">
									<div class="d-flex justify-content-between">
										<div class="previous_page pagination-button rounded-pill">
											<i class="fa fa-arrow-left mb-2"></i>
										</div>
										<div class="pagination-pageno rounded-pill">
											<div class="pageno_div font-weight-bold mt-2" style="font-size:16px;">Page: 1</div>
											<input class="pageno_input text-primary text-center form-control rounded-pill p-0 m-0 border-0" style="display:none;font-size:16px;" type="text" value="1" placeholder="Enter Page No">
										</div>
										<div class="next_page pagination-button rounded-pill">
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

	<div id="userstatus_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Change User Status</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="userstatus_container" role="group" class="btn-group-sm btn-group-toggle text-center" data-toggle="buttons"></div>
				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary rounded-pill px-4 shadow" data-dismiss="modal">Close</button>
					<button id="userstatus_save_button" type="button" class="btn btn-primary rounded-pill px-4 shadow">Save</button>
				</div>
			</div>
		</div>
	</div>

	<div id="client_detail_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Client Details</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

				</div>
				<div class="modal-footer py-2">
					<button type="button" class="btn btn-secondary rounded-pill px-4 shadow" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(function() {
			function padZero(value) {
				return value < 10 ? `0${value}` : `${value}`;
			}

			function formatDateTime(dateTime, withTime = true) {
				const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
				let date = new Date(dateTime);
				let result = padZero(date.getDate()) + " " + months[date.getMonth()] + " " + date.getFullYear();

				if (withTime) {
					let hours = date.getHours();
					let minutes = date.getMinutes();
					let ampm = hours >= 12 ? 'PM' : 'AM';
					hours = hours % 12;
					hours = hours ? hours : 12; // the hour '0' should be '12'
					minutes = minutes < 10 ? '0' + minutes : minutes;
					let strTime = hours + ':' + minutes + ' ' + ampm;
					result += " " + strTime;
				}

				return result;
			}

			function formatTime(timeString = "00:00:00") {
				if (!timeString || !timeString.length) {
					return ``;
				}

				let H = +timeString.substr(0, 2);
				let h = H % 12 || 12;
				let ampm = (H < 12 || H === 24) ? " AM" : " PM";
				return h + timeString.substr(2, 3) + ampm;
			}

			const userStatus = new SelectElemDataLoad({
				readURL: `${publicAccessUrl}agami/php/ui/clients/get_userstatus_titles.php`,
				targets: [{
					selectElem: `#client_users_card [name="userstatusno"]`,
					defaultOptionText: `All...`,
					defaultOptionValue: `-1`
				}],
				optionText: `userstatustitle`,
				optionValue: `userstatusno`
			});

			let userStatusID = setInterval(() => {
				if (userStatus.data && userStatus.data.length) {
					show_userstatus(userStatus.data);
					clearInterval(userStatusID);
				}
			}, 500);

			function show_userstatus(data) {
				let target = $(`#userstatus_container`);

				$.each(data, (index, value) => {
					let template = $(`<label class="btn btn-outline-primary ripple custom_shadow mr-2 mb-2">
							<input name="userstatusno" type="radio" class="form-check-input" value="${value.userstatusno}"> ${value.userstatustitle}
						</label>`)
						.appendTo(target);
				});
			}

			class ClientUsers extends BasicCRUD {
				show(data) {
					let thisObj = this;
					let dataTable = this.targetContainer.closest('table').DataTable();

					if ($.fn.DataTable.isDataTable(this.targetContainer.closest('table'))) {
						this.targetContainer.closest('table').DataTable().clear().destroy();
					}

					$.each(data, (index, value) => {
						let template = $(`<tr>
								<td>${1 + index}</td>
								<td>
									<a href="javascript:void(0);" class="client_name text-primary">
										${value.firstname || ``}
										${value.lastname || ``}
									</a>
									${value.username ? `(${value.username})` : ``}
								</td>
								<td>
									${value.countrycode ? `(${value.countrycode})` : ``}
									${value.primarycontact || ``}
								</td>
								<td>${value.email || ``}</td>
								<td>${value.ucreatedatetime ? formatDateTime(value.ucreatedatetime) : ``}</td>
								<td>${value.updatetime ? formatDateTime(value.updatetime) : ``}</td>
								<td class="text-center">
									<button class="userstatus_button btn ${value.userstatusno == 1 ? `btn-success` : (value.userstatusno == 9 ? `btn-info` : `btn-danger`)} btn-sm ripple custom_shadow" type="button">
										${value.userstatustitle}
									</button>
								</td>
							</tr>`)
							.data(value)
							.appendTo(this.targetContainer);

						(function($) {
							$(`.client_name`, template).click(function(e) {
								let modal = $(`#client_detail_modal`);
								$(`.modal-body`, modal).empty();
								get_user_orgs({
									userno: value.userno
								}, value);
							});

							$(`.userstatus_button`, template).click(function(e) {
								let modal = $(`#userstatus_modal`).modal(`show`).data(`userno`, value.userno);

								$(`[name="userstatusno"]:not([value="${value.userstatusno}"])`, modal)
									.prop(`checked`, false)
									.parents(`label`)
									.removeClass(`active`);

								$(`[name="userstatusno"][value="${value.userstatusno}"]`, modal)
									.prop(`checked`, true)
									.parents(`label`)
									.addClass(`active`);
							});
						})(jQuery);
					});

					dataTable = this.targetContainer.closest('table').DataTable({
						bInfo: false,
						bPaginate: false,
					});
					$(dataTable.table().container()).addClass("table-responsive");
				}
			}

			const clientUsers = new ClientUsers({
				readURL: `${publicAccessUrl}agami/php/ui/clients/filter_users.php`,
				targetCard: `#client_users_card`,
				targetContainer: `#client_users_tbody`,
				pagination: true,
				topic: `User`,
				tablePK: `userno`
			});

			clientUsers.get();

			$(`#userstatus_save_button`).click(function(e) {
				let modal = $(`#userstatus_modal`);

				let json = {
					orguser: modal.data(`userno`),
					userstatusno: $(`[name="userstatusno"]:checked`, modal).val()
				};

				$.post(`${publicAccessUrl}agami/php/ui/clients/update_userstatus.php`, json, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						modal.modal(`hide`);
						clientUsers.get();
					}
				}, `json`);
			});

			function get_user_orgs(json) {
				$.post(`${publicAccessUrl}agami/php/ui/clients/get_user_orgs.php`, json, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						show_user_orgs(resp.data);
					}
				}, `json`);
			}

			function show_user_orgs(data) {
				$(`#client_detail_modal`).modal(`show`);
				let target = $(`#client_detail_modal .modal-body`);

				$.each(data, (index, value) => {
					// ADDRESS
					let address = ``;

					if (value.street && value.street.length) {
						address += value.street;
					}

					if (value.city && value.city.length) {
						if (address.length) {
							address += `, `;
						}
						address += value.city;
					}

					if (value.country && value.country.length) {
						if (address.length) {
							address += `, `;
						}
						address += value.country;
					}

					let primarycontact = value.primarycontact;

					// OFFICE TIME
					let officeTime = ``;

					if (value.starttime && value.starttime.length) {
						if (value.endtime && value.endtime.length) {
							officeTime += `Office Time: `;
						} else {
							officeTime += `Office Open: `;
						}

						officeTime += `<b>${formatTime(value.starttime)}</b>`;
					}

					if (value.endtime && value.endtime.length) {
						if (officeTime.length) {
							officeTime += ` - `;
						}
						officeTime += `<b>${formatTime(value.endtime)}</b>`;
					}

					// WEEKEND
					let weekend = ``;

					if (value.weekend1 && value.weekend1.length) {
						if (value.endtime && value.endtime.length) {
							weekend += `Weekend: `;
						}
						weekend += `<b>${value.weekend1}</b>`;
					}

					if (value.weekend2 && value.weekend2.length) {
						if (weekend.length) {
							weekend += `, `;
						}
						weekend += `<b>${value.weekend2}</b>`;
					}

					let validityClass = `alert-success`;

					if (!value.accyear || !value.accyear.length) {
						validityClass = `alert-danger`;
					} else if (value.verifiedno != 1) {
						validityClass = `alert-danger`;
					} else if (value.pack_validuntil && value.pack_validuntil.length) {
						if (differenceOfDays(value.pack_validuntil) <= 7) {
							validityClass = `alert-warning`;
						}
					}

					let template = $(`<div class="mb-3">
							<div class="media">
								<img src="${value.picurl || `assets/store_logo/demo_logo.png`}" class="align-self-start img-fluid rounded shadow-sm border mr-3 cursor-pointer preview_orglogo" style="width:100px;" alt="...">

								<div class="media-body">
									<h5 class="font-weight-bold mb-0">${value.orgname}</h5>
									${value.orgtypename && value.orgtypename.length ? `<div class="small">(${value.orgtypename})</div>` : ``}
									${address.length ? `<div class="d-flex font-size-lg">
											<div><i class="fas fa-home mr-2"></i></div>
											<div>${address}</div>
										</div>` : ``}
									${primarycontact.length ? `<div  class="d-flex font-size-lg">
											<div><i class="fas fa-phone-alt mr-2"></i></div>
											<div>${primarycontact}</div>
										</div>` : ``}
									${officeTime.length ? `<div>${officeTime}</div>` : ``}
									${weekend.length ? `<div>${weekend}</div>` : ``}
									${value.gpslat && value.gpslon ? `<span class="mt-1">
											<a href="${link}&query=${value.gpslat}%2C${value.gpslon}" target="_blank" class="btn btn-secondary btn-sm ripple custom_shadow" title="View Store In Map">
												<i class="fas fa-map-marked mr-2"></i> View In Map
											</a>
										</span>` : ``
									}
								</div>
							</div>
							${value.orgnote && value.orgnote.length ? `<div>${value.orgnote}</div>` : ``}
						</div>`)
						.appendTo(target);
				});
			}
		});
	</script>

</body>

</html>