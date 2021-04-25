<?php
require_once('functions.php');
$jsonStr = file_get_contents('php://input');
$arr = json_decode($jsonStr, true);

$finalOutput = [];

if ($arr['fileName']) {
    require_once('Channel.php');
    $obj = new Channel($arr['fileName']);

    /**
     * php-word
     */

    require_once '../vendor/autoload.php';
    $phpWord = new \PhpOffice\PhpWord\PhpWord();
    $section = $phpWord->addSection();

    foreach (result($obj->getLinesByFileName(), false) as $line) {

        if (preg_match('/^(Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье)(.+)?$/ui', $line)){
            $section->addPageBreak();
            $section->addText($line);
        }
        else{
            $section->addText(str_replace(['"', '&'], ['', '&amp;'], $line));
        }


//        Если только дни недели
//        switch ($line) {
//            case 'Вторник':
//            case 'Среда':
//            case 'Четверг':
//            case 'Пятница':
//            case 'Суббота':
//            case 'Воскресенье':
//                $section->addPageBreak();
//                $section->addText($line);
//                break;
//            default;
//                /**
//                 * Удаление кавычек и "&" (& - вызывает ошибку при создании word-файла, если встречается в txt-файле)
//                 */
//                $section->addText(str_replace(['"', '&'], ['', '&amp;'], $line));
//                break;
//        }
    }

    $file = str_replace('.txt', '', $arr['fileName']) . '.docx';
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord);
    $objWriter->save($file);

    /**
     * php-word -- end
     */

    $finalOutput =
        [
            'startTime' => $obj->startTime,
            'endTime' => $obj->endTime,
            'afterDot' => $obj->afterDot,
            'lowerCase' => $obj->lowerCase,
            'raw' => join("\n", $obj->getLinesUTF8()),
            'result' => result($obj->getLinesByFileName()),
        ];
} else {
    $_POST = json_decode($jsonStr, true);
    $finalOutput =
        [
            'startTime' => $_POST['startTime'],
            'endTime' => $_POST['endTime'],
            'raw' => $_POST['txt_in'],
            'result' => result(getLinesByJSEvent($_POST['startTime'], $_POST['endTime'])),
        ];
}

echo json_encode($finalOutput, JSON_UNESCAPED_UNICODE);