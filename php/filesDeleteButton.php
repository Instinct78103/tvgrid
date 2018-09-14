<?

$folder = 'txt';
$f_arr = array_values( array_diff( scandir($folder), array('.', '..') ) );
if( count($f_arr) ){
	foreach($f_arr as $item){
		unlink("$folder/$item");
	}
}
echo 'Папка пуста!';

?>