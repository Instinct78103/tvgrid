<?php
require_once('define.php');
require_once('Channel.php');

//Начальные настройки для функций ниже
if (!isset($_POST['afterDot'])) {
    $_POST['afterDot'] = null;
}
if (!isset($_POST['changeTime'])) {
    $_POST['changeTime'] = 0;
}
if (!isset($_POST['deleteReps'])) {
    $_POST['deleteReps'] = 1;
}
if (!isset($_POST['deleteShortPros'])) {
    $_POST['deleteShortPros'] = 1;
}
if (!isset($_POST['startTime'])) {
    $_POST['startTime'] = '07:00';
}
if (!$_POST['endTime']) {
    $_POST['endTime'] = '02:00';
}
if (!$_POST['lowerCase']) {
    $_POST['lowerCase'] = null;
}

//Чистка
function firstLetterUpperCase($str)
{
    //Первая буква строки начнется с ЗАГЛАВНОЙ
    return mb_convert_case(mb_substr(trim($str), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8") . mb_substr(trim($str), 1, mb_strlen(trim($str), 'UTF8'), 'UTF8');
}

function cleaner($week)
{
    if ($week) {

        //Сначала замена кавычек
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $week[$day][$time] = preg_replace(['~[«“]~u', '~[»”]~u'], '"', $show);

            }
        }

        $regExpToRemove = [
            '~\(ОТВ,\s?[0-9]{4}\)~ui',
            '~\(20\d\d, Россия\)~ui',
        ];


        //Подключение к базе
        $conn = new mysqli(SERVER, USER, PWORD, DB);
        if ($conn->connect_error) {
            exit('Ошибка подключения к базе: ' . $conn->connect_error);
        }

        //Оставляемые фразы
        $sql = 'SELECT `item` FROM `DeleteAllExcept`';
        $result = $conn->query($sql);
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $findAndLeave[] = $row['item'];
            }
        }

        //Имена собственные с большой буквы
        $sql = 'SELECT `item` FROM `RealNames`';
        $result = $conn->query($sql);
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $realNames[] = $row['item'];
            }
        }

        //Найти и удалить
        $sql = 'SELECT `item` FROM `DeleteAll`';
        $result = $conn->query($sql);
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $findAndDelete[] = $row['item'];
            }
        }

        //Найти и заменить
        $sql = 'SELECT `find_what`, `replace_with` FROM `FindReplace`';
        $result = $conn->query($sql);
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $find_what[] = $row['find_what'];
                $replace_with[] = $row['replace_with'];
            }
        }

        // Удаление по регулярным выражениям
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $week[$day][$time] = trim(preg_replace($regExpToRemove, '', trim($show)));
            }
        }

        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                //Найти и удалить
                $week[$day][$time] = trim(str_replace($findAndDelete, '', trim($show)));

                //Если передача удалена полностью, а время осталось
                if ($week[$day][$time] == '') {
                    unset($week[$day][$time]);
                }

                //Удаляет все кроме Оставляемых фраз
                foreach ($findAndLeave as $str) {
                    $str = preg_quote($str);// экранирование символов
                    preg_match("~{$str}~ui", $show, $matches);
                    if ($matches[0]) {
                        $week[$day][$time] = trim($matches[0]);
                    }
                }

                //Находит имена собственные и делает первую букву с Большой буквы
                foreach ($realNames as $str) {
                    if (preg_match("~([\s.\-,!?;:\"])({$str})~u", $show, $matches)) {
                        $week[$day][$time] = trim(str_replace($matches[2], firstLetterUpperCase($matches[2]), $show));
                    }
                }
            }
        }

        //Найти и заменить
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $week[$day][$time] = trim(str_replace($find_what, $replace_with, $show));
            }
        }

        //Это завершение после всех манипуляций выше.
        //Удаление точки в конце предложения
        //Удаление восклицательных знаков сразу после времени и в конце передач, если их больше одного
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                //Находим точку в конце с численно-буквенными символами.
                //Найденное заменяем только на численно-буквенные символы
                /* preg_match('~(["a-zа-я0-9]+)[.,\s]+$~ui', trim($show), $matches);
                $show = str_replace($matches[0], $matches[1], trim($show));
                $week[$day][$time] = $show; */

                $show = preg_replace('~([а-яa-z])(,)([а-яa-z])~ui', '$1$2 $3', $show); // Добавить пробел, если он отсутствует после запятой
                $show = trim(preg_replace(['~^!{1,}~', '~!{2,}$~', '~[.,\s]+$~ui'], '', $show));
                $week[$day][$time] = $show;
            }
        }

        return $week;
    }
}

