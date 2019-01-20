var ping = 0;
var typing = 0;
var site = document.domain;

function scrolls() {
	objDiv = document.getElementById("chat");
	objDiv.scrollTop = objDiv.scrollHeight;
}

function chatPing(){
	if(Date.now() > ping){
		$.post("https://"+site+"/public/chat.php", {do: "ping"}, function(json){
			data = JSON.parse(json);
			$("#online").html(data[0]);
			$("#online_kun").html(data[1]);
			$("#online_chan").html(data[2]);
		});
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
		$("#chat").append("<span class='system'>Система</span>: превышен лимит, не больше 1000 символов<br/>");
		return;
	}
	if(t.length > 0){
		$.post("https://"+site+"/public/chat.php", {mes: m, do: "add"}, function(json){
			if(json != ""){
				data = JSON.parse(json);
				if(data.status == "ok"){
					$("#chat").append("<span class='self'>Я</span>: "+data.mes+"<br/>");
				}
				if(data.status == "spam"){
					$("#chat").append("<span class='system'>Система</span>: "+data.mes+"<br/>");
				}
				scrolls();
			}
		});
	}
	$("#clean").val('');
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
	if(!$("#stop").hasClass("hidden")){
		$.post("https://"+site+"/public/chat.php", {do: "search"}, function(json){
			data = JSON.parse(json);
				if(data.status == 'search'){
					setTimeout(search, 1000);
				}
				if(data.status == 'find'){
					$("#stop").addClass("hidden");
					$("#status").removeClass("loading");
					$("#status").html('&nbsp;');
					$("#chat").append("<i>Поиск:</i> " + data.mes);
					$("#clean, #send, #end").removeClass("hidden");
					scrolls();
					getMsg();
				}
		});
	}
}

function startGetMsg(){
	setTimeout(getMsg, 500);
}

function getMsg(){
	chatPing();
	if(!$("#end").hasClass("hidden")){
		$.post("https://"+site+"/public/chat.php", {do: "get"}, function(json){
			if(json != ""){
				data = JSON.parse(json);
				if(data.status == 'typing'){
					tmp = '<i>Собеседник печатает...</i>';
					if($("#status").html() != tmp){
						$("#status").html(tmp);
					}
				}
				if(data.status == 'offline'){
					tmp = '<font color="#DB0000">●</font> <i>собеседник оффлайн</i>';
					if($("#status").html() != tmp){
						$("#status").html(tmp);
					}
				}
				if(data.status == 'online'){
					if($("#status").html() != '&nbsp;'){
						$("#status").html('&nbsp;');
					}
				}
				if(data.status == 'end'){
					$("#status").html('<i>Разговор закончен...</i>');
					scrolls();
					$("#send, #end, #clean").addClass("hidden");
					$("#search, #exit, #ban").removeClass("hidden");
					return;
				}
				if(data.mes.length > 0){
					$("#chat").append("<span class='inter'>Гость</span>: " +data.mes+ "<br/>");
					scrolls();
				}
			}
			startGetMsg();
		});
	}
}

$(document).ready(function(){
	beforeSearch();
	search();
});

$("#send").click(function(e){
	e.preventDefault();	
	sendMes();
});

$("#exit").click(function(e){
	$.post("https://"+site+"/public/chat.php", {do: "exit"}, function(json){
		window.location.href = "https://"+site+"/pages/chat.php";
	});
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
