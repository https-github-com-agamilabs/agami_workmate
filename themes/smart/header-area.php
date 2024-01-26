<?php
// if (isset($_GET['lang'])) {
//     $_SESSION["lang"] = $_GET['lang'];
// } else if (!isset($_SESSION["lang"])) {
//     $_SESSION["lang"] = "en";
// }

//require 'dependancy_checker.php';

date_default_timezone_set("Asia/Dhaka");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$default_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
$lang = $default_lang;
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}

require_once dirname(dirname(dirname(__FILE__))) . "/lang_converter/converter.php";
// $jasonFilePath = dirname(dirname(dirname(__FILE__))).'/lang-json/' . $lang . '/nav.json';
if (!isset($arrayData)) {
    $arrayData = array();
}
$arrayData = array_merge($arrayData, langConverter($lang, 'nav'));
//print_r($arrayData);
//echo $arrayData['lang_about_us'];

?>

<script>
    console.log(<?= json_encode($arrayData) ?>);
</script>

<style>
    .form-control-sm {
        height: calc(1.8125rem + 2px);
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    .grecaptcha-badge {
        visibility: hidden;
    }

    .login_form [name],
    #signup_modal [name] {
        margin-bottom: 0;
        color: #000;
    }

    label>i.toggle_password {
        display: none;
        cursor: pointer;
    }

    label:focus>i.toggle_password,
    label:hover>i.toggle_password {
        display: inline-block;
    }

    @media only screen and (max-width: 991.98px) {
        .top-header-area {
            height: 70px;
        }

        .main-header-area {
            height: auto;
        }
    }
</style>

