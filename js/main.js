$(function(){
    const hslider = $('#hslider');
    if ($('.slide', hslider).length > 1) {

        $('.slideshow', hslider).bxSlider({
            auto: true,
            autoHover: true,
            mode: 'fade',
            speed: 1000
        });
    }
});

/*
			$('.torrent-block').mouseover(function() {
				$( this ).fadeOut( 100 );
				
				console.log('in');
			});
			
			$( '.torrent-block' ).mouseout(function() {
				$( this ).fadeIn( 500 );
				console.log('out');
			});
*/
