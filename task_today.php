<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php
	include_once("header.php");
	date_default_timezone_set("Asia/Dhaka"); ?>

</head>

<body>
	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
		<?php include_once("navbar.php"); ?>

		<div class="app-main">
			<?php include_once("sidebar.php"); ?>

			<div class="app-main__outer">
				<div class="app-main__inner">

					<div id="all_task_today_container"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
		get_all_task_today();

		function get_all_task_today() {
			$(`#all_task_today_container`).empty();

			$.post(`php/ui/taskmanager/selection/get_all_task_today.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else if (resp.results.length) {
					show_all_task_today(resp.results);
				}
			}, `json`);
		}

		function show_all_task_today(data) {
			let cardClass = ``;
			let cardHeaderClass = ``;

			$.each(data, (index, value) => {
				if (value.progress.find(a => a.wstatusno == 4) != null) {
					cardClass = ` border border-danger card-shadow-danger`;
					cardHeaderClass = ` bg-strong-bliss`;
				} else if (value.progress.find(a => a.wstatusno == 3) != null) {
					if (value.deadlines && value.deadlines.length > 1) {
						cardClass = ` border border-warning card-shadow-warning`;
						cardHeaderClass = ` bg-sunny-morning`;
					} else {
						cardClass = ` border border-success card-shadow-success`;
						cardHeaderClass = ` bg-grow-early`;
					}
				} else if (value.progress.find(a => a.wstatusno == 2) != null) {
					cardClass = ` border border-info card-shadow-info`;
					cardHeaderClass = ` bg-malibu-beach`;
				} else {
					cardClass = ``;
					cardHeaderClass = ``;
				}

				let card = $(`<div class="card mb-3${cardClass}">
						<div class="card-header justify-content-between${cardHeaderClass}">
							<div class="d-flex">
								<div class="bg-royal text-white rounded text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${value.channeltitle}</div>
								<div class="alert alert-info text-center px-2 py-1 mb-0" style="width: max-content;">${value.storyphasetitle}</div>
							</div>
						</div>
						<div class="card-body py-2">
							<div class="d-flex flex-wrap">
								<div class="alert alert-primary text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">Points: ${value.points}</div>
								<div class="alert alert-danger text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">Priority: ${value.priorityleveltitle} (${value.relativepriority})</div>
								<div class="alert alert-info text-center px-2 py-1 mb-2 mr-2" style="width: max-content;">By: ${value.assignedby || ``}</div>
							</div>
							<div>${value.story}</div>
						</div>
						<div class="card-footer p-2">
							<div class="w-100 px-2 py-1">
								<div class="d-flex justify-content-between">
									<div>Assignee: ${value.assignee}</div>
									<div>
										[${formatDate(value.scheduledate)}
										to
										${value.deadlines ? value.deadlines.map((obj, i) => `<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ") : `-`}]
									</div>
								</div>
								<div>How to solve (Tips)</div>
								<div>${deNormaliseUserInput(value.howto)}</div>
								<hr>
								${value.progress.length
									? value.progress
										.map(b => `<div class="media mb-3">
											<div class="mr-2" title="${formatDateTime(b.progresstime)}" data-toggle="tooltip" data-placement="top">${formatDateTime(b.progresstime)}</div>
											<div class="media-body">
												<div>${b.statustitle} (${b.entryby})</div>
												<div>${deNormaliseUserInput(b.result)}</div>
											</div>
										</div>`)
										.join("")
									: ``
								}
							</div>
						</div>
					</div>`);

				card.appendTo(`#all_task_today_container`);
			});
		}
	</script>
</body>

</html>