const list = document.querySelector('.list')
const deleteFiles = document.querySelector('#clear')
const tv = document.querySelector('.tv')

function show_files(){	
	let xhr = new XMLHttpRequest();
	xhr.open('GET', 'php/handler-list.php')
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			list.removeAttribute('style')
			resp = JSON.parse(xhr.response)
			list.innerHTML = '<p><b>Файлов загружено: ' + resp.length + '</b></p>' + '<ul id="files"></ul>'
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
							tv.value = xhr2.response;
							//tv.focus();
						}
						else{
							tv.style.border = '1px solid red';
							tv.value = 'Ошибка: ' + xhr.status;
						}
					}
					
					
				}
			}
		}
		else if(xhr.status != 200){
			list.style.border = '1px solid red'
			list.innerHTML = 'Ошибка: ' + xhr.status
		}
	}
	xhr.send();
} 

function start(){
	id = setInterval('show_files()', 2000);
}

list.onload = start();

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
			list.style.border = '1px solid red';
			list.innerHTML = 'Ошибка: ' + xhr.status;
		}
	}
}



//drag-n-drop
;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
	tv.addEventListener(eventName, preventDefaults, false)
})

function preventDefaults(event){
	event.preventDefault()
	event.stopPropagation()
}

;['dragenter', 'dragover'].forEach(eventName => {
	tv.addEventListener(eventName, highlight, false)
})

;['dragleave', 'drop'].forEach(eventName => {
	tv.addEventListener(eventName, unhighlight, false)
})

function highlight(event){
	tv.classList.add('highlight')
}

function unhighlight(event){
	tv.classList.remove('highlight')
}

tv.addEventListener('drop', handleDrop, false)

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
			tv.innerHTML = xhr.response;
		}
		else{
			tv.innerHTML = 'Ошибка: ' + xhr.status
		}
	}
	
	formData.append('file', file)
	xhr.send(formData);	

}