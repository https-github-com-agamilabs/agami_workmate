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
    return padZero(date.getDate()) + " " + months[date.getMonth()] + " " + date.getFullYear() + " " + strTime;
}

function formatTime(timeString = "00:00:00") {
    if (!timeString || !timeString.length) {
        return ``;
    }

    let H = +timeString.substr(0, 2);
    let h = H % 12 || 12;
    let ampm = (H < 12 || H === 24) ? " AM" : " PM";
    return h + timeString.substr(2, 3) + ampm;
}

function differenceOfDays(date) {
    const date1 = new Date();
    const date2 = new Date(date);
    if (date2 - date1 <= 0) {
        return 0;
    }

    const diffTime = Math.abs(date2 - date1);
    return diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

const orgType = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/organization/get_org_type.php`,
    targets: [{
        selectElem: `#orgs_modal [name="orgtypeid"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `orgtypename`,
    optionValue: `orgtypeid`
});

const settings = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/orgsettings/get_settings.php`
});

const userCategoryLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/user/list_usercat.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="ucatno"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `ucattitle`,
    optionValue: `ucatno`
});

const usersLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/user/list_users.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="foruserno"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    templateString: `<span>{{firstname}}</span>
        <span>{{lastname}}</span>
        <span>[{{username}}]</span>`,
    optionValue: `userno`
});

const timeFlexibilityLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/settings/orguser/get_timeflexibility.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="timeflexibility"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `timeflextitle`,
    optionValue: `timeflexno`
});

const timeZonesLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/settings/orguser/get_timezones.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="timezone"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `timezonetitle`,
    optionValue: `timezonetitle`
});

const shiftLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/settings/orguser/get_shift.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="shiftno"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    templateString: `<span>{{shifttitle}}</span>
		<span>[{{starttime}}</span> - <span>{{endtime}}]</span>`,
    optionValue: `shiftno`
});

const modules = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/organization/get_modules.php`,
    targets: [{
        selectElem: `#userorg_setup_modal_form [name="moduleno"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `moduletitle`,
    optionValue: `moduleno`
});

const colorLoad = new SelectElemDataLoad({
    readURL: `${publicAccessUrl}php/ui/taskmanager/selection/list_color.php`,
    targets: [{
        selectElem: `#org_storyphase_modal [name="colorno"]`,
        defaultOptionText: `Select...`,
        defaultOptionValue: ``
    }],
    optionText: `colortitle`,
    optionValue: `colorno`
});

const orgAccS2Settings = (additionalParams = {}, placeholder = `Select account...`) => {
    return {
        placeholder,
        allowClear: true,
        width: `calc(100% - 0px)`,
        ajax: {
            url: `${publicAccessUrl}php/ui/settings/pop_vorgaccounts.php`,
            dataType: "json",
            type: "POST",
            data: function (params) {
                return {
                    search_key: params.term,
                    pageno: params.page || 1,
                    limit: 20,
                    ...additionalParams
                };
            },
            processResults: function (data, params) {
                params.pageno = params.page || 1;

                $.each(data.results, (index, value) => {
                    value.id = value.accno;
                    value.text = `[${value.accno}] ${value.accname}`;
                });

                return data;
            },
            cache: false
        }
    }
};

function load_org_settings(target) {
    let settingsInterval = setInterval(() => {
        if (settings.data && settings.data.length) {
            target.empty().append(`<option value="">Select...</option>`);

            $.each(settings.data, (index, value) => {
                target.append(`<option value="${value.setid}">${value.settitle}</option>`);
            });

            clearInterval(settingsInterval);
        }
    }, 500);
}

function load_modules(target) {
    let modulesInterval = setInterval(() => {
        if (modules.data && modules.data.length) {
            target.empty();

            $.each(modules.data, (index, value) => {
                target.append(`<option value="${value.moduleno}">${value.moduletitle}</option>`);
            });

            clearInterval(modulesInterval);
        }
    }, 500);
}

