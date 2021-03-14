<?
require_once('functions.php');
require_once('Channel.php');
require_once 'vendor/autoload.php';


$channel = 'tv1000_s.txt';
$obj = new Channel($channel);
$text = '';


$tvName = str_replace('.txt', '', $channel);
$text .= $tvName . "\r\n";
foreach($obj->raw() as $day=>$item){
	$text .= $day . "\r\n";
	foreach($item as $time=>$str){
		$text .= $time . ' ' . $str . "\r\n";
	}
}



//file_put_contents($tvName . '.txt', $text);
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* $temp = new \PhpOffice\PhpWord\TemplateProcessor('template.docx');
$temp->setValue('channel', $text);
$temp->saveAs("$tvName.docx"); */

$doc = $phpWord->loadTemplate('template.docx');
$section = $phpWord->createSection();

$section->addText('123456');
$section->addPageBreak();

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$tttxt = $objWriter->getWriterPart('document')->getObjectAsText($section);

$doc->setValue('channel', $tttxt);
$doc->saveAs('result.docx');