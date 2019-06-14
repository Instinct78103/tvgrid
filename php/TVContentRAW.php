<?
require_once('define.php');
require_once('functions.php');
$dir = FOLDER;

//При нажатии на название канала в списке div.list, 
//формируется json-переменная "fileName", 
//затем появляется объект $obj = new Channel($arr['fileName']).

$jsonStr = file_get_contents('php://input');
$arr = json_decode($jsonStr, true);

if(isset($arr)){
	$fileName = $arr['fileName'];
	$arrayOfStr = file("$dir/$fileName");

	foreach($arrayOfStr as $key=>$str){
		$arrayOfStr[$key] = trim(iconv('CP1251', 'UTF-8', $str));
	}
}

foreach($arrayOfStr as $item){
	echo $item . "\n";
}