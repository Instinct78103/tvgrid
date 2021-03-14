//<textarea> и div.leftBar
const txt_in = document.querySelector('.in');
const txt_out = document.querySelector('.out');
const leftBar = document.querySelector('.left-bar');
const fileList = document.querySelector('.files');

//<select>
const startTime = document.querySelector('select#startTime');
const endTime = document.querySelector('select#endTime');
const changeTime = document.querySelector('select#changeTime');

//input[type=checkbox]
const deleteReps = document.querySelector('input#deleteReps');
const deleteShortPros = document.querySelector('input#deleteShortPros');
const lowerCase = document.querySelector('input#lowerCase');
const afterDot = document.querySelector('input#afterDot');

const mn = document.querySelector('.main');

//drag-n-drop
if (txt_in) {
  for (let eventName of ['dragenter', 'dragover', 'dragleave', 'drop']) {
    txt_in.addEventListener(eventName, preventDefaults, false);
  }

  function preventDefaults(event) {
    event.preventDefault();
    event.stopPropagation();
  }

  for (let eventName of ['dragenter', 'dragover']) {
    txt_in.addEventListener(eventName, highlight, false);
  }
  for (let eventName of ['dragleave', 'drop']) {
    txt_in.addEventListener(eventName, unhighlight, false);
  }

  function highlight(event) {
    txt_in.classList.add('border-5px-blue');
  }

  function unhighlight(event) {
    txt_in.classList.remove('border-5px-blue');
  }

  txt_in.addEventListener('drop', handleDrop, false);

  function handleDrop(event) {
    let dt = event.dataTransfer;
    let files = dt.files;

    handleFiles(files);
  }

  function handleFiles(files) {
    for (let file of [...files]) {
      uploadFile(file);
    }
  }

  function uploadFile(file) {
    let xhr = new XMLHttpRequest();
    let formData = new FormData();
    xhr.open('POST', 'php/upload.php');

    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) {
        return;
      }
      if (xhr.status === 200) {
        txt_out.innerHTML = xhr.response;
      } else {
        txt_out.innerHTML = 'Ошибка: ' + xhr.status;
      }
    };

    formData.append('file', file);
    xhr.send(formData);
  }

  txt_in.oninput = jsonPost;

  for (let item of leftBar.children) {
    item.onchange = function () {
      if (txt_in.value !== '') {
        jsonPost();
      }
    };
  }

  txt_in.onpaste = function () {
    //сброс перевода времени при вставке
    // for (let i in changeTime) {
    //   changeTime[i].selected = changeTime[i].defaultSelected;
    // }

    changeTime.value = 0;
    afterDot.checked = false;
    lowerCase.checked = false;
  };
}

function jsonPost() {
  if (txt_in.value !== '') {
    deleteReps.value = deleteReps.checked ? 1 : 0;
    deleteShortPros.value = deleteShortPros.checked ? 1 : 0;
    lowerCase.value = lowerCase.checked ? 1 : 0;
    afterDot.value = afterDot.checked ? 1 : 0;

    let jsonStr = JSON.stringify({
      'startTime': startTime.options[startTime.selectedIndex].value,
      'endTime': endTime.options[endTime.selectedIndex].value,
      'deleteReps': deleteReps.value,
      'deleteShortPros': deleteShortPros.value,
      'lowerCase': lowerCase.value,
      'afterDot': afterDot.value,
      'changeTime': changeTime.options[changeTime.selectedIndex].value,
      'txt_in': txt_in.value,
    });

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/handler.php');
    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    xhr.send(jsonStr);

    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) {
        return;
      }
      if (xhr.status === 200) {
        let resp = JSON.parse(xhr.response);
        txt_out.value = resp.result;
      } else {
        txt_out.value = 'Не удалось связаться с сервером!';
      }
    };
  }
}

function start() {
  id = setInterval('show_files()', 1000);
}

function show_files() {
  let xhr = new XMLHttpRequest();
  xhr.open('GET', 'php/handler-list.php');
  xhr.send();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let resp = JSON.parse(xhr.response);

      if (fileList) {
        fileList.innerHTML = `<p><b>Файлов загружено: ${resp.length}</b></p>` + ((resp.length)
          ? `<ul id="files"></ul><br>
				<button class="padding-5" id="delete">Удалить файлы</button>`
          : '');
      }

      //Заполняем ul
      let ul = document.querySelector('#files');
      for (let i in resp) {
        let li = document.createElement('li');
        li.innerHTML = resp[i];
        ul.appendChild(li);

        li.onclick = fillAllTextareas;

        function fillAllTextareas(e) {
          const fileName = JSON.stringify({
            'fileName': e.target.innerText,
          });

          let xhr = new XMLHttpRequest();
          xhr.open('POST', 'php/handler.php');
          xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
          xhr.send(fileName);

          xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
              return;
            }
            if (xhr.status === 200) {
              let resp = JSON.parse(xhr.response);
              startTime.value = resp.startTime;
              endTime.value = resp.endTime;
              afterDot.checked = resp.afterDot;
              lowerCase.checked = resp.lowerCase;

              txt_in.value = resp.raw;
              txt_out.value = resp.result;
            } else {
              txt_out.style.border = '1px solid red';
              txt_out.value = 'Ошибка: ' + xhr.status;
            }
          };
        }
      }

      //Кнопка Удалить файлы
      let deleteFiles = document.querySelector('#delete');
      if (deleteFiles) {
        deleteFiles.onclick = function () {
          let xhr = new XMLHttpRequest();
          xhr.open('GET', 'php/filesDeleteButton.php');
          xhr.send();
        };
      }

    } else if (xhr.status !== 200) {
      fileList.style.border = '1px solid red';
      fileList.innerHTML = 'Ошибка: ' + xhr.status;
    }
  };
}

start();

/* settings.php */
// const tables = document.querySelector('.tables');
// if (tables.length) {
//   for (let item of tables.children) {
//     item.onclick = function () {
//       let jsonStr = JSON.stringify({
//         'tableName': item.id,
//       });
//
//       for (let nitem of tables.children) {
//         nitem.style.fontWeight = '';
//         nitem.style.textDecoration = '';
//       }
//       this.style.fontWeight = 'bold';
//       this.style.textDecoration = 'underline';
//
//
//       let xhr = new XMLHttpRequest();
//       xhr.open('POST', 'php/tableContent.php');
//       xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
//       xhr.send(jsonStr);
//       xhr.onreadystatechange = function () {
//         mn.innerHTML = xhr.response;
//       };
//     };
//   }
// }


function myFunc(el) {
  let elemId = document.querySelector(`#${el}`);
  console.log(elemId.parentElement.parentElement.parentElement.parentElement.id);
}