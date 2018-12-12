var site = document.domain;
var snd	= new Audio("//"+site+"/media/new.ogg");
var i	= 0;
var j	= 0;
var k	= 0;
var ban = 0;
var send_block = 0;

function runScript(e) 
{	
	var m = $('textarea[name=text]').val();
	var t =  $.trim(m);
	
	if(t.length > 1000){
		alert("превышен лимит, не больше 1000 символов");
		return false;
	}
	
    if (e.keyCode == 13 & t.length > 0 && !$("#send").hasClass("hidden") && send_block == 0) 
	{ // Отправка сообщения на ENTER
		send_block = 1;
			$.post("https://"+site+"/public/chat.php", { mes: $('textarea[name=text]').val(), do: "add"  }, function( data ){
				if(data != "SPAM"){
					$("#chat").append("<span class='self'>Я</span>: "+data+"<br/>")
					$("#clean").val('');
					scrolls();
				}else{
					alert("Спам!");
				}
			});
		send_block = 0;
       return false;
	}
	
	if (e.keyCode != 13 && !$("#send").hasClass("hidden") && Date.now() > j)
	{
		$.post("https://"+site+"/public/chat.php", { do: "typing"  } ); // Набираем сообщение
		j	= Date.now()+500;
	}
}

function scrolls()
{
	var objDiv = document.getElementById("chat");
	objDiv.scrollTop = objDiv.scrollHeight;
}

$( "#ban" ).click(function(e) {
	if(ban==0) {
		$.post("https://"+site+"/public/chat.php", { do: "ban" } );
		$("#chat").append("<span class='system'>Система</span>: Ваш собеседник добавлен в черный список на час.<br/>")
		scrolls();
		$("#clean").val('');
		ban = 1;
	}
});

$( "#send" ).click(function(e) {
	if(!$(this).hasClass("hidden"))
	{
		e.preventDefault();
		
		var m = $('textarea[name=text]').val();
		var t =  $.trim(m);
		
		if(t.length > 1000){
			alert("превышен лимит, не больше 1000 символов");
			return false;
		}
		
		if(t.length > 0 && send_block == 0)
		{
			send_block = 1;
			$.post("https://"+site+"/public/chat.php", { mes: $('textarea[name=text]').val(), do: "add"  }, function( data ) {
				if(data != "SPAM"){
					$("#chat").append("<span class='self'>Я</span>: "+data+"<br/>");
					$("#clean").val('');
					scrolls();
				}else{
					alert("Спам!");
				}
				send_block = 0;
			});
		}
	}
});

$( "#end" ).click(function(e) 
{
	$("#ping").html('&nbsp;');
	e.preventDefault();
	if(!$(this).hasClass("hidden"))
	{
		$.post("https://"+site+"/public/chat.php", { do: "close"  } );
		$("#status").html('<i>Разговор закончен...</i>');
		$("#send").addClass("hidden");
		$("#video").addClass("hidden");
		$("#invite").addClass("hidden");
		$(this).addClass("hidden");
		$("#search, #exit, #ban").removeClass("hidden");
		$("#clean").hide();
	}
});


function f() 
{
	if(Date.now() > k){

		$("#online").html(Cookies.getJSON("online")[0]);
		$("#online_kun").html(Cookies.getJSON("online")[1]);
		$("#online_chan").html(Cookies.getJSON("online")[2]);
		
		$.post("https://"+site+"/public/chat.php", { do: "ping"  } );
		k	= Date.now()+10000;
	}
	if(i==0)
	{
		if(Cookies.get("status") == 1)
		{
			$.post( "https://"+site+"/public/chat.php", {do: "get"}, function( data ) 
			{
				if(data.length > 0)
				{
					$("#chat").append("<span class='inter'>Гость</span>: " + data + "<br/>");
					scrolls();
				}
			});
		}
		
		if(Cookies.get("status") == 2 )
		{
			if($("#status").html() != '<i>Разговор закончен...</i>'){
				$("#status").html('<i>Разговор закончен...</i>');
				scrolls();
				$("#send, #end, #invite, #video").addClass("hidden");
				$("#search, #exit, #ban").removeClass("hidden");
				$("#clean").hide();
			}
		}
		
		if(Cookies.get("typing") == 1)
		{
			if($("#status").html() != '<i>Собеседник печатает...</i>'){
				$("#status").html('<i>Собеседник печатает...</i>');
			}
		}
		
		if(Cookies.get("typing") == 0 & Cookies.get("status") != 2 & Cookies.get("ping") != 0)
		{
			if($("#status").html() != '&nbsp;'){
				$("#status").html('&nbsp;');
			}
		}
		
		if(Cookies.get("ping") == 0 & Cookies.get("status") != 2)
		{
			if($("#status").html() != '<font color="#DB0000">●</font> <i>собеседник оффлайн</i>'){
				$("#status").html('<font color="#DB0000">●</font> <i>собеседник оффлайн</i>');
			}	
		}
		setTimeout( f, 500 );
	}
}
var i = 0;
var searchcounter=0;
var searching = true;
function search(force)
{
	if(force)
	{
		searching = true;
	}
	if(searching)
	{
		if(i == 0)
		{
			$("#chat").html('');
			scrolls();
			$("#send, #end, #search, #exit, #ban, #video").addClass("hidden");
			$("#stop").removeClass("hidden");
			$("#ping").html('&nbsp;');
			i++;
			searching = true;
		}
		$.post("https://"+site+"/public/chat.php", { do: "ping"  } );
		$.post( "https://"+site+"/public/chat.php", {do: "search"}, function( data ) 
		{
			if(data.length < 10)
			{
				if(searchcounter >= 4) searchcounter=0;
				$("#status").html("<b>Поиск"+str_repeat('.', searchcounter)+"</b>");
				searchcounter++;
				setTimeout( search, 1000 );
			}
			else
			{
				searchcounter=0;
				i=0;
				searching = false;
				$("#chat").append("<b>Поиск:</b> " + data);
				$("#status").html('&nbsp;');
				scrolls();
				$("#stop").addClass("hidden");
				$("#send, #end, #video").removeClass("hidden");
				ban = 0;
				f();
			}
		});
	}
}

$( "#search" ).click(function(e) 
{
	
	$("#clean").show();
	
	if(!$(this).hasClass("hidden"))
	{
		$("#chat").val('');
		$("#chat").append("<b>Ищем собеседника</b><br/>");
		$("#send, #end, #search").addClass("hidden");
		search(true);
	}
});

$( "#exit" ).click(function(e) 
{
	$.post( "https://"+site+"/public/chat.php", {do: "exit"} );
	window.location.href = "https://"+site+"/public/chat.php?exitlink=1";
});

$( "#stop" ).click(function(e) 
{
	if(!$(this).hasClass("hidden") && searching)
	{
		$.post("https://"+site+"/public/chat.php", { do: "stop"  } );
		$("#status").html("&nbsp;");
		i = 0;
		searchcounter=0;
		searching = false;
		$("#search, #exit").removeClass("hidden");
		$(this).addClass("hidden");
	}
});

search(true);

function str_repeat ( input, multiplier ) {	// Repeat a string
	// 
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	
	var buf = '';
	
	for (i=0; i < multiplier; i++){
		buf += input;
	}
	
	return buf;
}
