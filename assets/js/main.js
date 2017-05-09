
"use strict";


/*---------------------------------------------*
 * SETTINGS
 ---------------------------------------------*/



//MENU HIDE
var hide_menu = false; // If you like to hide your menu set true

// Animation 
var wowAnimation = false;  //
// TWITTER ID
var twitterID = '695213657840607236';  //

// MailChimp OPTIN URL
var mailchimpUrl = ""; //Replace this with your own mailchimp post URL. Don't remove the "". Just paste the url inside "".  



/*---------------------------------------------*
 * PRELOADER
 ---------------------------------------------*/

    $(window).load(function () {
        $(".loaded").fadeOut();
        $(".preloader").delay(1000).fadeOut("slow");
    });





jQuery(document).ready(function ($) {




    /*---------------------------------------------*
     * STICKY HIDE NAVIGATION 
     ---------------------------------------------*/

    var windowWidth = $(window).width();
    if (windowWidth > 767) {

        if (hide_menu === true) {
            $('.navbar').addClass('hide-nav').hide();
            $(window).scroll(function () {
                if ($(this).scrollTop() > 200) {
                    $('.hide-nav').fadeIn(500);
                    $('.hide-nav').addClass('navbar');

                } else {
                    $('.hide-nav').fadeOut(500);
                    $('.hide-nav').removeClass('navbar');
                }
            });
        }
    }
    $('#navbar-collapse').find('a[href*=#]:not([href=#])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: (target.offset().top - 40)
                }, 1000);
                if ($('.navbar-toggle').css('display') != 'none') {
                    $(this).parents('.container').find(".navbar-toggle").trigger("click");
                }
                return false;
            }
        }
    });





    /*---------------------------------------------*
     * popup option
     ---------------------------------------------*/

    $('.home-video').magnificPopup({type: 'iframe'});

    var popupImages = $('.portfolio-item img');
    popupImages.each(function () {
        $(this).attr('data-mfp-src', $(this).attr('src'));
    });
    popupImages.magnificPopup({type: 'image'});


    /*---------------------------------------------*
     * Portfolio  Masonry
     ---------------------------------------------*/

    var $container = $('.portfolio');

    $container.imagesLoaded( function(){
        $container.masonry({
          itemSelector: '.portfolio-item',
          isAnimated: true
        });
    });

    /*---------------------------------------------*
     * STICKY scroll
     ---------------------------------------------*/

//    $.localScroll();



    /*---------------------------------------------*
     * tab related
     ---------------------------------------------*/

    function toggleChevron(e) {
        $(e.target)
                .prev('.panel-heading')
                .find("i.indicator")
                .toggleClass('glyphicon-minus glyphicon-plus');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleChevron);
    $('.panel-group').on('shown.bs.collapse', toggleChevron);




    /*---------------------------------------------*
     * Counter 
     ---------------------------------------------*/

    $('.statistic-counter').counterUp({
        delay: 10,
        time: 2000
    });






    /*---------------------------------------------*
     * Parallax
     ---------------------------------------------*/


    parallaxInit();

    function parallaxInit() {
        $('.home-wrap').parallax("30%", 0.3);
//        $('.testimonial').parallax("30%", 0.1);
//        $('.timeline-overview').parallax("30%", 0.1);
//        $('.tweets').parallax("30%", 0.1);
//        $('.calltoaction-bg').parallax("30%", 0.1);
//        $('.funfact').parallax("30%", 0.1);
//        $('.footer-img').parallax("30%", 0.1);
    }



    /*---------------------------------------------*
     * WOW
     ---------------------------------------------*/
    if (wowAnimation === true) {
        var wow = new WOW({
            mobile: false // trigger animations on mobile devices (default is true)
        });


        wow.init();
    }





    /*---------------------------------------------*
     * Twitter
     ---------------------------------------------*/


    var xs_tweet = {
        "id": twitterID,
        "maxTweets": 3, // maxx post will show 3
        "domId": 'tweet',
        "enableLinks": true,
        "showUser": true,
        "showTime": true,
        "dateFunction": '',
        "showRetweet": false,
        "customCallback": handleTweets,
        "showInteraction": false
    };
    function handleTweets(tweets) {
        var x = tweets.length;
        var n = 0;
        var element = document.getElementById('tweet');
        var html = '<div class="slides">';
        while (n < x) {
            html += '<div>' + tweets[n] + '</div>';
            n++;
        }
        html += '</div>';
        if ($('#tweet').length) {
            element.innerHTML = html;
        }
        /* Twits attached to owl-carousel */
        $("#tweet .slides").owlCarousel({
            responsiveClass: true,
            autoplay: false,
            items: 1
        });

    }
    if (self == top) { // its load with iframe or not
        twitterFetcher.fetch(xs_tweet);
    }






    /* ------------------------------------------------
     ---  MAILCHIMP                 ------
     --------------------------------------------------- */

    $('#mailchimp').ajaxChimp({
        callback: mailchimpCallback,
        url: mailchimpUrl //Replace this with your own mailchimp post URL. Don't remove the "". Just paste the url inside "".  
    });
    function mailchimpCallback(resp) {
        var rm = "0 -";
        var msgs = resp.msg.replace(rm, '');
        if (resp.result === 'success') {
            $('.subscription-success').html('<h4><i class="fa fa-check success-msg"></i> ' + msgs + '</h4>').fadeIn(1000);
            $('.subscription-error').fadeOut(500);
        } else if (resp.result === 'error') {
            $('.subscription-error').html('<h4><i style="color:red" class="fa fa-times error-msg"></i> ' + msgs + '</h4>').fadeIn(1000);

        }
    }




    /*---------------------------------------------*
     * Form input style
     ---------------------------------------------*/

    $('#fancy-inputs input[type="text"]').blur(function () {
        if ($(this).val().length > 0) {
            $(this).addClass('white');
        } else {
            $(this).removeClass('white');
        }
    });
    $('#fancy-inputs textarea').blur(function () {
        if ($(this).val().length > 0) {
            $(this).addClass('white');
        } else {
            $(this).removeClass('white');
        }
    });



});