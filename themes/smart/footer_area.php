<!-- ***** Footer Area Start ***** -->

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

// $jasonFilePath = './lang-json/' . $lang . '/about.json';
if (!isset($langData)) {
    $langData = array();
}
$langData = array_merge($langData, langConverter($lang, 'about'));
//print_r($langData);
//echo $langData['lang_about_us'];

?>


<footer id="footer_area" class="footer-area section-padding-100">
    <!-- Main Footer Area -->
    <div class="main-footer-area">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="footer-widget-area">
                        <div class="footer-logo">
                            <img src="<?= $publicAccessUrl . $response['orglogourl']; ?>" style="height:45px;" alt="">
                        </div>
                        <p id="footer_about">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer.</p>
                        <div class="footer-social-info">
                            <!-- <a href="#"><i class="fab fa-google-plus-g" aria-hidden="true"></i></a> -->
                            <a href="#"><i class="fab fa-pinterest-p" aria-hidden="true"></i></a>
                            <a href="#"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                            <a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="footer-widget-area">
                        <div class="widget-title">
                            <h6><?= $langData['lang_latest_blog_post']; ?></h6>
                        </div>
                        <div class="blog_post_container widget-blog-post">
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="https://smartacc.agamilabs.com/assets/image/background/bg01.webp" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#"><?= $langData['lang_how_multi_level_hiworkmate_works?']; ?></a>
                                    <p>Jan 04, 2023</p>
                                </div>
                            </div>
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="https://smartacc.agamilabs.com/assets/image/background/bg02.webp" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#"><?= $langData['lang_how_can_i_get_trial_balance?']; ?></a>
                                    <p>Dec 03, 2022</p>
                                </div>
                            </div>
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="https://smartacc.agamilabs.com/assets/image/background/bg01.webp" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#"><?= $langData['lang_how_to_activate_hiworkmate_year?']; ?></a>
                                    <p>Nov 02, 2022</p>
                                </div>
                            </div>
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="https://smartacc.agamilabs.com/assets/image/background/bg02.webp" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#"><?= $langData['lang_how_to_open_new_hiworkmate_year?']; ?></a>
                                    <p>Oct 01, 2022</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-widget-area">
                        <div class="widget-title d-none">
                            <h6><?= $langData['lang_subscribe_blog']; ?> </h6>
                        </div>

                        <div class="footer-newsletter-area">
                            <form id="newsletter_form">
                                <input name="email" type="email" class="form-control border-0 mb-0" placeholder="<?= $langData['lang_your_email_here']; ?>" required>
                                <button type="submit"><?= $langData['lang_subscribe']; ?></button>
                                <div class="d-flex justify-content-between">
                                    <p id="footer_subscription">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                                    <!-- <a id="unsubscribe_anchor" href="javascript:void(0);" class="text-muted" style="font-size: 12px;padding: 0 21.5px;">
                                        <u><?php //$langData['lang_unsubscribe'];
                                            ?></u>
                                    </a> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                    <div id="contact_form_div" class="footer-widget-area">
                        <div class="widget-title">
                            <h6><?= $langData['lang_contact_form']; ?></h6>
                        </div>
                        <div class="footer-contact-form">
                            <form action="#" method="post">
                                <input type="text" class="form-control border-top-0 border-right-0 border-left-0" name="footer-name" id="footer-name" placeholder="<?= $langData['lang_name']; ?>">
                                <input type="email" class="form-control border-top-0 border-right-0 border-left-0" name="footer-email" id="footer-email" placeholder="<?= $langData['lang_email']; ?>">
                                <textarea name="message" class="form-control border-top-0 border-right-0 border-left-0" id="footerMessage" placeholder="<?= $langData['lang_message']; ?>"></textarea>
                                <button type="submit" class="btn medilife-btn"><?= $langData['lang_contact_us']; ?> <span>+</span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer Area -->
    <div class="bottom-footer-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="bottom-footer-content">
                        <!-- Copywrite Text -->
                        <div class="copywrite-text">
                            <p>
                                <?= $langData['lang_copyright']; ?> &copy;<script>
                                    document.write(new Date().getFullYear());
                                </script><?= $langData['lang_all_rights_reserved']; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ***** Footer Area End ***** -->

<script>
    function padZero(value) {
        return value < 10 ? `0${value}` : `${value}`;
    }

    function formatDateTime(dateTime) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        let date = new Date(dateTime);
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        let strTime = hours + ':' + minutes + ' ' + ampm;
        return padZero(date.getDate()) + " " + months[date.getMonth()] + " " + date.getFullYear();
    }

    // get_limited_blogarticles();

    function get_limited_blogarticles() {
        $(`.blog_post_container`).empty();

        let json = {
            limit: 3
        };

        $.post(`${publicAccessUrl}php/ui/api/get_limited_blogarticles.php`, json, resp => {
            if (resp.error) {
                // toastr.error(resp.message);
            } else {
                show_limited_blogarticles(resp.data);
            }
        }, `json`);
    }

    function show_limited_blogarticles(data) {
        let target = $(`.blog_post_container`);

        $.each(data, (index, value) => {
            let column = $(` <div class="widget-single-blog-post d-flex">
                        <div class="widget-post-thumbnail">
                            <img src="${value.cover}" alt="">
                        </div>
                        <div class="widget-post-content">
                            <a href="#">${value.title}</a>
                            <p>${formatDateTime(value.posttime)}</p>
                        </div>
                    </div>`)
                .appendTo(target);
        });
    }

    $(`#newsletter_form`).submit(function(e) {
        e.preventDefault();
        let json = Object.fromEntries((new FormData(this)).entries());

        $.post(`${publicAccessUrl}php/ui/subscribers/add_subscriber.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                toastr.success(resp.message);
                $(this).trigger(`reset`);
            }
        }, `json`);
    });

    $(`#unsubscribe_anchor`).click(function(e) {
        e.preventDefault();
        let form = $(`#newsletter_form`);
        let json = Object.fromEntries((new FormData(form[0])).entries());

        $.post(`${publicAccessUrl}php/ui/subscribers/remove_subscriber.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
            } else {
                toastr.success(resp.message);
                form.trigger(`reset`);
            }
        }, `json`);

    });
</script>