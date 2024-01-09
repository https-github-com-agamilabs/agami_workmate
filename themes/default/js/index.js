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
      data: function (params) {
        return {
          search_key: params.term,
          pageno: params.page || 1,
          limit: 20,
        };
      },
      processResults: function (data, params) {
        params.pageno = params.page || 1;

        $.each(data.results, (index, value) => {
          value.id = value.postcode;
          value.text = `[${value.postcode}] ${value.po}, ${value.ps}, ${value.districtname}`;
        });

        return data;
      },
      cache: false,
    },
  })
  .on("select2:select", function (e) {
    let data = e.params.data;
    // console.log(data);
    localStorage.setItem("local_postcode", JSON.stringify(data));
    $(`[name="storeno"]`).val(null).trigger("change");
  });

let localPostcode = JSON.parse(localStorage.getItem(`local_postcode`)) || {
  id: 4000,
  text: "[4000] Chittagong GPO, Chittagong Sadar, Chattogram",
  postcode: 4000,
  po: "Chittagong GPO",
  ps: "Chittagong Sadar",
  districtno: 40,
  districtname: "Chattogram",
  iscity: 0,
};

if (
  postcodeSelect.find(`option[value="${localPostcode.postcode}"]`).length == 0
) {
  postcodeSelect.append(
    new Option(
      `[${localPostcode.postcode}] ${localPostcode.po}, ${localPostcode.ps}, ${localPostcode.districtname}`,
      localPostcode.postcode,
      true,
      true
    )
  );
}
postcodeSelect.val(localPostcode.postcode).trigger(`change`);

pop_nearest_pharmacy();

function pop_nearest_pharmacy() {
  $(`.medicine_stores_row`).empty();

  let json = {
    postcode: $(`[name="postcode"]`).val(),
    pageno: 1,
    limit: 6,
  };

  $.post(
    `${publicAccessUrl}php/ui/api/pop_nearest_pharmacy.php`,
    json,
    (resp) => {
      if (resp.error) {
        // toastr.error(resp.message);
      } else {
        show_nearest_pharmacy(resp.results);
      }
    },
    `json`
  );
}

function show_nearest_pharmacy(data) {
  let target = $(`.medicine_stores_row`);

  $.each(data, (index, value) => {
    let column = $(`<div class="col-md-6">
                            <div class="card shadow-3-strong mb-3">
								<div class="card-body">
									<h6 class="text-primary mb-0" style="text-transform: none;">${
                    value.title || ``
                  }</h6>
									<div class="small my-1">
                                        ${
                                          value.street && value.street.length
                                            ? `<span>${value.street}</span>`
                                            : ``
                                        }${
      value.postcode
        ? `, <span>[${value.postcode}] ${value.po}, ${value.ps}, ${value.districtname}</span>`
        : ``
    }${
      value.country && value.country.length
        ? `, <span>${value.country}</span>`
        : ``
    }
                                    </div>
									${
                    value.loclat
                      ? `<div>
											<a href="${link}&query=${value.loclat}%2C${value.loclon}" target="_blank" class="btn btn-warning btn-sm ripple custom_shadow" title="View Store In Map">
												<i class="fas fa-map-marked mr-2"></i> View In Map
											</a>
										</div>`
                      : ``
                  }
								</div>
							</div>
                        </div>`).appendTo(target);
  });
}

get_stats();

function get_stats() {
  $.post(
    `${publicAccessUrl}php/ui/api/get_stats.php`,
    (resp) => {
      if (resp.error) {
        // toastr.error(resp.message);
      } else {
        show_stats(resp.results);
      }
    },
    `json`
  );
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

  $.post(
    `${publicAccessUrl}php/ui/api/get_gallery.php`,
    json,
    (resp) => {
      if (resp.error) {
        // toastr.error(resp.message);
      } else {
        show_gallery(resp.data);
      }
    },
    `json`
  );
}

