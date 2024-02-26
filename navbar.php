<style media="screen">
    .logo-src {
        background-image: url('<?= $response['orglogourl']; ?>') !important;
        height: 60px !important;
        background-size: contain !important;
        width: 62px !important;
    }

    .datepicker table {
        margin: auto;
    }

    .APPROVED,
    .REJECTED,
    .DELETED,
    .PENDING {
        color: white;
    }

    .card.border-left {
        border-left-width: 3px !important;
    }

    .wm_photo_url {
        width: 42px;
    }

    @media (max-width: 991.98px) {
        .app-header .app-header__content.header-mobile-open {
            top: 0px;
            left: 0px;
            width: 100%;
            border-radius: 0;
            padding-left: 0;
        }

        .app-header .app-header__content .header-btn-lg {
            margin-left: 4px;
            padding: 0;
        }

        .wm_photo_url {
            width: 30px;
        }
    }
</style>

<style>
    .IN_PROGRESS {
        color: orange;
    }

    .TODO {
        color: blue;
    }

    .COMPLETED {
        color: green;
    }

    .ABONDONED {
        color: red;
    }
</style>

<style>
    .glass-modal {
        /* From https://css.glass */
        background: rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(6.8px);
        -webkit-backdrop-filter: blur(6.8px);
        border: 1px solid rgba(255, 255, 255, 1);
    }
