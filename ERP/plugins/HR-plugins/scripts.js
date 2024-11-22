;(function($) {

"use strict";

var $body = $('body');
var $head = $('head');
var $header = $('#header');
var transitionSpeed = 300;
var marker = 'img/marker.png';



// Mediaqueries
// ---------------------------------------------------------
var XS = window.matchMedia('(max-width:767px)');
var SM = window.matchMedia('(min-width:768px) and (max-width:991px)');
var MD = window.matchMedia('(min-width:992px) and (max-width:1199px)');
var LG = window.matchMedia('(min-width:1200px)');
var XXS = window.matchMedia('(max-width:480px)');
var SM_XS = window.matchMedia('(max-width:991px)');
var LG_MD = window.matchMedia('(min-width:992px)');









    $('.candidate-skills .toggle a').on('click', function(events){
      events.preventDefault(),
      $(this).toggleClass('active'),
      $(this).parent().next().children('.toggle-content').slideToggle(300);


    });

    $('.accordion-content .toggle a').on('click', function(events){
      events.preventDefault(),
      $(this).toggleClass('active'),
      $(this).parent().parent().siblings('.toggle-content').slideToggle(300);


    });

    $('.search-skill-select .accordion-content .toggle a').on('click', function(events){
      events.preventDefault(),
      $(this).parent().toggleClass('active'),
      $(this).parent().next('.toggle-content').slideToggle(300),
      $(this).parent().siblings('.toggle-content').slideUp(300);


    });


      $('.accordion').each(function () {

        $(this).find('ul > li > a').on('click', function (event) {
          event.preventDefault();

          var $this = $(this),
            $li = $this.parent('li'),
            $div = $this.siblings('div'),
            $siblings = $li.siblings('li').children('div');

          if (!$li.hasClass('active')) {
            $siblings.slideUp(250, function () {
              $(this).parent('li').removeClass('active');
            });

            $div.slideDown(250, function () {
              $li.addClass('active');
            });
          } else {
            $div.slideUp(250, function () {
              $li.removeClass('active');
            });
          }
        });

      });


      $('.toggle-content-client').hide();


      $('.toggle-details a').on('click', function(e){
        e.preventDefault();

        $(this).parent().siblings('.toggle-content-client').slideToggle(350);
        $(this).parent().toggleClass('active');

      });






function mobileHeaderSearchToggle(SM_XS) {
  if (!SM_XS.matches) {
    $headerSearchToggle.removeAttr('style');
  }
}



// Advanced Search
// ---------------------------------------------------------
var $advancedSearchBar = $('.header-search-bar');

$advancedSearchBar.each(function () {
  var $this = $(this);

  $this.find('.toggle').on('click', function (event) {
    event.preventDefault();

    if (!$this.hasClass('active')) {
      $this.addClass('active');
      $this.find('.advanced-form').slideDown();
    } else {
      $this.removeClass('active');
      $this.find('.advanced-form').slideUp();
    }
  });

  function moveAdvancedBarSelect(XS) {
    if (XS.matches) {
      $this.find('.advanced-form .container').prepend($this.find('.hsb-select'));
    } else {
      $this.find('.hsb-select').appendTo($this.find('.hsb-container'));
    }
  }

  moveAdvancedBarSelect(XS);
  XS.addListener(moveAdvancedBarSelect);





});










// Responsive Videos
// ---------------------------------------------------------
if ($.fn.fitVids) {
  $('.fitvidsjs').fitVids();
}






// Advanced Search Range Slider
// ---------------------------------------------------------
if ($.fn.slider) {
  $('.header-search-bar .range-slider .slider, .header-search .range-slider .slider').each(function () {
    var $this = $(this),
      min = $this.data('min'),
      max = $this.data('max'),
      current = $this.data('current');

    $this.slider({
      range: 'min',
      min: min,
      max: max,
      step: 1,
      value: current,
      slide: function (event, ui) {
        $this.parent('.range-slider').find('.last-value > span').html(ui.value);
      }
    });
  });
}



// Accordion
// ---------------------------------------------------------
$('.accordion').each(function () {

  $(this).find('ul > li > a').on('click', function (event) {
    event.preventDefault();

    var $this = $(this),
      $li = $this.parent('li'),
      $div = $this.siblings('div'),
      $siblings = $li.siblings('li').children('div');

    if (!$li.hasClass('active')) {
      $siblings.slideUp(250, function () {
        $(this).parent('li').removeClass('active');
      });

      $div.slideDown(250, function () {
        $li.addClass('active');
      });
    } else {
      $div.slideUp(250, function () {
        $li.removeClass('active');
      });
    }
  });

});








// Candidates Item
// ---------------------------------------------------------
$('.candidates-item').each(function () {
  var $item = $(this),
    $content = $item.find('.content'),
    $toggle = $item.find('.top-btns .toggle');

  $toggle.on('click', function (event) {
    event.preventDefault();

    if ($item.hasClass('active')) {
      $content.slideUp();
      $item.removeClass('active');
      $toggle.removeClass('fa-minus').addClass('fa-plus');
    } else {
      $content.slideDown();
      $item.addClass('active');
      $toggle.removeClass('fa-plus').addClass('fa-minus');
    }
  });

  $item.find('.read-more').on('click', function (event) {
    event.preventDefault();

    $content.slideDown();
    $item.addClass('active');
    $toggle.removeClass('fa-plus').addClass('fa-minus');
  });
});




// Jobs Filters Range Slider
// ---------------------------------------------------------
if ($.fn.slider) {
  $('.jobs-filter-widget .range-slider .slider, .compare-price-filter-widget .range-slider .slider').each(function () {
    var $this = $(this),
      min = $this.data('min'),
      max = $this.data('max');

    $this.slider({
      range: true,
      min: min,
      max: max,
      step: 1,
      values: [min, max],
      slide: function (event, ui) {
        $(this).parent().find('.first-value').text(ui.values[0]);
        $(this).parent().find('.last-value').text(ui.values[1]);
      }
    });
  });
}

// Jobs Filters List
// ---------------------------------------------------------
$('.jobs-filter-widget .filter-list, .compare-price-filter-widget .filter-list').each(function () {
  var $this = $(this),
    $toggle = $this.siblings('.toggle');

  $this.find('li').each(function () {
    var $this = $(this);

    if ($this.children('ul').length > 0) {
      $this.addClass('has-submenu');
    }
  });

  $toggle.on('click', function (event) {
    event.preventDefault();

    $this.slideToggle();
    $toggle.toggleClass('active');
  });

  $this.find('.has-submenu > a').on('click', function (event) {
    event.preventDefault();

    var $thisLi = $(this).parent('li'),
      $thisUl = $thisLi.children('ul');

    if (!$thisLi.hasClass('active')) {
      $thisLi.addClass('active');
      $thisUl.slideDown();
    } else  {
      $thisLi.removeClass('active');
      $thisUl.slideUp().find('.has-submenu').removeClass('active').children('ul').slideUp();
    }
  });
});


// Jobs Views
// ---------------------------------------------------------
$('.jobs-view-toggle').each(function () {
  var $this = $(this),
    $items = $this.closest('.page-content').find('.jobs-item');

  $this.find('.btn').on('click', function (event) {
    event.preventDefault();

    var $this = $(this),
      layout = $this.data('layout');

    if (!$this.hasClass('active')) {
      if (layout == 'with-thumb') {
        $items.removeClass('compact').addClass('with-thumb');
      } else if (layout == 'compact') {
        $items.removeClass('with-thumb').addClass('compact');
      } else {
        $items.removeClass('with-thumb compact');
      }

      $this.addClass('active').parent('li').siblings('li').children('a').removeClass('active');
    }
  });
});

// Search/Filter Toggle
// ---------------------------------------------------------
$('.jobs-search-widget, .jobs-filter-widget').each(function () {
  var $this = $(this);

  $this.find('.widget-title').on('click', function (event) {
    if (XS.matches) {
      event.preventDefault();

      $this.find('.widget-content').slideToggle();
    }
  });
});

function searchFilterToggle(XS) {
  if (!XS.matches) {
    $('.jobs-search-widget .widget-content, .jobs-filter-widget .widget-content').removeAttr('style');
  }
}

searchFilterToggle(XS);
XS.addListener(searchFilterToggle);




}(jQuery));