<!-- ***** Header Area Start ***** -->
<header class="header-area">
    <!-- Top Header Area -->
    <div class="top-header-area" style='z-index: 999999;'>
        <div id="top_header_area" class="container-fluid" style="padding: 7px 0; background-color: #081f3e;">
            <div class="row w-100 px-0 mx-0">
                <div class="col-10 col-lg-12 w-100 px-0">
                    <div class="w-100 d-flex flex-wrap justify-content-around justify-content-lg-around align-items-center">
                        <a class="navbar-brand d-lg-none py-0" href="index.php">
                            <img src="<?= $publicAccessUrl . $response['orglogourl']; ?>" style="height:50px;" alt="Logo">
                        </a>
                        <div class="text-center text-md-left d-none d-md-block">
                            <p id="nav_title">
                                <?= $arrayData['lang_welcome_to']; ?>
                                <span><?= $arrayData['lang_holistic_online_health_care_system']; ?> </span>
                            </p>
                        </div>
                        <div class="text-center text-md-left">
                            <div>
                                <!-- <a href="#" class="btn-sm btn-light"><i class="fab fa-google-plus-g" aria-hidden="true"></i></a> -->
                                <a href="https://youtube.com/@HiWorkmate" class="btn-sm btn-light"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-facebook-f px-1" aria-hidden="true"></i></a>
                                <a href="https://twitter.com/agamismartacc" class="btn-sm btn-light mr-sm-3"><i class="fab fa-twitter" aria-hidden="true"></i></a>

                                <select name="lang" title="Language" style="padding: 2px 0 4px;">
                                    <option value="en">EN</option>
                                    <option value="bn">BN</option>
                                </select>

                                <!-- <div class="d-inline-block mt-2 mt-sm-0">
                                    <a href="< ?= $publicAccessUrl ?>login.php" class="btn-sm btn-light text-nowrap">
                                        <i class="far fa-user-circle mr-1"></i>< ?= $arrayData['lang_login/register']; ?>
                                    </a>
                                </div> -->
                                <div class="dropdown d-inline-block1 d-none">
                                    <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle btn btn-light btn-sm shadow-sm rounded-0" style="padding: 2px 4px;">
                                        <i class="far fa-user-circle mr-1"></i><?= $arrayData['lang_login/register']; ?>
                                    </button>
                                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right" style="width: 320px;">
                                        <form class="login_form px-2">
                                            <div class="mb-2">
                                                <input name="username" type="text" class="form-control shadow-sm" minlength="3" autocomplete="off" placeholder="<?= $arrayData['lang_enter_your_username']; ?>" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="d-block mb-0">
                                                    <input name="password" class="form-control shadow-sm" type="password" minlength="6" autocomplete="off" placeholder="<?= $arrayData['lang_enter_your_password']; ?>" required>
                                                </label>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary btn-block ripple font-size-lg custom_shadow">Log in</button>
                                            </div>
                                        </form>

                                        <div class="text-center px-2 my-3">
                                            <a href="javascript:void(0);" class="forgotten_password"><?= $arrayData['lang_forgotten_password?']; ?></a>
                                        </div>

                                        <hr>

                                        <div class="text-center px-2">
                                            <button type="button" class="create_new_account_button btn btn-success ripple font-size-lg custom_shadow"><?= $arrayData['lang_create_new_account']; ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-2 col-lg-0 d-block d-lg-none align-self-center text-center">
                    <button class="navbar-toggler my-0 p-0" type="button" data-toggle="collapse" data-target="#medilifeMenu" aria-controls="medilifeMenu" aria-expanded="false" aria-label="Explore all menus">
                        <span class="navbar-toggler-icon"></span>
                    </button>
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
                                <a class="navbar-brand d-none d-lg-block" href="index.php">
                                    <img src="<?= $publicAccessUrl . $response['orglogourl']; ?>" style="height:60px;" alt="Logo">
                                </a>

                                <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#medilifeMenu" aria-controls="medilifeMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button> -->

                                <div class="collapse navbar-collapse" id="medilifeMenu">
                                    <!-- Menu Area -->
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item active">
                                            <a class="nav-link" href="index.php"><?= $arrayData['lang_home']; ?> <span class="sr-only">(current)</span></a>
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
                                            <a class="nav-link" href="#about_section"><?= $arrayData['lang_about']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#features_section"><?= $arrayData['lang_features']; ?></a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" href="#medicine_store_section">< ?= $arrayData['lang_pharmacy']; ?></a>
                                        </li> -->
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?= $publicAccessUrl ?>blog/index.php"><?= $arrayData['lang_blog']; ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#footer_area"><?= $arrayData['lang_contact']; ?></a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ***** Header Area End ***** -->

<script>
    let langSelect = document.getElementsByName(`lang`)[0];
    langSelect.value = `<?= $_SESSION["lang"] ?>`;

    langSelect.addEventListener(`change`, function(e) {
        if (location.search.length) {
            if (location.href.lastIndexOf(`lang`) >= 0) {
                let start = location.href.lastIndexOf(`lang`) + 5,
                    end = start + 2;
                let lang = location.href.substring(start, end);

                location.href = location.href.replace(lang, this.value);
            } else {
                location.href = `${location.href}&lang=${this.value}`;
            }
        } else {
            location.href = `${location.href}?lang=${this.value}`;
        }
    });

    $(document).ready(function() {
        let splits = location.pathname.split(`/`),
            fileName = splits[Math.max(splits.length - 1, 0)];

        if (fileName && fileName.length) {
            let navItem = $(`#medilifeMenu [href*="${fileName}"]:first`).parents(`.nav-item`);
            $(`#medilifeMenu .nav-item.active`).removeClass(`active`);
            navItem.addClass(`active`);
        }

        $(`#medilifeMenu [href^="#"]`).click(function(e) {
            e.preventDefault();

            const hash = e.target.getAttribute('href');
            const target = document.getElementById(hash.substring(1));

            window.scroll({
                top: target.offsetTop - 100,
                behavior: 'smooth'
            });

            if (history.pushState) {
                history.pushState(null, null, hash);
            } else {
                location.hash = hash;
            }
        });
    });
</script>