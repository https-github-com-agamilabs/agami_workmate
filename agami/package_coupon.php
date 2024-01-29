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

					<!-- PACKAGE COUPON CARD -->
					<div id="package_coupon_card" class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold">Package Coupon</h5>
							<button class="add_button btn btn-primary btn-sm rounded-pill px-3 custom_shadow" type="button">
								<i class="fa fa-plus-circle mr-1"></i> Add
							</button>
						</div>
						<div class="card-body">
							<form class="filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Package Coupon Filter Form</legend>

									<div class="row">
										<div class="col-md-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Min Use</span>
												</div>
												<input name="min_use" class="form-control shadow-sm" type="number" min="1" placeholder="Min Use...">
											</div>
										</div>

										<div class="col-md-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Validity</span>
												</div>
												<select name="isactive" class="form-control shadow-sm">
													<option value="-1">All</option>
													<option value="1">Valid</option>
													<option value="0">Invalid</option>
												</select>
											</div>
										</div>

										<div class="col-md-4">
											<div class="d-flex flex-wrap justify-content-end mb-3">
												<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow mx-1" type="submit">
													<i class="fa fa-search mr-1"></i> Search
												</button>
											</div>
										</div>
									</div>
								</fieldset>
							</form>

							<div class="table-responsive mt-3">
								<table class="table table-sm table-striped table-bordered table-hover mb-0">
									<thead class="table-primary">
										<tr>
											<th>SL</th>
											<th>Coupon</th>
											<th>Discount (Fixed)</th>
											<th>Discount (Percentage)</th>
											<th>Max Use</th>
											<th>Already Used</th>
											<th>Description</th>
											<th class="text-center">Activation</th>
											<th class="text-center">Removal</th>
										</tr>
									</thead>
									<tbody id="package_coupon_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PACKAGE COUPON MODAL -->
	<div id="package_coupon_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form class="setup_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Package Coupon</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block mb-0">
								Coupon <span class="text-danger">*</span>
								<input name="coupon" class="form-control shadow-sm mt-2" type="text" placeholder="Coupon..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Discount (Fixed)
								<input name="discount_fixed" class="form-control shadow-sm mt-2" type="number" min="0" step="0.01" placeholder="Discount (Fixed)...">
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Discount (Percentage)
								<input name="discount_percentage" class="form-control shadow-sm mt-2" type="number" min="0" max="100" step="0.01" placeholder="Discount (Percentage)...">
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Max Use <span class="text-danger">*</span>
								<input name="max_use" class="form-control shadow-sm mt-2" type="number" min="1" value="1" placeholder="Max Use..." required>
							</label>
						</div>

						<label class="d-block mb-0">
							Description
							<textarea name="description" class="form-control shadow-sm mt-2" placeholder="Description..." rows="3"></textarea>
						</label>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 custom_shadow">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		$(function() {
			class PackageCoupon extends BasicCRUD {
				show(data) {
					let thisObj = this;
					let dataTable = this.targetContainer.closest('table').DataTable();

					if ($.fn.DataTable.isDataTable(this.targetContainer.closest('table'))) {
						this.targetContainer.closest('table').DataTable().clear().destroy();
					}

					$.each(data, (index, value) => {
						let template = $(`<tr class="${value.isactive == 0 ? `table-secondary` : (value.max_use <= value.already_used_qty ? `table-danger` : `table-success`)}">
								<td>${1 + index}</td>
								<td>${value.coupon}</td>
								<td>${value.discount_fixed}</td>
								<td>${value.discount_percentage}</td>
								<td>${value.max_use}</td>
								<td>${value.already_used_qty}</td>
								<td>${value.description || ``}</td>
								<td class="text-center">
									<button class="activation_button btn btn-sm ${value.isactive == 0 ? `btn-success` : `btn-danger`} ripple custom_shadow m-1" type="button" title="Activation">
										${value.isactive == 0 ? `Activate` : `Deactivate`}
									</button>
								</td>
								<td class="text-center">
									<button class="delete_button btn btn-sm btn-danger rounded-circle ripple custom_shadow m-1" type="button" title="Delete ${this.topic}">
										<i class="fas fa-trash"></i>
									</button>
								</td>
							</tr$>`)
							.data(value)
							.appendTo(this.targetContainer);

						(function($) {
							$(`.activation_button`, template).click((e) => {
								if (confirm("Are you sure you want to change this status?")) {
									thisObj.toggleStatus({
										coupon: value.coupon,
										isactive: (value.isactive == 1) ? 0 : 1
									});
								}
							});

							thisObj.deleteButtonTrigger(template, value);
						})(jQuery);
					});

					dataTable = this.targetContainer.closest('table').DataTable();
					$(dataTable.table().container()).addClass("table-responsive");
				}

				toggleStatus(json) {
					$.post(`${publicAccessUrl}agami/php/ui/package/toggle_coupon_activation.php`, json, resp => {
						if (resp.error) {
							toastr.error(resp.message);
						} else {
							toastr.success(resp.message);
							this.get();
						}
					}, `json`);
				}
			}

			const packageCoupon = new PackageCoupon({
				readURL: `${publicAccessUrl}agami/php/ui/package/filter_coupons.php`,
				createURL: `${publicAccessUrl}agami/php/ui/package/add_coupon.php`,
				deleteURL: `${publicAccessUrl}agami/php/ui/package/remove_coupon.php`,
				targetCard: `#package_coupon_card`,
				targetContainer: `#package_coupon_tbody`,
				setupModal: `#package_coupon_modal`,
				topic: `Coupon`,
				tablePK: `coupon`
			});

			packageCoupon.get();
		});
	</script>

</body>

</html>