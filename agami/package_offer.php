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

					<!-- PACKAGE OFFER CARD -->
					<div id="package_offer_card" class="card mb-3">
						<div class="card-header justify-content-between">
							<h5 class="font-weight-bold">Package Offer</h5>
							<button class="add_button btn btn-primary btn-sm rounded-pill px-3 custom_shadow" type="button">
								<i class="fa fa-plus-circle mr-1"></i> Add
							</button>
						</div>
						<div class="card-body">
							<form class="filter_form">
								<fieldset class="custom_fieldset pb-0">
									<legend class="legend-label">Package Offer Filter Form</legend>

									<div class="row">
										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Validity</span>
												</div>
												<select name="valid" class="form-control shadow-sm">
													<option value="-1">All...</option>
													<option value="1">Valid</option>
													<option value="0">Invalid</option>
												</select>
											</div>
										</div>

										<div class="col-md-6 col-xl-4">
											<div class="input-group mb-3">
												<div class="input-group-prepend">
													<span class="input-group-text shadow-sm">Tag</span>
												</div>
												<select name="tag" class="form-control shadow-sm"></select>
											</div>
										</div>

										<div class="col-xl-4">
											<div class="d-flex flex-wrap justify-content-end mb-3">
												<button class="btn btn-primary font-weight-bold rounded-pill px-4 custom_shadow mx-1" type="submit">
													<i class="fa fa-search mr-1"></i> Search
												</button>
												<button class="btn btn-warning font-weight-bold rounded-pill px-4 custom_shadow mx-1" type="reset">
													<i class="fa fa-history mr-1"></i> Reset
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
											<th>Title</th>
											<th>Detail</th>
											<th>Items</th>
											<th>Rate</th>
											<th class="text-center">Is<br>Coupon<br>Applicable</th>
											<th>Valid Until</th>
											<th class="text-center">Action</th>
										</tr>
									</thead>
									<tbody id="package_offer_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PACKAGE OFFER MODAL -->
	<div id="package_offer_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form class="setup_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Package Offer</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block mb-0">
								Offer Title <span class="text-danger">*</span>
								<input name="offertitle" class="form-control shadow-sm mt-2" type="text" placeholder="Offer Title..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Offer Detail
								<textarea name="offerdetail" class="form-control shadow-sm mt-2" placeholder="Offer Detail..." rows="3"></textarea>
							</label>
						</div>

						<div id="items_container"></div>

						<div class="form-group">
							<label class="d-block mb-0">
								Rate <span class="text-danger">*</span>
								<input name="rate" class="form-control shadow-sm mt-2" type="number" min="1" step="0.01" placeholder="Rate..." required>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Tag
								<select name="tag" class="form-control shadow-sm mt-2"></select>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Is Coupon Applicable
								<select name="is_coupon_applicable" class="form-control shadow-sm mt-2">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</label>
						</div>

						<label class="d-block mb-0">
							Valid Until
							<input name="validuntil" class="form-control shadow-sm mt-2" type="datetime-local" placeholder="Valid Until...">
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

			const tags = new SelectElemDataLoad({
				readURL: `${publicAccessUrl}agami/php/ui/package/get_tags.php`,
				targets: [{
						selectElem: `#package_offer_card [name="tag"]`,
						defaultOptionText: `All...`,
						defaultOptionValue: ``
					},
					{
						selectElem: `#package_offer_modal [name="tag"]`,
						defaultOptionText: `Select...`,
						defaultOptionValue: ``
					}
				],
				optionText: `tag`,
				optionValue: `tag`
			});

			get_items();

			function get_items() {
				$(`#items_container`).empty();

				$.post(`${publicAccessUrl}agami/php/ui/package/get_items.php`, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						show_items(resp.data);
					}
				}, `json`);
			}

			function show_items(data) {
				let target = $(`#items_container`);

				$.each(data, (index, value) => {
					let template = $(`<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text shadow-sm">No. of ${value.itemtitle}</span>
								</div>
								<input name="${value.item}" class="form-control shadow-sm" type="number" placeholder="No. of ${value.itemtitle}...">
							</div>`)
						.appendTo(target);
				});
			}

			class PackageOffer extends BasicCRUD {
				show(data) {
					let thisObj = this;
					let dataTable = this.targetContainer.closest('table').DataTable();

					if ($.fn.DataTable.isDataTable(this.targetContainer.closest('table'))) {
						this.targetContainer.closest('table').DataTable().clear().destroy();
					}

					$.each(data, (index, value) => {
						let template = $(`<tr>
								<td>${1 + index}</td>
								<td>${value.offertitle}</td>
								<td>${value.offerdetail}</td>
								<td>
									${value.items && value.items.length
										? value.items
											.map(a => `${a.item}: ${a.qty}`)
											.join(`<br>`)
										: ``}
								</td>
								<td>${value.rate}</td>
								<td class="text-center">
									${value.is_coupon_applicable == 1
										? `<span class="badge badge-success">Yes</span>`
										: `<span class="badge badge-danger">No</span>`}
								</td>
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
							thisObj.editButtonTrigger(template, value);

							thisObj.deleteButtonTrigger(template, value);
						})(jQuery);
					});

					dataTable = this.targetContainer.closest('table').DataTable();
					$(dataTable.table().container()).addClass("table-responsive");
				}

				editButtonTrigger(template, value) {
					$(`.edit_button`, template).click((e) => {
						this.setupModal.modal(`show`).find(`.modal-title`).html(`Update ${this.topic}`);
						this.setupForm.trigger("reset").data(this.tablePK, value[this.tablePK]).data(`action`, this.updateURL);

						$(`[name]`, this.setupForm).each((i, elem) => {
							let elementName = $(elem).attr("name");
							if (value[elementName] != null) {
								$(elem).val(value[elementName]);
							} else if ($(`#items_container [name="${elementName}"]`).length) {
								let obj = value.items.find(a => a.item == elementName);

								if (obj && obj.qty > 0) {
									$(elem).val(obj.qty);
								}
							}
						});
					});
				}

				setupFormSubmitTrigger() {
					this.setupForm.submit((e) => {
						e.preventDefault();
						let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());

						if (json.validuntil && json.validuntil.length) {
							json.validuntil = json.validuntil.split(`T`).join(` `);

							if (json.validuntil.length == 16) {
								json.validuntil += `:00`;
							}
						}

						let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
						let url = this.setupForm.data(`action`);

						if (tablePK > 0) {
							json[this.tablePK] = tablePK;
						}

						json.offeritems = [];

						$(`#items_container [name]`).each((i, elem) => {
							let item = $(elem).attr("name");
							let qty = $(elem).val();

							if (qty.length) {
								json.offeritems = [...json.offeritems, {
									item,
									qty
								}];
							}

							delete json[item];
						});

						if (!json.offeritems.length) {
							toastr.error(`You have to select atleast 1 items.`);
							$(`#items_container [name]:first`).focus();
							return;
						}

						json.offeritems = JSON.stringify(json.offeritems);

						$.post(url, json, resp => this.successCallback(resp), "json");
					});
				}
			}

			const packageOffer = new PackageOffer({
				readURL: `${publicAccessUrl}agami/php/ui/package/filter_package_offers.php`,
				createURL: `${publicAccessUrl}agami/php/ui/package/setup_package_offer.php`,
				updateURL: `${publicAccessUrl}agami/php/ui/package/setup_package_offer.php`,
				deleteURL: `${publicAccessUrl}agami/php/ui/package/remove_package_offer.php`,
				targetCard: `#package_offer_card`,
				targetContainer: `#package_offer_tbody`,
				setupModal: `#package_offer_modal`,
				topic: `Package Offer`,
				tablePK: `offerno`
			});

			packageOffer.get();
		});
	</script>

</body>

</html>