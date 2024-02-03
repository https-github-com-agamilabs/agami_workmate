$(document).ready(function () {
    let langSelect = document.getElementsByName(`lang`)[0];
    langSelect.value = lang;

    $(document).on("submit", "#login_form", function (e) {
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

        complete_login({
            username,
            password,
            action: `employee_login`
        });
    });

    langSelect.addEventListener(`change`, function (e) {
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

    $(function () {
        let splits = location.pathname.split(`/`),
            fileName = splits[Math.max(splits.length - 1, 0)];

        if (fileName && fileName.length) {
            let navItem = $(`#medilifeMenu [href*="${fileName}"]:first`).parents(
                `.nav-item`
            );
            $(`#medilifeMenu .nav-item.active`).removeClass(`active`);
            navItem.addClass(`active`);
        }

        $(`#medilifeMenu [href^="#"]`).click(function (e) {
            e.preventDefault();

            const hash = e.target.getAttribute("href");
            const target = document.getElementById(hash.substring(1));

            window.scroll({
                top: target.offsetTop - 100,
                behavior: "smooth",
            });

            if (history.pushState) {
                history.pushState(null, null, hash);
            } else {
                location.hash = hash;
            }
        });
    });

    function complete_login(json) {
        // needs for recaptacha ready
        grecaptcha.ready(function () {
            // do request for recaptcha token
            // response is promise with passed token
            grecaptcha.execute("6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA", {
                action: json.action
            }).then(function (captchatoken) {
                json.captchatoken = captchatoken;

                $.post(`php/ui/login/login.php`, json, resp => {
                    if (resp.error) {
                        toastr.error(resp.message);
                    } else {
                        // toastr.success(resp.message);
                        window.location.href = resp.redirecturl;
                        // window.location.href = resp.ucatno == 5 ? "dashboard.php" : "time_keeper.php";
                    }
                }, `json`);
            });
        });
    }
});