</style>
<div class="app-header header-shadow d-print-none">
    <div class="app-header__logo">
        <div class="logo-src mx-auto"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>

    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>

    <div class="app-header__content">
        <div class="app-header-left">
        </div>

        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left d-flex align-items-center">

                            <a href="time_keeper.php" class="rounded-circle p-1 px-sm-3 px-md-2">
                                <i class="fas fa-play-circle fa-2x text-primary" style="line-height:1;"></i>
                            </a>
                            <a href="time_keeper_summary.php" class="rounded-circle p-1 px-sm-3 px-md-2">
                                <i class="fas fa-chart-bar fa-2x text-primary" style="line-height:1;"></i>
                            </a>
                            <a href="task_filter.php" class="rounded-circle p-1 px-sm-3 px-md-2 mr-sm-2">
                                <i class="fas fa-tasks fa-2x text-primary" style="line-height:1;"></i>
                            </a>

                            <form name="channel_select_form" class="mb-0 mr-1 mr-sm-3">
                                <div class="input-group input-group-sm">
                                    <select name="channelno" class="form-control form-control-sm"></select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-light text-primary px-1 px-sm-2" type="button">
                                            Go
                                            <!-- <i class='fa fa-arrow-right'></i> -->
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="mb-0 mr-1 mr-sm-3">
                            <button id="leave_application_button" class="d-flex alert alert-primary shadow-sm border border-primary btn btn-sm btn-default border px-1 shadow-sm py-0" type="button" data-toggle="tooltip" data-placement="bottom" title="Leave Application">
                                <img src="assets/image/exit.png" width="20" alt="">
                            </button>
                        </div>

                        <div class="widget-content-left d-flex">
                            
                            <!-- Timekeppeer Start -->
                            <div class="mt-lg-2 mr-1 mr-sm-2 mr-md-4">
                                <div class="alert alert-primary d-flex shadow-sm text-center px-0 px-sm-1 px-md-2 py-0 mb-0" style="height:max-content; border-radius: 15px;">
                                    <h5 class="stopwatch border-secondary border-right mb-0 px-1" style="line-height: unset;">00:00:00</h5>
                                    <i data-hideontimerpage="true" class="timer_button far btn-sm fa-play-circle text-success px-1" style="line-height:1;font-size:1.4rem;"></i>
                                </div>
                            </div>
                            <!-- Time keeper end -->
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img class="rounded-circle wm_photo_url" src="<?php
                                                                                    if (!empty($_SESSION["wm_photo_url"])) {
                                                                                        echo $_SESSION["wm_photo_url"];
                                                                                    } else {
                                                                                        echo 'assets/image/user_icon.png';
                                                                                    }
                                                                                    ?>" onerror="this.onerror=null;this.src='assets/image/user_icon.png'" alt="">
                                    <i class="fa fa-angle-down ml-lg-2 opacity-8 d-none d-sm-inline-block"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-180px, 44px, 0px);">
                                    <h6 tabindex="-1" class="dropdown-header d-none justify-content-between align-self-center">
                                        <span id="light_theme" class="badge badge-light shadow-sm border grow cursor-pointer">Light</span>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="theme_custom_switch" checked>
                                            <label class="custom-control-label" for="theme_custom_switch"> </label>
                                        </div>
                                        <span id="dark_theme" class="badge badge-dark shadow-sm border grow cursor-pointer">Dark</span>
                                    </h6>
                                    <a href="organizations.php" tabindex="0" class="dropdown-item">
                                        <i class="fas fa-building mr-2 d-none d-sm-inline-block"></i> Organizations
                                    </a>
                                    <a href="my_packages.php" tabindex="0" class="dropdown-item">
                                        <i class="fas fa-boxes mr-2 d-none d-sm-inline-block"></i> Packages
                                    </a>
                                    <a href="profile.php" tabindex="0" class="dropdown-item">
                                        <i class="fas fa-user-cog mr-2 d-none d-sm-inline-block"></i> Profile
                                    </a>
                                    <div tabindex="-1" class="dropdown-divider"></div>
                                    <button id="change_password_anchor" type="button" tabindex="0" class="dropdown-item">
                                        <i class="fas fa-user-lock mr-2 d-none d-sm-inline-block"></i> Change Password
                                    </button>
                                    <a href='logout.php' tabindex="0" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt mr-2 d-none d-sm-inline-block"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="widget-content-left ml-3 header-user-info">
                            <div class="widget-heading">
                                <?php
                                if (!empty($_SESSION["wm_firstname"])) {
                                    echo $_SESSION["wm_firstname"];
                                }
                                if (!empty($_SESSION["wm_lastname"])) {
                                    echo " " . $_SESSION["wm_lastname"];
                                }
                                ?>
                            </div>
                            <div class="widget-subheading">
                                <?php
                                if (!empty($_SESSION["wm_designation"])) {
                                    echo $_SESSION["wm_designation"];
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PASSWORD CHANGE MODAL -->
<div id="modal_change_password" class="modal animated fadeInUp fade-scale" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Your Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="password_message_root"> </div>

                <form id="user_form_change_password" name="user_form_change_password">
                    <div class="form-group">
                        <label for="form_oldpassword">Old Password</label>
                        <input type="password" class="form-control shadow-sm" id="form_oldpassword" required>
                    </div>

                    <div class="form-group">
                        <label for="form_newpassword">Type New Password</label>
                        <input type="password" class="form-control shadow-sm" id="form_newpassword" required>
                    </div>

                    <div class="form-group">
                        <label for="form_retypenewpassword">Re-type New Password</label>
                        <input type="password" class="form-control shadow-sm" id="form_retypenewpassword" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="leave_application_modal" class="modal animated fadeInUp" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leave Application</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card shadow-none">
                    <div class="card-header-tab card-header-tab-animation card-header">
                        <ul class="nav">
                            <li class="nav-item">
                                <a data-toggle="tab" href="#new_application_tab" class="nav-link">New Application</a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#previous_application_tab" class="nav-link active show">Previous Application</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="new_application_tab" role="tabpanel">
                                <form id="leave_application_modal_form">
                                    <div class="form-group">
                                        <label class="d-block mb-0">
                                            Leave Date <span class="text-danger">*</span>
                                            <input id="leave_dates_input" name="leavedates" class="form-control shadow-sm mt-2" type="text" readonly required />
                                        </label>
                                    </div>
                                    <div id="parent" class="container mb-3">
                                        <div class="row mb-2">
                                            <div class="col-6 col-md-1 order-3 order-md-1 text-center align-self-center">
                                                <a href="#" id="previous" class="btn btn-sm btn-dark mt-2 mt-md-0" onclick="previous()">
                                                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                            <div class="card-header month-selected col-sm" id="monthAndYear">
                                            </div>
                                            <div class="col-6 col-md-5 order-1 order-md-2">
                                                <select class="form-control shadow-sm" id="month" onchange="change()"></select>
                                            </div>
                                            <div class="col-6 col-md-5 order-2 order-md-3">
                                                <select class="form-control shadow-sm" id="year" onchange="change()"></select>
                                            </div>
                                            <div class="col-6 col-md-1 order-4 order-md-4 text-center align-self-center">
                                                <a href="#" id="next" class="btn btn-sm btn-dark mt-2 mt-md-0" onclick="next()">
                                                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <table id="calendar_table" class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>S</th>
                                                    <th>M</th>
                                                    <th>T</th>
                                                    <th>W</th>
                                                    <th>T</th>
                                                    <th>F</th>
                                                    <th>S</th>
                                                </tr>
                                            </thead>
                                            <tbody id="calendar_table_body"></tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label class="d-block mb-0">
                                            Leave Type <span class="text-danger">*</span>
                                            <select name="leavetypeno" class="form-control shadow-sm mt-2" required></select>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="d-block mb-0">
                                            Reason <span class="text-danger">*</span>
                                            <textarea name="reasontext" class="form-control shadow-sm mt-2" placeholder="Description..." rows="3" required></textarea>
                                        </label>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Apply for Leave</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane show active" id="previous_application_tab" role="tabpanel">
                                <div id="previous_application_table_container" class="table-responsive shadow-sm mt-2">
                                    <table id="previous_application_table" class="table table-sm table-striped table-hover table-bordered mb-0">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>SL No</th>
                                                <th>Who</th>
                                                <th>Type</th>
                                                <th class="text-center">Status</th>
                                                <th>Days</th>
                                                <th>Reason</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="previous_application_table_tbody"></tbody>
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

<div id="incomplete_task_alltime_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">All Incomplete Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="incomplete_task_alltime_container"></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="status_update_modal" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="status_update_modal_form">
                <div class="modal-header">
                    <h5 class="modal-title">Status Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class='row'>
                        <div class="col-12 col-md-6 form-group">
                            <label class="d-block mb-0">
                                Status <span class="text-danger">*</span>
                                <select name="wstatusno" class="form-control shadow-sm mt-2" required></select>
                            </label>
                        </div>

                        <div class="col-12 col-md-6 form-group">
                            <label class="d-block mb-0">
                                Percentage of Completion <span class="text-danger">*</span>
                                <input name="percentile" class="form-control shadow-sm mt-2" type="number" min="0" max="100" required />
                            </label>
                        </div>
                    </div>

                    <label>Result <span class="text-danger">*</span></label>

                    <div id="status_update_result_container">
                        <p></p>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="workingtime_workfor_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="workingtime_workfor_modal_form">
                <div class="modal-header">
                    <h5 class="modal-title">Who Are You Working For?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="d-block mb-0">
                        Work For
                        <select name="workfor" class="form-control shadow-sm mt-2"></select>
                    </label>
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 ripple custom_shadow">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/stopwatch.js"></script>

<script>
    class Clock {
        constructor({
            template
        }) {
            this.template = template
        }
        render() {
            let d = new Date();
            $("#today_date_span").html(d.toDateString());
            $("#today_time_span").html(d.toLocaleString().split(', ')[1]);
        }
        stop() {
            clearInterval(this.timer)
        }
        start() {
            this.render()
            this.timer = setInterval(() => this.render(), 1000)
        }
    }

    let clock = new Clock({
        template: 'h:m:s'
    });

    clock.start();
</script>

<script>
    const ORGNAME = `<?= $response['orgname']; ?>`;
    const UCAT_NO = <?= $_SESSION['wm_ucatno']; ?>;
    const USER_NO = <?= $userno ?>;
    const ONE_DAY_IN_SECOND = 86400; // 60 * 60 * 24

    function padZero(value) {
        return value.toString().padStart(2, 0);
    }

    function formatDateTime(d) {
        d = new Date(d);
        let month_short = d.toLocaleString('default', {
            month: 'short'
        });

        let date = d.getDate().toString().padStart(2, 0);

        return `${date} ${month_short} ${d.getFullYear()} ${d.toLocaleString('default', {
				timeStyle: 'short',
				hour12: true
			})}`;
    }

    function formatDateToYYYYMMDD(date = new Date()) {
        return `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}`;
    }

    function formatDate(date) {
        date = new Date(date);
        let month_short = date.toLocaleString('default', {
            month: 'short'
        });
        return `${padZero(date.getDate())} ${month_short} ${date.getFullYear()}`;
    }

    function formatTime(timeString = "00:00:00") {
        let H = +timeString.substr(0, 2);
        let h = H % 12 || 12;
        let ampm = (H < 12 || H === 24) ? " AM" : " PM";
        return padZero(h) + timeString.substr(2) + ampm;
    }

    let savedAt = localStorage.getItem(`savedAt`);
    let now = Date.now();

    if (now - savedAt < (6 * 60 * 60 * 1000) || formatDateToYYYYMMDD(new Date(savedAt)) == formatDateToYYYYMMDD(new Date(now))) {
        if ($("#startdate_input").length && $("#enddate_input").length) {
            $("#startdate_input").val(localStorage.getItem(`startdate`));
            $("#enddate_input").val(localStorage.getItem(`enddate`));
        }
    }

    let stopWatch = new StopWatch(".stopwatch", ".timer_button");
    // console.log(`stopWatch`, stopWatch);

    if ($(window).width() >= 992 && $(`.app-header__content`).hasClass(`header-mobile-open`)) {
        $(`.app-header__content`).removeClass(`header-mobile-open`);

    } else if ($(window).width() < 992 && !$(`.app-header__content`).hasClass(`header-mobile-open`)) {
        $(`.app-header__content`).addClass(`header-mobile-open`);
    }

    get_va_owner_users();

    function get_va_owner_users() {
        $(`#workingtime_workfor_modal_form [name="workfor"]`).empty();

        $.post(`php/ui/user/get_users.php`, {
            ucatno: 13
        }, resp => {
            if (resp.error) {
                // toastr.error(resp.message);
            } else {
                show_va_owner_users(resp.results);
            }
        }, `json`);
    }

    function show_va_owner_users(data) {
        let select1 = $(`#workingtime_workfor_modal_form [name="workfor"]`).append(`<option value="">AGAMiLabs Ltd.</option>`);

        $.each(data, (index, value) => {
            $(`<option value="${value.userno}">
                    ${value.firstname}
                    ${value.lastname ? `${value.lastname}` : ``}
                    ${value.jobtitle ? `(${value.jobtitle})` : ``}
                </option>`)
                .appendTo(select1);
        });
    }

    $(document).on('click', '.timer_button', function() {
        let userno = `<?= $userno; ?>`;

        // if (!stopWatch.isTimeStopped) {
        //     $(`#incomplete_task_alltime_modal`).modal("show");
        // }

        onoff_workingtime({
            userno
        });
        // get_my_incomplete_task().then((data) => {
        // }).catch((reason) => {
        //     toastr.warning("Please enter the task title!");
        // });
    });

    function get_employee_workingtime() {
        let json = {
            startdate: `<?= date("Y-m-d") ?>`,
            enddate: `<?= date("Y-m-d") ?>`
        };

        if ($("#startdate_input").length && $("#enddate_input").length) {
            json = {
                startdate: $("#startdate_input").val(),
                enddate: $("#enddate_input").val()
            };
        }

        localStorage.setItem(`startdate`, json.startdate);
        localStorage.setItem(`enddate`, json.enddate);
        localStorage.setItem(`savedAt`, Date.now());

        $.ajax({
            type: "POST",
            url: "php/ui/workingtime/get_emp_workingtime.php",
            data: json,
            success: (result) => {
                stopWatch.stopTimer().resetTimer();

                let resp = $.parseJSON(result);

                if (resp.elapsedtime >= 0) {
                    stopWatch.setTimer(resp.elapsedtime).startTimer();
                }
                if (!$("#time_keeper_table").length) {
                    return;
                }

                setTimeout(() => {
                    if ($.fn.DataTable.isDataTable("#time_keeper_table")) {
                        $("#time_keeper_table").DataTable().clear().destroy();
                    }
                    $("#time_keeper_table_tbody").empty();
                    if (resp.error) {
                        toastr.error(resp.message);
                        $("#display_total_time_span").html(`00:00:00`);
                    } else {
                        add_row_in_table(resp.data);
                    }
                }, 1500);
            }
        });
    }

    function onoff_workingtime(json) {
        if (!stopWatch.isTimeStopped) {
            onoff_working_time(json);
            return;
        }

        let modal = $(`#workingtime_workfor_modal`).modal(`show`);

        let promise = new Promise((resolve, reject) => {
            modal.on(`hidden.bs.modal`, function(e) {
                reject(`You have mention who are you working for?`);
            });

            $(`#workingtime_workfor_modal_form`).submit(function(e) {
                e.preventDefault();
                let obj = Object.fromEntries((new FormData(this)).entries());
                json = {
                    ...json,
                    ...obj
                };

                resolve(json);
            });
        });

        promise.then(
            results => onoff_working_time(results),
            error => toastr.error(error)
        );
    }

    function onoff_working_time(json) {
        $.post(`php/ui/workingtime/onoff_workingtime.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                if (stopWatch.isTimeStopped) {
                    stopWatch.startTimer();
                } else {
                    stopWatch.stopTimer().resetTimer();
                }
                get_employee_workingtime();
                $(`#workingtime_workfor_modal`).modal(`hide`);
            }
        }, `json`);
    }

    function setTimeframe(fromDate, toDate) {
        $("#startdate_input").val(formatDateToYYYYMMDD(fromDate));
        $("#enddate_input").val(formatDateToYYYYMMDD(toDate));
        get_employee_workingtime();
    }

    $(function() {
        get_employee_workingtime();
    });
