//<textarea> и div.leftBar
const txt_in = document.querySelector('.in');
const txt_out = document.querySelector('.out');
const leftBar = document.querySelector('.left-bar');
//const deleteFiles = document.querySelector('#clear')

//<select>
const startTime = document.querySelector('select#startTime');
const endTime = document.querySelector('select#endTime');
const changeTime = document.querySelector('select#changeTime');

//input[type=checkbox]
const deleteReps = document.querySelector('input#deleteReps');
const deleteShortPros = document.querySelector('input#deleteShortPros');
const lowerCase = document.querySelector('input#lowerCase');
const afterDot = document.querySelector('input#afterDot');

function jsonPost(){
	
	deleteReps.value = deleteReps.checked ? 1 : 0;
	deleteShortPros.value = deleteShortPros.checked ? 1 : 0;
	lowerCase.value = lowerCase.checked ? 1 : 0;
	afterDot.value = afterDot.checked ? 1 : 0;
	
	let xhr = new XMLHttpRequest();
	let jsonStr = JSON.stringify({
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
		}
		else{
			txt_out.value = 'Не удалось связаться с сервером!';
		}
	}
	
	
}

txt_in.oninput = function(){
	jsonPost();
}

leftBar.childNodes.forEach(function(item){
	item.onchange = function(){
		if(txt_in.value != ''){
			jsonPost();
		}
	}
})

txt_in.addEventListener('paste', function(){
	for (var i = 0; i < changeTime.length; i++){
		changeTime[i].selected = changeTime[i].defaultSelected;
	}
});




function show_files(){	
	let xhr = new XMLHttpRequest();
	xhr.open('GET', 'php/handler-list.php')
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			leftBar.removeAttribute('style');
			resp = JSON.parse(xhr.response);
			
			if(!leftBar.classList.contains("static")){
				leftBar.innerHTML = (resp.length == 0) ? '<p><b>Файлов загружено: ' + resp.length + '</b></p>': '<p><b>Файлов загружено: ' + resp.length + '</b></p>' + '<ul id="files"></ul><br>' + '<button class="padding-5" id="clear">Удалить файлы</button>'
				//Создаем ul
				let ul = document.querySelector('#files')
				for(let i = 0; i < resp.length; i++){
					let li = document.createElement('li')
					li.innerHTML = resp[i]
					ul.appendChild(li)
					li.onclick = function(event){
						
						
						event.preventDefault()
						event.stopPropagation()
						
						let xhr2 = new XMLHttpRequest();
						var jsonStr = JSON.stringify({
							"fileName": li.innerHTML
						});
						xhr2.open('POST', 'php/showmetv.php');
						xhr2.setRequestHeader('Content-type', 'application/json; charset=utf-8');
						xhr2.send(jsonStr)
						
						xhr2.onreadystatechange = function(){
							if(xhr2.readyState != 4){
								return;	
							}
							if(xhr2.status == 200){
								txt_out.value = xhr2.response;
							}
							else{
								txt_out.style.border = '1px solid red';
								txt_out.value = 'Ошибка: ' + xhr.status;
							}
						}
					}
				}
				//Кнопка Удалить файлы
				let deleteFiles = document.querySelector('#clear');
				deleteFiles.onclick = function(){
					let xhr = new XMLHttpRequest();
					xhr.open('GET', 'php/filesDeleteButton.php');
					xhr.send();
					
					xhr.onreadystatechange = function(){
						if(xhr.readyState != 4){
							return;	
						}
						if(xhr.status == 200){
							start();
						}
						else{
							leftBar.style.border = '1px solid red';
							leftBar.innerHTML = 'Ошибка: ' + xhr.status;
						}
					}
				}
				
			}
		}
		else if(xhr.status != 200){
			leftBar.style.border = '1px solid red'
			leftBar.innerHTML = 'Ошибка: ' + xhr.status
		}
	}
	xhr.send();
} 

function start(){
	id = setInterval('show_files()', 1000);
}

leftBar.onload = start();

//drag-n-drop
;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
	txt_in.addEventListener(eventName, preventDefaults, false)
})

function preventDefaults(event){
	event.preventDefault()
	event.stopPropagation()
}

;['dragenter', 'dragover'].forEach(eventName => {
	txt_in.addEventListener(eventName, highlight, false)
})

;['dragleave', 'drop'].forEach(eventName => {
	txt_in.addEventListener(eventName, unhighlight, false)
})

function highlight(event){
	txt_in.classList.add('border-5px-blue')
}

function unhighlight(event){
	txt_in.classList.remove('border-5px-blue')
}

txt_in.addEventListener('drop', handleDrop, false)

function handleDrop(event){
	let dt = event.dataTransfer
	let files = dt.files

	handleFiles(files)
}

function handleFiles(files){
	([...files]).forEach(uploadFile)
}

function uploadFile(file){
	
	let xhr = new XMLHttpRequest();
	let formData = new FormData();
	xhr.open('POST', 'php/upload.php');
	
	
	xhr.onreadystatechange = function(){
		if(xhr.readyState != 4){
			return;	
		}
		if(xhr.status == 200){
			txt_out.innerHTML = xhr.response;
		}
		else{
			txt_out.innerHTML = 'Ошибка: ' + xhr.status
		}
	}
	
	formData.append('file', file)
	xhr.send(formData);	

}