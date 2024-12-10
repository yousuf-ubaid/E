$(document).ready(function () {
  // ===== Scroll to Top ====
  $(window).scroll(function () {
    if ($(this).scrollTop() >= 50) {
      // If page is scrolled more than 50px
      $("#return-top").fadeIn(200); // Fade in the arrow
    } else {
      $("#return-top").fadeOut(200); // Else fade out the arrow
    }
  });

  // Return to top
  $("#return-top").click(function () {
    // When arrow is clicked
    $("body,html").animate(
      {
        scrollTop: 0, // Scroll to top of body
      },
      500
    );
  });

  $(".carousel-home-item").owlCarousel({
    loop: true,
    nav: true,
    dots: false,
    navText: "",
    lazyLoad: true,
    autoplay: false,
    autoplayTimeout: 2000,
    margin: 5,
    responsive: {
      0: {
        items: 2,
      },
      576: {
        items: 2,
      },
      768: {
        items: 3,
      },
    },
  });

  // Left Menu Toggle
  $("#left-sec-btn").click(function (e) {
    e.preventDefault();
    $("#left-sec-cover").toggleClass("open");
  });

  // images parallax
  $(".parallax-eff").parallax();

  //--------------------- end of document ready
});

$("#carousel-banner").carousel({
  pause: "false",
});

// scroll
$(".left-nav-scroll, .right-sec-scroll").mCustomScrollbar({
  scrollButtons: { enable: true },
  theme: "light-thick",
  scrollbarPosition: "outside",
});

// Tooltip
$('[data-toggle="tooltip"]').tooltip();
$('[data-content="popover"]').popover();

// Loader
$(window).on("load", function () {
  $(".loader-overlay").delay(2000).fadeOut("slow");
});