</script>

<script>
    $(document).on("hidden.bs.modal", ".modal", function(e) {
        let element = $(".modal-backdrop.show");
        if (element.length) {
            element.remove();
        }
    });

    document.getElementById('change_password_anchor').addEventListener('click', function() {
        showChangePassword();
    });

    function showChangePassword() {
        $('#modal_change_password').modal('show');
        document.getElementById('password_message_root').innerHTML = '';


        document.getElementById('user_form_change_password').addEventListener('submit', function(event) {
            event.preventDefault();

            var oldpassword = document.getElementById('form_oldpassword').value;
            var newpassword = document.getElementById('form_newpassword').value;
            var retypenewpassword = document.getElementById('form_retypenewpassword').value;

            if (newpassword !== retypenewpassword) {
                get_alert('password_message_root', 'alert alert-warning', 'fa fa-exclamation-circle',
                    'Password mismatch!');
                return;
            }

            $.ajax({
                url: 'php/ui/user/change_password.php',
                type: 'POST',
                data: {
                    oldpassword: oldpassword,
                    newpassword: newpassword,
                    newconfirmpassword: retypenewpassword
                },
                success: function(result) {
                    // console.log(result);
                    result = JSON.parse(result);
                    if (result.error) {
                        get_alert('password_message_root', 'alert alert-warning',
                            'fa fa-exclamation-circle', result.message);
                    } else {
                        get_alert('password_message_root', 'alert alert-success', 'fa fa-check-circle',
                            result.message);
                        document.getElementById('user_form_change_password').reset();
                    }
                }
            });
        });
    }

    function get_alert(password_message_root_id, className, iconClassName, textToDisplay) {
        var div = document.createElement('div');
        div.className = className;
        div.role = "alert";
        div.style.borderRadius = "25px";
        var icon = document.createElement('i');
        icon.className = iconClassName;
        var text = document.createElement('span');
        text.innerHTML = "&nbsp;&nbsp;&nbsp;" + textToDisplay;

        div.appendChild(icon);
        div.appendChild(text);

        document.getElementById(password_message_root_id).innerHTML = '';
        document.getElementById(password_message_root_id).appendChild(div);
    }

    $.ajax({
        type: "POST",
        url: "php/ui/workingtime/get_emp_elapsedtime_only.php",
        success: (result) => {
            // console.log("GET_EMP_ELAPSEDTIME_ONLY RESULT=>", result);
            let resp = $.parseJSON(result);
            // console.log("GET_EMP_ELAPSEDTIME_ONLY RESP=>", resp);
            $(`.header-user-info .widget-subheading`).html();
        }
    });
