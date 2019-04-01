<?
require_once('DBconfig.php');
require_once('class_CHANNEL.php');

//Начальные настройки для функций ниже
if( !isset($_POST['afterDot']) ){
	$_POST['afterDot'] = null;
}
if( !isset($_POST['changeTime']) ){
	$_POST['changeTime'] = 0;
}
if( !isset($_POST['deleteReps']) ){
	$_POST['deleteReps'] = 1;
}
if( !isset($_POST['deleteShortPros']) ){
	$_POST['deleteShortPros'] = 1;
}
if( !isset($_POST['startTime']) ){
	$_POST['startTime'] = '07:00';
}
if( !$_POST['endTime'] ){
	$_POST['endTime'] = '02:00';
}
if( !$_POST['lowerCase'] ){
	$_POST['lowerCase'] = null;
}

//Чистка
function firstLetterUpperCase($str){
	//Первая буква строки начнется с ЗАГЛАВНОЙ
	return mb_convert_case( mb_substr(trim($str), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8" ) . mb_substr(trim($str), 1, mb_strlen(trim($str), 'UTF8'),  'UTF8');
}
function cleaner($week){
	if($week){
		
		//Сначала замена кавычек
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				$week[$day][$time] = preg_replace( array('~[«“]~u','~[»”]~u'), '"',  $show );		
						
			}
		}
		
		
		//Подключение к базе
		$conn = new mysqli(SERVER, USER, PWORD, DB);
		if($conn->connect_error){
			exit('Ошибка подключения к базе: ' . $conn->connect_error);
		}
		
		//Оставляемые фразы
		$sql = 'SELECT `item` FROM `DeleteAllExcept`';
		$result = $conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$findAndLeave[] = $row['item'];
			}
		}
		
		//Имена собственные с большой буквы
		$sql = 'SELECT `item` FROM `RealNames`';
		$result = $conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$realNames[] = $row['item'];
			}
		}
		
		//Найти и удалить
		$sql = 'SELECT `item` FROM `DeleteAll`';
		$result = $conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$findAndDelete[] = $row['item'];
			}
		}
		
		//Найти и заменить
		$sql = 'SELECT `find_what`, `replace_with` FROM `FindReplace`';
		$result = $conn->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$find_what[] = $row['find_what'];
				$replace_with[] = $row['replace_with'];
			}
		}
		
		
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				//Найти и удалить
				$week[$day][$time] = trim(str_replace($findAndDelete, '', trim($show)));

				//Если передача удалена полностью, а время осталось
				if($week[$day][$time] == ''){
					unset($week[$day][$time]);
				}
				
				//Удаляет все кроме Оставляемых фраз
				foreach($findAndLeave as $str){
					preg_match("~{$str}~u", $show, $matches);
					if($matches[0]){
						$week[$day][$time] = trim($matches[0]);
					}
				}
				
				//Находит имена собственные и делает первую букву их с Большой буквы
 				foreach($realNames as $str){
					if(preg_match("~([\s\.\-,!?;:\"])({$str})~u", $show, $matches)){
						$week[$day][$time] = trim(str_replace($matches[2], firstLetterUpperCase($matches[2]), $show));
					}
				}
			}
		}
		
		//Найти и заменить
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				$week[$day][$time] = trim(str_replace($find_what, $replace_with, $show));
			}
		}
		
		//Это завершение после всех манипуляций выше. 
		//Удаление точки в конце предложения
		//Удаление восклицательных знаков сразу после времени и в конце передач, если их больше одного
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				//Находим точку в конце с численно-буквенными символами.
				//Найденное заменяем только на численно-буквенные символы
				/* preg_match('~(["a-zа-я0-9]+)[.,\s]+$~ui', trim($show), $matches);
				$show = str_replace($matches[0], $matches[1], trim($show));
				$week[$day][$time] = $show; */
				
				$show = trim(preg_replace(['~^!{1,}~', '~!{2,}$~', '~[.,\s]+$~ui'], '', $show));
				$week[$day][$time] = $show;
			}	
		}
		
		return $week;
	}
}
function TVseries($week){
	
	
	$TVseries = array
	(
		'~Телесериал~ui',
		'~ОТВсериал~ui',
		'~Т[/]с(ериал)? ~ui',
		'~в многосерийном фильме~',
		'~Сериал ~u',
		'~Телехикая~ui',
		'~Многосерийный фильм~ui',
		'~многосерийного фильма~ui',
		'~в многосерийной~ui',
		'~фэнтези-сериал~ui',
		'~ сериал~ui',
		'~ Заключительная серия~ui',
		'~Заключительные серии~ui'
	);
	
	$movies = array
	(
		'~Художественный фильм~ui',
		'~ОТВкино~ui',
		'~в комедии~u',
		'~ в фильме ~ui',
		'~Триллер~ui',
		'~Фильм~u',
		'~фэнтэзи~ui',
		'~боевик~ui',
		'~вестерн~ui',
		'~комедия~ui',
		'~мелодрама~ui',
		'~Коркем[ ]?-?[ ]?фильм~ui',
		'~драма~ui',
		'~детектив~ui',
		'~в мелодраме~ui',
		'~Мегахит~ui',
		'~Х[/]ф~ui',
		'~фильм ужасов~ui',
		'~Приключения~ui'
	);
	
	$docmovies = array
	(
		'~Документальный фильм~u',
		'~Д[/]ф~u',
		'~Д[/]с~u',
		'~Документальный цикл~u'
	);
	
	$cartoons = array
	(
		'~Анимационный фильм~ui',
		'~мультсериал~ui',
		'~Мультхикая~ui',
		'~Мультфильм~ui'
	);
	
	$telefilms = array
	(
		'~Телевизионный фильм~ui'
	);
	
	if($week){
		foreach($week as $day=>$item){
			foreach($item as $time=>$pro){
				foreach($movies as $str){
					if( preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1 ){
						$week[$day][$time] = 'Х/ф' . ' ' . trim($matches[0]);
					}
				}
			}
		}
		
		foreach($week as $day=>$item){
			foreach($item as $time=>$pro){
				foreach($TVseries as $str){
					if( preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1 ){
						$week[$day][$time] = 'Т/с' . ' ' . trim($matches[0]);
					}
				}
			}
		}
		
		foreach($week as $day=>$item){
			foreach($item as $time=>$pro){
				foreach($docmovies as $str){
					if( preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1 ){
						$week[$day][$time] = 'Д/ф' . ' ' . trim($matches[0]);
					}
				}
			}
		}
		
		foreach($week as $day=>$item){
			foreach($item as $time=>$pro){
				foreach($cartoons as $str){
					if( preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1 ){
						$week[$day][$time] = 'М/ф' . ' ' . trim($matches[0]);
					}
				}
			}
		}
		
		foreach($week as $day=>$item){
			foreach($item as $time=>$pro){
				foreach($telefilms as $str){
					if( preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1 ){
						$week[$day][$time] = 'Т/ф' . ' ' . trim($matches[0]);
					}
				}
			}
		}
	}
	return $week;


}
//Управление
function deleteReps($week){
	if( $_POST['deleteReps'] && $week ){
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
function deleteShortPros($week){
	
	if( $_POST['deleteShortPros'] && $week ){
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				$timeArr = explode(':', $time);
				$timeArrPrev = explode(':', $prev);
				$diff = $timeArr[0] * 60 + $timeArr[1] - $timeArrPrev[0] * 60 - $timeArrPrev[1];
				if($diff > 0 && $diff <= 10){
					unset($week[$day][$prev]);
				}
				$prev = $time;
			}
		}
	}
	return $week;
}
function lowerCase($week){
	if( $_POST['lowerCase'] && $week ){
	
		//Сначала разбиваем на предложения по '~([.?!]+\s?)~u'
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				$week[$day][$time] = preg_split('~([.?!]+\s?)~u', $show, -1, PREG_SPLIT_DELIM_CAPTURE);
			}
		}
		//Каждую первую букву предложения делаем с большой буквы
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				foreach($show as $key=>$sentence){
					$firstLetter = mb_strtoupper( mb_substr( $sentence, 0, 1, 'utf8' ) );
					$otherLetters = mb_convert_case(  mb_substr($sentence, 1, mb_strlen( $sentence, 'UTF8' ),  'utf8'),  MB_CASE_LOWER, 'UTF8'  );
					$week[$day][$time][$key] = $firstLetter . $otherLetters;
				}
			}
		}
		//Соединяем предложения в одно
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				$week[$day][$time] = trim(implode('', $show));
			}
		}
		//Если есть символы в кавычках, то первая буква c Большой буквы		
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				if( preg_match('~(["])(.+)(["])~ui', $show, $matches) ){
					$str = '"' . trim( mb_convert_case( mb_substr(trim($matches[2]), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8" ) . mb_substr(trim($matches[2]), 1, mb_strlen($matches[0], 'UTF8'),  'UTF8') ) . '"';
					$week[$day][$time] = preg_replace('~["].+["]~ui', $str, $show);
				}
			}
		}
		
	}
	return $week;
}
function afterDot($week){
	if($_POST['afterDot'] && $week){
		$new = array();
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				$new[$day][$time] = trim(preg_replace('~([.]{1,}|:).+~ui', '', $show));
			}
		}
		return $new;
	}
	else{
		return $week;
	}
}
function changeTime($week){
	if( isset($_POST['changeTime']) && $week){
		$chng = $_POST['changeTime'] % 24;
		$new = array();
		foreach($week as $day=>$item){
			foreach($item as $time=>$show){
				$time_expld = explode(':', $time);
				$time_expld[0] += $chng;
				if($time_expld[0] < 0){
					$time_expld[0] += 24;
				}
				elseif($time_expld[0] >= 24){
					$time_expld[0] -= 24;
				}
				$time_expld[0] = $time_expld[0] < 10 ? '0'. $time_expld[0] : $time_expld[0];
				$new[$day][implode(':', $time_expld)] = $show;
			}
		}
		return $new;
	}
}
//Показать массив
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
			if(preg_match("~^{$week[$i]}~ui", $arr[$key]) && !preg_match('~\d\d[:]\d\d~ui', $arr[$key])){
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
function getArray(){
	
	if(isset($_POST['txt_in'])){
		$textArea = htmlspecialchars($_POST['txt_in']);
		$arrayOfStr = explode("\n", $textArea);
		
		$arrayOfStr = array_values(array_filter($arrayOfStr, "trim"));
		foreach($arrayOfStr as $key=>$str){
			$arrayOfStr[$key] = preg_replace("/[\t\r\n\s]+/", ' ', trim($str));
		}
	
		checkDays($arrayOfStr);
	 
		$weekArray = [];
		$day = -1;
		foreach($arrayOfStr as $str){
			if(preg_match('/^(Понедельник|Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье)(.+)?$/ui', $str, $matches)){
				$day++;
				//$rusDates[] = $matches[1];
				$rusDates[] = mb_convert_case( mb_substr(trim($matches[1]), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8" ) . 
							  mb_convert_case( mb_substr(trim($matches[1]), 1, mb_strlen(trim($matches[1]), 'UTF8'),  'UTF8'), MB_CASE_LOWER, 'UTF8');
			}
			else{
				if($day > -1){
					$weekArray[$rusDates[$day]][] = $str;
				}
			}
		}
	}

	if(count($weekArray) != 7){ 
		exit('Проверьте данные!'); 
	}
	
	foreach($weekArray as $date=>$day){
		$list = [];
		foreach($day as $key=>$str){
			if(preg_match_all('/^(\d?\d[:|.]\d\d(?:, \d?\d[:|.]\d\d)*)[ ]?(.*)/m', $weekArray[$date][$key], $matches, PREG_SET_ORDER)){
				foreach(explode(', ', $matches[0][1]) as $time){
					$time = str_replace('.', ':', $time);
					$time = strlen($time) == 4 ? '0' . $time : $time;
					$list[$time] = trim($matches[0][2]);
				}
			}
		}
		$first_key = array_search(reset($list), $list);
		ksort($list);
		
		$key = array_search($first_key, array_keys($list), true);
		$slice_1 = array_slice($list, $key);
		$slice_2 = array_diff_key($list, $slice_1);
		
		$weekArray[$date] = $slice_1 + $slice_2;
	}
	
	$week = [];
	for($i = 0; $i < count($weekArray); $i++){
		foreach($weekArray[$rusDates[$i]] as $time=>$show){
			if($time >= $_POST['startTime'] && $time < key($weekArray[$rusDates[$i]])){
				$cut[$rusDates[$i]][$time] = $show;
				unset($weekArray[$rusDates[$i]][$time]);
			}
			if($time < $_POST['startTime'] && $time > $_POST['endTime']){
				unset($weekArray[$rusDates[$i]][$time]);
			}
		}
		switch($i){
			case 0:
				$week[$rusDates[$i]] = $weekArray[$rusDates[$i]];
				break;
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
				$week[$rusDates[$i]] = !empty( $cut[$rusDates[$i - 1]] ) 
				? $cut[$rusDates[$i - 1]] + $weekArray[$rusDates[$i]]
				: $weekArray[$rusDates[$i]];
				break;
		}
	}
	
	foreach($week as $date=>$day){
		foreach($day as $time=>$show){
			$week[$date][$time] = htmlspecialchars_decode($show);
		}
	}
	
	return $week;
}
function pre($week){
	if($week){
		echo '<pre>';
		print_r($week);
		echo '</pre>';
	}
}
function view($week){
	if($week){
		foreach($week as $day => $item){
			echo $day . "\n";
			foreach($item as $time => $show){
				echo $time . ' ' . preg_replace('~\s{2,}~', ' ', trim(firstLetterUpperCase($show))) . "\n";
			}
		}
	}
}
function result($week){
	view( 
	changeTime( 
	cleaner( 
	deleteReps(
	cleaner( 
	lowerCase( 
	deleteReps( 
	TVseries( 
	deleteReps( 
	afterDot( 
	deleteShortPros( 
	cleaner(
	deleteReps( 
	cleaner( $week ) ) ) ) ) ) ) ) ) ) ) ) ) );
}