function show_gallery(data) {
  let target = $(`.medilife-gallery-area`);

  $.each(data, (index, value) => {
    let item = $(`<div class="${value.catno == 1 ? `` : `single-gallery-item`}">
                        ${
                          value.catno == 2
                            ? `<img src="${
                                value.thumbnailimageurl || value.imageurl || ``
                              }" alt="${value.image_title}">
                                <div class="view-more-btn">
                                    <a href="${
                                      value.imageurl
                                    }" class="btn gallery-img">See More +</a>
                                </div>`
                            : `<iframe src="${value.imageurl}" title="${value.image_title}" style="width:100%;" frameborder="0" allowfullscreen></iframe>`
                        }
                    </div>`).appendTo(target);
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
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
      },
    });

    $("[data-delay]").each(function () {
      var anim_del = $(this).data("delay");
      $(this).css("animation-delay", anim_del);
    });

    $("[data-duration]").each(function () {
      var anim_dur = $(this).data("duration");
      $(this).css("animation-duration", anim_dur);
    });
  }

  if ($.fn.magnificPopup) {
    $(".gallery-img").magnificPopup({
      type: "image",
    });
    $(".popup-video").magnificPopup({
      disableOn: 700,
      type: "iframe",
      mainClass: "mfp-fade",
      removalDelay: 160,
      preloader: false,
      fixedContentPos: false,
    });
  }
}

$.get(
  `${publicAccessUrl}themes/default/index.json`,
  (data) => {
    show_data(data);
  },
  `json`
);

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

  if (
    sections.appointment &&
    sections.appointment.title &&
    sections.appointment.title.length
  ) {
    let section = $(`.section_appointment`);
    let appointment = sections.appointment;

    $(`.section_title`, section).html(appointment.title || ``);
    $(`.section_subtitle`, section).html(appointment[`sub-title`] || ``);
    $(`.section_description`, section).html(appointment.description || ``);
  }

  if (
    sections.service &&
    sections.service.title &&
    sections.service.title.length
  ) {
    $(`.services_row`).empty();
    show_service_data(sections.service, `.section_services`, `.services_row`);
  }

  // if (sections[`home-service`] && sections[`home-service`].title && sections[`home-service`].title.length) {
  //     $(`.home_services_row`).empty();
  //     show_service_data(sections[`home-service`], `.section_home_services`, `.home_services_row`);
  // }

  // if (sections.pharmacy && sections.pharmacy.title && sections.pharmacy.title.length) {
  //     let section = $(`.section_pharmacy`);
  //     let pharmacy = sections.pharmacy;

  //     $(`.section_title`, section).html(pharmacy.title || ``);
  //     $(`.section_subtitle`, section).html(pharmacy[`sub-title`] || ``);
  //     $(`.section_description`, section).html(pharmacy.description || ``);
  // }
}