</script>

<script>
    $(`#leave_application_button`).click(function(e) {
        $(`#leave_application_modal`).modal("show");
        $(`#leave_application_modal_form`).trigger("reset");
    });

    function formatdateMMDDYYYY_to_YYYYMMDD(d) {
        let [mm, dd, yyyy] = d.trim().split("/");
        return `${yyyy}-${mm.padStart(2, 0)}-${dd.padStart(2, 0)}`;
    }

    $.post("php/ui/leave/get_leavetypes.php", {}, (resp) => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            $.each(resp.data, (index, value) => $(`#leave_application_modal_form [name="leavetypeno"]`).append(
                new Option(value.leavetypetitle, value.leavetypeno)));
        }
    }, "json");

    $.post("php/ui/leave/get_leavestatus.php", {}, (resp) => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            $.each(resp.data, (index, value) => $(`#leave_application_modal_form [name="leavestatusno"]`).append(
                new Option(value.leavestatustitle, value.leavestatusno)));
        }
    }, "json");

    $(function() {
        get_leave_application_list();
    });

    function get_leave_application_list() {
        $("#previous_application_table_tbody").empty();
        $.post("php/ui/leave/get_leaveapplicationlist.php", {}, (resp) => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                show_leave_application_list(resp.data);
            }
        }, "json");
    }

    function show_leave_application_list(data) {
        $.each(data, (index, value) => {
            let row = $(`<tr>`)
                .append(`<td>${index + 1}</td>
					<td>
						${value.firstname}${value.lastname ? ` ${value.lastname}` : ``}
						${value.jobtitle ? `<br>${value.jobtitle}` : ``}
						${value.primarycontact ? ` <br/>${value.primarycontact}` : ``}
					</td>
					<td>${value.leavetypeshort || `-`}</td>
					<td class="text-center">
						<div class='badge badge-primary ${value.leavestatustitle}' style='background-color:${value.leavestatucolor || "blue"}'>
							${value.leavestatustitle}
						</div>
					</td>
					<td>${value.leavedays || `-`} ${value.leavedays?("<b class='text-danger'>= "+value.leavedays.split(",").length+" days</b>"):""}</td>
					<td>${value.reasontext || `-`}</td>
					<td>
						<div class="d-flex justify-content-center p-0">
							${value.can_approve ? `<button class='leave_approve btn btn-sm btn-success shadow mx-1'>Approve</button>` : ``}
							${value.can_reject ? `<button class='leave_reject btn btn-sm btn-danger shadow mx-1'>Reject</button>` : ``}
							${value.can_delete ? `<button class='leave_delete btn btn-sm btn-warning shadow mx-1'>Cancel</button>` : ``}
						</div>
					</td>`)
                .appendTo(`#previous_application_table_tbody`);

            $(`.leave_approve`, row).click(function(e) {
                let json = {
                    ...value
                };
                json.leavestatusno = 2;
                setup_leave_application(json);
            });

            $(`.leave_reject`, row).click(function(e) {
                let json = {
                    ...value
                };
                json.leavestatusno = 3;
                setup_leave_application(json);
            });

            $(`.leave_delete`, row).click(function(e) {
                let json = {
                    ...value
                };
                json.leavestatusno = 4;
                setup_leave_application(json);
            });
        });
    }

    $(`#leave_application_modal_form`).submit(async function(e) {
        e.preventDefault();

        $(`#leave_application_modal_form :submit.btn-primary`).html('Processing....').prop('disabled', true);
        let last_click = $(`#leave_application_modal_form`).data('last_click') || 0;

        // restrict for 10s
        if (last_click > 0 && ((Date.now() - last_click) / 1000) > 10) {
            toastr.warning('Processing Previous Request...');
            return;
        } else {
            $(`#leave_application_modal_form`).data('last_click', Date.now());
        }

        let json = Object.fromEntries((new FormData(this)).entries());
        json.leavedates = JSON.stringify(json.leavedates.split(",").map(a => formatdateMMDDYYYY_to_YYYYMMDD(a)));
        await setup_leave_application(json);

        setTimeout(() => {
            $(`#leave_application_modal_form :submit.btn-primary`).html('Apply for Leave').prop('disabled', false);
        }, 1500);
    });

    async function setup_leave_application(json) {
        return new Promise((res, rej) => {
            $.post("php/ui/leave/setup_leaveapplication.php", json, (resp) => {
                if (resp.error) {
                    toastr.error(resp.message);
                    rej(resp.message);
                } else {
                    toastr.success(resp.message);
                    res(resp.message);
                    get_leave_application_list();
                    $(`#leave_application_modal`).modal("hide");
                }
            }, "json");
        });
    }
