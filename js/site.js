/*
 * Copyright (C) 2009 Joel Sutherland.
 * Liscenced under the MIT liscense
 */

// Filterable
(function($) {
  $.fn.filterable = function(settings) {
    settings = $.extend({
      useHash: true,
      animationSpeed: 0,
      show: {
        height: 'show',
        opacity: 'show'
      },
      hide: {
        height: 'hide',
        opacity: 'hide'
      },
      useTags: true,
      tagSelector: '.article_filter a',
      selectedTagClass: 'current',
      allTag: 'all'
    }, settings);
    return $(this).each(function() {
      $(this).bind('filter', function(e, tagToShow) {
        if (settings.useTags) {
          $(settings.tagSelector).removeClass(settings.selectedTagClass);
          $(settings.tagSelector + '[href=' + tagToShow + ']').addClass(settings.selectedTagClass);
        }
        $(this).trigger('filterarticle', [tagToShow.substr(1)]);
      });
      $(this).bind('filterarticle', function(e, classToShow) {
        if (classToShow === settings.allTag) {
          $(this).trigger('show');
        } else {
          $(this).trigger('show', ['.' + classToShow]);
          $(this).trigger('hide', [':not(.' + classToShow + ')']);
        }
        if (settings.useHash) {
          location.hash = '#' + classToShow;
        }
      });
      $(this).bind('show', function(e, selectorToShow) {
        $(this).children(selectorToShow).animate(settings.show, settings.animationSpeed);
      });
      $(this).bind('hide', function(e, selectorToHide) {
        $(this).children(selectorToHide).animate(settings.hide, settings.animationSpeed);
      });
      if (settings.useHash) {
        if (location.hash !== '') {
          $(this).trigger('filter', [location.hash]);
        } else {
          $(this).trigger('filter', ['#' + settings.allTag]);
        }
      }
      if (settings.useTags) {
        $(settings.tagSelector).click(function() {
          $('#articles_wrap').trigger('filter', [$(this).attr('href')]);
          $(settings.tagSelector).removeClass('current');
          $(this).addClass('current');
        });
      }
    });
  };
})(jQuery);





(function($) {
  $.fn.bannerImage = function(settings) {
    settings = $.extend({}, settings);

    return $(this).each(function() {

      var content = $('.banner_inner', $(this)),
        blur = $('.banner_effect', $(this)),
        wHeight = $(window).height();

      $(window).on('resize', function() {
        wHeight = $(window).height();
      });

      window.requestAnimFrame = (function() {
        return window.requestAnimationFrame ||
          window.webkitRequestAnimationFrame ||
          window.mozRequestAnimationFrame ||
          function(callback) {
            window.setTimeout(callback, 1000 / 60);
          };
      })();

      /**
       * Scroller
       */
      function Scroller() {
        this.latestKnownScrollY = 0;
        this.ticking = false;
      }

      Scroller.prototype = {
        init: function() {
          window.addEventListener('scroll', this.onScroll.bind(this), false);
        },
        onScroll: function() {
          this.latestKnownScrollY = window.scrollY;
          this.requestTick();
        },
        requestTick: function() {
          if (!this.ticking) {
            window.requestAnimFrame(this.update.bind(this));
          }
          this.ticking = true;
        },
        update: function() {
          var currentScrollY = this.latestKnownScrollY;
          this.ticking = false;

          var slowScroll = currentScrollY / 6,
            blurScroll = currentScrollY * 2.5;

          content.css({
            'transform': 'translateY(-' + slowScroll + 'px)',
            '-moz-transform': 'translateY(-' + slowScroll + 'px)',
            '-webkit-transform': 'translateY(-' + slowScroll + 'px)'
          });

          blur.css({
            'opacity': blurScroll / wHeight
          });
        }
      };
      var scroller = new Scroller();
      scroller.init();
    });
  };
})(jQuery);





jQuery(document).ready(function($) {
  'use strict';



  $('#page_header').bannerImage();



  // $('#main', '.blog').filterable();

  // if ($('body').hasClass('blog')) {
  //   $('#child_links a').click(function() {
  //     $('h1.entry-title').text($(this).text());
  //   });
  // }

  // Floating Menu
  if ($('#child_links').length) {
    (function() {
      var offset = $('#child_links').offset(),
        marginTop = $('#child_links').css('margin-top');

      var topPadding = 200;
        $(window).scroll(function() {
          if ($(window).scrollTop() > (offset.top-topPadding)) {
            $('#child_links').stop().css({
              marginTop: $(window).scrollTop() - offset.top + topPadding
            });
          } else {
            $('#child_links').stop().css({
              marginTop: marginTop
            });
          }
        });
    })();
  }

  // add caret to drop menu
  $('ul.sub-menu').each(function() {
    $(this).parent('li').addClass('has_submenu');
  });

  $('#kt_contact_widget').submit(function(e) {

    var formData = new FormData($('#kt_contact_widget')[0]),
      actionUrl = $(this).attr('action');

    e.preventDefault();
    $.ajax({
      type: 'POST',
      xhr: function() { // custom xhr
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) { // check if upload property exists
          myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // for handling the progress of the upload
        }
        return myXhr;
      },
      url: actionUrl,
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(data) {
        console.log(data);
        if (!data.status) {
          $('#kt_contact_success').hide();
          $('#kt_contact_error').html(data.errors[0].msg).show(500);
          $('#upload_progress').hide(250);
        } else {
          $('#kt_contact_error').hide();
          $('#kt_contact_success').slideDown(500).delay(3500).slideUp(500);
          $('#kt_contact_widget').get(0).reset();
        }
      },
      error: function(data) {
        console.log(data);
      }
    });
  });

  function progressHandlingFunction(e) {
    if (e.lengthComputable) {
      $('progress').attr({
        value: e.loaded,
        max: e.total
      });
    }
  }





  $('#contactform').submit(function(e) {
    //console.log(e);
    $('#upload_progress').slideDown(250);
    var formData = new FormData($('#contactform')[0]);
    var actionUrl = $(this).attr('action');

    e.preventDefault();
    $.ajax({
      type: 'POST',
      xhr: function() { // custom xhr
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) { // check if upload property exists
          myXhr.upload.addEventListener('progress', progressHandlingFunction, false); // for handling the progress of the upload
        }
        return myXhr;
      },
      url: actionUrl,
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(data) {
        console.log(data);
        if (!data.status) {
          $('#server_success_msg').hide();
          $('#server_fail_msg').css('line-height', '5').html(data.errors[0].msg).show('slow');
          $('#upload_progress').hide(250);
        } else {
          $("#server_fail_msg").hide();
          $("#server_success_msg").html("Your email was sent with success").show("slow");
          $('#upload_progress').hide(250);
          $('#contactform').get(0).reset();
        }
      },
      error: function(data) {
        console.log('there was an error: ' + data);
      }
    });
  });



  $('#kt_newsletter_widget #email').focus(function() {
    $('#kt_newsletter_widget #first_name, #kt_newsletter_widget #last_name, #kt_newsletter_widget .submit_wrap').slideDown(500);
  });



  $(document.getElementById('main')).css('padding-bottom', $(document.getElementById('footer')).outerHeight());


}); //jQuery document ready