function show_hero_carousel_data(data) {
  let target = $(`.hero-slides`);

  $.each(data, (index, value) => {
    let slide =
      $(`<div class="single-hero-slide bg-img bg-overlay-white" style="background-image: url(${
        value.imageurl || ``
      });">
                        <div class="container h-100">
                            <div class="row h-100 align-items-center">
                                <div class="col-12">
                                    <div class="hero-slides-content">
                                        <h2 data-animation="fadeInUp" data-delay="100ms">${
                                          value.title || ``
                                        }</h2>
                                        <h3 data-animation="fadeInUp" data-delay="200ms">${
                                          value[`sub-title`] || ``
                                        }</h3>
                                        <h6 data-animation="fadeInUp" data-delay="400ms">${
                                          value[`subsub-title`] || ``
                                        }</h6>
                                        <a href="#" class="btn medilife-btn mt-50" data-animation="fadeInUp" data-delay="700ms">${
                                          value[`extra-button`] || ``
                                        }</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`).appendTo(target);

    (function ($) {
      $(`[href="#"]`, slide).click(function (e) {
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
      navText: [
        '<i class="ti-angle-left"></i>',
        '<i class="ti-angle-right"></i>',
      ],
      dots: true,
      autoplay: false,
      autoplayTimeout: 5000,
      smartSpeed: 1000,
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
                                    <i class="${
                                      value.icon || `fas fa-user-nurse`
                                    }"></i>
                                </div>
                                <div class="service-content">
                                    <h5>${value.title || ``}</h5>
                                    <p>${value[`short-description`] || ``}</p>
                                </div>
                            </div>
                        </div>`).appendTo(target);
  });
}

const EPOCH = new Date(0);
const EPOCH_YEAR = EPOCH.getUTCFullYear();
const EPOCH_MONTH = EPOCH.getUTCMonth();
const EPOCH_DAY = EPOCH.getUTCDate();

const calculateAge = (birthDate, toDate = new Date()) => {
  const diff = new Date(
    new Date(toDate).getTime() - new Date(birthDate).getTime()
  );
  const age = {
    years: Math.abs(diff.getUTCFullYear() - EPOCH_YEAR),
    months: Math.abs(diff.getUTCMonth() - EPOCH_MONTH),
    days: Math.abs(diff.getUTCDate() - EPOCH_DAY),
  };

  return age.years > 0
    ? `${age.years} Years`
    : age.months > 0
    ? `${age.months} Months`
    : age.days > 0
    ? `${age.days} Days`
    : ``;
};

function formatTime(timeString = "00:00:00") {
  if (!timeString || !timeString.length) {
    return ``;
  }

  let H = +timeString.substr(0, 2);
  let h = H % 12 || 12;
  let ampm = H < 12 || H === 24 ? "AM" : "PM";
  return h + timeString.substr(2, 3) + ampm;
}

get_all_specialtycategory();

function get_all_specialtycategory() {
  $.post(
    `${publicAccessUrl}php/ui/api/get_all_specialtycategory.php`,
    (resp) => {
      if (resp.error) {
        // toastr.error(resp.message);
      } else {
        show_specialtycategory(resp.results);
      }
    },
    `json`
  );
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
      data,
    })
    .val(null)
    .trigger("change")
    .on("select2:select", function (e) {
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
    data: function (params) {
      return {
        search_key: params.term,
        pageno: params.page || 1,
        limit: 20,
        spno: $(`#patient_appointment_form [name="spno"]`).val(),
      };
    },
    processResults: function (data, params) {
      params.pageno = params.page || 1;

      $.each(data.results, (index, value) => {
        value.id = value.doctno;
        value.text = `${value.firstname} ${value.lastname || ``}`;
      });

      return data;
    },
    cache: false,
  },
  templateResult: (value) => {
    if (!value.id) {
      return value.text;
    }

    return $(`<div class="">
                            <div class="font-weight-bold">${value.firstname} ${
      value.lastname || ``
    }</div>
                            ${
                              value.specialty && value.specialty.length
                                ? `<div>${value.specialty}</div>`
                                : ``
                            }
                            <div>${value.countrycode} ${value.contactno}</div>
                        </div>`);
  },
  templateSelection: (value) => (value.id ? value.text : "Select doctor"),
};

$(`[name="doctno"]`)
  .select2(doctorSelect2Settings)
  .val(null)
  .trigger("change")
  .on("select2:select", function (e) {
    let data = e.params.data;
    // console.log(data);
    $(`#doctor_chamber_schedule_container`).empty();
    $(`#patient_appointment_form`).data(`doctno`, data.doctno);
    get_doctorchambers({
      doctno: data.doctno,
    });
  });

function get_doctorchambers(json) {
  $.post(
    `${publicAccessUrl}healthcare/php/ui/chamber/get_doctorchambers.php`,
    json,
    (resp) => {
      if (resp.error) {
        toastr.error(resp.message);
      } else {
        show_doctor_chamber_schedule(resp.results);
      }
    },
    `json`
  );
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

    let template =
      $(`<div class="custom-radio custom-control custom-control-inline">
							<input id="chamber_${
                value.chamberno
              }_radio" type="radio" name="chamberno" value="${
        value.chamberno
      }" class="custom-control-input" required>
							<label class="custom-control-label" for="chamber_${value.chamberno}_radio">
								<div>${value.chambername} (${value.countrycode} ${value.contacts})</div>
								${address.length ? `<div class="small">${address}</div>` : ``}
							</label>
						</div>`).appendTo(`#doctor_chamber_schedule_container`);

    $(`[name="chamberno"]`, template).data(value);
  });
}