function get_my_valid_packages(json, target) {
    target.empty();
    let formElem = target.parents(`.tab-pane`).find(`form`);

    $.post(`${publicAccessUrl}php/ui/package/get_my_valid_packages.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
            target.hide().siblings(`.invalid-feedback`).show();
            formElem.hide();
        } else {
            target.show().siblings(`.invalid-feedback`).hide();
            formElem.show();
            show_my_valid_packages(resp.results, target);
        }
    }, `json`);
}

function show_my_valid_packages(data, target) {
    let packages = [];

    $.each(data, (index, value) => {
        let pack = packages.find(a => a.purchaseno == value.purchaseno);

        if (pack) {
            pack.items = [...pack.items, {
                item: value.item,
                max_user_qty: value.max_user_qty,
                duration: value.duration,
                starttime: value.starttime
            }];
        } else {
            packages = [...packages, {
                purchaseno: value.purchaseno,
                licensekey: value.licensekey,
                offerno: value.offerno,
                offertitle: value.offertitle,
                items: [{
                    item: value.item,
                    max_user_qty: value.max_user_qty,
                    duration: value.duration,
                    starttime: value.starttime
                }]
            }];
        }
    });

    $.each(data, (index, value) => {
        let template = $(`<option value="${value.purchaseno}">${value.offertitle} (${value.licensekey})</option>`)
            .data(value)
            .appendTo(target);
    });
}

class Organization extends BasicCRUD {
    show(data) {
        let thisObj = this;

        $.each(data, (index, value) => {
            // ADDRESS
            let address = ``;

            if (value.street && value.street.length) {
                address += value.street;
            }

            if (value.city && value.city.length) {
                if (address.length) {
                    address += `, `;
                }
                address += value.city;
            }

            if (value.country && value.country.length) {
                if (address.length) {
                    address += `, `;
                }
                address += value.country;
            }

            let primarycontact = value.primarycontact;

            // OFFICE TIME
            let officeTime = ``;

            if (value.starttime && value.starttime.length) {
                if (value.endtime && value.endtime.length) {
                    officeTime += `Office Time: `;
                } else {
                    officeTime += `Office Open: `;
                }

                officeTime += `<b>${formatTime(value.starttime)}</b>`;
            }

            if (value.endtime && value.endtime.length) {
                if (officeTime.length) {
                    officeTime += ` - `;
                }
                officeTime += `<b>${formatTime(value.endtime)}</b>`;
            }

            // WEEKEND
            let weekend = ``;

            if (value.weekend1 && value.weekend1.length) {
                if (value.endtime && value.endtime.length) {
                    weekend += `Weekend: `;
                }
                weekend += `<b>${value.weekend1}</b>`;
            }

            if (value.weekend2 && value.weekend2.length) {
                if (weekend.length) {
                    weekend += `, `;
                }
                weekend += `<b>${value.weekend2}</b>`;
            }

            let validityClass = `alert-success`;

            if (value.verifiedno != 1) {
                validityClass = `alert-danger`;
            } else if (value.pack_validuntil && value.pack_validuntil.length) {
                if (differenceOfDays(value.pack_validuntil) <= 7) {
                    validityClass = `alert-warning`;
                }
            }

            let isOwner = value.permissionlevel == 7 && value.ucatno == 19;
            let isEditDeleteAllowed = value.createdby == USERNO || isOwner;
            let isOrgControllerAllowed = value.createdby == USERNO || isOwner;

            let orgControllerHTML = ``;
            if (isOrgControllerAllowed) {
                orgControllerHTML = `<hr class="my-2">
                    <div class="">
                        <div class="text-center mb-2">
                            <a class="h5" data-toggle="collapse" href="#org_${value.orgno}_controller_collapse" class="collapsed">
                                Organization Controller
                                <i class="fas fa-angle-down rotate-icon"></i>
                            </a>
                        </div>
                        <div id="org_${value.orgno}_controller_collapse" class="collapse">
                            <ul class="nav tabs-animated tabs-animated-shadow">
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#org_${value.orgno}_setting_tabpane" class="nav-link active">
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#org_${value.orgno}_working_location_tabpane" class="nav-link">
                                        <span>Working Location</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#org_${value.orgno}_module_tabpane" class="nav-link">
                                        <span>Employees</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#org_${value.orgno}_tasktype_tabpane" class="nav-link">
                                        <span>Task Type</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="org_${value.orgno}_setting_tabpane" role="tabpanel">
                                    <div class="settings_container"></div>
                                    <fieldset class="custom_fieldset position-relative pt-1" style="background:azure;">
                                        <form class="orgsettings_form org_${value.orgno}_collapse mb-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label class="d-block mb-0">
                                                        Set ID <span class="text-danger">*</span>
                                                        <select name="setid" class="form-control form-control-sm shadow-sm mt-1" required></select>
                                                    </label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="d-block mb-0">
                                                        Set Label <span class="text-danger">*</span>
                                                        <input name="setlabel" class="form-control form-control-sm shadow-sm mt-1" type="text" placeholder="Set Label..." required>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-2 d-none">
                                                <label class="d-block mb-0">
                                                    File
                                                    <input name="fileurl" class="form-control form-control-sm shadow-sm mt-1" type="file">
                                                </label>
                                            </div>
                                            <div class="text-right mt-2">
                                                <button class="btn btn-primary btn-sm px-5 ripple custom_shadow" type="submit" title="Save Organization Settings">Save</button>
                                            </div>
                                        </form>
                                    </fieldset>
                                </div>

                                <div class="tab-pane" id="org_${value.orgno}_working_location_tabpane" role="tabpanel">
                                    <div id="org_${value.orgno}_working_location_card">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="font-weight-bold mb-0">Organization Working Location</h5>
                                            <button class="add_button btn btn-primary btn-sm rounded-pill px-3 custom_shadow" type="button">
                                                <i class="fa fa-plus-circle mr-1"></i> Add
                                            </button>
                                        </div>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm table-striped table-bordered table-hover mb-0">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Location</th>
                                                        <th>Latitude</th>
                                                        <th>Longitude</th>
                                                        <th class="text-center">Activation</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="org_${value.orgno}_working_location_tbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="org_${value.orgno}_module_tabpane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p>Number of active user is controlled by the applied package.</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="d-block">
                                                <div class="d-flex justify-content-between align-items-end">
                                                    <div>Your Valid Packages</div>
                                                    <div>
                                                        <a href="my_packages.php" class="btn btn-primary btn-sm ripple custom_shadow">Buy</a>
                                                    </div>
                                                </div>
                                                <select name="purchaseno" class="form-control form-control-sm shadow-sm mt-2"></select>
                                                <div class="invalid-feedback">You don't have any valid package. Please buy a new package.</div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="userorg_info_container_div">
                                        <div class="text-right mb-1">
                                            <button class="userorg_add_button btn btn-primary btn-sm ripple custom_shadow" type="button">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>
                                        <div class="table-responsive rounded shadow-sm">
                                            <table class="table table-sm table-bordered table-hover table-striped mb-0">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>SL</th>
                                                        <th style="min-width:300px;">User</th>
                                                        <th>Module & Supervisor</th>
                                                        <th>Work load & Salary</th>
                                                        <th>Shift</th>
                                                        <th style="width:75px;min-width:75px;">Validity</th>
                                                        <th style="width:134px;min-width:134px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="userorg_info_tbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="org_${value.orgno}_tasktype_tabpane" role="tabpanel">
                                    <div id="org_${value.orgno}_storyphase_card">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="font-weight-bold mb-0">Task Type</h5>
                                            <button class="add_button btn btn-primary btn-sm rounded-pill px-3 custom_shadow" type="button">
                                                <i class="fa fa-plus-circle mr-1"></i> Add
                                            </button>
                                        </div>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-sm table-striped table-bordered table-hover mb-0">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>SL</th>
                                                        <th>Task Type</th>
                                                        <th>Color</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="org_${value.orgno}_storyphase_tbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }

            let template = $(`<div class="card org_card mb-3">
                    <div class="card-header justify-content-between ${validityClass}">
                        <div style="text-transform: initial;">
                            <h5 class="font-weight-bold mb-0">${value.orgname}</h5>
                            ${value.orgtypename && value.orgtypename.length ? `<div class="small">(${value.orgtypename})</div>` : ``}
                        </div>
                        ${value.verifiedno == 1 ? `<div class="mx-auto mr-md-0 mt-2 mt-md-0" style="max-width:170px;">
                            <button class="proceed_to_workmate btn btn-primary btn-sm ripple custom_shadow" type="button" title="Proceed To Workmate">
                                <i class="fas fa-sign-in-alt mr-1"></i>
                                Proceed <span class="d-none d-sm-inline">To Workmate</span>
                            </button>
                        </div>` : ``}
                    </div>
                    <div class="card-body">
                        <div class="media">
                            <img src="${value.picurl || `assets/store_logo/demo_logo.png`}" class="align-self-start img-fluid rounded shadow-sm border mr-3 cursor-pointer preview_orglogo" style="width:100px;" alt="${value.orgname}">
                            <div class="media-body position-relative">
                                ${address.length ? `<div class="d-flex flex-wrap align-items-center font-size-lg">
                                        <div><i class="fas fa-home mr-2"></i></div>
                                        <div class="mr-2">${address}</div>
                                        ${value.gpslat && value.gpslon ? `<a href="${link}&query=${value.gpslat}%2C${value.gpslon}"
                                            target="_blank" class="small" title="View organization in map">
                                                (View Map)
                                            </a>` : ``}
                                    </div>` : ``}
                                ${primarycontact.length ? `<div  class="d-flex flex-wrap align-items-center font-size-lg">
                                        <div><i class="fas fa-phone-alt mr-2"></i></div>
                                        <div>${primarycontact}</div>
                                    </div>` : ``}
                                ${officeTime.length ? `<div>${officeTime}</div>` : ``}
                                ${weekend.length ? `<div>${weekend}</div>` : ``}
                            </div>
                            ${isEditDeleteAllowed ? `<div class="">
                                    <button class="edit_button btn btn-sm btn-info ripple rounded-circle custom_shadow mr-sm-1 mb-1" type="button" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete_button btn btn-sm btn-danger ripple rounded-circle custom_shadow mr-sm-1 mb-1" type="button" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>` : ``}
                        </div>
                        ${value.orgnote && value.orgnote.length ? `<div>${value.orgnote}</div>` : ``}
                        ${orgControllerHTML}
                    </div>
                    ${value.verifiedno == 1 ? `<div class="card-footer p-0">
                        <div class="mx-auto mr-md-0" style="max-width:170px;">
                            <button class="proceed_to_workmate btn btn-primary btn-sm ripple rounded-0 custom_shadow" type="button" title="Proceed To Workmate">
                                <i class="fas fa-sign-in-alt mr-1"></i>
                                Proceed <span class="d-none d-sm-inline">To Workmate</span>
                            </button>
                        </div>
                    </div>` : ``}
                </div>`)
                .data(value)
                .appendTo(this.targetContainer);

            let setidSelect = $(`.orgsettings_form [name="setid"]`, template);
            let packageSelect = $(`[name="purchaseno"]`, template);

            let settingsContainer = $(`.settings_container`, template);
            let userOrgInfoTbody = $(`.userorg_info_tbody`, template);

            if (value.picurl && value.picurl.length) {
                $(`.preview_orglogo`, template)
                    // .attr(`src`, value.picurl)
                    .data(`response`, {
                        fileurl: value.picurl
                    });
            }

            (function ($) {
                $('.preview_orglogo', template).on('click', function (e) {
                    console.log($(this).data(`response`));
                    show_image_cropping_modal($(this), {
                        title: `Organization logo`,
                        target_dir: `files/orglogo/`,
                        preview_target: $(this),
                        prev_fileurl: $(this).data(`response`)
                    }, function (upload_response) {
                        console.log(upload_response);

                        let json = {};
                        let tablePK = Number(value[thisObj.tablePK]) || 0;
                        let url = thisObj.updateURL;

                        console.log(thisObj, url);
                        if (tablePK > 0) {
                            json[thisObj.tablePK] = tablePK;
                        }

                        if (!upload_response?.fileurl.length) {
                            return;
                        }

                        json["picurl"] = upload_response.fileurl;

                        $.post(url, json, resp => thisObj.successCallback(resp), "json");
                    });
                });

                $(`.edit_button`, template).click((e) => {
                    if (value.picurl && value.picurl.length) {
                        $(`.preview_orglogo`, thisObj.setupForm)
                            .attr(`src`, value.picurl)
                            .data(`response`, {
                                fileurl: value.picurl
                            });
                    }

                    thisObj.setupModal.modal(`show`).find(`.modal-title`).html(`Update ${thisObj.topic}`);
                    thisObj.setupForm.trigger("reset").data(thisObj.tablePK, value[thisObj.tablePK]).data(`action`, thisObj.updateURL);

                    $(`[name]`, thisObj.setupForm).each((i, elem) => {
                        let elementName = $(elem).attr("name");
                        if (value[elementName] != null && elementName != `picurl`) {
                            $(elem).val(value[elementName]);
                        }
                    });
                });

                thisObj.deleteButtonTrigger(template, value);

                $(`.proceed_to_workmate`, template).click(function (e) {
                    start_org_operation({
                        orgno: value.orgno
                    });
                });

                if (isOrgControllerAllowed) {
                    $(`[href="#org_${value.orgno}_controller_collapse"]`, template).click(function (e) {
                        if (!$(this).data(`is_loaded`)) {
                            load_org_settings(setidSelect);
                            get_my_valid_packages({
                                orgno: value.orgno
                            }, packageSelect);

                            get_orgsettings({
                                orgno: value.orgno
                            }, settingsContainer);

                            get_userorg_detail({
                                orgno: value.orgno
                            }, userOrgInfoTbody);
                            $(this).data(`is_loaded`, true);
                        }
                    });

                    $(`.orgsettings_form`, template).submit((e) => {
                        e.preventDefault();
                        let json = Object.fromEntries((new FormData(e.target)).entries());
                        json.orgno = value.orgno;
                        setup_orgsettings(json, settingsContainer);
                    });

                    $(`.userorg_add_button`, template).click(function (e) {
                        let packageSelect = $(`#org_${value.orgno}_module_tabpane [name="purchaseno"]`);
                        let purchaseno = packageSelect.val();
                        let aPackage = $(`option:selected`, packageSelect).data();

                        // if (!aPackage) {
                        //     toastr.error(`You don't have any valid package. Please buy a new package.`);
                        //     return;
                        // }

                        let userorgDetail = userOrgInfoTbody.data(`userorg_detail`);
                        let usedQty = userorgDetail ? userorgDetail.length : 1;

                        if (aPackage.max_user_qty == usedQty) {
                            toastr.error(`Your package has already been used up. Please select a different package to add user.`);
                            return;
                        }

                        $(`#userorg_setup_modal`).modal(`show`).html(`Create User Organization`);
                        let form = $(`#userorg_setup_modal_form`)
                            .trigger(`reset`)
                            .data({
                                uono: -1,
                                orgno: value.orgno,
                                purchaseno,
                                userOrgInfoTbody
                            });

                        let supervisorSelect = $(`[name="supervisor"]`, form).empty().append(`<option value="">Select...</option>`);
                        console.log($(`tr`, userOrgInfoTbody));
                        $(`tr`, userOrgInfoTbody).each((index, elem) => {
                            let userOrg = $(elem).data();
                            if (userOrg && userOrg.userno > 0) {
                                $(`<option value="${userOrg.userno}">
                                                        ${userOrg.firstname}
                                                        ${userOrg.lastname || ``}
                                                        [${userOrg.username}]
                                                    </option>`)
                                    .appendTo(supervisorSelect);
                            }
                        });
                    });

                    $(`[href="#org_${value.orgno}_working_location_tabpane"]`, template).on(`shown.bs.tab`, function (e) {
                        if (!$(this).data(`is_loaded`)) {
                            let settings = orgWorkingLocationSettings();
                            settings.targetCard = `#org_${value.orgno}_working_location_card`;
                            settings.targetContainer = `#org_${value.orgno}_working_location_tbody`;

                            const orgWorkingLocation = new OrgWorkingLocation(settings);
                            orgWorkingLocation.get({
                                orgno: value.orgno
                            });

                            $(this).data(`is_loaded`, true);
                        }
                    });

                    $(`[href="#org_${value.orgno}_tasktype_tabpane"]`, template).on(`shown.bs.tab`, function (e) {
                        if (!$(this).data(`is_loaded`)) {
                            let settings = orgStoryPhaseSettings();
                            settings.targetCard = `#org_${value.orgno}_storyphase_card`;
                            settings.targetContainer = `#org_${value.orgno}_storyphase_tbody`;

                            const orgStoryPhase = new OrgStoryPhase(settings);
                            orgStoryPhase.get({
                                orgno: value.orgno
                            });

                            $(this).data(`is_loaded`, true);
                        }
                    });
                }
            })(jQuery);
        });
    }

    setupFormSubmitTrigger() {
        this.setupForm.submit((e) => {
            e.preventDefault();
            let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());

            let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
            let url = this.setupForm.data(`action`);

            if (tablePK > 0) {
                json[this.tablePK] = tablePK;
            }

            delete json.picurl;

            $.post(url, json, resp => this.successCallback(resp), "json");
        });
    }
}

