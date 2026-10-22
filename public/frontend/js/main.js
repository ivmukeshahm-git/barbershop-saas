(function($) {
    "use strict";
    $('[data-toggle="tooltip"]').tooltip(); 
   
    /*** Menu js ***/
    $( '.dropdown-menu a.dropdown-toggle' ).on( 'click', function ( e ) {
        var $el = $( this );
        var $parent = $( this ).offsetParent( ".dropdown-menu" );
        if ( !$( this ).next().hasClass( 'show' ) ) {
            $( this ).parents( '.dropdown-menu' ).first().find( '.show' ).removeClass( "show" );
        }
        var $subMenu = $( this ).next( ".dropdown-menu" );
        $subMenu.toggleClass( 'show' );
        
        $( this ).parent( "li" ).toggleClass( 'show' );

        $( this ).parents( 'li.nav-item.dropdown.show' ).on( 'hidden.bs.dropdown', function ( e ) {
            $( '.dropdown-menu .show' ).removeClass( "show" );
        } );
        
         if ( !$parent.parent().hasClass( 'navbar-nav' ) ) {
            $el.next().css( { "top": $el[0].offsetTop, "left": $parent.outerWidth() - 4 } );
        }
        return false;
    });

    /*** header Mini Cart ***/
    $("#cart").on("click", function() {
        $(".shopping-cart").fadeToggle( "fast");
    });
    $("#s-click").on("click", function() {
        $(".search-2-open").fadeToggle( "fast");
    });

    /** POPUp video Function **/
    // $(function(){
    //     $(".pop-video").videoPopup({
    //         autoplay: 1,
    //         controlsColor: 'white',
    //         showVideoInformations: 0,
    //         width: 1000,
    //         customOptions: {
    //             rel: 0,
    //             end: 60
    //         }
    //     });
    // });
    /**** team member ***/
    var swiper = new Swiper('.swiper-slides', {
        effect: 'coverflow',
        initialSlide: 2,
        centeredSlides: true,
        loop: true,
        spaceBetween: 0,
        slidesPerView: 4,
        pagination: false,
        coverflowEffect: {
            rotate: 0,
            stretch: 0,
            depth: 100,
            modifier: 3,
            slideShadows: false,
        },
        autoplay: {
            delay: 3000,
        },  
        breakpoints: {      
          320: {       
             slidesPerView: 1,
             spaceBetween: 10     
          },         
          480: {       
             slidesPerView: 1,       
             spaceBetween: 20     
          },       
          640: {       
             slidesPerView: 2,       
             spaceBetween: 30     
          } ,
          768: {       
             slidesPerView: 2,       
             spaceBetween: 30     
          } 
       } 
    });

    //----------------- testimonial  -----------------//
    var owl = $('#testimonial');
    owl.owlCarousel({
        loop: true,
        nav: false,
        dots: true,
        margin: 30,
        autoplayHoverPause: true,
        lazyLoad: true,
        autoplay: true,
        autoplaySpeed: 600,
        responsive: {
            0: {
                items: 1
            },
            480: {
            items: 1

            },
            768: {
                items: 2
            },
            992: {
              items: 2
            },
            1200: {
                items: 2
            },
            1440: {
                items: 2
            }
        }
    })
    //----------------- Client  -----------------//
    var owl = $('.client-items');
        owl.owlCarousel({
            loop: true,
            nav: false,
            dots: false,
            margin: 30,
            autoplayHoverPause: true,
            lazyLoad: true,
            autoplay: true,
            autoplaySpeed: 600,
            responsive: {
                0: {
                    items: 2
                },
                480: {
                    items: 3
                },
                768: {
                    items: 4
                },
                992: {
                    items: 7
                },
                1200: {
                    items: 7
                },
                1440: {
                    items: 7
                }
            }
        })
    /***** slider 3 ***/
    var owl = $('#slider3');
        owl.owlCarousel({
            loop: true,
            nav: false,
            dots: false,
            margin:0,
            autoplayHoverPause: true,
            lazyLoad: true,
            autoplay: true,
            autoplaySpeed: 600,
            responsive: {
                0: {
                    items: 1
                },
                480: {
                items: 2

                },
                768: {
                    items: 3
                },
                992: {
                  items: 3
                },
                1100: {
                    items: 4
                },
                1200: {
                    items: 4
                },
                1440: {
                    items: 5
                }
            }
        })

    /*** Top To ****/
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('#back-top').fadeIn();
        }
        else {
            $('#back-top').fadeOut();
        }
    });
    $('#back-top').on('click', function() {
        $("html, body").animate( {
            scrollTop: 0
        }
        , 1000);
        return false;
    });
    // preloader 
    var winObj = $( window ),
    bodyObj = $( 'body' ),
    headerObj = $( 'header' );
    winObj.on( 'load', function()
    {
    var $preloader = $( '.peloaders' );
    $preloader.find( '.preloader' ).fadeOut();
    $preloader.delay( 350 ).fadeOut( 'slow' );
    });
}(jQuery))
