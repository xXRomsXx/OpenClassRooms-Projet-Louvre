$(window).on('load', function(){

    $('#menu-xs').hide();
    $('#message-flash').hide();

    /*                  Click menu hamburger                    */

   $('#icon').on('click', function(){

       $('#icon').toggleClass('isActive');
       $('#menu-xs').fadeToggle(300);

   });

    /*                  Scrollspy                   */

    $(function() {

        $('#myScrollspy a').on('click', function(e) {

            e.preventDefault();
            var hash = this.hash;
            $('html, body').animate({

                scrollTop: $(this.hash).offset().top

            }, 800, function(){

                window.location.hash = hash;

            });

        });

        $('nav a').on('click', function(e) {

            e.preventDefault();
            var hash = this.hash;
            $('html, body').animate({

                scrollTop: $(this.hash).offset().top

            }, 800, function(){

                window.location.hash = hash;

            });

        });

    });

});

/*                  Google map API                  */

    window.initMap = function() {

        var louvre = {lat: 48.860294, lng: 2.338629};
        var map = new google.maps.Map(document.getElementById('block-API'), {

            zoom: 15,
            center: louvre

    });

    var marker = new google.maps.Marker({

        position: louvre,
        map: map

    });

};