function TVseries($week)
{


    $TVseries = [
        '~Телесериал~ui',
        '~ОТВсериал~ui',
        '~Т[/]с(ериал)? ~ui',
        '~многосерийном фильме~ui',
        '~Сериал ~u',
        '~Телехикая~ui',
        '~Многосерийный фильм~ui',
        '~многосерийного фильма~ui',
        '~в многосерийной~ui',
        '~фэнтези-сериал~ui',
        '~ сериал~ui',
        '~ Заключительная серия~ui',
        '~Заключительные серии~ui',
        '~Минисериал~ui',
        '~^сериал~ui',
        '~т\/с~ui',
        '~^cериал~ui',
    ];

    $movies = [
        '~Художественный фильм~ui',
        '~КИНО~u',
        '~ОТВкино~ui',
        '~в комедии~u',
        '~ в фильме ~ui',
        '~Триллер~ui',
        '~Фильм~u',
        '~фэнтэзи~ui',
        '~боевик~ui',
        '~вестерн~ui',
        '~^кино\.~ui',
        '~комедия~ui',
        '~мелодрама~ui',
        '~Коркем[ ]?-?[ ]?фильм~ui',
        '~драма~ui',
        '~в драме~ui',
        '~детектив~ui',
        '~в мелодраме~ui',
        '~Мегахит~ui',
        '~Х[/]ф~ui',
        '~фильм ужасов~ui',
        '~отечественного кино~ui',
        '~Наши любимые комедии~ui',
        '~Ночной кинотеатр\.~ui',
        '~коркем–фильм~ui',
        '~Казахстанское кино~ui',
        '~семейное кино~ui',
        '~казахстанского кино~ui',
        '~казахстанское кино~ui',
        '~легендарное кино~ui',
        '~фантастика~ui',
        '~Советское кино~ui',
        '~Наше любимое кино~ui',
        '~мьюзикл~ui',
        '~мюзикл~ui',
        '~в приключенческом фильме~ui',
        '~Семейный кинотеатр~ui',
        '~Приключения~ui',
    ];

    $docmovies = [
        '~Документальный фильм~ui',
        '~Д[/]ф~ui',
        '~Д[/]с~ui',
        '~д\.ф~ui',
        '~документальных фильмов~ui',
        '~Док[.][ ]?фильм~ui',
        '~Документального фильма~ui',
        '~биографический фильм~ui',
        '~Документальный цикл~ui',
        '~Документальная драма~ui',
    ];

    $cartoons = [
        '~Анимационный фильм~ui',
        '~мультсериал~ui',
        '~Мультхикая~ui',
        '~Мультфильм~ui',
        '~мультипликационный фильм~ui',
        '~м[/]ф~ui',
    ];

    $telefilms = [
        '~Телевизионный фильм~ui',
    ];

    if ($week) {
        foreach ($week as $day => $item) {
            foreach ($item as $time => $pro) {
                foreach ($movies as $str) {
                    if (preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) // Найти то, что в кавычках
                        && preg_match($str, $week[$day][$time]) // если ключевые слова встречаются в строке...
                        && !preg_match($str, $matches[0])) { // ...и ключевые слова отсутствуют внутри кавычек
                        $week[$day][$time] = 'Х/ф' . ' ' . trim($matches[0]);
                    }
                }
            }
        }

        foreach ($week as $day => $item) {
            foreach ($item as $time => $pro) {
                foreach ($TVseries as $str) {
                    if (preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1) {
                        $week[$day][$time] = 'Т/с' . ' ' . trim($matches[0]);
                    }
                }
            }
        }

        foreach ($week as $day => $item) {
            foreach ($item as $time => $pro) {
                foreach ($docmovies as $str) {
                    if (preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1) {
                        $week[$day][$time] = 'Д/ф' . ' ' . trim($matches[0]);
                    }
                }
            }
        }

        foreach ($week as $day => $item) {
            foreach ($item as $time => $pro) {
                foreach ($cartoons as $str) {
                    if (preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1) {
                        $week[$day][$time] = 'М/ф' . ' ' . trim($matches[0]);
                    }
                }
            }
        }

        foreach ($week as $day => $item) {
            foreach ($item as $time => $pro) {
                foreach ($telefilms as $str) {
                    if (preg_match('~["].{1,}["]~ui', $week[$day][$time], $matches) && preg_match($str, $week[$day][$time]) && count($matches) == 1) {
                        $week[$day][$time] = 'Т/ф' . ' ' . trim($matches[0]);
                    }
                }
            }
        }
    }
    return $week;


}

