<style>
    .app-theme-white .app-sidebar {
        background-color: transparent !important;
    }

    .app-sidebar.sidebar-shadow {}
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

    <div class="scrollbar-sidebar" style="overflow-y:auto;">
        <div class="app-sidebar__inner">
            <ul id="channels_container" class="vertical-nav-menu mt-3">

                <?php
                if ($ucatno == 5) : ?>
                    <li>
                        <a href="dashboard.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                if ($ucatno != 5) : ?>
                    <li class="app-sidebar__heading">Time Keeper</li>

                    <li>
                        <a href="time_keeper.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-play-circle"></i> Time Keeper
                        </a>
                    </li>
                    <li>
                        <a href="time_keeper_summary.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-chart-bar"></i> Time Keeper Summary
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                if ($ucatno == 19) : ?>
                    <!-- <li>
                        <a href="gantt_chart.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-chart-area"></i> Gantt Chart
                        </a>
                    </li> -->

                    <!-- <li>
                        <a href="key_performance_indicator.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-chart-line"></i> KPI Setting
                        </a>
                    </li> -->
                <?php endif; ?>

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
                if ($ucatno == 19) : ?>
                    <li>
                        <a href="users.php" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-users"></i> Users
                        </a>
                    </li>
                <?php endif; ?>

                <!-- <li class="app-sidebar__heading">Task Manager</li> -->

                <?php
                if ($_SESSION['cogo_permissionlevel'] >= 1 || $ucatno == 19) : ?>
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
                if ($ucatno == 19) : ?>
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
                if ($ucatno === 19) : ?>
                    <li>
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
        $.each(data, (index, value) => {
            let listTag = $(`<li>`)
                .appendTo(`#channels_container`)
                .append(`<a href="javascript:void(0);" class="menu-anchor menu-anchor-lvl-1">
                            <i class="metismenu-icon fas fa-layer-group"></i> ${value.channeltitle}
                            <span class="chat_badge badge badge-info rounded-circle p-1"></span>
                            <span class="task_badge badge badge-warning rounded-circle p-1"></span>
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            ${value.subchannels.map(a =>
                            `<li>
                                <a href="story.php?channelno=${a.channelno}" class="menu-anchor menu-anchor-lvl-2">
                                    <i class="metismenu-icon"></i> <i class="fas fa-comments opacity-6 mr-2"></i> ${a.channeltitle}
                                    <span class="chat_badge badge badge-info rounded-circle p-1"></span>
                                    <span class="task_badge badge badge-warning rounded-circle p-1"></span>
                                </a>
                            </li>`)
                            .join("")}
                        </ul>`);
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

            $.each(value.subchannels, (indexInSubChannels, valueOfSubChannels) => {
                // $(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup1);
                // $(`<option value="${valueOfSubChannels.channelno}">${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup2);
                $(`<option value="${valueOfSubChannels.channelno}" ${selected_channel==valueOfSubChannels.channelno?"selected":""}>${valueOfSubChannels.channeltitle}</option>`).appendTo(optgroup3);
            });
        });

        $(select3).input(function(){
            location.href = "story.php"+"?"+"channelno="+$(this).val();
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
                // console.log("GET_CHANNEL_NOTIFICATION RESULT=>", result);
                let resp = $.parseJSON(result);
                // console.log("GET_CHANNEL_NOTIFICATION RESP=>", resp);

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

    const isDarkTheme = localStorage.getItem("isDarkTheme") == "true";
    $("#theme_custom_switch").prop("checked", isDarkTheme);

    if (isDarkTheme) {
        $(".app-header").addClass("bg-dark header-text-light");
        $(".app-sidebar").addClass("bg-dark sidebar-text-light");
    }

    $("#dark_theme").click(function(e) {
        e.preventDefault();
        localStorage.setItem("isDarkTheme", true);
        $("#theme_custom_switch").prop("checked", true);
        $(".app-header").addClass("bg-dark header-text-light");
        $(".app-sidebar").addClass("bg-dark sidebar-text-light");
    });

    $("#light_theme").click(function(e) {
        e.preventDefault();
        localStorage.setItem("isDarkTheme", false);
        $("#theme_custom_switch").prop("checked", false);
        $(".app-header").removeClass("bg-dark header-text-light");
        $(".app-sidebar").removeClass("bg-dark sidebar-text-light");
    });
</script>