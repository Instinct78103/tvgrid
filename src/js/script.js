const txt_in = document.querySelector('.in');
const txt_out = document.querySelector('.out');
const sidebar = document.querySelector('.sidebar');
const fileList = document.querySelector('.files');

const startTime = document.querySelector('#startTime');
const endTime = document.querySelector('#endTime');
const changeTime = document.querySelector('#changeTime');

//input[type=checkbox]
const deleteReps = document.querySelector('#deleteReps');
const deleteShortPros = document.querySelector('#deleteShortPros');
const lowerCase = document.querySelector('#lowerCase');
const afterDot = document.querySelector('#afterDot');

const getFilesList = async () => {
  try {
    const promise = await fetch('php/handler-list.php');
    const dirs = await promise.json();
    let txt = '<div class="txt_files">';
    let docx = '<div class="docx_files">';

    if (dirs.hasOwnProperty('txt')) {
      txt += `<p><b>TXT: ${dirs.txt.length}</b></p>`;
      txt += `<ul class="files_txt">${dirs.txt.map(file => `<li class="file_name">${file}</li>`).join('')}</ul>`;
      txt += '<p><button class="padding-5" id="delete_txt">Удалить txt</button></p>';
    }
    txt += '</div>';

    if (dirs.hasOwnProperty('docx')) {
      docx += `<p><b>DOCX: ${dirs.docx.length}</b></p>`;
      docx += `<ul class="files_docx">${dirs.docx.map(file => `<li class="file_name">${file}</li>`).join('')}</ul>`;
      docx += '<p><button class="padding-5" id="delete_docx">Удалить docx</button></p>';
    }
    docx += '</div>';

    fileList.innerHTML = txt + docx;

  } catch (e) {
    console.log(e);
  }
};

getFilesList();

const submitData = async () => {
  if (txt_in.value !== '') {
    const jsonStr = JSON.stringify({
      'startTime': startTime.value,
      'endTime': endTime.value,
      'deleteReps': deleteReps.checked ? 1 : 0,
      'deleteShortPros': deleteShortPros.checked ? 1 : 0,
      'lowerCase': lowerCase.checked ? 1 : 0,
      'afterDot': afterDot.checked ? 1 : 0,
      'changeTime': changeTime.value,
      'txt_in': txt_in.value,
    });

    try {
      const resp = await fetch('php/handler.php', {
        method: 'POST',
        headers: {
          'Content-type': 'application/json; charset=utf-8',
        },
        body: jsonStr,
      });
      const json = await resp.json();
      txt_out.value = json.result;
    } catch (e) {
      console.log(e);
    }
  }
};

//drag-n-drop
if (txt_in) {
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => txt_in.addEventListener(event, preventDefaults, false));

  function preventDefaults(event) {
    event.preventDefault();
    event.stopPropagation();
  }

  ['dragenter', 'dragover'].forEach(event => txt_in.addEventListener(event, () => txt_in.classList.add('border-5px-blue')));
  ['dragleave', 'drop'].forEach(event => txt_in.addEventListener(event, () => txt_in.classList.remove('border-5px-blue')));

  txt_in.addEventListener('drop', handleDrop, false);

  function handleDrop(event) {
    const files = event.dataTransfer.files;
    [...files].forEach(file => uploadFile(file));
  }

  const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);
    await fetch('php/upload.php', {
      method: 'POST',
      body: formData,
    });
    await getFilesList();
  };

  txt_in.oninput = submitData;
  txt_in.onpaste = () => {
    changeTime.value = 0;
    afterDot.checked = false;
    lowerCase.checked = false;
    setTimeout(() => {
      txt_in.scrollTo(0, 0);
    }, 0);
  };

  [...sidebar.children].forEach(item => {
    item.onchange = () => {
      if (txt_in.value !== '') {
        submitData();
      }
    };
  });
}

document.addEventListener('click', (e) => {
  if (e.target.matches('.files_txt li')) {
    fileList.querySelectorAll('li').forEach(li => li.classList.remove('list-item-highlighted'));
    e.target.classList.add('list-item-highlighted');
    selectFile(e);
  }
});

document.addEventListener('click', async (e) => {
  if (e.target.matches('.files [id^="delete_"]')) {
    if (confirm(`Удалить ${e.target.id.split('_')[1]}-файлы?`)) {
      await fetch('php/filesDeleteButton.php', {
        method: 'POST',
        body: e.target.id,
        headers: {
          'Content-type': 'application/json; charset=utf-8',
        },
      });
      await getFilesList();
    }
  }
});

const selectFile = async (e) => {
  const fileName = JSON.stringify({
    'fileName': e.target.innerText,
  });
  changeTime.value = 0;

  try {
    const promise = await fetch('php/handler.php', {
      method: 'POST',
      headers: {
        'Content-type': 'application/json; charset=utf-8',
      },
      body: fileName,
    });
    const json = await promise.json();

    startTime.value = json.startTime;
    endTime.value = json.endTime;
    afterDot.checked = json.afterDot;
    lowerCase.checked = json.lowerCase;
    txt_in.value = json.raw;
    txt_out.value = json.result;
  } catch (err) {
    console.log(err);
  }
};