define(['jquery'], function ($) {
    "use strict";

    //===== Preloader
    $(window).on("load", function () {
        setTimeout(function () {
            $(".preloader").css({
                "opacity": "0",
                "display": "none"
            });
        }, 500);
    });

    /*=====================================
    Sticky + Back to Top
    ======================================= */
    $(window).on("scroll", function () {
        var scrollTop = $(this).scrollTop();

        // show or hide the back-to-top button
        if (scrollTop > 50) {
            $(".scroll-top").css("display", "flex");
        } else {
            $(".scroll-top").css("display", "none");
        }
    });

    //===== Mobile Menu Button
    $(".mobile-menu-btn").on("click", function () {
        $(this).toggleClass("active");
    });

});