$(document).on(
  `change`,
  `#patient_appointment_form [name="chamberno"]`,
  function (e) {
    let form = $(`#patient_appointment_form`);
    let scheduleSelect = $(`[name="wsno"]`, form).empty();

    let chamberData = $(this).data();

    $.each(chamberData.schedule, (indexInSchedule, schedule) => {
      $(`<option value="${schedule.wsno}">
							${schedule.weekday} [${formatTime(schedule.chambertimestart)} - ${formatTime(
        schedule.chambertimeend
      )}]
						</option>`)
        .data(schedule)
        .appendTo(scheduleSelect);
    });

    if (this.checked) {
      form.data(`chamberno`, this.value).data(`schedule`, chamberData.schedule);
    } else {
      form.data(`schedule`, []);
    }
  }
);

$(`#proceed_buttom`).click(function (e) {
  let form = $(`#patient_appointment_form`);

  let doctno = $(`[name="doctno"]`, form).val(),
    chamberno = $(`[name="chamberno"]:checked`, form).val(),
    scheduledate = $(`[name="scheduledate"]`, form).val(),
    wsno = $(`[name="wsno"]`, form).val();

  console.log({
    doctno,
    chamberno,
    scheduledate,
    wsno,
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

$(`#previous_buttom`).click(function (e) {
  $(`.patient_information.collapse`).collapse(`hide`);
  $(`.doctor_and_schedule.collapse`).collapse(`show`);
});

$(`#people_filter_button`).click(function (e) {
  e.preventDefault();
  let form = $(`#patient_appointment_form`);
  $(
    `[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`,
    form
  )
    .val(``)
    .prop(`disabled`, false);

  let json = {
    contactno: $(`[name="contactno"]`, form).val(),
  };

  if (json.contactno.length == 11 && json.contactno.startsWith(`01`)) {
    json.contactno = json.contactno.substring(1);
  } else if (json.contactno.length == 10 && json.contactno.startsWith(`1`)) {
  } else {
    toastr.error(`Invalid mobile no!`);
    return;
  }

  $.post(
    `${publicAccessUrl}php/ui/api/is_exist_people.php`,
    json,
    (resp) => {
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
    },
    `json`
  );
});

function show_existing_people_data(data) {
  let form = $(`#patient_appointment_form`).data(`people_data`, data);

  $(
    `[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`,
    form
  ).each((index, elem) => {
    let elemName = $(elem).attr(`name`);

    if (data.hasOwnProperty(elemName) && data[elemName]) {
      $(elem).val(data[elemName]).prop(`disabled`, true);
    }
  });

  $(`[name="age"]`, form).val(calculateAge(data.dob));
}

$(`#patient_appointment_form [name="faf"]`).change(function (e) {
  let form = $(`#patient_appointment_form`);
  $(`.people_div`, form).hide();
  $(`[name="peopleno"]`, form).val(null).trigger("change");
  $(`[name="firstname"],[name="lastname"]`, form).show();

  let people_data = form.data(`people_data`);
  if (!people_data) {
    return;
  }

  if (this.value != `0`) {
    $(
      `[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`,
      form
    )
      .val(``)
      .prop(`disabled`, false);

    let json = {
      contactno: people_data.contactno,
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
    return;
  }

  $.post(
    `${publicAccessUrl}php/ui/api/get_fafs.php`,
    json,
    (resp) => {
      if (resp.error) {
        toastr.error(resp.message);
      } else {
        $(`#patient_appointment_form [name="faf"]`).data(
          `fafs_of_${json.contactno}`,
          resp.result
        );
        show_fafs(resp.result);
      }
    },
    `json`
  );
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
      data,
    })
    .val(null)
    .trigger("change")
    .on("select2:select", function (e) {
      let data = e.params.data;
      // console.log(data);
      if (data.peopleno > 0) {
        show_existing_people_data(data);
      } else {
        $(
          `[name="firstname"],[name="lastname"],[name="dob"],[name="age"],[name="gender"],[name="bloodgroup"]`,
          form
        )
          .val(``)
          .prop(`disabled`, false);
      }
    });
}

$(`#patient_appointment_form [name="dob"]`).on(`input`, function (e) {
  $(`#patient_appointment_form [name="age"]`).val(calculateAge(this.value));
});

$(`#patient_appointment_form [name="scheduledate"]`).on(`input`, function (e) {
  let form = $(`#patient_appointment_form`);
  let scheduleSelect = $(`[name="wsno"]`, form);
  let data = $(form).data();
  let schedule = data.schedule.filter((a) => a.doctno == data.doctno);

  let weekday = new Date(this.value)
    .toLocaleDateString("en-us", {
      weekday: "short",
    })
    .toUpperCase();

  $(`option`, scheduleSelect).each((index, elem) => {
    if ($(elem).data(`weekday`) != weekday && !$(elem).hasClass(`d-none`)) {
      $(elem).addClass(`d-none`);
    } else if (
      $(elem).data(`weekday`) == weekday &&
      $(elem).hasClass(`d-none`)
    ) {
      $(elem).removeClass(`d-none`);
    }
  });

  if ($(`option:not(.d-none)`, scheduleSelect).length) {
    scheduleSelect.val($(`option:not(.d-none):first`, scheduleSelect).val());
    $(this).removeClass(`border-danger`);
  } else {
    scheduleSelect.val(``);
    $(this).addClass(`border-danger`);
    toastr.error(
      `No schedule available on '${weekday}' for this doctor. Only available for ${[
        ...new Set(schedule.map((a) => `'${a.weekday}'`)),
      ].join(`, `)}.`
    );
  }
});

$(`#get_serialno_button`).click(function (e) {
  let form = $(`#patient_appointment_form`);
  let data = $(form).data();

  let scheduledate = $(`[name="scheduledate"]`, form).val();
  if (!scheduledate.length) {
    toastr.error(`Schedule date not set properly.`);
    return;
  }

  let chambertimestart = $(`[name="wsno"] option:selected`, form).data(
    `chambertimestart`
  );
  if (!chambertimestart.length) {
    toastr.error(`Schedule not set properly.`);
    return;
  }

  let json = {
    doctno: data.doctno,
    chamberno: data.chamberno,
    scheduletime: `${scheduledate} ${chambertimestart}`,
  };

  $.post(
    `${publicAccessUrl}healthcare/php/ui/appointment/get_serialno.php`,
    json,
    (resp) => {
      $(`[name="localserial"]`, form).val(Number(resp.localserial) + 1 || 1);
    },
    `json`
  );
});

$(`#patient_appointment_form`).submit(function (e) {
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

  let chambertimestart = $(`[name="wsno"] option:selected`, this).data(
    `chambertimestart`
  );
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
    return;
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
    .html(
      `<div class="d-flex"> <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Saving...</div>`
    );

  $.post(
    `${publicAccessUrl}healthcare/php/ui/appointment/add_appointment.php`,
    json,
    (resp) => {
      if (resp.error) {
        toastr.error(resp.message);
      } else {
        toastr.success(resp.message);
        reset_appointment_form();
        // location.reload();
      }
    },
    `json`
  ).always(() => {
    $(`:submit`, this)
      .prop("disabled", false)
      .html(`Make an Appointment <span>+</span>`);
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
