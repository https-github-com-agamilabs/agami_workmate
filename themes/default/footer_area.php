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
include_once "./lang_converter/converter.php";
$jasonFilePath = './lang-json/' . $lang . '/about.json';
$langData = langConverter($jasonFilePath);
//print_r($arrayData);
//echo $arrayData[$lang]['lang_about_us'];

?>


<footer id="footer_area" class="footer-area section-padding-100">
    <!-- Main Footer Area -->
    <div class="main-footer-area">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="footer-widget-area">
                        <div class="footer-logo">
                            <img src="<?= $publicAccessUrl . $response['orglogourl']; ?>" style="height:45px;" alt="">
                        </div>
                        <p id="footer_about">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer.</p>
                        <div class="footer-social-info">
                            <a href="#"><i class="fab fa-google-plus-g" aria-hidden="true"></i></a>
                            <a href="#"><i class="fab fa-pinterest-p" aria-hidden="true"></i></a>
                            <a href="#"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                            <a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="footer-widget-area">
                        <div class="widget-title">
                            <h6><?= $langData[$lang]['lang_latest_blog_post']; ?></h6>
                        </div>
                        <div class="blog_post_container widget-blog-post">
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="<?= $imgPath ?>blog-img/ln1.jpg" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#">Better Health Care</a>
                                    <p>Dec 02, 2017</p>
                                </div>
                            </div>
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="<?= $imgPath ?>blog-img/ln2.jpg" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#">A new drug is tested</a>
                                    <p>Dec 02, 2017</p>
                                </div>
                            </div>
                            <!-- Single Blog Post -->
                            <div class="widget-single-blog-post d-flex">
                                <div class="widget-post-thumbnail">
                                    <img src="<?= $imgPath ?>blog-img/ln3.jpg" alt="">
                                </div>
                                <div class="widget-post-content">
                                    <a href="#">Health department advice</a>
                                    <p>Dec 02, 2017</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div id="contact_form_div" class="footer-widget-area">
                        <div class="widget-title">
                            <h6><?= $langData[$lang]['lang_contact_form']; ?></h6>
                        </div>
                        <div class="footer-contact-form">
                            <form action="#" method="post">
                                <input type="text" class="form-control border-top-0 border-right-0 border-left-0" name="footer-name" id="footer-name" placeholder="Name">
                                <input type="email" class="form-control border-top-0 border-right-0 border-left-0" name="footer-email" id="footer-email" placeholder="Email">
                                <textarea name="message" class="form-control border-top-0 border-right-0 border-left-0" id="footerMessage" placeholder="Message"></textarea>
                                <button type="submit" class="btn medilife-btn"><?= $langData[$lang]['lang_contact_us']; ?> <span>+</span></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="footer-widget-area">
                        <div class="widget-title">
                            <h6><?= $langData[$lang]['lang_subscribe_blog']; ?> </h6>
                        </div>

                        <div class="footer-newsletter-area">
                            <form id="newsletter_form">
                                <input name="email" type="email" class="form-control border-0 mb-0" placeholder="Your Email Here" required>
                                <button type="submit"><?= $langData[$lang]['lang_subscribe']; ?></button>
                                <div class="text-right">
                                    <a id="unsubscribe_anchor" href="javascript:void(0);" class="text-muted mr-3" style="font-size: 12px;"><?= $langData[$lang]['lang_unsubscribe']; ?></a>
                                </div>
                            </form>
                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Lorem ipsum dolor sit amet, consectetuer.</p>
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
                                <?= $langData[$lang]['lang_copyright']; ?> &copy;<script>
                                    document.write(new Date().getFullYear());
                                </script><?= $langData[$lang]['lang_all_rights_reserved']; ?>
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

    get_limited_blogarticles();

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