const organization = new Organization({
    readURL: `${publicAccessUrl}php/ui/organization/get_my_org.php`,
    createURL: `${publicAccessUrl}php/ui/organization/setup_organization.php`,
    updateURL: `${publicAccessUrl}php/ui/organization/setup_organization.php`,
    deleteURL: `${publicAccessUrl}php/ui/organization/remove_organization.php`,
    targetCard: `#orgs_card`,
    targetContainer: `#orgs_container`,
    setupModal: `#orgs_modal`,
    topic: `Organization`,
    tablePK: `orgno`
});

organization.get();

$(`.lat_lon_button`).click(function() {
    let form = $(this).parents(`form`);
    get_lat_lon(form);
});

// $(`#googlemapurl`).on('paste', function() {
//     get_lat_lon();
// });

function get_lat_lon(form) {
    let url = $(`.googlemapurl`, form).val();

    let splitUrl = url.split('!3d');
    let latLong = splitUrl[splitUrl.length - 1].split('!4d');
    let latitude = parseFloat(latLong[0]),
        longitude;

    if (latLong.indexOf('?') !== -1) {
        longitude = latLong[1].split('\\?')[0];
    } else {
        longitude = latLong[1];
    }

    longitude = parseFloat(longitude);

    $(`[name].latitude`, form).val(latitude);
    $(`[name].longitude`, form).val(longitude);
}

