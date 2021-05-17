<?php

class PhpWordCreateFiles
{
    const DIR = 'docx/';
    public static function init($txt_file)
    {
        require_once('functions.php');
        require_once '../vendor/autoload.php';

        $obj = new Channel($txt_file);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $section = $phpWord->addSection();
        foreach (result($obj->getLinesByFileName(), false) as $line) {
            if (preg_match('/^(Вторник|Среда|Четверг|Пятница|Суббота|Воскресенье)(.+)?$/ui', $line)) {
                $section->addPageBreak();
                $section->addText($line);
            } else {
                /**
                 * Удаление кавычек и "&" (& - вызывает ошибку при создании word-файла, если встречается в txt-файле)
                 */
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
//                $section->addText(str_replace(['"', '&'], ['', '&amp;'], $line));
//                break;
//        }
        }

        $file = str_replace('.txt', '', $txt_file) . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord);
        $objWriter->save(static::DIR.$file);
    }
}