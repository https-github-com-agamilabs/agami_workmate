<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<?php
function readContents($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $contents = curl_exec($ch);
    if (curl_errno($ch)) {
        echo curl_error($ch);
        echo "\n<br />";
        $contents = '';
    } else {
        curl_close($ch);
    }

    if (!is_string($contents) || !strlen($contents)) {
        echo "Failed to get contents.";
        $contents = '';
    }

    return $contents;
}

?>

<?php
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

?>

<?php
require_once dirname(dirname(dirname(__FILE__))) . "/lang_converter/converter.php";
// $jasonFilePath = './lang-json/' . $lang . '/index.json';
if (!isset($arrayData)) {
    $arrayData = array();
}
$arrayData = array_merge($arrayData, langConverter($lang, 'index'));
//print_r($arrayData);
//echo $arrayData[$lang]['lang_home'];

?>



<?php
$basePath = dirname(dirname(dirname(__FILE__)));
include_once($basePath . "/configmanager/org_configuration.php");
if (!defined("DB_USER")) {
    include_once $basePath . '/php/db/config.php';
} else {
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>Smart Accounting System | Home</title>

    <!-- Favicon  -->
    <link rel="icon" href="<?= $publicAccessUrl . $response['orglogourl']; ?>">

    <!-- Style CSS -->
    <link rel="stylesheet" href="<?= $publicAccessUrl ?>themes/medi/css/style.css">

    <!-- jQuery (Necessary for All JavaScript Plugins) -->
    <script src="<?= $publicAccessUrl ?>themes/medi/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- <script src="< ?= $publicAccessUrl ?>vendor/jquery/3.5.1/jquery-3.5.1.min.js"></script> -->
    <!-- Popper js -->
    <script src="<?= $publicAccessUrl ?>vendor/popper/1.11.0/umd/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="<?= $publicAccessUrl ?>vendor/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Plugins js -->
    <script src="<?= $publicAccessUrl ?>themes/medi/js/plugins.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />

    <style>
        .main-header-area .search-query::placeholder {
            color: #081f3e;
        }

        .is-sticky .main-header-area .search-query::placeholder {
            color: #868e96;
        }

        .main-header-area .search-query:focus {
            border-color: #113f71;
        }

        .is-sticky .main-header-area .search-query:focus {
            border-color: #80bdff;
        }

        .shadow-3-strong {
            box-shadow: 0 2px 6px -1px rgba(0, 0, 0, .16), 0 6px 18px -1px rgba(0, 0, 0, .1) !important;
        }

        #patient_appointment_form .select2-container .select2-selection--single {
            height: 38px !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #patient_appointment_form .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px !important;
        }

        #patient_appointment_form .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }

        #patient_appointment_form input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #fff !important;
            border-radius: 0.2rem;
        }

        #patient_appointment_form .form-control[disabled],
        #patient_appointment_form .form-control[disabled]:focus,
        #patient_appointment_form .form-control[disabled]:hover,
        #patient_appointment_form .form-control[readonly],
        #patient_appointment_form .form-control[readonly]:focus,
        #patient_appointment_form .form-control[readonly]:hover {
            color: #081f3e;
            background-color: #fff;
        }

        #patient_appointment_form .form-control:not(select, [disabled], [readonly]):focus {
            color: #fff;
        }

        @media (min-width: 992px) {
            .single-hero-slide {
                height: 100vh;
            }
        }
    </style>

    <style>
        @media (max-width: 575.98px) {
            .hero-slides .owl-controls {
                display: none;
            }
        }
    </style>

    <?php

    $PAGE_DATA = array();


    if (isset($_GET['lang'])) {
        $_SESSION["lang"] = $_GET['lang'];
    } else if (!isset($_SESSION["lang"])) {
        $_SESSION["lang"] = "en";
    }
    $lang = $_SESSION["lang"];

    require_once dirname(dirname(dirname(__FILE__))) . "/lang_converter/converter.php";
    // $jasonFilePath = $basePath . "/lang-json/$lang/organizations.json";
    if (!isset($orgData)) {
        $orgData = array();
    }
    $orgData = array_merge($orgData, langConverter($lang, 'index_en'));

    // $file_path = $publicAccessUrl . "themes/medi/index.json";
    $file_path = $publicAccessUrl . "lang-json/$lang/index_en.json";

    $PAGE_DATA = json_decode(readContents($file_path), true);

    if (!$PAGE_DATA) {
        $PAGE_DATA = null;
    } else {
        // $PAGE_DATA = json_encode($PAGE_DATA);
    }

    ?>

</head>

