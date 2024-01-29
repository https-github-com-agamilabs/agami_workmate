<?php
$base_path = dirname(dirname(__FILE__));
include_once $base_path . "/php/ui/login/check_session.php";
?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once $base_path . "/shared/layout/header.php"; ?>

	<script src="../js/select_elem_data_load.js"></script>
	<script src="../js/basic_crud_type_1.js"></script>

	<style>
		.account_card i.rotate-icon {
			-moz-osx-font-smoothing: grayscale;
			-webkit-font-smoothing: antialiased;
			display: inline-block;
			font-style: normal;
			font-variant: normal;
			text-rendering: auto;
			line-height: 1;

			font-family: 'Font Awesome 5 Free';
			font-weight: 900;

			transition: all 300ms;
		}

		.account_card a.collapsed i.rotate-icon::before {
			content: "\f067";
		}

		.account_card a:not(.collapsed) i.rotate-icon::before {
			content: "\f068";
		}

		.account_card a.collapsed i.rotate-icon {
			-webkit-transform: rotate(0deg);
			transform: rotate(0deg);
		}

		.account_card a:not(.collapsed) i.rotate-icon {
			-webkit-transform: rotate(180deg);
			transform: rotate(180deg);
		}

		#account_accordion,
		#account_accordion a {
			color: #fff;
		}

		#account_accordion .collapse {
			border: 1px solid #2a5296;
			background-color: #3d66b0;
		}

		#account_accordion .collapse .collapse {
			border-right: none;
			background-color: #577cbe;
		}

		#account_accordion .collapse .collapse .collapse {
			background-color: #7b9edd;
		}

		#account_accordion .collapse .collapse .collapse .collapse,
		#account_accordion .collapse .collapse .collapse .collapse a {
			background-color: #deeaff;
			color: #000;
		}

		@media (min-width: 576px) {
			.account_card .account_head .account_buttons {
				visibility: hidden;
			}

			.account_card .account_head:hover .account_buttons {
				visibility: visible;
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
				<div class="app-main__inner">
					<!-- Image loader -->
					<div id='loader' style="display: none; position: fixed; top:0%; z-index:100; width:100vw; height:100vh; background-color:#00000024">
						<div align="center">
							<img src='../assets/loading.gif' style="height:50%;">
						</div>
					</div>

					<div class="app-page-title">
						<div class="page-title-wrapper">
							<div class="page-title-heading">
								<div class="page-title-icon">
									<i class="fas fa-user-tag icon-gradient bg-amy-crisp"></i>
								</div>
								<div>
									Chart of Accounts
									<div class="page-title-subheading">
										All of your accounts mentioned here. You can add new account, update or delete previous created acccount.
										There are few account with lock icon that system is using, so you can't delete those account. Moreover, you can't even delete the account that has sub-accounts. To delete a parent account, you need to delete all children account without containing any transaction first. Click on account to expand.
									</div>
								</div>
							</div>
						</div>
					</div>

					<ul class="nav tabs-animated tabs-animated-shadow">
						<li class="nav-item">
							<a data-toggle="tab" href="#chart_of_common_acc_tabpane" class="nav-link active">
								<span>Chart Of Common Account</span>
							</a>
						</li>
						<li class="nav-item">
							<a data-toggle="tab" href="#common_acc_type_tabpane" class="nav-link">
								<span>Common Account Type</span>
							</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane active" id="chart_of_common_acc_tabpane" role="tabpanel">
							<div class="row justify-content-center">
								<div class="col-md-10 col-lg-9 col-xl-8">
									<form id="common_account_filter_form">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text shadow-sm">Account Type</span>
											</div>
											<select name="commontypeno" class="form-control shadow-sm" required></select>
											<div class="input-group-append">
												<button class="btn btn-secondary ripple custom_shadow">Get Common Accounts</button>
											</div>
										</div>
									</form>
								</div>
							</div>

							<div class="card mb-3">
								<div class="card-body bg-night-sky text-white">
									<div id="account_accordion"></div>
								</div>
							</div>
						</div>

						<div class="tab-pane" id="common_acc_type_tabpane" role="tabpanel">
							<!-- COMMON ACC TYPE CARD -->
							<div id="common_acc_type_card" class="card mb-3">
								<div class="card-header justify-content-between">
									<h5 class="font-weight-bold">Common Acc Type</h5>
									<button class="add_button btn btn-primary btn-sm rounded-pill px-3 custom_shadow" type="button">
										<i class="fa fa-plus-circle mr-1"></i> Add
									</button>
								</div>
								<div class="card-body">
									<div class="table-responsive shadow-sm rounded">
										<table class="table table-sm table-striped table-bordered table-hover mb-0">
											<thead class="table-primary">
												<tr>
													<th>SL</th>
													<th>Common Type Title</th>
													<th>Accounting Level (Max)</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody id="common_acc_type_tbody"></tbody>
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

	<div id="orgaccount_head_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form class="setup_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Organization Account Head</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="d-block mb-0">
										Acc No <span class="text-danger">*</span>
										<div class="input-group mt-2">
											<div class="input-group-prepend">
												<span class="accno_prefix input-group-text shadow-sm bg-transparent pr-0 pb-1" style="font-size: 1rem;">10101</span>
											</div>
											<input name="accno" class="form-control shadow-sm border-left-0 pl-1" type="text" placeholder="Acc No..." required>
										</div>
									</label>
								</div>

								<div class="form-group">
									<label class="d-block mb-0">
										Acc Name <span class="text-danger">*</span>
										<input name="accname" class="form-control shadow-sm mt-2" type="text" placeholder="Acc Name..." required>
									</label>
								</div>

								<div class="form-group">
									<label class="d-block mb-0">
										Voucher Entry
										<select name="vtype" class="form-control shadow-sm mt-2" required>
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</label>
								</div>

								<div class="form-group">
									<label class="d-block mb-0">
										System Account
										<select name="sysacc" class="form-control shadow-sm mt-2" required>
											<option value="0">No</option>
											<option value="1">Yes</option>
										</select>
									</label>
								</div>

								<div class="form-group">
									<label class="d-block mb-0">
										Description
										<textarea name="acchints" class="form-control shadow-sm mt-2" placeholder="Description..." rows="3"></textarea>
									</label>
								</div>

								<div class="text-right mt-2">
									<button type="button" class="btn btn-secondary btn-sm rounded-pill px-5 ripple custom_shadow" data-dismiss="modal">Cancel</button>
									<button type="submit" class="btn btn-primary btn-sm rounded-pill px-5 ripple custom_shadow">Save</button>
								</div>
							</div>

							<div class="col-md-6">
								<img src="../assets/image/acc-head.png" class="img-fluid mt-3 mt-md-0" alt="Image of account type hierarcy">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- COMMON ACC TYPE SETUP MODAL -->
	<div id="common_acc_type_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form class="setup_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Common Acc Type</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block mb-0">
								Common Type Title <span class="text-danger">*</span>
								<input name="commontypetitle" class="form-control shadow-sm mt-2" type="text" placeholder="Common Type Title..." required>
							</label>
						</div>

						<label class="d-block mb-0">
							Accounting Level (Max) <span class="text-danger">*</span>
							<input name="maxacclevel" class="form-control shadow-sm mt-2" type="number" placeholder="Accounting Level (Max)..." required>
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
		let ACC_LEVEL = 5;

		class CommonAccType extends BasicCRUD {
			show(data) {
				let thisObj = this;
				let select = $(`#common_account_filter_form [name="commontypeno"]`).empty().append(`<option value="">Select...</option>`);
				ACC_LEVEL = data[0].maxacclevel;

				$.each(data, (index, value) => {
					$(`<option value="${value.commontypeno}">${value.commontypetitle}</option>`).data(value).appendTo(select);

					let template = $(`<tr>
								<td>${1 + index}</td>
								<td>${value.commontypetitle || ``}</td>
								<td>${value.maxacclevel || ``}</td>
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
			}
		}

		const commonAccType = new CommonAccType({
			readURL: `${publicAccessUrl}agami/php/ui/commontype/get_commontypes.php`,
			createURL: `${publicAccessUrl}agami/php/ui/commontype/setup_a_commontype.php`,
			updateURL: `${publicAccessUrl}agami/php/ui/commontype/setup_a_commontype.php`,
			deleteURL: `${publicAccessUrl}agami/php/ui/commontype/remove_a_commontype.php`,
			targetCard: `#common_acc_type_card`,
			targetContainer: `#common_acc_type_tbody`,
			setupModal: `#common_acc_type_modal`,
			topic: `Common Acc Type`,
			tablePK: `commontypeno`
		});

		commonAccType.get();

		$(`#common_account_filter_form [name="commontypeno"]`).change(function(e) {
			ACC_LEVEL = $(`option:selected`, this).data(`maxacclevel`);
			$(`#account_accordion`).hide();
		});

		$(`#common_account_filter_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			get_parented_commonaccount(json, $(`#account_accordion`));
		});

		function get_parented_commonaccount(json, target) {
			target.empty();

			if (!target.hasClass(`collapse`)) {
				target.show();
			}

			$.post(`${publicAccessUrl}agami/php/ui/commonaccounts/get_parented_commonaccount.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
					target.data(`is_loaded`, true).html(`<div class="text-center">${resp.message}</div>`);
				} else {
					show_parented_commonaccount(resp.data, target);
				}
			}, `json`);
		}

		function show_parented_commonaccount(data, target) {
			target.data(`is_loaded`, true);

			$.each(data, (index, value) => {
				let template = $(`<div class="account_card">
						<div class="account_head d-flex flex-wrap-reverse py-1">
							<div class="mr-3">
								${value.levelno < ACC_LEVEL
									? `<a data-toggle="collapse" href="#account_${value.accno}_collapse" class="collapsed text-decoration-none d-flex align-items-center p-0">
									<i class="rotate-icon mr-2"></i>
									<div class="">[${value.accno}] ${value.accname}</div>
								</a>`
								: `<a href="javascript:void(0);" class="text-decoration-none d-flex align-items-center p-0">
									<div class="">[${value.accno}] ${value.accname}</div>
								</a>`}
							</div>
							<div class="account_buttons">
								${value.levelno < ACC_LEVEL ? `<button class="add_button btn btn-primary btn-sm rounded-circle custom_shadow mr-1" style="padding: 1px 4px;" type="button">
									<span class="fas fa-plus"></span>
								</button>` : ``}
								<button class="edit_button btn btn-info btn-sm rounded-circle custom_shadow mr-1" style="padding: 1px 4px;" type="button">
									<span class="fas fa-pen-alt"></span>
								</button>
								${value.sysacc == 0 ? `<button class="delete_button btn btn-danger btn-sm rounded-circle custom_shadow" style="padding: 1px 4px;" type="button">
									<span class="fas fa-times"></span>
								</button>` : `<button class="lock_button btn btn-light btn-sm border border-dark rounded-circle custom_shadow" style="padding: 1px 4px;" type="button">
									<span class="fas fa-lock"></span>
								</button>`}
							</div>
							${value.levelno ? `<div class="ml-auto mr-2">Level: ${value.levelno}</div>` : ``}
						</div>
						${value.acchints ? `<div class="ml-3 mb-1">${value.acchints}</div>` : ``}
						${value.levelno < ACC_LEVEL ? `<div id="account_${value.accno}_collapse" class="collapse pl-4 pb-1" style="margin-left:5px;"></div>` : ``}
					</div>`)
					.appendTo(target);

				let collapse = $(`#account_${value.accno}_collapse`);

				(function($) {
					if (collapse.length) {
						$(`[href="#account_${value.accno}_collapse"]`, template).click(function(e) {
							if (!collapse.data(`is_loaded`)) {
								get_parented_commonaccount({
									commontypeno: value.commontypeno,
									praccno: value.accno
								}, collapse);
							}
						});
					}

					$(`.add_button`, template).click(function(e) {
						let modal = $(`#orgaccount_head_modal`).modal(`show`);
						$(`.modal-title`, modal).html(`Add New Common Account Head`);
						let form = $(`.setup_form`, modal)
							.trigger(`reset`)
							.data({
								commontypeno: value.commontypeno,
								praccno: value.accno,
								acctypeno: value.acctypeno,
								flag: -1,
								levelno: (Number(value.levelno) + 1)
							});

						let accnoPrefix = value.accno.toString().replace(/0+$/, '');
						$(`.accno_prefix`, form).html(accnoPrefix);
					});

					$(`.edit_button`, template).click(function(e) {
						let modal = $(`#orgaccount_head_modal`).modal(`show`);
						$(`.modal-title`, modal).html(`Update New Common Account Head`);
						let form = $(`.setup_form`, modal)
							.trigger(`reset`)
							.data({
								commontypeno: value.commontypeno,
								praccno: value.praccno,
								acctypeno: value.acctypeno,
								flag: value.accno,
								levelno: value.levelno
							});

						let accnoPrefix = value.accno.toString().replace(/0+$/, '');
						let accnoSuffix = value.accno.toString().replace(accnoPrefix, '');

						$(`[name]`, form).each((i, elem) => {
							let elementName = $(elem).attr("name");
							if (value[elementName] != null) {
								if (elementName == `accno`) {
									if (value.levelno == ACC_LEVEL) {
										accnoPrefix = value.praccno.toString().replace(/0+$/, '');
										accnoSuffix = value.accno.toString().replace(accnoPrefix, '');
									}
									$(`.accno_prefix`, form).html(accnoPrefix);
									$(elem).val(accnoSuffix);
								} else {
									$(elem).val(value[elementName]);
								}
							}
						});
					});

					$(`.delete_button`, template).click(function(e) {
						if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

						remove_a_orgaccount({
							commontypeno: value.commontypeno,
							accno: value.accno
						}, value.praccno);
					});

					$(`.lock_button`, template).click(function(e) {
						toastr.error(`System is using this account. So you can't delete this account.`);
					});
				})(jQuery);
			});
		}

		$(`#orgaccount_head_modal .setup_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());
			json.commontypeno = $(this).data(`commontypeno`);
			json.flag = $(this).data(`flag`);
			json.praccno = $(this).data(`praccno`);
			// json.acctypeno = $(this).data(`acctypeno`);

			json.accno = $(`.accno_prefix`, this).text() + json.accno;

			$.post(`${publicAccessUrl}agami/php/ui/commonaccounts/setup_a_commonaccount.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					$(`#orgaccount_head_modal`).modal(`hide`);

					if (json.praccno > 0) {
						get_parented_commonaccount({
							commontypeno: json.commontypeno,
							praccno: json.praccno
						}, $(`#account_${json.praccno}_collapse`));
					} else {
						get_parented_commonaccount({
							commontypeno: json.commontypeno
						}, $(`#account_accordion`));
					}
				}
			}, `json`);
		});

		function remove_a_orgaccount(json, praccno) {
			$.post(`${publicAccessUrl}agami/php/ui/commonaccounts/remove_a_commonaccount.php`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
					if (praccno > 0) {
						get_parented_commonaccount({
							commontypeno: json.commontypeno,
							praccno
						}, $(`#account_${praccno}_collapse`));
					} else {
						get_parented_commonaccount({
							commontypeno: json.commontypeno
						}, $(`#account_accordion`));
					}
				}
			}, `json`);
		}

		$(`#orgaccount_head_modal [name="vtype"]`).change(function(e) {
			let form = $(`#orgaccount_head_modal .setup_form`);
			let levelno = form.data(`levelno`);

			if (this.value == 1 && levelno < ACC_LEVEL) {
				toastr.warning(`This is not max accounting level!`);
			}
		});
	</script>

</body>

</html>