//Управление
function deleteReps($week)
{
    if ($_POST['deleteReps'] && $week) {
        foreach ($week as $day => $item) {
            $rep = '';
            foreach ($item as $time => $show) {
                if ($show == $rep) {
                    unset($week[$day][$time]);
                }
                $rep = $show;
            }
        }
    }
    return $week;
}

function deleteShortPros($week)
{
    $prev = '';
    if ($_POST['deleteShortPros'] && $week) {
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $timeArr = explode(':', $time);
                $timeArrPrev = explode(':', $prev);
                $diff = (int)$timeArr[0] * 60 + (int)$timeArr[1] - (int)$timeArrPrev[0] * 60 - (int)$timeArrPrev[1];
                if ($diff > 0 && $diff <= 10) {
                    unset($week[$day][$prev]);
                }
                $prev = $time;
            }
        }
    }
    return $week;
}

function lowerCase($week)
{
    if ($_POST['lowerCase'] && $week) {

        //Сначала разбиваем на предложения по '~([.?!]+\s?)~u'
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $week[$day][$time] = preg_split('~([.?!]+\s?)~u', $show, -1, PREG_SPLIT_DELIM_CAPTURE);
            }
        }
        //Каждую первую букву предложения делаем с большой буквы
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                foreach ($show as $key => $sentence) {
                    $firstLetter = mb_strtoupper(mb_substr($sentence, 0, 1, 'utf8'));
                    $otherLetters = mb_convert_case(mb_substr($sentence, 1, mb_strlen($sentence, 'UTF8'), 'utf8'), MB_CASE_LOWER, 'UTF8');
                    $week[$day][$time][$key] = $firstLetter . $otherLetters;
                }
            }
        }
        //Соединяем предложения в одно
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $week[$day][$time] = trim(implode('', $show));
            }
        }
        //Если есть символы в кавычках, то первая буква c Большой буквы
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                if (preg_match('~(["])(.+)["]~ui', $show, $matches)) {
                    $str = '"' . trim(mb_convert_case(mb_substr(trim($matches[2]), 0, 1, 'UTF8'), MB_CASE_UPPER, "UTF-8") . mb_substr(trim($matches[2]), 1, mb_strlen($matches[0], 'UTF8'), 'UTF8')) . '"';
                    $week[$day][$time] = preg_replace('~["].+["]~ui', $str, $show);
                }
            }
        }

    }
    return $week;
}

function afterDot($week)
{
    if ($_POST['afterDot'] && $week) {
        $new = [];
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $new[$day][$time] = trim(preg_replace('~([.]{1,}|:|!|\?).+~ui', '', $show));
            }
        }
        return $new;
    } else {
        return $week;
    }
}

function changeTime($week)
{
    if (isset($_POST['changeTime']) && $week) {
        $chng = $_POST['changeTime'] % 24;
        $new = [];
        foreach ($week as $day => $item) {
            foreach ($item as $time => $show) {
                $time_expld = explode(':', $time);
                $time_expld[0] += $chng;
                if ($time_expld[0] < 0) {
                    $time_expld[0] += 24;
                } elseif ($time_expld[0] >= 24) {
                    $time_expld[0] -= 24;
                }
                $time_expld[0] = $time_expld[0] < 10 ? '0' . $time_expld[0] : $time_expld[0];
                $new[$day][implode(':', $time_expld)] = $show;
            }
        }
        return $new;
    }
}

