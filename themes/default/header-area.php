<?php
if (isset($_GET['lang'])) {
    $_SESSION["lang"] = $_GET['lang'];
} else if (!isset($_SESSION["lang"])) {
    $_SESSION["lang"] = "en";
}

//require 'dependancy_checker.php';

date_default_timezone_set("Asia/Dhaka");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$default_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
$lang = $default_lang;
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    //$_SESSION['lang'] = $lang;
}

include_once "./lang_converter/converter.php";
$jasonFilePath = './lang-json/' . $lang . '/nav.json';
$langHeaderData = langConverter($jasonFilePath);
//print_r($arrayData);
//echo $arrayData[$lang]['lang_about_us'];

?>


<!-- ***** Header Area Start ***** -->
<header class="header-area">
    <!-- Top Header Area -->
    <div class="top-header-area">
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12 h-100">
                    <div class="h-100 d-md-flex justify-content-between align-items-center">
                        <div>
                            <p id="nav_title">
                                <?= $langHeaderData[$lang]['lang_welcome_to']; ?>
                                <span><?= $langHeaderData[$lang]['lang_holistic_online_health_care_system']; ?> </span>
                            </p>
                        </div>
                        <div>
                            <div>
                                <!-- <a href="#" class="btn-sm btn-light"><i class="fab fa-google-plus-g" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-pinterest-p px-1" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-facebook-f px-1" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light mr-3"><i class="fab fa-twitter" aria-hidden="true"></i></a> -->

                                <select class="d-none" name="lang" title="Language" style="padding: 2px 0 4px;">
                                    <option value="en">EN</option>
                                    <option value="bn">BN</option>
                                </select>


                                <div class="dropdown d-inline-block">
                                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle btn btn-light btn-sm shadow-sm rounded-0" style="padding: 2px 4px;">
                                        <i class="far fa-user-circle mr-1"></i><?= $langHeaderData[$lang]['lang_login/register']; ?>
                                    </button>
                                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right" style="width: 320px;">
                                        <form id="login_form" class="px-2">
                                            <div class="mb-2">
                                                <input id="username" name="username" type="text" class="form-control shadow-sm" minlength="3" autocomplete="off" placeholder="Enter Your Username" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="d-block mb-0">
                                                    <input id="password" name="password" class="form-control shadow-sm" type="password" minlength="6" autocomplete="off" placeholder="Enter Your Password" required>
                                                </label>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary btn-block ripple font-size-lg custom_shadow">Log in</button>
                                            </div>
                                        </form>

                                        <div class="text-center px-2 my-3">
                                            <a href="javascript:void(0);" class="forgotten_password">Forgotten password?</a>
                                        </div>

                                        <!-- <hr>

                                        <div class="text-center px-2">
                                            <button id="create_new_account_button" type="button" class="btn btn-success ripple font-size-lg custom_shadow">Create new account</button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header Area -->
        <div class="main-header-area" id="stickyHeader">
            <div class="container h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-12 h-100">
                        <div class="main-menu h-100">
                            <nav class="navbar h-100 navbar-expand-lg">
                                <!-- Logo Area  -->
                                <a class="navbar-brand" href="index.php"><img src="<?= $publicAccessUrl . $response['orglogourl']; ?>" style="height:45px;" alt="Logo"></a>

                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#medilifeMenu" aria-controls="medilifeMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

                                <div class="collapse navbar-collapse" id="medilifeMenu">
                                    <!-- Menu Area -->
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item active">
                                            <a class="nav-link" href="index.php"><?= $langHeaderData[$lang]['lang_home']; ?> <span class="sr-only">(current)</span></a>
                                        </li>
                                        <!-- <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pages</a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="index.html">Home</a>
                                            <a class="dropdown-item" href="about-us.html">About Us</a>
                                            <a class="dropdown-item" href="services.html">Services</a>
                                            <a class="dropdown-item" href="services.html">Pharmacy</a>
                                            <a class="dropdown-item" href="blog.html">Blog</a>
                                            <a class="dropdown-item" href="contact.html">Contact</a>
                                        </div>
                                    </li> -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?= $publicAccessUrl ?>about-us.php"><?= $langHeaderData[$lang]['lang_about']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#medica-about-us-area"><?= $langHeaderData[$lang]['lang_services']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#medicine_store_section"><?= $langHeaderData[$lang]['lang_va']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?= $publicAccessUrl ?>blog/index.php"><?= $langHeaderData[$lang]['lang_blog']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#footer_area"><?= $langHeaderData[$lang]['lang_contact']; ?></a>
                                        </li>
                                    </ul>

                                    <!-- <div class="input-group col-sm-3 col-md-6">
                                        <input type="text" class="search-query form-control" placeholder="<?= $langHeaderData[$lang]['lang_search_services']; ?>" />
                                        <span class="input-group-append">
                                            <button class="btn btn-danger" type="button">
                                                <span class="fa fa-search"></span>
                                            </button>
                                        </span>
                                    </div> -->
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</header>
<!-- ***** Header Area End ***** -->

<?php include_once "signup_modal.php"; ?>
<!-- <script src="js/login.js"></script> -->
