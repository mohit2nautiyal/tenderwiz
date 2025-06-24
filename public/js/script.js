// public/js/script.js
$(document).ready(function () {
    // Sidebar Menu Active State
    $('#sidebar .side-menu.top li a').on('click', function (e) {
        $('#sidebar .side-menu.top li').removeClass('active');
        $(this).parent().addClass('active animate__animated animate__pulse');
    });

    // Toggle Sidebar
    $('#content nav .bx.bx-menu').on('click', function () {
        $('#sidebar').toggleClass('hide animate__animated animate__slideInLeft');
        $('#content').toggleClass('sidebar-hidden');
        if ($('#sidebar').hasClass('hide')) {
            $(this).addClass('bx-x').removeClass('bx-menu');
        } else {
            $(this).addClass('bx-menu').removeClass('bx-x');
        }
    });

    // Search Form Toggle (Mobile)
    $('#content nav form .form-input button').on('click', function (e) {
        if (window.innerWidth < 576) {
            e.preventDefault();
            $('#content nav form').toggleClass('show animate__animated animate__fadeIn');
            const $icon = $(this).find('.bx');
            if ($('#content nav form').hasClass('show')) {
                $icon.removeClass('bx-search').addClass('bx-x');
            } else {
                $icon.removeClass('bx-x').addClass('bx-search');
            }
        }
    });

    // Responsive Behavior
    if (window.innerWidth < 768) {
        $('#sidebar').addClass('hide');
    } else if (window.innerWidth > 576) {
        $('#content nav form .form-input button .bx').removeClass('bx-x').addClass('bx-search');
        $('#content nav form').removeClass('show');
    }

    $(window).on('resize', function () {
        if (window.innerWidth > 576) {
            $('#content nav form .form-input button .bx').removeClass('bx-x').addClass('bx-search');
            $('#content nav form').removeClass('show');
        }
    });

    // Dark Mode Toggle
    $('#switch-mode').on('change', function () {
        if ($(this).is(':checked')) {
            $('body').addClass('dark animate__animated animate__fadeIn');
        } else {
            $('body').removeClass('dark animate__animated animate__fadeIn');
        }
    });

    // Menu Hover Animation
    $('#sidebar .side-menu li a').hover(
        function () {
            $(this).addClass('animate__animated animate__bounceIn');
        },
        function () {
            $(this).removeClass('animate__animated animate__bounceIn');
        }
    );

    // Notification Pulse Animation
    setInterval(function () {
        $('#content nav .notification').toggleClass('animate__animated animate__pulse');
    }, 2000);
});