<?

class Channel
{
    public $folder = 'txt';
    public $fileName;
    public $startTime;
    public $endTime;
    public $afterDot = false;


    public function __construct($fileName)
    {

        $this->fileName = $fileName;

        switch ($this->fileName) {
            case 'history_s.txt':
            case 'animal_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '01:00';
                $this->afterDot = true;
                break;
            case 'cultura_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '01:00';
                break;
            case 'tvci_s.txt':
            case 'disc_eu_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '02:00';
                $this->afterDot = true;
                break;
            case 'tv1000_s.txt':
            case 'rtvi_s.txt':
            case 'esp_s.txt':
                $this->startTime = '09:00';
                $this->endTime = '01:00';
                break;
            case 'match-planeta_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '06:00';
                $this->afterDot = true;
                break;
            case 'match-tv_s.txt':
                $this->startTime = '09:30';
                $this->endTime = '01:00';
                break;
            case 'nostalg_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '00:00';
                break;
            case 'ntvm_s.txt':
                $this->startTime = '07:00';
                $this->endTime = '05:00';
                break;
            case 'usadba_s.txt':
            case 'ohota_s.txt':
                $this->startTime = '12:00';
                $this->endTime = '00:00';
                $this->afterDot = true;
                break;
            case 'rtrpl_s.txt':
                $this->startTime = '06:00';
                $this->endTime = '03:00';
                break;
            case 'tv1000action_s.txt':
            case 'tv1000k_s.txt':
            case 'tv21_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '02:00';
                break;
            case 'vremya_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '03:00';
                $this->afterDot = true;
                break;
            default;
                $this->startTime = '07:00';
                $this->endTime = '06:00';
                break;
        }

        $_POST['afterDot'] = $this->afterDot;
    }

    public function getLinesUTF8()
    {
        $arrayOfStr = file("$this->folder/$this->fileName");

        foreach ($arrayOfStr as $key => $str) {
            $arrayOfStr[$key] = trim(iconv('CP1251', 'UTF-8', $str));
        }

        return $arrayOfStr;
    }

    public function getLines()
    {
        $lines = '';
        foreach ($this->getLinesUTF8() as $item) {
            $lines .= $item . "\n";
        }
        return $lines;
    }

    public function raw()
    {
        $arrayOfStr = $this->getLinesUTF8();
        checkDays($arrayOfStr);

        $weekArray = [];
        $rusDates = [];
        $day = -1;
        foreach ($this->getLinesUTF8() as $str) {
            if (preg_match('/^(Понедельник|Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье)(.+)?$/ui', $str, $matches)) {
                $day++;
                //$rusDates[] = $matches[0]; Будет и день недели, и дата
                $rusDates[] = mb_convert_case(mb_substr(trim($matches[1]), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8") .
                    mb_convert_case(mb_substr(trim($matches[1]), 1, mb_strlen(trim($matches[1]), 'UTF8'), 'UTF8'), MB_CASE_LOWER, 'UTF8');
            } else {
                if ($day > -1) {
                    $weekArray[$rusDates[$day]][] = $str;
                }
            }
        }

        if (count($weekArray) != 7) {
            exit(json_encode(['Проверьте данные!'], JSON_UNESCAPED_UNICODE));
        }

        foreach ($weekArray as $date => $day) {
            $list = [];
            foreach ($day as $key => $str) {
                if (preg_match_all('/^(\d?\d[:|.]\d\d(?:, \d?\d[:|.]\d\d)*)[ ]?(.*)/m', $weekArray[$date][$key], $matches, PREG_SET_ORDER)) {
                    foreach (explode(', ', $matches[0][1]) as $time) {
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
        for ($i = 0; $i < count($weekArray); $i++) {
            foreach ($weekArray[$rusDates[$i]] as $time => $show) {
                if ($time >= $this->startTime && $time < key($weekArray[$rusDates[$i]])) {
                    $cut[$rusDates[$i]][$time] = $show;
                    unset($weekArray[$rusDates[$i]][$time]);
                }
                if ($time < $this->startTime && $time > $this->endTime) {
                    unset($weekArray[$rusDates[$i]][$time]);
                }
            }
            switch ($i) {
                case 0:
                    $week[$rusDates[$i]] = $weekArray[$rusDates[$i]];
                    break;
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    $week[$rusDates[$i]] = !empty($cut[$rusDates[$i - 1]])
                        ? $cut[$rusDates[$i - 1]] + $weekArray[$rusDates[$i]]
                        : $weekArray[$rusDates[$i]];
                    break;
            }
        }

        foreach ($week as $date => $day) {
            foreach ($day as $time => $show) {
                $week[$date][$time] = htmlspecialchars_decode($show);
            }
        }

        return $week;
    }
}