<body>
    <script>
        const publicAccessUrl = `<?= $publicAccessUrl ?>`;
    </script>
    <script>
        var PAGE_DATA = <?= json_encode($PAGE_DATA); ?>;
        console.log(PAGE_DATA);
        if (PAGE_DATA.length) {
            $.get(`${publicAccessUrl}lang-json/en/index_en.json`, data => {
                // $.get(`${publicAccessUrl}themes/medi/index.json`, data => {

                // show_data(data);
                PAGE_DATA = data;
            }, `json`);
        }
    </script>
    <!-- Preloader -->
    <div id="preloader">
        <div class="medilife-load"></div>
    </div>

    <?php
    $imgPath = $publicAccessUrl . "themes/medi/img/";
    ?>

    <?php require_once "header-area.php"; ?>

    <!-- ***** Hero Area Start ***** -->
    <section class="hero-area">
        <div class="hero-slides owl-carousel">
            <!-- Single Hero Slide -->
            <!-- <div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(<?= $imgPath ?>bg-img/hero1.jpg);">
                <div class="container h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-12">
                            <div class="hero-slides-content">
                                <h2 data-animation="fadeInUp" data-delay="100ms">Holistic & Online<br>Health-care Services</h2>
                                <h3 data-animation="fadeInUp" data-delay="200ms">That you can Trust 100%</h3>
                                <h6 data-animation="fadeInUp" data-delay="400ms">The system is dedicated to <br> patients, doctors, care-givers, medicine company, pharmacy, and diagnostics service.</h6>
                                <a href="#" class="btn medilife-btn mt-50" data-animation="fadeInUp" data-delay="700ms">Which Doctor I Should See <span>AI</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </section>
    <!-- ***** Hero Area End ***** -->

    <!-- ***** Book An Appoinment Area Start ***** -->
    <div id="about_section" class="medilife-book-an-appoinment-area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_about appointment-form-content">
                        <div class="row no-gutters align-items-center">
                            <div class="col-12 col-lg-9">
                                <div class="medilife-appointment-form">
                                    <div class="section_title h3 text-white mb-3">About</div>
                                    <div class="section_subtitle h5 text-white mb-3">Smart Accounting</div>
                                    <div class="section_description text-white mb-3"></div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="medilife-contact-info">
                                    <div class="section_keynote single-contact-info text-white">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Book An Appoinment Area End ***** -->

    <!-- ***** About Us Area Start ***** -->
    <section id="features_section" class="medica-about-us-area">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="section_services medica-about-content sticky-top pb-4" style="padding-top: 90px;">
                        <div class="px-3 py-4" style="background-color: #f2fff5;">
                            <h2 class="section_title">Features</h2>
                            <h6 class="section_subtitle">We always put our patients first</h6>
                            <p class="section_description">
                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing eli.
                            </p>
                            <a href="#" class="btn medilife-btn mt-50"><?= $arrayData['lang_view_the_services']; ?> <span>+</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8 section-padding-100-20">
                    <div class="services_row row">
                        <!-- Single Service Area -->
                        <!-- <div class="col-12 col-sm-6">
                            <div class="single-service-area d-flex">
                                <div class="service-icon">
                                    <i class="icon-doctor"></i>
                                </div>
                                <div class="service-content">
                                    <h5>The Best Doctors</h5>
                                    <p>Lorem ipsum dolor sit amet, consecte tuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut.</p>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** About Us Area End ***** -->

    <!-- ***** Gallery Area Start ***** -->
    <!-- gellary.html -->
    <!-- ***** Gallery Area End ***** -->

    <!-- ***** Cool Facts Area Start ***** -->
    <section class="medilife-cool-facts-area section-padding-100-0 d-none">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-user-injured"></i>
                        <h2><span class="patient_qty counter">0</span></h2>
                        <h6>Patient</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-prescription"></i>
                        <h2><span class="prescription_qty counter">0</span></h2>
                        <h6>Prescription</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fa fa-hospital-alt"></i>
                        <h2><span class="healthcenter_qty counter">0</span></h2>
                        <h6>Health Center</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fa fa-user-md"></i>
                        <h2><span class="doctor_qty counter">0</span></h2>
                        <h6>Doctor</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-user-nurse"></i>
                        <h2><span class="caregiver_qty counter">0</span></h2>
                        <h6>Caregiver</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-briefcase-medical"></i>
                        <h2><span class="servicecall_qty counter">0</span></h2>
                        <h6>Service Call</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-store"></i>
                        <h2><span class="store_qty counter">0</span></h2>
                        <h6>Store</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-pills"></i>
                        <h2><span class="medicine_qty counter">0</span></h2>
                        <h6>Medicine</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Cool Facts Area End ***** -->

    <!-- ***** Features Area Start ***** -->
    <div class="medilife-features-area section-padding-100 d-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="features-content">
                        <h2>A new way to treat pacients in a revolutionary facility</h2>
                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing eli.Lorem ipsum dolor sit amet, consec tetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer.</p>
                        <a href="#" class="btn medilife-btn mt-50"><?= $arrayData['lang_view_the_services']; ?> <span>+</span></a>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="features-thumbnail">
                        <img src="<?= $imgPath ?>bg-img/medical1.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Features Area End ***** -->

    <!-- ***** Blog Area Start ***** -->
    <div class="medilife-blog-area section-padding-100-0 d-none">
        <div class="container">
            <div class="row">
                <!-- Single Blog Area -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="single-blog-area mb-100">
                        <!-- Post Thumbnail -->
                        <div class="blog-post-thumbnail">
                            <img src="<?= $imgPath ?>blog-img/1.jpg" alt="">
                            <!-- Post Date -->
                            <div class="post-date">
                                <a href="#">Jan 23, 2018</a>
                            </div>
                        </div>
                        <!-- Post Content -->
                        <div class="post-content">
                            <div class="post-author">
                                <a href="#"><img src="<?= $imgPath ?>blog-img/p1.jpg" alt=""></a>
                            </div>
                            <a href="#" class="headline">New drog release soon</a>
                            <p>Dolor sit amet, consecte tuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.</p>
                            <a href="#" class="comments">3 Comments</a>
                        </div>
                    </div>
                </div>
                <!-- Single Blog Area -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="single-blog-area mb-100">
                        <!-- Post Thumbnail -->
                        <div class="blog-post-thumbnail">
                            <img src="<?= $imgPath ?>blog-img/2.jpg" alt="">
                            <!-- Post Date -->
                            <div class="post-date">
                                <a href="#">Jan 23, 2018</a>
                            </div>
                        </div>
                        <!-- Post Content -->
                        <div class="post-content">
                            <div class="post-author">
                                <a href="#"><img src="<?= $imgPath ?>blog-img/p2.jpg" alt=""></a>
                            </div>
                            <a href="#" class="headline">Free dental care</a>
                            <p>Dolor sit amet, consecte tuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.</p>
                            <a href="#" class="comments">3 Comments</a>
                        </div>
                    </div>
                </div>
                <!-- Single Blog Area -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="single-blog-area mb-100">
                        <!-- Post Thumbnail -->
                        <div class="blog-post-thumbnail">
                            <img src="<?= $imgPath ?>blog-img/3.jpg" alt="">
                            <!-- Post Date -->
                            <div class="post-date">
                                <a href="#">Jan 23, 2018</a>
                            </div>
                        </div>
                        <!-- Post Content -->
                        <div class="post-content">
                            <div class="post-author">
                                <a href="#"><img src="<?= $imgPath ?>blog-img/p3.jpg" alt=""></a>
                            </div>
                            <a href="#" class="headline">Good news for the pacients</a>
                            <p>Dolor sit amet, consecte tuer adipiscing elit, sed diam nonummy nibh euismod tincidunt.</p>
                            <a href="#" class="comments">3 Comments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Blog Area End ***** -->

    <!-- ***** Emergency Area Start ***** -->
    <div class="medilife-emergency-area section-padding-100-50">
        <div class="container">
            <div class="row text-center text-lg-left">
                <div class="col-12 col-lg-4 text-lg-center mb-3">
                    <i class="fas fa-money-check-alt fa-10x text-white"></i>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="emergency-content">
                        <h3 class="text-dark">
                            <!-- For Emergency calls -->
                            <?= $arrayData['lang_support_center']; ?> <br><?= $arrayData['lang_(9am-9pm)']; ?>
                        </h3>
                        <a href="tel:+8801312257899" class="text-white">
                            <h3>+880 1312-257899</h3>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Emergency Area End ***** -->


    <?php include_once "footer_area.php"; ?>

    <script>
        const link = `https://www.google.com/maps/search/?api=1`;

        function show_data() {
            const data = PAGE_DATA;
            if (data.title && data.title.length) {
                document.title = data.title;
            }

            if (data[`nav-title`] && data[`nav-title`].length) {
                $(`#nav_title`).html(data[`nav-title`]);
            }

            if (data[`head-carousel`] && data[`head-carousel`].length) {
                $(`.hero-slides`).empty();
                show_hero_carousel_data(data[`head-carousel`]);
            }

            if (data.about && data.about.length) {
                $(`#footer_about`).html(data.about || ``);
            }

            if (data.subscription && data.subscription.length) {
                $(`#footer_subscription`).html(data.subscription || ``);
            }

            let sections = data.sections;
            // if (!Object.keys(sections).length) {
            //     return;
            // }

            if (sections.about && sections.about.title && sections.about.title.length) {
                let section = $(`.section_about`);
                let about = sections.about;

                $(`.section_title`, section).html(about.title || ``);
                $(`.section_subtitle`, section).html(about[`sub-title`] || ``);
                $(`.section_description`, section).html(about.description || ``);
                $(`.section_keynote`, section).html(about.keynote || ``);
            }

            if (sections.service && sections.service.title && sections.service.title.length) {
                $(`.services_row`).empty();
                show_service_data(sections.service, `.section_services`, `.services_row`);
            }
        }

        function show_hero_carousel_data(data) {
            let target = $(`.hero-slides`);

            $.each(data, (index, value) => {
                // if (value.isvisible == 0) {
                //     return;
                // }
                let slide = $(`<div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(${value.imageurl || ``});">
                        <div class="container-lg h-100">
                            <div class="row h-100 align-content-center justify-content-center">
                                <div class="col-12 col-sm-11 col-md-7 col-lg-7 pt-5 pt-sm-0">
                                    <div class="hero-slides-content">
                                        <h2 data-animation="fadeInUp" data-delay="100ms" class="pt-5 pt-md-0">${value.title || ``}</h2>
                                        <h3 data-animation="fadeInUp" data-delay="200ms">${value[`sub-title`] || ``}</h3>
                                        <div data-animation="fadeInUp" data-delay="400ms" style="max-width:600px;">${value[`subsub-title`] || ``}</div>
                                        <a href="#" class="btn medilife-btn mt-2 mt-lg-3" data-animation="fadeInUp" data-delay="700ms">${value[`extra-button`] || ``}</a>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-11 col-md-5 col-lg-5">
                                    <div class="bg-white rounded p-3 mt-3" style='border-radius: 10px !important;'>
                                        <div class="h5 mb-3"><?= $arrayData['lang_accounting_login']; ?></div>
                                        <form class="login_form">
                                            <div class="mb-2">
                                                <input style='border-radius: 5px !important;' name="username" type="text" class="form-control shadow-sm" minlength="3" autocomplete="off" placeholder="Enter Your Username" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="d-block mb-0">
                                                    <input style='border-radius: 5px !important;' name="password" class="form-control shadow-sm" type="password" minlength="6" autocomplete="off" placeholder="Enter Your Password" required>
                                                </label>
                                            </div>
                                            <div class="text-center">
                                                <button style='border-radius: 5px !important;' type="submit" class="btn btn-primary btn-block ripple font-size-lg custom_shadow"><?= $arrayData['lang_log_in']; ?></button>
                                            </div>
                                        </form>
                                        <div class="text-center px-2 my-1 my-md-3">
                                            <a href="javascript:void(0);" class="forgotten_password"><?= $arrayData['lang_forgotten_password?']; ?></a>
                                        </div>
                                        <hr class="my-2 my-md-3">
                                        <div class="text-center px-2">
                                            <button  style='border-radius: 5px !important;' type="button" class="create_new_account_button btn btn-success ripple font-size-lg custom_shadow"><?= $arrayData['lang_create_new_account']; ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`)
                    .appendTo(target);

                (function($) {
                    $(`[href="#"]`, slide).click(function(e) {
                        e.preventDefault();
                        $(`#findDoctorModalLabelModal`).modal(`show`);
                    });
                })(jQuery);
            });

            if ($.fn.owlCarousel) {
                target.owlCarousel({
                    items: 1,
                    margin: 0,
                    loop: true,
                    nav: true,
                    navText: ['<i class="ti-angle-left"></i>', '<i class="ti-angle-right"></i>'],
                    dots: true,
                    autoplay: false,
                    autoplayTimeout: 5000,
                    smartSpeed: 1000
                });
            }
        }

        function show_service_data(data, section, target) {
            $(`.section_title`, section).html(data.title || ``);
            $(`.section_subtitle`, section).html(data[`sub-title`] || ``);
            $(`.section_description`, section).html(data.description || ``);

            $.each(data.list, (index, value) => {
                let column = $(`<div class="col-sm-6">
                            <div class="single-service-area d-flex">
                                <div class="service-icon">
                                    <i class="${value.icon || `fas fa-user-nurse`}"></i>
                                </div>
                                <div class="service-content">
                                    <h5>${value.title || ``}</h5>
                                    <p>${value[`short-description`] || ``}</p>
                                </div>
                            </div>
                        </div>`)
                    .appendTo(target);
            });
        }
    </script>

    <!-- Active js -->
    <script src="<?= $publicAccessUrl ?>themes/medi/js/active.js" async defer></script>

    <script src='//www.google.com/recaptcha/api.js?render=6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
    <script src="<?= $publicAccessUrl ?>themes/medi/js/login.js"></script>

    <?php require_once "signup_modal.php"; ?>

</body>

</html>