<?
require_once('DBconfig.php');

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
		//Удаление в тв-программах
		$findAndDelete = array(
		//'~[ ]?[(]?[0|6][+][)]?~',
		'~[ ]?[(]?[0|1]?[2|6|8]?[+][)]?$~',
		'~^[-]~',
		'~^!+~',
		//'~^[.]~',
		'~[.]+$~m',
		'~Программа ~u',
		'~[(]рус[.][ ]?яз[.]?[)]~u',
		'~[(]каз[.]яз[.]?[)]~u',
		'~[ ]?[(]продолжение[)]~u',
		'~([.] )?(\d(\d)?[ ]?[,|-]?[ ]?)?\d\d? сери[я|и]~u',
		'~Телеигра ~u',
		'~Итоговая программа ~u',
		'~Док. фильм ~',
		'~[(]каз[.]?[)]~u',
		'~[(]рус[.]?[)]~u',
		'~Азiл - кулкi багдарламасы ~u',
		'~ [(]повтор[)]~u',
		'~Кулинарная программа~u',
		'~ [(]\d{4},[ ]?.+[)]~u',
		'~ [(]ОТВ,[ ]?\d{4}[)]~u',
		'~[(]\d{4},[ ]?ОТВ[)]~u',
		'~Документальный цикл~u',
		'~Итоги[.] ~u',
		'~Социально-политическое ток-шоу~u',
		'~Мистическое реалити-шоу~u',
		'~(:[ ]?)?Новый сезон~u',
		'~Премьера сезона[.]~u',
		'~[.] Продолжение~u',
		'~[(]с субтитрами[)]~u',
		'~Премьера[.]~u',
		'~Информационно-аналитическая программа~u',
		'~с Дмитрием Дибровым~u',
		'~с Дм. Крыловым~u',
		'~с Алексеем Пимановым~u',
		'~с Марией Шукшиной~u',
		'~[(][)]~',
		//'~[)]~',
		'~\d?\d сезон~u',
		'~каз. с рус.субтитрами~u',
		'~Деректi фильм~ui',
		'~Тусаукесер!~ui',
		'~повтор вечернего выпуска~u',
		'~Премьера!~ui',
		'~, реалити-шоу~ui',
		'~, ойын-сауык багдарламасы~',
		'~Жана маусым!~ui',
		'~багдарламасы~ui',
		'~каз[/]рус~ui',
		'~нын тусаукесерi~ui',
		'~нын жалгасы~ui',
		'~Шоу уникальных способностей~ui',
		'~с ольгой артамоновой~ui',
		'~информационно-развлекательная программа~ui',
		'~Премьера[ ]?!+~',
		'~багдарлама~ui',
		'~Шоу ~ui',
		'~док[.]цикл~ui',
		'~Живых пародий ~ui',
		'~балалар жобасы~ui',
		'~[.]Ночная версия~ui',
		'~скетчком~ui',
		'~Реалити~ui',
		'~^Ток-~ui',
		'~акпараттык-сараптамалык~ui',
		'~галамторга шолу~ui',
		'~ шоуы~ui',
		'~ жобасы~ui',
		'~ нда~ui',
		'~Магълумати- кунел ачу программасы~ui',
		'~Ретро-концерт~ui',
		'~Юмористик тапшыру~ui',
		'~Тiкелей эфир~ui',
		'~Социальный проект~ui',
		'~с Нормундом Грабовскисом~u',
		'~с Сержем Марковичем~u',
		'~с Эдуардом Бендерским~u',
		'~с Евгением Полонским~u',
		'~в нижнем Прикамье~u',
		'~и рыболовная~u',
		'~На рыбалку~u',
		'~с Оливией АндриакО~ui',
		'~\. Прямая трансляция$~u',
		'~^Окончание$~ui',
		'~- специальный выпуск~u',
		'~\d\d?-й этап$~u',
		'~Спецвыпуск$~ui',
		'~Прямая трансляция из .+$~ui',
		'~^С[/]р ~u',
		'~Трансляция из .+$~',
		'~с Мариной Рыкалиной~u',
		'~с Павлом Стерховым~u',
		'~с Глебом Астафьевым~u',
		'~с Робсоном Грином~u',
		'~с Гарри Льюисом~u',
		'~с Сергеем Астаховым~u',
		'~[.] [а-яё]+ день$~ui',//для esp.txt, удаляет: "Первый день", "Второй день" и т.д
		'~\[\d\d\+]$~u',
		'~,+$~u',
		'~с Александром Борисовым~u',
		'~на колымского снежного барана~u',
		'~на камских просторах~u',
		'~Плей-офф$~u',
		'~Тележурнал "Зал Славы"[.] ~u',
		'~Тележурнал ~u',
		'~Обзор$~u',
		'~[(]Сезон ?\d\d?[)]~u',
		'~^Д[/]ц ~u'
		);
		
		//Оставляемые фразы
		
		/* $conn = new mysqli(SERVER, USER, PASSWORD, DB);
		if($conn->connect_error){
			exit('Ошибка подключения к базе: ' . $conn->connect_error);
		}
		
		$sql = 'SELECT `item` FROM `DeleteAllExcept`';
		$result = $conn->query($sql);
		
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				$findAndLeave[] = $row['item'];
			}
		} */
		
		
		$findAndLeave = array(
		'~"20:30"~ui',
		'~"7 кун"~ui',
		'~"X factor"~u',
		'~"ГЛАВНАЯ РЕДАКЦИЯ"~ui',
		'~"Жди меня"~ui',
		'~"Любимые актеры"~ui',
		'~"ПОРТРЕТ НЕДЕЛИ"~ui',
		'~"Я стесняюсь своего тела"~u',
		'~^"Битва экстрасенсов"~u',
		'~^"Вопрос времени"~u',
		'~^Дaчныe радoсти~u',
		'~В гостях у Митрофановны~u',
		'~Все на Матч!~u',
		'~Галыгин.ru~ui',
		'~Дела семейные~u',
		'~Искры камина~u',
		'~Истории в деталях~ui',
		'~История советской эстрады~ui',
		'~Кино в деталях~u',
		'~Нахлыст~u',
		'~Особенности охоты~',
		'~ОТВдетям. Мультфильмы~',
		'~Пешком\.\.\.~u',
		'~Профессиональный бокс~u',
		'~Радзишевский и К~u',
		'~Сати[.] Нескучная классика~u',
		'~Сквозной эфир~u',
		'~Смешанные единоборства~u',
		'~Съешьте это немедленно!~ui'
		);
		
		//Сначала замена кавычек
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				$week[$day][$time] = preg_replace( array('~[«“]~u','~[»”]~u'), '"',  $show );			
			}
		}	
		
		foreach($week as $day => $item){
			foreach($item as $time => $show){
				$week[$day][$time] = preg_replace($findAndDelete, '', trim($show));

				//Если передача удалена полностью, а время осталось
				if($week[$day][$time] == ''){
					unset($week[$day][$time]);
				}
				foreach($findAndLeave as $str){
					preg_match($str, $show, $matches);
					if($matches[0]){
						$week[$day][$time] = $matches[0];
					}
				}
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
		'~кино~ui',
		'~в мелодраме~ui',
		'~Мегахит~ui',
		'~Х[/]ф~ui',
		'~фильм ужасов~ui',
		'~Приключения~ui'
	);
	
	$docmovies = array
	(
		'~Документальный фильм~u',
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
					$str = '"' . trim( mb_convert_case( mb_substr($matches[2], 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8" ) . mb_substr($matches[2], 1, mb_strlen($matches[0], 'UTF8'),  'UTF8') ) . '"';
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
			if(preg_match("~^{$week[$i]}[ ,.]?~ui", $arr[$key]) ){
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
	 
		$weekArray = array();
		$day = -1;
		foreach($arrayOfStr as $str){
			if(preg_match('/^Понедельник|^Вторник|^Среда|^Четверг|^Пятница|^Суббота|^Воскресенье/ui', $str)){
				$day++;
			}
			else{
				if($day > -1){
					$weekArray[$day][] = $str;
				}
			}
		}
	}
	
	
	if(count($weekArray) != 7){ 
		exit('Проверьте данные!'); 
	}
	
	for($day = 0; $day < count($weekArray); $day++){
		$list = array();
		foreach($weekArray[$day] as $str){
			preg_match_all('/^(\d?\d[:|.]\d\d(?:, \d?\d[:|.]\d\d)*)[ ]?(.*)/m', $str, $matches, PREG_SET_ORDER);
			$matches[0][1] = strlen($matches[0][1]) == 4 ? '0' . $matches[0][1] : $matches[0][1];
			foreach((explode(', ', $matches[0][1])) as $time){
				$time = str_replace('.', ':', $time);
				$list[$time] = $matches[0][2];
			}
		}
		$first_key = array_search(reset($list), $list);
		ksort($list);
				
		$slice_1 = array();
		$slice_2 = array();
		foreach($list as $time => $tvPro){
			if($time >= $first_key){
				$slice_1[$time] = $tvPro;
			}
			else{
				$slice_2[$time] = $tvPro;
			}
		}
				
		switch($day){
			case 0;
				$monday = $slice_1 + $slice_2;
			break;
			case 1;
				$tuesday = $slice_1 + $slice_2;
			break;
			case 2;
				$wednesday = $slice_1 + $slice_2;
			break;
			case 3;
				$thursday = $slice_1 + $slice_2;
			break;
			case 4;
				$friday =	$slice_1 + $slice_2;
			break;
			case 5;
				$saturday = $slice_1 + $slice_2;
			break;
			case 6;
				$sunday = $slice_1 + $slice_2;
			break;
		}
	}
	
			
	$week = array(
		$monday,
		$tuesday,
		$wednesday,
		$thursday,
		$friday,
		$saturday,
		$sunday
	);
	
	
	$main_key = min(key($week[0]), key($week[1]), key($week[2]), key($week[3]), key($week[4]), key($week[5]), key($week[6]));
	
	for($day = 0; $day < count($week); $day++){
		$cut[$day] = array();
		//$nextMonday переменная для следующей недели, нужно сохранить в сессию на 7 дней
		$nextMonday[$day] = array();
		unset($week[$day][null]);
		
		foreach($week[$day] as $time => $show){
			if($time >= $_POST['startTime'] && $time < $main_key){
				$cut[$day][$time] = $show;
				unset($week[$day][$time]);
			}
			// добавить if() потому что возникает проблема, если endTime < '00:00'
			if($time < $_POST['startTime'] && $time > $_POST['endTime']){
				unset($week[$day][$time]);
			}
			if($time < $first_key && $time > $_POST['startTime']){
				$nextMonday[$day][$time] = $show;
				//unset($week[$day][$time]);
			}
		}

		switch($day){
			case 0:
			$monday = $week[$day];
			break;
			case 1:
			$tuesday = $cut[$day - 1] + $week[$day];
			break;
			case 2:
			$wednesday = $cut[$day - 1] + $week[$day];
			break;
			case 3:
			$thursday = $cut[$day - 1] + $week[$day];
			break;
			case 4:
			$friday = $cut[$day - 1] + $week[$day];
			break;
			case 5:
			$saturday = $cut[$day - 1] + $week[$day];
			break;
			case 6:
			$sunday = $cut[$day - 1] + $week[$day] + $nextMonday[$day];
			break;
		}		
	}
	
	$week = array(
		'Понедельник'	=> $monday,
		'Вторник'		=> $tuesday,
		'Среда'			=> $wednesday,
		'Четверг'		=> $thursday,
		'Пятница'		=> $friday,
		'Суббота'		=> $saturday,
		'Воскресенье'	=> $sunday
	);

	foreach($week as $day=>$item){
		foreach($item as $time=>$pro){
			$week[$day][$time] = htmlspecialchars_decode($pro);
		}
	}	
	return $week;	

}
function preview($week){
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
				echo $time . ' ' . firstLetterUpperCase($show) . "\n";
			}
		}
	}
}
function result($week){
	view( 
	changeTime( 
	cleaner( 
	lowerCase( 
	deleteReps( 
	TVseries( 
	deleteReps( 
	afterDot( 
	deleteShortPros( 
	deleteReps( 
	cleaner( $week ) ) ) ) ) ) ) ) ) ) );
}	

/* 
Цель функции не ставить символ перехода \n в воскресенье, в самой последней строке, но проблема ее в том, в результате появляется строка со временем, которая по факту за пределами startTime и endTime.
function view($week){
	if($week){
		foreach($week as $day => $item){
			echo $day . "\n";
			if($day == 'Воскресенье' ){
				$length = count($item);
				$counter = 0;
				foreach($item as $time => $show){
					if(++$counter == $length){
						echo $time . ' ' . firstLetterUpperCase($show);
					}
					else{
						echo $time . ' ' . firstLetterUpperCase($show) . "\n";
					}
				}
			}	
			else{
				foreach($item as $time => $show){
					echo $time . ' ' . firstLetterUpperCase($show) . "\n";
				}	
			}
		}
	}
} */