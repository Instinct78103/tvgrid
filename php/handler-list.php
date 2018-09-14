<?
$folder = 'txt';
$f_arr = array_values( array_diff( scandir($folder), array('.', '..') ) );
$arr = array();
foreach($f_arr as $item ){
	if( $item && round(date( time() - filemtime("$folder/$item") ) / (24*60*60), 2) > 5.5 ){
		unlink("$folder/$item");
	}
	else{
		$arr[] = $item;
	}
}
echo json_encode($arr);
?>