function start_org_operation(json) {
    $.post(`${publicAccessUrl}php/ui/organization/start_org_operation.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            location.href = resp.redirecturl;
        }
    }, `json`);
}

// ORG SETTINGS

function get_orgsettings(json, target) {
    target.empty();

    $.post(`${publicAccessUrl}php/ui/orgsettings/get_orgsettings.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            show_orgsettings(resp.results, target);
            sessionStorage.setItem(`orgsettings_${json.orgno}`, JSON.stringify(resp.results));
        }
    }, `json`);
}

function show_orgsettings(data, target) {
    let setidSelect = target.data(`orgsettings`, data).siblings(`fieldset`).find(`.orgsettings_form [name="setid"]`);
    $(`option:hidden`, setidSelect).show();

    $.each(data, (index, value) => {
        $(`option[value="${value.setid}"]`, setidSelect).hide();

        let template = $(`<div class="position-relative shadow-sm border rounded p-2 mb-2">
                ${value.fileurl ? `<img src="${value.fileurl}" style="height:50px;" />` : ``}
                <div>
                    ${value.settitle || ``}:
                    <span class="edit_toggle">${value.setlabel || ``}</span>
                    <span class="d-inline mt-1">
                        <input name="setlabel" class="form-control form-control-sm shadow-sm edit_toggle" style="max-width:300px;display: none;" value="${value.setlabel || ``}" />
                    </span>
                </div>
                <div class="position-absolute" style="top:0;right:5px;">
                    <button class="edit_orgsettings_button btn btn-sm btn-info rounded-circle custom_shadow py-1 px-2 m-1" type="button" title="Update">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="cancel_orgsettings_button btn btn-sm btn-secondary custom_shadow m-1" style="display: none;" type="button" title="Update">
                        Cancel
                    </button>
                    <button class="save_orgsettings_button btn btn-sm btn-success custom_shadow m-1" style="display: none;" type="button" title="Update">
                        Save
                    </button>
                </div>
            </div>`)
            .appendTo(target);

        (function ($) {
            // $(`.delete_orgsettings_button`, template).click(function(e) {
            // 	if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

            // 	let json = {
            // 		orgno: value.orgno,
            // 		setid: value.setid
            // 	};

            // 	remove_an_orgsetting(json, target);
            // });

            $(`.edit_orgsettings_button`, template).click(function (e) {
                $(`.edit_toggle, button`, template).toggle();
            });

            $(`.cancel_orgsettings_button`, template).click(function (e) {
                $(`.edit_toggle, button`, template).toggle();
            });

            $(`.save_orgsettings_button`, template).click(function (e) {
                let json = {
                    orgno: value.orgno,
                    setid: value.setid,
                    setlabel: $(`[name="setlabel"]`, template).val()
                };

                if (json.setlabel == value.setlabel) {
                    toastr.error(`Set Label haven't change.`);
                    return;
                }

                setup_orgsettings(json, target);
            });
        })(jQuery);
    });
}

