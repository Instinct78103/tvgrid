<?
class Channel{
	
	protected $folder;
	public $fileName;
	public $startTime;
	public $endTime;
	
	public function __construct($fileName){
		$this->folder = 'txt';
		$this->fileName = $fileName;

		switch($this->fileName){
			case 'animal_s.txt':
				$this->startTime = '10:00';
				$this->endTime = '01:00';
				$_POST['afterDot'] = 1;
				break;
			case 'cultura_s.txt':
				$this->startTime = '10:00';
				$this->endTime = '01:00';
				break;
			case 'disc_eu_s.txt':
				$this->startTime = '10:00';
				$this->endTime = '02:00';
				$_POST['afterDot'] = 1;
				break;
			case 'dom-kino_s.txt':
				$this->startTime = '07:00';
				$this->endTime = '06:00';
				break;
			case 'esp_s.txt':
				$this->startTime = '09:00';
				$this->endTime = '02:00';
				break;
			case 'geogr_s.txt':
				$this->startTime = '09:30';
				$this->endTime = '01:00';
				$_POST['afterDot'] = 1;
				break;
			case 'history_s.txt':
				$this->startTime = '10:00';
				$this->endTime = '02:00';
				$_POST['afterDot'] = 1;
				break;
			case 'ilusionpl_s.txt':
				$this->startTime = '07:00';
				$this->endTime = '06:00';
				break;
			case 'match-planeta_s.txt':
				$this->startTime = '08:00';
				$this->endTime = '06:00';
				$_POST['afterDot'] = 1;
				break;
			case 'match-tv_s.txt':
				$this->startTime = '09:30';
				$this->endTime = '01:00';
				break;
			case 'nostalg_s.txt':
				$this->startTime = '09:00';
				$this->endTime = '01:00';
				break;
			case 'ntvm_s.txt':
				$this->startTime = '07:00';
				$this->endTime = '05:00';
				break;
			case 'ohota_s.txt':
				$this->startTime = '12:00';
				$this->endTime = '00:00';
				$_POST['afterDot'] = 1;
				break;
			case 'rtrpl_s.txt':
				$this->startTime = '06:00';
				$this->endTime = '03:00';
				break;
			case 'rtvi_s.txt':
				$this->startTime = '07:00';
				$this->endTime = '01:00';
				break;
			case 'stsint_s.txt':
				$this->startTime = '09:00';
				$this->endTime = '05:00';
				$_POST['lowerCase'] = 1;
				break;
			case 'tnt4_s.txt':
				$this->startTime = '07:00';
				$this->endTime = '07:00';
				break;
			case 'tv21_s.txt':
				$this->startTime = '08:00';
				$this->endTime = '02:00';
				break;
			case 'tv1000_s.txt':
				$this->startTime = '09:00';
				$this->endTime = '02:00';
				break;
			case 'tv1000action_s.txt':
				$this->startTime = '08:00';
				$this->endTime = '02:00';
				break;
			case 'tv1000k_s.txt':
				$this->startTime = '08:00';
				$this->endTime = '02:00';
				break;
			case 'tvci_s.txt':
				$this->startTime = '10:00';
				$this->endTime = '02:00';
				break;
			case 'usadba_s.txt':
				$this->startTime = '12:00';
				$this->endTime = '00:00';
				$_POST['afterDot'] = 1;
				break;
			case 'vremya_s.txt':
				$this->startTime = '08:00';
				$this->endTime = '03:00';
				$_POST['afterDot'] = 1;
				break;
			default;
				$this->startTime = '07:00';
				$this->endTime = '06:00';
				break;
		}
	}
	public function raw(){
		$arrayOfStr = file("$this->folder/$this->fileName");
		
		$arrayOfStr = array_values(array_filter($arrayOfStr, "trim"));
		foreach($arrayOfStr as $key=>$str){
			$arrayOfStr[$key] = trim(iconv('CP1251', 'UTF-8', $str));
		}
			
		checkDays($arrayOfStr);
		
		$weekArray = [];
		$day = -1;
		foreach($arrayOfStr as $str){
			if (preg_match('/^Понедельник.+$|^Вторник.+$|^Среда.+$|^Четверг.+$|^Пятница.+$|^Суббота.+$|^Воскресенье.+$/ui', $str, $matches)){
				$day++;
				$rusDates[] = $matches[0];
			}
			else{
				if($day > -1){
					$weekArray[$rusDates[$day]][] = $str;
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
			
		$main_key = min(
		key($weekArray[$rusDates[0]]), 
		key($weekArray[$rusDates[1]]), 
		key($weekArray[$rusDates[2]]), 
		key($weekArray[$rusDates[3]]), 
		key($weekArray[$rusDates[4]]), 
		key($weekArray[$rusDates[5]]), 
		key($weekArray[$rusDates[6]])
		);
			
		$week = [];
		for($i = 0; $i < count($weekArray); $i++){
			foreach($weekArray[$rusDates[$i]] as $time=>$show){
				if($time >= $this->startTime && $time < $main_key){
					$cut[$rusDates[$i]][$time] = $show;
					unset($weekArray[$rusDates[$i]][$time]);
				}
				if($time < $this->startTime && $time > $this->endTime){
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
			
		return $week;
	}
}