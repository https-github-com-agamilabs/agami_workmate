<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

	<style>
		[type="submit"]:disabled {
			cursor: not-allowed;
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

					<div class="card mb-3">
						<div class="card-body py-2">
							<div class="media">
								<img src="<?php
											if (!empty($_SESSION["cogo_photoname"])) {
												echo $_SESSION["cogo_photoname"];
											} else {
												echo 'assets/image/user_icon.png';
											}
											?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-circle mr-3" style="width:40px;height:40px;" alt="...">
								<div class="media-body">
									<input name="create_post" class="form-control shadow-sm rounded-pill cursor-pointer" type="text" placeholder="What's on your mind?" readonly>
								</div>
							</div>
							<hr class="my-2">

							<div class="row no-gutters">
								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="1">
										<i class="fas fa-comment-alt text-danger mr-2 d-none d-sm-inline-block"></i> Chat
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="2">
										<i class="fas fa-bullhorn text-success mr-2 d-none d-sm-inline-block"></i> Notification
									</button>
								</div>

								<div class="col-4">
									<button class="btn btn-outline-light btn-block border-0 font-size-lg" type="button" data-storytype="3">
										<i class="fas fa-tasks text-warning mr-2 d-none d-sm-inline-block"></i> Task
									</button>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div id="setup_channel_backlog_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="setup_channel_backlog_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Create Post</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="media mb-2">
							<img src="<?php
										if (!empty($_SESSION["cogo_photoname"])) {
											echo $_SESSION["cogo_photoname"];
										} else {
											echo 'assets/image/user_icon.png';
										}
										?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" class="align-self-start img-fluid rounded-circle mr-3" style="width:40px;height:40px;" alt="...">
							<div class="media-body">
								<div class="text-primary font-weight-bold">
									<?php
									if (!empty($_SESSION["cogo_firstname"])) {
										echo $_SESSION["cogo_firstname"];
									}
									if (!empty($_SESSION["cogo_lastname"])) {
										echo " " . $_SESSION["cogo_lastname"];
									}
									?>
								</div>
								<div>
									<?php
									if (!empty($_SESSION["cogo_designation"])) {
										echo $_SESSION["cogo_designation"];
									}
									?>
								</div>
							</div>
						</div>

						<div class="form-group">
							<textarea name="story" class="form-control shadow-sm" placeholder="What's on your mind?" rows="3"></textarea>
						</div>

						<div class="row align-items-center">
							<div class="col-4">
								<button class="btn btn-primary btn-sm" type="button">
									<i class="fas fa-upload mr-sm-2"></i> Attachment
								</button>
							</div>
							<div class="col-8">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text shadow-sm">Type</span>
									</div>
									<select name="storytype" class="form-control shadow-sm" required>
										<option value="1">Chat</option>
										<option value="2">Notification</option>
										<option value="3">Task</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer py-2">
						<button type="submit" class="btn btn-primary btn-block shadow" disabled>Post</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		const ucatno = `<?= $ucatno; ?>`;

		$(`[name="create_post"], button[data-storytype]`).on(`click`, function(e) {
			let modal = $(`#setup_channel_backlog_modal`).modal(`show`);
			let storytype = $(this).data(`storytype`) || 3;
			$(`[name="storytype"]`, modal).val(storytype);
		});

		$(`[name="story"]`, `#setup_channel_backlog_modal_form`).on(`input`, function(e) {
			let submitButton = $(`#setup_channel_backlog_modal_form :submit`);
			submitButton.prop(`disabled`, this.value.length <= 0);
		});

		$(`#setup_channel_backlog_modal_form`).submit(function(e) {
			e.preventDefault();
			let json = Object.fromEntries((new FormData(this)).entries());

			$.post(`php/ui/`, json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					toastr.success(resp.message);
				}
			}, `json`);
		});
	</script>
</body>

</html>