function setup_orgsettings(json, target) {
    let fileurlElem = target.siblings(`fieldset`).find(`.orgsettings_form [name="fileurl"]`);

    if (json.fileurl && json.fileurl.size) {
        json.fileurl = fileurlElem.data(`response`).fileurl;
    } else {
        json.fileurl = null;
    }

    $.post(`${publicAccessUrl}php/ui/orgsettings/setup_orgsettings.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            fileurlElem.data(`response`, null);
            get_orgsettings({
                orgno: json.orgno
            }, target);
        }
    }, `json`);
}

function remove_an_orgsetting(json, target) {
    $.post(`${publicAccessUrl}php/ui/orgsettings/remove_an_orgsetting.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            get_orgsettings({
                orgno: json.orgno
            }, target);
        }
    }, `json`);
}

// USER ORG MODULE

function get_userorg_detail(json, target) {
    target.empty();

    $.post(`php/ui/organization/get_userorg_detail.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            show_userorg_detail(resp.results, target);

            //sessionStorage.setItem(`orgusermodule_${json.orgno}`, JSON.stringify(resp.results));
        }
    }, `json`);
}

function show_userorg_detail(data, target) {
    target.data(`userorg_detail`, data);
    let isOwner = data.find(a => a.userno == USERNO && a.permissionlevel == 7 && a.ucatno == 19);
    let regex_time = /\d\d[:]\d\d/g;

    $.each(data, (index, value) => {
        let shift = value.shifttitle || ``;
        if (value.starttime && value.endtime) {
            shift += ` <span class="text-nowrap">[${value.starttime.match(regex_time)} - ${value.endtime.match(regex_time)}]</span>`;
        } else if (value.starttime) {
            shift += ` <span class="text-nowrap">Start: ${value.starttime.match(regex_time)}</span>`;
        } else if (value.endtime) {
            shift += ` <span class="text-nowrap">End: ${value.endtime.match(regex_time)}</span>`;
        }

        let permissionlevel = ``;
        if (value.permissionlevel == 0 || value.permissionlevel == null) {
            permissionlevel = `Employee`;
        } else if (value.permissionlevel == 1) {
            permissionlevel = `Senior Employee`;
        } else if (value.permissionlevel == 3) {
            permissionlevel = `Manager`;
        } else if (value.permissionlevel == 7) {
            permissionlevel = `Admin`;
        }

        let template = $(`<tr class="${value.isactive == 1 ? `table-success` : `table-danger`}">
                <td>${1 + index}</td>
                <td class="text-nowrap">
                    <div class="text-primary font-weight-bold">
                        ${value.firstname || ``}
                        ${value.lastname || ``}
                        (${value.userno != USERNO ? value.username : `you`})
                    </div>
                    ${value.designation ? `<div>Designation: ${value.designation}</div>` : ``}
                    ${value.uuid ? `<div>ID: ${value.uuid}</div>` : ``}
                    ${value.ucattitle ? `<div>Role: ${value.ucattitle}; Permission : ${permissionlevel}</div>` : ``}
                    
                </td>
                <td>
                    ${value.moduletitle ? `<div><span class="text-nowrap">${value.moduletitle}</span></div>` : ``}
                    ${value.supervisor_name ? `<div>Supervisor: <span class="text-nowrap">${value.supervisor_name}</span></div>` : ``}
                </td>
                <td>
                    ${value.dailyworkinghour ? `<div class="text-nowrap">${value.dailyworkinghour}Hour / Day</div>` : ``}
                    <div class="text-nowrap">
                    ${value.hourlyrate ? `${value.hourlyrate} / Hour ` : ``}
                    ${value.monthlysalary ? ` ${value.monthlysalary} / Month ` : ``}
                    </div>
                    ${value.timeflextitle ? `<span class="badge badge-alternate" style="text-transform:none;">${value.timeflextitle}</span>` : ``}
                </td>
                <td>
                    
                    ${value.timezone ? `
                    <div class="text-center mb-1"><span class="badge badge-info" style="text-transform:none;">${value.timezone}</span></div>
                    ` : ``}
                    ${shift}
                    
                </td>
                <td class="text-center">
                    ${value.userno != USERNO && isOwner ? `<button class="toggle_userorg_button btn btn-sm ${value.isactive == 1 ? `btn-danger` : `btn-success`} ripple custom_shadow"
                            type="button" title="${value.isactive == 1 ? `Deactivate` : `Activate`} user">
                            ${value.isactive == 1 ? `Deactivate` : `Activate`}
                        </button>` : ``}
                </td>
                <td class="text-center">
                    ${isOwner ? `<button class="edit_userorg_button mx-1 my-1 btn btn-sm btn-info custom_shadow"
                        type="button" title="Update module">
                            Edit
                        </button>
                        <button class="user_workinglocation mx-1 my-1 btn btn-sm btn-warning custom_shadow" type="button" title="Restrict working location">
                            Working Location
                        </button>` : ``}
                </td>
            </tr>`)
            .data(value)
            .appendTo(target);

        (function ($) {
            $(`.toggle_userorg_button`, template).click(function (e) {
                let json = {
                    orgno: value.orgno,
                    userno: value.userno,
                    moduleno: value.moduleno,
                };

                toggle_userorg_activation(json, target);
            });

            $(`.edit_userorg_button`, template).click(function (e) {
                let modal = $(`#userorg_setup_modal`).modal(`show`).find(`.modal-title`).html(`Update User Organization`);
                let form = $(`#userorg_setup_modal_form`)
                    .trigger("reset")
                    .data({
                        uono: value.uono,
                        orgno: value.orgno,
                        userOrgInfoTbody: target
                    });

                let supervisorSelect = $(`[name="supervisor"]`, form).empty().append(`<option value="">Select...</option>`);
                $.each(data, (_i, userOrg) => {
                    $(`<option value="${userOrg.userno}">
                            ${userOrg.firstname}
                            ${userOrg.lastname || ``}
                            [${userOrg.username}]
                        </option>`)
                        .appendTo(supervisorSelect);
                });

                $(`[name]`, form).each((i, elem) => {
                    let elementName = $(elem).attr("name");

                    if (elementName == `foruserno` && value.hasOwnProperty(`userno`)) {
                        $(elem).val(value.userno);
                    } else if (value[elementName] != null) {
                        $(elem).val(value[elementName]);
                    }
                });
            });

            $('.user_workinglocation', template).click(async function () {
                let wl_modal = $('#userorg_workinglocation_modal').modal('show')
                    .find(`.modal-title`)
                    .html(`Restrict User Working Location`);
                $('#userorg_workinglocation_modal_form').data(value);

                let json = {
                    userno: value.userno,
                    orgno: value.orgno,
                };

                // settings
                get_available_org_working_locations(json).then((resp) => {
                    if (resp.results) {
                        display_org_working_locations(resp.results);
                    }
                });

                // user data
                get_user_working_locations(json).then((resp) => {
                    if (resp.error) {
                        toastr.warning(resp.message);
                        return;
                    }

                    let working_locations = resp.results;
                    display_user_working_ocation(working_locations, json);
                });
            });
        })(jQuery);
    });
}

