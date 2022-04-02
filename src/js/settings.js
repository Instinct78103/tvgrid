const tds = document.querySelectorAll('.main > table td');
tds.forEach(td => td.addEventListener('changes', function (item) {
    console.log(item.parent.id)
}))