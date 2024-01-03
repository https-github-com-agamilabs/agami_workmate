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
    <title>Holistic Online Health Care System | Home</title>

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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>

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

        #medicine_store_section .select2-selection__rendered {
            font-size: .75rem;
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
    </style>

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

    <?php include_once "header-area.php"; ?>

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
            <!-- Single Hero Slide -->
            <!-- <div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(<?= $imgPath ?>bg-img/breadcumb3.jpg);">
                <div class="container h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-12">
                            <div class="hero-slides-content">
                                <h2 data-animation="fadeInUp" data-delay="100ms">Medical Services that <br>You can Trust 100%</h2>
                                <h6 data-animation="fadeInUp" data-delay="400ms">Lorem ipsum dolor sit amet, consectetuer adipiscing elit sed diam nonummy nibh euismod.</h6>
                                <a href="#" class="btn medilife-btn mt-50" data-animation="fadeInUp" data-delay="700ms">Discover Medifile <span>+</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Single Hero Slide -->
            <!-- <div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(<?= $imgPath ?>bg-img/breadcumb1.jpg);">
                <div class="container h-100">
                    <div class="row h-100 align-items-center">
                        <div class="col-12">
                            <div class="hero-slides-content">
                                <h2 data-animation="fadeInUp" data-delay="100ms">Medical Services that <br>You can Trust 100%</h2>
                                <h6 data-animation="fadeInUp" data-delay="400ms">Lorem ipsum dolor sit amet, consectetuer adipiscing elit sed diam nonummy nibh euismod.</h6>
                                <a href="#" class="btn medilife-btn mt-50" data-animation="fadeInUp" data-delay="700ms">Discover Medifile <span>+</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
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
                                    <div class="section_title h3 text-white mb-3">Doctor's Appointment</div>
                                    <div class="section_subtitle h5 text-white mb-3">Doctor's Appointment</div>
                                    <form id="patient_appointment_form">
                                        <div class="doctor_and_schedule collapse show">
                                            <div class="row align-items-end">
                                                <div class="col-md-6 form-group">
                                                    <select name="spno" class="form-control"></select>
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <select name="doctno" class="form-control" required></select>
                                                </div>
                                            </div>

                                            <div id="doctor_chamber_schedule_container" class="text-white my-3"></div>

                                            <div class="row align-items-end">
                                                <div class="col-md-6 input-group mb-3 mb-md-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Schedule Date</span>
                                                    </div>
                                                    <input name="scheduledate" class="form-control shadow-sm" type="date" min="<?= date("Y-m-d") ?>" value="<?= date("Y-m-d") ?>" title="Schedule Date" required>
                                                </div>

                                                <div class="col-md-6 input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Schedule</span>
                                                    </div>
                                                    <select name="wsno" class="form-control" title="Schedule time" required></select>
                                                </div>
                                            </div>

                                            <div class="text-center mt-5">
                                                <button id="proceed_buttom" type="button" class="btn medilife-btn">
                                                    Proceed <span>+</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="patient_information collapse">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text shadow-sm bg-white border-right-0">
                                                        +880
                                                    </span>
                                                </div>
                                                <input name="contactno" class="form-control shadow-sm border-left-0" type="tel" placeholder="Mobile number..." required>
                                                <div class="input-group-append">
                                                    <button id="people_filter_button" class="btn btn-primary ripple custom_shadow" type="button">
                                                        <i class="fas fa-search mr-2"></i> Search
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="d-sm-flex mb-3">
                                                <select name="faf" class="form-control shadow-sm mb-0 pl-1 pr-0" style="min-width: 135px;width: 180px;" required>
                                                    <option value="0">Self</option>
                                                    <option value="1">Family & Friend</option>
                                                </select>
                                                <input name="firstname" class="form-control shadow-sm mb-0" type="text" placeholder="First Name...">
                                                <input name="lastname" class="form-control shadow-sm mb-0" type="text" placeholder="Last Name...">
                                                <div class="people_div w-available" style="display: none;">
                                                    <select name="peopleno" class="form-control"></select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Date of Birth</span>
                                                    </div>
                                                    <input name="dob" class="form-control shadow-sm" type="date" max="<?= date("Y-m-d") ?>" required>
                                                </div>

                                                <div class="col-md-6 input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Age</span>
                                                    </div>
                                                    <input name="age" class="form-control shadow-sm" type="text" placeholder="Age...">
                                                </div>

                                                <div class="col-md-6 input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Gender</span>
                                                    </div>
                                                    <select name="gender" class="form-control shadow-sm">
                                                        <option value="">Select...</option>
                                                        <option value="Male" selected>Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Others">Others</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text shadow-sm">Blood Group</span>
                                                    </div>
                                                    <select name="bloodgroup" class="form-control shadow-sm">
                                                        <option value="">Select...</option>
                                                        <option value="A+">A+</option>
                                                        <option value="A-">A-</option>
                                                        <option value="B+">B+</option>
                                                        <option value="B-">B-</option>
                                                        <option value="AB+">AB+</option>
                                                        <option value="AB-">AB-</option>
                                                        <option value="O+">O+</option>
                                                        <option value="O-">O-</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text shadow-sm bg-white">Serial No</span>
                                                </div>
                                                <input name="localserial" class="form-control shadow-sm" type="number" placeholder="Serial No..." required readonly>
                                                <div class="input-group-append">
                                                    <button id="get_serialno_button" class="btn btn-primary ripple custom_shadow" type="button">
                                                        Get Serial No
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <button id="previous_buttom" type="button" class="btn btn-warning px-5 rounded-0 mr-2" style="height: 45px;">
                                                    Previous
                                                </button>
                                                <button type="submit" class="btn medilife-btn">
                                                    Make an Appointment <span>+</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="medilife-contact-info">
                                    <!-- Single Contact Info -->
                                    <!-- <div class="single-contact-info mb-30">
                                        <img src="<?= $imgPath ?>icons/alarm-clock.png" alt="">
                                        <p>Mon - Sat 08:00 - 21:00 <br>Sunday CLOSED</p>
                                    </div> -->
                                    <!-- Single Contact Info -->
                                    <!-- <div class="single-contact-info mb-30">
                                        <img src="<?= $imgPath ?>icons/envelope.png" alt="">
                                        <p>0080 673 729 766 <br>contact@business.com</p>
                                    </div> -->
                                    <!-- Single Contact Info -->
                                    <!-- <div class="single-contact-info">
                                        <img src="<?= $imgPath ?>icons/map-pin.png" alt="">
                                        <p>Lamas Str, no 14-18 <br>41770 Miami</p>
                                    </div> -->

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
                            <h6 class="section_subtitle">We always put our patients first</h6>
                            <p class="section_description">
                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing eli.
                            </p>
                            <a href="#" class="btn medilife-btn mt-50">View the services <span>+</span></a>
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
                        <!-- Single Service Area -->
                        <!-- <div class="col-12 col-sm-6">
                            <div class="single-service-area d-flex">
                                <div class="service-icon">
                                    <i class="icon-blood-donation-1"></i>
                                </div>
                                <div class="service-content">
                                    <h5>Baby Nursery</h5>
                                    <p>Dolor sit amet, consecte tuer elit, sed diam nonummy nibh euismod tincidunt ut ldolore magna.</p>
                                </div>
                            </div>
                        </div> -->
                        <!-- Single Service Area -->
                        <!-- <div class="col-12 col-sm-6">
                            <div class="single-service-area d-flex">
                                <div class="service-icon">
                                    <i class="icon-flask-2"></i>
                                </div>
                                <div class="service-content">
                                    <h5>Laboratory</h5>
                                    <p>Lorem ipsum dolor sit amet, consecte tuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut.</p>
                                </div>
                            </div>
                        </div> -->
                        <!-- Single Service Area -->
                        <!-- <div class="col-12 col-sm-6">
                            <div class="single-service-area d-flex">
                                <div class="service-icon">
                                    <i class="icon-emergency-call-1"></i>
                                </div>
                                <div class="service-content">
                                    <h5>Emergency Room</h5>
                                    <p>Dolor sit amet, consecte tuer elit, sed diam nonummy nibh euismod tincidunt ut ldolore magna.</p>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12 col-lg-8 section-padding-100-20">
                    <div class="home_services_row row"></div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="section_home_services medica-about-content sticky-top pb-4" style="padding-top: 90px;">
                        <div class="px-3 py-4" style="background-color: #fff0f1;">
                            <h2 class="section_title">Home Services</h2>
                            <h6 class="section_subtitle">We always put our pacients first</h6>
                            <p class="section_description">
                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer adipiscing eli.
                            </p>
                            <a href="#" class="btn medilife-btn mt-50">View the home services <span>+</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** About Us Area End ***** -->

    <hr>
    <section id="medicine_store_section" class="section-padding-100-20">
        <div class="container pb-5">
            <div class="row pb-5">
                <div class="col-lg-4">
                    <div class="section_pharmacy medica-about-content sticky-top">
                        <div class="px-3 py-4" style="background-color: #fefaeb;">
                            <h2 class="section_title">Medicine Stores</h2>
                            <h6 class="section_subtitle">Your One-Stop Online Medical Store</h6>
                            <p class="section_description">
                                Say goodbye to long queues and crowded aisles.
                            </p>
                            <a href="product_search.php" class="btn medilife-btn mt-50">
                                Order <span>+</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="my-3">
                        <select name="postcode" class="form-control shadow-sm"></select>
                    </div>

                    <div class="medicine_stores_row row"></div>

                    <div class="text-center">
                        <a href="product_search.php" class="btn medilife-btn mt-50">
                            Detail <span>+</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ***** Gallery Area Start ***** -->
    <div class="medilife-gallery-area owl-carousel">
        <!-- Single Gallery Item -->
        <!-- <div class="single-gallery-item">
            <img src="<?= $imgPath ?>bg-img/g1.jpg" alt="">
            <div class="view-more-btn">
                <a href="<?= $imgPath ?>bg-img/g1.jpg" class="btn gallery-img">See More +</a>
            </div>
        </div> -->
        <!-- Single Gallery Item -->
        <!-- <div class="single-gallery-item">
            <img src="<?= $imgPath ?>bg-img/g2.jpg" alt="">
            <div class="view-more-btn">
                <a href="<?= $imgPath ?>bg-img/g2.jpg" class="btn gallery-img">See More +</a>
            </div>
        </div> -->
        <!-- Single Gallery Item -->
        <!-- <div class="single-gallery-item">
            <img src="<?= $imgPath ?>bg-img/g3.jpg" alt="">
            <div class="view-more-btn">
                <a href="<?= $imgPath ?>bg-img/g3.jpg" class="btn gallery-img">See More +</a>
            </div>
        </div> -->

        <!-- Single Gallery Item -->
        <!-- <div class="single-gallery-item">
            <img src="<?= $imgPath ?>bg-img/g4.jpg" alt="">
            <div class="view-more-btn">
                <a href="<?= $imgPath ?>bg-img/g4.jpg" class="btn gallery-img">See More +</a>
            </div>
        </div> -->
    </div>
    <!-- ***** Gallery Area End ***** -->

    <!-- ***** Cool Facts Area Start ***** -->
    <section class="medilife-cool-facts-area section-padding-100-0">
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
            <div class="row">
                <div class="col-12 col-lg-4 text-lg-center mb-3">
                    <i class="icon-smartphone fa-10x text-white"></i>
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

    <script>
        const publicAccessUrl = `<?= $publicAccessUrl ?>`;
    </script>

    <?php include_once "footer_area.php"; ?>

    <script>
        const link = `https://www.google.com/maps/search/?api=1`;

        let postcodeSelect = $(`[name="postcode"]`)
            .select2({
                placeholder: "Select your postcode...",
                allowClear: true,
                width: `calc(100% - 0px)`,
                ajax: {
                    url: `${publicAccessUrl}php/ui/postoffice/get_filtered_postcodes_list.php`,
                    dataType: "json",
                    type: "POST",
                    data: function(params) {
                        return {
                            search_key: params.term,
                            pageno: params.page || 1,
                            limit: 20
                        };
                    },
                    processResults: function(data, params) {
                        params.pageno = params.page || 1;

                        $.each(data.results, (index, value) => {
                            value.id = value.postcode;
                            value.text = `[${value.postcode}] ${value.po}, ${value.ps}, ${value.districtname}`;
                        });

                        return data;
                    },
                    cache: false
                }
            })
            .on("select2:select", function(e) {
                let data = e.params.data;
                // console.log(data);
                localStorage.setItem("local_postcode", JSON.stringify(data));
                $(`[name="storeno"]`).val(null).trigger('change');
            });

        let localPostcode = JSON.parse(localStorage.getItem(`local_postcode`)) || {
            "id": 4000,
            "text": "[4000] Chittagong GPO, Chittagong Sadar, Chattogram",
            "postcode": 4000,
            "po": "Chittagong GPO",
            "ps": "Chittagong Sadar",
            "districtno": 40,
            "districtname": "Chattogram",
            "iscity": 0
        };

        if (postcodeSelect.find(`option[value="${localPostcode.postcode}"]`).length == 0) {
            postcodeSelect.append(new Option(`[${localPostcode.postcode}] ${localPostcode.po}, ${localPostcode.ps}, ${localPostcode.districtname}`, localPostcode.postcode, true, true));
        }
        postcodeSelect.val(localPostcode.postcode).trigger(`change`);

        pop_nearest_pharmacy();

        function pop_nearest_pharmacy() {
            $(`.medicine_stores_row`).empty();

            let json = {
                postcode: $(`[name="postcode"]`).val(),
                pageno: 1,
                limit: 6
            };

            $.post(`${publicAccessUrl}php/ui/api/pop_nearest_pharmacy.php`, json, resp => {
                if (resp.error) {
                    // toastr.error(resp.message);
                } else {
                    show_nearest_pharmacy(resp.results);
                }
            }, `json`);
        }

        function show_nearest_pharmacy(data) {
            let target = $(`.medicine_stores_row`);

            $.each(data, (index, value) => {
                let column = $(`<div class="col-md-6">
                            <div class="card shadow-3-strong mb-3">
								<div class="card-body">
									<h6 class="text-primary mb-0" style="text-transform: none;">${value.title || ``}</h6>
									<div class="small my-1">
                                        ${value.street && value.street.length ? `<span>${value.street}</span>` : ``}${value.postcode ? `, <span>[${value.postcode}] ${value.po}, ${value.ps}, ${value.districtname}</span>` : ``}${value.country && value.country.length ? `, <span>${value.country}</span>` : ``}
                                    </div>
									${value.loclat ? `<div>
											<a href="${link}&query=${value.loclat}%2C${value.loclon}" target="_blank" class="btn btn-warning btn-sm ripple custom_shadow" title="View Store In Map">
												<i class="fas fa-map-marked mr-2"></i> View In Map
											</a>
										</div>`
										: ``}
								</div>
							</div>
                        </div>`)
                    .appendTo(target);
            });
        }

        get_stats();

        function get_stats() {
            $.post(`${publicAccessUrl}php/ui/api/get_stats.php`, resp => {
                if (resp.error) {
                    // toastr.error(resp.message);
                } else {
                    show_stats(resp.results);
                }
            }, `json`);
        }

        function show_stats(data) {
            $(`.patient_qty`).html(data.patient_qty || 0);
            $(`.prescription_qty`).html(data.prescription_qty || 0);
            $(`.healthcenter_qty`).html(data.healthcenter_qty || 0);
            $(`.doctor_qty`).html(data.doctor_qty || 0);
            $(`.caregiver_qty`).html(data.caregiver_qty || 0);
            $(`.servicecall_qty`).html(data.servicecall_qty || 0);
            $(`.store_qty`).html(data.store_qty || 0);
            $(`.medicine_qty`).html(data.medicine_qty || 0);

            // $('.counter').counterUp({
            //     delay: 10,
            //     time: 2000
            // });
        }

        get_gallery();

        function get_gallery() {
            $(`.medilife-gallery-area`).empty();

            let json = {};

            $.post(`${publicAccessUrl}php/ui/api/get_gallery.php`, json, resp => {
                if (resp.error) {
                    // toastr.error(resp.message);
                } else {
                    show_gallery(resp.data);
                }
            }, `json`);
        }

        function show_gallery(data) {
            let target = $(`.medilife-gallery-area`);

            $.each(data, (index, value) => {
                let item = $(`<div class="${value.catno == 1 ? `` : `single-gallery-item`}">
                        ${value.catno == 2
                            ? `<img src="${value.thumbnailimageurl || (value.imageurl || ``)}" alt="${value.image_title}">
                                <div class="view-more-btn">
                                    <a href="${value.imageurl}" class="btn gallery-img">See More +</a>
                                </div>`
                            : `<iframe src="${value.imageurl}" title="${value.image_title}" style="width:100%;" frameborder="0" allowfullscreen></iframe>`
                        }
                    </div>`)
                    .appendTo(target);
            });

            let setIntervalID = setInterval(() => {
                let height = $(`.single-gallery-item:first`, target).height();
                if (height > 0) {
                    $(`iframe`, target).height(height);
                    clearInterval(setIntervalID);
                }
            }, 1000);

            if ($.fn.owlCarousel) {
                target.owlCarousel({
                    items: 4,
                    margin: 0,
                    loop: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    smartSpeed: 2000,
                    responsive: {
                        0: {
                            items: 1
                        },
                        768: {
                            items: 2
                        },
                        992: {
                            items: 3
                        },
                        1200: {
                            items: 4
                        }
                    }
                });

                $("[data-delay]").each(function() {
                    var anim_del = $(this).data('delay');
                    $(this).css('animation-delay', anim_del);
                });

                $("[data-duration]").each(function() {
                    var anim_dur = $(this).data('duration');
                    $(this).css('animation-duration', anim_dur);
                });
            }

            if ($.fn.magnificPopup) {
                $('.gallery-img').magnificPopup({
                    type: 'image'
                });
                $('.popup-video').magnificPopup({
                    disableOn: 700,
                    type: 'iframe',
                    mainClass: 'mfp-fade',
                    removalDelay: 160,
                    preloader: false,
                    fixedContentPos: false
                });
            }

        }

        $.get(`${publicAccessUrl}themes/default/index.json`, data => {
            show_data(data);
        }, `json`);

        function show_data(data) {
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

            let sections = data.sections;
            if (!sections) {
                return;
            }

            if (sections.appointment && sections.appointment.title && sections.appointment.title.length) {
                let section = $(`.section_appointment`);
                let appointment = sections.appointment;

                $(`.section_title`, section).html(appointment.title || ``);
                $(`.section_subtitle`, section).html(appointment[`sub-title`] || ``);
                $(`.section_description`, section).html(appointment.description || ``);
            }

            if (sections.service && sections.service.title && sections.service.title.length) {
                $(`.services_row`).empty();
                show_service_data(sections.service, `.section_services`, `.services_row`);
            }

            if (sections[`home-service`] && sections[`home-service`].title && sections[`home-service`].title.length) {
                $(`.home_services_row`).empty();
                show_service_data(sections[`home-service`], `.section_home_services`, `.home_services_row`);
            }

            if (sections.pharmacy && sections.pharmacy.title && sections.pharmacy.title.length) {
                let section = $(`.section_pharmacy`);
                let pharmacy = sections.pharmacy;

                $(`.section_title`, section).html(pharmacy.title || ``);
                $(`.section_subtitle`, section).html(pharmacy[`sub-title`] || ``);
                $(`.section_description`, section).html(pharmacy.description || ``);
            }
        }

        function show_hero_carousel_data(data) {
            let target = $(`.hero-slides`);

            $.each(data, (index, value) => {
                let slide = $(`<div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(${value.imageurl || ``});">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-12">
                                    <div class="hero-slides-content">
                                        <h2 data-animation="fadeInUp" data-delay="100ms">${value.title || ``}</h2>
                                        <h3 data-animation="fadeInUp" data-delay="200ms">${value[`sub-title`] || ``}</h3>
                                        <h6 data-animation="fadeInUp" data-delay="400ms">${value[`subsub-title`] || ``}</h6>
                                        <a href="#" class="btn medilife-btn mt-50" data-animation="fadeInUp" data-delay="700ms">${value[`extra-button`] || ``}</a>
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

        const EPOCH = new Date(0);
        const EPOCH_YEAR = EPOCH.getUTCFullYear();
        const EPOCH_MONTH = EPOCH.getUTCMonth();
        const EPOCH_DAY = EPOCH.getUTCDate();

        const calculateAge = (birthDate, toDate = new Date()) => {
            const diff = new Date((new Date(toDate)).getTime() - (new Date(birthDate)).getTime());
            const age = {
                years: Math.abs(diff.getUTCFullYear() - EPOCH_YEAR),
                months: Math.abs(diff.getUTCMonth() - EPOCH_MONTH),
                days: Math.abs(diff.getUTCDate() - EPOCH_DAY)
            };

            return age.years > 0 ?
                `${age.years} Years` :
                (age.months > 0 ?
                    `${age.months} Months` :
                    (age.days > 0 ?
                        `${age.days} Days` : ``));
        };

        function formatTime(timeString = "00:00:00") {
            if (!timeString || !timeString.length) {
                return ``;
            }

            let H = +timeString.substr(0, 2);
            let h = H % 12 || 12;
            let ampm = (H < 12 || H === 24) ? "AM" : "PM";
            return h + timeString.substr(2, 3) + ampm;
        }

        get_all_specialtycategory();

        function get_all_specialtycategory() {
            $.post(`${publicAccessUrl}php/ui/api/get_all_specialtycategory.php`, resp => {
                if (resp.error) {
                    // toastr.error(resp.message);
                } else {
                    show_specialtycategory(resp.results);
                }
            }, `json`);
        }

        function show_specialtycategory(data) {
            $.each(data, (index, value) => {
                value.id = value.spno;
                value.text = value.specialty;
            });

            $(`#patient_appointment_form [name="spno"]`)
                .select2({
                    placeholder: "Select Specialty...",
                    allowClear: true,
                    width: `calc(100% - 0px)`,
                    data
                })
                .val(null)
                .trigger('change')
                .on("select2:select", function(e) {
                    let data = e.params.data;
                    // console.log(data);
                    $(`[name="doctno"]`).val(null).trigger("change");
                });
        }

        const doctorSelect2Settings = {
            placeholder: "Select doctor",
            allowClear: true,
            width: "calc(100% - 0px)",
            ajax: {
                url: `${publicAccessUrl}php/ui/api/pop_doctors.php`,
                dataType: `json`,
                type: "POST",
                data: function(params) {
                    return {
                        search_key: params.term,
                        pageno: params.page || 1,
                        limit: 20,
                        spno: $(`#patient_appointment_form [name="spno"]`).val()
                    };
                },
                processResults: function(data, params) {
                    params.pageno = params.page || 1;

                    $.each(data.results, (index, value) => {
                        value.id = value.doctno;
                        value.text = `${value.firstname} ${value.lastname || ``}`;
                    });

                    return data;
                },
                cache: false
            },
            templateResult: (value) => {
                if (!value.id) {
                    return value.text;
                }

                return $(`<div class="">
                            <div class="font-weight-bold">${value.firstname} ${value.lastname || ``}</div>
                            ${value.specialty && value.specialty.length ? `<div>${value.specialty}</div>` : ``}
                            <div>${value.countrycode} ${value.contactno}</div>
                        </div>`);
            },
            templateSelection: (value) => value.id ? value.text : "Select doctor"
        };

        $(`[name="doctno"]`)
            .select2(doctorSelect2Settings)
            .val(null)
            .trigger("change")
            .on("select2:select", function(e) {
                let data = e.params.data;
                // console.log(data);
                $(`#doctor_chamber_schedule_container`).empty();
                $(`#patient_appointment_form`).data(`doctno`, data.doctno);
                get_doctorchambers({
                    doctno: data.doctno
                });
            });

        function get_doctorchambers(json) {
            $.post(`${publicAccessUrl}healthcare/php/ui/chamber/get_doctorchambers.php`, json, resp => {
                if (resp.error) {
                    toastr.error(resp.message);
                } else {
                    show_doctor_chamber_schedule(resp.results);
                }
            }, `json`);
        }

        function show_doctor_chamber_schedule(data) {
            $.each(data, (index, value) => {
                let address = ``;
                if (value.street && value.street.length) {
                    address += value.street;
                }
                if (value.postcode && value.postcode.length) {
                    if (address.length) {
                        address += `, `;
                    }
                    address += value.postcode;
                }
                if (value.country && value.country.length) {
                    if (address.length) {
                        address += `, `;
                    }
                    address += value.country;
                }

                let template = $(`<div class="custom-radio custom-control custom-control-inline">
							<input id="chamber_${value.chamberno}_radio" type="radio" name="chamberno" value="${value.chamberno}" class="custom-control-input" required>
							<label class="custom-control-label" for="chamber_${value.chamberno}_radio">
								<div>${value.chambername} (${value.countrycode} ${value.contacts})</div>
								${address.length ? `<div class="small">${address}</div>` : ``}
							</label>
						</div>`)
                    .appendTo(`#doctor_chamber_schedule_container`);

                $(`[name="chamberno"]`, template).data(value);
            });
        }

        $(document).on(`change`, `#patient_appointment_form [name="chamberno"]`, function(e) {
            let form = $(`#patient_appointment_form`);
            let scheduleSelect = $(`[name="wsno"]`, form).empty();

            let chamberData = $(this).data();

            $.each(chamberData.schedule, (indexInSchedule, schedule) => {
                $(`<option value="${schedule.wsno}">
							${schedule.weekday} [${formatTime(schedule.chambertimestart)} - ${formatTime(schedule.chambertimeend)}]
						</option>`)
                    .data(schedule)
                    .appendTo(scheduleSelect);
            });

            if (this.checked) {
                form.data(`chamberno`, this.value).data(`schedule`, chamberData.schedule);
            } else {
                form.data(`schedule`, []);
            }
        });

        $(`#proceed_buttom`).click(function(e) {
            let form = $(`#patient_appointment_form`);

            let doctno = $(`[name="doctno"]`, form).val(),
                chamberno = $(`[name="chamberno"]:checked`, form).val(),
                scheduledate = $(`[name="scheduledate"]`, form).val(),
                wsno = $(`[name="wsno"]`, form).val();

            console.log({
                doctno,
                chamberno,
                scheduledate,
                wsno
            });

            if (!doctno && doctno <= 0) {
                toastr.error(`You have to select a doctor!`);
                return;
            }

            if (!chamberno && chamberno <= 0) {
                toastr.error(`You have to select a chamber!`);
                return;
            }

            if (!scheduledate && scheduledate.length <= 0) {
                toastr.error(`You have to select a schedule date!`);
                return;
            }

            if (!wsno && wsno <= 0) {
                toastr.error(`You have to select a doctor's schedule!`);
                return;
            }

            $(`.doctor_and_schedule.collapse`).collapse(`hide`);
            $(`.patient_information.collapse`).collapse(`show`);
        });

        $(`#previous_buttom`).click(function(e) {
            $(`.patient_information.collapse`).collapse(`hide`);
            $(`.doctor_and_schedule.collapse`).collapse(`show`);
        });

        $(`#people_filter_button`).click(function(e) {
            e.preventDefault();
            let form = $(`#patient_appointment_form`);
            $(`[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`, form).val(``).prop(`disabled`, false);

            let json = {
                contactno: $(`[name="contactno"]`, form).val()
            };

            if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
                json.contactno = json.contactno.substring(1);
            } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {

            } else {
                toastr.error(`Invalid mobile no!`);
                return
            }

            $.post(`${publicAccessUrl}php/ui/api/is_exist_people.php`, json, resp => {
                if (resp.error) {
                    toastr.error(resp.message);
                }

                if (resp.result) {
                    $(`#people_filter_button`).data(`people_data`, resp.result);
                    $(`[name="faf"]`, form).val(0).prop(`disabled`, false);
                    show_existing_people_data(resp.result);
                } else {
                    $(`[name="faf"]`, form).val(0).prop(`disabled`, true);
                }
            }, `json`);
        });

        function show_existing_people_data(data) {
            let form = $(`#patient_appointment_form`).data(`people_data`, data);

            $(`[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`, form).each((index, elem) => {
                let elemName = $(elem).attr(`name`);

                if (data.hasOwnProperty(elemName) && data[elemName]) {
                    $(elem).val(data[elemName]).prop(`disabled`, true);
                }
            });

            $(`[name="age"]`, form).val(calculateAge(data.dob));
        }

        $(`#patient_appointment_form [name="faf"]`).change(function(e) {
            let form = $(`#patient_appointment_form`);
            $(`.people_div`, form).hide();
            $(`[name="peopleno"]`, form).val(null).trigger("change");
            $(`[name="firstname"],[name="lastname"]`, form).show();

            let people_data = form.data(`people_data`);
            if (!people_data) {
                return;
            }

            if (this.value != `0`) {
                $(`[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`, form).val(``).prop(`disabled`, false);

                let json = {
                    contactno: people_data.contactno
                };

                let fafs = $(this).data(`fafs_of_${json.contactno}`);
                if (fafs && fafs.length) {
                    show_fafs(fafs);
                } else {
                    get_fafs(json);
                }
            } else {
                people_data = $(`#people_filter_button`).data(`people_data`);
                show_existing_people_data(people_data);
            }
        });

        function get_fafs(json) {
            if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
                json.contactno = json.contactno.substring(1);
            } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {

            } else {
                toastr.error(`Invalid mobile no!`);
                return
            }

            $.post(`${publicAccessUrl}php/ui/api/get_fafs.php`, json, resp => {
                if (resp.error) {
                    toastr.error(resp.message);
                } else {
                    $(`#patient_appointment_form [name="faf"]`).data(`fafs_of_${json.contactno}`, resp.result);
                    show_fafs(resp.result);
                }
            }, `json`);
        }

        function show_fafs(data) {
            let form = $(`#patient_appointment_form`);
            $(`[name="firstname"],[name="lastname"]`, form).hide();
            $(`.people_div`, form).show();

            $.each(data, (index, value) => {
                value.id = value.peopleno;
                value.text = `${value.firstname} ${value.lastname}`;
            });

            $(`[name="peopleno"]`, form)
                .select2({
                    placeholder: "Select family & friend",
                    allowClear: true,
                    width: "calc(100% - 0px)",
                    tags: true,
                    data
                })
                .val(null)
                .trigger("change")
                .on("select2:select", function(e) {
                    let data = e.params.data;
                    // console.log(data);
                    if (data.peopleno > 0) {
                        show_existing_people_data(data);
                    } else {
                        $(`[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`, form).val(``).prop(`disabled`, false);
                    }
                });
        }

        $(`#patient_appointment_form [name="dob"]`).on(`input`, function(e) {
            $(`#patient_appointment_form [name="age"]`).val(calculateAge(this.value));
        });

        $(`#patient_appointment_form [name="scheduledate"]`).on(`input`, function(e) {
            let form = $(`#patient_appointment_form`);
            let scheduleSelect = $(`[name="wsno"]`, form);
            let data = $(form).data();
            let schedule = data.schedule.filter(a => a.doctno == data.doctno);

            let weekday = ((new Date(this.value)).toLocaleDateString("en-us", {
                weekday: 'short'
            })).toUpperCase();

            $(`option`, scheduleSelect).each((index, elem) => {
                if ($(elem).data(`weekday`) != weekday && !$(elem).hasClass(`d-none`)) {
                    $(elem).addClass(`d-none`);
                } else if ($(elem).data(`weekday`) == weekday && $(elem).hasClass(`d-none`)) {
                    $(elem).removeClass(`d-none`);
                }
            });

            if ($(`option:not(.d-none)`, scheduleSelect).length) {
                scheduleSelect.val($(`option:not(.d-none):first`, scheduleSelect).val());
                $(this).removeClass(`border-danger`);
            } else {
                scheduleSelect.val(``);
                $(this).addClass(`border-danger`);
                toastr.error(`No schedule available on '${weekday}' for this doctor. Only available for ${[...new Set(schedule.map(a => `'${a.weekday}'`))].join(`, `)}.`);
            }
        });

        $(`#get_serialno_button`).click(function(e) {
            let form = $(`#patient_appointment_form`);
            let data = $(form).data();

            let scheduledate = $(`[name="scheduledate"]`, form).val();
            if (!scheduledate.length) {
                toastr.error(`Schedule date not set properly.`);
                return;
            }

            let chambertimestart = $(`[name="wsno"] option:selected`, form).data(`chambertimestart`);
            if (!chambertimestart.length) {
                toastr.error(`Schedule not set properly.`);
                return;
            }

            let json = {
                doctno: data.doctno,
                chamberno: data.chamberno,
                scheduletime: `${scheduledate} ${chambertimestart}`,
            };

            $.post(`${publicAccessUrl}healthcare/php/ui/appointment/get_serialno.php`, json, resp => {
                $(`[name="localserial"]`, form).val((Number(resp.localserial) + 1) || 1);
            }, `json`);
        });

        $(`#patient_appointment_form`).submit(function(e) {
            e.preventDefault();

            let data = $(this).data();
            // console.log(`data =>`, data);

            let json = {
                doctno: data.doctno,
                chamberno: data.chamberno,
            };

            $(`[name]`, this).each((i, elem) => {
                let elementName = $(elem).attr("name");
                if (elementName != `chamberno`) {
                    json[elementName] = $(elem).val();
                }
            });

            let chambertimestart = $(`[name="wsno"] option:selected`, this).data(`chambertimestart`);
            if (!chambertimestart.length) {
                toastr.error(`Schedule not set properly.`);
                return;
            }

            json.scheduletime = `${json.scheduledate} ${chambertimestart}`;

            if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
                json.contactno = json.contactno.substring(1);
            } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {

            } else {
                toastr.error(`Invalid mobile no!`);
                return
            }

            if (data.people_data) {
                json.peopleno = data.people_data.peopleno;

                if (json.faf != 0) {
                    json.faf_parentpeopleno = json.peopleno;

                    let peopleno = $(`[name="peopleno"]`, this).val();

                    if (peopleno && peopleno.length) {
                        if (!isNaN(peopleno) && peopleno > 0) {
                            json.peopleno = peopleno;
                        } else if (isNaN(peopleno)) {

                            let nameArr = peopleno.trim().split(` `);
                            if (nameArr.length > 1) {
                                json.firstname = nameArr.slice(0, -1).join(` `);
                                json.lastname = nameArr.slice(-1)[0];
                            } else {
                                json.firstname = nameArr[0];
                            }

                            delete json.peopleno;
                        }
                    } else {
                        delete json.peopleno;
                    }
                }
            } else {
                delete json.peopleno;
            }

            if (!json.firstname.length) {
                toastr.error(`Patient name is required!`);
                $(`[name="firstname"]`, this).focus();
                return;
            }

            if (!json.localserial.length) {
                toastr.error(`Patient serial no is required!`);
                $(`[name="localserial"]`, this).focus();
                return;
            }

            delete json.age;
            delete json.wsno;
            delete json.scheduledate;

            // console.log(`json =>`, json);

            $(`:submit`, this)
                .prop("disabled", true)
                .html(`<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`);

            $.post(`${publicAccessUrl}healthcare/php/ui/appointment/add_appointment.php`, json, resp => {
                    if (resp.error) {
                        toastr.error(resp.message);
                    } else {
                        toastr.success(resp.message);
                        reset_appointment_form();
                        // location.reload();
                    }
                }, `json`)
                .always(() => {
                    $(`:submit`, this).prop("disabled", false).html(`Make an Appointment <span>+</span>`);
                });
        });

        function reset_appointment_form() {
            let form = $(`#patient_appointment_form`).trigger(`reset`).data({});

            $(`[name="spno"]`, form).val(null).trigger(`change`);
            $(`[name="doctno"]`, form).val(null).trigger(`change`);
            $(`#doctor_chamber_schedule_container`).empty();
            $(`[name="wsno"]`, form).empty();

            $(`[name="peopleno"]`, form).val(null).trigger(`change`);
            $(`.people_div`, form).hide();
            $(`[name="firstname"],[name="lastname"]`, form).show();
            $(`[name="faf"]`, form).data({});

            $(`.patient_information.collapse`).collapse(`hide`);
            $(`.doctor_and_schedule.collapse`).collapse(`show`);
        }
    </script>

    <?php
    // require_once $basePath . "/find_doctor.php";
    ?>

</body>

</html>