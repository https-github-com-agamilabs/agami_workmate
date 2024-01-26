$(document).on(`click`, `.toggle_password`, function (e) {
    if ($(this).hasClass(`fa-eye`)) {
      $(this)
        .removeClass(`fa-eye`)
        .addClass(`fa-eye-slash`)
        .siblings(`[type="password"]`)
        .attr(`type`, `text`);
    } else {
      $(this)
        .removeClass(`fa-eye-slash`)
        .addClass(`fa-eye`)
        .siblings(`[type="text"]`)
        .attr(`type`, `password`);
    }
  });
  
  function complete_login(json, form) {
    let form_submit = $(":submit", form);
    form_submit
      .data("html", form_submit.html())
      .html("<i class='fa fa-spinner fa-spin'></i>")
      .prop("disabled", true);
  
    json.action = "accounting_login";
  
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA", {
          action: json.action,
        })
        .then(function (token) {
          json.captchatoken = token;
  
          $.post(
            `php/ui/login/login.php`,
            json,
            (resp) => {
  
              form_submit.html(form_submit.data("html")).prop("disabled", false);
  
              if (resp.error) {
                toastr.error(resp.message);
              } else {
                toastr.success(resp.message);
  
                const params = new Proxy(
                  new URLSearchParams(window.location.search),
                  {
                    get: (searchParams, prop) => searchParams.get(prop),
                  }
                );
                // Get the value of "some_key" in eg "https://example.com/?some_key=some_value"
  
                if (params.redirect) {
                  // redirection
                  location.href = params.redirect;
                } else if (resp.redirecturl) {
                  location.href = resp.redirecturl;
                } else {
                  // default
                  // location.href = `organizations.php`;
                  window.location.href = resp.ucatno == 5 ? "dashboard.php" : "time_keeper.php";
                }
              }
            },
            `json`
          );
        });
    });
  }
  
  $(document).on(`submit`, `.login_form`, function (e) {
    e.preventDefault();
  
    let json = Object.fromEntries(new FormData(this).entries());
  
    if (json.username.length < 3) {
      toastr.warning("Username should be at least 3 characters long!");
      return;
    }
  
    if (json.password.length < 6) {
      toastr.warning("Password should be at least 6 characters long!");
      return;
    }
  
    complete_login(json, this);
  });
  
  $(document).on(`click`, `.create_new_account_button`, function (e) {
    $(`#signup_modal`).modal(`show`);
  });
  
  $(document).on("submit", `#signup_modal_form`, function (e) {
    e.preventDefault();
    let json = Object.fromEntries(new FormData(this).entries());
  
    if (json.password !== json.retype_password) {
      toastr.error(`Password doesn't match!`);
      return;
    } else {
      delete json.retype_password;
    }
  
    if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
      json.contactno = json.contactno.substring(1);
    } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {
    } else {
      toastr.error(`Invalid mobile no!`);
      return;
    }
  
    let isValid = $(`[name="contactno"]`, this).data(`valid`);
    if (!isValid) {
      $(`[name="contactno"]`, this)
        .siblings(`.invalid-feedback`)
        .html(
          `You are already our user! <a href="javascript:void(0);" class="forgotten_password">Forgotten password?</a>`
        )
        .show();
      toastr.error(
        `You are already our user! If you want to create new user, then change mobile no.`
      );
      return;
    }
  
    json.dob = `${json.dob_year}-${json.dob_month}-${json.dob_date}`;
    delete json.dob_year;
    delete json.dob_month;
    delete json.dob_date;
  
    let usernameChecked = $(`[name="username_type"]:checked`, this).val();
  
    if (usernameChecked == 1) {
      json.username = `0${json.contactno}`;
    } else if (usernameChecked == 2) {
      if (json.email.length) {
        json.username = json.email;
      } else {
        toastr.error(`Email is required, if you want to set email as username.`);
        $(`[name="email"]`, this).focus();
        return;
      }
    } else if (usernameChecked == 3) {
      json.username = $(`[name="username"][type="text"]`, this).val();
  
      if (!json.username.length) {
        toastr.error(`Username is required.`);
        $(`[name="username"][type="text"]`, this).focus();
        return;
      }
    }
  
    $.post(
      `php/ui/login/create_user.php`,
      json,
      (resp) => {
        if (resp.error) {
          toastr.error(resp.message);
        } else {
          toastr.success(resp.message);
          $(`#signup_modal`).modal(`hide`);
          $(`#signup_modal_form`).trigger(`reset`);
          complete_login(
            {
              username: json.username,
              password: json.password,
            },
            `#signup_modal_form`
          );
        }
      },
      `json`
    );
  });
  
  $(document)
    .on(`blur`, `#signup_modal_form [name="contactno"]`, function (e) {
      check_exist_people();
    })
    .on(`keyup`, `#signup_modal_form [name="contactno"]`, function (e) {
      if (e.keyCode == 13) {
        check_exist_people();
      }
    });
  
  function check_exist_people() {
    let contactInput = $(`#signup_modal_form [name="contactno"]`);
    let invalidFeedback = contactInput.siblings(`.invalid-feedback`);
  
    let json = {
      contactno: contactInput.val(),
    };
  
    if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
      json.contactno = json.contactno.substring(1);
    } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {
    } else {
      toastr.error(`Invalid mobile no!`);
      return;
    }
  
    $.post(
      `php/ui/login/is_exist_user.php`,
      json,
      (resp) => {
        if (resp.error) {
          invalidFeedback
            .html(`${resp.message} <a href="" class="">Forgotten password?</a>`)
            .show();
          toastr.error(resp.message);
          contactInput.data(`valid`, false);
        } else {
          invalidFeedback.hide();
          contactInput.data(`valid`, true);
        }
      },
      `json`
    );
  }
  
  $(document).on(
    `change`,
    `#signup_modal_form [name="username_type"]`,
    function (e) {
      let form = $(`#signup_modal_form`);
      let contactnoElem = $(`[name="contactno"]`, form);
      let emailElem = $(`[name="email"]`, form);
      let usernameElem = $(`[name="username"]`, form);
  
      if (this.value == 1) {
        usernameElem.hide();
  
        let contactno = contactnoElem.val();
  
        if (contactno.length == 11 && contactno.startsWith(`01`)) {
        } else if (contactno.length == 10 && contactno.startsWith(`1`)) {
          contactno = `0${contactno}`;
        } else {
          toastr.error(`Invalid mobile no!`);
          contactnoElem.focus();
          return;
        }
  
        usernameElem.val(contactno);
      } else if (this.value == 2) {
        usernameElem.hide();
  
        if (emailElem.val().length) {
          usernameElem.val(emailElem.val());
        } else {
          toastr.error(`Email not set.`);
          emailElem.focus();
        }
      } else if (this.value == 3) {
        usernameElem.val(``).show().focus();
      }
    }
  );
  
  $(document).on(`click`, `.forgotten_password`, function (e) {
    e.preventDefault();
    $(`#forgotten_password_modal`).modal(`show`);
  });
  
  $(document).on("submit", `#forgotten_password_modal_form`, function (e) {
    e.preventDefault();
    let json = Object.fromEntries(new FormData(this).entries());
  
    let form_submit = $(":submit", this);
    form_submit
      .data("html", form_submit.html())
      .html("<i class='fa fa-spinner fa-spin'></i>")
      .prop("disabled", true);
  
    let message_div = $(".message_div", this);
    message_div.html("");
  
    json.action = "accounting_forget_password";
  
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA", {
          action: json.action,
        })
        .then(function (token) {
          json.captchatoken = token;
  
          //   await new Promise((res, rej) => setTimeout(() => res(), 2000));
          $.post(
            `php/ui/login/forget_password.php`,
            json,
            (resp) => {
              form_submit.html(form_submit.data("html")).prop("disabled", false);
  
              // console.log(message_div);
              if (resp.error) {
                toastr.error(resp.message);
                message_div
                  .attr("class", "message_div alert alert-danger text-center")
                  .html(resp.message);
              } else {
                toastr.success(resp.message);
                message_div
                  .attr("class", "message_div alert alert-success text-center")
                  .html(resp.message);
  
                new Promise((res, rej) => setTimeout(() => res(), 2000)).then(
                  () => {
                    $(`#forgotten_password_modal`).modal(`hide`);
                  }
                );
              }
            },
            `json`
          );
        });
    });
  });
  