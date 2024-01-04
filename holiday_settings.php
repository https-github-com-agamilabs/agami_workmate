<?php
$response['error'] = true;
$response['message'] = "Not available!";
echo json_encode($response);
exit();
?>

<?php include_once "php/ui/login/check_session.php"; ?>

<!doctype html>
<html lang="en">

<head>
	<?php include_once("header.php"); ?>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" integrity="sha256-16PDMvytZTH9heHu9KBPjzrFTaoner60bnABykjNiM0=" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js" integrity="sha256-XOMgUu4lWKSn8CFoJoBoGd9Q/OET+xrfGYSo+AKpFhE=" crossorigin="anonymous"></script>

	<style>

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
						<div class="card-body">
							<div id="range_holiday_container" class=" d-flex flex-wrap justify-content-center"></div>

							<?php if ($ucatno === 19) : ?>
								<form id="set_weekend_form">
									<fieldset class="custom_fieldset pb-0 mb-3">
										<legend class="legend-label shadow-sm">Set Range Holiday</legend>

										<div class="row">
											<div class="col-md-6 form-group">
												<label class="d-block mb-0">
													Start Date <span class="text-danger">*</span>
													<input name="start_date" class="form-control shadow-sm mt-2" type="date" value="<?= date('Y-m-d'); ?>" required>
												</label>
											</div>

											<div class="col-md-6 form-group">
												<label class="d-block mb-0">
													End Date <span class="text-danger">*</span>
													<input name="end_date" class="form-control shadow-sm mt-2" type="date" value="<?= date('Y-m-d', strtotime('Dec 31')); ?>" required>
												</label>
											</div>
										</div>

										<div class="form-group">
											<label class="d-block">
												Week Day <span class="text-danger">*</span>
											</label>

											<div role="group" class="btn-group-sm btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Sunday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Monday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Tuesday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Wednesday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Thursday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Friday
												</label>
												<label class="btn btn-outline-primary shadow mb-2">
													<input name="weekend_date" type="radio" class="form-check-input"> Saturday
												</label>
											</div>

											<?php
											if ($ucatno === 19) : ?>
												<div class="position-relative form-check">
													<label class="form-check-label">
														<input name="delete_previous_weekends" type="checkbox" class="form-check-input" checked>
														Delete Previous
													</label>
												</div>
											<?php endif; ?>
										</div>

										<div class="form-group text-center">
											<div class="dropdown d-inline-block">
												<button type="button" tabindex="0" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle btn btn-primary btn-sm rounded-pill px-4 shadow">
													Set As
												</button>
												<div id="hdtypeid_dropdown_menu" role="menu" aria-hidden="true" class="dropdown-menu"></div>
											</div>
										</div>
									</fieldset>
								</form>
							<?php endif; ?>
						</div>
					</div>

					<div class="main-card mb-3 card">
						<div class="card-body">
							<div id="holiday_calendar"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="setup_holiday_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="setup_holiday_modal_form">
					<div class="modal-header">
						<h5 class="modal-title">Setup Holiday</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="d-block mb-0">
								Holiday Date <span class="text-danger">*</span>
								<textarea name="holidaydate" class="form-control shadow-sm mt-2" rows="1" readonly required></textarea>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Holiday Type
								<select name="hdtypeid" class="form-control shadow-sm mt-2"></select>
							</label>
						</div>

						<div class="form-group">
							<label class="d-block mb-0">
								Reason
								<input name="reasontext" class="form-control shadow-sm mt-2" placeholder="Reason..." maxlength="50">
							</label>
						</div>

						<label class="d-block mb-0">
							Min Working Hour
							<input name="minworkinghour" class="form-control shadow-sm mt-2" type="number" placeholder="Min Working Hour...">
						</label>
					</div>
					<div class="modal-footer py-2">
						<button id="delete_holiday_button" type="button" class="btn btn-danger rounded-pill px-4 shadow">Delete Holiday</button>
						<button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Save Holiday</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	<script>
		const WEEK_DAY = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
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

		function padZero(value) {
			return value < 10 ? `0${value}` : `${value}`;
		}

		function formatDateToYYYYMMDD(date = new Date()) {
			return `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}`;
		}

		set_weekend_date();

		function set_weekend_date() {
			let start_date = $(`#set_weekend_form [name="start_date"]`).val();

			if (start_date) {
				start_date = new Date(start_date);
			} else {
				start_date = new Date(`<?= date('Y-m-d'); ?>`);
			}

			for (let index = 0; index < 7; index++) {
				$(`#set_weekend_form [name="weekend_date"]:eq(${start_date.getDay()})`).val(formatDateToYYYYMMDD(start_date));
				start_date.setDate(start_date.getDate() + 1);
			}
		}

		$(`#set_weekend_form [name="start_date"]`).change(() => set_weekend_date());

		get_holidaytypes();

		function get_holidaytypes() {
			$(`#hdtypeid_dropdown_menu, #setup_holiday_modal_form [name="hdtypeid"]`).empty();

			$.post(`php/ui/holiday/get_holidaytypes.php`, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					pageSettingsFunc(`holidaytypes`, resp.data);
					show_holidaytypes(resp.data);
				}
			}, `json`);
		}

		function show_holidaytypes(data) {
			let select = $(`#setup_holiday_modal_form [name="hdtypeid"]`);
			let target = $(`#hdtypeid_dropdown_menu`);

			$.each(data, (index, value) => {
				// if (value.hdtypeid != `WEEK_END`) {

				// }

				$(`<option value="${value.hdtypeid}">${value.displaytitle}</option>`)
					.data(value)
					.appendTo(select);

				let template = $(`<button type="button" tabindex="0" class="dropdown-item">${value.displaytitle}</button>`)
					.data(value)
					.appendTo(target);

				(function(template, value) {

					template.click(function() {
						console.log(this);
						$('[data-toggle="dropdown"]').dropdown('hide');

						let json = Object.fromEntries((new FormData($(`#set_weekend_form`)[0])).entries());
						json.hdtypeid = $(this).data(`hdtypeid`);
						json.delete_previous_weekends = Number(json.delete_previous_weekends == "on");
						setup_holidays(json);
					});

				})(template, value);
			});
			$(function() {
				$('[data-toggle="dropdown"]').dropdown();
			});
		}

		// $(document).on('click', '[data-toggle="dropdown"]', function() {
		// 	console.log(this);
		// 	// $(this).dropdown('toggle');
		// });

		$(`#setup_holiday_modal_form [name="hdtypeid"]`).change(function(e) {
			let elem = $(`#setup_holiday_modal_form [name="minworkinghour"]`);
			let minworkinghour = Number($(`option:selected`, this).data(`minworkinghour`)) || 0;
			elem.val(minworkinghour);
		});

		const calendarEl = document.getElementById('holiday_calendar');

		const calendar = new FullCalendar.Calendar(calendarEl, {
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay'
			},
			themeSystem: "bootstrap",
			selectable: true,
			dayMaxEvents: true,
			moreLinkClick: "day",
			datesSet: (dateInfo) => {
				calendar.removeAllEvents();
				get_holidays({
					start_date: formatDateToYYYYMMDD(calendar.view.activeStart),
					end_date: formatDateToYYYYMMDD(calendar.view.activeEnd)
				});
			},
			select: (info) => {
				<?php
				if ($ucatno === 19) : ?>
					$(`#setup_holiday_modal`).modal("show").find(`.modal-title`).html(`Add Holiday`);
					$(`#setup_holiday_modal_form`).trigger("reset").data("holidayno", -1);
					$(`#delete_holiday_button`).hide();

					let currentDate = new Date(info.startStr),
						end = new Date(info.endStr),
						between = [];

					while (currentDate < end) {
						between = [...between, formatDateToYYYYMMDD(currentDate)];
						currentDate.setDate(currentDate.getDate() + 1);
					}
					$(`#setup_holiday_modal_form [name="holidaydate"]`).val(between.join());
				<?php endif; ?>
			},
			eventClick: (info) => {
				<?php
				if ($ucatno === 19) : ?>
					let extendedProps = info.event.extendedProps;
					// if (extendedProps.hdtypeid == `WEEK_END`) {
					// 	toastr.error(`You can only change Week-End above form.`);
					// 	return;
					// }
					$(`#setup_holiday_modal`).modal("show").find(`.modal-title`).html(`Update/Delete Holiday`);
					$(`#setup_holiday_modal_form`).trigger("reset").data("holidayno", extendedProps.holidayno);
					$(`#delete_holiday_button`).show();

					$(`#setup_holiday_modal_form [name]`).each((index, elem) => {
						console.log(extendedProps);
						$(elem).val(extendedProps[$(elem).attr("name")]);
					});
				<?php endif; ?>
			}
		});

		calendar.render();

		function get_holidays(json) {
			$(`#range_holiday_container`).empty();

			$.post("php/ui/holiday/get_holidays.php", json, resp => {
				if (resp.error) {
					toastr.error(resp.message);
				} else {
					check_holiday_Types_loaded_then_add_event(resp.data);
				}
			}, "json");
		}

		function check_holiday_Types_loaded_then_add_event(data) {
			let holidayTypes = pageSettingsFunc(`holidaytypes`);

			if (holidayTypes && holidayTypes.length) {
				addCalendarEvent(data, holidayTypes);
			} else {
				let holidayTypesID = setInterval(() => {
					if (holidayTypes && holidayTypes.length) {
						addCalendarEvent(data, holidayTypes);
						clearInterval(holidayTypesID);
					}
				}, 500);
			}
		}

		function addCalendarEvent(data, holidayTypes) {
			let WEEK_ENDS = [];
			let HALF_DAYS = [];
			let target = $(`#range_holiday_container`);

			$.each(data, (index, value) => {
				value.title = value.reasontext || value.hdtypeid;
				value.start = value.holidaydate;

				let holidayType = holidayTypes.find(a => a.hdtypeid == value.hdtypeid);
				if (holidayType && holidayType.color) {
					value.color = holidayType.color;
				}

				if (value.hdtypeid == `WEEK_END`) {
					let dayName = WEEK_DAY[(new Date(value.holidaydate)).getDay()];
					if (WEEK_ENDS.indexOf(dayName) < 0) {
						WEEK_ENDS = [...WEEK_ENDS, dayName];
					}
				} else if (value.hdtypeid == "HALF_DAY") {
					let dayName = WEEK_DAY[(new Date(value.holidaydate)).getDay()];
					if (HALF_DAYS.indexOf(dayName) < 0) {
						HALF_DAYS = [...HALF_DAYS, dayName];
					}
				}

				calendar.addEvent(value);
			});

			if (WEEK_ENDS.length) {
				$(`<div class="alert-info rounded shadow-sm px-2 py-1 mr-2 mb-2" style="width: max-content;">
						Week End: ${WEEK_ENDS.map(a => `<b>${a}</b>`).join(`,`)}
					</div>`)
					.appendTo(target);
			}

			if (HALF_DAYS.length) {
				$(`<div class="alert-info rounded shadow-sm px-2 py-1 mr-2 mb-2" style="width: max-content;">
						Half Day Office: ${HALF_DAYS.map(a => `<b>${a}</b>`).join(``)}
					</div>`)
					.appendTo(target);
			}
		}

		<?php
		if ($ucatno === 19) : ?>
			$(document).on(`click`, `#hdtypeid_dropdown_menu .dropdown-item`, function(e) {
				// let json = Object.fromEntries((new FormData($(`#set_weekend_form`)[0])).entries());
				// json.hdtypeid = $(this).data(`hdtypeid`);
				// json.delete_previous_weekends = Number(json.delete_previous_weekends == "on");
				// setup_holidays(json);
			});

			$(`#setup_holiday_modal_form`).submit(function(e) {
				e.preventDefault();
				let json = Object.fromEntries((new FormData(this)).entries());

				json.holidays = JSON.stringify(json.holidaydate.split(","));
				delete json.holidaydate;

				let holidayno = $(this).data("holidayno");
				if (holidayno > 0) {
					json.holidayno = holidayno;
				}

				setup_holidays(json);
			});

			function setup_holidays(json) {
				$.post("php/ui/holiday/setup_holidays.php", json, (resp) => {
					if (resp.error) {
						toastr.error(resp.message);
					} else {
						toastr.success(resp.message);
						$(`#setup_holiday_modal`).modal("hide");
						calendar.removeAllEvents();
						get_holidays({
							start_date: formatDateToYYYYMMDD(calendar.view.activeStart),
							end_date: formatDateToYYYYMMDD(calendar.view.activeEnd)
						});
					}
				}, "json");
			}

			$(`#delete_holiday_button`).click(function(e) {
				if (!confirm("Are you sure? You are going to delete this holiday.")) return;

				let json = {
					holidayno: $(`#setup_holiday_modal_form`).data("holidayno")
				};

				if (json.holidayno > 0) {
					$.post("php/ui/holiday/remove_holidays.php", json, (resp) => {
						if (resp.error) {
							toastr.error(resp.message);
						} else {
							toastr.success(resp.message);
							$(`#setup_holiday_modal`).modal("hide");
							calendar.removeAllEvents();
							get_holidays({
								start_date: formatDateToYYYYMMDD(calendar.view.activeStart),
								end_date: formatDateToYYYYMMDD(calendar.view.activeEnd)
							});
						}
					}, "json");
				}
			});
		<?php endif; ?>
	</script>

</body>

</html>