var ping = 0;
var typing = 0;
var site = document.domain;


var k	= 0;
var i = 0;

function scrolls() {
	objDiv = document.getElementById("chat");
	objDiv.scrollTop = objDiv.scrollHeight;
}

function chatPing(){
	if(Date.now() > ping){
		$.post("https://"+site+"/public/chat.php", {do: "ping"});
		ping = Date.now()+10000;
	}
}

function runScript(e){
    if(e.keyCode == 13 && !$("#send").hasClass("hidden")){
		e.preventDefault();
		sendMes();
	}
	if(e.keyCode != 13 && !$("#send").hasClass("hidden") && Date.now() > typing){
		$.post("https://"+site+"/public/chat.php", {do: "typing"});
		typing = Date.now()+500;
	}
}

function sendMes(){
	m = $('textarea[name=text]').val();
	t = $.trim(m);
	if(t.length > 1000){
		alert("превышен лимит, не больше 1000 символов");
		return;
	}
	if(t.length > 0){
		$.post("https://"+site+"/public/chat.php", {mes: m, do: "add"}, function( data ) {
			if(data != "SPAM"){
				$("#chat").append("<span class='self'>Я</span>: "+data+"<br/>");
				scrolls();
			}else{
				alert("Спам!");
			}
		});
	}
	$("#clean").val('');
}

$("#send").click(function(e){
	e.preventDefault();	
	sendMes();
});

$( "#exit" ).click(function(e){
	$.post("https://"+site+"/public/chat.php", {do: "exit"});
	window.location.href = "https://"+site+"/public/chat.php?exitlink=1";
});

$("#ban").click(function(e){
	e.preventDefault();
	$("#chat").append("<span class='system'>Система</span>: Ваш собеседник добавлен в черный список на час.<br/>")
	scrolls();
	$("#clean").val('');
	$.post("https://"+site+"/public/chat.php", {do: "ban"});
});

$("#search").click(function(e){
	$("#chat").val('');
	$("#chat").append("<i>Ищем собеседника</i><br/>");
	$("#send, #end, #search, #clean").addClass("hidden");
	beforeSearch();
	search();
});

$("#stop").click(function(e) {
	$.post("https://"+site+"/public/chat.php", {do: "stop"});
	$("#status").html("&nbsp;");
	i = 0;
	searchcounter=0;
	searching = false;
	$("#search, #exit").removeClass("hidden");
	$("#stop").addClass("hidden");
});

$("#end").click(function(e){
	e.preventDefault();
	$("#end, #send, #clean").addClass("hidden");
	$("#status").html('<i>Разговор закончен...</i>');
	$("#search, #exit, #ban").removeClass("hidden");
	$("#clean").val('');
	$.post("https://"+site+"/public/chat.php", {do: "close"});
});





function f() 
{
	if(Date.now() > k){

		$("#online").html(Cookies.getJSON("online")[0]);
		$("#online_kun").html(Cookies.getJSON("online")[1]);
		$("#online_chan").html(Cookies.getJSON("online")[2]);
		
		chatPing();
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
				$("#send, #end, #clean").addClass("hidden");
				$("#search, #exit, #ban").removeClass("hidden");
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
		setTimeout(f, 500);
	}
}

function beforeSearch(){
	$("#status").html("Поиск");
	$("#status").addClass("loading");
	$("#chat").html('');
	scrolls();
	$("#send, #end, #search, #exit, #ban, #clean").addClass("hidden");
	$("#stop").removeClass("hidden");
}

function search(){
	chatPing();
	$.post("https://"+site+"/public/chat.php", {do: "search"}, function(json){
		data = JSON.parse(json);
		if(data.status == 'searching'){
			setTimeout(search, 1000);
		}	
		if(data.status == 'find'){
			$("#stop").addClass("hidden");
			$("#status").removeClass("loading");
			$("#status").html('&nbsp;');
			$("#chat").append("<i>Поиск:</i> " + data.mes);
			$("#clean, #send, #end").removeClass("hidden");
			scrolls();
			f();
		}
	});
}

beforeSearch();
search();