</script>

<script>
    let taskResultTextEditor;

    ClassicEditor
        .create(document.querySelector("#status_update_result_container"), {
            // plugins: [Base64UploadAdapter]
        })
        .then(editor => {
            taskResultTextEditor = editor;
            // console.log(editor);
        })
        .catch(error => {
            console.error(error);
        });

    function delayedDate(date1str, date2str) {
        const date1 = new Date(date1str);
        const date2 = new Date(date2str);
        const diffInMs = Math.abs(date2 - date1);
        const diffInHs = diffInMs / (1000 * 60 * 60);
        const diffInDs = diffInMs / (1000 * 60 * 60 * 24);

        return {
            date1str,
            date2str,
            days_diff: diffInDs,
            hours_diff: diffInHs,
            mills_diff: diffInMs
        };
    }

    get_work_status();

    function get_work_status() {
        let select = $(`#status_update_modal_form [name="wstatusno"]`).empty();

        $.post(`php/ui/taskmanager/selection/list_workstatus.php`, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                $.each(resp.results, (index, value) => {
                    select.append(new Option(value.statustitle, value.wstatusno));
                });
            }
        }, `json`);
    }

    // get_my_incomplete_task_alltime();

    function get_my_incomplete_task_alltime() {
        $(`#incomplete_task_alltime_container`).empty();

        $.post(`php/ui/taskmanager/selection/get_my_incomplete_task_alltime.php`, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else if (resp.results.length) {
                show_task(resp.results, `#incomplete_task_alltime_container`);
            }
        }, `json`);
    }

    function show_task2(data, targetContainer) {
        let today = `<?= date('Y-m-d'); ?>`,
            start = ``,
            delay = {},
            cardClass = ``;

        $.each(data, (index, value) => {
            start = value.deadlines.length ? value.deadlines[value.deadlines.length - 1].deadline : value.scheduledate;

            if (value.progress.find(a => a.wstatusno == 4) != null) {
                cardClass = ` border-left border-danger card-shadow-danger`;
            } else if (value.progress.find(a => a.wstatusno == 3) != null) {
                if (value.deadlines && value.deadlines.length > 1) {
                    cardClass = ` border-left border-warning card-shadow-warning`;
                } else {
                    cardClass = ` border-left border-success card-shadow-success`;
                }
            } else if (value.progress.find(a => a.wstatusno == 2) != null) {
                cardClass = ` border-left border-info card-shadow-info`;
                delay = delayedDate(today, start);
            } else {
                cardClass = ``;
                delay = delayedDate(today, start);
            }

            // console.log(`delay =>`, delay);

            let card = $(`<div class="card mb-3${cardClass}">
                    <div class="card-header justify-content-between" style="height:auto;">
                        <div class="my-md-1">
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start">
                                <div class="bg-info text-white rounded text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${value.channeltitle}</div>
                                <div class="alert alert-info text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${value.priorityleveltitle} (${value.relativepriority})</div>
                                ${delay.days_diff > 0
                                    ? `<div class="alert alert-danger text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${delay.days_diff} day(s) behind</div>`
                                    : ``}
                                ${(delay.days_diff <= 0 && delay.hours_diff > 0)
                                    ? `<div class="alert alert-danger text-center px-2 py-1 mb-0 mr-2" style="width: max-content;">${delay.hours_diff} hour(s) behind</div>`
                                    : ``}
                            </div>
                            <div class="small mt-1">
                                <div style="text-transform:none;">
                                    ${value.storyphasetitle},
                                    Points: ${value.points},
                                    By: ${value.assignedby || ``}
                                </div>
                            </div>
                        </div>
                        ${UCAT_NO == 19 || value.assignedto == USER_NO
                            ? `<button class="status_button btn btn-sm btn-info custom_shadow" type="button">Update Status</button>`
                            : ``
                        }
                    </div>
                    <div class="card-body py-2">
                        <div>${value.story}</div>
                    </div>
                    <div class="card-footer p-2">
                        <div class="w-100 px-2 py-1">
                            ${value.assignee ? `<div>Assignee: ${value.assignee}</div>` : ``}
                            <div class="d-flex justify-content-between">
                                <div>How to solve (Tips)</div>
                                <div>
                                    [${formatDate(value.scheduledate)}
                                    to
                                    ${value.deadlines.map((obj, i) => `<span class="${i != 0 ? `text-danger` : ``}">${formatDate(obj.deadline)}</span>`).join(", ")}]
                                </div>
                            </div>
                            <div>${deNormaliseUserInput(value.howto)}</div>
                            <hr>
                            ${value.progress.length
                                ? value.progress
                                    .map(b => `<div class="media mb-3">
                                        <div class="mr-2">${formatDateTime(b.progresstime)}</div>
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
                </div>`)
                .appendTo(targetContainer);

            (function($) {
                $(`.status_button`, card).click(function(e) {
                    $("#status_update_modal").modal("show");
                    $(`#status_update_modal_form`).data("cblscheduleno", value.cblscheduleno).data("cblprogressno", -1);
                });
            })(jQuery);
        });
    }

    $(`#status_update_modal_form`).submit(function(e) {
        e.preventDefault();
        let json = {
            wstatusno: $(`[name="wstatusno"]`, this).val(),
            percentile: $(`[name="percentile"]`, this).val(),
            result: normaliseUserInput(taskResultTextEditor.getData())
        };

        let cblscheduleno = $(this).data("cblscheduleno");
        if (cblscheduleno > 0) {
            json.cblscheduleno = cblscheduleno;
        }

        let cblprogressno = $(this).data("cblprogressno");
        if (cblprogressno > 0) {
            json.cblprogressno = cblprogressno;
        }

        $.post(`php/ui/taskmanager/progress/setup_progress.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                toastr.success(resp.message);
                $(".modal.show").modal("hide");

                if (typeof get_filtered_task === 'function') {
                    get_filtered_task();
                }

                if (typeof get_channel_task_detail === 'function') {
                    get_channel_task_detail();
                }

                if (typeof get_my_incomplete_task_alltime === 'function') {
                    get_my_incomplete_task_alltime();
                }
            }
        }, `json`);
    });

    function normaliseUserInput(text) {
        // var text = elem.value;
        if (!text) return text;

        // console.log(text);

        text = text.replaceAll('<', '&lt;');
        // console.log(text);

        text = text.replaceAll('>', '&gt;');
        // console.log(text);

        return text;
    }

    function deNormaliseUserInput(text) {
        if (!text) return text;
        // console.log(text);

        text = text.replaceAll('&lt;', '<');
        // console.log(text);

        text = text.replaceAll('&gt;', '>');
        // console.log(text);

        return text;
    }
</script>