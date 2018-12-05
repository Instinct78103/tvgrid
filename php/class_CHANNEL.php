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
				$this->startTime = '07:00';
				$this->endTime = '02:00';
				break;
			case 'tv1000_s.txt':
				$this->startTime = '09:00';
				$this->endTime = '02:00';
				break;
			case 'tv1000action_s.txt':
				$this->startTime = '07:00';
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
	/* public $findAndDelete;
	
	public function del($weekArray){
		if($weekArray){
			$new_array = array();
			foreach($weekArray as $day => $item){
				foreach($item as $time => $show){
					$new_array[$day][$time] = trim(preg_replace($this->findAndDelete, '', $show));
				}
			}
			return $new_array;
		}
	} */
	public function raw(){

			$arrayOfStr = file("$this->folder/$this->fileName");
			$arrayOfStr = array_values(array_filter($arrayOfStr, "trim"));
			foreach($arrayOfStr as $key=>$str){
				$arrayOfStr[$key] = trim(iconv('CP1251', 'UTF-8', $str));
			}
		
			$weekArray = array();
			$now = -1;
			foreach($arrayOfStr as $str){
				if (preg_match('/^Понедельник|^Вторник|^Среда|^Четверг|^Пятница|^Суббота|^Воскресенье/ui', $str)){
					$now++;
				}
				else{
					if($now > -1){
						$weekArray[$now][] = $str;
					}
				}
			}
			
			for($day = 0; $day < count($weekArray); $day++){
				$list = array();
				foreach($weekArray[$day] as $str){
					preg_match_all('/^(\d?\d[:|.]\d\d(?:, \d?\d[:|.]\d\d)*) (.*)/m', $str, $matches, PREG_SET_ORDER);
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
				$nextMonday = array();
				unset($week[$day][null]);
				
				foreach($week[$day] as $time => $show){
					if($time >= $this->startTime && $time < $main_key){
						$cut[$day][$time] = $show;
						unset($week[$day][$time]);
					}
					// добавить if() потому что возникает проблема, если endTime < '00:00'
					if($time < $this->startTime && $time > $this->endTime){
						$nextMonday[$day][$time] = $show;
						unset($week[$day][$time]);
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
					$sunday = $cut[$day - 1] + $week[$day]; //+ $nextMonday[$day];
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
			return $week;
	}

}