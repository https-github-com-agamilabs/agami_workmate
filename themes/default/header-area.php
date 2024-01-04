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

<script src='//www.google.com/recaptcha/api.js?render=6Ld1TUUpAAAAAPfUZlFrc5Hsf-TTGRVDP9wFeMbt'></script>

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

    #login_form [name],
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
</style>

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
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-google-plus-g" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-pinterest-p px-1" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light"><i class="fab fa-facebook-f px-1" aria-hidden="true"></i></a>
                                <a href="#" class="btn-sm btn-light mr-3"><i class="fab fa-twitter" aria-hidden="true"></i></a>

                                <select name="lang" title="Language" style="padding: 2px 0 4px;">
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

                                    <div class="input-group col-sm-3 col-md-6">
                                        <input type="text" class="search-query form-control" placeholder="<?= $langHeaderData[$lang]['lang_Search_doctor_by_name,_specialty_or_symptoms']; ?>" />
                                        <span class="input-group-append">
                                            <button class="btn btn-danger" type="button">
                                                <span class="fa fa-search"></span>
                                            </button>
                                        </span>
                                    </div>
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
<script src="js/login.js"></script>

<script>
    let langSelect = document.getElementsByName(`lang`)[0];
    langSelect.value = `<?= $_SESSION["lang"] ?>`;

    document.getElementById('login_form').addEventListener('submit', function(e) {
        e.preventDefault();
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;

        if (username.length < 3) {
            alert("Username should be at least 3 characters long!");
            return;
        }

        if (password.length < 6) {
            alert("Password should be at least 6 characters long!");
            return;
        }

        complete_login(username, password);
    });

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

    $(function() {
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

    function complete_login(username, password) {
        // needs for recaptacha ready
        grecaptcha.ready(function() {
            // do request for recaptcha token
            // response is promise with passed token
            grecaptcha.execute('6LfqIcoaAAAAAKD9ytBZCLxdPPcW4EhMNPAmbd0P', {
                action: 'employee_login'
            }).then(function(token) {
                // add token to form
                var action = "employee_login";

                console.log({
                    captchatoken: token,
                    action: action,
                    username: username,
                    password: password
                });

                $.ajax({
                    url: "php/ui/login/login.php",
                    type: 'POST',
                    data: {
                        captchatoken: token,
                        action: action,
                        username: username,
                        password: password
                    },
                    success: (result) => {
                        console.log('login result=>', result);
                        let resp = JSON.parse(result);
                        console.log('login resp=>', resp);
                        if (resp.error) {
                            toastr.error(resp.message);
                            console.log(resp.message);
                            alert(resp.message);
                        } else {
                            // toastr.success(resp.message);
                            window.location.href = (resp.ucatno == 5) ? "dashboard.php" : "time_keeper.php";
                        }
                    }
                });

            });;
        });
    }

</script>