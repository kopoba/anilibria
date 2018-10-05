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
		$("#user_dropdown_menu").slideDown(300);
    });
	$(document).mouseup(function(e) {
		const container = $("#user_dropdown_menu");
		if (!container.is(e.target) && container.has(e.target).length === 0) {
			container.slideUp(300);
		}
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
	});
});
$(document).on("click", "[data-submit-passwdrecovery]", function(e) {
	$(this).blur();
	e.preventDefault();
	var submit = $(this);
	var mail = $('input[id=email]').val();	
	if($("div.coinhive-captcha").css('display') == 'none'){
		submit.hide(); // recaptcha has some delay
		grecaptcha.execute('6LfA2mUUAAAAAAbcTyBWyTXV2Kp6vi247GywQF1A').then(function(token) {
			$.post("//"+document.domain+"/public/password_recovery.php", { 'mail': mail, 'g-recaptcha-response': token }, function(json){
				data = JSON.parse(json);
				$("div#error").html(data.mes);
				if($("div#error").css('display') == 'none'){
					$("div#error").show();
				}
				if(data.err != 'ok'){
					$("div.coinhive-captcha").show();
				}
				submit.show();
			});
		});
	}else{
		token = $('input[name=coinhive-captcha-token]').val();
		$.post("//"+document.domain+"/public/password_recovery.php", { 'mail': mail, 'coinhive-captcha-token': token }, function(json){
			data = JSON.parse(json);
			$("div#error").html(data.mes);
			if($("div#error").css('display') == 'none'){
				$("div#error").show();
			}
			if(data.err == 'ok'){
				$("div.coinhive-captcha").show();
			}
		});
	}
});
$(document).on("click", "[data-submit-register]", function(e) {
	$(this).blur();
	e.preventDefault();
	var submit = $(this);
	var mail = $('input[id=email]').val();
	var login = $('input[id=login]').val();	
	if($("div.coinhive-captcha").css('display') == 'none'){
		submit.hide(); // recaptcha has some delay
		grecaptcha.execute('6LfA2mUUAAAAAAbcTyBWyTXV2Kp6vi247GywQF1A').then(function(token) {
			$.post("//"+document.domain+"/public/registration.php", { 'login': login, 'mail': mail, 'g-recaptcha-response': token }, function(json){
				data = JSON.parse(json);
				$("div#error").html(data.mes);
				if($("div#error").css('display') == 'none'){
					$("div#error").show();
				}
				if(data.err != 'ok'){
					$("div.coinhive-captcha").show();
				}
				submit.show();
			});
		});
	}else{
		token = $('input[name=coinhive-captcha-token]').val();
		$.post("//"+document.domain+"/public/registration.php", { 'login': login, 'mail': mail, 'coinhive-captcha-token': token }, function(json){
			data = JSON.parse(json);
			$("div#error").html(data.mes);
			if($("div#error").css('display') == 'none'){
				$("div#error").show();
			}
			if(data.err == 'ok'){
				$("div.coinhive-captcha").show();
			}
		});
	}
});
