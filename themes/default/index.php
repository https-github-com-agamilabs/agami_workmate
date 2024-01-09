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
include_once "./lang_converter/converter.php";
$jasonFilePath = './lang-json/' . $lang . '/index.json';
$arrayData = langConverter($jasonFilePath);
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
    <title>Virtual Assistant Management System | Home</title>

    <script>
        const publicAccessUrl = `<?= $publicAccessUrl ?>`;
        const lang = `<?= $_SESSION["lang"] ?>`;
    </script>

    <!-- Favicon  -->
    <link rel="icon" href="<?= $publicAccessUrl . $response['orglogourl']; ?>">

    <!-- Style CSS -->
    <link rel="stylesheet" href="<?= $publicAccessUrl ?>themes/default/css/style.css">

    <!-- jQuery (Necessary for All JavaScript Plugins) -->
    <script src="<?= $publicAccessUrl ?>themes/default/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- <script src="< ?= $publicAccessUrl ?>vendor/jquery/3.5.1/jquery-3.5.1.min.js"></script> -->
    <!-- Popper js -->
    <script src="<?= $publicAccessUrl ?>vendor/popper/1.11.0/umd/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="<?= $publicAccessUrl ?>vendor/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Plugins js -->
    <script src="<?= $publicAccessUrl ?>themes/default/js/plugins.js" async defer></script>
    <!-- Active js -->
    <script src="<?= $publicAccessUrl ?>themes/default/js/active.js" async defer></script>

    <!-- select2 css, js  -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <!-- toastr css, js -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>

    <!-- recaptcha  -->
    <script src='//www.google.com/recaptcha/api.js?render=6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA'></script>

    <!-- header-area css-->
    <link rel="stylesheet" href="<?= $publicAccessUrl ?>themes/default/css/index.css">
    <link rel="stylesheet" href="<?= $publicAccessUrl ?>themes/default/css/header-area.css">

    <!-- index, header-area js  -->
    <script src="<?= $publicAccessUrl ?>themes/default/js/index.js" async defer></script>
    <script src="<?= $publicAccessUrl ?>themes/default/js/header-area.js" async defer></script>

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

</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="medilife-load"></div>
    </div>

    <?php
    $imgPath = $publicAccessUrl . "themes/default/img/";
    ?>

    
    <?php require_once "header-area.php"; ?>

    <!-- ***** Hero Area Start ***** -->
    <section class="hero-area">
        <div class="hero-slides owl-carousel">

        </div>
    </section>
    <!-- ***** Hero Area End ***** -->

    <!-- ***** Book An Appoinment Area Start ***** -->
    <div class="medilife-book-an-appoinment-area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_appointment appointment-form-content">
                        <div class="row no-gutters align-items-center">
                            <div class="col-12 col-lg-9">
                                <div class="medilife-appointment-form">
                                    <div class="section_title h3 text-white mb-3">Try a VA for free</div>

                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="medilife-contact-info">

                                    <div class="section_description single-contact-info text-white">
                                        <p class="text-justify">Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                                            Itaque debitis ullam vitae vel totam aliquam id! Illum, fugiat. Ex vel, sapiente quibusdam iusto sint harum magnam.
                                        </p>
                                        <p class="text-justify">Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                                            Itaque debitis ullam vitae vel totam aliquam id! Illum, fugiat. Ex vel, sapiente quibusdam iusto sint harum magnam.
                                        </p>
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
    <section id="medica-about-us-area" class="medica-about-us-area">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="section_services medica-about-content sticky-top pb-4" style="padding-top: 90px;">
                        <div class="px-3 py-4" style="background-color: #f2fff5;">
                            <h2 class="section_title">Services</h2>
                            <h6 class="section_subtitle">We always put our customer first</h6>
                            <p class="section_description">

                            </p>
                            <a href="#" class="btn medilife-btn mt-50">View the services <span>+</span></a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8 section-padding-100-20">
                    <div class="services_row row">

                    </div>
                </div>
            </div>

            <hr>
        </div>
    </section>
    <!-- ***** About Us Area End ***** -->

    <!-- ***** Gallery Area Start ***** -->
    <div class="medilife-gallery-area owl-carousel">

    </div>
    <!-- ***** Gallery Area End ***** -->

    <!-- ***** Cool Facts Area Start ***** -->
    <section class="medilife-cool-facts-area section-padding-100-0">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-building"></i>
                        <h2><span class="company_qty">3+</span></h2>
                        <h6>Companies</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fas fa-user"></i>
                        <h2><span class="va_qty">10+</span></h2>
                        <h6>Virtual Assistants</h6>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="single-cool-fact-area text-center mb-100">
                        <i class="fa fa-list"></i>
                        <h2><span class="service_qty">19+</span></h2>
                        <h6>Services</h6>
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
                        <h2>A new way to treat customer in a revolutionary facility</h2>
                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing eli.Lorem ipsum dolor sit amet, consec tetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer.</p>
                        <a href="#" class="btn medilife-btn mt-50">View the services <span>+</span></a>
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

            </div>
        </div>
    </div>
    <!-- ***** Blog Area End ***** -->

    <!-- ***** Emergency Area Start ***** -->
    <div class="medilife-emergency-area section-padding-100-50">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4 text-lg-center mb-3">
                    <i class="icon-monitor fa-10x text-white"></i>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="emergency-content">
                        <h3 class="text-dark">
                            <!-- For Emergency calls -->
                            24/7 Hotline
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


    <?php
    // require_once $basePath . "/find_doctor.php";
    ?>

</body>

</html>