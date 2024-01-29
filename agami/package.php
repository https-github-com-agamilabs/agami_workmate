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

					<!-- <div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-tachometer-alt icon-gradient bg-amy-crisp"></i>
								</div>
								<div>
									Package
									<div class="page-title-subheading">Your agami statistics mentioned here.</div>
								</div>
							</div>
						</div>
					</div> -->

					<!-- OFFER CARD -->
					<div id="offer_card" class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold">Offer</h5>
						</div>
						<div class="card-body">
							<form class="filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Offer Filter Form</legend>

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
											<th>Title</th>
											<th>Rate</th>
											<th>Valid Until</th>
											<th class="text-center">Action</th>
										</tr>
									</thead>
									<tbody id="offer_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(function() {
			const link = `https://www.google.com/maps/search/?api=1`;
			const DEFAULT_PHOTO = `assets/image/no_image_found.jpg`;

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

			const orgType = new SelectElemDataLoad({
				readURL: `${publicAccessUrl}php/ui/organization/get_org_type.php`,
				targets: [{
					selectElem: `#offer_card [name="orgtypeid"]`,
					defaultOptionText: `All...`,
					defaultOptionValue: `-1`
				}],
				optionText: `orgtypename`,
				optionValue: `orgtypeid`
			});

			class Offer extends BasicCRUD {
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
									<div class="text-primary">${value.offertitle || ``}</div>
									<div class="d-flex flex-wrap">
										<div class="mr-2">includes:</div>
										<div class="offer_buttons">
											<button class="add_button btn btn-primary btn-sm rounded-circle custom_shadow mr-1" style="padding: 1px 4px;" type="button">
												<span class="fas fa-plus"></span>
											</button>
										</div>
									</div>
									${value.includes
										.map(a => `<div class="d-flex flex-wrap mb-1">
											<div class="mr-2">${a.item}: ${a.unitqty}</div>
											<div class="offer_buttons">
												<button class="edit_button btn btn-info btn-sm rounded-circle custom_shadow mr-1" style="padding: 1px 4px;" type="button">
													<span class="fas fa-pen-alt"></span>
												</button>
												<button class="lock_button btn btn-danger btn-sm rounded-circle custom_shadow" style="padding: 1px 4px;" type="button">
													<span class="fas fa-times"></span>
												</button>
											</div>
										</div>`)
										.join(``)}
								</td>
								<td>${value.rate || ``}</td>
								<td>${value.validuntil ? formatDateTime(value.validuntil) : ``}</td>
								<td>
									<div class="d-flex justify-content-center p-0">
										<button class="edit_button btn btn-sm btn-info rounded-circle custom_shadow m-1" type="button" title="Edit ${this.topic}">
											<i class="fas fa-edit"></i>
										</button>
										<button class="delete_button btn btn-sm btn-danger rounded-circle custom_shadow m-1" type="button" title="Delete ${this.topic}">
											<i class="fas fa-trash"></i>
										</button>
									</div>
								</td>
							</tr>`)
							.data(value)
							.appendTo(this.targetContainer);

						(function($) {
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

			const offer = new Offer({
				readURL: `${publicAccessUrl}agami/php/ui/package/filter_package_offers.php`,
				targetCard: `#offer_card`,
				targetContainer: `#offer_tbody`,
				topic: `Offer`,
				tablePK: `orgno`
			});

			offer.get();
		});
	</script>

</body>

</html>