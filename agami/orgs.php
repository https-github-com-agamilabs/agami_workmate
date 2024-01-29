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
									<i class="fas fa-building icon-gradient bg-arielle-smile"></i>
								</div>
								<div>
									Client Organizations
									<div class="page-title-subheading">All of organizations mentioned here.</div>
								</div>
							</div>
						</div>
					</div>

					<!-- ORGANIZATIONS CARD -->
					<div id="orgs_card" class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold">Organizations</h5>
						</div>
						<div class="card-body">
							<form class="filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Organizations Filter Form</legend>

									<div class="row">
										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Type</span>
												</div>
												<select name="orgtypeid" class="form-control shadow-sm">
													<option value="-1">All...</option>
												</select>
											</div>
										</div>

										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Verification</span>
												</div>
												<select name="verifiedno" class="form-control shadow-sm">
													<option value="-9">All...</option>
													<option value="1">Verified</option>
													<option value="0">Yet to verify</option>
													<option value="-1">Postponed</option>
												</select>
											</div>
										</div>

										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">City</span>
												</div>
												<input name="city" class="form-control shadow-sm" type="text" placeholder="City...">
											</div>
										</div>

										<div class="offset-xl-8 col-md-6 col-xl-4 text-right mb-3">
											<button class="btn btn-primary btn-block font-weight-bold rounded-pill custom_shadow" type="submit">
												<i class="fa fa-search mr-1"></i> Search
											</button>
										</div>
									</div>
								</fieldset>
							</form>

							<div class="table-responsive mt-3">
								<table class="table table-sm table-striped table-bordered table-hover mb-0">
									<thead class="table-primary">
										<tr>
											<th>SL</th>
											<th>Name</th>
											<th>Type</th>
											<th>City</th>
											<th>Country</th>
											<th class="text-center">Verification</th>
										</tr>
									</thead>
									<tbody id="orgs_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="org_detail_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Organization Details</h5>
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
			const link = `https://www.google.com/maps/search/?api=1`;
			const DEFAULT_PHOTO = `assets/image/no_image_found.jpg`;

			function formatTime(timeString = "00:00:00") {
				if (!timeString || !timeString.length) {
					return ``;
				}

				let H = +timeString.substr(0, 2);
				let h = H % 12 || 12;
				let ampm = (H < 12 || H === 24) ? " AM" : " PM";
				return h + timeString.substr(2, 3) + ampm;
			}

			const orgType = new SelectElemDataLoad({
				readURL: `${publicAccessUrl}php/ui/settings/get_orgtype.php`,
				targets: [{
					selectElem: `#orgs_card [name="orgtypeid"]`,
					defaultOptionText: `All...`,
					defaultOptionValue: `-1`
				}],
				optionText: `orgtypename`,
				optionValue: `orgtypeid`
			});

			class Organizations extends BasicCRUD {
				show(data) {
					let thisObj = this;
					let dataTable = this.targetContainer.closest('table').DataTable();

					if ($.fn.DataTable.isDataTable(this.targetContainer.closest('table'))) {
						this.targetContainer.closest('table').DataTable().clear().destroy();
					}

					$.each(data, (index, value) => {
						let template = $(`<tr class="${value.verifiedno == 1 ? `table-success` : (value.verifiedno == -1 ? `table-danger` : ``)}">
								<td>${1 + index}</td>
								<td>
									<div>
										<a href="javascript:void(0);" class="orgname">${value.orgname || ``}</a>
									</div>
									${value.primarycontact ? `<span class="small">(${value.primarycontact})</span>` : ``}
								</td>
								<td>${value.orgtypename || ``}</td>
								<td>${value.city || ``}</td>
								<td>${value.country || ``}</td>
								<td class="text-center">
									${value.verifiedno == 1
										? `<span class="">Verified</span>`
										: (value.verifiedno == -1
											? `<span class="">Postponed</span>`
											: `<span class="">Yet to verify</span>`)}
									<hr class="my-1">
									${value.verifiedno != 1
										? `<button class="verify_button btn btn-success btn-sm ripple custom_shadow" data-verifiedno="1">Verify</button>`
										: ``}
									${value.verifiedno != -1
										? `<button class="verify_button btn btn-danger btn-sm ripple custom_shadow" data-verifiedno="-1">Postpone</button>`
										: ``}
								</td>
							</tr>`)
							.data(value)
							.appendTo(this.targetContainer);

						(function($) {
							$(`.orgname`, template).click(function(e) {
								let modal = $(`#org_detail_modal`).modal(`show`);
								$(`.modal-body`, modal).empty();
								get_an_org({
									orgno: value.orgno
								});
							});

							$(`.verify_button`, template).click(function(e) {
								let json = {
									orgno: value.orgno,
									verifiedno: $(this).data(`verifiedno`)
								};

								if (!confirm(`Are you sure?`)) return;

								$.post(`${publicAccessUrl}agami/php/ui/clients/update_org_verification.php`, json, resp => {
									if (resp.error) {
										toastr.error(resp.message);
									} else {
										toastr.success(resp.message);
										thisObj.get();
									}
								}, `json`);
							});
						})(jQuery);
					});

					dataTable = this.targetContainer.closest('table').DataTable();
					$(dataTable.table().container()).addClass("table-responsive");
				}
			}

			const organizations = new Organizations({
				readURL: `${publicAccessUrl}agami/php/ui/clients/filter_orgs.php`,
				targetCard: `#orgs_card`,
				targetContainer: `#orgs_tbody`,
				topic: `Organizations`,
				tablePK: `orgno`
			});

			organizations.get();

			function get_an_org(json) {
				$.post(`${publicAccessUrl}agami/php/ui/clients/get_an_org.php`, json, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						show_an_org(resp.data);
					}
				}, `json`);
			}

			function show_an_org(data) {
				let modal = $(`#org_detail_modal`);
				let modalBody = $(`.modal-body`, modal);

				// ADDRESS
				let address = ``;

				if (data.street && data.street.length) {
					address += data.street;
				}

				if (data.city && data.city.length) {
					if (address.length) {
						address += `, `;
					}
					address += data.city;
				}

				if (data.country && data.country.length) {
					if (address.length) {
						address += `, `;
					}
					address += data.country;
				}

				let primarycontact = data.primarycontact;

				// OFFICE TIME
				let officeTime = ``;

				if (data.starttime && data.starttime.length) {
					if (data.endtime && data.endtime.length) {
						officeTime += `Office Time: `;
					} else {
						officeTime += `Office Open: `;
					}

					officeTime += `<b>${formatTime(data.starttime)}</b>`;
				}

				if (data.endtime && data.endtime.length) {
					if (officeTime.length) {
						officeTime += ` - `;
					}
					officeTime += `<b>${formatTime(data.endtime)}</b>`;
				}

				// WEEKEND
				let weekend = ``;

				if (data.weekend1 && data.weekend1.length) {
					if (data.endtime && data.endtime.length) {
						weekend += `Weekend: `;
					}
					weekend += `<b>${data.weekend1}</b>`;
				}

				if (data.weekend2 && data.weekend2.length) {
					if (weekend.length) {
						weekend += `, `;
					}
					weekend += `<b>${data.weekend2}</b>`;
				}

				let template = $(`<div class="media">
						<img src="${data.picurl || `assets/store_logo/demo_logo.png`}" class="align-self-start img-fluid rounded shadow-sm border mr-3 cursor-pointer preview_orglogo" style="width:100px;" alt="...">

						<div class="media-body">
							<h5 class="font-weight-bold mb-0">${data.orgname}</h5>
							${data.orgtypename && data.orgtypename.length ? `<div class="small">(${data.orgtypename})</div>` : ``}
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
							${data.gpslat && data.gpslon ? `<span class="mt-1">
									<a href="${link}&query=${data.gpslat}%2C${data.gpslon}" target="_blank" class="btn btn-secondary btn-sm ripple custom_shadow" title="View Store In Map">
										<i class="fas fa-map-marked mr-2"></i> View In Map
									</a>
								</span>` : ``
							}
						</div>
					</div>`)
					.appendTo(modalBody);
			}
		});
	</script>

</body>

</html>