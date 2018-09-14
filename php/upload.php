<?php

/* echo '<pre>';
print_r($_FILES);
echo '</pre>'; */

if($_FILES['file']['type'] != 'text/plain'){
	exit('Недопустимый тип файла: <b>' . $_FILES['file']['name'] . '</b>');
}
elseif($_FILES['file']['size'] == 0){
	exit('Файл пустой: <b>' . $_FILES['file']['name'] . '</b>');	
}
elseif($_FILES['file']['size'] > 14000){
	exit('Cлишком большой файл (' . round($_FILES['file']['size'] / 1000, 1) . ' kb): <b>' . $_FILES['file']['name'] . '</b>' );
}
else{
	$file_info = pathinfo($_FILES['file']['name']);			
	$place = 'txt/' . $file_info['basename'];
	move_uploaded_file($_FILES['file']['tmp_name'], $place);
	//echo 'Файлы загружены!';
}
?>