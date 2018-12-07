<?
require_once('DBconfig.php');
include('class_channel.php');

function pre($arr){
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}
function checkDays($arr){
	
	$week = [
    'Понедельник', 
	'Вторник', 
	'Среда', 
	'Четверг', 
    'Пятница', 
	'Суббота', 
	'Воскресенье'
	];

	$new = [];
	for($i = 0; $i < count($week); $i++){
		foreach($arr as $key=>$item){
			if(preg_match("~^{$week[$i]}~ui", $arr[$key]) && !preg_match('~\d\d[:.]\d\d~ui', $arr[$key])){
				$new[$i] = $week[$i];
			}
		}
	}

	if( array_diff($week, $new) ){
		foreach(array_diff($week, $new) as $item){
			if(mb_substr($item, -1) == 'а'){
				exit($item . ' не найдена или написана с ошибками!');
			}
			elseif(mb_substr($item, -1) == 'е'){
				exit($item . ' не найдено или написано с ошибками!');
			}
			else{
				exit($item . ' не найден или написан с ошибками!');
			}
		}
	}
	
}
function deleteReps($week){
	if( /* $_POST['deleteReps'] &&  */$week ){
		foreach($week as $day => $item){
			$rep = '';
			foreach($item as $time => $show){
				if($show == $rep){
					unset($week[$day][$time]);
				}
				$rep = $show;
			}
		}
	}
	return $week;
}

$tv = new Channel('tv21_s.txt');
pre(deleteReps($tv->raw()));