$(`#userorg_setup_modal_form`).submit(function (e) {
    e.preventDefault();
    setup_userorg($(this), $(this).data(`userOrgInfoTbody`));
});

function setup_userorg(form, target) {
    let json = Object.fromEntries((new FormData(form[0])).entries());
    json.uono = Number(form.data(`uono`)) || -1;
    json.orgno = Number(form.data(`orgno`)) || -1;
    json.purchaseno = Number(form.data(`purchaseno`)) || -1;

    if (json.orgno <= 0) {
        toastr.error(`Select an organization.`);
        return;
    }

    if (json.uono <= 0 && json.purchaseno <= 0) {
        toastr.error(`Select a package.`);
        return;
    }

    let url = `${publicAccessUrl}php/ui/organization/add_userorg.php`;
    if (json.uono > 0) {
        url = `${publicAccessUrl}php/ui/organization/update_userorg.php`;
    }

    $.post(url, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            form.trigger(`reset`);
            $(`#userorg_setup_modal`).modal(`hide`);

            if (json.uono <= 0) {
                get_my_valid_packages({
                    orgno: json.orgno
                }, $(`[name="purchaseno"]`));
            }
            get_userorg_detail({
                orgno: json.orgno
            }, target);
        }
    }, `json`);
}

function toggle_userorg_activation(json, target) {
    $.post(`${publicAccessUrl}php/ui/organization/toggle_userorg_activation.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            get_userorg_detail({
                orgno: json.orgno
            }, target);
        }
    }, `json`);
}

// WORKING LOCATION

class OrgWorkingLocation extends BasicCRUD {
    constructor(settings) {
        super(settings);
        this.toggleStatusURL = settings.toggleStatusURL;
    }

    get(json = {}) {
        this.targetContainer.empty();
        this.orgno = json.orgno;

        $.post(this.readURL, json, (resp) => {
            if (resp.error) {
                // toastr.error(resp.message);

                $(`<th colspan="${this.targetContainer.closest(`table`).find(`thead th`).length}">
                        <div class="text-center text-secondary w-100">
                            <div class="py-4">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                                <h5 class="text-500 font-weight-normal mb-0">${resp.message || `You haven't added any ${this.topic} yet.`}</h5>
                            </div>
                        </div>
                    </th>`)
                    .appendTo(this.targetContainer);
            } else {
                this.data = resp.results;
                this.show(this.data);
            }
        }, "json");
    }

    successCallback(resp) {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            this.get({
                orgno: this.orgno
            });
            if (this.setupModal.is(`:visible`)) {
                this.setupModal.modal(`hide`);
            }
        }
    }

    show(data) {
        let thisObj = this;

        $.each(data, (index, value) => {
            let template = $(`<tr class="${value.active == 0 ? `table-danger` : `table-success`}">
                    <td>${1 + index}</td>
                    <td>${value.locname}</td>
                    <td>${value.loclat}</td>
                    <td>${value.loclon}</td>
                    <td class="text-center">
                        <button class="activation_button btn btn-sm ${value.active == 0 ? `btn-success` : `btn-danger`} ripple custom_shadow m-1" type="button" title="Activation">
                            ${value.active == 0 ? `Activate` : `Deactivate`}
                        </button>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center p-0">
                            <button class="edit_button btn btn-sm btn-info ripple rounded-circle custom_shadow mr-2" type="button" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete_button btn btn-sm btn-danger ripple rounded-circle custom_shadow" type="button" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`)
                .data(value)
                .appendTo(this.targetContainer);

            (function ($) {
                $(`.activation_button`, template).click((e) => {
                    if (confirm("Are you sure you want to change this status?")) {
                        $.post(thisObj.toggleStatusURL, {
                            orgno: thisObj.orgno,
                            locno: value.locno,
                            active: (value.active == 1) ? 0 : 1
                        }, resp => thisObj.successCallback(resp), `json`);
                    }
                });

                thisObj.editButtonTrigger(template, value);

                thisObj.deleteButtonTrigger(template, value);
            })(jQuery);
        });
    }

    setupFormSubmitTrigger() {
        this.setupForm.submit((e) => {
            e.preventDefault();
            let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());
            json.orgno = this.orgno;
            let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
            let url = this.setupForm.data(`action`);

            if (tablePK > 0) {
                json[this.tablePK] = tablePK;
            }

            $.post(url, json, resp => this.successCallback(resp), "json");
        });
    }

    deleteButtonTrigger(template, value) {
        $(`.delete_button`, template).click((e) => {
            if (!confirm(`Your are going to delete this working location. Are you sure to proceed?`)) return;

            $.post(this.deleteURL, {
                [this.tablePK]: value[this.tablePK],
                orgno: this.orgno
            }, resp => this.successCallback(resp), "json");
        });
    }
}

