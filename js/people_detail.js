function dateToDDMMYYYY(date) {
    const d = new Date(date);
    return `${d.getDate().toString().padStart(2, 0)}-${(d.getMonth() + 1).toString().padStart(2, 0)}-${d.getFullYear()}`;
}

get_peopleprimary();

function get_peopleprimary() {
    $.post(`${publicAccessUrl}php/ui/user/get_users.php`, {
        showmyinfo: 1
    }, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else if (resp.results.length) {
            show_peopleprimary(resp.results[0]);
        }
    }, "json");
}

function show_peopleprimary(data) {
    $(`#people_detail_update_form`).data(data);

    if (data.photo_url && data.photo_url.length) {
        $(`#tchrProPic`).attr("src", data.photo_url);
    }
    $(`#tchrProPic`).data(`photo_url`, data.photo_url);

    $(`#people_name`).html(`${data.firstname || ``}${data.lastname ? ` ${data.lastname}` : ``}`);
    $(`#people_primarycontact`).html(data.primarycontact ? data.primarycontact : ``);
    $(`#people_email`).html(data.email || ``);

    // $(`#people_detail_shortbio`).html(data.shortbio || ``);
    // $(`#people_gender`)
    //     .html(data.gender == `Male`
    //         ? `<i class="fas fa-male mr-2"></i> Male`
    //         : (data.gender == `Female`
    //             ? `<i class="fas fa-female mr-2"></i> Female`
    //             : `<i class="fas fa-genderless mr-2"></i> Other`));

    // $(`#people_bloodgroup`).html(data.bloodgroup || ``);
    // $(`#people_dob`).html(data.dob ? dateToDDMMYYYY(data.dob) : ``);
    // $(`#people_nid`).html(data.nid || ``);
    // $(`#people_street`).html(data.street || ``);
    // $(`#people_postcode`).html(data.postcode ? `[${data.postcode}] ${data.po}, ${data.ps}` : ``);
    // $(`#people_country`).html(data.country || ``);
}

$(`#people_detail_update_form`).submit(function (e) {
    e.preventDefault();
    let json = Object.fromEntries((new FormData(this)).entries());
    json.userno = $(this).data(`userno`);

    $.post(`${publicAccessUrl}php/ui/user/setup_user.php`, json, (resp) => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            $(`.people_detail_collapse`).collapse("toggle");
            get_peopleprimary();
            sessionStorage.removeItem(`exist_people_${json.userno}`);
        }
    }, "json");
});

$(`#people_detail_edit_profile_button`).click(function (e) {
    const data = $(`#people_detail_update_form`).data();
    $(`#people_detail_update_form [name]`).each((index, elem) => {
        let elemName = $(elem).attr("name");
        if (data[elemName] != null) {
            $(elem).val(data[elemName]);
            // if ($(elem).hasClass(`select2-hidden-accessible`)) {
            //     $(elem).trigger(`change`);
            // }
        }
    });
});

$(`#people_detail_edit_profile_button, #people_detail_form_cancel_button`).click(function (e) {
    $(`.people_detail_collapse`).collapse("toggle");
});