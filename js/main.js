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
$(function() {
    const main_link = $(".main_nav_link");
    const sub_block = $(".subnav-content");
    const sub_link = $(".subnav-content > a");
    $(main_link).click(function() {
        $(main_link).removeClass("active");
        $(sub_block).removeClass("active");
        $(sub_link).removeClass("active");
        $(this).addClass("active");
        $(this).siblings(sub_block).addClass("active");
    });
    $(sub_link).click(function() {
        $(sub_link).removeClass("active");
        $(sub_block).removeClass("active");
        $(this).addClass("active");
        $(this).parent(sub_link).addClass("active");
    });
});

$(function() {
    const avatar_button = $("#user_avatar");
    $(avatar_button).click(function() {
		$("#user_dropdown_menu").slideToggle(300);
    });
});
$(document).on("click", "[data-submit-login]", function(e) {
	$(this).blur();
	e.preventDefault();
	login = $('input[id=login]').val();
	passwd = $('input[id=passwd]').val();
	fa2code = $('input[id=fa2code]').val();
	$.post("//"+document.domain+"/public/login.php", { 'login': login, 'passwd': passwd, 'fa2code': fa2code }, function(json){
		data = JSON.parse(json);
		if(data.err == 'ok'){
			document.location.href="/";
		}else{
			$("div#error").html(data.mes);
			if($("div#error").css('display') == 'none'){
				$("div#error").show();
			}
		}
		//console.log(data);
	});
});
