<?php
$basePath = dirname(__FILE__);
include_once $basePath . "/php/ui/login/check_session.php";
include_once $basePath . "/configmanager/firebase_configuration.php";

if (isset($_GET['lang'])) {
	$_SESSION["lang"] = $_GET['lang'];
} else if (!isset($_SESSION["lang"])) {
	$_SESSION["lang"] = "en";
}
$lang = $_SESSION["lang"];

require_once dirname(__FILE__) . "/lang_converter/converter.php";
if (!isset($arrayData)) {
	$arrayData = array();
}
$arrayData = array_merge($arrayData, langConverter($lang, 'my_packages'));

?>

<script>
	console.log(<?= json_encode($arrayData) ?>);
</script>


<!doctype html>
<html lang="en">

<head>
	<?php include_once $basePath . "/shared/layout/header.php";	?>

	<script src="js/basic_crud_type_1.js"></script>
</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once "settings_navbar.php"; ?>

		<div class="app-main">
			<?php include_once "settings_sidebar.php"; ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-boxes icon-gradient bg-midnight-bloom"></i>
								</div>
								<div>
									<?= $arrayData['lang_my_packages']; ?>
									<div class="page-title-subheading"><?= $arrayData['lang_your_packages_mentioned_here.']; ?></div>
								</div>
							</div>
						</div>
					</div>

					<div id="package_offer_card">
						<div id="package_offer_container" class="row mt-3"></div>
					</div>

					<div class="card mb-3">
						<div class="card-header"><?= $arrayData['lang_my_packages']; ?></div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-sm table-striped table-bordered table-hover mb-0">
									<thead class="table-primary">
										<tr>
											<th style="width: 50px;"><?= $arrayData['lang_sl']; ?></th>
											<th style="width: 200px;"><?= $arrayData['lang_offer']; ?></th>
											<th style="width: 200px;"><?= $arrayData['lang_detail']; ?></th>
											<th><?= $arrayData['lang_feature']; ?></th>
										</tr>
									</thead>
									<tbody id="my_packages_tbody"> </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="buy_package_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="buy_package_modal_form">
					<div class="modal-header">
						<h5 class="modal-title"><?= $arrayData['lang_package_summary']; ?></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div id="package_detail"></div>

						<div id="coupon_detail" class="text-center mb-4" style="display: none;">
							<a id="have_a_promo_code_anchor" href="javascript:void(0);"><?= $arrayData['lang_have_a_promo_code?']; ?></a>

							<div class="input-group collapse mx-auto my-2" style="width: 300px;">
								<input name="coupon" class="form-control shadow-sm" type="text" placeholder="Enter a promo code">
								<div class="input-group-append">
									<button class="btn btn-secondary ripple custom_shadow" type="button"><?= $arrayData['lang_apply']; ?></button>
								</div>
							</div>

							<div id="discount_message"></div>
						</div>

						<hr class="my-2" />

						<div class="d-flex justify-content-between mb-3">
							<div><span class="font-size-lg"><?= $arrayData['lang_total_payable']; ?></span> <span class="small"><?= $arrayData['lang_(bdt)']; ?></span></div>
							<div id="total_amount" class="h5 text-danger mb-0">Tk. 0</div>
						</div>

						<div class="text-center h6 font-weight-bold py-3 border">
							<?= $arrayData['lang_payment']; ?>

							<div class="d-flex">

							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary rounded-pill px-4 ripple custom_shadow"><?= $arrayData['lang_confirm']; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<script>
		function copyToClipboard(input_id) {
			// Get the text field
			var copyText = document.getElementById(input_id);

			// Select the text field
			copyText.select();
			copyText.setSelectionRange(0, 99999); // For mobile devices

			// Copy the text inside the text field
			navigator.clipboard.writeText(copyText.value);

			// Alert the copied text
			// alert("Copied the text: " + copyText.value);
			toastr.success("Copied to clipboard: " + copyText.value);
		}

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

			function groupArrayOfObjects(list, key) {
				return list.reduce((accumulator, currentValue) => {
					(accumulator[currentValue[key]] = accumulator[currentValue[key]] || []).push(currentValue);
					return accumulator;
				}, {});
			};

			class PackageOffer extends BasicCRUD {
				show(data) {
					let thisObj = this;

					$.each(data, (index, value) => {
						let template = $(`<div class="col-md-6 col-xl-4">
								<div class="card mb-4">
									<div class="card-body">
										<div class="d-flex flex-wrap">
											<div class="h6 font-weight-bold mb-0">${value.offertitle}</div>
											${value.tag ? `(${value.tag})` : ``}
										</div>
										<div class="mb-1">${value.offerdetail || ``}</div>
										${value.items && value.items.length
											? value.items
												.map(a => `<div class="" style="color:black;">
													<i class="fa fa-check text-info ml-3 mr-2"></i>
													<span class="text-primary">${a.qty || `-`}</span>
													${a.itemtitle}${(a.qty || 0) > 1 ? `s` : ``}
												</div>`)
												.join(``)
											: ``}
										<div class="d-flex justify-content-between mt-2">
											<div class="h5 text-danger mb-0">Tk. ${Number(value.rate) || ``}</div>
											<div class="">
												<button class="buy_now_button btn btn-danger btn-sm text-uppercase px-3 ripple custom_shadow" type="button">Buy Now</button>
											</div>
										</div>
									</div>
								</div>
							</div>`)
							.data(value)
							.appendTo(this.targetContainer);

						(function($) {
							$(`.buy_now_button`, template).click(function(e) {
								let modal = $(`#buy_package_modal`).modal(`show`);
								$(`#discount_message`).data({
									'visible_discount': 0,
									'visible_total': value.rate
								});
								let form = $(`form`, modal)
									.trigger(`reset`)
									.data(`offerno`, value.offerno)
									.data(`rate`, value.rate);

								if (value.is_coupon_applicable == 1) {
									$(`#coupon_detail`).show();
								} else {
									$(`#coupon_detail`).hide();
								}

								$(`#package_detail`)
									.html(`<div class="mb-4">
										<div class="h5 font-weight-bold" style='color:black;'>${value.offertitle}</div>
										<div class="">${value.offerdetail||""}</div>
										<hr>
										<div class='mb-2'>Upon purchase, you will receive the following:</div>
										<div class="font-weight-bold" style='color:black;'>
											<i class="fa fa-check text-info ml-3 mr-2"></i>
											<span class="text-primary">${value.accyear_qty || `-`}</span>
											Accounting Year${(value.accyear_qty || 0) > 1 ? `s` : ``}
										</div>
										<div class="font-weight-bold" style='color:black;'>
											<i class="fa fa-check text-info ml-3 mr-2"></i>
											<span class="text-primary">${value.org_qty || `-`}</span>
											Organization${(value.org_qty || 0) > 1 ? `s` : ``}
										</div>
										<div class="font-weight-bold" style='color:black;'>
											<i class="fa fa-check text-info ml-3 mr-2"></i>
											<span class="text-primary">${value.user_qty || `-`}</span>
											User${(value.user_qty || 0) > 1 ? `s` : ``}
										</div>
										<hr class="my-2">
										<div class="d-flex justify-content-between">
											<div><span class="font-size-lg">Subtotal</span> <span class="small">(BDT)</span></div>
											<div class="h5 text-danger mb-0">Tk. ${Number(value.rate) || ``}</div>
										</div>
									</div>`);

								$(`#total_amount`).html(`Tk. ${Number(value.rate) || ``}`);
							});
						})(jQuery);
					});
				}
			}

			const packageOffer = new PackageOffer({
				readURL: `${publicAccessUrl}php/ui/package/filter_packages_4clients.php`,
				targetCard: `#package_offer_card`,
				targetContainer: `#package_offer_container`,
				topic: `Package`,
				tablePK: `offerno`
			});

			packageOffer.get();

			$(`#have_a_promo_code_anchor`).click(function(e) {
				$(`#coupon_detail .input-group`).collapse(`toggle`);
			});

			$(`#buy_package_modal_form [name="coupon"]`)
				.on(`focusout`, function(e) {
					if (!this.value.length) {
						return;
					}
					verify_coupon({
						coupon: this.value
					});
				})
				.on(`keyup`, function(e) {
					if (!this.value.length) {
						return;
					}
					if (e.keyCode == 13) {
						verify_coupon({
							coupon: this.value
						});
					}
				});

			function verify_coupon(json) {
				$.post(`php/ui/package/verify_coupon.php`, json, resp => {
					if (resp.error) {
						toastr.error(resp.message);
						$(`#discount_message`).removeClass(`text-info`).addClass(`text-danger`).html(resp.message);
						let rate = Number($(`#buy_package_modal_form`).data(`rate`)) || 0;
						$(`#total_amount`).html(`Tk. ${rate}`);
					} else {
						apply_coupon(resp.results);
					}
				}, `json`);
			}

			function apply_coupon(data) {
				let rate = Number($(`#buy_package_modal_form`).data(`rate`)) || 0;

				let discount_fixed = Number(data.discount_fixed) || 0;
				let discount_percentage = Number(data.discount_percentage) || 0;
				let discount = 0;

				if (discount_fixed > 0 && discount_percentage > 0) {
					let distP = (rate * discount_percentage) / 100;
					discount = distP > discount_fixed ? discount_fixed : distP;
				} else if (discount_fixed > 0) {
					discount = discount_fixed;
				} else if (discount_percentage > 0) {
					let distP = (rate * discount_percentage) / 100;
					discount = distP;
				}

				let amount = rate - discount;
				if (discount >= rate) {
					amount = rate;
					discount = 0;
				}

				$(`#discount_message`).data({
					'visible_discount': discount,
					'visible_total': amount
				}).removeClass(`text-danger`).addClass(`text-info`).html(`Nice! You saved Tk. ${discount} on your package.`);
				$(`#total_amount`).html(`Tk. ${amount}`);
			}

			$(`#buy_package_modal_form`).submit(function(e) {
				e.preventDefault();
				let json = Object.fromEntries((new FormData(this)).entries());
				json.offerno = $(this).data(`offerno`);
				let visible_amount = $(`#discount_message`).data();

				console.log(json);
				json = {
					...json,
					...visible_amount
				};
				console.log(json);

				$.post(`${publicAccessUrl}php/ui/package/add_purchaseoffer.php`, json, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						$(`#buy_package_modal`).modal(`hide`);
						if (resp.paymenturl) {
							location.href = resp.paymenturl;
						}
					}
				}, `json`);
			});

			get_my_packages();

			function get_my_packages() {
				$(`#my_packages_tbody`).empty();

				$.post(`${publicAccessUrl}php/ui/package/get_my_packages.php`, resp => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						show_my_packages(resp.data);
					}
				}, `json`);
			}

			function show_my_packages(data) {
				let target = $(`#my_packages_tbody`);

				$.each(data, (index, value) => {
					let appliedwith = ``;

					if (value.appliedwith && value.appliedwith.length) {
						let groupedData = groupArrayOfObjects(value.appliedwith, `orgno`);

						$.each(groupedData, (orgno, orgAppliedWithArr) => {
							$.each(orgAppliedWithArr, (_i, orgAppliedWith) => {
								if (_i == 0) {
									appliedwith += `<div class="text-primary font-weight-bold">${orgAppliedWith.orgname}</div>`;
								}

								appliedwith += `<div>Item: <b>${orgAppliedWith.item}</b>, Assigned To: <b>${orgAppliedWith.assignedto}</b></div>`;
							});
						});
					}

					let template = $(`<tr>
							<td>${1 + index}</td>
							<td>
								<div class="text-primary font-weight-bold">${value.offertitle || ``}</div>
								<div>${value.offerdetail || ``}</div>
								<div class="mt-2" style="cursor:pointer;" onclick='copyToClipboard("licensekey_${value.licensekey}")'>
									${value.licensekey} <i class='fa fa-copy' style='cursor:copy;'></i>
									<input id="licensekey_${value.licensekey}" style="display:none;" type="text" value="${value.licensekey}">
								</div>
							</td>
							<td>
								<div>Buyer: <b>${value.buyername || ``}</b></div>
								<div>Owner: <b>${value.ownername || ``}</b></div>
								<div>Price: <b>${value.amount ? Number(value.amount) : ``}</b></div>
								<div>Paid: <b>${value.paidamount ? Number(value.paidamount) : ``}</b></div>
							</td>
							<td class="text-left">${appliedwith}</td>
						</tr>`)
						.appendTo(target);

					(function($) {
						$(`.accyear_add_button`, template).click(function(e) {
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
		});
	</script>

</body>

</html>