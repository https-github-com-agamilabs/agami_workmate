<?php
include_once("configmanager/org_configuration.php");
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0,minimum-scale=1.0">
<title>Employee | <?= $response['orgname']; ?></title>
<link rel="shortcut icon" type="image/png" sizes="16x16" href="<?= $response['orglogourl']; ?>">

<?php
$debug = false;
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "localhost";
$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$debughost = array('localhost', '192.168.1');

for ($i = 0; $i < count($debughost); $i++) {
    // code...
    $adebughost = $debughost[$i];
    if (strpos($host, $adebughost) === 0) {
        $debug = true;
    }
}

// if ($debug) {
//     include_once "dependency_style_script_offline.php";
// } else {
// }
include_once "dependency_style_script_online.php";

?>

<link href="css/main.css" rel="stylesheet">
<script type="text/javascript" src="js/main.js" async defer></script>
<script type="text/javascript" src="js/utility.js"></script>

<link href="css/multidatespicker.css" rel="stylesheet">
<script type="text/javascript" src="js/multidatespicker.js" async defer></script>

<script src="//cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

<style media="screen">
    body {
        font-family: 'Play', 'Rockwell', Tahoma, sans-serif;
    }

    /* .app-main .app-main__inner {
        padding: 15px 15px 0;
    } */

    .app-sidebar__inner .vertical-nav-menu li a {
        margin: 0;
    }
</style>

<style>
    input.text-white::-webkit-input-placeholder {
        color: #fff;
    }

    input.text-white::-moz-placeholder {
        color: #fff;
    }

    input.text-white:-ms-input-placeholder {
        color: #fff;
    }

    input.text-white:-moz-placeholder {
        color: #fff;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .grow {
        transition: all .2s ease-in-out;
    }

    .grow:hover {
        transform: scale(1.005);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .custom_shadow {
        box-shadow: 0 2px 5px 0 rgb(0 0 0 / 20%), 0 2px 10px 0 rgb(0 0 0 / 10%);
    }

    .custom_shadow:hover {
        box-shadow: 0 4px 10px 0 rgb(0 0 0 / 20%), 0 4px 20px 0 rgb(0 0 0 / 10%);
    }

    .card a:not(.collapsed) i.rotate-icon {
        -webkit-transform: rotate(180deg);
        transform: rotate(180deg)
    }

    .custom_fieldset {
        border: 1px solid #cccccc;
        border-radius: .25rem;
        width: 100%;
        padding: 15px;
    }

    .legend-label {
        min-width: 250px;
        width: max-content;
        margin: 0px;
        border: 1px solid #cccccc;
        padding: 5px 10px;
        border-radius: 30px;
        text-align: center;
        box-shadow: 0 0.125rem 0.25rem rgb(0 0 0 / 8%);
    }

    .pagination-button {
        background-color: #3f6ad8;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        padding: 0px 10px;
        border: 2px solid white;
        border-radius: 5px;
        cursor: pointer;
    }

    .pagination-pageno {
        color: #3f6ad8;
        /* background-color: #3f6ad8; */
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 1px 10px 0 rgba(0, 0, 0, 0.19);
        border: 2px solid white;
        border-radius: 5px;
        cursor: pointer;
        /* height: 75%; */
        margin-top: 0px;
        text-align: center;
        min-width: 120px;
    }

    a.mm-active .fa {
        color: #d92550;
    }

    a.mm-active .fas {
        color: #d92550;
    }

    .tabs-animated-shadow .nav-link {
        color: #3f6ad8;
        border: 1px solid #3f6ad8;
        border-radius: .25rem;
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 35px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
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

    $(document).on("shown.bs.modal", ".modal", (e) => $(e.target).find("input:not(:disabled,input[type=button],input[type=submit]),select,textarea").filter(":visible:first").trigger("focus"));

    $(document).on("keydown", "input:not(:disabled,input[type=button],input[type=submit]),select,textarea", function(e) {
        let form = $(this).parents("form:eq(0)");
        if (form && e.shiftKey && e.key === "Enter") {
            e.preventDefault();
            form.find(":submit").trigger("click");
        } else if (form && e.key === "Enter") {
            let focusable = form.find("input:not(:disabled,input[type=button],input[type=submit]),select,textarea").filter(":visible");
            let next = focusable.eq(focusable.index(this) + 1);
            if (next.length) {
                next.focus();
            } else {
                e.preventDefault();
                form.find(":submit").trigger("click");
            }
        }
    });
</script>