//Показать массив
function checkDays($arr)
{

    $week = [
        'Понедельник',
        'Вторник',
        'Среда',
        'Четверг',
        'Пятница',
        'Суббота',
        'Воскресенье',
    ];

    $new = [];
    for ($i = 0; $i < count($week); $i++) {
        foreach ($arr as $key => $item) {
            if (preg_match("~^{$week[$i]}~ui", $arr[$key]) && !preg_match('~\d\d[:]\d\d~ui', $arr[$key])) {
                $new[$i] = $week[$i];
            }
        }
    }

    if (array_diff($week, $new)) {
        $msg = [];
        foreach (array_diff($week, $new) as $item) {
            if (mb_substr($item, -1) == 'а') {
                $msg['result'] = $item . ' не найдена или написана с ошибками!';
            } elseif (mb_substr($item, -1) == 'е') {
                $msg['result'] = $item . ' не найдено или написано с ошибками!';
            } else {
                $msg['result'] = $item . ' не найден или написан с ошибками!';
            }
        }
        exit(json_encode($msg, JSON_UNESCAPED_UNICODE));
    }

}

function getParsedArr($arrayOfStr, $startTime, $endTime)
{
    checkDays($arrayOfStr);

    $weekArray = [];
    $day = -1;
    $rusDates = [];
    foreach ($arrayOfStr as $str) {
        if (preg_match('/^(Понедельник|Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье)(.+)?$/ui', $str, $matches)) {
            $day++;
//            $rusDates[] = $matches[0];
            //Только дни недели
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
            if (preg_match_all('/(\d?\d[:|.]\d\d(?:, \d?\d[:|.]\d\d)*)[ ]?(.*)/m', $weekArray[$date][$key], $matches, PREG_SET_ORDER)) {
                foreach (explode(', ', $matches[0][1]) as $time) {
                    $time = str_replace('.', ':', $time);
                    $time = strlen($time) == 4 ? '0' . $time : $time;
                    $list[$time] = trim(preg_replace('~^:00~', '', $matches[0][2]));
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
            if ($time >= $startTime && $time < key($weekArray[$rusDates[$i]])) {
                $cut[$rusDates[$i]][$time] = $show;
                unset($weekArray[$rusDates[$i]][$time]);
            }
            if ($time < $startTime && $time > $endTime) {
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

function getLinesByJSEvent($startTime, $endTime)
{
    $textArea = $_POST['txt_in'];
    $arrayOfStr = explode("\n", $textArea);

    $arrayOfStr = array_filter($arrayOfStr, "trim");
    foreach ($arrayOfStr as $key => $str) {
        $arrayOfStr[$key] = preg_replace('/[\t\r\n\s]+/ui', ' ', trim($str));
    }

    return getParsedArr($arrayOfStr, $_POST['startTime'], $_POST['endTime']);
}

function pre($week)
{
    if ($week) {
        echo '<pre>';
        print_r($week);
        echo '</pre>';
    }
}

function view($week, $result_is_string = true)
{
    if ($week) {
        if ($result_is_string) {
            $tvLines = '';
            foreach ($week as $day => $item) {
//                if ($day !== 'Понедельник') {
                    /**
                     * Удаляем понедельник
                     */
                    $tvLines .= $day . "\n";
//                }

                foreach ($item as $time => $show) {
                    $tvLines .= $time . ' ' . preg_replace('~\s{2,}~ui', ' ', trim(firstLetterUpperCase($show))) . "\n";
                }
            }
        } else {
            $tvLines = [];
            foreach ($week as $day => $item) {
//                if ($day !== 'Понедельник') {
                    /**
                     * Удаляем понедельник
                     */
                    $tvLines[] = $day;
//                }

                foreach ($item as $time => $show) {
                    $tvLines[] = $time . ' ' . preg_replace('~\s{2,}~ui', ' ', trim(firstLetterUpperCase($show)));
                }
            }
        }
    }
    return $tvLines;
}

function result($week, $result_is_string = true)
{
    return view(
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
                                                        cleaner($week))))))))))))), $result_is_string);
}