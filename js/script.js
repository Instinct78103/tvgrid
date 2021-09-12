//<textarea> и div.leftBar
const txt_in = document.querySelector('.in');
const txt_out = document.querySelector('.out');
const leftBar = document.querySelector('.left-bar');
const fileList = document.querySelector('.files');

const startTime = document.querySelector('#startTime');
const endTime = document.querySelector('#endTime');
const changeTime = document.querySelector('#changeTime');

//input[type=checkbox]
const deleteReps = document.querySelector('#deleteReps');
const deleteShortPros = document.querySelector('#deleteShortPros');
const lowerCase = document.querySelector('#lowerCase');
const afterDot = document.querySelector('#afterDot');

const getFilesList = () => {
  let xhr = new XMLHttpRequest();
  xhr.open('GET', 'php/handler-list.php');
  xhr.send();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let resp = JSON.parse(xhr.response);
      let txt_files = resp.txt ? resp.txt : [];
      let doc_files = resp.docx ? resp.docx : [];

      if (fileList) {
        fileList.innerHTML = `<p><b>Файлов загружено: ${txt_files.length}</b></p>`;
        fileList.innerHTML += txt_files.length
          ? '<ul id="files"></ul><br><button class="padding-5" id="delete">Удалить файлы</button>'
          : '';
      }

      let ul = document.querySelector('#files');
      for (let item of txt_files) {
        let li = document.createElement('li');
        li.innerText = item;
        ul.appendChild(li);
      }
    } else if (xhr.status !== 200) {
      fileList.style.border = '1px solid red';
      fileList.innerHTML = 'Ошибка: ' + xhr.status;
    }
  };
};

getFilesList();

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
    txt_in.addEventListener(eventName, () => {
      txt_in.classList.add('border-5px-blue');
    }, false);
  }

  for (let eventName of ['dragleave', 'drop']) {
    txt_in.addEventListener(eventName, () => {
      txt_in.classList.remove('border-5px-blue');
    }, false);
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
    formData.append('file', file);

    xhr.open('POST', 'php/upload.php');
    xhr.send(formData);

    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) {
        return;
      }
      if (xhr.status === 200) {
        txt_out.innerHTML = xhr.response;
        getFilesList();
      } else {
        txt_out.innerHTML = 'Ошибка: ' + xhr.status;
      }
    };
  }

  txt_in.oninput = jsonPost;

  for (let item of leftBar.children) {
    item.onchange = function () {
      if (txt_in.value !== '') {
        jsonPost();
      }
    };
  }

  txt_in.onpaste = () => {
    changeTime.value = 0;
    afterDot.checked = false;
    lowerCase.checked = false;
    setTimeout(() => {
      txt_in.scrollTo(0, 0);
    }, 0);
  };
}

fileList.addEventListener('click', (e) => {
  if (e.target.matches('li')) {
    fileList.querySelectorAll('li').forEach(li => li.classList.remove('list-item-highlighted'));
    e.target.classList.add('list-item-highlighted');
    fillAllTextareas(e);
  }
});

fileList.addEventListener('click', (e) => {
  if (e.target.matches('#delete')) {
    fetch('php/filesDeleteButton.php').then(() => {
      getFilesList();
    });
  }
});

// fileList.addEventListener('click', async (e) => {
//   if (e.target.matches('#delete_txt')) {
//       let result = await fetch('php/filesDeleteButton.php', {
//         method: 'POST',
//         body: JSON.stringify(e.target.id),
//         headers: {
//           'Content-Type': 'application/json',
//         },
//       });
//
//       const resp = await result.json()
//       console.log(resp)
//
//   } else if (e.target.matches('#delete_doc')) {
//
//   }
// });

function fillAllTextareas(e) {
  const fileName = JSON.stringify({
    'fileName': e.target.innerText,
  });
  changeTime.value = 0;

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

function jsonPost() {
  if (txt_in.value !== '') {
    deleteReps.value = deleteReps.checked ? 1 : 0;
    deleteShortPros.value = deleteShortPros.checked ? 1 : 0;
    lowerCase.value = lowerCase.checked ? 1 : 0;
    afterDot.value = afterDot.checked ? 1 : 0;

    let jsonStr = JSON.stringify({
      'startTime': startTime.value,
      'endTime': endTime.value,
      'deleteReps': deleteReps.value,
      'deleteShortPros': deleteShortPros.value,
      'lowerCase': lowerCase.value,
      'afterDot': afterDot.value,
      'changeTime': changeTime.value,
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
        txt_out.value = 'Ошибка: ' + xhr.status;
      }
    };
  }
}