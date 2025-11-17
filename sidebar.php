<style>
    .app-theme-white .app-sidebar {
        background-color: transparent !important;
        min-width: auto;
        max-width: 250px;
        /* width: 250px; */
    }

    .app-sidebar.sidebar-shadow {}
</style>

<style>
    .progress-circle {
        width: 40px;
        height: 40px;
        background: none;
        position: relative;
    }

    .progress-circle::after {
        content: "";
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #eee;
        position: absolute;
        top: 0;
        left: 0;
    }

    .progress-circle>span {
        width: 50%;
        height: 100%;
        overflow: hidden;
        position: absolute;
        top: 0;
        z-index: 1;
    }

    .progress-circle .progress-left {
        left: 0;
    }

    .progress-circle .progress-bar {
        width: 100%;
        height: 100%;
        background: none;
        border-width: 2px;
        border-style: solid;
        position: absolute;
        top: 0;
    }

    .progress-circle .progress-left .progress-bar {
        left: 100%;
        border-top-right-radius: 80px;
        border-bottom-right-radius: 80px;
        border-left: 0;
        -webkit-transform-origin: center left;
        transform-origin: center left;
    }

    .progress-circle .progress-right {
        right: 0;
    }

    .progress-circle .progress-right .progress-bar {
        left: -100%;
        border-top-left-radius: 80px;
        border-bottom-left-radius: 80px;
        border-right: 0;
        -webkit-transform-origin: center right;
        transform-origin: center right;
    }

    .progress-circle .progress-value {
        position: absolute;
        top: 0;
        left: 0;
    }
</style>

<style>
    .border-percent-0 {
        border-color: #ff0000 !important;
    }

    .bg-percent-0 {
        background-color: #ff0000 !important;
    }

    .border-percent-5 {
        border-color: #ff3900 !important;
    }

    .bg-percent-5 {
        background-color: #ff3900 !important;
    }

    .border-percent-10 {
        border-color: #ff5600 !important;
    }

    .bg-percent-10 {
        background-color: #ff5600 !important;
    }

    .border-percent-15 {
        border-color: #ff6c00 !important;
    }

    .bg-percent-15 {
        background-color: #ff6c00 !important;
    }

    .border-percent-20 {
        border-color: #ff7e00 !important;
    }

    .bg-percent-20 {
        background-color: #ff7e00 !important;
    }

    .border-percent-25 {
        border-color: #ff8f00 !important;
    }

    .bg-percent-25 {
        background-color: #ff8f00 !important;
    }

    .border-percent-30 {
        border-color: #ff9e00 !important;
    }

    .bg-percent-30 {
        background-color: #ff9e00 !important;
    }

    .border-percent-35 {
        border-color: #ffad00 !important;
    }

    .bg-percent-35 {
        background-color: #ffad00 !important;
    }

    .border-percent-40 {
        border-color: #ffbc00 !important;
    }

    .bg-percent-40 {
        background-color: #ffbc00 !important;
    }

    .border-percent-45 {
        border-color: #ffcb00 !important;
    }

    .bg-percent-45 {
        background-color: #ffcb00 !important;
    }

    .border-percent-50 {
        border-color: #ffd900 !important;
    }

    .bg-percent-50 {
        background-color: #ffd900 !important;
    }

    .border-percent-55 {
        border-color: #e9d70c !important;
    }

    .bg-percent-55 {
        background-color: #e9d70c !important;
    }

    .border-percent-60 {
        border-color: #d4d81c !important;
    }

    .bg-percent-60 {
        background-color: #d4d81c !important;
    }

    .border-percent-65 {
        border-color: #bfd82a !important;
    }

    .bg-percent-65 {
        background-color: #bfd82a !important;
    }

    .border-percent-70 {
        border-color: #abd738 !important;
    }

    .bg-percent-70 {
        background-color: #abd738 !important;
    }

    .border-percent-75 {
        border-color: #97d545 !important;
    }

    .bg-percent-75 {
        background-color: #97d545 !important;
    }

    .border-percent-80 {
        border-color: #84d251 !important;
    }

    .bg-percent-80 {
        background-color: #84d251 !important;
    }

    .border-percent-85 {
        border-color: #72d05d !important;
    }

    .bg-percent-85 {
        background-color: #72d05d !important;
    }

    .border-percent-90 {
        border-color: #5fcc69 !important;
    }

    .bg-percent-90 {
        background-color: #5fcc69 !important;
    }

    .border-percent-95 {
        border-color: #4dc873 !important;
    }

    .bg-percent-95 {
        background-color: #4dc873 !important;
    }

    .border-percent-100 {
        border-color: #3bc47d !important;
    }

    .bg-percent-100 {
        background-color: #3bc47d !important;
    }
