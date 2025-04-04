/* =================================================================
* Scrolling
*/
$(window).on('onscroll scrollstart touchmove', function(){
    $(window).trigger('scroll');
});
$(window).on('resize', function(){
    setTimeout(function(){
        $(window).trigger('scroll');
    }, 500);
});

function loadBackgroundImage($obj) {
    const screenWidth = $(window).width();
    let backgroundImage;

    if (screenWidth <= 768) {
        backgroundImage = $obj.attr('data-bg-small') || $obj.attr('data-bg');
    } else if (screenWidth <= 1600) {
        backgroundImage = $obj.attr('data-bg-medium') || $obj.attr('data-bg');
    } else {
        backgroundImage = $obj.attr('data-bg');
    }

    if (backgroundImage) {
        $obj.css('background-image', 'url(' + backgroundImage + ')');
    }
}

(function($) {
    "use strict";

    var isMobile = false;

    function checkIfMobile() {
        const userAgent = navigator.userAgent || navigator.vendor || window.opera;
        const mobileRegex = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i;
        const isMobileWidth = window.innerWidth <= 768;
        isMobile = mobileRegex.test(userAgent) || isMobileWidth;
        return isMobile;
    }

    isMobile = checkIfMobile();

    $(document).ready(function(){
        'use strict';

        /* =================================================================
        * Preloader
        */
        $('.preloader-close').on('click', function() {
            $('#preloader').fadeOut();
        });
        $('#preloader').fadeOut(500);
        
        /* =================================================================
        * Scroll
        */
        $('a#toTop').on('click', function(e){
            e.defaultPrevented;
            $('html, body').animate({scrollTop: '0px'});
            return false;
        });
        $('body').bind('touchmove', function(e){
            $(window).trigger('scroll');
        });
        $(window).on('onscroll scrollstart touchmove', function(){
            $(window).trigger('scroll');
        });
        $(window).scroll(function(){
            var scroll_1 = $('html, body').scrollTop();
            var scroll_2 = $('body').scrollTop();
            var scrolltop = scroll_1;
            if(scroll_1 == 0) scrolltop = scroll_2;
            
            if(scrolltop >= 200) $('a#toTop').css({bottom: '30px'});
            else $('a#toTop').css({bottom: '-40px'});
            if(scrolltop > 100) $('.navbar-fixed-top').addClass('fixed');
            else $('.navbar-fixed-top').removeClass('fixed');
        });
        $(window).trigger('scroll');

        /* =================================================================
        * COOKIES
        */
        if($('#cookies-notice').length){
            $('#cookies-notice button').on('click', function(){
                $.cookie('cookies_enabled', '1', {expires: 7});
                $('#cookies-notice').fadeOut();
            });
        }

        /* =================================================================
        * Mobile Menu
        */
        if($('#mobile-menu').length){
            $('#mobile-menu').meanmenu({
                meanMenuContainer: '.mobile-menu',
                meanScreenWidth: '1199',
                meanExpand: ['<i class="fal fa-plus"></i>'],
                meanContract: ['<i class="fal fa-minus"></i>'],
            });
        }

        /* =================================================================
        * Sidebar
        */
        $('.sidebar-toggle-btn').on('click', function() {
            $('.sidebar__area').addClass('sidebar-opened');
            $('.body-overlay').addClass('opened');
        });
        $('.sidebar__close-btn').on('click', function() {
            $('.sidebar__area').removeClass('sidebar-opened');
            $('.body-overlay').removeClass('opened');
        });

        /* =================================================================
        * Side Info
        */
        $('.side-info-close,.offcanvas-overlay').on('click', function() {
            $('.side-info').removeClass('info-open');
            $('.offcanvas-overlay').removeClass('overlay-open');
        });
        $('.side-toggle').on('click', function() {
            $('.side-info').addClass('info-open');
            $('.offcanvas-overlay').addClass('overlay-open');
        });

        $('.offset__btn').on('click', function() {
            $('.offset-content-wrapper').addClass('offset-show');
        });
        $('.offset-content-close').on('click', function() {
            $('.offset-content-wrapper').removeClass('offset-show');
        });

        /* =================================================================
        * Search
        */
        $('.search-open-btn').on('click', function() {
            $('.search__popup').addClass('search-opened');
        });
        $('.search-close-btn').on('click', function() {
            $('.search__popup').removeClass('search-opened');
        });

        /* =================================================================
        * Sticky Menu
        */
        $(window).scroll(function() {
            var Width = $(document).width();

            if ($('body').scrollTop() > 100 || $('html').scrollTop() > 100) {
                if (Width > 767) {
                    $('header').addClass('sticky');
                }
            } else {
                $('header').removeClass('sticky');
            }
        });

        $('.search-btn').on('click', function() {
            $('.search-box').toggleClass('show');
        });

        /* =================================================================
        * ISOTOPE
        */
        if($('.isotope').length){
            var $container = $('.isotope');
            var $resize = $('.isotope').attr('data-cols');
            $container.imagesLoaded(function() {
                $container.addClass('loaded').isotope({
                    layoutMode: 'masonry',
                    itemSelector: '.isotopeItem',
                    resizable: false,
                    masonry: {
                        columnWidth: $container.width() / $resize
                    }
                });
                $container.on('arrangeComplete', function(event, filteredItems){
                    $('.img-container', $container).imagefill();
                });
                $('.isotope-filter button').on('click', function(e){
                    $('.isotope-filter button').removeClass('active');
                    $(this).addClass('active');
                    var selector = $(this).attr('data-filter');
                    $container.isotope({
                        filter: selector,
                        animationOptions: {
                            duration: 300,
                            easing: 'easeOutQuart'
                        }
                    });
                    e.defaultPrevented;
                    return false;
                });
            });
        }
        
        /* =================================================================
        * PARALLAX
        */
        if($('.parallax-wrap').length){
            $('.parallax-wrap').each(function() {
                var $el = $(this);
                var background = $el.data('background');
                //$el.css({'background-image': 'url(' + background + ')'});
    
                // Create an intersection observer
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            // Element is in the viewport
                            let ticking = false;
                            
                            const updateBackgroundPosition = () => {
                                var scrollTop = $(window).scrollTop();
                                var windowHeight = $(window).height();
                                var elementTop = $el.offset().top;
                                var elementHeight = $el.height();
                                
                                var viewportPosition = elementTop - scrollTop;
                                var relativeScroll = scrollTop - elementTop + windowHeight;
    
                                var backgroundPositionY = -relativeScroll * 0.4;
    
                                $el.css({
                                    'background-image': 'url(' + background + ')',
                                    'background-position': '50% ' + backgroundPositionY + 'px'
                                });
    
                                ticking = false;
                            };
    
                            const onScroll = () => {
                                if (!ticking) {
                                    requestAnimationFrame(updateBackgroundPosition);
                                    ticking = true;
                                }
                            };
    
                            $(window).on('scroll.parallax', onScroll);
                        } else {
                            // Element is out of the viewport
                            $(window).off('scroll.parallax');
                        }
                    });
                }, {
                    root: null,
                    threshold: 0
                });
    
                // Observe the element
                observer.observe($el[0]);
            });   
        }
        
        /* =================================================================
        * MATCH HEIGHT
        */
        if($('.matchHeight').length){
            $('.matchHeight').matchHeight();
        }
        
        /* =================================================================
        * MAGNIFIC POPUP (MODAL)
        */
        if($('.popup-modal').length){
            $('.popup-modal').magnificPopup({
                type: 'inline',
                preloader: false,
                closeBtnInside: false,
                callbacks: {
                    open: function(){
                        
                        var content = $(this.content);
                        
                        if($('.owl-carousel', content).length){
                            $('.owl-carousel', content).each(function(){
                                $(this).addClass('owlWrapper').owlCarousel({
                                    items: $(this).data('items'),
                                    nav: $(this).data('nav'),
                                    dots: $(this).data('dots'),
                                    autoplay: $(this).data('autoplay'),
                                    mouseDrag: $(this).data('mousedrag'),
                                    rtl: $(this).data('rtl'),
                                    responsive : {
                                        0 : {
                                            items: 1
                                        },
                                        768 : {
                                            items: $(this).data('items')
                                        }
                                    }
                                });
                            });
                        }
                    }
                }
            });
            $(document).on('click', '.popup-modal-dismiss', function(e){
                e.defaultPrevented;
                $.magnificPopup.close();
            });
            $('.popup-modal').on('click', function(e){
                e.defaultPrevented;
            });
            $('.popup-modal.hide').trigger('click');
        }
        if($('.ajax-popup-link').length){
            $('.ajax-popup-link').each(function(){
                $(this).magnificPopup({
                    type: 'ajax',
                    ajax: {
                        settings: {
                            method: 'POST',
                            data: $(this).data('params')
                        }
                    }
                });
            });
        }

        /* =================================================================
        * IMAGE FILL
        */
        if($('.img-container').not('.lazyload').length){
            $('.img-container').not('.lazyload').imagefill();
        }

        /* =================================================================
        * LOAZYLOAD IMAGES
        */
        if($('.lazy:not(.img-container)').length){
            $('.lazy').Lazy();
        }
        if($('.img-container.lazy').length){
            $('.img-container.lazy img').Lazy();
            
            $('.img-container.lazy img').on('load', function(){
                if($(this).parents('.img-container').length > 0)
                    $(this).parents('.img-container').imagefill();
            });
        }

        /* =================================================================
        * OWL CAROUSEL
        */
        if($('.owl-carousel').length){
            $('.owlWrapper').each(function(){
                $(this).owlCarousel({
                    items: $(this).data('items'),
                    nav: $(this).data('nav'),
                    dots: $(this).data('dots'),
                    autoplay: $(this).data('autoplay'),
                    mouseDrag: $(this).data('mousedrag'),
                    rtl: $(this).data('rtl'),
                    responsive : {
                        0 : {
                            items: 1
                        },
                        768 : {
                            items: $(this).data('items')
                        }
                    }
                });
            });

            /**
             *  Main slider ********************************
             */
            $('.hero-1 .hero-contents h1, .hero-1 .hero-contents h2, .hero-1 .hero-contents h3, .hero-1 .hero-contents p').addClass('animated-text');
            $('.hero-1 .hero-contents .btn, .hero-1 .hero-contents .plus-text-btn').addClass('animated-text animated-btn');
            const $sliderDelay = 8000;
            const $heroSlider = $('.hero-1 .hero-slider-active').owlCarousel({
                items: 1,
                dots: false,
                loop: true,
                autoplayTimeout: $sliderDelay,
                autoplay: true,
                nav: true,
                navText: ['<i class="fal fa-long-arrow-left"></i>PREV', 'Next<i class="fal fa-long-arrow-right"></i>'],
            });

            $heroSlider.on('changed.owl.carousel', function(event) {
                const $currentOwlItem = $('.owl-item').eq(event.item.index);
                const $currentSlide = $currentOwlItem.find('.single-slide');
                loadBackgroundImage($currentSlide);
                $heroSlider.trigger('stop.owl.autoplay');
                $heroSlider.trigger('play.owl.autoplay', [$sliderDelay]);
            });

            $(".owl-carousel").on("initialized.owl.carousel", () => {
                setTimeout(() => {
                    $(".owl-item.active .animated-text").addClass("is-initial is-transitioned");

                    $heroSlider.trigger('stop.owl.autoplay');
                    $heroSlider.trigger('play.owl.autoplay', [$sliderDelay]);

                    if(!isMobile || $(window).width() > 768) {
                        const $firstSlide = $('.owl-item.active .single-slide');
                        loadBackgroundImage($firstSlide);
                    }
                }, 50);
            });

            $heroSlider.on("changed.owl.carousel", e => {
                $(".animated-text").removeClass("is-transitioned is-initial");

                const $currentOwlItem = $(".owl-item").eq(e.item.index);
                $currentOwlItem.find(".animated-text").addClass("is-transitioned");
            });

            $heroSlider.trigger('initialized.owl.carousel');

        }

        /* =================================================================
        * LAZY LOADER
        */
        if($('.lazy-wrapper').length){
            $('.lazy-wrapper').each(function(){
                $(this).lazyLoader({
                    loader: $(this).data('loader'),
                    mode: $(this).data('mode'),
                    limit: $(this).data('limit'),
                    total: $(this).data('total'),
                    variables: $(this).data('variables'),
                    isIsotope: $(this).data('is_isotope'),
                    more_caption: $(this).data('more_caption')
                });
            });
        }

        if($('.lazy-video').length){
            const $lazyVideos = $('.lazy-video');
            const observer = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const $video = $(entry.target);
                        const $source = $video.find('source');
                        $source.attr('src', $source.data('src'));
                        $video[0].load();
                        observer.unobserve(entry.target);
                    }
                });
            });

            $lazyVideos.each(function() {
                observer.observe(this);
            });
        }
        
        $('[data-bg], [data-bg-medium], [data-bg-small]').each(function() {
            var $obj = $(this);
    
            var backgroundImage = $obj.css('background-image');
    
            if (!backgroundImage || backgroundImage === 'none') {
                loadBackgroundImage($obj);
            }
        });

        /* =====================================================================
        * APPEAR ANIMATION
        */
        $('[data-anim]').each(function() {
            var obj = $(this);
    
            if (!isMobile) {
                // Bind appear event directly to the elements
                obj.appear();
    
                obj.on('appear', function() {
                    var delay = obj.data('delay') ? obj.data('delay') : 1;
                    var animation = obj.data('anim') ? obj.data('anim') : 'animate__fadeIn';
    
                    if (delay > 1) obj.css('animation-delay', delay + 'ms');
    
                    // Add Animate.css classes
                    obj.addClass('animate__animated').addClass(animation);
    
                    setTimeout(function() {
                        obj.removeClass('appear').addClass('appear-visible');
                    }, delay);
                });
            } else {
                obj.removeClass('appear').addClass('appear-visible');
            }
            
            $(window).trigger('scroll');
        });

        /* =====================================================================
        * SHARE BUTTONS
        */
        if($('.share-block').length){
            $('.share-block').cShare({
                description: $(this).data('text'),
                show_buttons: ['fb','twitter','pinterest','email']
            });
        }
        
        /*==================================================================
        * RECAPTCHA V3
        */
        if ($('#g-recaptcha-response').length) {
            var pKey = $('meta[name="recaptcha_pkey"]').attr('content');
            
            var s = document.createElement('script');
            s.src = 'https://www.google.com/recaptcha/api.js?render=' + pKey;
            document.head.appendChild(s);
            
            $('#g-recaptcha-response').parents('form').on('submit', function(e) {
                e.preventDefault();
                // Function to initialize reCAPTCHA
                grecaptcha.ready(function() {
                    grecaptcha.execute(pKey, { action: 'homepage' }).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                        e.target.submit();
                    });
                });
            });
        }

        /*==================================================================
        * GOOGLE MAP
        */
        if ($('#contact-map-wrap').length) {
            var apiKey = $('meta[name="gmaps_api_key"]').attr('content');
            if (!apiKey) {
                console.error('API key not found');
                return;
            }
            // Add the Google Maps script with async and defer
            (g => {
                var h, a, k, p = 'The Google Maps JavaScript API',
                    c = 'google', l = 'importLibrary', q = '__ib__',
                    m = document, b = window;
                b = b[c] || (b[c] = {});
                var d = b.maps || (b.maps = {}),
                    r = new Set, e = new URLSearchParams,
                    u = () => h || (h = new Promise(async (f, n) => {
                        await (a = m.createElement('script'));
                        e.set('libraries', [...r] + '');
                        for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                        e.set('callback', c + ".maps." + q);
                        a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                        d[q] = f;
                        a.onerror = () => h = n(Error(p + ' could not load.'));
                        a.nonce = m.querySelector('script[nonce]')?.nonce || "";
                        m.head.append(a);
                    }));
                d[l] ? console.warn(p + ' only loads once. Ignoring:', g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n));
            })({ key: apiKey, v: 'weekly' });
            
            initMap();
        }
    });

    let map;
    async function initMap() {
        const gmaps_id = 'contact-map-wrap';
        if ($('#' + gmaps_id).length) {
            const overlayTitle = 'Agencies';
    
            const { Map } = await google.maps.importLibrary('maps');
            const { AdvancedMarkerElement } = await google.maps.importLibrary('marker');
            const { places } = await google.maps.importLibrary('places');
            const { geometry } = await google.maps.importLibrary('geometry');
            const { Geocoder } = await google.maps.importLibrary('geocoding');
            const { DrawingManager } = await google.maps.importLibrary('drawing');
    
            map = new Map(document.getElementById(gmaps_id), {
                mapId: "DEMO_MAP_ID",
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                streetViewControl: true,
                scaleControl: false,
                zoom: 12
            });
    
            var bounds = new google.maps.LatLngBounds();
            var infowindow = new google.maps.InfoWindow({ content: 'loading...' });
            const markerElement = document.getElementById('map-marker');
            for (let i = 0; i < pms_locations.length; i++) {
                if (pms_locations[i][2] !== undefined && pms_locations[i][3] !== undefined) {
                    const content = '<div class="infoWindow">' + pms_locations[i][0] + '<br>' + pms_locations[i][1] + '</div>';
                    const myLatlng = { lat: pms_locations[i][2], lng: pms_locations[i][3] };
    
                    const marker = new AdvancedMarkerElement({
                        map,
                        position: myLatlng,
                        title: overlayTitle,
                        content: markerElement
                    });
    
                    google.maps.event.addListener(marker, 'click', (function (content) {
                        return function () {
                            infowindow.setContent(content);
                            infowindow.open(map, this);
                        };
                    })(content));
    
                    if (pms_locations.length > 1) {
                        bounds.extend(myLatlng);
                        map.fitBounds(bounds);
                    } else {
                        map.setCenter(myLatlng);
                    }
                } else {
                    const geocoderInstance = new Geocoder();
                    const info = pms_locations[i][0];
                    const addr = pms_locations[i][1];
                    const latLng = pms_locations[i][1];
                    (function (info, addr) {
                        geocoderInstance.geocode({ 'address': latLng }, function (results) {
                            const myLatlng = results[0].geometry.location;
                            const marker = new AdvancedMarkerElement({
                                map,
                                position: myLatlng,
                                title: overlayTitle
                            });
    
                            const $content = '<div class="infoWindow">' + info + '<br>' + addr + '</div>';
                            google.maps.event.addListener(marker, 'click', function () {
                                infowindow.setContent($content);
                                infowindow.open(map, this);
                            });
    
                            if (pms_locations.length > 1) {
                                bounds.extend(myLatlng);
                                map.fitBounds(bounds);
                            } else {
                                map.setCenter(myLatlng);
                            }
                        });
                    })(info, addr);
                }
            }
        }
    }
})(jQuery); // End jQuery
