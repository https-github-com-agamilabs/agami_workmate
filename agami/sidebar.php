<div class="app-sidebar sidebar-shadow d-print-none">
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
            <ul class="vertical-nav-menu mt-3">
                <li class="app-sidebar__heading">Agami Operations</li>

                <li>
                    <a href="dashboard.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="orgs.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-building"></i> Client Organizations
                    </a>
                </li>

                <li>
                    <a href="clients.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-users"></i> Client Users
                    </a>
                </li>

                <hr>

                <li>
                    <a href="package_coupon.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-suitcase-rolling"></i> Manage Coupons
                    </a>
                </li>

                <li>
                    <a href="package_offer.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-suitcase-rolling"></i> Package Offers
                    </a>
                </li>
                
                <li class="mm-active">
                    <a href="javascript:void(0);" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-user-tag"></i> Chart of Accounts
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        <li>
                            <a href="chart_of_accounts.php" class="menu-anchor menu-anchor-lvl-2">
                                <i class="metismenu-icon"></i> Hierarchical
                            </a>
                        </li>
                        <!-- <li>
                            <a href="list_of_accounts.php" class="menu-anchor menu-anchor-lvl-2">
                                <i class="metismenu-icon"></i> Flat
                            </a>
                        </li> -->
                    </ul>
                </li>

                <li>
                    <a href="imagegallery.php" class="menu-anchor menu-anchor-lvl-1">
                        <i class="metismenu-icon fas fa-photo-video"></i> Portfolio & Gallery
                    </a>
                </li>

            </ul>
        </div>
        <!-- <div class="sticky-top text-center bg-light w-100" style="bottom:0; padding: 10px 0 10px 0;">
            <div> Developed by </div>
            <a class="text-decoration-none" style="font-size:16px;" href="//agamilabs.com" target="_blank">AGAMiLabs Ltd.</a>
        </div> -->
    </div>
</div>

<script type="text/javascript">
    var currentmenu = $(`.scrollbar-sidebar a[href=\'${window.location.pathname.split("/").pop()}\']`);
    currentmenu.addClass("mm-active").closest("ul").addClass("mm-show").parent("li").addClass("mm-active");

    $('.scrollbar-sidebar').scrollTop(0);

    if ($(currentmenu).length) {
        $(".scrollbar-sidebar").animate({
            scrollTop: ($(currentmenu).offset().top || 0) - 230
        });
    }

    console.log("html", currentmenu.text().trim());

    var newTitle = currentmenu.text().trim() + " | Agami | <?= $response['orgname']; ?>.";

    if (document.title != newTitle) {
        document.title = newTitle;
    }
    // $('meta[name="description"]').attr("content", newDescription);

    // if (currentmenu.hasClass('menu-anchor-lvl-1')) {
    //     // top level menu selected
    // } else if (currentmenu.hasClass('menu-anchor-lvl-2')) {
    //     // second level menu selected
    //
    //     // select the top level menu
    //
    //     // var topMenu = $(currentmenu).closest('ul').closest('li')[0];//.trigger('click');//.siblings('a.menu-anchor-lvl-1').expand();
    //
    //     // console.log("topMenu", topMenu);
    //     // $(topMenu).addClass('mm-active');
    // } else {
    //     // undefined
    // }
    //
    // console.log("currentmenu", currentmenu);

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