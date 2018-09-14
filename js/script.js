//<textarea> и div.tuning
var txt_in = document.querySelector('.in');
var txt_out = document.querySelector('.out');
var tuning = document.querySelector('.tuning');

//<select>
var startTime = document.querySelector('select#startTime');
var endTime = document.querySelector('select#endTime');
var changeTime = document.querySelector('select#changeTime');

//input[type=checkbox]
var deleteReps = document.querySelector('input#deleteReps');
var deleteShortPros = document.querySelector('input#deleteShortPros');
var lowerCase = document.querySelector('input#lowerCase');
var afterDot = document.querySelector('input#afterDot');

function sendData(){
	
	deleteReps.value = deleteReps.checked ? 1 : 0;
	deleteShortPros.value = deleteShortPros.checked ? 1 : 0;
	lowerCase.value = lowerCase.checked ? 1 : 0;
	afterDot.value = afterDot.checked ? 1 : 0;
	
	var xhr = new XMLHttpRequest();
	var jsonStr = JSON.stringify({
		"startTime": startTime.options[startTime.selectedIndex].value,
		"endTime": endTime.options[endTime.selectedIndex].value,
		"deleteReps": deleteReps.value,
		"deleteShortPros": deleteShortPros.value,
		"lowerCase": lowerCase.value,
		"afterDot": afterDot.value,
		"changeTime": changeTime.options[changeTime.selectedIndex].value,
		"txt_in": txt_in.value
	});
	
	//console.log( JSON.parse(jsonStr) );
	
	xhr.open('POST', 'php/handler.php');
	xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
	xhr.send(jsonStr);
	
	xhr.onreadystatechange = function(){
		if(xhr.readyState != 4){
			return;
			
		}
		if(xhr.status == 200){
			 txt_out.value = xhr.response;
			//txt_out.focus();
		}
		else{
			txt_out.value = 'Не удалось связаться с сервером!';
		}
	}
	
	
}

txt_in.oninput = function(){
	sendData();
}

tuning.childNodes.forEach(function(item){
	item.onchange = function(){
		if(txt_in.value != ''){
			sendData();
		}
	}
})

txt_in.addEventListener('paste', function(){
	for (var i = 0; i < changeTime.length; i++){
		changeTime[i].selected = changeTime[i].defaultSelected;
	}
});