</style>

<style>
    .card-body .remove_watch_list {
        display: none;
        transition: .3s all;
    }

    .card-body:hover .remove_watch_list {
        display: inline;
    }
</style>

<div class="app-sidebar sidebar-shadow0 d-print-none">
    <div class="app-header__logo">
        <div class="logo-src"></div>
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

    <div class="scrollbar-sidebar" style="overflow-y:auto; background: #fff;">
        <div class="app-sidebar__inner">
            <ul id="channels_container" class="vertical-nav-menu mt-3">

                <?php
                if ($_SESSION['wm_ucatno'] == 5) : ?>
                    <li>
                        <a href="dashboard.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                if ($_SESSION['wm_ucatno'] != 5) : ?>
                    <li class="app-sidebar__heading">Time Keeper</li>

                    <li style="margin: -0.5rem 0;">
                        <a href="time_keeper.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-play-circle"></i> Time Keeper
                        </a>
                    </li>
                    <li style="margin: -0.5rem 0;">
                        <a href="time_keeper_summary.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-chart-bar"></i> Time Keeper Summary
                        </a>
                    </li>

                <?php endif; ?>

                <li>
                    <a href="gantt_chart.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-chart-area"></i> Gantt Chart
                    </a>
                </li>

                <li>
                    <a href="digest.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-chart-area"></i> Activity Digest
                    </a>
                </li>
                    

                <li>
                    <a id="leave-application-side-menu" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-sign-out-alt"></i> Leave Application
                    </a>
                </li>
                <!-- <li>
                    <a href="kpi_report.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-chart-pie"></i> KPI Report
                    </a>
                </li> -->

                <!-- <li>
                    <a href="specialday_settings.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-gifts"></i> specialday Calender
                    </a>
                </li> -->

                <?php
                if ($_SESSION['wm_ucatno'] == 19) : ?>
                    <li style="margin: -0.5rem 0;">
                        <a href="users.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-users"></i> Users
                        </a>
                    </li>
                <?php endif; ?>

                <!-- <li class="app-sidebar__heading">Task Manager</li> -->

                <?php
                if ($_SESSION['wm_permissionlevel'] >= 1 || $_SESSION['wm_ucatno'] == 19) : ?>
                    <!-- <li>
                        <a href="task_manager.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-tasks"></i> Task Settings
                        </a>
                    </li> -->
                <?php endif; ?>

                <!-- <li>
                    <a href="task_filter.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-filter"></i> Task Filter
                    </a>
                </li> -->

                <?php
                if ($_SESSION['wm_ucatno'] == 19) : ?>
                    <!-- <li style="display: none;">
                        <a href="task_today.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-tasks"></i> Task Today
                        </a>
                    </li>
                    <li style="display: none;">
                        <a href="incomplete_task_alltime.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-folder-minus"></i>All Incomplete Task
                        </a>
                    </li> -->
                <?php endif; ?>

                <!-- <li style="display: none;">
                    <a href="my_incomplete_task.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-folder-minus"></i> My Incomplete Task
                    </a>
                </li>
                <li>
                    <a href="available_task.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-folder"></i> Available Task
                    </a>
                </li> -->

                <li class="app-sidebar__heading">Channels</li>

                <?php
                if ($_SESSION['wm_ucatno'] === 19) : ?>
                    <li style="margin: -0.5rem 0;">
                        <a href="setup_channels.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-plus-square"></i> Setup Channel
                        </a>
                    </li>

                    <!-- <li>
                        <a href="story.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-comments"></i> Story
                        </a>
                    </li> -->
                <?php endif; ?>

                <li style="margin: -0.5rem 0;">
                    <a href="task_filter.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-tasks"></i> Task Filter
                    </a>
                </li>
            </ul>
        </div>
        <!-- <div class="sticky-top text-center bg-light w-100" style="bottom:0; padding: 10px 0 10px 0;">
            <div class=""> Developed by </div>
            <a class="text-decoration-none" style="font-size:16px;" href="//agamilabs.com" target="_blank">AGAMiLabs Ltd.</a>
        </div> -->
    </div>