function orgWorkingLocationSettings() {
    return {
        readURL: `${publicAccessUrl}php/ui/workinglocation/get_all_workinglocation.php`,
        createURL: `${publicAccessUrl}php/ui/workinglocation/setup_workinglocation.php`,
        updateURL: `${publicAccessUrl}php/ui/workinglocation/setup_workinglocation.php`,
        deleteURL: `${publicAccessUrl}php/ui/workinglocation/remove_workinglocation.php`,
        toggleStatusURL: `${publicAccessUrl}php/ui/workinglocation/toggle_activation_workinglocation.php`,
        setupModal: `#org_working_location_modal`,
        topic: `Location`,
        tablePK: `locno`
    };
}

// USER WORKING LOCATION

function get_available_org_working_locations(json) {
    return new Promise((resolve, reject) => {
        $.post(`${publicAccessUrl}php/ui/workinglocation/get_active_workinglocation.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
                reject(resp.message);
            }

            resolve(resp);

        }, `json`);
    });
}

function display_org_working_locations(data) {
    let locno = $('form [name="locno"]').empty();
    $.each(data, (i, elm) => {
        let option = `<option value='${elm.locno}'>${elm.locname}</option>`;
        locno.append(option);
    });
}

function get_user_working_locations(json) {
    return new Promise((resolve, reject) => {
        $.post(`${publicAccessUrl}php/ui/userattlocset/get_user_wherework_future.php`, json, resp => {
            if (resp.error) {
                toastr.error(resp.message);
                reject(resp.message);
            }

            resolve(resp);

        }, `json`);
    });
}

function display_user_working_ocation(working_locations, json) {
    let target = $(`#table_working_location tbody`).empty();

    $.each(working_locations, (i, loc) => {
        let template = $(`<tr>
                <td>${loc.locname}</td>
                <td>${loc.mindistance} Meters</td>
                <td>${loc.starttime}</td>
                <td>${loc.endtime}</td>
                <td class="py-0">
                    <button class="delete_button btn btn-danger btn-block ripple custom_shadow" type="button">Remove</button>
                </td>
            </tr>`)
            .appendTo(target);

        (function ($) {
            $(`.delete_button`, template).click(function (e) {
                if (!confirm(`Your are going to delete this user working location. Are you sure to proceed?`)) return;

                json.attlocno = loc.attlocno;
                remove_userattlocset(json);
            });
        })(jQuery);
    });
}

$('#userorg_workinglocation_modal_form').submit(function (e) {
    e.preventDefault();
    let json = {
        userno: $(this).data().userno,
        orgno: $(this).data().orgno,
        locno: $('[name="locno"]', this).val(),
        mindistance: $('[name="mindistance"]', this).val(),
        starttime: $('[name="starttime"]', this).val(),
        endtime: $('[name="endtime"]', this).val(),
    }

    setup_user_workinglocation(json);
});

function setup_user_workinglocation(json) {
    $.post(`${publicAccessUrl}php/ui/userattlocset/setup_userattlocset.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);

            get_user_working_locations({
                orgno: json.orgno,
                userno: json.userno
            }).then((resp) => {
                if (resp.error) {
                    toastr.warning(resp.message);
                    return;
                }

                let working_locations = resp.results;
                display_user_working_ocation(working_locations, {
                    orgno: json.orgno,
                    userno: json.userno
                });
            });
        }
    }, `json`);
}

function remove_userattlocset(json) {
    $.post(`php/ui/userattlocset/remove_userattlocset.php`, json, resp => {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);

            delete json.attlocno;
            get_user_working_locations(json).then((resp) => {
                if (resp.error) {
                    toastr.warning(resp.message);
                    return;
                }

                let working_locations = resp.results;
                display_user_working_ocation(working_locations, json);
            });
        }
    }, `json`);
}

// TASK TYPE || ORG STORY PHASE

class OrgStoryPhase extends BasicCRUD {
    get(json = {}) {
        this.targetContainer.empty();
        this.orgno = json.orgno;

        $.post(this.readURL, json, (resp) => {
            if (resp.error) {
                // toastr.error(resp.message);

                $(`<th colspan="${this.targetContainer.closest(`table`).find(`thead th`).length}">
                        <div class="text-center text-secondary w-100">
                            <div class="py-4">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                                <h5 class="text-500 font-weight-normal mb-0">${resp.message || `You haven't added any ${this.topic} yet.`}</h5>
                            </div>
                        </div>
                    </th>`)
                    .appendTo(this.targetContainer);
            } else {
                this.data = resp.results;
                this.show(this.data);
            }
        }, "json");
    }

    successCallback(resp) {
        if (resp.error) {
            toastr.error(resp.message);
        } else {
            toastr.success(resp.message);
            this.get({
                orgno: this.orgno
            });
            if (this.setupModal.is(`:visible`)) {
                this.setupModal.modal(`hide`);
            }
        }
    }

    show(data) {
        let thisObj = this;

        $.each(data, (index, value) => {
            let template = $(`<tr>
                    <td>${1 + index}</td>
                    <td>${value.storyphasetitle}</td>
                    <td class="text-center">
                        ${value.colorcode ? `<span class="text-white rounded shadow-sm px-2 py-1" style="background-color:${value.colorcode};">${value.colortitle}</span>` : ``}
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center p-0">
                            <button class="edit_button btn btn-sm btn-info ripple rounded-circle custom_shadow mr-2" type="button" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete_button btn btn-sm btn-danger ripple rounded-circle custom_shadow" type="button" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`)
                .data(value)
                .appendTo(this.targetContainer);

            (function ($) {
                thisObj.editButtonTrigger(template, value);

                thisObj.deleteButtonTrigger(template, value);
            })(jQuery);
        });
    }

    setupFormSubmitTrigger() {
        this.setupForm.submit((e) => {
            e.preventDefault();
            let json = Object.fromEntries((new FormData(this.setupForm[0])).entries());
            json.orgno = this.orgno;
            let tablePK = Number(this.setupForm.data(this.tablePK)) || 0;
            let url = this.setupForm.data(`action`);

            if (tablePK > 0) {
                json[this.tablePK] = tablePK;
            }

            $.post(url, json, resp => this.successCallback(resp), "json");
        });
    }

    deleteButtonTrigger(template, value) {
        $(`.delete_button`, template).click((e) => {
            if (!confirm(`Your are going to delete this record. Are you sure to proceed?`)) return;

            $.post(this.deleteURL, {
                [this.tablePK]: value[this.tablePK],
                orgno: this.orgno
            }, resp => this.successCallback(resp), "json");
        });
    }
}

function orgStoryPhaseSettings() {
    return {
        readURL: `${publicAccessUrl}php/ui/storyphase/get_org_storyphase.php`,
        createURL: `${publicAccessUrl}php/ui/storyphase/setup_org_storyphase.php`,
        updateURL: `${publicAccessUrl}php/ui/storyphase/setup_org_storyphase.php`,
        deleteURL: `${publicAccessUrl}php/ui/storyphase/remove_org_storyphase.php`,
        setupModal: `#org_storyphase_modal`,
        topic: `Task Type`,
        tablePK: `storyphaseno`
    };
}

// ORG LOGO

$(document).on(`change`, `[name="fileurl"]`, function (e) {
    show_image_cropping_modal($(this), {
        title: `Settings related file`
    });
});

function show_image_cropping_modal(target, data, callback) {
    console.log(target, data);
    const image = $("#photo_preview_image")[0];
    if (image.cropper != undefined) {
        image.cropper.destroy();
    }
    $('#photo-update-hint').html(data.title);
    $("#crop_image_button")
        .data(`target`, target)
        .data(`upload_data`, data);

    $("#crop_image_button").unbind('on_image_uploaded');
    $("#crop_image_button").on('on_image_uploaded', function (e, upload_response) {
        console.log("received upload completed");
        console.log("upload_response", upload_response);

        if (callback) {
            callback(upload_response);
        }
    });

    if (data.prev_fileurl && data.prev_fileurl.fileurl) {
        $('#photo_preview_previous_image').attr('src', data.prev_fileurl.fileurl);
    } else {
        $('#photo_preview_previous_image').attr('src', DEFAULT_PHOTO);
    }
    $('#photo_preview_image').attr('src', DEFAULT_PHOTO);
    $('#image_cropping_modal').modal('show');
}