(function ($) {
  "use strict";

  // :: Index of Plugins Active Code :: //

  var $window = $(window);

  console.log("show_data called");
  show_data();

  // :: Preloader Active Code
  $window.on("load", function () {
    console.log("window loaded");

    $("#preloader").fadeOut("slow", function () {
      console.log("preloader fadeout");

      console.log("preloader removed");
      $(this).remove();
    });
  });

  // :: Fullscreen Active Code
  $window.on("resizeEnd", function () {
    console.log("resizeEnd called");
    $(".full_height").height($window.height());
  });

  $window
    .on("resize", function () {
      console.log("resize called");
      if (this.resizeTO) clearTimeout(this.resizeTO);
      this.resizeTO = setTimeout(function () {
        $(this).trigger("resizeEnd");
      }, 100);
      if ($.fn.sticky) {
        console.log("sticky re-loaded");
        if ($window.width() > 991.98) {
          $("#top_header_area").unstick();
          $("#stickyHeader").sticky({
            topSpacing: 0,
          });
        } else {
          $("#stickyHeader").unstick();
          $("#top_header_area").sticky({
            topSpacing: 0,
            zIndex: 9999,
          });
        }
      }
    })
    .trigger("resize");

  if ($.fn.sticky) {
    console.log("sticky loaded");

    if ($window.width() > 991.98) {
      $("#top_header_area").unstick();
      $("#stickyHeader").sticky({
        topSpacing: 0,
      });
    } else {
      $("#stickyHeader").unstick();
      $("#top_header_area").sticky({
        topSpacing: 0,
        zIndex: 9999,
      });
    }
  } else {
    console.log("sticky not loaded");
  }

  // :: Tooltip Active Code
  $('[data-toggle="tooltip"]').tooltip();

  // :: Nicescroll Active Code
  //   if ($.fn.niceScroll) {
  //     console.log("Nice Scroll loaded");
  //     $("body, textarea").niceScroll({
  //       cursorcolor: "#151515",
  //       cursorwidth: "6px",
  //       background: "#f0f0f0",
  //     });
  //   } else {
  //     console.log("Nice Scroll not loaded");
  //   }

  // :: Nice Select Active Code
  // if ($.fn.niceSelect) {
  //     $('select').niceSelect();
  // }

  // :: Owl Carousel Active Code
  if ($.fn.owlCarousel) {
    console.log("owlCarousel loaded");

    var welcomeSlide = $(".hero-slides");

    // $('.hero-slides').owlCarousel({
    //     items: 1,
    //     margin: 0,
    //     loop: true,
    //     nav: true,
    //     navText: ['Prev', 'Next'],
    //     dots: true,
    //     autoplay: false,
    //     autoplayTimeout: 5000,
    //     smartSpeed: 1000
    // });

    // welcomeSlide.on('translate.owl.carousel', function () {
    //     var slideLayer = $("[data-animation]");
    //     slideLayer.each(function () {
    //         var anim_name = $(this).data('animation');
    //         $(this).removeClass('animated ' + anim_name).css('opacity', '0');
    //     });
    // });

    welcomeSlide.on("translated.owl.carousel", function () {
      console.log("translated.owl.carousel called");

      var slideLayer = welcomeSlide
        .find(".owl-item.active")
        .find("[data-animation]");
      slideLayer.each(function () {
        var anim_name = $(this).data("animation");
        $(this)
          .addClass("animated " + anim_name)
          .css("opacity", "1");
      });
    });

    $("[data-delay]").each(function () {
      var anim_del = $(this).data("delay");
      $(this).css("animation-delay", anim_del);
    });

    $("[data-duration]").each(function () {
      var anim_dur = $(this).data("duration");
      $(this).css("animation-duration", anim_dur);
    });

    // $('.testimonials-slider').owlCarousel({
    //     items: 1,
    //     margin: 0,
    //     loop: true,
    //     nav: true,
    //     navText: ['<i class="ti-angle-left"></i>', '<i class="ti-angle-right"></i>'],
    //     dots: true,
    //     autoplay: true,
    //     autoplayTimeout: 5000,
    //     smartSpeed: 1000
    // });

    // $('.medilife-gallery-area').owlCarousel({
    //     items: 4,
    //     margin: 0,
    //     loop: true,
    //     autoplay: true,
    //     autoplayTimeout: 5000,
    //     smartSpeed: 1000,
    //     responsive: {
    //         0: {
    //             items: 1
    //         },
    //         768: {
    //             items: 2
    //         },
    //         992: {
    //             items: 3
    //         },
    //         1200: {
    //             items: 4
    //         }
    //     }
    // });
  } else {
    console.log("owlCarousel not loaded");
  }

  // :: Magnific Popup Active Code

  if ($.fn.magnificPopup) {
    console.log("magnificPopup loaded");

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
  } else {
    console.log("magnificPopup not loaded");
  }

  // :: MatchHeight Active Code
  if ($.fn.matchHeight) {
    console.log("matchHeight loaded");

    $(".equalize").matchHeight({
      byRow: true,
      property: "height",
    });
  } else {
    console.log("matchHeight not loaded");
  }

  // :: CounterUp Active Code
  if ($.fn.counterUp) {
    console.log("counterUp loaded");

    $(".counter").counterUp({
      delay: 10,
      time: 2000,
    });
  } else {
    console.log("counterUp not loaded");
  }

  // :: ScrollUp Active Code
  if ($.fn.scrollUp) {
    console.log("scrollUp loaded");
    $.scrollUp({
      scrollSpeed: 1000,
      easingType: "easeInOutQuart",
      scrollText: '<i class="fa fa-angle-up" aria-hidden="true"></i>',
    });
  } else {
    console.log("scrollUp not loaded");
  }

  // :: PreventDefault a Click
  $("a[href='#']").on("click", function ($) {
    $.preventDefault();
  });

  // :: wow Active Code
  if ($.fn.scrollUp) {
    new WOW().init();
  }
  // if ($window.width() > 767) {
  //     new WOW().init();
  // }
})(jQuery);