</div>


<script type="text/javascript">
    function get_channels() {
        return new Promise((resolve, reject) => {
            let channel_data = $(`#channels_container`).data(`channel_data`);

            if (channel_data && channel_data.length) {
                resolve(channel_data);
            } else {
                try {
                    $.post(`php/ui/settings/channel/get_channels.php`, resp => {
                        if (resp.error) {
                            toastr.error(resp.message);
                        } else {
                            $(`#channels_container`).data(`channel_data`, resp.data);
                            resolve(resp.data);
                        }
                    }, `json`);
                } catch (error) {
                    reject(error);
                }
            }
        });
    };

    (get_channels()).then(
        result => show_channels(result),
        error => console.log(error)
    );

    function show_channels(data) {
        data = data.filter(a => a.isactive == 1);
        $.each(data, (index, value) => {

            let subchannels = value.subchannels.filter(a => a.isactive == 1);

            let listTag = $(`<li class="mm-active" style="margin: -0.5rem 0;">
                    <a href="javascript:void(0);" class="menu-anchor menu-anchor-lvl-1" aria-expanded="true">
                        <i class="metismenu-icon fas fa-layer-group"></i> ${value.channeltitle}
                        <span class="chat_badge badge badge-info rounded-circle p-0"></span>
                        <span class="task_badge badge badge-warning rounded-circle p-0"></span>
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse mm-show">
                        ${subchannels.map(a =>
                        `<li style="margin: -0.5rem 0;">
                            <a href="story.php?channelno=${a.channelno}" class="menu-anchor menu-anchor-lvl-2">
                                <i class="metismenu-icon"></i> <i class="fas fa-comments opacity-6 mr-2"></i> ${a.channeltitle}
                                <span class="chat_badge badge badge-info rounded-circle p-0"></span>
                                <span class="task_badge badge badge-warning rounded-circle p-0"></span>
                            </a>
                        </li>`)
                        .join("")}
                    </ul>
                </li>`)
                .appendTo(`#channels_container`);
        });


        const searchParams = new URLSearchParams(window.location.search);
        const selected_channel = searchParams.has('channelno') ? searchParams.get('channelno') : '';

        // let select1 = $(`#task_channel_select`).empty();
        // let select2 = $(`#task_manager_setup_modal_form [name="channelno"]`).empty();
        let select3 = $(`.widget-content-left [name="channelno"]`).empty();

        $.each(data, (index, value) => {
            // let optgroup1 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select1);
            // let optgroup2 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select2);
            let optgroup3 = $(`<optgroup label="${value.channeltitle}"></optgroup>`).appendTo(select3);
            let subchannels = value.subchannels.filter(a => a.isactive == 1);
            $.each(subchannels, (indexInSubChannels, valueOfSubChannels) => {
                // $(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup1);
                // $(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup2);
                $(`<option value="${valueOfSubChannels.channelno}" ${selected_channel==valueOfSubChannels.channelno?"selected":""}>${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup3);
            });
        });

        $(select3).change(function() {
            location.href = "story.php" + "?" + "channelno=" + $(this).val();
        });

        $('.widget-content-left [name="channel_select_form"]').submit(function(e) {
            e.preventDefault();
            let channelno = $('.widget-content-left [name="channelno"]').val();
            location.href = "story.php" + "?" + "channelno=" + channelno;
        });

        setTimeout(function() {
            sidebar_menu_activate();
        }, 500);

        setTimeout(function() {
            get_channel_notification();
        }, 500);
    }

    function get_channel_notification() {
        $.ajax({
            type: "POST",
            url: "php/ui/notification/get_channel_notification.php",
            success: (result) => {
                let resp = $.parseJSON(result);

                if (resp.error) {
                    toastr.error(resp.message);
                } else {
                    // let parentUpdateQty = resp.data?.filter(a => a.parentchannel == value.channelno)?.map(a => a.updateqty)?.reduce((a, b) => parseInt(a) + parseInt(b), 0) || "";

                    $.each(resp.chat, (index, value) => {
                        $(`a[href="story.php?channelno=${value.channelno}"] .chat_badge`).html(value.updateqty);
                        let parentBadgeElement = $(`a[href="story.php?channelno=${value.channelno}"]`).parents(`.mm-collapse`).siblings(`.menu-anchor-lvl-1`).find(`.chat_badge`);
                        parentBadgeElement.hide().html((parseInt(parentBadgeElement.html(), 10) || 0) + value.updateqty).show(index * 50);
                    });

                    $.each(resp.task, (index, value) => {
                        $(`a[href="story.php?channelno=${value.channelno}"] .task_badge`).html(value.updateqty);
                        let parentBadgeElement = $(`a[href="story.php?channelno=${value.channelno}"]`).parents(`.mm-collapse`).siblings(`.menu-anchor-lvl-1`).find(`.task_badge`);
                        parentBadgeElement.hide().html((parseInt(parentBadgeElement.html(), 10) || 0) + value.updateqty).show(index * 50);
                    });
                }
            }
        });
    }

    setInterval(function() {
        get_channel_notification();
    }, 600000);
</script>

<script>
    function get_my_watchlist() {
        return new Promise((resolve, reject) => {
            let channel_data = $(`#watchlist_container`).data(`watchlist_data`);

            if (channel_data && channel_data.length) {
                resolve(channel_data);
            } else {
                try {
                    $.post(`php/ui/watchlist/get_my_watchlist.php`, resp => {
                        if (resp.error) {
                            toastr.error(resp.message);
                        } else {
                            $(`#watchlist_container`).data(`watchlist_data`, resp.data);
                            resolve(resp.data);
                        }
                    }, `json`);
                } catch (error) {
                    reject(error);
                }
            }
        });
    };

    (get_my_watchlist()).then(
        result => show_watchlist(result),
        error => console.log(error)
    );

    function show_watchlist(result) {
        const my_watchlist = $('.my_watchlist').empty();

        $.each(result, function(index, elm) {
            let scheduleHTML = ``;
            if (elm.schedule_progress.length) {
                $.each(elm.schedule_progress, (_i, prog) => {
                    let progressTitle = `: ${prog.statustitle} (${prog.percentile}%)`;

                    let percentileClass = `border-percent-${(Math.round((prog.percentile || 0) % 101 / 10) * 10).toFixed(0)}`;

                    let progressHTML = `<div class="progress-circle mr-1" data-value="${prog.percentile || 0}" title="${prog.assignee}${progressTitle}">
                            <span class="progress-left">
                                <span class="progress-bar ${percentileClass}"></span>
                            </span>
                            <span class="progress-right">
                                <span class="progress-bar ${percentileClass}"></span>
                            </span>
                            <div class="progress-value w-100 he-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="font-weight-bold">
                                    <img src="${prog.photo_url || `assets/image/user_icon.png`}"
                                        class="rounded-circle" style="width:36px;" alt="${prog.assignee}">
                                </div>
                            </div>
                        </div>`;

                    scheduleHTML += progressHTML;
                });

                scheduleHTML = `<div class="d-flex">${scheduleHTML}</div>`
            } else {
                scheduleHTML = `<i>No progress yet!</i>`;
            }

            let tpl = $(`<div class="card mt-1" style="border-radius:10px 0px 0 10px;">
                    <div class='card-body pl-2 pr-2 py-2'>
                        <div style='color:black;'>
                            <i class='cursor-pointer remove_watch_list fa fa-window-close text-danger'></i> ${elm.channeltitle}
                        </div>
                        <div style='font-size:10px;' title='${elm.story||""}'>
                            ${(elm.story || ``).substr(0, 100)} ${elm.story.length > 100 ? "..." : ""}
                        </div>
                        <div class='card-footer pt-2 pb-0 px-0 w-100' style='overflow-x: auto;'>
                            ${scheduleHTML}
                        </div>
                    </div>
                </div>`)
                .appendTo(my_watchlist);

            (function() {
                $('.remove_watch_list', tpl).click(function() {
                    if (confirm("Are you sure?")) {
                        remove_my_watchlist({
                            backlogno: elm.backlogno
                        }, $(this).parents(`.progress_parent_div`));
                    }
                });
            })();
        });
    }

    function add_my_watchlist(json, parentContainer) {
        $.post(`php/ui/watchlist/add_my_watchlist.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                toastr.success(resp.message);
                $(parentContainer).next(`.fa-arrow-right`).remove();
                $(parentContainer).remove();
                let pageno = $("#task_manager_table_pageno_input").val();
                get_channel_task_detail(pageno);
                // get_channel_backlogs(pageno);
            }

            (get_my_watchlist()).then(
                result => show_watchlist(result),
                error => console.log(error)
            );
        }, `json`);
    }

    function remove_my_watchlist(json, parentContainer) {
        $.post(`php/ui/watchlist/remove_my_watchlist.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                toastr.success(resp.message);
                $(parentContainer).next(`.fa-arrow-right`).remove();
                $(parentContainer).remove();
                let pageno = $("#task_manager_table_pageno_input").val();
                get_channel_task_detail(pageno);
                // get_channel_backlogs(pageno);
            }

            (get_my_watchlist()).then(
                result => show_watchlist(result),
                error => console.log(error)
            );
        }, `json`);
    }
</script>

<script type="text/javascript">
    function sidebar_menu_activate() {
        let filename = window.location.pathname.split("/").pop();
        filename = (filename == `story.php`) ? filename + window.location.search : filename;
        // console.log(`filename =>`, filename);

        var currentmenu = $(`.scrollbar-sidebar a[href=\"${filename}\"]`);
        currentmenu.addClass("mm-active");
        currentmenu.closest("ul").addClass("mm-show")
        currentmenu.parent("li").addClass("mm-active");

        $('.scrollbar-sidebar').scrollTop(0);

        if ($(currentmenu).length) {
            $(".scrollbar-sidebar").animate({
                scrollTop: ($(currentmenu).offset().top || 0) - 230
            });
        }

        // console.log("html", currentmenu.text().trim());

        var newTitle = currentmenu.text().trim() + ` | Employee | <?= $response['orgname']; ?>.`;

        if (document.title != newTitle) {
            document.title = newTitle;
        }
    }

    // const isDarkTheme = localStorage.getItem("isDarkTheme") == "true";
    // $("#theme_custom_switch").prop("checked", isDarkTheme);

    // if (isDarkTheme) {
    //     $(".app-header").addClass("bg-dark header-text-light");
    //     $(".app-sidebar").addClass("bg-dark sidebar-text-light");
    // }

    // $("#dark_theme").click(function(e) {
    //     e.preventDefault();
    //     localStorage.setItem("isDarkTheme", true);
    //     $("#theme_custom_switch").prop("checked", true);
    //     $(".app-header").addClass("bg-dark header-text-light");
    //     $(".app-sidebar").addClass("bg-dark sidebar-text-light");
    // });

    // $("#light_theme").click(function(e) {
    //     e.preventDefault();
    //     localStorage.setItem("isDarkTheme", false);
    //     $("#theme_custom_switch").prop("checked", false);
    //     $(".app-header").removeClass("bg-dark header-text-light");
    //     $(".app-sidebar").removeClass("bg-dark sidebar-text-light");
    // });
</script>

<script>
    $(`#leave-application-side-menu`).click(function(e) {
        $(`#leave_application_modal`).modal("show");
        $(`#leave_application_modal_form`).trigger("reset");
    });
</script>
